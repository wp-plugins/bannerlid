<?php

$output = '';

//
// Actions
//
switch($action){
    case "banner_process":
        //
        // Check the nonce
        //
        check_admin_referer( 'banner_process');
        //
        // Sznitize variables and check everything is included that neess to be included. 
        // 
        !empty($_GET['name']) ? $name = sanitize_text_field($_GET['name']) : $name = null;
        !empty($_GET['slug']) ? $slug = sanitize_title_with_dashes($_GET['slug']) : $slug = null;
        !empty($_GET['file']) ? $file = sanitize_text_field($_GET['file']) : $file = null;
        !empty($_GET['height']) ? $height = intval($_GET['height']) : $height = null;
        !empty($_GET['width']) ? $width = intval($_GET['width']) : $width = null;
        !empty($_GET['url']) ? $url = sanitize_text_field($_GET['url']) : $url = null;
        !empty($_GET['zone_ids']) ? $zone_ids = $_GET['zone_ids'] : $zone_ids = null;
        !empty($_GET['new_window']) ? $new_window = $_GET['new_window'] : $new_window=0;
        !empty($_GET['live_date']) ? $live_date = $_GET['live_date'] : $live_date=0;
        !empty($_GET['end_date']) ? $end_date = $_GET['end_date'] : $end_date=0;
        !empty($_GET['banner_posts']) ? $banner_posts = $_GET['banner_posts'] : $banner_posts=null;

        if(is_null($name) || is_null($file)){
            add_settings_error('banner', esc_attr( 'settings_updated' ), __("Fill in all the details", 'bannerlid'), "error");
            break;
        }

        if(is_null($slug)){
            $slug = strtolower(sanitize_title_with_dashes( $name ));
        }

        // Check slug hasn't been used elsewhere
        $check_slug = $dataobj->getBySlug($slug);
        if(!empty($check_slug) && $check_slug['ID'] != $id){
            add_settings_error('banner', esc_attr( 'settings_updated' ), __("This slug has been used", 'bannerlid'), "error");
            break;
        }

        // Check url is valid and has http:// in string. If not, we add it
        if(!empty($url) && (strpos($url, 'http://') !== 0  && strpos($url, 'https://') !== 0)){
            $url = 'http://'.$url;
        }

        //
        // If we have an ID then we know we're updating. If not, then we're creating a 
        // new record.
        //
        if($id){
            $dataobj->update($id, $name, $slug, $file, $url, $new_window, $width, $height, $live_date, $end_date);
            $dataobj->deleteRelations($id);
            $dataobj->addRelations($id, $zone_ids);

            $dataobj->deletePostRelations($id);
            $dataobj->addPostRelations($id, $banner_posts);
        } else {
            $new_banner_id = $dataobj->add($name, $slug, $file, $url, $new_window, $width, $height, $live_date, $end_date);
            $dataobj->addRelations($new_banner_id, $zone_ids);
            $dataobj->addPostRelations($id, $banner_posts);
        }

        //
        // Set the status message
        //
        add_settings_error('banner', esc_attr( 'settings_updated' ), __("Banner created or updated successfully", 'bannerlid'), "updated");
        //
        // Redirect to table
        //
        $subpage = null;
    break;
    case "delete_banner":
        //
        // Check the nonce
        //
        check_admin_referer( 'banner_delete');
        //
        // Delete the bnner
        //
        if(!$id){
            add_settings_error('banner', esc_attr( 'settings_updated' ), __("No banner ID selected", 'bannerlid'), "error");
        } else {
            $dataobj->delete($id);
            $dataobj->deleteRelations($id);
            $stats_obj = new Bannerlid\Stats();
            $stats_obj->deleteByBanner($id);
            add_settings_error('banner', esc_attr( 'settings_updated' ), __("Banner deleted", 'bannerlid'), "updated");
        }
        //
        // Redirect to table
        //
        $subpage = null;
    break;

    
}

//
// Show the wrrors
//
settings_errors('banner');
?>

<div class="wrap">
    
<?php
//
// Pick page
//
switch($subpage){
    case "Overview":
        self::showTabs(array("Overview", "Users", "Countries"));
        include self::getViewPath() . 'AdminBannersOverview.php';
    break;
    case "Users":
        self::showTabs(array("Overview", "Users", "Countries"));
        include self::getViewPath() . 'AdminBannersUsers.php';
    break;
    case "Countries":
        self::showTabs(array("Overview", "Users", "Countries"));
        include self::getViewPath() . 'AdminBannersCountries.php';
    break;
    case "edit_banner":
        include self::getViewPath() . 'AdminBannersEdit.php';
    break;
    default:
        include self::getViewPath() . 'AdminBannersDefault.php';
    break;
}
?>

</div>