<?php

// Dynamic Options twig_templates

cs_dynamic_content_register_dynamic_option('twig_templates', [
  'key' => 'twig_templates',
  'type' => 'select',
  'label' => __('Twig Templates', CS_LOCALIZE),
  'options' => [
    'choices' => 'dynamic:twig_templates',
    'placeholder' => __('Select a Function', CS_LOCALIZE),
  ],
  'filter' => function() {
    // Loop templates and build choices
    $templates = cs_twig_templates();

    $out = [];
    foreach ($templates as $template) {
      $out[] = [
        'label' => $template['title'],
        'value' => 'cs-template:' . $template['id'],
      ];
    }

    // Directories templates setup
    $directories = cs_twig_directory_templates();

    // Get recursive lists and append
    foreach ($directories as $directory) {
      $files = cs_directory_get_file_list($directory);

      $out = array_merge($out, cs_array_as_choices($files));
    }

    return $out;
  },
]);

