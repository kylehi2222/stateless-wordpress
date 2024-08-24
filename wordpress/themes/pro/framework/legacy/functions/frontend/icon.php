<?php

// =============================================================================
// FUNCTIONS/ICON.PHP
// -----------------------------------------------------------------------------
// Icon specific functions.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Entry Meta
//   02. Portfolio Tags
//   03. Individual Comment
//   04. Comment Number and Link
// =============================================================================

// Entry Meta
// =============================================================================

if ( ! function_exists( 'x_icon_entry_meta' ) ) :
  function x_icon_entry_meta() {

    $date = sprintf( '<span><time class="entry-date" datetime="%1$s">%2$s</time></span>',
      esc_attr( get_the_date( 'c' ) ),
      esc_html( get_the_date() )
    );

    if ( x_does_not_need_entry_meta() ) {
      return;
    } else {
      printf( '<p class="p-meta">%s</p>',
        $date
      );
    }

  }
endif;



// Portfolio Tags
// =============================================================================

if ( ! function_exists( 'x_icon_portfolio_tags' ) ) :
  function x_icon_portfolio_tags() {

    $terms = get_the_terms( get_the_ID(), 'portfolio-tag' );

    echo '<ul class="inline">';
    foreach( $terms as $term ) {
      echo '<li><a href="' . get_term_link( $term->slug, 'portfolio-tag' ) . '">' . x_icon_get( "f02b", "x-icon-tag") . $term->name . '</a></li>';
    };
    echo '</ul>';

  }
endif;



// Individual Comment
// =============================================================================

//
// 1. Pingbacks and trackbacks.
// 2. Normal Comments.
//

if ( ! function_exists( 'x_icon_comment' ) ) :
  function x_icon_comment( $comment, $args, $depth ) {

    $GLOBALS['comment'] = $comment;
    switch ( $comment->comment_type ) :
      case 'pingback' :  // 1
      case 'trackback' : // 1
    ?>
    <li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
      <p><?php _e( 'Pingback:', '__x__' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)', '__x__' ), '<span class="edit-link">', '</span>' ); ?></p>
    <?php
        break;
      default : // 2
      GLOBAL $post;
      if ( class_exists( 'WooCommerce' ) ) :
        $rating = esc_attr( get_comment_meta( $GLOBALS['comment']->comment_ID, 'rating', true ) );
      endif;
      if ( x_is_product() ) :
        $comment_time = sprintf( __( '%1$s', '__x__' ), get_comment_date() );
      else :
        $comment_time = sprintf( __( '%1$s at %2$s', '__x__' ), get_comment_date(), get_comment_time() );
      endif;
    ?>
    <li id="li-comment-<?php comment_ID(); ?>" <?php echo apply_filters ('x_woocommerce_review_schema_attributes', ''); ?> <?php comment_class(); ?>>
      <?php $comment_reply = ( ! x_is_product() ) ? '<div class="x-reply">' . get_comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply<span class="comment-reply-link-after">' . x_icon_get("f3e5", "x-icon-reply") . '</span>', '__x__' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ) . '</div>' : ''; ?>
      <?php
      printf( '<div class="x-comment-img">%1$s %2$s %3$s</div>',
        '<span class="avatar-wrap cf">' . get_avatar( $comment, 120 ) . '</span>',
        ( $comment->user_id === $post->post_author ) ? '<span class="bypostauthor">' . __( 'Post<br>Author', '__x__' ) . '</span>' : '',
        $comment_reply
      );
      ?>
      <article id="comment-<?php comment_ID(); ?>" class="comment">
        <header class="x-comment-header">
          <?php echo apply_filters ('x_woocommerce_review_schema_item', ''); ?>
          <?php
          printf( '<cite class="x-comment-author" itemprop="author">%1$s</cite>',
            get_comment_author_link()
          );
          if ( x_is_product() && get_option( 'woocommerce_enable_review_rating' ) == 'yes' && !empty( $rating ) ) : ?>
            <div class="star-rating-container">
              <div <?php echo apply_filters ('x_woocommerce_review_schema_rating_attributes', ''); ?> class="star-rating" title="<?php echo sprintf( __( 'Rated %d out of 5', '__x__' ), $rating ) ?>">
                <span style="width:<?php echo ( intval( get_comment_meta( $GLOBALS['comment']->comment_ID, 'rating', true ) ) / 5 ) * 100; ?>%"><strong itemprop="ratingValue"><?php echo intval( get_comment_meta( $GLOBALS['comment']->comment_ID, 'rating', true ) ); ?></strong> <?php _e( 'out of 5', '__x__' ); ?></span>
              </div>
            </div>
          <?php endif;
          printf( '<div><a href="%1$s" class="x-comment-time"><time itemprop="datePublished" datetime="%2$s">%3$s</time></a></div>',
            esc_url( get_comment_link( $comment->comment_ID ) ),
            get_comment_time( 'c' ),
            $comment_time
          );
          edit_comment_link( sprintf( __( '%s Edit', '__x__' ), x_icon_get("f044", "x-icon-edit") ) );
          ?>
        </header>
        <?php if ( '0' == $comment->comment_approved ) : ?>
          <p class="x-comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', '__x__' ); ?></p>
        <?php endif; ?>
        <section class="x-comment-content" itemprop="description">
          <?php comment_text(); ?>
        </section>
      </article>
    <?php
        break;
    endswitch;

  }
endif;



// Comment Number and Link
// =============================================================================

if ( ! function_exists( 'x_icon_comment_number' ) ) :
  function x_icon_comment_number() {

    if ( comments_open() ) {

      $title  = apply_filters( 'x_entry_meta_comments_title', get_the_title() );
      $link   = apply_filters( 'x_entry_meta_comments_link', get_comments_link() );
      $number = apply_filters( 'x_entry_meta_comments_number', get_comments_number() );

      if ( $number == 0 ) {
        $comments = '';
      } else {
        $comments = sprintf( '<a href="%1$s" title="%2$s" class="meta-comments">%3$s</a>',
          esc_url( $link ),
          esc_attr( sprintf( __( 'Leave a comment on: &ldquo;%s&rdquo;', '__x__' ), $title ) ),
          number_format_i18n( $number )
        );
      }

    } else {

      $comments = '';

    }

    $post_type      = get_post_type();
    $post_type_post = $post_type == 'post';
    $no_post_meta   = x_get_option( 'x_blog_enable_post_meta' ) == '';

    if ( $post_type_post && $no_post_meta ) {
      return;
    } else {
      echo $comments;
    }

  }
endif;


// Comment form not logged in
// Ethos actualls runs for all adding in the asterisks
// Icon overwrites that

add_filter('comment_form_default_fields', function($fields) {

  if (x_get_stack() !== "icon") {
    return $fields;
  }

  $commenter     = wp_get_current_commenter();
  $req           = get_option( 'require_name_email' );
  $asterisk      = ( $req ? '*' : '' );
  $asterisk_html = ( $req ? '<span class="required">*</span>' : '' );
  $aria_req      = ( $req ? " aria-required='true' required='required'" : '' );

  return array_merge( $fields, array(
    'author' =>
      '<p class="comment-form-author">' .
        '<label for="author">' . __( 'Name', '__x__' ) . ' ' . $asterisk_html . '</label> ' .
        x_icon_get("f007", "x-comment-form-icon") .
        '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" placeholder="' . __( 'Your Name', '__x__' ) . ' ' . $asterisk . '" size="30"' . $aria_req . ' />' .
      '</p>',
    'email'  =>
      '<p class="comment-form-email">' .
        '<label for="email">' . __( 'Email', '__x__' ) . ' ' . $asterisk_html . '</label> ' .
        x_icon_get("f0e0", "x-comment-form-icon") .
        '<input id="email" name="email" type="text" value="' . esc_attr( $commenter['comment_author_email'] ) . '" placeholder="' . __( 'Your Email', '__x__' ) . ' ' . $asterisk . '" size="30"' . $aria_req . ' />' .
      '</p>',
    'url'    =>
      '<p class="comment-form-url">' .
        '<label for="url">' . __( 'Website', '__x__' ) . '</label>' .
        x_icon_get("f0c1", "x-comment-form-icon") .
        '<input id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" placeholder="' . __( 'Your Website', '__x__' ) . '" size="30" />' .
      '</p>'
  ));
}, 10000, 1);
