<?php

namespace Themeco\Cornerstone\Tss\Util;

class IdEncoder {
  protected $base = 0;
  protected $prefix = '';
  protected $nextId = 0;
  protected $cache = [];

  protected static $encoderCache = [];

  protected $pathCache = [];

  public function setup( $base, $prefix = '' ) {
    $this->base = $base;
    $this->prefix = $prefix;
    return $this;
  }

  protected function encodeIds( $ids = []) {
    $encoded = array_map(function($id) {
      return is_int($id) ? base_convert($id,10,36) : $id;
    }, $ids);
    return $this->prefix  . implode('-', $encoded);
  }

  public function nextId() {
    return $this->encodeIds([$this->base, $this->nextId++ ]);
  }

  public function idForPath( $path ) {
    if ( ! isset( $this->pathCache[ $path ] ) ) {
      $this->pathCache[ $path ] = $this->nextId();
    }
    return $this->pathCache[ $path ];
  }

  public static function getEncoder( $namespace, $base ) {
    $key = "::" . $base;
    if ( ! isset( self::$encoderCache[ $namespace ] ) ) {
      self::$encoderCache[ $namespace ] = [];
    }
    if ( ! isset( self::$encoderCache[ $namespace ][ $key ] ) ) {
      $instance = new self;
      $instance->setup($base);
      self::$encoderCache[ $namespace ][ $key ] = $instance;
    }
    return self::$encoderCache[ $namespace ][ $key ];
  }

}
