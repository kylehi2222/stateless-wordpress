<?php

// =============================================================================
// FUNCTIONS/OPTIONS.PHP
// -----------------------------------------------------------------------------
// Plugin options.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Set Options
//   02. Get Options
// =============================================================================

// Set Options
// =============================================================================

//
// Set $_POST variables to options array and update option.
//

GLOBAL $tco_custom_404_options;

if ( isset( $_POST['tco_custom_404_form_submitted'] ) ) {
  if ( strip_tags( $_POST['tco_custom_404_form_submitted'] ) == 'submitted' && current_user_can( 'manage_options' ) && wp_verify_nonce( $_POST['tco_custom_404_noncename'], 'tco_custom_404' ) ) {

    $tco_custom_404_options['tco_custom_404_enable']        = ( isset( $_POST['tco_custom_404_enable'] ) ) ? strip_tags( $_POST['tco_custom_404_enable'] ) : '';
    $tco_custom_404_options['tco_custom_404_entry_include'] = strip_tags( $_POST['tco_custom_404_entry_include'] );

    update_option( 'tco_custom_404', $tco_custom_404_options );

  }
}



// Get Options
// =============================================================================

$tco_custom_404_options = apply_filters( 'tco_custom_404_options', get_option( 'tco_custom_404' ) );

if ( $tco_custom_404_options != '' ) {

  $tco_custom_404_enable        = $tco_custom_404_options['tco_custom_404_enable'];
  $tco_custom_404_entry_include = $tco_custom_404_options['tco_custom_404_entry_include'];

}
