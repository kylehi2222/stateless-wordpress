<?php

class NextendSocialLoginPROProviderExtensionPatreon extends NextendSocialLoginPROProviderExtensionWithSyncData {

    /** @var NextendSocialPROProviderPatreon */
    protected $provider;

    public function providerEnabled() {

        parent::providerEnabled();

        add_filter('nsl_' . $this->provider->getId() . '_sync_field_hide_pledges', array(
            $this,
            'sync_bool_field'
        ), 10, 2);

        add_filter('nsl_' . $this->provider->getId() . '_sync_field_social_connections', array(
            $this,
            'sync_field_social_connections'
        ), 10, 2);

        add_filter('nsl_' . $this->provider->getId() . '_sync_field_data', array(
            $this,
            'sync_field_campaigns'
        ), 10, 2);

        add_filter('nsl_' . $this->provider->getId() . '_sync_warning', array(
            $this,
            'patreon_sync_warning'
        ), 10);


    }

    public function sync_bool_field($value, $original_value) {
        if (isset($original_value) && !empty($original_value)) {
            return 'Yes';
        } else if ($original_value === false) {
            return 'No';
        }

        return false;
    }

    public function sync_field_social_connections($value, $original_value) {
        if (isset($original_value) && !empty($original_value)){
            return $this->filter_array($original_value);
        }
        return false;
    }

    function filter_array($array): array {
        $filteredArray = [];

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $filteredSubArray = $this->filter_array($value);
                if (!empty($filteredSubArray)) {
                    $filteredArray[$key] = $filteredSubArray;
                }
            } elseif ($value !== null && $value !== '') {
                $filteredArray[$key] = $value;
            }
        }

        return $filteredArray;
    }

    public function patreon_sync_warning() {
        return sprintf(__('Most of these information can only be retrieved, when the field is filled on the user\'s %s page! For the campaigns, the user must have a public campaign!', 'nextend-facebook-connect'), '<a href="https://www.patreon.com/settings/basics" target="_blank">Settings</a>');
    }

    protected function getRemoteData($node = 'me') {

        switch ($node) {
            case 'me':
                return $this->provider->getMe();
        }

        return array();
    }
}