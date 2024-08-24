<?php

/**
 * Font Awesome sharps had invalid false values of '0' during launch
 * this changes that for 6.4.5+ update
 */
add_action("cs_fa_migration_645", function() {
  $affected = [
    'x_font_awesome_sharp-light_enable',
    'x_font_awesome_sharp-regular_enable',
    'x_font_awesome_sharp-solid_enable',
  ];

  // Update to proper false value
  $update = function($type) {
    $val = get_option($type);
    if ($val !== "0") {
      return;
    }

    update_option($type, false, true);
  };

  foreach ($affected as $toAffect) {
    $update($toAffect);
  }
});
