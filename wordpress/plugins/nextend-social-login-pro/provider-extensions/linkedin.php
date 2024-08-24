<?php

class NextendSocialLoginPROProviderExtensionLinkedIn extends NextendSocialLoginPROProviderExtensionWithSyncData {

    /** @var NextendSocialPROProviderLinkedIn */
    protected $provider;

    protected function getRemoteData($node = 'me') {
        switch ($node) {
            case 'me':
                return $this->provider->getMe();
            case 'basicprofile':
                return $this->provider->getBasicProfile();
        }

        return array();
    }
}