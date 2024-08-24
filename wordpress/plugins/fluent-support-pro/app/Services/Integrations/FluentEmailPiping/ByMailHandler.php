<?php

namespace FluentSupportPro\App\Services\Integrations\FluentEmailPiping;

use FluentSupport\App\Models\Attachment;
use FluentSupport\App\Models\Customer;
use FluentSupport\App\Models\MailBox;
use FluentSupport\App\Models\Conversation;
use FluentSupport\App\Models\Ticket;
use FluentSupport\App\Services\Helper;
use FluentSupport\App\Services\Parser\Parsedown;
use FluentSupport\Framework\Support\Arr;
use FluentSupport\App\Services\Includes\UploadService;

class ByMailHandler
{
    public static function handleEmailData($data, $mailBox = false, $source = 'email')
    {
        $data['subject'] = self::getActualSubject(Arr::get($data, 'subject', 'Subject not defined'));

        $onBehalf = Arr::get($data, 'sender');
        $fullName = sanitize_text_field(Arr::get($onBehalf, 'name', ''));
        unset($onBehalf['name']);

        $nameArray = explode(' ', $fullName);

        if (count($nameArray) >= 2) {
            $onBehalf['last_name'] = array_pop($nameArray);
            $onBehalf['first_name'] = implode(' ', $nameArray);
        } else if ($fullName) {
            $onBehalf['first_name'] = $fullName;
        }

        $customer = Customer::maybeCreateCustomer($onBehalf);

        if (!$customer || $customer->status == 'inactive') {
            return [
                'type'    => 'error',
                'message' => 'Customer is inactive so no ticket can be created'
            ];
        }

        $existingTicket = false;

        if (!$customer->wasRecentlyCreated) {
            $existingTicket = self::guessTicket($customer, $data);
        }

        $carbonCopy = Arr::get($data, 'carbon_copy', []);

        //If no ticket found then check if this user reply to the thread as a carbon copy user
        if (!$existingTicket && $source == 'email' && !empty($carbonCopy)) {
            $existingTicket = self::mayBeCarbonCopyUser($customer, $data['subject'], $carbonCopy);
        }

        $responseOrTicketData = [
            'title'      => sanitize_text_field($data['subject']),
            'message_id' => \FluentSupportPro\App\Services\ProHelper::sanitizeMessageId($data['message_id']),
            'content'    => wp_specialchars_decode(wp_unslash(wp_kses_post($data['content'])))
        ];

        if (empty($responseOrTicketData['message_id']) && $messageId = \FluentSupportPro\App\Services\ProHelper::generateMessageID($customer->email)) {
            $responseOrTicketData['message_id'] = $messageId;
        }

        if (!empty($data['isMarkDown'])) {
            if ($parsedContent = (new Parsedown())->text(wp_specialchars_decode(wp_unslash(wp_kses_post($data['content']))))) {
                $responseOrTicketData['content'] = $parsedContent;
            }
        }

        if ($existingTicket && !empty($data['message_id']) && empty($existingTicket->message_id)) {
            $existingTicket->message_id = $data['message_id'];
            $existingTicket->save();
        }

        if (!$existingTicket) {
            $responseOrTicketData['customer_id'] = $customer->id;
            $responseOrTicketData['source'] = $source;
            $responseOrTicketData['client_priority'] = sanitize_text_field(Arr::get($data, 'priority', 'normal'));
            $responseOrTicketData['priority'] = sanitize_text_field(Arr::get($data, 'priority', 'normal'));

            if (!$mailBox) {
                $mailBox = MailBox::where('is_default', 'yes')->orderBy('id', 'ASC')->first();
            }

            if ($mailBox) {
                $responseOrTicketData['mailbox_id'] = $mailBox->id;
            }

            $responseOrTicketData['source'] = $source;

            $ticketData = apply_filters('fluent_support/create_ticket_data', $responseOrTicketData, $customer);
            // Check if the ticket is already added or not

            if ($mailBox) {
                $contentHash = md5($ticketData['content']);
                $maybeDuplicate = Ticket::where('content_hash', $contentHash)
                    ->where('customer_id', $customer->id)
                    ->where('status', '!=', 'closed')
                    ->where('mailbox_id', $mailBox->id)
                    ->first();

                if ($maybeDuplicate) {
                    return false;
                }
            }

            $canCreateTicket = apply_filters('fluent_support/can_customer_create_ticket', true, $customer, $data);

            if (!$canCreateTicket || is_wp_error($canCreateTicket)) {
                return [
                    'type'    => 'error',
                    'message' => (is_wp_error($canCreateTicket)) ? $canCreateTicket->get_error_message() : __('Sorry you can not create ticket', 'fluent-support-pro')
                ];
            }

            do_action('fluent_support/before_ticket_create', $responseOrTicketData, $customer);

            /**
             * This hook will action before ticket create via email
             * @param array $responseOrTicketData
             * @param object $customer
             */
            do_action('fluent_support/before_ticket_create_from_email', $responseOrTicketData, $customer);

            $createdTicket = Ticket::create($ticketData);

            if ($createdTicket && isset($data['custom_fields']) && !empty($data['custom_fields'])) {
                $customData = wp_unslash($data['custom_fields']);
                $createdTicket->syncCustomFields($customData);
            }

            //If list of carbon copy user not empty, store cc in the ticket meta
            if (!empty($carbonCopy)) {
                //Set the default carbon copy user
                $createdTicket->updateSettingsValue('cc_email', $carbonCopy);//Set the default carbon copy user
                //List all carbon copy user under the ticket
                $createdTicket->updateSettingsValue('all_cc_email', $carbonCopy);//Store all carbon copy
            }

            self::handleAttachments(Arr::get($data, 'attachments', []), $createdTicket, $customer);

            do_action('fluent_support/ticket_created', $createdTicket, $customer);

            /**
             * This hook will action after ticket create via email
             * @param object $createdTicket
             * @param object $customer
             */
            do_action('fluent_support/after_ticket_create_from_email', $createdTicket, $customer);

            return [
                'type'      => 'new_ticket',
                'ticket_id' => $createdTicket->id,
                'ticket'    => $createdTicket
            ];
        }

        if (!empty($existingTicket->extra_content)) {
            $responseOrTicketData['content'] = $existingTicket->extra_content . $responseOrTicketData['content'];
            unset($existingTicket->extra_content);
        }

        // we have to create a response
        unset($responseOrTicketData['title']);
        $responseOrTicketData['person_id'] = $customer->id;
        $responseOrTicketData['ticket_id'] = $existingTicket->id;
        $responseOrTicketData['conversation_type'] = 'response';
        $responseOrTicketData['source'] = $source;

        $canCreateResponse = apply_filters('fluent_support/can_customer_create_response', true, $customer, $existingTicket, $data);

        if (!$canCreateResponse || is_wp_error($canCreateResponse)) {
            return [
                'type'    => 'error',
                'message' => (is_wp_error($canCreateResponse)) ? $canCreateResponse->get_error_message() : __('Sorry you can not create response', 'fluent-support-pro')
            ];
        }

        if ($existingTicket->last_agent_response && strtotime($existingTicket->last_agent_response) > strtotime($existingTicket->last_customer_response)) {
            $existingTicket->waiting_since = current_time('mysql');
        }

        $createdResponse = Conversation::create($responseOrTicketData);

        if ($existingTicket->status != 'active') {
            $existingTicket->status = 'active';
        }

        $existingTicket->last_customer_response = current_time('mysql');
        $existingTicket->response_count += 1;

        if (!empty($data['message_id']) && !$existingTicket->message_id) {
            $existingTicket->message_id = sanitize_text_field($data['message_id']);
        }

        $existingTicket->save();

        //If carbon copy user is not empty, update meta for the ticket and response
        if (!empty($carbonCopy)) {
            //Track all the carbon copy customers under same ticket
            $existingCcEmails = $existingTicket->getSettingsValue('all_cc_email', []);
            $newData = array_merge($existingCcEmails, $carbonCopy);
            $existingTicket->updateSettingsValue('all_cc_email', array_unique($newData));

            //List the carbon copy customers to the response
            $createdResponse->updateSettingsValue('cc_email', $carbonCopy);
            $createdResponse->cc_info = ['cc_email' => $carbonCopy];
        }

        self::handleAttachments(Arr::get($data, 'attachments', []), $existingTicket, $customer, $createdResponse);

        do_action('fluent_support/response_added_by_customer', $createdResponse, $existingTicket, $customer);

        return [
            'type'        => 'new_response',
            'ticket_id'   => $existingTicket->id,
            'response_id' => $createdResponse->id,
            'response'    => $createdResponse,
            'customer'    => $customer
        ];
    }

    private static function getActualSubject($string)
    {
        // regex pattern to match common prefixes
        $pattern = '/^(Re: |RE: |Fwd: |Request Received: |AW: |Aw:)/i';
        $string = preg_replace($pattern, '', $string, 1);

        return $string;
    }

    protected static function guessTicket($customer, $data)
    {
        $subject = $data['subject'];

        // check if the customer has any ticket or not
        if (!Ticket::where('customer_id', $customer->id)->first()) {
            return false;
        }


        if (!empty($data['message_id'])) {
            $ticket = Ticket::where('customer_id', $customer->id)
                ->where('message_id', $data['message_id'])
                ->first();
            if ($ticket) {
                return $ticket;
            }
        }

        preg_match_all('/#([0-9]*)/', $subject, $matches);

        $ticketId = false;
        if (count($matches[1])) {
            $ticketId = array_pop($matches[1]);
        }

        if ($ticketId) {
            $existingTicket = Ticket::where('customer_id', $customer->id)
                ->where('id', $ticketId)
                ->first();

            if ($existingTicket) {
                return $existingTicket;
            }

            $subject = str_replace('#' . $ticketId, '', $subject);
        }

        $existingTicket = Ticket::where('customer_id', $customer->id)
            ->where('title', 'like', '%%' . $subject . '%%')
            ->orderBy('ID', 'DESC')
            ->first();

        if ($existingTicket) {
            return $existingTicket;
        }

        if (apply_filters('fluent_support/ticket_partial_match', true)) {
            // Let's try to guess ticket from ticket subject part
            $subjectParts = explode(' ', $subject);
            $subjectParts = array_filter($subjectParts);

            $partCounts = count($subjectParts);
            if ($partCounts <= 5) {
                if ($partCounts <= 2) {
                    return $existingTicket;
                }

                $subjectPart = implode(' ', array_slice($subjectParts, -2));
            } else {
                $middleItem = intval($partCounts / 2);
                $subjectPart = $subjectParts[$middleItem - 1] . ' ' . $subjectParts[$middleItem] . ' ' . $subjectParts[$middleItem + 1];
            }

            $existingTicket = Ticket::where('customer_id', $customer->id)
                ->where('title', 'like', '%%' . $subjectPart . '%%')
                ->orderBy('ID', 'DESC')
                ->first();
        }

        return $existingTicket;
    }

    private static function mayBeCarbonCopyUser($customer, $subject, $CcEmailsInRequest = [])
    {
        $subject = str_replace('Re: ', '', $subject);

        $existingTicket = Ticket::with('customer')->where('title', 'like', '%%' . $subject . '%%')
            ->orderBy('ID', 'DESC')
            ->first();

        if (!$existingTicket) {
            return false;
        }

        $allCcEmailsInExistingTicket = $existingTicket->getSettingsValue('all_cc_email', []);

        if (!self::isTicketOwnerInTheCcList($existingTicket, $CcEmailsInRequest) &&
            !self::isSenderInTheExistingCcList($customer->email, $allCcEmailsInExistingTicket)) {
            return false;
        }

        return $existingTicket;
    }

    private static function isTicketOwnerInTheCcList($ticket, $ccList)
    {
        return in_array($ticket->customer->email, $ccList);
    }

    private static function isSenderInTheExistingCcList($customer, $ccList)
    {
        return in_array($customer->email, $ccList);
    }

    private static function handleAttachments($attachments, $ticket, $customer, $convo = false)
    {
        if (!$attachments) {
            return false;
        }

        preg_match_all('/\[image: (.*?)]/', $ticket->content, $inlineImages);
        $inlineImageMapper = array_combine($inlineImages[1], $inlineImages[0]);
        $inlineImages = false;
        $modelThatNeedsInlineImages = $convo ? $convo : $ticket;

        $storageDriver = 'local';
        if (method_exists(Helper::class, 'getUploadDriverKey')) {
            $storageDriver = Helper::getUploadDriverKey();
        }

        $acceptedMimes = Helper::ticketAcceptedFileMiles();

        foreach ($attachments as $attachment) {
            $fileTicketId = null;
            if ($storageDriver == 'local') {
                $fileTicketId = $ticket->id;
            }

            // download and save the file from attachment URL
            $upload = UploadService::handleEmailAttachments($attachment, $fileTicketId, $acceptedMimes);

            if (!$upload) {
                continue; // File could not be uploaded
            }

            $filePath = isset($upload['file']) ? $upload['file'] : $upload['file_path'];

            $attachmentData = [
                'status'    => 'active',
                'ticket_id' => $ticket->id,
                'person_id' => $customer->id,
                'file_type' => $upload['type'],
                'file_path' => $filePath,
                'full_url'  => sanitize_url($upload['url']),
                'title'     => sanitize_text_field($upload['name']),
                'settings'  => [
                    'local_temp_path' => $filePath
                ],
                'driver'    => 'local'
            ];

            if ($convo) {
                $attachmentData['conversation_id'] = $convo->id;
            }

            if (Arr::get($attachment, 'contentDisposition') === 'inline') {
                $attachmentData['status'] = 'inline';
            }

            // let's create the attachment record
            $dbAttachment = Attachment::create($attachmentData);

            do_action_ref_array('fluent_support/finalize_file_upload_' . $storageDriver, [&$dbAttachment, $ticket->id]);

            if ($dbAttachment->driver == 'local' && $storageDriver != 'local') {
                // This got failed to we are copying the file to local
                $newFileInfo = UploadService::copyFileTicketFolder($dbAttachment->file_path, $ticket->id);
                if ($newFileInfo && !empty($newFileInfo['file_path'])) {
                    $dbAttachment->file_path = $newFileInfo['file_path'];
                    $dbAttachment->full_url = $newFileInfo['url'];
                    $dbAttachment->save();
                }
            }


            if ($dbAttachment->status === 'inline') {
                if (array_key_exists($attachment['filename'], $inlineImageMapper)) {

                    $inlineImages = true;
                    if ($dbAttachment->driver == 'local') {
                        $modelThatNeedsInlineImages->content = str_replace(
                            $inlineImageMapper[$attachment['filename']],
                            "<img src='{$attachmentData['full_url']}' alt='{$attachment['filename']}' />",
                            $modelThatNeedsInlineImages->content
                        );
                    } else {
                        // It's remote
                        $modelThatNeedsInlineImages->content = str_replace(
                            $inlineImageMapper[$attachment['filename']],
                            "<a href='{$dbAttachment->full_url}' target='_blank' rel='noopener' alt='{$attachment['filename']}'>{$attachment['filename']}</a>",
                            $modelThatNeedsInlineImages->content
                        );
                    }
                }
            }
        }

        if ($inlineImages) {
            $modelThatNeedsInlineImages->save();
        }

        return true;
    }

    public static function overWriteUpDir($upload)
    {
        $uploadDir = wp_upload_dir();

        $upload['path'] = $uploadDir['basedir'] . '/' . FLUENT_SUPPORT_UPLOAD_DIR . '/email_attachments';
        $upload['url'] = $uploadDir['baseurl'] . '/' . FLUENT_SUPPORT_UPLOAD_DIR . '/email_attachments';
        $upload['subdir'] = '/email_attachments';
        return $upload;
    }

    public static function isCustomPipeSupported()
    {
        if (defined('FLUENTSUPPORT_ENABLE_CUSTOM_PIPE') && FLUENTSUPPORT_ENABLE_CUSTOM_PIPE) {
            return true;
        }

        return apply_filters('fluent_support/enable_custom_piping', false);
    }
}
