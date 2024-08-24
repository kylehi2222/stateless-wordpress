<?php

namespace Themeco\Cornerstone\Tss\Functions;
use Themeco\Cornerstone\Tss\Util\IdEncoder;

class NormalizeParameter extends BuiltInFunction {

  public function run( $pathSource, $idSource ) {
    return '--' . IdEncoder::getEncoder("param",$idSource->toString())->idForPath( $pathSource->toString() );
  }

}