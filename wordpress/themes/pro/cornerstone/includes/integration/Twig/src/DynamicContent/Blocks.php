<?php

namespace Cornerstone\Twig\DyanmicContent;

// For Loop field to extend from
$FOR_LOOP_FIELD = [
  'name'  => 'for-loop-array',
  'group' => 'twig',
  'type' => 'scalar',
  'label' => __( 'For Loop (Array)', CS_LOCALIZE ),
  'deep' => true,
  'controls' => [
    [
      'label' => __('Array', CS_LOCALIZE),
      'key' => 'array',
      'type' => 'text',
      'options' => [
        'placeholder' => __('0..10', CS_LOCALIZE),
      ],
    ]
  ],
  'format' => "{% for item in \$array %}\n\t{{item}}\n{% endfor %}",
];

// If block
cornerstone_dynamic_content_register_field([
  'name'  => 'if-block',
  'group' => 'twig',
  'type' => 'scalar',
  'label' => __( 'If Block', CS_LOCALIZE ),
  'deep' => true,
  'controls' => [
    [
      'label' => __('Condition', CS_LOCALIZE),
      'key' => 'condition',
      'type' => 'text',
      'options' => [
        'placeholder' => __('post.status == \'publish\'', CS_LOCALIZE),
      ],
    ]
  ],
  'format' => "{% if \$condition %}\n\t{# content here #}\n{% endif %}",
]);


// If Tests
cornerstone_dynamic_content_register_field([
  'name'  => 'if-block-tests',
  'group' => 'twig',
  'type' => 'scalar',
  'label' => __( 'If Block (Test)', CS_LOCALIZE ),
  'deep' => true,
  'controls' => [
    // Condition
    [
      'label' => __('Condition', CS_LOCALIZE),
      'key' => 'condition',
      'type' => 'text',
      'options' => [
        'placeholder' => __('post.id', CS_LOCALIZE),
      ],
    ],

    // Tests
    [
      'label' => __('Test', CS_LOCALIZE),
      'key' => 'test',
      'type' => 'select',
      'options' => [
        'choices' => cs_array_as_choices([
          'defined', 'empty',
          'odd',  'even',
          'iterable', 'null', 'same as(false)',
          'constant(\'YOUR_CONSTANT\')',
        ]),
      ],
    ],

  ],
  'format' => "{% if \$condition is \$test %}\n\t{# content here #}\n{% endif %}",
]);


// Math
cornerstone_dynamic_content_register_field([
  'name'  => 'match-basic',
  'group' => 'twig',
  'type' => 'scalar',
  'label' => __( 'Math', CS_LOCALIZE ),
  'deep' => true,
  'controls' => [
    // X
    [
      'label' => __('X', CS_LOCALIZE),
      'key' => 'x',
      'type' => 'text',
      'options' => [
        'placeholder' => __('10', CS_LOCALIZE),
      ],
    ],

    // Operator
    [
      'label' => __('Operator', CS_LOCALIZE),
      'key' => 'operator',
      'type' => 'select',
      'options' => [
        'choices' => cs_array_as_choices([
          '+', '-', '/',
          '%', '//', '*',
          '**',
        ]),
      ],
    ],

    // Y
    [
      'label' => __('Y', CS_LOCALIZE),
      'key' => 'y',
      'type' => 'text',
      'options' => [
        'placeholder' => __('10', CS_LOCALIZE),
      ],
    ],
  ],
  'format' => '{{ $x $operator $y }}',
]);

// Filter
cornerstone_dynamic_content_register_field([
  'name'  => 'filter-basic',
  'group' => 'twig',
  'type' => 'scalar',
  'label' => __( 'Filter', CS_LOCALIZE ),
  'deep' => true,
  'controls' => [
    // Value
    [
      'label' => __('Value', CS_LOCALIZE),
      'key' => 'value',
      'type' => 'text',
      'options' => [
        'placeholder' => __('my_variable_name', CS_LOCALIZE),
      ],
    ],

    // Filter
    [
      'label' => __('Filter', CS_LOCALIZE),
      'key' => 'filter',
      'type' => 'select',
      'options' => [
        'placeholder' => __('[ 0, true, \'value\', {} ]', CS_LOCALIZE),
        'choices' => 'dynamic:twig_filters',
      ],
    ],

    // Args
    [
      'label' => __('Arguments', CS_LOCALIZE),
      'key' => 'args',
      'type' => 'text',
      'options' => [
        'placeholder' => __('2, \'.\', \',\'', CS_LOCALIZE),
      ],
    ],

  ],
  'format' => '{{ $value | $filter($args) }}',
]);

// Functions
cornerstone_dynamic_content_register_field([
  'name'  => 'function-basic',
  'group' => 'twig',
  'type' => 'scalar',
  'label' => __( 'Function', CS_LOCALIZE ),
  'deep' => true,
  'controls' => [
    // Filter
    [
      'label' => __('Function', CS_LOCALIZE),
      'key' => 'function',
      'type' => 'select',
      'options' => [
        'placeholder' => __('[ 0, true, \'value\', {} ]', CS_LOCALIZE),
        'choices' => 'dynamic:twig_functions',
      ],
    ],

    // Args
    [
      'label' => __('Value', CS_LOCALIZE),
      'key' => 'args',
      'type' => 'text',
      'options' => [
        'placeholder' => __('2, \'.\', \',\'', CS_LOCALIZE),
      ],
    ],

  ],
  'format' => '{{ $function($args) }}',
]);

// Set block
cornerstone_dynamic_content_register_field([
  'name'  => 'set-block',
  'group' => 'twig',
  'type' => 'scalar',
  'label' => __( 'Set Statement', CS_LOCALIZE ),
  'deep' => true,
  'controls' => [
    // Key
    [
      'label' => __('Key', CS_LOCALIZE),
      'key' => 'key',
      'type' => 'text',
      'options' => [
        'placeholder' => __('my_variable_name', CS_LOCALIZE),
      ],
    ],

    // Value
    [
      'label' => __('Value', CS_LOCALIZE),
      'key' => 'value',
      'type' => 'text',
      'options' => [
        'placeholder' => __('[ 0, 2, \'value\' ]', CS_LOCALIZE),
      ],
    ],

  ],
  'format' => "{% set \$key = \$value %}",
]);

// For Loop Array
cornerstone_dynamic_content_register_field($FOR_LOOP_FIELD);

// For Loop Keys
cornerstone_dynamic_content_register_field(array_merge(
  $FOR_LOOP_FIELD,
  [
    'name' => 'for-loop-array-keys',
    'label' => __( 'For Loop (Keys)', CS_LOCALIZE ),
    'format' => "{% for item in \$array|keys %}\n\t{{item}}\n{% endfor %}",
  ],
));


// Range Numbers
cornerstone_dynamic_content_register_field([
  'name'  => 'range-numbers',
  'group' => 'twig',
  'type' => 'scalar',
  'label' => __( 'Range (Numbers)', CS_LOCALIZE ),
  'deep' => true,
  'controls' => [
    // Start
    [
      'label' => __('Start', CS_LOCALIZE),
      'key' => 'start',
      'type' => 'text',
      'options' => [
        'placeholder' => __('0', CS_LOCALIZE),
      ],
    ],

    // End
    [
      'label' => __('End', CS_LOCALIZE),
      'key' => 'end',
      'type' => 'text',
      'options' => [
        'placeholder' => __('10', CS_LOCALIZE),
      ],
    ]
  ],
  'format' => "{% for item in \$start..\$end %}\n\t{{item}}\n{% endfor %}",
]);

// Macro
cornerstone_dynamic_content_register_field([
  'name'  => 'macro-create',
  'group' => 'twig',
  'type' => 'scalar',
  'label' => __( 'Macro (Create)', CS_LOCALIZE ),
  'deep' => true,
  'controls' => [
    // Name
    [
      'label' => __('Name', CS_LOCALIZE),
      'key' => 'name',
      'type' => 'text',
      'options' => [
        'placeholder' => __('my_macro_name', CS_LOCALIZE),
      ],
    ],
  ],
  'format' => "{% macro \$name(name = 'Fallback') %}\n\t{{name}}\n{% endmacro %}",
]);

// Macro Import
cornerstone_dynamic_content_register_field([
  'name'  => 'macro-import',
  'group' => 'twig',
  'type' => 'scalar',
  'label' => __( 'Macro (Import)', CS_LOCALIZE ),
  'deep' => true,
  'controls' => [
    // Template
    [
      'label' => __('Template', CS_LOCALIZE),
      'key' => 'template',
      'type' => 'select',
      'options' => [
        'placeholder' => __('Select a Template', CS_LOCALIZE),
        'choices' => 'dynamic:twig_templates',
      ],
    ],

    // Macro Name
    [
      'label' => __('Name', CS_LOCALIZE),
      'key' => 'name',
      'type' => 'text',
    ],
  ],
  'format' => '{% import \'$template\' as $name %}',
]);


// Template
cornerstone_dynamic_content_register_field([
  'name'  => 'template-include',
  'group' => 'twig',
  'type' => 'scalar',
  'label' => __( 'Template (Include)', CS_LOCALIZE ),
  'deep' => true,
  'controls' => [
    // Template
    [
      'label' => __('Template', CS_LOCALIZE),
      'key' => 'template',
      'type' => 'select',
      'options' => [
        'placeholder' => __('[ 0, true, \'value\', {} ]', CS_LOCALIZE),
        'choices' => 'dynamic:twig_templates',
      ],
    ],

  ],
  'format' => '{% include \'$template\' %}',
]);

// Template Extends
cornerstone_dynamic_content_register_field([
  'name'  => 'template-extends',
  'group' => 'twig',
  'type' => 'scalar',
  'label' => __( 'Template (Extends)', CS_LOCALIZE ),
  'deep' => true,
  'controls' => [
    // Template
    [
      'label' => __('Template', CS_LOCALIZE),
      'key' => 'template',
      'type' => 'select',
      'options' => [
        'placeholder' => __('Template', CS_LOCALIZE),
        'choices' => 'dynamic:twig_templates',
      ],
    ],

  ],
  'format' => '{% extends \'$template\' %}',
]);

// Block
cornerstone_dynamic_content_register_field([
  'name'  => 'block-basic',
  'group' => 'twig',
  'type' => 'scalar',
  'label' => __( 'Block', CS_LOCALIZE ),
  'deep' => true,
  'controls' => [
    // Name
    [
      'label' => __('Name', CS_LOCALIZE),
      'key' => 'name',
      'type' => 'text',
      'options' => [
        'placeholder' => __('my_block_name', CS_LOCALIZE),
      ],
    ],
  ],
  'format' => "{% block \$name %}\n{% endblock %}",
]);


// Apply
cornerstone_dynamic_content_register_field([
  'name'  => 'apply-block',
  'group' => 'twig',
  'type' => 'scalar',
  'label' => __( 'Apply', CS_LOCALIZE ),
  'deep' => true,
  'controls' => [
    // Filter
    [
      'label' => __('Filter', CS_LOCALIZE),
      'key' => 'filter',
      'type' => 'select',
      'options' => [
        'placeholder' => __('upper', CS_LOCALIZE),
        'choices' => 'dynamic:twig_filters',
      ],
    ],

  ],
  'format' => "{% apply \$filter %}\n\t{# content here #}\n{% endapply %}",
]);
