<?php

/**
 * Get recursive list of files in a directory
 * No trailing slash
 *
 * @param string $dir
 *
 * @return array
 */
function cs_directory_get_file_list($dir) {
  // Directory does not exist
  if (!file_exists($dir)) {
    return [];
  }

  $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
  $files = [];

  /** @var SplFileInfo $file */
  foreach ($rii as $file) {
    if ($file->isDir()){
      continue;
    }

    // Only path
    $files[] = str_replace($dir . '/', '', $file->getPathname());
  }

  return $files;
}
