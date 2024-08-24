<?php
require_once NSL_PATH . '/includes/oauth2.php';

class NextendSocialProviderPatreonClient extends NextendSocialOauth2 {

    protected $access_token_data = array(
        'access_token' => '',
        'expires_in'   => -1,
        'created'      => -1
    );


    protected $endpointAuthorization = 'https://www.patreon.com/oauth2/authorize';

    protected $endpointAccessToken = 'https://www.patreon.com/api/oauth2/token';

    protected $endpointRestAPI = 'https://www.patreon.com/api/oauth2/v2';


    protected $scopes = array(
        'identity',
        'identity[email]'
    );

    protected function extendAuthenticateHttpArgs($http_args) {
        $http_args['headers'] = [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];

        return $http_args;
    }

}

