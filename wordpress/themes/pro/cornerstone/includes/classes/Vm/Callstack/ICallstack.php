<?php

namespace Themeco\Cornerstone\Vm\Callstack;

interface ICallstack {
  public function exec($input);
  public function initializeStackFrame( $frame );
}