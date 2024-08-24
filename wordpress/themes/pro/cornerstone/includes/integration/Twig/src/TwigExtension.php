<?php

/**
 * Extension for DC to Twig
 */

namespace Cornerstone\TwigIntegration;

class TwigExtension extends \Twig\Extension\AbstractExtension implements \Twig\Extension\GlobalsInterface
{

  /**
   * Global variables
   * dc maps to class later
   *
   * Any error that happens here will be hidden and cause a white screen of death
   */
  public function getGlobals() : array
  {
    // Default map of original {{dc.post.id}}
    $out = [
      'dc' => new DCToTwigGrabber(),
    ];

    // Register groups for using {{post.id}}
    $fields = cornerstone('DynamicContent')->get_dynamic_fields();

    foreach ($fields['groups'] as $key => $group) {
      $out[$key] = new DCToTwigGrabber($key);

      // Setup aliases like p.id
      if (!empty($group['aliases']) && is_array($group['aliases'])) {
        foreach ($group['aliases'] as $alias) {
          $out[$alias] = $out[$key];
        }
      }
    }


    return $out;
  }

}
