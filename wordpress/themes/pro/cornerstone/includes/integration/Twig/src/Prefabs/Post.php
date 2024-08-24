<?php

/**
 * Read time
 */
cs_register_prefab_element( 'post', 'post-readtime', [
  'type' => 'text',
  'scope'  => [ 'layout:archive', 'layout:single', 'layout:header', 'layout:footer', ],
  'title' => __('Post Read time', CS_LOCALIZE),

  'values' => [
    '_label' => __('Post Read Time', CS_LOCALIZE),
    'text_content' => 'Read Time: {{ post.the_content | readtime }}',
  ],
]);
