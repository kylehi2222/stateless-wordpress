<?php

namespace Themeco\Cornerstone\Parsy;

class Language {

  protected $debug;
  protected $language;

  public function setup($debug = false) {
    $this->debug = $debug;
    $this->language = self::create($this->grammar());
    return $this;
  }

  public function grammar() {
    return [];
  }

  public function __get($key) {
    return $this->language->{$key};
  }

  // This lets us define a set of parsers that can be called recursively
  public static function create( $grammar ) {
    $language = new \stdClass;
    $cache = new \stdClass;
    foreach ( $grammar as $key => $parserFn ) {
      if (!is_callable($parserFn)) {
        $language->{$key} = $parserFn;
      } else {
        $language->{$key} = P::lazy(function() use( $parserFn, $language, &$cache, $key ) {
          if ( ! isset( $cache->{$key} ) ) {
            $cache->{$key} = $parserFn($language,$key)->name($key);
          }
          return $cache->{$key};
        }, $key);
      }
    }
    return $language;
  }

}
