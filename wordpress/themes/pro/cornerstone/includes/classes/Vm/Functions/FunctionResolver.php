<?php

namespace Themeco\Cornerstone\Vm\Functions;

use Themeco\Cornerstone\Vm\Callstack\Frame;
use Themeco\Cornerstone\Vm\Constants;
use Themeco\Cornerstone\Vm\Lib\LibLoader;
use Themeco\Cornerstone\Vm\Operations\OpLoader;

class FunctionResolver {
  protected $frame;

  public function setup(Frame $frame) {
    $this->frame = $frame;
    return $this;
  }

  public function rootSetup() {
    LibLoader::load($this);
    OpLoader::load($this);
  }

  public function locate( $name ) {
    return $this->frame->get(Constants::Functions, $name);
  }

  public function invoke( $name, Arguments $input ) {
    return $this->locate( $name )->invoke( $this->frame, $input );
  }

  public function define( $name, Invokable $fn ) {
    $this->frame->set(Constants::Functions, $name, $fn);
    return $fn;
  }

  public function fromStaticClass( $className ) {

    $reflection = new \ReflectionClass( $className );
    $functions = $reflection->getMethods(\ReflectionMethod::IS_STATIC);

    foreach ($functions as $fn) {
      list($argTypes, $cb) = call_user_func( [ $fn->class, $fn->name]);

      $invokable = new Invokable;
      foreach( $argTypes as $name => $type) {
        if ( is_array( $type ) ) {
          $invokable->addArgument($name, $type[0], $type[1] );
        } else {
          $invokable->addArgument($name, $type);
        }
      }

      $this->define( $fn->name, $invokable->setHandler( $cb ) );
    }

  }

  public function fromClosure( $name, \Closure $fn) {

    $invokable = new InvokableClosure;

    $reflection = new \ReflectionFunction($fn);
    $arguments  = $reflection->getParameters();

    foreach ( $reflection->getParameters() as $param ) {
      $invokable->addArgument(
        $param->name,
        self::getType( $param ),
        $param->isOptional() ? $param->getDefaultValue() : null
      );
    }

    $this->define( $name, $invokable->setHandler($fn) );

  }

  public static function getType( $param ) {
    if ( ! $param->hasType() ) {
      return null;
    }

    $type = $param->getType();

    if ( $type->isBuiltin() ) {
      return $type->getName();
    }

    return $param->getType()->getName();

  }

}
