<?php

namespace FluentRoadmap\App\Hooks\Handlers;

use FluentBoards\App\App;

class AdminMenuHandler
{
	public function register()
	{
        $allowed = $this->checkDependencies();
        if(!$allowed) {
            return;
        }

	}


	public function renderRoadmapApp()
	{
		$app = App::getInstance();

		$menuItems =  (new \FluentBoards\App\Hooks\Handlers\AdminMenuHandler())->getMenuItems();

		$app['view']->render('admin.menu', [
			'menuItems' => $menuItems,
			'slug' => 'fluent-boards',
			'logo' => FLUENT_ROADMAP_PLUGIN_URL . 'assets/images/logo.svg',
			'base_url' => fluent_boards_page_url()
		]);
	}

    private function checkDependencies()
    {
       if(defined('FLUENT_BOARDS_PRO')) {
            return true;
        }
        return false;
    }

}

