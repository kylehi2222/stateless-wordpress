<?php

namespace Themeco\Cornerstone\Vm\Callstack;
use Themeco\Cornerstone\Util\Factory;

class Stack {

  protected $frames = [];
  protected $frame;
  protected $env;
  protected $active;

  public function __construct(Frame $frame) {
    $this->frame = $frame;
    $this->active = $this->frame;
  }

  public function setup(ICallstack $env) {
    $this->env = $env;
    $this->frame->initializeFrame($this, null, null);
    $this->env->initializeStackFrame( $this->frame );
    return $this;
  }

  public function initializeFrame( $frame ) {
    $this->env->initializeStackFrame( $frame );
    $this->frames[$frame->id()] = $frame;
  }

  public function disposeFrame( $frame ) {
    $this->setActive($frame->previous());
    unset($this->frames[$frame->id()]);
  }

  public function setActive($frame) {
    $this->active = $frame;
  }

  public function active() {
    return $this->active;
  }

  public function newFrame( $from = null ) {
    return $this->active->newFrame( $this->locateOtherFrame( $from ) );
  }

  public function locateOtherFrame( $frameOrId ) {
    if ( is_null( $frameOrId ) ) {
      return $frameOrId;
    }

    if ( is_a( $frameOrId, Frame::class ) ) {
      return $frameOrId;
    }

    return isset( $this->frames[$frameOrId] ) ? $this->frames[$frameOrId] : null;
  }

  public function env() {
    return $this->env();
  }

  public function frame() {
    return $this->frame;
  }

}
