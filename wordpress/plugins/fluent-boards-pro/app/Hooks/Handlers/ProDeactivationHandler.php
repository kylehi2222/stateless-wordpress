<?php

namespace FluentBoardsPro\App\Hooks\Handlers;

class ProDeactivationHandler
{

    public function handle()
    {
        // clear the daily task reminder schedule
        (new ProScheduleHandler())->clearSchedule();
    }
}