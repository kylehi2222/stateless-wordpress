<?php


// Dynamic Options API Global

use Cornerstone\TwigIntegration\Renderer;

cs_dynamic_content_register_dynamic_option('twig_filters', [
  'key' => 'twig_filters',
  'type' => 'select',
  'label' => __('Twig Filters', CS_LOCALIZE),
  'options' => [
    'choices' => 'dynamic:twig_filters',
    'placeholder' => __('Select a Filter', CS_LOCALIZE),
  ],
  'filter' => function() {
    $twig = Renderer::twigInstance();

    // These are keyed as 'name' => Class
    // We only need name
    $filters = $twig->getFilters();
    $filters = array_keys($filters);
    sort($filters);

    // Return as control select choices
    return cs_array_as_choices($filters);
  },
]);
