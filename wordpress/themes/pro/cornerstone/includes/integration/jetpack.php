<?php

//Jetpack integration with CS_CONTENT_SEO,
//This is needed since jetpack directly pulls content from post_content instead of using Wordpress generated excerpt bypassing the function that cleans excerpt.
//Resulting to visible [cs_content_seo], hence, let's clean it here
add_filter( 'jetpack_open_graph_tags', function( $opengraph) {

  if(empty($opengraph['og:description'])) {
    return $opengraph;
  }

  $description = $opengraph['og:description'];

  if ( !strpos( $description, '[cs_content_seo]') ) {

    $opengraph ['og:description'] = preg_replace('/\[cs_content_seo\]|\[\/cs_content_seo\]/m', '', $description );

  }

  return  $opengraph;

}, 999999 );

// Fix background video issue only present
// because jetpack includes mediaelementplayer-legacy.css
add_action("init", function() {
  $css = <<<CSS
.bg .mejs-container,
.x-video .mejs-container
 {
  position: unset !important;
}
CSS;

  // Register CSS
  cornerstone_register_styles("cs-jetpack-background-video-fix", $css);
});
