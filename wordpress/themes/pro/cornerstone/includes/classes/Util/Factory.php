<?php

namespace Themeco\Cornerstone\Util;

class Factory {

  protected static $container;

  public static function create($className) {
    return self::$container->resolve($className);
  }

  public static function container() {
    return self::$container;
  }

  public static function setup() {
    if ( empty ( self::$container ) ) {
      self::$container = new IocContainer;
    }

  }

}
