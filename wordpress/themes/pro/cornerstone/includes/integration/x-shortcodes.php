<?php

include_once( ABSPATH . '/wp-admin/includes/plugin.php' );
deactivate_plugins( array( 'x-shortcodes/x-shortcodes.php' ) );
remove_action( 'init', 'x_shortcodes_init' );