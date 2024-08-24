<?php
defined('ABSPATH') || die();
/** @var $this NextendSocialProviderAdmin */

$lastUpdated = '2023-09-11';

$provider = $this->getProvider();
?>
<div class="nsl-admin-sub-content">
    <div class="nsl-admin-getting-started">
        <h2 class="title"><?php _e('Getting Started', 'nextend-facebook-connect'); ?></h2>

        <p><?php printf(__('To allow your visitors to log in with their %1$s account, first you must create a %1$s App. The following guide will help you through the %1$s App creation process. After you have created your %1$s App, head over to "Settings" and configure the given "%2$s" and "%3$s" according to your %1$s App.', 'nextend-facebook-connect'), "Spotify", "Cliend ID", "Client Secret"); ?></p>

        <p><?php do_action('nsl_getting_started_warnings', $provider, $lastUpdated); ?></p>

        <h2 class="title"><?php printf(_x('Create %s', 'App creation', 'nextend-facebook-connect'), 'Spotify App'); ?></h2>

        <ol>
            <li><?php printf(__('Navigate to %s', 'nextend-facebook-connect'), '<a href="https://developer.spotify.com/dashboard" target="_blank">https://developer.spotify.com/dashboard</a>'); ?></li>
            <li>
                <?php printf(__('Log in with your %s credentials if you are not logged in.', 'nextend-facebook-connect'), 'Spotify'); ?>
            </li>
            <li><?php printf(__('Click on the purple "%s" button!', 'nextend-facebook-connect'), '<b>Create App</b>'); ?></li>
            <li><?php printf(__('Enter an "%1$s" and "%2$s" of your choice (they will be displayed to the user on the grant screen).', 'nextend-facebook-connect'), '<b>App Name</b>', '<b>App description</b>'); ?>
            <li><?php printf(__('Fill the %1$s field with the url of your homepage, probably: %2$s', 'nextend-facebook-connect'), '"<b>Website</b>"', '<b>' . site_url() . '</b>'); ?></li>
            <li>
                <?php
                $loginUrls = $provider->getAllRedirectUrisForAppCreation();
                printf(__('Add the following URL to the %s field:', 'nextend-facebook-connect'), '"<b>Redirect URI</b>"');
                echo "<ul>";
                foreach ($loginUrls as $loginUrl) {
                    echo "<li><strong>" . $loginUrl . "</strong></li>";
                }
                echo "</ul>";
                ?>
            </li>
            <li><?php printf(__('Choose the "%1$s" option at "%2$s".', 'nextend-facebook-connect'), '<b>Web API</b>', '<b>Which API/SDKs are you planning to use?</b>'); ?>
            <li><?php printf(__('Read through the "%1$s" and put a tick in the checkbox.', 'nextend-facebook-connect'), '<b>Developer Terms of Service</b>'); ?>
            <li><?php printf(__('Click on "%1$s". Your application is now registered, and you\'ll be redirected to the app overview page.', 'nextend-facebook-connect'), '<b>Save</b>'); ?>
            <li><?php printf(__('Click on the the %1$s button.', 'nextend-facebook-connect'), '<b>Settings</b>'); ?></li>
            <li><?php printf(__('Copy and save the "%1$s" value. This is what you will use later on the Settings tab for the "%1$s" field.', 'nextend-facebook-connect'), '<b>Client ID</b>'); ?></li>
            <li><?php printf(__('Click on "%1$s", copy and save the "%2$s" value. This is what you will use later on the Settings tab for the "%2$s" field.', 'nextend-facebook-connect'), '<b>View client secret</b>', '<b>Client Secret</b>'); ?></li>
            <li><?php printf(__('Currently your app is in %1$s which  means that, up to 25 authenticated %2$s users can use your app. If you want anybody with a %2$s account to use your app, then you need to move to %3$s. To request %3$s, use the navigation menu and go to %4$s, then hit "%5$s", and go through the "%6$s" process. In the last step you will need to choose the following OAuth scope: %7$s and all the other extra scopes connected to the %8$s fields you enabled.', 'nextend-facebook-connect'), '<b><a href="https://developer.spotify.com/documentation/web-api/concepts/quota-modes#development-mode" target="_blank">Development mode</a></b>', 'Spotify', '"Extended quota mode"', '"<b>Extension Requests</b>"', '<b>Start</b>', '<b>Extension Request</b>', '<b>user-read-private</b>', __('Sync data', 'nextend-facebook-connect')); ?></li>
            <li><?php printf(__('Once %1$s reviewed and approved your request ( it can take up to 6 weeks ), find the "%2$s" and "%3$s" values you copied earlier. Enter these for the fields with the same name on the "%4$s" tab.', 'nextend-facebook-connect'), 'Spotify', '<b>Client ID</b>', '<b>Client Secret</b>', __('Settings', 'nextend-facebook-connect')); ?></li>
            <a href="<?php echo $this->getUrl('settings'); ?>"
               class="button button-primary"><?php printf(__('I am done setting up my %s', 'nextend-facebook-connect'), 'Spotify App'); ?></a>
    </div>
</div>