<?php

namespace Themeco\Cornerstone\API;

/**
 * HTTP Methods
 */
const METHOD_CHOICES = [
  [
    'value' => 'GET',
    'label' => 'GET',
  ],
  [
    'value' => 'POST',
    'label' => 'POST',
  ],
  [
    'value' => 'PUT',
    'label' => 'PUT',
  ],
  [
    'value' => 'DELETE',
    'label' => 'DELETE',
  ],
  [
    'value' => 'HEAD',
    'label' => 'HEAD',
  ],
  [
    'value' => 'CONNECT',
    'label' => 'CONNECT',
  ],
  [
    'value' => 'OPTIONS',
    'label' => 'OPTIONS',
  ],
  [
    'value' => 'TRACE',
    'label' => 'TRACE',
  ],
  [
    'value' => 'PATCH',
    'label' => 'PATCH',
  ],
];

// Used in conjuctor with request types
// and return type controls
const BUILTIN_VALUES = [
  'run' => true,
  'endpoint' => '',
  'path' => '',
  'method' => 'GET',
  'headers' => '',
  'cache_time' => '',
  'request_type' => 'attributes',
  'args' => '',
  'return_type' => 'json',
  'data_key' => '',
  'timeout' => 7,
  'httpconnect_timeout' => 7,
  'follow_redirect' => true,
  'debug' => false,
];
