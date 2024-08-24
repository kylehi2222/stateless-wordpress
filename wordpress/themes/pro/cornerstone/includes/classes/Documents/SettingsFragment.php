<?php

namespace Themeco\Cornerstone\Documents;

class SettingsFragment implements IDocumentSettings {

  protected $id;
  protected $data;
  protected $stylePriority;

  public function setup( $id, $data, $stylePriority = []) {
    $this->id = $id;
    $this->data = $data;
    $this->stylePriority =count($stylePriority) === 2 ? $stylePriority : [5,6];
    return $this;
  }

  public function getCustomCss() {
    return $this->data['customCss'];
  }

  public function getCustomJs() {
    return $this->data['customJs'];
  }

  public function id() {
    return $this->id;
  }

  public function getStylePriority() {
    return $this->stylePriority;;
  }

}