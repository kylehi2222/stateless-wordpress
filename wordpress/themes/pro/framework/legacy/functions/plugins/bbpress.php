<?php

// =============================================================================
// FUNCTIONS/GLOBAL/PLUGINS/BBPRESS.PHP
// -----------------------------------------------------------------------------
// Plugin setup for theme compatibility.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Styles
//   02. Forums
//   03. Topics
//   04. Replies
//   05. Users
//   06. Search
//   07. Breadcrumbs
//   08. Miscellaneous
//   09. Template Includes
// =============================================================================

// Styles
// =============================================================================

function x_bbpress_enqueue_styles( $stack, $ext ) {
  wp_deregister_style( 'bbp-default' );
  wp_enqueue_style( 'x-bbpress', X_TEMPLATE_URL . '/framework/dist/css/site/bbpress/' . $stack . $ext . '.css', [ 'x-stack' ], X_ASSET_REV, 'all' );
}

add_action( 'x_enqueue_styles', 'x_bbpress_enqueue_styles', 10, 2 );



// Forums
// =============================================================================

//
// Filters the output of nested forums within forums.
//

if ( ! function_exists( 'x_bbpress_filter_list_forums' ) ) :
  function x_bbpress_filter_list_forums( $r ) {

    $r = array(
      'before'            => '<ul class="bbp-forums-list">',
      'after'             => '</ul>',
      'link_before'       => '<li class="bbp-forum">',
      'link_after'        => '</li>',
      'count_before'      => '',
      'count_after'       => '',
      'count_sep'         => '',
      'separator'         => '',
      'forum_id'          => '',
      'show_topic_count'  => false,
      'show_reply_count'  => false,
    );

    return $r;

  }
endif;

add_filter( 'bbp_before_list_forums_parse_args', 'x_bbpress_filter_list_forums' );


//
// Removes the single forum description.
//

add_filter( 'bbp_get_single_forum_description', '__return_empty_string' );


//
// Add forum actions to the beginning of single forum.
//

if ( ! function_exists( 'x_bbpress_add_actions_single_forum' ) ) :
  function x_bbpress_add_actions_single_forum() { ?>

    <div class="x-bbp-header">
      <div class="actions">
        <a href="<?php echo get_post_type_archive_link( bbp_get_forum_post_type() ); ?>"><?php _e( 'To Forums List', '__x__' ); ?></a>
        <?php bbp_forum_subscription_link(); ?>
      </div>
    </div>

  <?php }
endif;

add_filter( 'bbp_template_before_single_forum', 'x_bbpress_add_actions_single_forum' );



// Topics
// =============================================================================

//
// Removes the single topic description.
//

add_filter( 'bbp_get_single_topic_description', '__return_empty_string' );


//
// Filters the output of the topic tags.
//

if ( ! function_exists( 'x_bbpress_filter_get_topic_tag_list' ) ) :
  function x_bbpress_filter_get_topic_tag_list( $r ) {

    $r['before'] = '<div class="bbp-topic-tags"><span>' . __( 'Topic Tags', '__x__' ) . '</span>';
    $r['sep']    = '';
    $r['after']  = '</div>';

    return $r;

  }
endif;

add_filter( 'bbp_before_get_topic_tag_list_parse_args', 'x_bbpress_filter_get_topic_tag_list' );


//
// Filters the output of the topic admin links.
//

if ( ! function_exists( 'x_bbpress_filter_get_topic_admin_links' ) ) :
  function x_bbpress_filter_get_topic_admin_links( $r ) {

    $r['sep'] = '';

    if ( is_user_logged_in() ) {
      $r['before'] = '<div class="bbp-admin-links"><div class="x-bbp-admin-links-inner">';
      $r['after']  = '</div></div>';
    } else {
      $r['before'] = '';
      $r['after']  = '';
    }

    return $r;

  }
endif;

add_filter( 'bbp_before_get_topic_admin_links_parse_args', 'x_bbpress_filter_get_topic_admin_links' );


//
// Hides topic pagination if amount of topics is less than the topics per
// page option.
//

if ( ! function_exists( 'x_bbpress_show_topic_pagination' ) ) :
  function x_bbpress_show_topic_pagination() {

    $total_topics = bbpress()->topic_query->found_posts;

    if ( bbp_get_topics_per_page() <= $total_topics ) {
      return true;
    } else {
      return false;
    }

  }
endif;



// Replies
// =============================================================================

//
// Filters the output of the subscription link.
//

if ( ! function_exists( 'x_bbpress_filter_get_user_subscribe_link' ) ) :
  function x_bbpress_filter_get_user_subscribe_link( $r ) {

    $r['before'] = '';

    return $r;

  }
endif;

add_filter( 'bbp_before_get_user_subscribe_link_parse_args', 'x_bbpress_filter_get_user_subscribe_link' );


//
// Filters the output of the reply admin links.
//

if ( ! function_exists( 'x_bbpress_filter_get_reply_admin_links' ) ) :
  function x_bbpress_filter_get_reply_admin_links( $r ) {

    $r['sep'] = '';

    if ( is_user_logged_in() ) {
      $r['before'] = '<div class="bbp-admin-links"><div class="x-bbp-admin-links-inner">';
      $r['after']  = '</div></div>';
    } else {
      $r['before'] = '';
      $r['after']  = '';
    }

    return $r;

  }
endif;

add_filter( 'bbp_before_get_reply_admin_links_parse_args', 'x_bbpress_filter_get_reply_admin_links' );


//
// Add reply actions to the beginning of the replies loop.
//

if ( ! function_exists( 'x_bbpress_add_actions_replies' ) ) :
  function x_bbpress_add_actions_replies() { ?>

    <?php if ( ! bbp_show_lead_topic() && ! bbp_is_single_user_replies() && ! ( function_exists( 'bp_is_user' ) && bp_is_user() ) ) : ?>

      <div class="x-bbp-header">
        <div class="actions">
          <a href="<?php echo bbp_get_forum_permalink( bbp_get_topic_forum_id() ); ?>"><?php _e( 'To Parent Forum', '__x__' ); ?></a>
          <?php bbp_topic_subscription_link(); ?>
          <?php bbp_user_favorites_link(); ?>
        </div>
      </div>

    <?php endif; ?>

  <?php }
endif;

add_filter( 'bbp_template_before_replies_loop', 'x_bbpress_add_actions_replies' );


//
// Hides reply pagination if amount of replies is less than the reples per
// page option.
//

if ( ! function_exists( 'x_bbpress_show_reply_pagination' ) ) :
  function x_bbpress_show_reply_pagination() {

    $total_replies = bbpress()->reply_query->found_posts;

    if ( bbp_get_replies_per_page() <= $total_replies ) {
      return true;
    } else {
      return false;
    }

  }
endif;



// Users
// =============================================================================

//
// Add an informational message about changing user passwords.
//

if ( ! function_exists( 'x_bbpress_user_edit_before_account' ) ) :
  function x_bbpress_user_edit_before_account() { ?>

      <div class="bbp-template-notice">
        <p><?php _e( 'Your password should be at least ten characters long. Use upper and lower case letters, numbers, and symbols to make it even stronger.', 'bbpress' ); ?></p>
      </div>

  <?php }
endif;

add_action( 'bbp_user_edit_after_account', 'x_bbpress_user_edit_before_account' );



// Search
// =============================================================================

//
// Hides search pagination if amount of search items is less than the search
// items per page option.
//

if ( ! function_exists( 'x_bbpress_show_search_pagination' ) ) :
  function x_bbpress_show_search_pagination() {

    $total_search_items = bbpress()->search_query->found_posts;

    if ( bbp_get_replies_per_page() <= $total_search_items ) {
      return true;
    } else {
      return false;
    }

  }
endif;



// Breadcrumbs
// =============================================================================

//
// Removes the bbPress breadcrumbs from all default locations (is later
// removed then added back in the breadcrumbs.php file to allow for standard
// output as the breadcrumbs are filtered below to be used there).
//

add_filter( 'bbp_no_breadcrumb', '__return_true' );


//
// Removes the bbPress breadcrumbs from all default locations (is later
// removed then added back in the breadcrumbs.php file to allow for standard
// output as the breadcrumbs are filtered below to be used there).
//

if ( ! function_exists( 'x_bbpress_filter_breadcrumbs' ) ) :
  function x_bbpress_filter_breadcrumbs( $r ) {

    if ( bbp_is_search() ) {
      $current_text = bbp_get_search_title();
    } elseif ( bbp_is_forum_archive() ) {
      $current_text = bbp_get_forum_archive_title();
    } elseif ( bbp_is_topic_archive() ) {
      $current_text = bbp_get_topic_archive_title();
    } elseif ( bbp_is_single_view() ) {
      $current_text = bbp_get_view_title();
    } elseif ( bbp_is_single_forum() ) {
      $current_text = bbp_get_forum_title();
    } elseif ( bbp_is_single_topic() ) {
      $current_text = bbp_get_topic_title();
    } elseif ( bbp_is_single_reply() ) {
      $current_text = bbp_get_reply_title();
    } elseif ( bbp_is_topic_tag() || ( get_query_var( 'bbp_topic_tag' ) && ! bbp_is_topic_tag_edit() ) ) {

      // Always include the tag name
      $tag_data[] = bbp_get_topic_tag_name();

      // If capable, include a link to edit the tag
      if ( current_user_can( 'manage_topic_tags' ) ) {
        $tag_data[] = '<a href="' . esc_url( bbp_get_topic_tag_edit_link() ) . '" class="bbp-edit-topic-tag-link">' . esc_html__( '(Edit)', 'bbpress' ) . '</a>';
      }

      // Implode the results of the tag data
      $current_text = sprintf( __( 'Topic Tag: %s', 'bbpress' ), implode( ' ', $tag_data ) );

    } elseif ( bbp_is_topic_tag_edit() ) {
      $current_text = __( 'Edit', 'bbpress' );
    } else {
      $current_text = get_the_title();
    }

    $is_ltr = ! is_rtl();

    $iconClass = 'x-icon-angle-' . ( ( $is_ltr ) ? 'right' : 'left' );
    $iconUnicode = ( ( $is_ltr ) ? 'f105' : 'f104' );

    $r = array(
      'before'          => '',
      'after'           => '',
      'sep'             => '<span class="delimiter">' . x_icon_get($iconUnicode, $iconClass) . '</span> ',
      'pad_sep'         => 0,
      'sep_before'      => '',
      'sep_after'       => '',
      'crumb_before'    => '',
      'crumb_after'     => '',
      'include_home'    => false,
      'home_text'       => '<span class="home">' . x_icon_get("f015", "x-icon-home") . '</span>',
      'include_root'    => true,
      'root_text'       => bbp_get_forum_archive_title(),
      'include_current' => true,
      'current_text'    => $current_text,
      'current_before'  => '',
      'current_after'   => '',
    );

    return $r;

  }
endif;

add_filter( 'bbp_before_get_breadcrumb_parse_args', 'x_bbpress_filter_breadcrumbs' );



// Miscellaneous
// =============================================================================

//
// Filter the author link to remove all images.
//

if ( ! function_exists( 'x_bbpress_filter_author_link' ) ) :
  function x_bbpress_filter_author_link( $r ) {

    $r['type'] = 'name';

    return $r;

  }
endif;

add_filter( 'bbp_before_get_author_link_parse_args', 'x_bbpress_filter_author_link' );


//
// Outputs a navigation item with quick links to bbPress-specific components
// such as the forums, et cetera.
//

if ( ! function_exists( 'x_bbpress_navbar_menu' ) ) :
  function x_bbpress_navbar_menu( $items, $args ) {

    if ( x_get_option( 'x_bbpress_header_menu_enable' ) == '1' && did_action( 'x_classic_headers' ) ) {

      $submenu_items  = '';
      $submenu_items .= '<li class="menu-item menu-item-bbpress-navigation"><a href="' . bbp_get_search_url() . '" class="cf">' . x_icon_get( "f002", "x-icon-search") . ' <span>' . __( 'Forums Search', '__x__' ) . '</span></a></li>';

      if ( is_user_logged_in() ) {
        $submenu_items .= '<li class="menu-item menu-item-bbpress-navigation"><a href="' . bbp_get_favorites_permalink( get_current_user_id() ) . '" class="cf">' . x_icon_get("f005", "x-icon-star") . ' <span>' . __( 'Favorites', '__x__' ) . '</span></a></li>';
        $submenu_items .= '<li class="menu-item menu-item-bbpress-navigation"><a href="' . bbp_get_subscriptions_permalink( get_current_user_id() ) . '" class="cf">' . x_icon_get("f02e", "x-icon-bookmark") . ' <span>' . __( 'Subscriptions', '__x__' ) . '</span></a></li>';
      }

      $has_buddypress = class_exists( 'BuddyPress' );
      if ( ! $has_buddypress || $has_buddypress && x_get_option( 'x_buddypress_header_menu_enable' ) == '' ) {
        if ( ! is_user_logged_in() ) {
          $submenu_items .= '<li class="menu-item menu-item-bbpress-navigation"><a href="' . wp_login_url() . '" class="cf">' . x_icon_get("f2f6", "x-icon-sign-in") . ' <span>' . __( 'Log in', '__x__' ) . '</span></a></li>';
        } else {
          $submenu_items .= '<li class="menu-item menu-item-bbpress-navigation"><a href="' . bbp_get_user_profile_url( get_current_user_id() ) . '" class="cf">' . x_icon_get("f013", "x-icon-cog") . ' <span>' . __( 'Profile', '__x__' ) . '</span></a></li>';
        }
      }

      if ( $args->theme_location == 'primary' ) {
        $items .= '<li class="menu-item current-menu-parent menu-item-has-children x-menu-item x-menu-item-bbpress">'
                  . '<a href="' . get_post_type_archive_link( bbp_get_forum_post_type() ) . '" class="x-btn-navbar-bbpress">'
                    . '<span>' . x_icon_get("f075", "x-icon-comment") . '<span class="x-hidden-desktop"> ' . __( 'Forums', '__x__' ) . '</span></span>'
                  . '</a>'
                  . '<ul class="sub-menu">'
                    . $submenu_items
                  . '</ul>'
                . '</li>';
      }
    }

    return $items;

  }
  add_filter( 'wp_nav_menu_items', 'x_bbpress_navbar_menu', 9996, 2 );
endif;


//
// Remove new post form quicktags.
//

if ( ! function_exists( 'x_bbpress_filter_get_the_content' ) ) :
  function x_bbpress_filter_get_the_content( $r ) {

    $r['before']        = '<div class="bbp-the-content-wrapper"><label for="bbp_topic_content">' . __( 'Content', '__x__' ) . '</label>';
    $r['after']         = '</div>';
    $r['textarea_rows'] = 8;
    $r['quicktags']     = ( x_get_option( 'x_bbpress_enable_quicktags' ) == '' ) ? false : true;

    return $r;

  }
endif;

add_filter( 'bbp_before_get_the_content_parse_args', 'x_bbpress_filter_get_the_content' );


// Template Includes
// =============================================================================

add_filter( 'bbp_get_template_stack', function($stack) {
  return array_merge([ X_TEMPLATE_PATH . '/framework/legacy/templates/bbpress'], $stack);
});
