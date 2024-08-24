<?php

add_filter('cornerstone_app_choices_gravityforms', function() {

  $forms = RGFormsModel::get_forms( null, 'title' );
  $result = [];

  foreach ($forms as $form) {
    $result[] = array( 'value' => $form->id, 'label' => $form->title );
  }

  return $result;

});


add_action( 'cornerstone_before_custom_endpoint', function() {
  add_filter( 'gform_disable_print_form_scripts', '__return_true' );
});