<?php

use Twig\Extra\String\StringExtension;

add_action('cs_twig_boot', function($twig) {
  $twig->addExtension(new StringExtension());
});
