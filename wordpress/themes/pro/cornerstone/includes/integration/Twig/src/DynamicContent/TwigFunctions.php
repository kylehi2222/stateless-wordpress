<?php


// Dynamic Options API Global

use Cornerstone\TwigIntegration\Renderer;

cs_dynamic_content_register_dynamic_option('twig_functions', [
  'key' => 'twig_functions',
  'type' => 'select',
  'label' => __('Twig Functions', CS_LOCALIZE),
  'options' => [
    'choices' => 'dynamic:twig_functions',
    'placeholder' => __('Select a Function', CS_LOCALIZE),
  ],
  'filter' => function() {
    $twig = Renderer::twigInstance();

    // These are keyed as 'name' => Class
    // We only need name
    $functions = $twig->getFunctions();
    $functions = array_keys($functions);
    sort($functions);

    // Return as control select choices
    return cs_array_as_choices($functions);
  },
]);
