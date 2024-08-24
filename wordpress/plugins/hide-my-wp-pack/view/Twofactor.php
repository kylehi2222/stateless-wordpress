<?php if(!isset($view)) return; ?>
<noscript> <style>#hmwp_wrap .tab-panel:not(.tab-panel-first){display: block}</style> </noscript>
<div id="hmwp_wrap" class="d-flex flex-row p-0 my-3">
    <?php echo $view->getAdminTabs(HMWPP_Classes_Tools::getValue('page', 'hmwp_templogin')); ?>
    <div class="hmwp_row d-flex flex-row p-0 m-0">
        <div class="hmwp_col flex-grow-1 px-3 py-3 mr-2 mb-3 bg-white">

            <div id="logins" class="card col-sm-12 p-0 m-0 tab-panel tab-panel-first">
                <h3 class="card-title hmwp_header p-2 m-0"><?php echo esc_html__('Two-factor Authentication', 'hide-my-wp-pack'); ?>
                    <a href="<?php echo esc_url(HMWPP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/two-factor-authentication/#monitor-2fa-logins--bbb97359-2431-4bd9-b247-3f7a0c561cfe') ?>" target="_blank" class="d-inline-block ml-2" style="color: white"><i class="dashicons dashicons-editor-help"></i></a>
                </h3>
                <div class="card-body">
                    <?php if (HMWPP_Classes_Tools::getOption('hmwp_2falogin') ) {
                        echo $view->getLogListTable();
                    }else{ ?>
                        <div class="col-sm-12 p-1 text-center">
                            <a href="#settings" class="btn btn-default hmwp_nav_item" data-tab="settings"><?php echo esc_html__('Activate TwoFactor Authentication', 'hide-my-wp-pack'); ?></a>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <form method="POST">
                <?php wp_nonce_field('hmwpp_2fasettings', 'hmwp_nonce') ?>
                <input type="hidden" name="action" value="hmwpp_2fasettings"/>

                <?php do_action('hmwpp_two_factor_form_beginning') ?>

                <div id="settings" class="card col-sm-12 p-0 m-0 tab-panel ">
                    <h3 class="card-title hmwp_header p-2 m-0"><?php echo esc_html__('Two-factor Authentication Settings', 'hide-my-wp-pack'); ?>
                        <a href="<?php echo esc_url(HMWPP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/two-factor-authentication/') ?>" target="_blank" class="d-inline-block float-right mr-2" style="color: white"><i class="dashicons dashicons-editor-help"></i></a>
                    </h3>
                    <div class="card-body">
                        <div class="col-sm-12 row mb-1 py-1 mx-2 ">
                            <div class="checker col-sm-12 row my-2 py-1">
                                <div class="col-sm-12 p-0 switch switch-sm">
                                    <input type="hidden" name="hmwp_2falogin" value="0"/>
                                    <input type="checkbox" id="hmwp_2falogin" name="hmwp_2falogin" class="switch" <?php echo(HMWPP_Classes_Tools::getOption('hmwp_2falogin') ? 'checked="checked"' : '') ?> value="1"/>
                                    <label for="hmwp_2falogin"><?php echo esc_html__('Use 2FA Authentication', 'hide-my-wp-pack'); ?>
                                        <a href="<?php echo esc_url(HMWPP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/two-factor-authentication/#activate') ?>" target="_blank" class="d-inline ml-1"><i class="dashicons dashicons-editor-help d-inline"></i></a>
                                    </label>
                                    <div class="text-black-50 ml-5 pl-2"><?php echo esc_html__('Add an extra layer of security to your online accounts by requiring both a password and a second verification method, such as a text message or app-generated code, to log in.', 'hide-my-wp-pack'); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="hmwp_2falogin">

                            <div class="border-top"></div>
                            <input type="hidden" value="<?php echo(HMWPP_Classes_Tools::getOption('hmwp_2fa_totp') ? '1' : '0') ?>" name="hmwp_2fa_totp">
                            <input type="hidden" value="<?php echo(HMWPP_Classes_Tools::getOption('hmwp_2fa_email') ? '1' : '0') ?>" name="hmwp_2fa_email">

                            <div class="col-sm-12 group_autoload d-flex justify-content-center btn-group btn-group-lg mt-3 px-0" role="group" >
                                <button type="button" class="btn btn-outline-info hmwp_2fa_totp mx-1 py-4 px-4 <?php echo(HMWPP_Classes_Tools::getOption('hmwp_2fa_totp') ? 'active' : '') ?>"><?php echo esc_html__('2FA Code', 'hide-my-wp-pack'); ?></button>
                                <button type="button" class="btn btn-outline-info hmwp_2fa_email mx-1 py-4 px-4 <?php echo(HMWPP_Classes_Tools::getOption('hmwp_2fa_email') ? 'active' : '') ?>"><?php echo esc_html__("Email Code", 'hide-my-wp-pack'); ?></button>
                            </div>

                            <div class="hmwp_2fa_email" <?php echo(HMWPP_Classes_Tools::getOption('hmwp_2fa_totp') ? 'style="display:none"' : '') ?>>
                                <div class="col-12 py-4 px-2 text-danger"><?php echo sprintf(esc_html__('Guarantee email delivery using the complimentary email plugin like %s','hide-my-wp-pack'), '<a href="https://wordpress.org/plugins/wp-mail-smtp/" target="_blank">WP Mail SMTP by WPForms</a>') ?></div>
                            </div>

                            <div class="hmwp_2falogin_limits">

                                <div class="col-sm-12 row border-bottom border-light py-3 mx-0 my-3">
                                    <div class="col-md-4 p-0 font-weight-bold">
                                        <?php echo esc_html__('Max fail attempts', 'hide-my-wp-pack'); ?>:
                                        <div class="small text-black-50"><?php echo esc_html__('Block IP on login page', 'hide-my-wp-pack'); ?></div>
                                    </div>
                                    <div class="col-md-2 p-0 input-group">
                                        <input type="text" class="form-control bg-input" name="hmwp_2falogin_max_attempts" value="<?php echo HMWPP_Classes_Tools::getOption('hmwp_2falogin_max_attempts') ?>"/>
                                    </div>
                                </div>

                                <div class="col-sm-12 row border-bottom border-light py-3 mx-0 my-3">
                                    <div class="col-md-4 p-0 font-weight-bold">
                                        <?php echo esc_html__('Ban duration', 'hide-my-wp-pack'); ?>:
                                        <div class="small text-black-50"><?php echo esc_html__('No. of seconds', 'hide-my-wp-pack'); ?></div>
                                    </div>
                                    <div class="col-md-2 p-0 input-group input-group">
                                        <input type="text" class="form-control bg-input" name="hmwp_2falogin_max_timeout" value="<?php echo HMWPP_Classes_Tools::getOption('hmwp_2falogin_max_timeout') ?>"/>
                                    </div>
                                </div>

                                <div class="col-sm-12 row border-bottom border-light py-3 mx-0 my-3">
                                    <div class="col-md-4 p-0 font-weight-bold">
                                        <?php echo esc_html__('Failed Attempts Message', 'hide-my-wp-pack'); ?>:
                                        <div class="small text-black-50"><?php echo esc_html__('Show alert message for a specific user when there were fail attempts on his account.', 'hide-my-wp-pack'); ?></div>
                                        <div class="small text-black-50"><?php echo esc_html__('Variables: {count} - no. of tries, {time} - time since last fail.', 'hide-my-wp-pack'); ?></div>
                                    </div>
                                    <div class="col-md-8 p-0 input-group input-group">
                                        <textarea type="text" class="form-control bg-input" name="hmwp_2falogin_fail_message" style="height: 120px" placeholder="<?php echo HMWPP_Classes_Tools::getOption('hmwp_2falogin_fail_message') ?>"></textarea>
                                    </div>
                                </div>

                                <div class="col-sm-12 row border-bottom border-light py-3 mx-0 my-3">
                                    <div class="col-md-4 p-0 font-weight-bold">
                                        <?php echo esc_html__('Lockout Message', 'hide-my-wp-pack'); ?>:
                                        <div class="small text-black-50"><?php echo esc_html__('Show message instead over login form.', 'hide-my-wp-pack'); ?></div>
                                        <div class="small text-black-50"><?php echo esc_html__('Variables: {time} - time since available again.', 'hide-my-wp-pack'); ?></div>
                                    </div>
                                    <div class="col-md-8 p-0 input-group input-group">
                                        <textarea type="text" class="form-control bg-input" name="hmwp_2falogin_message" style="height: 80px" placeholder="<?php echo HMWPP_Classes_Tools::getOption('hmwp_2falogin_message') ?>"></textarea>
                                    </div>
                                </div>

                                <div class="col-sm-12 row mb-1 py-1 mx-2 ">
                                    <div class="checker col-sm-12 row my-2 py-1">
                                        <div class="col-sm-12 p-0 switch switch-sm switch-red">
                                            <input type="hidden" name="hmwp_2falogin_delete_uninstal" value="0"/>
                                            <input type="checkbox" id="hmwp_2falogin_delete_uninstal" name="hmwp_2falogin_delete_uninstal" class="switch" <?php echo(HMWPP_Classes_Tools::getOption('hmwp_2falogin_delete_uninstal') ? 'checked="checked"' : '') ?> value="1"/>
                                            <label for="hmwp_2falogin_delete_uninstal"><?php echo esc_html__('Delete 2FA Data on Plugin Uninstall', 'hide-my-wp-pack'); ?></label>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <?php do_action('hmwpp_two_factor_form_end') ?>

                <div class="col-sm-12 m-0 p-2 bg-light text-center" style="position: fixed; bottom: 0; right: 0; z-index: 100; box-shadow: 0 0 8px -3px #444;">
                    <button type="submit" class="btn rounded-0 btn-success px-5 mr-3 save"><?php echo esc_html__('Save', 'hide-my-wp-pack'); ?></button>
                    <?php if (HMWPP_Classes_Tools::getOption('hmwp_2falogin') ) {?>
                        <a href="<?php echo admin_url('profile.php') . '#hmwp_two_factor_options' ?>" class="btn rounded-0 btn-success px-5 mr-5" ><?php echo esc_html__('Add Two Factor Authentication', 'hide-my-wp-pack'); ?></a>
                    <?php }?>
                </div>
            </form>

        </div>

        <div class="hmwp_col hmwp_col_side p-0 m-0 mr-2">
            <div class="card col-sm-12 m-0 p-0 rounded-0">
                <div class="card-body f-gray-dark text-left">
                    <h3 class="card-title"><?php echo esc_html__('2FA Logins', 'hide-my-wp-pack'); ?></h3>
                    <div class="text-info"><?php echo sprintf(esc_html__("Add an extra layer of security to your online accounts by requiring both a password and a second verification method, such as a text message or app-generated code, to log in.", 'hide-my-wp-pack'),  '<br><br>'); ?>
                    </div>
                </div>
            </div>

        </div>

    </div>


</div>

