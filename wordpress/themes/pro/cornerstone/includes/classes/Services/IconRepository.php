<?php

namespace Themeco\Cornerstone\Services;

class IconRepository {
  private static $icons = [];

  public static function setIcons($data) {
    IconRepository::$icons = $data;
  }

  public static function getIcon($name) {
    if (empty(IconRepository::$icons)) {
      IconRepository::$icons = include( __DIR__ . '/../../elements/icons.php' );
    }

    return IconRepository::$icons[$name];
  }

  public static function getWithCSSClass($name, $class = '') {
    $icon = IconRepository::getIcon($name);

    if (empty($icon)) {
      return '';
    }

    $icon = preg_replace('/^<svg/', "<svg class='{$class}'", $icon);

    return $icon;
  }
}
