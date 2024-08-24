<?php

namespace FluentSupportPro\App\Services\TicketImporter;

use FluentSupport\App\Services\Tickets\Importer\BaseImporter;

class FreshDeskTickets extends BaseImporter
{
    protected $handler = 'freshdesk';
    public $accessToken;
    public $mailbox_id;
    private $domain;
    protected $limit = 10;
    private $hasMore;
    private $currentPage;
    private $totalTickets;
    private $originId;
    private $responseCount;

    public function stats()
    {
        return [
            'name'          => esc_html('Freshdesk'),
            'handler'       => $this->handler,
            'type'          => 'sass',
            'last_migrated' => get_option('_fs_migrate_freshdesk')
        ];
    }

    public function doMigration($page, $handler)
    {
        $this->currentPage = $page;
        $this->handler = $handler;
        $tickets = $this->ticketsWithReply();
        $results = $this->migrateTickets($tickets);


        $completedNow = isset($results['inserts']) ? count($results['inserts']) : 0;

        $response = [
            'handler'       => $this->handler,
            'insert_ids'    => $results['inserts'],
            'skips'         => count($results['skips']),
            'has_more'      => $this->hasMore,
            'completed'     => $completedNow,
            'imported_page' => $page,
            'total_pages'   => null,
            'next_page'     => $page + 1,
            'total_tickets' => null,
            'remaining'     => 0
        ];

        if (!$this->hasMore) {
            $response['message'] = __('All tickets has been imported successfully', 'fluent-support-pro');
            update_option('_fs_migrate_freshdesk', current_time('mysql'), 'no');
        }

        return $response;
    }

    private function ticketsWithReply()
    {
        try {
            add_filter('fluent_support/ticket_import_chunk_limit', 10);
            $url = "{$this->domain}/api/v2/tickets?per_page={$this->limit}&page={$this->currentPage}&include=stats,requester,description";
            $tickets = $this->makeRequest($url);
            $formattedTickets = [];
            if (empty($tickets)) {
                $this->hasMore = false;
                return [];
            }

            $this->hasMore = true;

            foreach ($tickets as $ticket) {
                $singleTicketUrl = "{$this->domain}/api/v2/tickets/{$ticket->id}?include=conversations,requester,stats";
                $singleTicket = $this->makeRequest($singleTicketUrl);
                if(!$singleTicket){
                    continue;
                }
                $this->originId = $singleTicket->id;
                $attachments = [];
                
                if ($singleTicket->attachments) {
                    $attachments = $this->getAttachments($singleTicket->attachments);
                }

                $lastCustomerResponse = $singleTicket->stats->requester_responded_at?? $singleTicket->stats->status_updated_at;
                $lastAgentResponse = $singleTicket->stats->agent_responded_at ? date('Y-m-d h:i:s', strtotime($singleTicket->stats->agent_responded_at)) : NULL;

                $formattedTickets[] = [
                    'title'                  => sanitize_text_field($ticket->subject),
                    'content'                => wp_kses_post($ticket->description),
                    'origin_id'              => intval($ticket->id),
                    'source'                 => sanitize_text_field($this->handler),
                    'customer'               => $this->fetchPerson($singleTicket->requester),
                    'replies'                => $this->getReplies($singleTicket->conversations, $singleTicket->requester),
                    'response_count'         => $this->responseCount,
                    'status'                 => $this->getStatus($ticket->status),
                    'client_priority'        => $this->getPriority($ticket->priority),
                    'priority'               => $this->getPriority($ticket->priority),
                    'created_at'             => date('Y-m-d h:i:s', strtotime($ticket->created_at)),
                    'updated_at'             => date('Y-m-d h:i:s', strtotime($ticket->updated_at)),
//                    'waiting_since'  => date('Y-m-d h:i:s', strtotime($response['customerWaitingSince']['time'])),
                    'last_customer_response' => date('Y-m-d h:i:s', strtotime($lastCustomerResponse)),
                    'last_agent_response'    => $lastAgentResponse,
                    'attachments' => $attachments
                ];
            }

            return $formattedTickets;

        } catch (\Exception $e) {
            return [];
        }
    }

    private function getReplies($replies, $requester)
    {
        if( !$requester || !$replies ) {
            return [];
        }

        $formattedReplies = [];
        $user = $this->fetchPerson($requester);
        $this->setResponseCount(count($replies));
        foreach ($replies as $reply) {
            $ticketReply = [
                'content'           => wp_kses_post($reply->body),
                'conversation_type' => 'response',
                'created_at'        => date('Y-m-d h:i:s', strtotime($reply->created_at)),
                'updated_at'        => date('Y-m-d h:i:s', strtotime($reply->updated_at)),
                'is_customer_reply' => ($requester->id === $reply->user_id),
            ];

            if ($requester->id == $reply->user_id) {
                $ticketReply['user'] = $user;
            } else {
                $ticketReply['user'] = $this->fetchPerson($reply->user_id, 'agent', $reply->support_email);
            }

            if (count($reply->attachments)) {
                $ticketReply['attachments'] = $this->getAttachments($reply->attachments);
            }
            $formattedReplies[] = $ticketReply;
        }
        return $formattedReplies;
    }

    private function makeRequest($url)
    {
        $token = base64_encode($this->accessToken . ':X');
        $request = wp_remote_get($url, [
            'headers' => [
                'Authorization' => "Bearer {$token}",
                'Content-Type'  => 'application/json'
            ]
        ]);

        if (is_wp_error($request)) {
            throw new \Exception('Error while making request');
        }

        $response = json_decode(wp_remote_retrieve_body($request));

        return $response;
    }

    private function fetchPerson($personData, $type = 'customer', $email = null)
    {
        if ('agent' == $type) {
            try {
                $url = "{$this->domain}/api/v2/agents/{$personData}";
                $agent = $this->makeRequest($url);

                if(!isset($agent->contact)){
                    $personArray = [
                        'first_name'  => 'Freshdesk anonymous agent',
                        'last_name'   => '',
                        'email' => $email,
                        'person_type' => 'agent',
                    ];
                    return Common::updateOrCreatePerson($personArray);
                }
                $personArray = Common::formatPersonData($agent->contact, $type);

                return Common::updateOrCreatePerson($personArray);
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
        } else {
            $personArray = Common::formatPersonData($personData, $type);
            return Common::updateOrCreatePerson($personArray);
        }
    }

    private function getAttachments($attachments)
    {
        $wpUploadDir = wp_upload_dir();
        $baseDir = $wpUploadDir['basedir'] . '/fluent-support/freshdesk-ticket-' . $this->originId . '/';

        $formattedAttachments = [];
        foreach ($attachments as $attachment) {
            $filePath = Common::downloadFile($attachment->attachment_url, $baseDir, $attachment->name);
            $fileUrl = $wpUploadDir['baseurl'] . '/fluent-support/freshdesk-ticket-' . $this->originId . '/' . $attachment->name;
            $formattedAttachments[] = [
                'full_url'  => $fileUrl,
                'title'     => $attachment->name,
                'file_path' => $filePath,
                'driver'    => 'local',
                'status'    => 'active',
                'file_type' => $attachment->content_type
            ];
        }

        return $formattedAttachments;
    }

    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    private function setResponseCount($count)
    {
        $this->responseCount = $count;
    }

    private function getStatus($statusCode)
    {
        switch ($statusCode) {
            case 2:
                return 'active';
            case 3:
                return 'pending';
            case 4 || 5:
                return 'closed';
            default:
                return 'new';
        }
    }

    private function getPriority($priorityCode)
    {
        switch ($priorityCode) {
            case 1:
                return 'normal';
            case 2:
                return 'medium';
            case 3 || 4:
                return 'critical';
            default:
                return 'normal';
        }
    }

    public function deleteTickets($page)
    {
        return;
    }
}
