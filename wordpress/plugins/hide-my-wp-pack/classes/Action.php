<?php
/**
 * Set the ajax action and call for WordPress
 *
 * @file The Actions file
 * @package HMWPP/Action
 * @since 1.0.0
 */

defined('ABSPATH') || die('Cheatin\' uh?');

class HMWPP_Classes_Action extends HMWPP_Classes_FrontController
{

    /**
     * 
     * All the registered actions
     * @var array with all form and ajax actions
     */
    var $actions = array();

    /**
     * The hookAjax is loaded as custom hook in hookController class
     *
     * @return void
     * @throws Exception
     */
    public function hookInit()
    {
        if (HMWPP_Classes_Tools::isAjax()) {
            $this->getActions(true);
        }
    }

    /**
     * The hookSubmit is loaded when action si posted
     *
     * @throws Exception
     * @return void
     */
    function hookMenu()
    {
        /* Only if post */
        if (!HMWPP_Classes_Tools::isAjax()) {
            $this->getActions();
        }
    }

    /**
     * Hook the Multisite Menu
     *
     * @throws Exception
     */
    function hookMultisiteMenu()
    {
        /* Only if post */
        if (!HMWPP_Classes_Tools::isAjax()) {
            $this->getActions();
        }
    }

    /**
     * Get the list with all the plugin actions
     *
     * @since 1.0.0
     * @return array
     */
    public function getActionsTable()
    {
        return array(
           array(
                "name" => "HMWPP_Controllers_Settings",
                "actions" => array(
                    "action" => array(
                        "hmwpp_settings",
                    )
                ),
            ),
            array(
                "name" => "HMWPP_Controllers_Templogin",
                "actions" => array(
                    "action" => array(
                        "hmwpp_temploginsettings",
                        "hmwpp_templogin_block",
                        "hmwpp_templogin_activate",
                        "hmwpp_templogin_delete",
                        "hmwpp_templogin_new",
                        "hmwpp_templogin_update",
                    )
                ),
            ),
            array(
                "name" => "HMWPP_Controllers_Uniquelogin",
                "actions" => array(
                    "action" => array(
                        "hmwpp_uniquelogin_settings",
                        "hmwpp_uniquelogin_new",
                    )
                ),
            ),
          array(
                "name" => "HMWPP_Controllers_Twofactor",
                "actions" => array(
                    "action" => array(
                        "hmwpp_2fasettings",
                        "hmwpp_totp_submit",
                        "hmwpp_totp_reset",
                        "hmwpp_codes_generate",
                        "hmwpp_email_submit",
                        "hmwpp_email_reset",
                    )
                ),
            ),
           array(
               "name" => "HMWPP_Classes_Error",
               "actions" => array(
                   "action" => array(
                       "hmwpp_ignoreerror"
                   )
               ),
           ),
        );
    }


    /**
     * Get all actions from config.json in core directory and add them in the WP
     *
     * @since 1.0.0
     * @param  bool $ajax
     * @throws Exception
     */
    public function getActions($ajax = false)
    {
        //Proceed only if logged in and in dashboard
        if (! is_admin() && ! is_network_admin() ) {
            return;
        }

        $this->actions = array();
        $action = HMWPP_Classes_Tools::getValue('action');
        $nonce = HMWPP_Classes_Tools::getValue('hmwp_nonce');

        if ($action == '' || $nonce == '') {
            return;
        }

        //Get all the plugin actions
        $actions = $this->getActionsTable();

        foreach ( $actions as $block ) {
            //If there is a single action
            if (isset($block['actions']['action']) ) {

                //If there are more actions for the current block
                if (! is_array($block['actions']['action']) ) {
                    //Add the action in the actions array
                    if ($block['actions']['action'] == $action ) {
                        $this->actions[] = array( 'class' => $block['name'] );
                    }
                } else {
                    //If there are more actions for the current block
                    foreach ( $block['actions']['action'] as $value ) {
                        //Add the actions in the actions array
                        if ($value == $action ) {
                            $this->actions[] = array( 'class' => $block['name'] );
                        }
                    }
                }
            }
        }

        //Validate referer based on the call type
        if ($ajax) {
            check_ajax_referer($action, 'hmwp_nonce');
        } else {
            check_admin_referer($action, 'hmwp_nonce');
        }

        //Add the actions in WP.
        foreach ($this->actions as $actions) {
            HMWPP_Classes_ObjController::getClass($actions['class'])->action();
        }
    }

}
