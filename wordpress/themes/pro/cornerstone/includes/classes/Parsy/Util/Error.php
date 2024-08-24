<?php

namespace Themeco\Cornerstone\Parsy\Util;

class Error extends State {
  protected $isError = true;
  protected $errorName;
  protected $errorMessage;

  public function __construct($previous, $name, $message) {
    $this->copyFrom($previous);
    $this->errorName = $name;
    $this->errorMessage = $message;
  }

  public function getErrorMessage() {
    return sprintf("%s|index: %d| %s", $this->errorName, $this->index, $this->errorMessage);
  }

}
