<?php

namespace Themeco\Cornerstone\Parsy\Util;

class ParseException extends \Exception {
  private $states;

  public function __construct($message, $states = [])  {
    $this->states = $states;
    parent::__construct($message);
  }

  public function getStates() {
    return $this->states;
  }

  /**
   * @return Error
   */
  public function getParseError() {
    return !empty($this->states)
      ? $this->states[1]
      : [];
  }

  /**
   * Parse error readable message
   *
   * @return string
   */
  public function getNiceMessage() {
    $parseError = $this->getParseError();
    $parseError->getIndex();

    $index = $parseError->getIndex();

    $ex = str_split($parseError->getTarget());
    $ex = array_slice($ex, $index, 100);
    $ex = implode("", $ex);

    $msg = $this->getMessage() . "\n"
      . $ex;

    return "<pre>{$msg}</pre>";
  }
}
