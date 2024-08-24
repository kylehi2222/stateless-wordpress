<?php

namespace Themeco\Cornerstone\Tss\Functions;

class ResolveImage extends BuiltInFunction {

  public function run( $imageSource ) {
    return cs_resolve_image_source( $imageSource->toString() );
  }
}