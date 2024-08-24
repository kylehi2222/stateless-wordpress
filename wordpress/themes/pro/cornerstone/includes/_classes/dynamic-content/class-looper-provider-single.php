<?php

// The archive query passes through to the existing query in scope from WordPress

class Cornerstone_Looper_Provider_Single extends Cornerstone_Looper_Provider_Wp_Query {

  protected $returnedOnce = false;

  protected function query_advance() {
    global $post;

    if (is_singular() && !$this->returnedOnce) {
      $this->returnedOnce = true;
      return $post;
    }

    return false;
  }

  public function query_end() {
    $this->returnedOnce = false;
  }

  public function query_begin() {
    $this->returnedOnce = false;
  }
}
