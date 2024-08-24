<?php

namespace Themeco\Cornerstone\Services;

use Themeco\Cornerstone\Plugin;


/** Original CSS output

$breakSmall:          480px  !default;
$breakMedium:         767px  !default;
$breakMediumLarge:    979px  !default; // (+1 = default desktop)
$breakLarge:          1200px !default;

@media (min-width: $breakLarge) {
  .x-hide-xl { display: none !important; }
}

@media (min-width: $breakMediumLarge) and (max-width: $breakLarge - .02) {
  .x-hide-lg { display: none !important; }
}

@media (min-width: $breakMedium) and (max-width: $breakMediumLarge - .02) {
  .x-hide-md { display: none !important; }
}

@media (min-width: $breakSmall) and (max-width: $breakMedium - .02) {
  .x-hide-sm { display: none !important; }
}

@media (max-width: $breakSmall - .02) {
  .x-hide-xs { display: none !important; }
}


@media (min-width: 1000px) { .x-hide-xl { display: none !important; } }
@media (min-width: 500px) and (max-width: 1000px - .02) { .x-hide-md { display: none !important; } }
@media (max-width: 500px - .02) { .x-hide-xs { display: none !important; } }

*/

class Breakpoints implements Service {

  protected $ranges;

  public function hideClassPrefix() {
    return apply_filters( 'cs_hide_class_prefix', 'x-hide-' );
  }
  public function hideClass($tag) {
    return $this->hideClassPrefix() . $tag;
  }

  public function hideClassOutput() {

    $offset = 1;
    ob_start();
    $info = $this->activeBreakpointRangeData();
    list($tag, $val) = array_pop($info);
    $class = $this->hideClass($tag);
    echo "@media (min-width: {$val}px) { .{$class} { display: none !important; } }\n";

    while (count($info) > 1) {
      $max = $val - $offset;
      list($tag, $val) = array_pop($info);
      $class = $this->hideClass($tag);
      echo "@media (min-width: {$val}px) and (max-width: {$max}px) { .{$class} { display: none !important; } }\n";
    }

    $max = $val - $offset;
    list($tag, $val) = array_pop($info);
    $class = $this->hideClass($tag);
    echo "@media (max-width: {$max}px) { .{$class} { display: none !important; } }";

    return ob_get_clean();
  }

  public function breakpointConfig() {
    $ranges = $this->breakpointRanges();
    return [
      $this->breakpointBase(),
      array_merge([0], $ranges), // px range values
      count($ranges)
    ];
  }

  public function breakpointConfigObject() {
    $config = $this->breakpointConfig();

    return [
      'base' => $config[0],
      'ranges' => $config[1],
      'count' => $config[2],
    ];
  }

  public function breakpointBase() {
    $ranges = $this->breakpointRanges();
    return (int) apply_filters( 'cs_breakpoint_base', count( $ranges ) );
  }

  public function breakpointRanges() {
    if ( ! isset( $this->ranges ) ) {
      $this->ranges = array_map('intval', apply_filters( 'cs_breakpoint_ranges', array_slice( $this->defaultRanges(), 0, 2) ));
    }
    return $this->ranges;
  }

  public function activeBreakpointRanges() {
    $ranges = $this->breakpointRanges();
    return $this->rangeInfo()[count($ranges) - 1];
  }

  public function activeBreakpointRangeData() {
    $index = 0;
    $ranges = $this->breakpointRanges();
    array_unshift($ranges,0);
    return array_map( function( $item) use (&$index, $ranges) {
      return [$item['tag'], $ranges[$index++]];
    }, $this->activeBreakpointRanges());
  }

  public function activeBreakpointRangeKeys() {
    return array_map( function( $item ) {
      return $item['tag'];
    }, $this->activeBreakpointRanges());
  }

  public function defaultRanges() {
    return apply_filters( 'cs_breakpoint_default_ranges', [ 500, 1000 ] );
  }

  public function rangeInfo() {
    $sizes = $this->defaultRanges();
    while (count($sizes) < 4) {
      $sizes[] = end($sizes) + 100;
    }
    return [
      [
        [ 'label' => 'Small', 'tag' => 'xs', 'default' => null ],
        [ 'label' => 'Large', 'tag' => 'xl', 'default' => $sizes[0]  ],
      ],
      [
        [ 'label' => 'Small',  'tag' => 'xs', 'default' => null ],
        [ 'label' => 'Medium', 'tag' => 'md', 'default' => $sizes[0]  ],
        [ 'label' => 'Large',  'tag' => 'xl', 'default' => $sizes[2]  ],
      ],
      [
        [ 'label' => 'Extra Small', 'tag' => 'xs', 'default' => null ],
        [ 'label' => 'Small',       'tag' => 'sm', 'default' => $sizes[0] ],
        [ 'label' => 'Medium',      'tag' => 'md', 'default' => $sizes[2] ],
        [ 'label' => 'Large',       'tag' => 'xl', 'default' => $sizes[3] ],
      ],
      [
        [ 'label' => 'Extra Small', 'tag' => 'xs', 'default' => null ],
        [ 'label' => 'Small',       'tag' => 'sm', 'default' => $sizes[0] ],
        [ 'label' => 'Medium',      'tag' => 'md', 'default' => $sizes[1] ],
        [ 'label' => 'Large',       'tag' => 'lg', 'default' => $sizes[2] ],
        [ 'label' => 'Extra Large', 'tag' => 'xl', 'default' => $sizes[3] ],
      ]
    ];
  }

  public function appData() {
    return [
      'config' => $this->breakpointConfig(),
      'controlRanges' => array_merge([0], $this->defaultRanges() ),
      'rangeInfo' => $this->rangeInfo(),
      'hideClassPrefix' => $this->hideClassPrefix(),
      'canChangeRanges' => apply_filters('cs_allow_breakpoint_ranges_change', true),
      'canChangeBase' => apply_filters('cs_allow_breakpoint_base_change', true)
    ];
  }

  /**
   * Sends breakpoint data based on values
   * in range
   * uses min-width
   */
  public function mediaOutputLoop($values = [], $prefix = "", $suffix = "") {
    $ranges = $this->breakpointRanges();
    $output = "";

    foreach ($ranges as $index => $range) {
      if (empty($values[$index])) {
        continue;
      }

      $rangeOutput = $range - 1;

      $output .= " @media (min-width: {$rangeOutput}px) {
      {$prefix} {$values[$index]} {$suffix}
      } ";
    }

    return $output;
  }
}
