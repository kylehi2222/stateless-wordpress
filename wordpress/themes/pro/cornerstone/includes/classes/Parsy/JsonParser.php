<?php

namespace Themeco\Cornerstone\Parsy;

use Themeco\Cornerstone\Parsy\P;

class JsonParser {

  public $language;

  public function __construct( $args = [] ) {

    $this->language = P::createLanguage([
      'value' => function ($l) {
        return P::any(
          $l->object,
          $l->array,
          $l->string,
          P::scalarNumber(),
          P::scalarNull(),
          P::scalarTrue(),
          P::scalarFalse()
        );
      },

      'string' => P::ws(P::doubleQuotedEscapedString()),

      'pair' => function( $l ) {
        return $l->string->skip( P::colon() )->next($l->value)->name('pair');
      },

      'array' => function( $l ) {
        return P::betweenBracketsSepByCommaStrict($l->value);
      },

      'object' => function($l) {
        return P::betweenBracesSepByCommaStrict($l->pair)->map(function ($pairs) {
          $result = new \stdClass;
          foreach ( $pairs as $pair ) {
            list($key, $value) = $pair;
            $result->{$key} = $value;
          }
          return $result;
        })->name('object');
      }
    ]);

  }

  public function __get($key) {
    return $this->language->{$key};
  }

}
