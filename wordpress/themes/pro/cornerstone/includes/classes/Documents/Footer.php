<?php

namespace Themeco\Cornerstone\Documents;

class Footer extends Layout {

  public function getStylePriority() {
    return [30,90];
  }

  public function getRegions() {
    return ['footer'];
  }

  public function getDocType() {
    return 'layout:footer';
  }

}