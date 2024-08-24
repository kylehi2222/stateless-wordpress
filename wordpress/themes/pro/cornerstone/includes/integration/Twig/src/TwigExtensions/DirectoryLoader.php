<?php

use Twig\Loader\FilesystemLoader;


/**
 * Add FilesystemLoader from directories
 */
add_filter('cs_twig_loaders', function($loaders) {
  // Get directories
  $directories = cs_twig_directory_templates();

  // None
  if (empty($directories)) {
    return $loaders;
  }

  // Validate directories, twig will fatal if this does not happen
  foreach ($directories as $index => $directory) {
    // Does not exist
    if (!file_exists($directory)) {
      unset($directories[$index]);
      trigger_error('This twig template directory does not exist : ' . $directory);
    }
  }

  $loaders[] = new FilesystemLoader($directories);

  return $loaders;
});

/**
 * Child theme add /twig directory
 */
add_filter('cs_twig_directory_templates', function($directories) {
  // Default child theme
  if (is_child_theme()) {
    $defaultTwigDirectory = get_stylesheet_directory() . '/twig';

    // Has twig directory
    if (file_exists($defaultTwigDirectory)) {
      $directories[] = $defaultTwigDirectory;
    }
  }

  return $directories;
});
