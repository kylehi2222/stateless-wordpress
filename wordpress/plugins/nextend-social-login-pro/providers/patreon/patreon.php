<?php

use NSL\Notices;

class NextendSocialPROProviderPatreon extends NextendSocialProviderOAuth {

    /** @var NextendSocialProviderPatreonClient */
    protected $client;

    protected $color = '#EB7254';

    protected $svg = '<svg width="24" height="24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 0h4v24H0V0Z" fill="#172234"/><path d="M15.0146 0C10.0432 0 6 4.043 6 9.012 6 13.9705 10.0432 18 15.0146 18 19.9724 18 24 13.9653 24 9.012 24 4.044 19.9714 0 15.0146 0Z" fill="#fff"/><defs><path fill="#fff" d="M0 0h24v24H0z"/><path fill="#fff" d="M0 0h24v24H0z"/></defs></svg>';


    protected $sync_fields = array(
        'created'            => array(
            'label' => 'Created at',
            'node'  => 'me'
        ),
        'hide_pledges'       => array(
            'label' => 'Hide pledges',
            'node'  => 'me'
        ),
        'like_count'         => array(
            'label' => 'Like count',
            'node'  => 'me'
        ),
        'social_connections' => array(
            'label' => 'Social connections',
            'node'  => 'me'
        ),
        'url'                => array(
            'label' => 'Patron/creator URL',
            'node'  => 'me'
        )
    );


    public function __construct() {
        $this->id    = 'patreon';
        $this->label = 'Patreon';

        $this->path = dirname(__FILE__);

        $this->requiredFields = array(
            'client_id'     => 'Client ID',
            'client_secret' => 'Client Secret'
        );

        parent::__construct(array(
            'client_id'      => '',
            'client_secret'  => '',
            'login_label'    => 'Continue with <b>Patreon</b>',
            'register_label' => 'Sign up with <b>Patreon</b>',
            'link_label'     => 'Link account with <b>Patreon</b>',
            'unlink_label'   => 'Unlink account from <b>Patreon</b>'
        ));
    }

    protected function forTranslation() {
        __('Continue with <b>Patreon</b>', 'nextend-facebook-connect');
        __('Sign up with <b>Patreon</b>', 'nextend-facebook-connect');
        __('Link account with <b>Patreon</b>', 'nextend-facebook-connect');
        __('Unlink account from <b>Patreon</b>', 'nextend-facebook-connect');
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
            }
        }

        return $newData;
    }

    /**
     * @return NextendSocialAuth|NextendSocialProviderPatreonClient
     */
    public function getClient() {
        if ($this->client === null) {

            require_once dirname(__FILE__) . '/patreon-client.php';

            $this->client = new NextendSocialProviderPatreonClient($this->id);

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

        $fields = array(
            'email',
            'is_email_verified',
            'full_name',
            'first_name',
            'last_name',
            'thumb_url'
        );

        $extra_fields = apply_filters('nsl_patreon_sync_node_fields', array(), 'me');

        /**
         * Make sure the brackets are URL encoded!
         */
        return $this->getClient()
                    ->get('/identity?' . urlencode('fields[user]') . '=' . implode(',', array_merge($fields, $extra_fields)));
    }

    public function getMe() {
        return $this->authUserData["data"]["attributes"];
    }

    public function getAuthUserData($key) {

        switch ($key) {
            case 'id':
                return $this->authUserData['data']['id'];
            case 'email':
                /**
                 * Patreon stated the following:
                 * - If the Patreon user has the Patreon email verified and there doesn't already exist a local account with the same email at your site/app, you can create a local user with the Patreon email and log the user into that new account. This implements signing up via Patreon in a safe way.
                 * - If the Patreon user has the Patreon email verified and there exists a local account with the same email and the local user has the local email verified, you can connect the local account with the user's Patreon account and log the user into the local account in a safe way.
                 * - If the Patreon user has the Patreon email verified and there exists a local account with the same email but that local account's email is not verified, require the user to log into that local account before linking the Patreon account to the local account.
                 * - If the Patreon user does not have the Patreon email verified, avoid allowing the user to log in, to register or to link that Patreon account with any local account, and show a notification to the user telling that the Patreon email must be verified at Patreon in order to be able to sign into your site/app via Patreon.
                 * For this reason we should check if the user has their email address verified, and if not, do not allow (auto)linking/registration.
                 * See NSLDEV-302
                 */ return $this->authUserData['data']['attributes']['email'];
            case 'email_verified':
                return $this->authUserData['data']['attributes']['is_email_verified'];
            case 'name':
                return $this->authUserData['data']['attributes']['full_name'];
            case 'first_name':
                $name = $this->authUserData['data']['attributes']['first_name'];

                return $name ?? '';
            case 'last_name':
                $name = $this->authUserData['data']['attributes']['last_name'];

                return $name ?? '';
            case 'picture':
                return !empty($this->authUserData['data']['attributes']['thumb_url']) ? $this->authUserData['data']['attributes']['thumb_url'] : '';
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

    public function deleteLoginPersistentData() {
        parent::deleteLoginPersistentData();

        if ($this->client !== null) {
            $this->client->deleteLoginPersistentData();
        }
    }

    public function getProviderEmailVerificationStatus() {
        return $this->getAuthUserData('email_verified');
    }

}

NextendSocialLogin::addProvider(new NextendSocialPROProviderPatreon());