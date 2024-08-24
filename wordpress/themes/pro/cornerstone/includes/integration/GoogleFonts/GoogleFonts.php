<?php

/**
 * Google Fonts integration
 *
 * Used by GlobalFonts and most code exists there still
 */

function cs_google_fonts_enabled() {
  $fontConfig = cornerstone('GlobalFonts')->get_font_config();
  return apply_filters('cs_load_google_fonts', '__return_true' ) && empty($fontConfig['googleDisabled']);
}

function cs_google_fonts_uri() {
  $fontConfig = cornerstone('GlobalFonts')->get_font_config();

  $uri = empty($fontConfig['googleFontsURL'])
    ? '//fonts.googleapis.com/css'
    : $fontConfig['googleFontsURL'];

  return apply_filters('cs_google_fonts_uri', $uri );
}
