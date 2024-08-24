<?php

namespace Themeco\Cornerstone\Services;

class VersionMigration implements Service {

  public function setup() {
    add_action( 'init', [ $this, 'versionMigration' ], -1000 );
  }

  public function versionMigration() {
    $prior = get_option( 'cornerstone_version', 0 );

    if ( version_compare( $prior, CS_VERSION, '>=' ) ) {
      return;
    }

    $this->update( $prior );
    do_action( 'cornerstone_updated', $prior );
    do_action( 'cs_purge_tmp' );
    do_action( 'cs_purge_cache' );

    update_option( 'cornerstone_version', CS_VERSION, true );

  }

  // Upgrade / update method
  public function update( $prior ) {
    // 7.3.0 to 7.4.0 / 6.3.0 to 6.4.0
    if ( version_compare( $prior, '7.4.0-beta1', '<' ) ) {
      FontAwesome::migrateFromBeforeVersion64();
    }
  }

}
