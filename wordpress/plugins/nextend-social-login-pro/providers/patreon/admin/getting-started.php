<?php
defined('ABSPATH') || die();
/** @var $this NextendSocialProviderAdmin */

$lastUpdated = '2024-07-29';

$provider = $this->getProvider();
?>
<div class="nsl-admin-sub-content">
    <div class="nsl-admin-getting-started">
        <h2 class="title"><?php _e('Getting Started', 'nextend-facebook-connect'); ?></h2>

        <p><?php printf(__('To allow your visitors to log in with their %1$s account, first you must create an %1$s App. The following guide will help you through the %1$s App creation process. After you have created your %1$s App, head over to "Settings" and configure the given "%2$s" and "%3$s" according to your %1$s App.', 'nextend-facebook-connect'), "Patreon", "Client ID", "Client Secret"); ?></p>

        <p><?php do_action('nsl_getting_started_warnings', $provider, $lastUpdated); ?></p>

        <h2 class="title"><?php printf(_x('Create %s', 'App creation', 'nextend-facebook-connect'), 'Patreon App'); ?></h2>

        <ol>
            <li><?php printf(__('Navigate to %s', 'nextend-facebook-connect'), '<a href="https://www.patreon.com/portal/registration/register-clients" target="_blank">https://www.patreon.com/portal/registration/register-clients</a>'); ?></li>
            <li>
                <?php printf(__('Log in with your %1$s credentials if you are not logged in, or make a new %1$s account.', 'nextend-facebook-connect'), 'Patreon creator'); ?>
                <ul>
                    <li><?php printf(__('%1$s Make sure to verify your %2$s email address, as %2$s does not allow connecting, or linking with a local account without a verified email address. You can find more information about this %3$shere%4$s.', 'nextend-facebook-connect'), '<b>' . __('WARNING:', 'nextend-facebook-connect') . '</b>', 'Patreon', '<a href="https://nextendweb.com/nextend-social-login-docs/provider-patreon/#limitations" target="_blank">', '</a>'); ?></li>
                </ul>
            </li>
            <li><?php printf(__('Click on the %s button.', 'nextend-facebook-connect'), '"<b>Create Client</b>"') ?></li>
            <li><?php printf(__('Enter the name of your App in the %s field.', 'nextend-facebook-connect'), '"<b>App Name</b>"') ?></li>
            <li><?php printf(__('Add the description of your App in the %s field.', 'nextend-facebook-connect'), '"<b>Description</b>"') ?></li>
            <li><?php printf(__('Select the %1$s, pick %2$s.', 'nextend-facebook-connect'), '"<b>App Category</b>"', '"<b>Member Recognition</b>"') ?></li>
            <li><?php printf(__('Enter your %s.', 'nextend-facebook-connect'), '"<b>Author or Company Name</b>"') ?></li>
            <li><?php printf(__('Add the domain of your company to the %1$s field, probably: %2$s', 'nextend-facebook-connect'), '"<b>Company Domain</b>"', '<b>' . (str_replace('www.', '', $_SERVER['HTTP_HOST'])) . '</b>'); ?></li>
            <li><?php printf(__('Add an %s.', 'nextend-facebook-connect'), '"<b>Icon URL</b>"') ?></li>
            <li><?php printf(__('Enter the %s.', 'nextend-facebook-connect'), '"<b>Privacy Policy URL</b>"') ?></li>
            <li><?php printf(__('Enter your %s.', 'nextend-facebook-connect'), '"<b>Terms of Service URL</b>"') ?></li>
            <li><?php
                $loginUrls = $provider->getAllRedirectUrisForAppCreation();
                printf(__('Add the following URL to the %s field:', 'nextend-facebook-connect'), '"<b>Redirect URIs</b>"');
                echo "<ul>";
                foreach ($loginUrls as $loginUrl) {
                    echo "<li><strong>" . $loginUrl . "</strong></li>";
                }
                echo "</ul>";
                ?>
            </li>
            <li><?php printf(__('Make sure that the %1$s is set to %2$s.', 'nextend-facebook-connect'), '"<b>Client API Version</b>"', '"<b>2</b>"') ?></li>
            <li><?php printf(__('Afterwards, press %s.', 'nextend-facebook-connect'), '"<b>Create Client</b>"') ?></li>
            <li><?php _e('You should see your newly created App. Click on the dropdown icon next to the name of your App.', 'nextend-facebook-connect') ?></li>
            <li><?php printf(__('Find the %1$s and %2$s values, and enter these on the %3$s tab into the fields with the same name!', 'nextend-facebook-connect'), '"<b>Client ID</b>"', '"<b>Client Secret</b>"', '"<b>' . __('Settings', 'nextend-facebook-connect') . '</b>"'); ?></li>
        </ol>

        <a href="<?php echo $this->getUrl('settings'); ?>"
           class="button button-primary"><?php printf(__('I am done setting up my %s', 'nextend-facebook-connect'), 'Patreon App'); ?></a>
    </div>
</div>