<?php

// Share
// =============================================================================

function x_shortcode_share( $atts ) {
  extract( shortcode_atts( array(
    'id'          => '',
    'class'       => '',
    'style'       => '',
    'title'       => '',
    'share_title' => '',
    'facebook'    => '',
    'twitter'     => '',
    'linkedin'    => '',
    'pinterest'   => '',
    'reddit'      => '',
    'email'       => '',
    'email_subject' => ''
  ), $atts, 'x_share' ) );

  $share_url        = urlencode( get_permalink() );

  if ( is_singular() ) {
    $share_url = urlencode( get_permalink() );
  } else {
    global $wp;
    $share_url = urlencode( home_url( ($wp->request) ? $wp->request : '' ) );
  }

  if ( is_singular() ) {
    $share_title = ( $share_title    != '' ) ? esc_attr( $share_title ) : urlencode( get_the_title() );
  } else {
    $share_title = ( $share_title    != '' ) ? esc_attr( $share_title ) : urlencode( apply_filters( 'the_title', get_post( get_option( 'page_for_posts' ) )->post_title) );
  }

  $share_source     = urlencode( get_bloginfo( 'name' ) );
  $share_image_info = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
  if (empty($share_image_info)) {
    $share_image_info = [''];
  }

  $share_image      = ( function_exists( 'x_get_featured_image_with_fallback_url' ) )
    ? urlencode( (string) x_get_featured_image_with_fallback_url() )
    : urlencode( (string) $share_image_info[0] );

  if ( $linkedin === 'true' ) {
    $share_content = urlencode( cs_get_excerpt_for_social() );
  }

  $tooltip_attr = cs_generate_data_attributes_extra( 'tooltip', 'hover', 'bottom' );


  $id          = ( $id          != ''     ) ? 'id="' . esc_attr( $id ) . '"' : '';
  $class       = ( $class       != ''     ) ? 'x-entry-share ' . esc_attr( $class ) : 'x-entry-share';
  $style       = ( $style       != ''     ) ? 'style="' . $style . '"' : '';
  $title       = ( $title       != ''     ) ? $title : __( 'Share this Post', 'cornerstone' );
  $facebook    = ( $facebook    == 'true' ) ? "<a href=\"#share\" {$tooltip_attr} class=\"x-share\" title=\"" . __( 'Share on Facebook', 'cornerstone' ) . "\" onclick=\"window.open('http://www.facebook.com/sharer.php?u={$share_url}&amp;t={$share_title}', 'popupFacebook', 'width=650, height=270, resizable=0, toolbar=0, menubar=0, status=0, location=0, scrollbars=0'); return false;\"><i class=\"x-icon-facebook-square\" " . fa_data_icon('facebook-square') . "></i></a>" : '';
  $twitter     = ( $twitter     == 'true' ) ? "<a href=\"#share\" {$tooltip_attr} class=\"x-share\" title=\"" . __( 'Share on X', 'cornerstone' ) . "\" onclick=\"window.open('https://twitter.com/intent/tweet?text={$share_title}&amp;url={$share_url}', 'popupTwitter', 'width=500, height=370, resizable=0, toolbar=0, menubar=0, status=0, location=0, scrollbars=0'); return false;\"><i class=\"x-icon-twitter-square\" " . fa_data_icon('square-x-twitter') . "></i></a>" : '';
  $linkedin    = ( $linkedin    == 'true' ) ? "<a href=\"#share\" {$tooltip_attr} class=\"x-share\" title=\"" . __( 'Share on LinkedIn', 'cornerstone' ) . "\" onclick=\"window.open('http://www.linkedin.com/shareArticle?mini=true&amp;url={$share_url}&amp;title={$share_title}&amp;summary={$share_content}&amp;source={$share_source}', 'popupLinkedIn', 'width=610, height=480, resizable=0, toolbar=0, menubar=0, status=0, location=0, scrollbars=0'); return false;\"><i class=\"x-icon-linkedin-square\" " . fa_data_icon('linkedin-square') . "></i></a>" : '';
  $pinterest   = ( $pinterest   == 'true' ) ? "<a href=\"#share\" {$tooltip_attr} class=\"x-share\" title=\"" . __( 'Share on Pinterest', 'cornerstone' ) . "\" onclick=\"window.open('http://pinterest.com/pin/create/button/?url={$share_url}&amp;media={$share_image}&amp;description={$share_title}', 'popupPinterest', 'width=750, height=265, resizable=0, toolbar=0, menubar=0, status=0, location=0, scrollbars=0'); return false;\"><i class=\"x-icon-pinterest-square\" " . fa_data_icon('pinterest-square') . "></i></a>" : '';
  $reddit      = ( $reddit      == 'true' ) ? "<a href=\"#share\" {$tooltip_attr} class=\"x-share\" title=\"" . __( 'Share on Reddit', 'cornerstone' ) . "\" onclick=\"window.open('http://www.reddit.com/submit?url={$share_url}', 'popupReddit', 'width=875, height=450, resizable=0, toolbar=0, menubar=0, status=0, location=0, scrollbars=0'); return false;\"><i class=\"x-icon-reddit-square\" " . fa_data_icon('reddit-square') . "></i></a>" : '';


  if ( $email       == 'true' ) {

    $email_subject = esc_attr( ( $email_subject != '' ) ? cs_decode_shortcode_attribute( $email_subject ) : __( 'Hey, thought you might enjoy this! Check it out when you have a chance:', 'cornerstone' ) );
    $mail_to_subject = esc_attr( $share_title );
    $mail_to_url = esc_url( get_permalink() );

    $mail_to = "mailto:?subject=$mail_to_subject&amp;body=$email_subject $mail_to_url";

    $email = "<a href=\"{$mail_to}\" {$tooltip_attr} class=\"x-share email\" title=\"" . __( 'Share via Email', 'cornerstone' ) . "\"><span><i class=\"x-icon-envelope-square\" " . fa_data_icon('envelope-square') . "></i></span></a>";

  } else {
    $email = '';
  }

  // @TODO move to icon system
  do_action("cs_fa_add_webfont_styles");

  $output = "<div {$id} class=\"{$class}\" {$style}>"
            . '<p>' . $title . '</p>'
            . '<div class="x-share-options">'
              . $facebook . $twitter . $linkedin . $pinterest . $reddit . $email
            . '</div>'
          . '</div>';

  return $output;
}

add_shortcode( 'x_share', 'x_shortcode_share' );
