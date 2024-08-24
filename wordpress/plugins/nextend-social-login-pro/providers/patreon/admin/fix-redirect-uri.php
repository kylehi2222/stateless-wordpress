<?php
defined('ABSPATH') || die();
/** @var $this NextendSocialProviderAdmin */

$provider = $this->getProvider();
?>
<ol>
    <li><?php printf(__('Navigate to %s', 'nextend-facebook-connect'), '<a href="https://www.patreon.com/portal/registration/register-clients" target="_blank">https://www.patreon.com/portal/registration/register-clients</a>'); ?></li>
    <li><?php printf(__('Log in with your %s credentials if you are not logged in.', 'nextend-facebook-connect'), 'Patreon'); ?></li>
    <li><?php _e('You will see your Apps here.', 'nextend-facebook-connect') ?></li>
    <li><?php printf(__('Click on the pencil/edit icon next to the name of your %s App.', 'nextend-facebook-connect'), 'Patreon'); ?></li>
    <li><?php printf(__('Scroll down to the %s field.', 'nextend-facebook-connect'), '"<b>Redirect URIs</b>"'); ?></li>
    <li><?php
        $loginUrls = $provider->getAllRedirectUrisForAppCreation();
        _e('Add the following URL:', 'nextend-facebook-connect');
        echo "<ul>";
        foreach ($loginUrls as $loginUrl) {
            echo "<li><strong>" . $loginUrl . "</strong></li>";
        }
        echo "</ul>";
        ?>
    </li>
    <li><?php printf(__('Click on the %s button', 'nextend-facebook-connect'), '"<b>Update Client</b>"'); ?></li>
</ol>