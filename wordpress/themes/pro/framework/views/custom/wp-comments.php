<?php

// =============================================================================
// VIEWS/RENEW/WP-COMMENTS.PHP
// -----------------------------------------------------------------------------
// The area of the page that contains both current comments and the comment
// form. The actual display of individual comments is handled by a callback to
// the x_comment() function.
// =============================================================================

//
// 1. If the current post is protected by a password and the visitor has not
//    yet entered the password, we will return early without loading the
//    comments.
//

if ( post_password_required() )
  return; // 1

?>

<div id="comments" class="x-comments-area">

  <?php if ( have_comments() ) : ?>

    <h2 class="h-comments-title">
      <?php
      printf( _n( 'One Comment on %2$s', '%1$s Comments on %2$s', get_comments_number(), '__x__' ),
        number_format_i18n( get_comments_number() ),
        '<span>&ldquo;' . get_the_title() . '&rdquo;</span>'
      );
      ?>
    </h2>

    <ol class="x-comments-list">
      <?php
      wp_list_comments( array(
        'callback' => 'cs_stack_comment_list',
        'style'    => 'ol'
      ) );
      ?>
    </ol>

    <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
    <nav id="comment-nav-below" class="navigation" role="navigation">
      <h1 class="visually-hidden"><?php _e( 'Comment navigation', '__x__' ); ?></h1>
      <div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', '__x__' ) ); ?></div>
      <div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', '__x__' ) ); ?></div>
    </nav>
    <?php endif; ?>

    <?php if ( ! comments_open() && get_comments_number() ) : ?>
    <p class="nocomments"><?php _e( 'Comments are closed.' , '__x__' ); ?></p>
    <?php endif; ?>

  <?php endif; ?>

  <?php
  comment_form( array(
    'comment_notes_after' => '',
    'id_submit'           => 'entry-comment-submit',
    'label_submit'        => __( 'Submit' , '__x__' )
  ) );
  ?>

</div>

