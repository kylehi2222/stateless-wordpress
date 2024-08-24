<?php

namespace Themeco\Cornerstone\Services;

class Permalinks implements Service {

  public function hasValidStructure() {

    $structure = get_option( 'permalink_structure' );

    // Permalinks disabled
    if ( ! $structure ) {
      return false;
    }

    // Don't support PATHINFO rules
    if ( false !== strpos( $structure, 'index.php' ) ) {
      return false;
    }

    return true;

  }
}