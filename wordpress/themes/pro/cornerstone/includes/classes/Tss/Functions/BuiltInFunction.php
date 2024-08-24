<?php

namespace Themeco\Cornerstone\Tss\Functions;

use Themeco\Cornerstone\Tss\Traits\StackAccessor;
use Themeco\Cornerstone\Tss\Typed\Typed;

class BuiltInFunction {

  use StackAccessor;

  public function getArgs() {
    $reflector = new \ReflectionClass(static::class);
    $params = $reflector->getMethod( 'run')->getParameters();
    $args = [];
    foreach ($params as $param) {
      $args[$param->getName()] = $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null;
    }

    return $args;
  }

  public function call($args) {
    $result = call_user_func_array( [ $this, 'run' ], $args );
    return $this->isTyped( $result ) ? $result : $this->stack->evaluator()->makeTyped('primitive', $result );
  }

  public function isTyped( $input ) {
    return is_a( $input, Typed::class);
  }

  public static function make($name, $stack) {
    $fn = self::identify($name);
    if ($fn) {
      $fn->setStack($stack);
      return $fn;
    }
    return null;
  }

  public static function identify($name) {
    switch ($name) {
      case 'get':
        return new Get;
      case 'get-base':
        return new GetBase;
      case 'is-base':
        return new IsBase;
      case 'empty':
        return new IsEmpty;
      case 'off':
        return new IsOff;
      case 'contains':
        return new Contains;
      case 'merge':
        return new Merge;
      case 'pre-process':
        return new PreProcess;
      case 'global-color':
        return new GlobalColor;
      case 'global-ff':
        return new GlobalFontFamily;
      case 'global-fw':
        return new GlobalFontWeight;
      case 'resolve-image':
        return new ResolveImage;
      case 'normalize-parameter-key':
        return new NormalizeParameter;
      case 'svg-in-url':
        return new SvgInUrl;
    }
  }
}