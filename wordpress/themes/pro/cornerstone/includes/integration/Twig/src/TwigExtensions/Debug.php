<?php

use Twig\Extension\DebugExtension;

add_action('cs_twig_boot', function($twig) {
  $twig->addExtension(new DebugExtension());
});
