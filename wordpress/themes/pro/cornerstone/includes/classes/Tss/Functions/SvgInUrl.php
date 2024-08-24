<?php

namespace Themeco\Cornerstone\Tss\Functions;

class SvgInUrl extends BuiltInFunction {

  public function run( $the_slug, $the_fill ) {

    $icons = [
      'arrow-down'        => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><title>arrow-down</title><g fill="__fill__"><polygon points="14.7,9.3 13.3,7.9 9,12.2 9,0 7,0 7,12.2 2.7,7.9 1.3,9.3 8,16 "></polygon></g></svg>',
      'arrowhead-down'    => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><title>down-arrow</title><g fill="__fill__"><polygon points="8,12.6 0.3,4.9 1.7,3.4 8,9.7 14.3,3.4 15.7,4.9 "></polygon></g></svg>',
      'calendar'          => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><title>calendar-2</title><g fill="__fill__"><path d="M14,3H13V1a1,1,0,0,0-2,0V3H5V1A1,1,0,0,0,3,1V3H2A2,2,0,0,0,0,5v9a2,2,0,0,0,2,2H14a2,2,0,0,0,2-2V5A2,2,0,0,0,14,3ZM2,14V7H14v7Z"></path></g></svg>',
      'calendar-confirm'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><title>event-confirm</title><g fill="__fill__"><path d="M11,16a1,1,0,0,1-.707-.293l-2-2,1.414-1.414L11,13.586l3.293-3.293,1.414,1.414-4,4A1,1,0,0,1,11,16Z"></path> <path d="M7,14H2V5H14V9h2V3a1,1,0,0,0-1-1H13V0H11V2H9V0H7V2H5V0H3V2H1A1,1,0,0,0,0,3V15a1,1,0,0,0,1,1H7Z"></path></g></svg>',
      'check'             => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><title>o-check</title><g fill="__fill__"><polygon points="5.6,8.4 1.6,6 0,7.6 5.6,14 16,3.6 14.4,2 "></polygon></g></svg>',
      'close-thin'        => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><title>e-remove</title><g fill="__fill__"><path d="M14.7,1.3c-0.4-0.4-1-0.4-1.4,0L8,6.6L2.7,1.3c-0.4-0.4-1-0.4-1.4,0s-0.4,1,0,1.4L6.6,8l-5.3,5.3 c-0.4,0.4-0.4,1,0,1.4C1.5,14.9,1.7,15,2,15s0.5-0.1,0.7-0.3L8,9.4l5.3,5.3c0.2,0.2,0.5,0.3,0.7,0.3s0.5-0.1,0.7-0.3 c0.4-0.4,0.4-1,0-1.4L9.4,8l5.3-5.3C15.1,2.3,15.1,1.7,14.7,1.3z"></path></g></svg>',
      'close-thick'       => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><title>d-remove</title><g fill="__fill__"><polygon points="12.2,0.9 8,5.2 3.8,0.9 0.9,3.8 5.2,8 0.9,12.2 3.8,15.1 8,10.8 12.2,15.1 15.1,12.2 10.8,8 15.1,3.8 "></polygon></g></svg>',
      'close-circle'      => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><title>c-remove</title><g fill="__fill__"><path d="M8,0C3.6,0,0,3.6,0,8s3.6,8,8,8s8-3.6,8-8S12.4,0,8,0z M11.5,10.1l-1.4,1.4L8,9.4l-2.1,2.1l-1.4-1.4L6.6,8 L4.5,5.9l1.4-1.4L8,6.6l2.1-2.1l1.4,1.4L9.4,8L11.5,10.1z"></path></g></svg>',
      'close-square'      => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><title>s-remove</title><g fill="__fill__"><path d="M15,0H1C0.4,0,0,0.4,0,1v14c0,0.6,0.4,1,1,1h14c0.6,0,1-0.4,1-1V1C16,0.4,15.6,0,15,0z M11.5,10.1l-1.4,1.4 L8,9.4l-2.1,2.1l-1.4-1.4L6.6,8L4.5,5.9l1.4-1.4L8,6.6l2.1-2.1l1.4,1.4L9.4,8L11.5,10.1z"></path></g></svg>',
      'ellipsis'          => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><title>menu-dots</title><g fill="__fill__"><circle cx="8" cy="8" r="2"></circle> <circle cx="2" cy="8" r="2"></circle> <circle cx="14" cy="8" r="2"></circle></g></svg>',
      'radio'             => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><title>shape-oval</title><g fill="__fill__"><circle cx="8" cy="8" r="8"></circle></g></svg>',
      'select-arrowheads' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><title>select-84</title><g fill="__fill__"><path d="M7.4,1.2l-5,4l1.2,1.6L8,3.3l4.4,3.5l1.2-1.6l-5-4C8.3,0.9,7.7,0.9,7.4,1.2z"></path> <path d="M8,12.7L3.6,9.2l-1.2,1.6l5,4C7.6,14.9,7.8,15,8,15s0.4-0.1,0.6-0.2l5-4l-1.2-1.6L8,12.7z"></path></g></svg>',
      'select-triangles'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><title>select-83</title><g><polygon fill="__fill__" points="2,6 14,6 8,0 "></polygon> <polygon fill="__fill__" points="8,16 14,10 2,10 "></polygon></g></svg>',
      'time-alarm'        => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><title>time-alarm</title><g fill="__fill__"><path d="M12.9,12.5c0.7-1,1.1-2.2,1.1-3.5c0-3.3-2.7-6-6-6S2,5.7,2,9c0,1.3,0.4,2.5,1.1,3.5l-1.8,1.8 c-0.4,0.4-0.4,1,0,1.4C1.5,15.9,1.7,16,2,16s0.5-0.1,0.7-0.3l1.8-1.8C5.5,14.6,6.7,15,8,15s2.5-0.4,3.5-1.1l1.8,1.8 c0.2,0.2,0.5,0.3,0.7,0.3s0.5-0.1,0.7-0.3c0.4-0.4,0.4-1,0-1.4L12.9,12.5z M7,10V6h2v2h2v2H7z"></path> <path d="M11.5,0c-1.1,0-2.1,0.4-2.8,1c3.2,0.3,5.9,2.4,6.9,5.4C15.8,5.8,16,5.2,16,4.5 C16,2,14,0,11.5,0z"></path> <path d="M7.3,1C6.6,0.4,5.6,0,4.5,0C2,0,0,2,0,4.5c0,0.7,0.2,1.3,0.4,1.9C1.5,3.5,4.1,1.3,7.3,1z"></path></g></svg>',
      'time-clock'        => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><title>time-clock</title><g fill="__fill__"><path d="M8,0a8,8,0,1,0,8,8A8.024,8.024,0,0,0,8,0ZM8,14a6,6,0,1,1,6-6A6.018,6.018,0,0,1,8,14Z"></path><path d="M11.5,7H9V4.5a1,1,0,0,0-2,0V8A1,1,0,0,0,8,9h3.5a1,1,0,0,0,0-2Z"></path></g></svg>',
      'time-watch-circle' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><title>watch-2</title><g fill="__fill__"><path d="M14,8c0-1.8-0.8-3.4-2-4.5V1c0-0.6-0.4-1-1-1H5C4.4,0,4,0.4,4,1v2.5C2.8,4.6,2,6.2,2,8s0.8,3.4,2,4.5V15 c0,0.6,0.4,1,1,1h6c0.6,0,1-0.4,1-1v-2.5C13.2,11.4,14,9.8,14,8z M8,12c-2.2,0-4-1.8-4-4s1.8-4,4-4s4,1.8,4,4S10.2,12,8,12z"></path></g></svg>',
      'time-watch-square' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><title>smartwatch</title><g fill="__fill__"><path d="M12,2V1c0-0.6-0.4-1-1-1H5C4.4,0,4,0.4,4,1v1C2.9,2,2,2.9,2,4v8c0,1.1,0.9,2,2,2v1c0,0.6,0.4,1,1,1h6 c0.6,0,1-0.4,1-1v-1c1.1,0,2-0.9,2-2V4C14,2.9,13.1,2,12,2z M4,12V4h8l0,8H4z"></path></g></svg>',
      'triangle-down'     => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><title>triangle-down</title><g fill="__fill__"><path d="M8.001,14c0.326,0,0.632-0.159,0.819-0.427l7-10c0.214-0.305,0.238-0.704,0.068-1.035 C15.715,2.207,15.374,2,15.001,2H0.999C0.626,2,0.285,2.207,0.112,2.538c-0.17,0.331-0.146,0.73,0.068,1.035l7,10 C7.367,13.841,7.673,14,7.999,14C8,14,8,14,8.001,14C8,14,8,14,8.001,14z"></path></g></svg>',
    ];

    $the_encoded_fill = str_replace('#', '%23', $the_fill->toString());
    $the_encoded_svg  = $icons[$the_slug->toString()];
    $the_encoded_svg  = str_replace('<', '%3C', $the_encoded_svg);
    $the_encoded_svg  = str_replace('>', '%3E', $the_encoded_svg);
    $the_encoded_svg  = str_replace('"', "'", $the_encoded_svg);
    $the_encoded_svg  = str_replace('__fill__', $the_encoded_fill, $the_encoded_svg);

    return 'url("data:image/svg+xml,' . $the_encoded_svg . '")';

  }
}