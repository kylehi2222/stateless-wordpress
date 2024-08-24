<?php

namespace Themeco\Cornerstone\Util;

class Filesystem {

  public function getFileSystem() {
    if ( ! function_exists( 'WP_Filesystem' ) ) {
      require_once ABSPATH . 'wp-admin/includes/file.php';
    }

    if (get_filesystem_method() !== 'direct' || ! WP_Filesystem()) {
      throw new \Exception('Unable to use file system.');
    }

    global $wp_filesystem;
    return $wp_filesystem;
  }

  public function sendFile( $file ) {
    // clear any possible buffers to
    // fix .tco issues on sites outputting stuff
    // all the time
    while (ob_get_level() !== 0) {
      ob_end_clean();
    }

    header("Content-type: application/octet-stream");
    echo $this->getFileSystem()->get_contents( $file );
    exit;
  }

}
