<?php

// Get block settings.
[
    'content_position' => $content_position,
    'guest_behavior' => $guest_behavior,
    'show_name' => $show_name,
    'compact_mode' => $compact_mode,
    'show_avatar' => $show_avatar,
    'show_notifications' => $show_notifications,
    'show_usermenu' => $show_usermenu,
    'show_logout' => $show_logout,
    'show_vip' => $show_vip,
    'show_badges' => $show_badges,
] = $attributes;

$compact_mode_class = '';

// Disable compact mode on preview.
if (isset($compact_mode) && !$preview) {
    $compact_mode = (int) $compact_mode;
    if (in_array($compact_mode, [1, 2, 3])) {
        if (in_array($compact_mode, [1, 3])) $compact_mode_class .= ' psw-userbar--mobile';
        if (in_array($compact_mode, [2, 3])) $compact_mode_class .= ' psw-userbar--desktop';
    }
}

?><div class="psw-userbar psw-userbar--<?php echo $content_position; ?> ps-js-widget-userbar <?php echo $compact_mode_class; ?>">
    <div class="psw-userbar__inner">
    <?php if (is_user_logged_in()) { ?>

        <div class="psw-userbar__user">
            <div class="ps-notifs psw-notifs--userbar ps-js-widget-userbar-notifications"><?php

                do_action('peepso_action_userbar_notifications_before', $user->get_id());
                echo $toolbar;
                do_action('peepso_action_userbar_notifications_after', $user->get_id());

            ?></div>
        </div>

        <?php
        if (isset($show_name)) {
            $show_name = (int) $show_name;
            if (in_array($show_name, [1, 2])) {
                $name = $show_name === 2 ? $user->get_fullname() : $user->get_firstname();
                echo sprintf(
                    '<div class="psw-userbar__name"><a href="%1$s">%2$s</a></div>',
                    esc_attr($user->get_profileurl()),
                    esc_attr($name)
                );
            }
        }
        ?>

        <?php if (isset($show_vip) && 1 === (int) $show_vip) { ?>
        <div class="ps-vip__icons"><?php do_action('peepso_action_userbar_user_name_before', $user->get_id()); ?></div>
        <?php } ?>

        <?php if (isset($show_badges) && 1 === (int) $show_badges) { ?>
        <div class="ps-vip__icons"><?php do_action('peepso_action_userbar_user_name_after', $user->get_id()); ?></div>
        <?php } ?>

        <div class="psw-userbar__user-profile">
            <?php if (isset($show_avatar) && 1 === (int) $show_avatar) { ?>
            <div class="ps-avatar psw-avatar--userbar">
                <a href="<?php echo $user->get_profileurl(); ?>">
                    <img src="<?php echo $user->get_avatar(); ?>" alt="<?php echo $user->get_fullname(); ?> avatar"
                        title="<?php echo $user->get_profileurl(); ?>">
                </a>
            </div>
            <?php } ?>

            <?php
            // Profile Submenu extra links
            if (apply_filters('peepso_filter_navigation_preferences', TRUE)) {
                $links['peepso-core-preferences'] = array(
                    'href' => $user->get_profileurl() . 'about/preferences/',
                    'icon' => 'gcis gci-user-edit',
                    'label' => __('Preferences', 'peepso-core'),
                );
            }

            if (apply_filters('peepso_filter_navigation_log_out', TRUE)) {
                $links['peepso-core-logout'] = array(
                    'href' => PeepSo::get_page('logout'),
                    'icon' => 'gcis gci-power-off',
                    'label' => __('Log Out', 'peepso-core'),
                    'widget' => TRUE,
                );
            }
            ?>

            <?php if (isset($show_usermenu) && 1 == (int) $show_usermenu) { ?>
            <div class="psw-userbar__menu ps-dropdown ps-dropdown--menu ps-dropdown--left ps-js-dropdown">
                <a href="javascript:" class="ps-dropdown__toggle psw-userbar__menu-toggle ps-js-dropdown-toggle">
                    <i class="gcis gci-angle-down"></i>
                </a>

                <div class="ps-dropdown__menu ps-js-dropdown-menu">
                <?php

                    foreach ($links as $id => $link) {
                        if (!isset($link['label']) || !isset($link['href']) || !isset($link['icon'])) {
                            var_dump($link);
                        }

                        $class = isset($link['class']) ? $link['class'] : '' ;

                        $href = $user->get_profileurl(). $link['href'];
                        if ('http' == substr(strtolower($link['href']), 0,4)) {
                            $href = $link['href'];
                        }

                        echo sprintf(
                            '<a href="%1$s" class="%2$s"><i class="%3$s"></i> %4$s</a>',
                            $href, $class, $link['icon'], esc_attr($link['label'])
                        );
                    }
                ?>
                </div>
            </div>
            <?php } ?>
        </div>

        <?php if (isset($show_logout) && 1 === (int) $show_logout) { ?>
        <a class="psw-userbar__logout" href="<?php echo PeepSo::get_page('logout'); ?>"
                title="<?php echo __('Log Out', 'peepso-core'); ?>"
                arialabel="<?php echo __('Log Out', 'peepso-core'); ?>">
            <i class="gcis gci-power-off"></i>
        </a>
        <?php } ?>

    <?php } else { ?>

        <a href="<?php echo PeepSo::get_page('activity'); ?>"><?php echo __('Log in', 'peepso-core'); ?></a>

    <?php } ?>
    </div>

    <?php if (is_user_logged_in()) { ?>
    <div class="psw-userbar__toggle psw-userbar__toggle--avatar ps-js-widget-userbar-toggle">
        <div class="ps-avatar psw-avatar--userbar">
            <img src="<?php echo $user->get_avatar();?>" alt="<?php echo $user->get_fullname(); ?> avatar" title="<?php echo $user->get_profileurl(); ?>">
        </div>
        <span class="ps-notif__bubble ps-notif__bubble--all ps-js-notif-counter"></span>
        <i class="gcis gci-times-circle"></i>
    </div>
    <?php } else { ?>
    <a href="#" class="psw-userbar__toggle ps-js-widget-userbar-toggle">
        <i class="gcis gci-user"></i>
        <span class="ps-notif__bubble ps-notif__bubble--all ps-js-notif-counter"></span>
    </a>
    <?php } ?>

</div>