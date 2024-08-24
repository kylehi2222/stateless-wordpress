<?php

/**
 * Extension for state
 */

namespace Cornerstone\TwigIntegration;

use Twig\TwigFunction;

class StateExtension extends \Twig\Extension\AbstractExtension
{
  private $state = [];

  public function getFunctions()
  {
    return [
      new TwigFunction('set_state', [$this, 'setState']),
      new TwigFunction('set_state_key', [$this, 'setStateKey']),
      new TwigFunction('get_state', [$this, 'getState']),
      new TwigFunction('get_state_key', [$this, 'getStateKey']),
    ];
  }

  public function setState($data) {
    $this->state = $data;
  }

  /**
   * Set key in state
   */
  public function setStateKey($key, $data) {
    // Errors
    if (!is_array($this->state)) {
      trigger_error('You have changed the state from an array to something else, you therefore cannot use set_state_key');
      return;
    }

    // Set key
    $this->state[$key] = $data;
  }

  public function getState() {
    return $this->state;
  }

  public function getStateKey($key) {
    return cs_get_path($this->state, $key);
  }
}
