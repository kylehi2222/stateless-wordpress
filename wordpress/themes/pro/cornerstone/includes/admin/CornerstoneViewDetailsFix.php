<?php

// Script to remove the "View Details" which conflicts with another Cornerstone plugin
add_action("admin_enqueue_scripts", function($hook) {

  // Not the plugins page
  if ($hook !== "plugins.php") {
    return;
  }

  // Main script
  $script = <<<JAVASCRIPT
// Change view details to redirect to Cornerstone Page
window.addEventListener("load", function() {

  // Plugin details modal link class
  const selected = document.querySelectorAll(".open-plugin-details-modal");

  // Loop all
  for(var i = 0; i < selected.length; ++i) {
    const el = selected[i];

    // not the cornerstone plugin
    if (el.href.indexOf("plugin=cornerstone&") === -1) {
      continue;
    }

    // On click overwrite page
    el.addEventListener("click", function(e) {
      location.href = "https://theme.co/cornerstone";
    });
  }

});

JAVASCRIPT;

  echo "<script>\n{$script}</script>";

});
