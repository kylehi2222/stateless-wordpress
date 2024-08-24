<?php

namespace Themeco\Cornerstone\Vm\Callstack;
use Themeco\Cornerstone\Util\Factory;

class Frame {

  protected $id;
  protected $data;
  protected $systems;
  protected $parent = null;
  protected $previousFrame = null;
  protected $root = null;
  protected $stack;

  public function __construct() {
    $this->data = new \stdClass;
    $this->systems = new \stdClass;
  }

  public function initializeFrame(Stack $stack, $parent, $from ) {
    $this->id = uniqid();
    $this->stack = $stack;
    $this->parent = $parent;
    $this->previousFrame = $from;
    return $this;
  }

  public function newFrame( $from = null ) {

    $frame = Factory::create(self::class)->initializeFrame(
      $this->stack,
      is_null( $from ) ? $this : $from,
      $this
    );

    $this->stack->initializeFrame($frame);
    $this->stack->setActive($frame);
    return $frame;
  }

  public function root() {
    return $this->stack->frame();
  }

  public function dispose() {
    $this->data = null;
    $this->stack->disposeFrame($this);
  }

  public function id() {
    return $this->id;
  }

  public function previous() {
    return $this->previousFrame;
  }

  public function set($scope, $key, $value) {
    // If is the same dynamic content then ignore
    // And attempt to get parent value
    if (
      is_string($value)
      && (
        strpos($value, "{{dc:p:$key}}") === 0
        || strpos($value, "{{dc:param:$key}}") === 0
      )
    ) {
      $value = $this->get($scope, $key);
    }

    $this->getScope($scope)->set($key, $value);
    $this->data->{$key} = $value;
  }

  public function getScope($scope) {
    if (!isset($this->data->{$scope})) {
      $this->data->{$scope} = Factory::create(Scope::class);
    }
    return $this->data->{$scope};
  }

  public function get($scope, $key, $trace = 0) {
    if ( $trace === -1 ) {
      return $this->stack->get( $scope, $key );
    }
    if ( $trace > 0 ) {
      return $this->parent->get( $scope, $key, $trace - 1 );
    }

    $ctx = $this->getScope($scope);

    if ( $ctx->has( $key ) ) {
      return $ctx->get( $key );
    }

    if ( ! is_null( $this->parent ) ) {
      return $this->parent->get( $scope, $key );
    }

    return null;
  }

  public function addSystem($key, $obj) {
    return $this->systems->{$key} = $obj;
  }

  public function system($key) {
    return $this->systems->{$key};
  }

}
