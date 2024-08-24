<?php

// Extension settings
add_filter("cs_extension_settings", function($settings) {
  return array_merge($settings, [
    'cs_api_extension_enabled' => get_option("cs_api_extension_enabled", false),
    'cs_api_extension_allowlist' => get_option("cs_api_extension_allowlist", ''),
  ]);
});

add_filter("cs_api_allowlist", function($settings) {
  $allowlist = get_option("cs_api_extension_allowlist");
  if (empty($allowlist)) {
    return $settings;
  }

  $settings = array_merge($settings, explode("\n", $allowlist));

  return $settings;
}, 0, 1);

// Not enabled
if (!get_option("cs_api_extension_enabled", false)) {
  return;
}

require_once(__DIR__ . "/functions.php");
require_once(__DIR__ . "/Definitions.php");
require_once(__DIR__ . "/helpers.php");
require_once(__DIR__ . "/ApiControls.php");
require_once(__DIR__ . "/ApiDynamicContent.php");
require_once(__DIR__ . "/ApiExtension.php");
require_once(__DIR__ . '/ApiAttributes.php');
require_once(__DIR__ . "/ApiJSON.php");
require_once(__DIR__ . "/ApiThemeOptions.php");
require_once(__DIR__ . "/ApiFileReturn.php");
require_once(__DIR__ . "/ApiLooper.php");
require_once(__DIR__ . "/ApiGlobalLooper.php");
require_once(__DIR__ . "/ApiTesterPrefab.php");
require_once(__DIR__ . "/Cache.php");

// Notices
require_once(__DIR__ . '/AdminNotices.php');

// XML Load
if (function_exists("xml_parser_create")) {
  require_once(__DIR__ . "/ApiXML.php");
}

// CSV Load
if (get_option("cs_csv_enabled", true)) {
  require_once(__DIR__ . "/ApiCSV.php");
}

// GraphQL Load
if (get_option("cs_api_graphql_enabled", true)) {
  require_once(__DIR__ . "/ApiGraphQL.php");
}

// YAML Load
if (function_exists("yaml_parse")) {
  require_once(__DIR__ . "/ApiYaml.php");
}

// Loaded last as its more advanced
require_once(__DIR__ . "/ApiRaw.php");

// Register prefabs
add_action("cs_register_elements", function() {
  require_once(__DIR__ . '/Prefabs/RSSFeed.php');
});



