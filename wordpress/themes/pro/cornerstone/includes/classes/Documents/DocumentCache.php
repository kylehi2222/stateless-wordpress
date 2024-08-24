<?php

namespace Themeco\Cornerstone\Documents;

use Themeco\Cornerstone\Documents\Document;

class DocumentCache {
  protected $cache = [];
  protected $documentCache = [];

  public function getKey( $postOrId ) {
    return (string) (is_a( $postOrId, 'WP_Post') ? $postOrId->ID : (int) $postOrId);
  }

  public function set($postOrId, $value) {
    $this->documentCache[$this->getKey($postOrId)] = $value;
  }

  public function unset($postOrId) {
    unset($this->documentCache[$this->getKey($postOrId)]);
  }

  public function get($postOrId) {

    $key = $this->getKey($postOrId);

    if ( ! isset($this->documentCache[$key] ) ) {
      $this->documentCache[$key] = Document::locate($postOrId);
    }
    return $this->documentCache[$key];
  }
}
