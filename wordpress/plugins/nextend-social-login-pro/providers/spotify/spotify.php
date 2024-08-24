<?php

use NSL\Notices;

class NextendSocialPROProviderSpotify extends NextendSocialProviderOAuth {

    /** @var NextendSocialProviderSpotifyClient */
    protected $client;

    protected $color = '#1DB954';
    protected $colorWhite = '#FFFFFF';
    protected $colorBlack = '#191414';

    protected $svg = '<svg width="24" height="24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 0C5.3726 0 0 5.3726 0 12s5.3726 12 12 12 12-5.3726 12-12S18.6274 0 12 0Zm5.2226 17.4153a.8206.8206 0 0 1-1.1226.2944c-2.1564-1.2602-4.6476-1.4627-6.3581-1.4107-1.8948.0576-3.2843.4317-3.2982.4355-.4367.1193-.8881-.1377-1.008-.5745a.8202.8202 0 0 1 .573-1.008c.0626-.0172 1.5579-.4234 3.6372-.4919 1.2248-.0405 2.4039.0449 3.5041.2536 1.3934.2643 2.6646.7283 3.7781 1.3791a.8207.8207 0 0 1 .2945 1.1225Zm1.5255-3.1685a.9714.9714 0 0 1-1.3296.3488c-2.5543-1.4928-5.5053-1.7326-7.5314-1.6711-2.2445.0683-3.8904.5114-3.9067.5159-.5173.1411-1.052-.1631-1.194-.6804-.1419-.5173.1616-1.0516.6787-1.1941.0742-.0204 1.8453-.5015 4.3083-.5827 1.4509-.0479 2.8475.0532 4.1507.3004 1.6506.3132 3.1563.8628 4.4753 1.6336.4635.2708.6196.8661.3487 1.3296Zm.9379-2.881a1.187 1.187 0 0 1-.6005-.163c-6.0726-3.549-13.95-1.4391-14.0288-1.4174-.635.175-1.2913-.1979-1.4661-.8327-.175-.6349.198-1.2913.8328-1.4661.091-.0251 2.2635-.6152 5.2846-.7148 1.7797-.0586 3.4928.0653 5.0912.3685 2.0247.3841 3.8716 1.0582 5.4894 2.0037.5685.3323.7601 1.0624.4279 1.6309a1.1918 1.1918 0 0 1-1.0305.5909Z" fill="#fff"/><defs><path fill="#fff" d="M0 0h24v24H0z"/><path fill="#fff" d="M0 0h24v24H0z"/></defs></svg>';
    protected $svgBlack = '<svg width="24" height="24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 0C5.3726 0 0 5.3726 0 12s5.3726 12 12 12 12-5.3726 12-12S18.6274 0 12 0Zm5.2226 17.4153a.8206.8206 0 0 1-1.1226.2944c-2.1564-1.2602-4.6476-1.4627-6.3581-1.4107-1.8948.0576-3.2843.4317-3.2982.4355-.4367.1193-.8881-.1377-1.008-.5745a.8202.8202 0 0 1 .573-1.008c.0626-.0172 1.5579-.4234 3.6372-.4919 1.2248-.0405 2.4039.0449 3.5041.2536 1.3934.2643 2.6646.7283 3.7781 1.3791a.8207.8207 0 0 1 .2945 1.1225Zm1.5255-3.1685a.9714.9714 0 0 1-1.3296.3488c-2.5543-1.4928-5.5053-1.7326-7.5314-1.6711-2.2445.0683-3.8904.5114-3.9067.5159-.5173.1411-1.052-.1631-1.194-.6804-.1419-.5173.1616-1.0516.6787-1.1941.0742-.0204 1.8453-.5015 4.3083-.5827 1.4509-.0479 2.8475.0532 4.1507.3004 1.6506.3132 3.1563.8628 4.4753 1.6336.4635.2708.6196.8661.3487 1.3296Zm.9379-2.881a1.187 1.187 0 0 1-.6005-.163c-6.0726-3.549-13.95-1.4391-14.0288-1.4174-.635.175-1.2913-.1979-1.4661-.8327-.175-.6349.198-1.2913.8328-1.4661.091-.0251 2.2635-.6152 5.2846-.7148 1.7797-.0586 3.4928.0653 5.0912.3685 2.0247.3841 3.8716 1.0582 5.4894 2.0037.5685.3323.7601 1.0624.4279 1.6309a1.1918 1.1918 0 0 1-1.0305.5909Z" fill="#191414"/><defs><path fill="#191414" d="M0 0h24v24H0z"/><path fill="#191414" d="M0 0h24v24H0z"/></defs></svg>';

    protected $sync_fields = array(
        'product'   => array(
            'label' => 'Subscription',
            'node'  => 'me'
        ),
        'country'   => array(
            'label' => 'Country',
            'node'  => 'me'
        ),
        'followers' => array(
            'label' => 'Followers',
            'node'  => 'me'
        ),
        'uri'       => array(
            'label' => 'Spotify URI',
            'node'  => 'me'
        ),
        'artists'   => array(
            'label' => 'Followed artists',
            'node'  => 'following',
            'scope' => 'user-follow-read'
        ),
        'items'     => array(
            'label' => 'Favorite',
            'node'  => 'top',
            'scope' => 'user-top-read'
        )
    );

    public function __construct() {
        $this->id    = 'spotify';
        $this->label = 'Spotify';

        $this->path = dirname(__FILE__);

        $this->requiredFields = array(
            'client_id'     => 'Client ID',
            'client_secret' => 'Client Secret'
        );

        parent::__construct(array(
            'client_id'          => '',
            'client_secret'      => '',
            'skin'               => 'green',
            'login_label'        => 'Continue with <b>Spotify</b>',
            'register_label'     => 'Sign up with <b>Spotify</b>',
            'link_label'         => 'Link account with <b>Spotify</b>',
            'unlink_label'       => 'Unlink account from <b>Spotify</b>',
            'profile_image_size' => 'mini',

        ));
    }

    protected function forTranslation() {
        __('Continue with <b>Spotify</b>', 'nextend-facebook-connect');
        __('Sign up with <b>Spotify</b>', 'nextend-facebook-connect');
        __('Link account with <b>Spotify</b>', 'nextend-facebook-connect');
        __('Unlink account from <b>Spotify</b>', 'nextend-facebook-connect');
    }

    public function getRawDefaultButton() {
        $skin = $this->settings->get('skin');
        switch ($skin) {
            case 'black':
                $color = $this->colorBlack;
                $svg   = $this->svg;
                break;
            case 'white':
                $color = $this->colorWhite;
                $svg   = $this->svgBlack;
                break;
            default:
                $color = $this->color;
                $svg   = $this->svg;
        }

        return '<div class="nsl-button nsl-button-default nsl-button-' . $this->id . '" data-skin="' . $skin . '" style="background-color:' . $color . ';"><div class="nsl-button-svg-container">' . $svg . '</div><div class="nsl-button-label-container">{{label}}</div></div>';
    }

    public function getRawIconButton() {
        $skin = $this->settings->get('skin');
        switch ($skin) {
            case 'black':
                $color = $this->colorBlack;
                $svg   = $this->svg;
                break;
            case 'white':
                $color = $this->colorWhite;
                $svg   = $this->svgBlack;
                break;
            default:
                $color = $this->color;
                $svg   = $this->svg;
        }

        return '<div class="nsl-button nsl-button-icon nsl-button-' . $this->id . '" data-skin="' . $skin . '" style="background-color:' . $color . ';"><div class="nsl-button-svg-container">' . $svg . '</div></div>';
    }

    public function validateSettings($newData, $postedData) {
        $newData = parent::validateSettings($newData, $postedData);

        foreach ($postedData as $key => $value) {

            switch ($key) {
                case 'tested':
                    if ($postedData[$key] == '1' && (!isset($newData['tested']) || $newData['tested'] != '0')) {
                        $newData['tested'] = 1;
                    } else {
                        $newData['tested'] = 0;
                    }
                    break;
                case 'client_id':
                case 'client_secret':
                    $newData[$key] = trim(sanitize_text_field($value));
                    if ($this->settings->get($key) !== $newData[$key]) {
                        $newData['tested'] = 0;
                    }

                    if (empty($newData[$key])) {
                        Notices::addError(sprintf(__('The %1$s entered did not appear to be a valid. Please enter a valid %2$s.', 'nextend-facebook-connect'), $this->requiredFields[$key], $this->requiredFields[$key]));
                    }
                    break;
                case 'skin':
                case 'profile_image_size':
                    $newData[$key] = trim(sanitize_text_field($value));
                    break;
            }
        }

        return $newData;
    }

    /**
     * @return NextendSocialAuth|NextendSocialProviderSpotifyClient
     */
    public function getClient() {
        if ($this->client === null) {

            require_once dirname(__FILE__) . '/spotify-client.php';

            $this->client = new NextendSocialProviderSpotifyClient($this->id);

            $this->client->setClientId($this->settings->get('client_id'));
            $this->client->setClientSecret($this->settings->get('client_secret'));
            $this->client->setRedirectUri($this->getRedirectUriForAuthFlow());
        }

        return $this->client;
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function getCurrentUserInfo() {

        return $this->getClient()
                    ->get('/me');
    }

    public function getMe() {
        return $this->authUserData;
    }

    public function getFollowing() {
        $following_fields = apply_filters('nsl_spotify_sync_node_fields', array(), 'following');

        if (!empty($following_fields)) {
            return $this->getClient()
                        ->get('/me/following?type=artist');
        }

        return array();
    }

    public function getTopItems() {

        $top_fields = apply_filters('nsl_spotify_sync_node_fields', array(), 'top');

        if (!empty($top_fields)) {
            $artistsData = $this->getClient()
                                ->get('/me/top/artists?limit=1');
            $tracksData  = $this->getClient()
                                ->get('/me/top/tracks?limit=1');

            return array_merge($artistsData, $tracksData);
        }

        return array();
    }

    public function getAuthUserData($key) {

        switch ($key) {
            case 'id':
                return $this->authUserData['id'];
            case 'email':
                return '';
            case 'name':
                return $this->authUserData['display_name'];
            case 'first_name':
                $name = explode(' ', $this->getAuthUserData('name'), 2);

                return isset($name[0]) ? $name[0] : '';
            case 'last_name':
                $name = explode(' ', $this->getAuthUserData('name'), 2);

                return isset($name[1]) ? $name[1] : '';
            case 'picture':
                $profile_image_size = $this->settings->get('profile_image_size');
                $avatar_url         = '';
                switch ($profile_image_size) {
                    case 'large':
                        if (!empty($this->authUserData['images'][1]['url'])) {
                            $avatar_url = $this->authUserData['images'][1]['url'];
                        }
                        break;
                    default:
                        if (!empty($this->authUserData['images'][0]['url'])) {
                            $avatar_url = $this->authUserData['images'][0]['url'];
                        }
                        break;
                }


                return $avatar_url;
        }

        return parent::getAuthUserData($key);
    }

    public function syncProfile($user_id, $provider, $data) {
        if ($this->needUpdateAvatar($user_id)) {

            if ($this->getAuthUserData('picture')) {
                $this->updateAvatar($user_id, $this->getAuthUserData('picture'));
            }
        }

        if (!empty($data['access_token_data'])) {
            $this->storeAccessToken($user_id, $data['access_token_data']);
        }
    }

    public function getSyncDataFieldDescription($fieldName) {
        if (isset($this->sync_fields[$fieldName]['scope'])) {
            return sprintf(__('Required scope: %1$s', 'nextend-facebook-connect'), $this->sync_fields[$fieldName]['scope']);
        }

        return parent::getSyncDataFieldDescription($fieldName);
    }

    public function deleteLoginPersistentData() {
        parent::deleteLoginPersistentData();

        if ($this->client !== null) {
            $this->client->deleteLoginPersistentData();
        }
    }

}

NextendSocialLogin::addProvider(new NextendSocialPROProviderSpotify());