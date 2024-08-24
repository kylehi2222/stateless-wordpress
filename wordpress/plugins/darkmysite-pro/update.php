<?php

add_filter( 'plugins_api', 'darkmysite_plugin_info', 20, 3);
add_filter( 'site_transient_update_plugins', 'darkmysite_push_update' );



function darkmysite_plugin_info( $res, $action, $args ){
    // do nothing if this is not about getting plugin information
    if( 'plugin_information' !== $action ) {
        return $res;
    }
    // do nothing if it is not our plugin
    if( plugin_basename( __DIR__ ) !== $args->slug ) {
        return $res;
    }
    $remote = wp_remote_get(
        DARKMYSITE_PRO_SERVER.'/api/license/plugin-api.php',
        array(
            'timeout' => 10,
            'headers' => array(
                'Accept' => 'application/json'
            )
        )
    );
    if(
        is_wp_error( $remote )
        || 200 !== wp_remote_retrieve_response_code( $remote )
        || empty( wp_remote_retrieve_body( $remote ) )
    ) {
        return $res;
    }

    $remote = json_decode( wp_remote_retrieve_body( $remote ) );

    $res = new stdClass();
    $res->name = $remote->name;
    $res->slug = $remote->slug;
    $res->author = $remote->author;
    $res->author_profile = $remote->author_profile;
    $res->version = $remote->version;
    $res->tested = $remote->tested;
    $res->requires = $remote->requires;
    $res->requires_php = $remote->requires_php;
    $res->download_link = $remote->download_url;
    $res->trunk = $remote->download_url;
    $res->last_updated = $remote->last_updated;
    $res->sections = array(
        'description' => $remote->sections->description,
        'installation' => $remote->sections->installation,
        'changelog' => $remote->sections->changelog
    );
    if( ! empty( $remote->sections->screenshots ) ) {
        $res->sections[ 'screenshots' ] = $remote->sections->screenshots;
    }

    $res->banners = array(
        'low' => $remote->banners->low,
        'high' => $remote->banners->high
    );

    return $res;
}


function darkmysite_push_update( $transient ){


    if ( empty( $transient->checked ) ) {
        return $transient;
    }

    $checkUpdate = False;
    $last_checked_time = get_option("darkmysite_last_update_checked", False);
    $last_checked_data = get_option("darkmysite_last_update_checked_data", False);
    if($last_checked_time == False || $last_checked_data == False){
        $checkUpdate = True;
    }else if(time() - $last_checked_time > 60){
        $checkUpdate = True;
    }

    if($checkUpdate){
        $remote = wp_remote_get(
            DARKMYSITE_PRO_SERVER.'/api/license/plugin-api.php',
            array(
                'timeout' => 10,
                'headers' => array(
                    'Accept' => 'application/json'
                )
            )
        );
        if(
            is_wp_error( $remote )
            || 200 !== wp_remote_retrieve_response_code( $remote )
            || empty( wp_remote_retrieve_body( $remote ) )
        ) {
            return $transient;
        }
        $response_body = wp_remote_retrieve_body( $remote );
        update_option("darkmysite_last_update_checked", time());
        update_option("darkmysite_last_update_checked_data", $response_body);
    }else{
        $response_body = get_option("darkmysite_last_update_checked_data", "[]");
    }


    $remote = json_decode( $response_body );

    if($remote && version_compare( DARKMYSITE_PRO_VERSION, $remote->version, '<' )) {

        $res = new stdClass();
        $res->slug = $remote->slug;
        $res->plugin = DARKMYSITE_PRO_BASE_PATH;
        $res->new_version = $remote->version;
        $res->tested = $remote->tested;
        $res->package = $remote->download_url;
        $transient->response[ $res->plugin ] = $res;
    }


    return $transient;

}