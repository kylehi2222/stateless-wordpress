<?php

// MySQL Configuration
define( 'DB_NAME',     'hdai_db' );
define( 'DB_USER',     'hdaiadmin' );
define( 'DB_PASSWORD', 'SuperAbundance@888' );
define( 'DB_HOST',     'hdai-db.mysql.database.azure.com' );
define( 'DB_CHARSET',  'utf8' );
define( 'DB_COLLATE',  '' );
$table_prefix = 'wp_6h';

// Enable SSL for MySQL
define('MYSQL_CLIENT_FLAGS', MYSQLI_CLIENT_SSL);
define('MYSQL_SSL_CA', '/etc/ssl/certs/DigiCertGlobalRootCA.crt.pem');

// Authentication Unique Keys and Salts
define( 'AUTH_KEY',         $_ENV['WP_AUTH_KEY']);
define( 'SECURE_AUTH_KEY',  $_ENV['WP_SECURE_AUTH_KEY']);
define( 'LOGGED_IN_KEY',    $_ENV['WP_LOGGED_IN_KEY']);
define( 'NONCE_KEY',        $_ENV['WP_NONCE_KEY']);
define( 'AUTH_SALT',        $_ENV['WP_AUTH_SALT']);
define( 'SECURE_AUTH_SALT', $_ENV['WP_SECURE_AUTH_SALT']);
define( 'LOGGED_IN_SALT',   $_ENV['WP_LOGGED_IN_SALT']);
define( 'NONCE_SALT',       $_ENV['WP_NONCE_SALT']);

// Debug
define( 'WP_DEBUG', false );

// Stateless
define( 'DISALLOW_FILE_MODS', true );
define( 'AUTOMATIC_UPDATER_DISABLED', true );
define( 'WP_AUTO_UPDATE_CORE', false );

// Reverse Proxy
if ( strpos( $_SERVER['HTTP_X_FORWARDED_PROTO'], 'https' ) !== false ) {
    $_SERVER['HTTPS'] = 'on';
}

if ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
    $http_x_headers = explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] );
    $_SERVER['REMOTE_ADDR'] = $http_x_headers[0];
}

// Absolute path to the WordPress directory
if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

// Sets up WordPress vars and included files
require_once( ABSPATH . 'wp-settings.php' );
