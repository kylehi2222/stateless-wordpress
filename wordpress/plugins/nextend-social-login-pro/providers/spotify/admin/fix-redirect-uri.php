<?php
defined('ABSPATH') || die();
/** @var $this NextendSocialProviderAdmin */

$provider = $this->getProvider();
?>
<ol>
    <li><?php printf(__('Navigate to %s', 'nextend-facebook-connect'), '<a href="https://developer.spotify.com/dashboard" target="_blank">https://developer.spotify.com/dashboard</a>'); ?></li>
    <li><?php printf(__('Log in with your %s credentials if you are not logged in.', 'nextend-facebook-connect'), 'Spotify'); ?></li>
    <li><?php printf(__('Click on the name of your %s App.', 'nextend-facebook-connect'), 'Spotify'); ?></li>
    <li><?php printf(__('Find the %1$s button on the top right and scroll down to the %2$s button, and click on it!', 'nextend-facebook-connect'), '"<b>Settings</b>"', '"<b>Edit</b>"'); ?></li>
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
    <li><?php printf(__('Finally, click on the %s button at the bottom.', 'nextend-facebook-connect'), '"<b>Save</b>"'); ?></li>
</ol>