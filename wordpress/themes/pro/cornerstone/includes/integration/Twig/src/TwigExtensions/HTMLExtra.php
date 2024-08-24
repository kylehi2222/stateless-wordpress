<?php

use Twig\Extra\Html\HtmlExtension;

add_action('cs_twig_boot', function($twig) {
  $twig->addExtension(new HtmlExtension());
});
