<?php

/**
 * Twig Dynamic content UI
 */

// Register group
cornerstone_dynamic_content_register_group([
  'name'  => 'twig',
  'label' => __( 'Twig', CS_LOCALIZE ),
]);

// Fields
require_once(__DIR__ . '/Blocks.php');
