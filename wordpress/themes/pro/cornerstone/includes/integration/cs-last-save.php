<?php

namespace Cornerstone\LastSave;

function updateLastSave() {
  update_option( 'cs_last_save', current_time( 'mysql' ) );
}

add_action('cs_save_document', __NAMESPACE__ . '\updateLastSave');
add_action('cs_theme_options_before_save', __NAMESPACE__ . '\updateLastSave');
