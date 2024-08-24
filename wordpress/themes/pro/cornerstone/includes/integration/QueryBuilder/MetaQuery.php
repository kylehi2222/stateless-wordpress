<?php

namespace Cornerstone\QueryBuilder\Meta;

/**
 * Meta query integration
 * uses list editor meta_values
 */
const META_KEY = 'looper_provider_query-builder_meta_values';
const META_RELATION_KEY = 'looper_provider_query-builder_meta_relation';

add_filter("cs_looper_provider_query_args", function($config, $element = []) {

  // Check is query builder
  $type = cs_get_array_value($element, 'looper_provider_type', '');

  if ($type !== 'query-builder') {
    return $config;
  }

  // Grab looper provider values values
  $metaValues = cs_get_array_value($element, META_KEY, []);
  $metaValues = cs_dynamic_content_object($metaValues);

  // No metavalues setup
  if (empty($metaValues)) {
    return $config;
  }

  $currentMetaQuery = cs_get_array_value($config, 'meta_query', []);

  $metaValues = cs_maybe_json_decode($metaValues);

  // Built value
  $orderBys = [];
  $metaQuery = [];

  foreach ($metaValues as $values) {
    $id = cs_get_array_value($values, 'id', 0);

    // Add to built named queries
    $metaQuery[$id] = [
      'key' => $values['key'],
      'value' => $values['value'],
      'compare' => $values['compare'],
    ];

    // No orderby
    if (empty($values['orderby'])) {
      continue;
    }

    $orderBys[$id] = cs_get_array_value($values, 'orderby_direction', 'DESC');
  }

  // Relation
  $metaQuery['relation'] = cs_get_array_value($element, META_RELATION_KEY, "AND");
  $metaQuery['relation'] = cs_dynamic_content($metaQuery['relation']);

  // Only expecting one order by
  // convert to multi
  if (isset($config['orderby']) && !is_array($config['orderby'])) {
    $config['orderby'] = [
      $config['orderby'] => cs_get_array_value($config, 'order', 'DESC'),
    ];

    unset($config['order']);
  }

  // No order by at all
  if (empty($config['orderby'])) {
    $config['orderby'] = [];
  }

  // Merge originals with new additions
  $config['orderby'] = array_merge($config['orderby'], $orderBys);

  $config['meta_query'] = array_merge($currentMetaQuery, $metaQuery);

  return $config;
}, 10, 2);
