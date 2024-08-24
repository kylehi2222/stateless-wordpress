<?php

namespace Cornerstone\API\FileReturn;


// Return type File Return
cs_api_register_return_type("file-return", [
  'label' => __("File Return", "cornerstone"),
  'controls' => [

    // File Name
    [
      'key' => 'return_file_name',
      'label' => __("File Name", "cornerstone"),
      'description' => __("In the uploads directory, what is the name to return on the frontend", "cornerstone"),
      'type' => 'text',
    ],

    // Local File
    [
      'key' => 'return_file_local',
      'label' => __("Local File", "cornerstone"),
      'description' => __("Return a local file path as your server would use it internally, or return a URL an end user could use. Both are placed in the uploads directory", "cornerstone"),
      'type' => 'toggle',
    ],

  ],

  'values' => [
    'return_file_name' => 'endpoint-results.mp3',
    'return_file_local' => false,
  ],

  // Filter request prior to sending
  'filter' => function($body, $type, $data) {
    $returnFileName = cs_get_array_value($data, 'return_file_name', '');
    $localFile = (bool)cs_get_array_value($data, 'return_file_local', false);

    // Checks
    if (!$returnFileName) {
      return [
        'errors' => __("No return file passed", "cornerstone"),
      ];
    }

    $target_dir = wp_upload_dir();


    // Upload directory and local file saving to uploads
    $customUploadDirectory = upload_directory_name();

    $pathToUpload = upload_directory();
    $pathToUpload .= $returnFileName;


    // Send local file or from upload directory
    $outFile = $localFile
      ? $pathToUpload
      : $target_dir['baseurl'] . "/$customUploadDirectory/" . $returnFileName;

    // Cache good
    if (cs_api_file_passes_cache($pathToUpload, $data)) {
      return $outFile;
    }

    // Check if directory exists if path has directory
    $base = dirname($pathToUpload);
    wp_mkdir_p($base);

    // Put file contents
    $body = is_array($body)
      ? json_encode($body)
      : $body;
    file_put_contents($pathToUpload, $body);

    return $outFile;
  },
]);

// Directory name
// no trailing or starting slashes
function upload_directory_name() {
  return apply_filters(
    "cs_api_file_return_upload_directory",
    'cornerstone/external-api'
  );
}

// Get API uploads directory
function upload_directory() {
  $target_dir = wp_upload_dir();

  // Cache directory
  $customUploadDirectory = upload_directory_name();
  $ds = DIRECTORY_SEPARATOR;

  // Upload path
  $pathToUpload = $target_dir['basedir']
    . $ds . str_replace("/", $ds, $customUploadDirectory)
    . $ds;

  return $pathToUpload;
}

// Remove upload directory on purge_all
add_action('cs_purge_all', function() {
  // File system directory
  cs_delete_directory(upload_directory());
});
