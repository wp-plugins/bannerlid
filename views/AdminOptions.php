<?php


/*
* Get the main header variableswhich detrmine which 
* pages we show
*/
isset($_GET['subpage']) ? $subpage = $_GET['subpage'] : $subpage = null;
isset($_GET['action']) ? $action = $_GET['action'] : $action = null;
isset($_GET['id']) ? $id = intval($_GET['id']) : $id = null;

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
        ?>
        <div class="wrap">
            <div id="icon-users" class="icon32"><br/></div>
            <h2><?php echo __('Options', 'banner-lid'); ?></h2>
            
            <div id="poststuff">     
                <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content">
                        <div class="meta-box-sortables ui-sortable">
                            <div class="postbox">
                                <div class="inside">
                                    
                                    <form method="get" action="<?php echo admin_url( 'admin.php'); ?>">
                                        <table class="baller-lid-zone-edit-form form-table">
                                            <tbody>
                                                <tr>
                                                    <th><?php _e('Collect Stats', 'bannerlid');?></th>
                                                    <td><input type="checkbox" value="1" name="bannerlid-collect-stats" <?php echo get_option('bannerlid-collect-stats') == 'true' ? 'checked' : '';?> />
                                                    <p><em>If you want to save mysql space, you can switch off the click and impression tracking.</em></p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th><?php _e('Enable flash banners', 'bannerlid');?></th>
                                                    <td>
                                                    <input type="checkbox" value="1" name="bannerlid-enable-flash" <?php echo get_option('bannerlid-enable-flash') == 'true' ? 'checked' : '';?> />
                                                    <p><em><?php _e('Flash banners can cause issues with performance and compatibility and bannerlid will not collect click data for Flash banners.', 'bannerlid');?></em></p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>
                                                        <?php wp_nonce_field( 'options_process' ); ?>
                                                        <input type="hidden" value="banner-lid-options" name="page" />
                                                        <input type="hidden" name="action" value="options_process" />
                                                        <input class="button-primary" type="submit" name="submit" value="<?php _e( 'Save' ); ?>" />    
                                                    </th>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </form>
                                </div> 
                            </div> 
                        </div> 
                    </div> 
                    
                    <!-- sidebar -->
                    <div id="postbox-container-1" class="postbox-container">
                        <div class="meta-box-sortables">
                            <div class="postbox">
                                <h3><span><?php _e('Bannerlid Info', 'bannerlid');?></span></h3>
                                <div class="inside">
                                   <p><?php _e('Version', 'bannerlid');?> <?php echo BANNERLID_VERSION;?></p>
                                   
                                </div> 
                            </div> 
                        </div> 
                    </div> 
                </div> 
                <br class="clear">
            </div> 
        </div> 
        <?php
    break;
}
?>