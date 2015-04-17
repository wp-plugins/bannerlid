<?php

/**
* Show html page according to $action in the header
*/
switch($action){

    case "options_process":
        //
        // Check the nonce
        //
        check_admin_referer( 'options_process');

        //
        // Sznitize variables and check everything is included that neess to be included. 
        // 
        !empty($_GET['bannerlid-collect-stats']) ? $collect_stats = 'true' : $collect_stats = null;
        !empty($_GET['bannerlid-enable-flash']) ? $enable_flash = 'true' : $enable_flash = null;
        
        if(is_null($collect_stats)){
            delete_option( 'bannerlid-collect-stats' );
        } else {
            update_option( 'bannerlid-collect-stats', 'true' );
        }

        if(is_null($enable_flash)){
            delete_option( 'bannerlid-enable-flash' );
        } else {
            update_option( 'bannerlid-enable-flash', 'true' );
        }
        add_settings_error('options', esc_attr( 'settings_updated' ), __("Options updated", 'bannerlid'), "updated");
        
        //
        // Redirect to table
        //
        $subpage = null;
    break;
}

//
// Show the wrrors
//
settings_errors('options');

//
// Pages html
//
switch($subpage){
 
    default:
        include self::getViewPath() . 'AdminOptionsDefault.php';
    break;
}
?>