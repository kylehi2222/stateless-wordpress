<?php
require_once NSL_PATH . '/includes/oauth2.php';

class NextendSocialProviderSpotifyClient extends NextendSocialOauth2 {

    protected $access_token_data = array(
        'access_token' => '',
        'expires_in'   => -1,
        'created'      => -1
    );

    protected $endpointAuthorization = 'https://accounts.spotify.com/authorize';

    protected $endpointAccessToken = 'https://accounts.spotify.com/api/token';

    protected $endpointRestAPI = 'https://api.spotify.com/v1';

    protected $scopes = array(
        'user-read-private'
    );


    protected function extendAuthenticateHttpArgs($http_args) {
        $http_args['headers'] = [
            'Authorization' => 'Basic ' . base64_encode($this->client_id . ':' . $this->client_secret),
            'Content-Type'  => 'application/x-www-form-urlencoded'
        ];
        $http_args['body']    = [
            'grant_type'    => 'authorization_code',
            'code'          => $_GET['code'],
            'redirect_uri'  => $this->redirect_uri,
        ];

        return $http_args;
    }

    /**
     * @param $response
     *
     * @throws Exception
     */
    protected function errorFromResponse($response) {
        if (isset($response['error']) && isset($response['error']['message'])) {
            throw new NSLSanitizedRequestErrorMessageException($response['error']['status'] . ' - ' . $response['error']['message']);
        } else {
            parent::errorFromResponse($response);
        }
    }
}

