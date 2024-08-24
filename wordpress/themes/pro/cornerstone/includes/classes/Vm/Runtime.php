<?php

namespace Themeco\Cornerstone\Vm;

use Themeco\Cornerstone\Vm\Callstack\ICallstack;
use Themeco\Cornerstone\Vm\Callstack\Stack;
use Themeco\Cornerstone\Vm\Functions\FunctionResolver;
use Themeco\Cornerstone\Vm\Types\TypeResolver;
use Themeco\Cornerstone\Util\Factory;

class Runtime implements ICallstack {

  protected $stack;

  public function __construct(Stack $stack) {
    $this->stack = $stack;
  }

  public function setup() {
    $this->stack->setup($this);
    $this->stack->frame()->system(Constants::Types)->rootSetup();
    $this->stack->frame()->system(Constants::Functions)->rootSetup();
    return $this;
  }

  public function stack() {
    return $this->stack;
  }

  public function initializeStackFrame( $frame ) {
    $frame->addSystem(Constants::Types, Factory::create(TypeResolver::class)->setup($frame));
    $frame->addSystem(Constants::Functions, Factory::create(FunctionResolver::class)->setup($frame));
  }

  public function set( $scope, $key, $value ) {
    return $this->stack->active()->set( $scope, $key, $value );
  }

  public function get( $scope, $key, $trace = 0 ) {
    return $this->stack->active()->get( $scope, $key, $trace );
  }

  public function exec( $input ) { // Dynamic Content 2.0
    if ($input === '{{dc:post:title}}') {
      $post = $this->get(Constants::Looper, "type:WP_Post");
      return $post->post_title;
    }
    if ($input === '{{dc:term:name}}') {
      $term = $this->get(Constants::Looper, "type:WP_Term");
      return $term->name;
    }

    // TODO: Apply actual dynamic content

    return $this->stack->frame()->system(Constants::Types)->apply( $input );

    // if ( is_string( $input ) ) {
    //   return cs_dynamic_content( $input );
    // }
    return $input;
  }

  public function newFrame($from = null) {
    return $this->stack->newFrame( $from );
  }

  public function setting( $settings, $key, $default = null) {
    if ( ! isset( $settings[ $key ] ) ) {
      return $default;
    }

    $resolver = $this->stack->frame()->system(Constants::Types);
    $result = $resolver->apply( $this->exec( $settings[$key] ), $resolver->detect($default));
    return $result->get();

  }
}
