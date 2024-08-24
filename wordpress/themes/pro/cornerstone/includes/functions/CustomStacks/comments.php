<?php

// Callback for comment list
function cs_stack_comment_list($comment, $args, $depth) {
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
    if ( x_is_product() ) {
      $comment_avatar = get_avatar( $comment, 240 );
    } else {
      $comment_avatar = get_avatar( $comment, 120 );
    }
?>
    <li id="li-comment-<?php comment_ID(); ?>" <?php echo apply_filters ('x_woocommerce_review_schema_attributes', ''); ?> <?php comment_class(); ?>>
      <article id="comment-<?php comment_ID(); ?>" class="comment">
        <?php echo apply_filters ('x_woocommerce_review_schema_item', ''); ?>
<?php
    printf( '<div class="x-comment-img">%s</div>',
      '<span class="avatar-wrap cf">' . $comment_avatar . '</span>'
    );
?>
        <?php if ( ! x_is_product() ) : ?>
        <div class="x-reply">
          <?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply <span class="comment-reply-link-after">"' . cs_fa_icon_tag_from_unicode("f3e5", "x-icon-reply") . '</span>', '__x__' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
        </div>
        <?php endif; ?>
        <div class="x-comment-wrap">
          <header class="x-comment-header">
<?php
    printf( '<cite class="x-comment-author" itemprop="author">%1$s</cite>',
      get_comment_author_link()
    );
    if ( x_is_product() && get_option('woocommerce_enable_review_rating') == 'yes' && !empty( $rating ) ) : ?>
              <div class="star-rating-container">
                <div <?php echo apply_filters ('x_woocommerce_review_schema_rating_attributes', ''); ?> class="star-rating" title="<?php echo sprintf( __( 'Rated %d out of 5', '__x__' ), $rating ) ?>">
                  <span style="width:<?php echo ( intval( get_comment_meta( $GLOBALS['comment']->comment_ID, 'rating', true ) ) / 5 ) * 100; ?>%"><strong itemprop="ratingValue"><?php echo intval( get_comment_meta( $GLOBALS['comment']->comment_ID, 'rating', true ) ); ?></strong> <?php _e( 'out of 5', '__x__' ); ?></span>
                </div>
              </div>
<?php endif;
printf( '<div><a href="%1$s" class="x-comment-time"><time itemprop="datePublished" datetime="%2$s">%3$s</time></a></div>',
  esc_url( get_comment_link( $comment->comment_ID ) ),
  get_comment_time( 'c' ),
  sprintf( __( '%1$s at %2$s', '__x__' ),
  get_comment_date( 'm.d.Y' ),
  get_comment_time()
  )
);
edit_comment_link( __( cs_fa_icon_tag_from_unicode("f044", "x-icon-edit") . ' Edit', '__x__' ) );
?>
          </header>
          <?php if ( '0' == $comment->comment_approved ) : ?>
            <p class="x-comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', '__x__' ); ?></p>
          <?php endif; ?>
          <section class="x-comment-content" itemprop="description">
            <?php comment_text(); ?>
          </section>
        </div>
      </article>
<?php
break;
endswitch;
}

