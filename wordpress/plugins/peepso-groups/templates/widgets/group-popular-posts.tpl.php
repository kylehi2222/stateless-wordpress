<?php /*NWJjbDNsYng1QmhMczU4UHdsd3hjSzN1MjJBd0NCS1BHTURkV2JMbVFnYnVkemJoWXprOUZUMWNMT0VJMjJPN1c2S2tBa2RVUkM3QTRPcm5IOW5JUWp6cjF6WTJRa2xpaDlYMFhjSGNmaFFOQ1dIZ0lRN3FRMnBrQ2MyWTl3QStOaDVpMVR1Y056TTJPeUo4RGFYdFN6bU5CdEpxL2ZkaEFUVVlQVGtNRnAxeXVIb3N2VnhsbHlNdjJRSXFUYmt0*/
$limit = (isset($instance['limit']) && is_int($instance['limit'])) ? $instance['limit'] : 10;

// Set global variables to filter only group posts
$GLOBALS['peepso_group_only'] = TRUE;
$GLOBALS['peepso_remove_post_actions'] = TRUE;

if (isset($_GET['legacy-widget-preview'])) {
    PeepSo3_Mayfly::del('peepso_groups_widget_popular_posts');
}

$peepso_activity = new PeepSoActivity();
$peepso_activity->post_query = PeepSo3_Mayfly::get_or_set_if_empty('peepso_groups_widget_popular_posts', HOUR_IN_SECONDS, function() use ($peepso_activity, $limit) {
    return $peepso_activity->get_posts(NULL, NULL, NULL, NULL, FALSE, $limit);
});

unset($GLOBALS['peepso_group_only']);

if ($peepso_activity->post_query->posts) {
    echo $args['before_widget'];

    ?><div class="ps-widget__wrapper<?php echo $instance['class_suffix'];?> ps-widget<?php echo $instance['class_suffix'];?>">
        <div class="ps-widget__header<?php echo $instance['class_suffix'];?>">
            <?php
            if ( ! empty( $instance['title'] ) ) {
                echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
            }
            ?>
        </div>

        <div class="ps-widget__body">
            <div class="psw-groups">
                <?php
                    add_filter('peepso_commentsbox_display', '__return_false');
                    add_filter('peepso_show_post_options', '__return_false');
                    add_filter('peepso_show_recent_comments', '__return_false');
                    add_filter('peepso_groups_update_post_title', '__return_true');

                    ob_start();
                    while ($peepso_activity->next_post()) {
                        $peepso_activity->show_post();
                    }
                    $html = ob_get_clean();

                    // Remove the classes to prevent postbox from showing when editing the post
                    $html = preg_replace('/ps-js-activity--\d+\s*/', '', $html);

                    echo $html;

                    remove_filter('peepso_commentsbox_display', '__return_false');
                    remove_filter('peepso_show_post_options', '__return_false');
                    remove_filter('peepso_show_recent_comments', '__return_false');
                    remove_filter('peepso_groups_update_post_title', '__return_true');
                ?>
            </div>
        </div>
    </div><?php

    echo $args['after_widget'];
}

unset($GLOBALS['peepso_remove_post_actions']);
// EOF
