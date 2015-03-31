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
    case "delete_zone":
        //
        // Check the nonce
        //
        check_admin_referer( 'zone_delete');
        //
        // Delete the zone
        //
        if(!$id){
            add_settings_error('zone', esc_attr( 'settings_updated' ), __("No id selected to delete", "bannerlid"),"error");
        } else {
            $dataobj->delete($id);  
            $stats_obj = new Bannerlid\Stats();
            $stats_obj->deleteByZone($id);  
            add_settings_error('zone', esc_attr( 'settings_updated' ), __("Zone deleted", "bannerlid"), "updated");
        }          
    break;
    case "zone_process":
        //
        // Check the nonce
        //
        check_admin_referer( 'zone_process');
        //
        // Sznitize variables and check everything is included that neess to be included. 
        // 
        !empty($_GET['name']) ? $name = sanitize_text_field($_GET['name']) : $name = null;
        !empty($_GET['slug']) ? $slug = sanitize_title_with_dashes($_GET['slug']) : $slug = null;
        !empty($_GET['type']) ? $type = sanitize_text_field($_GET['type']) : $type = null;
        !empty($_GET['description']) ? $description = sanitize_text_field($_GET['description']) : $description = null;
        !empty($_GET['positions']) ? $positions = sanitize_text_field($_GET['positions']) : $positions = null;

        if(is_null($name)){
            add_settings_error('zone', esc_attr( 'settings_updated' ), __("Zone Title missing", "bannerlid"), "error");
            break;
        }

        if(is_null($slug)){
            $slug = strtolower(sanitize_title_with_dashes( $name ));
        }

        // Check slug hasn't been used elsewhere
        $check_slug = $dataobj->getBySlug($slug);
        if(!empty($check_slug) && $check_slug['ID'] != $id){
            add_settings_error('zone', esc_attr( 'settings_updated' ), __("This slug has been used", "bannerlid"), "error");
            break;
        }

        if($id){
            $dataobj->update($id, $type, $name, $slug, $description);
        } else {
            $dataobj->add($type, $name, $slug, $description);
        }

        // If changed, re-order positions
        if($positions){
            
            $banner_obj = new Bannerlid\Banners();

            $pos_array = explode(",", $positions);
            $banner_obj->updateZonePositions($pos_array);
        }

        add_settings_error('zone', esc_attr( 'settings_updated' ), __("Zone added/updated", "bannerlid"), "updated");
        //
        // Redirect to table
        //
        $subpage = null;
    break;
}

//
// Show the wrrors
//
settings_errors('zone');


switch($subpage){
    
    case "edit_zone":

        if($id){
            $zone_data = $dataobj->get($id);
        }
        
        ?>
        <div class="wrap">
            <div id="icon-users" class="icon32"><br/></div>
            <h2><?php _e('Add / Edit Zone', 'bannerlid');?></h2>
            
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
                                                <th><?php _e('Zone type', 'bannerlid');?></th>
                                                <td>
                                                    <select name="type" id="type" >
                                                        <option value="all" <?php if(isset($zone_data) && $zone_data['type'] == "all") echo 'selected';?>><?php _e('Show all', 'bannerlid');?></option>
                                                        <option value="randomize" <?php if(isset($zone_data) && $zone_data['type'] == "randomize") echo 'selected';?>><?php _e('Show random', 'bannerlid');?></option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th><?php _e('Zone name', 'bannerlid');?></th>
                                                <td><input type="text" value="<?php echo isset($zone_data) ? $zone_data['name'] : '';?>" name="name" class="regular-text" /></td>
                                            </tr>
                                            <tr>
                                                <th><?php _e('Slug', 'bannerlid');?></th>
                                                <td><input type="text" value="<?php echo isset($zone_data) ? $zone_data['slug'] : '';?>" name="slug" class="regular-text" /></td>
                                            </tr>
                                            <tr>
                                                <th><?php _e('Description', 'bannerlid');?></th>
                                                <td><textarea cols="80" rows="10" name="description"><?php echo isset($zone_data) ? $zone_data['description'] : '';?></textarea></td>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <td>
                                                    <?php wp_nonce_field( 'zone_process' ); ?>
                                                    <input type="hidden" value="banner-lid-zones" name="page" />
                                                    <input type="hidden" name="subpage" value="edit_zone" />
                                                    <input type="hidden" name="action" value="zone_process" />
                                                    <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '';?>" />
                                                    <input type="hidden" name="positions" id="positions" value="" />
                                                    <input class="button-primary" type="submit" name="submit" value="<?php _e( 'Save' ); ?>" />
                                                </td>
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
                                <h3><span><?php _e('Order Adverts in Zone', 'bannerlid');?></span></h3>
                                <div class="inside">
                                    <?php
                                    if($id){
                                        $banner_obj = new Bannerlid\Banners();
                                        $relations = $banner_obj->getRelationsByZone($id);
                                        echo '<ul id="sortable">';
                                        foreach($relations as $banner){
                                            $banner_data = $banner_obj->get($banner['banner_id']);
                                            if($banner_data['file'])
                                                echo '<li id="banner_'.$banner['ID'].'">';
                                                $banner = new Bannerlid\Banner($banner['banner_id']);
                                                echo $banner->showPreview();
                                                echo '</li>';
                                        }
                                        echo '</ul>';
                                    }
                                    ?>
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
    default:
        ?>
        <div class="wrap">
	        <div id="icon-users" class="icon32"><br/></div>
	        <h2><?php echo __('Zones', 'bannerlid'); ?>
	           <a href="<?php echo admin_url( 'admin.php');?>?page=banner-lid-zones&amp;subpage=edit_zone" class="add-new-h2">Add New</a>
	        </h2>
			<?php
	        //Create an instance of our package class...
	        $testListTable = new Bannerlid\ZonesTable();
	        $testListTable->prepare_items();
			?>
	        <form id="movies-filter" method="get">
		    	<input type="hidden" name="page" value="<?php echo $_REQUEST['page'];?>" />
	        	<?php echo $testListTable->display(); ?>
	        </form>
        </div>

		<?php
    break;
}


?>