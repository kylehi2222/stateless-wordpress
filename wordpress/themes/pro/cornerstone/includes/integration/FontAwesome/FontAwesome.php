<?php

require_once(__DIR__ . '/migrations.php');

// Github hack for weird SVG naming descrepency

use Themeco\Cornerstone\Services\FontAwesome;

add_filter("cs_fa_svg_icon_object", function($icon) {
  // Not broken github square
  if ($icon['icon'] !== 'github-square') {
    return $icon;
  }

  // Overwrite
  $icon['shorthand'] = 'b';
  $icon['type'] = 'brands';
  $icon['icon'] = 'square-github';

  return $icon;
});

// Install SVG from zip download
add_action( 'init', function() {
  do_action("cs_fa_svg_ensure_load", get_option("x_font_awesome_icon_type"));
}, 1000);

// FA SVG ensure load and unzip svg folder
// for usage
add_action("cs_fa_svg_ensure_load", function($type) {

  static $svgDirectoryOkay;
  static $svgFADir = CS_ROOT_PATH . '/assets/svg/font_awesome';

  // SVG directory not checked
  if (is_null($svgDirectoryOkay)) {
    $svgDirectoryOkay = file_exists($svgFADir);
  }

  // SVG unzipped
  // but running webfont mode
  // with no individual controls
  // Delete directory
  if (
    !is_multisite()
    && $type !== "svg"
    && $svgDirectoryOkay
    && !FontAwesome::hasIndividualLoadTypes()
  ) {
    cs_delete_directory($svgFADir);
    $svgDirectoryOkay = false;
  }

  // Already good
  if (
    $svgDirectoryOkay
    || $type !== "svg"
  ) {
    return;
  }

  $svgDir = CS_ROOT_PATH . '/assets/svg/';
  $zipPath = CS_ROOT_PATH . '/assets/svg/font_awesome.zip';

  // To prevent extra unzips and extra errors
  $svgDirectoryOkay = true;

  // Check php-zip is installed
  if (!class_exists("ZipArchive")) {
    trigger_error("Attempting to load FontAwesome SVG without php-zip installed. Please install php-zip or switch icon or ThemeOption to Webfont");
    return;
  }

  $zip = new ZipArchive;

  // No zip found
  // this would be an error but I already know
  // somebody will delete the svg folder
  if ($zip->open($zipPath) !== true) {
    trigger_error("Could not find FA Svg zip folder. Reinstall Cornerstone or switch to FontAwesome Webfont mode");
    return;
  }

  // Extract directory
  $zip->extractTo($svgDir);

  // Close streams
  $zip->close();

});

// Force Webfonts if php-zip is not installed
add_filter("pre_option_x_font_awesome_icon_type", function($type) {
  // Zip Archive check
  if (!class_exists("ZipArchive")) {
    return 'webfont';
  }

  return $type;
});
