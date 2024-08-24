<?php

class NextendSocialLoginPROProviderExtensionSpotify extends NextendSocialLoginPROProviderExtensionWithSyncData {

    /** @var NextendSocialPROProviderSpotify */
    protected $provider;

    public function providerEnabled() {

        parent::providerEnabled();

        add_filter('nsl_' . $this->provider->getId() . '_sync_field_followers', array(
            $this,
            'sync_field_followers'
        ), 10, 2);

        add_filter('nsl_' . $this->provider->getId() . '_sync_field_artists', array(
            $this,
            'sync_field_artists'
        ), 10, 2);

        add_filter('nsl_' . $this->provider->getId() . '_sync_field_product', array(
            $this,
            'sync_field_product'
        ), 10, 2);

        add_filter('nsl_' . $this->provider->getId() . '_sync_field_items', array(
            $this,
            'sync_field_items'
        ), 10, 2);

        //Sync data warning
        add_filter('nsl_' . $this->provider->getId() . '_sync_warning', array(
            $this,
            'spotify_sync_warning'
        ), 10);
    }

    public function sync_field_followers($value, $original_value) {
        if (isset($original_value['total'])) {
            return $original_value['total'];
        }

        return false;
    }

    public function sync_field_product($value, $original_value) {
        if (!empty($original_value)) {
            return ucfirst($original_value);
        }

        return false;
    }

    public function sync_field_artists($value, $original_value) {
        $artitsData = array();
        if (!empty($original_value['items'])) {
            foreach ($original_value['items'] as $artistData) {
                if (!empty($artistData['id']) && !empty($artistData['name']) && !empty($artistData['uri'])) {
                    $artitsData[] = array(
                        'id'   => $artistData['id'],
                        'name' => $artistData['name'],
                        'uri'  => $artistData['uri']
                    );
                }
            }
        }

        return maybe_serialize($artitsData);
    }

    public function sync_field_items($value, $original_value) {
        if (!empty($original_value[0]['artists'][0]['name']) && !empty($original_value[0]['name'])) {
            return maybe_serialize(array(
                'favorite_artist' => $original_value[0]['artists'][0]['name'],
                'favorite_song'   => $original_value[0]['name']
            ));
        }

        return false;
    }

    public function spotify_sync_warning() {
        $sync_warning_message = sprintf(__('The Spotify Sync data requires the %1$s with the necessary scopes enabled!', 'nextend-facebook-connect'), '<a href="https://developer.spotify.com/documentation/web-api/concepts/scopes" target="_blank">Spotify API</a>');

        return $sync_warning_message;
    }

    protected function getRemoteData($node = 'me') {
        switch ($node) {
            case 'me':
                return $this->provider->getMe();
            case 'following':
                return $this->provider->getFollowing();
            case 'top':
                return $this->provider->getTopItems();
        }

        return array();
    }
}