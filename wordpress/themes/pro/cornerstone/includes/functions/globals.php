<?php

/**
 * Get all colors from global colors
 * @return array
 */
function cs_color_get_all() {
  return cornerstone("GlobalColors")->getAllColorItems();
}
