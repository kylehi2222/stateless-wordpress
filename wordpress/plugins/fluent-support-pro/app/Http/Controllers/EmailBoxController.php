<?php

namespace FluentSupportPro\App\Http\Controllers;

use FluentSupport\App\App;
use FluentSupport\App\Http\Controllers\Controller;
use FluentSupport\App\Models\Customer;
use FluentSupport\App\Models\Meta;
use FluentSupport\App\Models\MailBox;
use FluentSupport\Framework\Request\Request;
use FluentSupport\Framework\Support\Arr;
use FluentSupportPro\App\Services\Integrations\FluentEmailPiping\Api;
use FluentSupportPro\App\Services\Integrations\FluentEmailPiping\ByMailHandler;

class EmailBoxController extends Controller
{
    public function getPipeStatus(Request $request, $box_id)
    {
        $box = MailBox::findOrFail($box_id);

        if ($box->box_type != 'email') {
            return $this->sendError([
                'message' => 'This is a web type business inbox. No email piping is available'
            ]);
        }

        if (!$box->mapped_email) {
            return [
                'email_pipe'          => [
                    'status' => 'not_issued',
                ],
                'is_custom_supported' => ByMailHandler::isCustomPipeSupported(),
                'webhook_url'         => $this->getWebhookUrl($box)
            ];
        }

        $status = 'unknown';
        $licenseKey = $this->getLicenseKey();

        $errorMessage = '';

        if ($licenseKey) {
            $remoteStatus = (new Api)->getPipeEmailStatus([
                'box_token'    => $this->getBoxSecret($box),
                'license'      => $licenseKey,
                'mapped_email' => $box->mapped_email
            ]);

            if (is_wp_error($remoteStatus)) {
                // handle error here
                $errorMessage = $remoteStatus->get_error_message();
            } else {
                $status = $remoteStatus['status'];
            }
        } else {
            $errorMessage = 'License Key could not be found. Please Activate Fluent Support License First';
        }

        return [
            'email_pipe'          => [
                'status'       => $status,
                'mapped_email' => $box->mapped_email,
            ],
            'is_custom_supported' => ByMailHandler::isCustomPipeSupported(),
            'webhook_url'         => $this->getWebhookUrl($box),
            'error_message'       => $errorMessage
        ];

    }

    public function issueMappedEmail(Request $request, $box_id)
    {
        $box = MailBox::findOrFail($box_id);

        if ($box->box_type != 'email') {
            return $this->sendError([
                'message' => 'This is a web type business inbox. No email piping is available'
            ]);
        }

        if ($box->mapped_email) {
            return $this->sendError([
                'message' => 'Mapped email has been already issued'
            ]);
        }

        $licenseKey = $this->getLicenseKey();


        if ($licenseKey) {
            // we don't have any mapped email yet. So let's get a new mapped email
            $data = [
                'license'     => $licenseKey,
                'email'       => $box->email,
                'site_url'    => site_url(),
                'webhook_url' => $this->getWebhookUrl($box),
                'box_token'   => $this->getBoxSecret($box)
            ];

            $response = (new Api())->issuePipeEmail($data);
            if (is_wp_error($response)) {
                return $this->sendError([
                    'message' => $response->get_error_message()
                ]);
            }
        } else {
            return $this->sendError([
                'message' => __('Please activate Fluent Support license', 'fluent-support-pro')
            ]);
        }

        if (isset($response['masked_email_id']) && is_email($response['masked_email_id'])) {
            $box->mapped_email = $response['masked_email_id'];
            $box->save();
            $response['mapped_email'] = $response['masked_email_id'];
        }

        return [
            'message'    => 'Mailbox mapped email has been generated',
            'email_pipe' => $response
        ];
    }

    public function pipePayload(Request $request, $box_id, $token)
    {
        $box = MailBox::find($box_id);

        if (!$box) {
            $box = MailBox::where('box_type', 'email')->first();
        }

        if ($this->getBoxSecret($box) != $token) {
            return $this->sendError([
                'message' => 'Token Mismatch'
            ]);
        }

        $data = $request->get('payload');

        $data = json_decode($data, true);

        $data = apply_filters('fluent_support_pro/email_piping_raw_data', $data, $box);

        $formattedData = [
            'subject'     => Arr::get($data, 'subject'),
            'content'     => html_entity_decode(Arr::get($data, 'body_text'), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'message_id'  => Arr::get($data, 'messageId'),
            'attachments' => Arr::get($data, 'attachments', []),
            'isMarkDown'  => !!Arr::get($data, 'isMarkDown')
        ];

        $customerArr = Arr::get($data, 'from.value', []);
        if ($customerArr) {
            if (!count($customerArr)) {
                return $this->sendError([
                    'message' => 'from.value as an array is required'
                ]);
            }

            if (!empty($data['forwarded']) && is_email($data['forwarded']['address'])) {
                $customerArr = $data['forwarded'];
            } else {
                $customerArr = $customerArr[0];
            }

            $formattedData['sender'] = [
                'name'  => Arr::get($customerArr, 'name'),
                'email' => Arr::get($customerArr, 'address')
            ];
        } else {
            $formattedData['sender'] = [
                'name'  => Arr::get($data, 'from_name'),
                'email' => Arr::get($data, 'from_email')
            ];
        }

        if (empty($formattedData['sender']['email']) || !is_email($formattedData['sender']['email'])) {
            return $this->sendError([
                'message' => 'From Email address is not valid'
            ]);
        }

        //Collecting Email CC information and formatting
        $CcValueArr = Arr::get($data, 'cc.value', []);
        $receiverArr = Arr::get($data, 'to.value', []);

        if (count($receiverArr) > 1) {
            $CcValueArr = array_merge($CcValueArr, $receiverArr);
        }

        $ccEmails = array_unique(array_column($CcValueArr, 'address'));
        $formattedData['carbon_copy'] = array_diff($ccEmails, [$box->email, $box->mapped_email]);

        //Email CC information collection and formatting EMD

        $formattedData = apply_filters('fluent_support_pro/email_piping_data', $formattedData, $data, $box);

        $response = ByMailHandler::handleEmailData($formattedData, $box);

        if (is_array($response)) {
            return Arr::only($response, ['type', 'ticket_id', 'response_id']);
        }

        if (is_wp_error($response)) {
            return [
                'type'    => 'error',
                'message' => $response->get_error_message()
            ];
        }

        return $response;

    }

    protected function getWebhookUrl($mailBox)
    {
        $token = $this->getBoxSecret($mailBox);

        $app = App::getInstance();

        $ns = $app->config->get('app.rest_namespace');
        $v = $app->config->get('app.rest_version');

        return rest_url($ns . '/' . $v . '/mail-piping/' . $mailBox->id . '/push/' . $token);
    }

    protected function getBoxSecret($mailBox)
    {
        if (!$token = $mailBox->getMeta('_webhook_token')) {
            $token = substr(md5(wp_generate_uuid4()) . '_' . $mailBox->id . '_' . mt_rand(100, 10000), 0, 16);
            $mailBox->saveMeta('_webhook_token', $token);
        }
        return $token;
    }

    protected function getLicenseKey()
    {
        $licenseData = get_option('__fluentsupport_pro_license');

        if ($licenseData && !empty($licenseData['license_key']) && $licenseData['status'] == 'valid') {
            return $licenseData['license_key'];
        }

        return false;
    }
}
