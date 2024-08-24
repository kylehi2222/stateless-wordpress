<?php

// Simple cache integration

// Remove TMP directory for API
add_action('cs_purge_all', function() {
  cs_delete_directory(cs_api_cache_directory());
});
