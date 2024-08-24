<?php

namespace Themeco\Cornerstone\Documents;

class Header extends Layout {

  public function getStylePriority() {
    return [20,80];
  }

  public function getDocType() {
    return 'layout:header';
  }

  public function getRegions() {
    return ['top', 'right', 'bottom', 'left'];
  }

  public function defaultSettings() {
    return array_merge(
      $this->defaultSettingsLayout(),
      [
        'multi_region' => false
      ]
    );
  }

  public function getGeneralControls() {
    return [
      [
        'key'         => 'multi_region',
        'type'        => 'toggle',
        'label'       => __( 'Multi Region', 'cornerstone' ),
        'description' => __( 'Allows for more advanced layout options, such as left, right, and bottom Bar positioning. After selecting, go back to the "Outline" tab to see these new regions.', 'cornerstone' ),
      ]
    ];
  }

}