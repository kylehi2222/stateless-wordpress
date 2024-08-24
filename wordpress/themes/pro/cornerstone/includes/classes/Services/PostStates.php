<?php

namespace Themeco\Cornerstone\Services;

require_once ABSPATH . '/wp-admin/includes/template.php';

const WC_LOAD_FILE = ABSPATH . '/wp-content/plugins/woocommerce/includes/admin/class-wc-admin-post-types.php';

class PostStates {
  //State
  static $loaded = false;

  public const META_STATE_KEY = "_cs_states_cache";


  /**
   * Main loader
   * @TODO figure out why we need to manually load in some of these files
   */
  public static function load() {
    if (static::$loaded) {
      return;
    }

    //WooCommerce
    if (class_exists('WooCommerce') && file_exists(WC_LOAD_FILE)) {
      require_once WC_LOAD_FILE;
    }

    static::$loaded = true;
  }

  /**
   * Helper loads up if not loaded
   */
  public static function getPostState($post) {
    static::load();

    return (object)get_post_states($post);
  }

  public static function savePostState($post, $states = []) {
    static::load();

    //Save as comma delim string
    $states_str = implode(" ", $states);

    $data = get_post_meta($post->ID, static::META_STATE_KEY, false);

    //Already all good
    if ($data === $states_str) {
      return;
    }

    //Update DB
    update_post_meta( $post->ID, static::META_STATE_KEY, $states_str );
  }
}
