<?php

namespace FluentRoadmap\App\Hooks\Handlers;

use FluentRoadmap\App\Database\DBMigrator;
use FluentRoadmap\App\Database\DBSeeder;
use FluentCrm\Framework\Foundation\Application;

class ActivationHandler
{
    protected $app = null;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }
    
    public function handle($network_wide = false)
    {
        DBMigrator::run($network_wide);
        DBSeeder::run();
    }
}
