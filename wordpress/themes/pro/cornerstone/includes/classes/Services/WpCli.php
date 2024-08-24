<?php

namespace Themeco\Cornerstone\Services;

use Themeco\Cornerstone\WpCli\Commands;

class WpCli implements Service {

  public function setup() {

    if ( !class_exists( 'WP_CLI' ) ) {
      return;
    }

    \WP_CLI::add_command( 'cs', Commands::class );

    $themeRoot = CS_ROOT_PATH . '../';

    // Running in standalone mode
    if (!file_exists($themeRoot . 'style.css')) {
      return;
    }

    $frameworkRoot = $themeRoot . 'framework/';

    // Double Check
    if (!file_exists($frameworkRoot . 'legacy/functions/admin/class-validation.php')) {
      trigger_error('Trying to update X or Pro themes from Cornerstone when they are not installed');
      return;
    }

    require_once($frameworkRoot . 'legacy/functions/admin/class-validation.php');
    require_once($frameworkRoot . 'legacy/functions/admin/class-validation.php');
    require_once($frameworkRoot . 'legacy/functions/updates/class-theme-updater.php');
    require_once($frameworkRoot . 'legacy/functions/updates/class-plugin-updater.php');
    require_once($frameworkRoot . 'legacy/functions/admin/class-validation-updates.php');
    require_once($frameworkRoot . 'legacy/functions/admin/class-validation-extensions.php');
    require_once($frameworkRoot . 'legacy/functions/admin/class-validation-theme-options-manager.php');
    require_once($frameworkRoot . 'legacy/functions/admin/setup.php');

    $plugin_updater = new \X_Plugin_Updater;
    $theme_updater = new \X_Theme_Updater;

    add_filter( 'site_transient_update_plugins', [$plugin_updater, 'pre_set_site_transient_update_plugins']);
    add_filter( 'site_transient_update_themes', [$theme_updater, 'pre_set_site_transient_update_themes']);
  }

}
