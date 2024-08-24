<?php

// Base class for all WP Query based loopers

abstract class Cornerstone_Looper_Provider_Wp_Query extends Cornerstone_Looper_Provider {

  protected $previous_post = null;

  public function setup( $element = []) {
    $this->setup_query( $element, $this->config );
  }

  public function begin() {
    global $post;
    if ( $post ) {
      $this->previous_post = $post;
    }
    $this->query_begin();
  }

  public function resume() {
    $this->query_resume();
  }

  public function pause() {
    $this->query_pause();
  }

  public function end() {

    $this->query_end();

    global $post;

    if ( $this->previous_post ) {
      $post = $this->previous_post;
      setup_postdata( $post );
    }

  }

  public function advance() {
    return $this->query_advance();
  }

  public function rewind() {
    return $this->query_rewind();
  }

  public function get_context() {
    global $wp_query;
    return $wp_query;
  }

  public function get_index() {
    global $wp_query;
    return $wp_query->current_post;
  }

  public function get_size() {
    global $wp_query;
    return $wp_query->post_count;
  }

  public function total_pages() {
    global $wp_query;
    return $wp_query->max_num_pages;
  }

  public function found_posts() {
    global $wp_query;
    return $wp_query->found_posts;
  }

  public function setup_query($element, $config) {

  }

  public function query_begin() {

  }

  public function query_end() {
    wp_reset_query();

    // Reset for special pages
    if (is_author()) {
      global $post;
      $post = null;
    }
  }

  public function query_pause() {
    global $wp_query;
    $wp_query->in_the_loop = false;
  }

  public function query_resume() {
    wp_reset_postdata();
  }

  protected function query_rewind() {
    rewind_posts();
  }

  protected function query_advance() {
    global $post;
    if (!is_singular() && have_posts()) {
      the_post();
      return $post;
    } else {
      return false;
    }
  }

}
