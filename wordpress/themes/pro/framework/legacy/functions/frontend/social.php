<?php

// =============================================================================
// FUNCTIONS/GLOBAL/SOCIAL.PHP
// -----------------------------------------------------------------------------
// Various social functions.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Social Output
//   02. Social Meta
// =============================================================================

// Social Output
// =============================================================================

if ( ! function_exists( 'x_social_global' ) ) :
  function x_social_global() {

    $facebook    = x_get_option( 'x_social_facebook' );
    $twitter     = x_get_option( 'x_social_twitter' );
    $linkedin    = x_get_option( 'x_social_linkedin' );
    $xing        = x_get_option( 'x_social_xing' );
    $foursquare  = x_get_option( 'x_social_foursquare' );
    $youtube     = x_get_option( 'x_social_youtube' );
    $vimeo       = x_get_option( 'x_social_vimeo' );
    $instagram   = x_get_option( 'x_social_instagram' );
    $pinterest   = x_get_option( 'x_social_pinterest' );
    $dribbble    = x_get_option( 'x_social_dribbble' );
    $flickr      = x_get_option( 'x_social_flickr' );
    $github      = x_get_option( 'x_social_github' );
    $behance     = x_get_option( 'x_social_behance' );
    $tumblr      = x_get_option( 'x_social_tumblr' );
    $whatsapp    = x_get_option( 'x_social_whatsapp' );
    $soundcloud  = x_get_option( 'x_social_soundcloud' );
    $rss         = x_get_option( 'x_social_rss' );

    $target_blank = x_output_target_blank( false );

    $output = '<div class="x-social-global">';

      $output = apply_filters( 'x_social_global_before', $output );

    $rss_icon = '';

    if ($rss) {
      $rss_icon = x_get_option("x_font_awesome_icon_type") === "svg"
        ? x_icon_get_brand("f143", "x-icon-rss-square")
        : x_icon_get("f143", "x-icon-rss-square");
    }

      if ( $facebook )    : $output .= '<a href="' . $facebook    . '" class="facebook" title="Facebook" ' . $target_blank . '>' . x_icon_get_brand("f082", "x-icon-facebook-square") . '</a>'; endif;
      if ( $twitter )     : $output .= '<a href="' . $twitter     . '" class="twitter" title="X / Twitter" ' . $target_blank . '>' . x_icon_get_brand("e61a", "x-icon-twitter-square") . '</a>'; endif;
      if ( $linkedin )    : $output .= '<a href="' . $linkedin    . '" class="linkedin" title="LinkedIn" ' . $target_blank . '>' . x_icon_get_brand("f08c", "x-icon-linkedin-square") . '</a>'; endif;
      if ( $xing )        : $output .= '<a href="' . $xing        . '" class="xing" title="XING" ' . $target_blank . '>' . x_icon_get_brand("f169", "x-icon-xing-square") . '</a>'; endif;
      if ( $foursquare )  : $output .= '<a href="' . $foursquare  . '" class="foursquare" title="Foursquare" ' . $target_blank . '>' . x_icon_get_brand("f180", "x-icon-foursquare") . '</a>'; endif;
      if ( $youtube )     : $output .= '<a href="' . $youtube     . '" class="youtube" title="YouTube" ' . $target_blank . '>' . x_icon_get_brand("f431", "x-icon-youtube-square") . '</a>'; endif;
      if ( $vimeo )       : $output .= '<a href="' . $vimeo       . '" class="vimeo" title="Vimeo" ' . $target_blank . '>' . x_icon_get_brand("f194", "x-icon-vimeo-square") . '</a>'; endif;
      if ( $instagram )   : $output .= '<a href="' . $instagram   . '" class="instagram" title="Instagram" ' . $target_blank . '>' . x_icon_get_brand("f16d", "x-icon-instagram") . '</a>'; endif;
      if ( $pinterest )   : $output .= '<a href="' . $pinterest   . '" class="pinterest" title="Pinterest" ' . $target_blank . '>' . x_icon_get_brand("f0d3", "x-icon-pinterest-square") . '</a>'; endif;
      if ( $dribbble )    : $output .= '<a href="' . $dribbble    . '" class="dribbble" title="Dribbble" ' . $target_blank . '>' . x_icon_get_brand("f17d", "x-icon-dribbble") . '</a>'; endif;
      if ( $flickr )      : $output .= '<a href="' . $flickr      . '" class="flickr" title="Flickr" ' . $target_blank . '>' . x_icon_get_brand("f16e", "x-icon-flickr") . '</a>'; endif;
      if ( $github )      : $output .= '<a href="' . $github      . '" class="github" title="GitHub" ' . $target_blank . '>' . x_icon_get_brand("f092", "x-icon-github-square") . '</a>'; endif;
      if ( $behance )     : $output .= '<a href="' . $behance     . '" class="behance" title="Behance" ' . $target_blank . '>' . x_icon_get_brand("f1b5", "x-icon-behance-square") . '</a>'; endif;
      if ( $tumblr )      : $output .= '<a href="' . $tumblr      . '" class="tumblr" title="Tumblr" ' . $target_blank . '>' . x_icon_get_brand("f174", "x-icon-tumblr-square") . '</a>'; endif;
      if ( $whatsapp )    : $output .= '<a href="' . $whatsapp    . '" class="whatsapp" title="Whatsapp" ' . $target_blank . '>' . x_icon_get_brand("f232", "x-icon-whatsapp") . '</a>'; endif;
      if ( $soundcloud )  : $output .= '<a href="' . $soundcloud  . '" class="soundcloud" title="SoundCloud" ' . $target_blank . '>' . x_icon_get_brand("f1be", "x-icon-soundcloud") . '</a>'; endif;
      if ( $rss )         : $output .= '<a href="' . $rss         . '" class="rss" title="RSS" ' . $target_blank . '>' . $rss_icon . '</a>'; endif;

      $output = apply_filters( 'x_social_global_after', $output );

    $output .= '</div>';

    echo apply_filters( 'x_social_global', $output);

  }
endif;



// Social Meta
// =============================================================================

if ( ! function_exists( 'x_social_meta' ) ) :
  function x_social_meta() {

    if ( !x_get_option( 'x_social_open_graph' ) ) {
      return;
    }

    $url         = get_permalink();
    $type        = ( is_singular() ) ? 'article' : 'website';
    $image       = x_get_featured_image_with_fallback_url();
    $title       = the_title_attribute( array( 'echo' => false ) );
    $site_name   = get_bloginfo( 'name' );
    $description = '';

    if ( is_singular() ) {
      
      //Detect if a post type support an excerpt, then properly retrieve excerpt based on Wordpress get_the_excerpt()
      //Else, get the page content and generate one
      //If we include [cs_content_seo] to strip_tags() again, then it will again remove the entire block including the texts within boundaries, hence, let's  remove [cs_content_seo] first.

      $description = ( post_type_supports ( get_post_type(), 'excerpt') && $excerpt = get_the_excerpt() ) ? $excerpt : preg_replace( '/\[cs_content_seo\](.*)\[\/cs_content_seo\]/msi', '\1', get_post()->post_content );

    }

    $description = trim( wp_trim_words( strip_shortcodes( strip_tags( $description ) ), 35, '' ), '.!?,;:-' ) . '&hellip;';

    if ( ! $description || $description == '&hellip;' ) {
      $description = get_bloginfo( 'description' );
    }

    echo '<meta property="og:site_name" content="'   . $site_name   . '">';
    echo '<meta property="og:title" content="'       . $title       . '">';
    echo '<meta property="og:description" content="' . $description . '">';
    echo '<meta property="og:image" content="'       . $image       . '">';
    echo '<meta property="og:url" content="'         . $url         . '">';
    echo '<meta property="og:type" content="'        . $type        . '">';

  }

add_action( 'wp_head', 'x_social_meta', 2 );

endif;
