<?php


namespace FluentSupportPro\App\Hooks\Handlers;

use FluentSupportPro\App\Models\Workflow;
use FluentSupportPro\App\Services\Workflow\ConditionChecker;
use FluentSupportPro\App\Services\Workflow\WorkflowRunner;

class WorkflowHandler
{
    public function init()
    {
        add_action('fluent_support/ticket_created', function ($ticket, $customer) {
            $this->maybeHandleAutomaticWorkflow($ticket, $customer, 'fluent_support/ticket_created');
        }, 10, 2);
        add_action('fluent_support/response_added_by_customer', function ($createdResponse, $existingTicket, $customer) {{
            $this->maybeHandleAutomaticWorkflow($createdResponse, $customer, 'fluent_support/response_added_by_customer');
        }}, 10, 3);
        add_action('fluent_support/ticket_closed', function ($ticket, $person) {{
            $this->maybeHandleAutomaticWorkflow($ticket, $person, 'fluent_support/ticket_closed');
        }}, 10, 2);
    }

    public function maybeHandleAutomaticWorkflow($ticket, $customer, $event)
    {
        $workflows = $this->getAutoWorkflows($event);
        if ($workflows->isEmpty()) {
            return false;
        }

        foreach ($workflows as $workflow) {
            if ((new ConditionChecker())->check($workflow, $ticket, $customer)) {
                $runner = (new WorkflowRunner($workflow, $ticket));
                $runner->runActions();
            }
        }

    }


    private function getAutoWorkflows($triggerKey)
    {
        return Workflow::where('status', 'published')
            ->where('trigger_type', 'automatic')
            ->where('trigger_key', $triggerKey)
            ->get();
    }

}
