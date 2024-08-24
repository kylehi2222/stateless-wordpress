<?php

namespace Themeco\Cornerstone\Tss\Reducers;

/**
 * DeclarationReducer takes all the style declarations from a TSS result
 * and reduces them down to a final CSS string. The declarations are sorted into media queries
 *
 * When TSS is generated, shorthand properties are expended.
 * This class would be the ideal place to reconstitute shorthand properties if they are all present in a group
 */

class DeclarationReducer {

  static protected $maxThreshold = -1;

  public static function reduce($declarations, $baseBreakpoint, $ranges, $selectorPrefix) {
    $_prefix = is_string( $selectorPrefix ) ? trim($selectorPrefix) . ' ' : '';

    $tiers = [];

    $priorities = [];

    foreach($declarations as $declaration) {
      list($value, $property, $selector, $minMax) = $declaration;
      $_selector = $_prefix . $selector;
      list($priority, $mq) = static::resolveMediaQuery( $minMax, $baseBreakpoint, $ranges );
      if ( !isset( $tiers[$mq] ) ) {
        $priorities[$mq] = $priority;
        $tiers[$mq] = [];
      }
      if ( !isset( $tiers[$mq][$_selector] ) ) {
        $tiers[$mq][$_selector] = [];
      }
      $tiers[$mq][$_selector][$property] = $value;
    }

    $buffer = '';

    // SORT keys of $tiers based on priority
    uksort($tiers, function($a, $b) use ($priorities){
      if ($priorities[$a] == $priorities[$b]) {
        return 0;
      }
      return ($priorities[$a] < $priorities[$b]) ? -1: 1;
    });

    foreach( $tiers as $q => $styles) {

      $rules = '';

      foreach ($styles as $selector => $props) {
        $style = '';
        foreach ($props as $prop => $value) {
          if (! is_null($value) && $value !== '') {
            $style .= "$prop:$value;";
          }
        }
        if($style) {
          $rules .= $selector . '{' . $style . '}';
        }

      }

      if ($q === 'root') {
        $buffer .= $rules;
      } else {
        $buffer .= $q . '{' . $rules . '}';
      }

    }

    // echo '<pre>';
    // var_dump($buffer);
    // echo '</pre>';
    return $buffer;

  }

  public static function distanceFromBase($base, $number) {
    if ($number > $base) return $number - $base;
    if ($number < $base) return $base - $number;
    return 0;
  }

  // Return a media query and priority based on its distance from the base
  public static function resolveMediaQuery( $minMax, $baseBreakpoint, $ranges ) {

    list($min, $max) = $minMax;

    if ( is_null( $min ) && is_null( $max ) ) {
      return [-1, 'root'];
    }

    if ( is_null( $min ) ) {
      $maxw = static::floatToStr( $ranges[$max] + static::$maxThreshold );
      return [static::distanceFromBase( $baseBreakpoint, $max ), "@media screen and (max-width: {$maxw}px)"];
    }

    if ( is_null( $max ) ) {
      $minw = static::floatToStr( $ranges[$min] );
      return [static::distanceFromBase( $baseBreakpoint, $min ), "@media screen and (min-width: {$minw}px)"];
    }

    $maxw = $minw = static::floatToStr( $ranges[$max] + static::$maxThreshold );
    $minw = $minw = static::floatToStr( $ranges[$min] );
    $direction = $min < $baseBreakpoint ? -1 : 1;
    return [static::distanceFromBase( $baseBreakpoint, $min ) + 0.5 * $direction,"@media screen and (min-width: {$minw}px) and (max-width: {$maxw}px)"];

  }

  // prevent other locale number formats from effecting CSS output
  public static function floatToStr( $float ) {
    $locale = localeconv();
    $string = strval( $float );
    $string = str_replace( $locale['decimal_point'], '.', $string );
    return $string;
  }

  public static function getMinMaxBreakpoint(
    $bpData,
    $currentBreakpoint,
    $baseBreakpoint,
    $ranges,
    $numRanges = null
  ) {
    $numRanges = empty($numRanges)
      ? count($ranges)
      : $numRanges;

    $indexPlusOne = $currentBreakpoint + 1;
    $minBreakpoint = $currentBreakpoint === 0
      ? null
      : $currentBreakpoint;
    $maxBreakpoint = $indexPlusOne === $numRanges
      ? null
      : $indexPlusOne;

    $minBreakpoint = self::getClosestNonEmptyBreakpointValue($bpData, $currentBreakpoint, $baseBreakpoint, $ranges, $numRanges, false);
    $maxBreakpoint = self::getClosestNonEmptyBreakpointValue($bpData, $currentBreakpoint, $baseBreakpoint, $ranges, $numRanges, true);

    return [$minBreakpoint, $maxBreakpoint];
  }

  private static function getClosestNonEmptyBreakpointValue(
    $bpData,
    $currentBreakpoint,
    $baseBreakpoint,
    $ranges,
    $numRanges,
    $direction = false
  ) {
    if (
      ( $direction && ($currentBreakpoint + 1 ) === $numRanges )
      || ( !$direction && ($currentBreakpoint) === 0 )
    ) {
      return null;
    }

    do {
      $currentBreakpoint += (
        $direction
        ? 1
        : -1
      );

      if (
        !empty($bpData[$currentBreakpoint])
        || $currentBreakpoint === $baseBreakpoint
      ) {
        return $direction
          ? $currentBreakpoint
          : $currentBreakpoint + 1;
      }
    } while (
      $currentBreakpoint !== 0
      && ($currentBreakpoint) !== $numRanges
    );

    return null;
  }
}
