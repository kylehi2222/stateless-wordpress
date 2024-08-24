<?php

namespace Themeco\Cornerstone\Parsy;

use Themeco\Cornerstone\Parsy\P;

/**
 * The intent of this parser is to help break down colors PHP side so we can modify the alpha value
 * and reconstitute the color.
 *
 * This logic would happen in the GlobalColors service, in the applyColor method.
 * The idea is that global colors could be saved as hex codes or HSLA and still allow the alpha to be changed
 * per instance. With the current regex implementation it only works on rgba colors
 */
class ColorParser {

  public $language;

  public function __construct( $args = [] ) {

    $this->language = P::createLanguage([
      'hexcode' => function ($l) {
        return P::str('#')->then(P::digitsWithHex())->chain(function($result) {
          $digits = str_split($result);
          // var_dump($digits);
          switch (count($digits)) {
            case 3:

            case 4:
            case 6:
            case 8:
            default:
              return P::abort();
          }

        });
      },


    ]);

  }

  public function __get($key) {
    return $this->language->{$key};
  }

}
