<?php

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

//
// Show the tabs
//

?>
<div class="wrap">
<?php
//
// Pick page
//
switch($subpage){
    case "Overview":
        self::showTabs(array("Overview", "Users", "Countries"));
       include self::getViewPath() . 'AdminZonesOverview.php';
    break;
    case "Users":
        self::showTabs(array("Overview", "Users", "Countries"));
        include self::getViewPath() . 'AdminZonesUsers.php';
    break;
    case "Countries":
        self::showTabs(array("Overview", "Users", "Countries"));
        include self::getViewPath() . 'AdminZonesCountries.php';
    break;
    case "edit_zone":
        include self::getViewPath() . 'AdminZonesEdit.php';
    break;
    default:
        include self::getViewPath() . 'AdminZonesDefault.php';
    break;
}
?>
</div>