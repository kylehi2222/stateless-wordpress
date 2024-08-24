<?php

// Cache deletion for twig
add_action('cs_purge_all', function() {
  cs_delete_directory(cs_twig_cache_directory());
});
