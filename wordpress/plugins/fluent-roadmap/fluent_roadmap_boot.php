<?php

!defined('WPINC') && die;

spl_autoload_register(function ($class) {
    $match = 'FluentRoadmap';
    if (!preg_match("/\b{$match}\b/", $class)) {
        return;
    }

    $path = plugin_dir_path(__FILE__);
    $file = str_replace(
        ['FluentRoadmap', '\\', '/App/'],
        ['', DIRECTORY_SEPARATOR, 'app/'],
        $class
    );
    require(trailingslashit($path) . trim($file, '/') . '.php');
});

class FluentRoadmapDependency
{
    public function init()
    {
        $this->injectDependency();
    }

    /**
     * Notify the user about the FluentForm dependency and instructs to install it.
     */
    protected function injectDependency()
    {
        add_action('admin_notices', function () {
            $pluginName = 'fluent-boards';
            $pluginFileName = 'fluent-boards/fluent-boards.php';
            $selfAdminUrl = 'plugins.php?action=activate&plugin=fluent-boards/fluent-boards.php';
            $activatePluginPath = 'activate-plugin_fluent-boards/fluent-boards.php';

            $pluginInfo = $this->getInstallationDetails($pluginName, $pluginFileName, $selfAdminUrl, $activatePluginPath);

            $class = 'notice notice-error';

            $install_url_text = 'Click Here to Install the Plugin';

            if ($pluginInfo->action == 'activate') {
                $install_url_text = 'Click Here to Activate the Plugin';
            }

            $message = 'Fluent Roadmap Requires FluentBoards Base Plugin, <b><a href="' . $pluginInfo->url
                . '">' . $install_url_text . '</a></b>';

            printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), $message);
        });
    }

    /**
     * Get the FluentForm plugin installation information e.g. the URL to install.
     *
     * @return \stdClass $activation
     */
    protected function getInstallationDetails($pluginName, $pluginFileName, $selfAdminUrl, $activatePluginPath)
    {
        $activation = (object) [
            'action' => 'install',
            'url'    => ''
        ];

        $allPlugins = get_plugins();

        if (isset($allPlugins[$pluginFileName])) {
            $url = wp_nonce_url(
                self_admin_url($selfAdminUrl),
                $activatePluginPath
            );

            $activation->action = 'activate';
        } else {
            $api = (object)[
                'slug' => $pluginName
            ];

            $url = wp_nonce_url(
                self_admin_url('update.php?action=install-plugin&plugin=' . $api->slug),
                'install-plugin_' . $api->slug
            );
        }

        $activation->url = $url;

        return $activation;
    }
}

add_action('init', function () {
//     Check if FluentBoards is installed
    if (!defined('FLUENT_BOARDS')) {
        (new FluentRoadmapDependency())->init();
    }
});
