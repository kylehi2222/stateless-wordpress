<?php

namespace Cornerstone\TwigIntegration;

/**
 * Turns a standard DC group.field grab in twig
 * into a dynamic __call based the internal Dynamic Content filters
 */

class DCToTwigGrabber {
  private $group = null;

  public function __construct($_group = null)
  {
    $this->group = $_group;
  }

  public function __call($field = '', $args = [])
  {
    // No group set this is a {{dc.*}} call
    // Return another twig grabber
    if ($this->group === null) {
      return new DCToTwigGrabber($field);
    }

    // The PHP args are sent individually
    // We are only expecting one arg sent as 'JSON'
    // in twig
    if (isset($args[0])) {
      $args = $args[0];
    }

    // Run Dynamic content filters
    $result = apply_filters( "cs_dynamic_content_{$this->group}", '', $field, $args );
    $result = apply_filters( "cs_dynamic_content_{$this->group}_{$field}", $result, $args );

    return $result;
  }

  public function __toString()
  {
    return "dc." . $this->group;
  }
}
