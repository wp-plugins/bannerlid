<?php

isset($_GET['subpage']) ? $subpage = $_GET['subpage'] : $subpage = null;
isset($_GET['action']) ? $action = $_GET['action'] : $action = null;
isset($_GET['id']) ? $id = $_GET['id'] : $id = null;

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
            $dataobj->update($id, $name, $slug, $file, $url, $new_window, $width, $height);
            $dataobj->deleteRelations($id);
            $dataobj->addRelations($id, $zone_ids);
        } else {
            $new_banner_id = $dataobj->add($name, $slug, $file, $url, $new_window, $width, $height);
            $dataobj->addRelations($new_banner_id, $zone_ids);
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

//
// Pages html
//
switch($subpage){
 
    case "edit_banner":

        if($id){
            $banner_data = $dataobj->get($id);
            $relations = $dataobj->getRelations($id);
            $sequential=array();
            $b = array_map(function($val) use (&$sequential){ $sequential[] = $val['zone_id']; }, $relations);  
        }

        ?>

        <div id="icon-users" class="icon32"><br/></div>
        <h2><?php _e('Add / Edit Banner', 'banner-lid');?></h2>
        
        <form method="get" action="<?php echo admin_url( 'admin.php'); ?>">
            <table class="banner-lid-zone-edit-form form-table">
                <tbody>
                    <tr>
                        <th><?php _e('Banner name', 'bannerlid'); ?></th>
                        <td><input type="text" value="<?php echo isset($banner_data) ? stripslashes($banner_data['name']) : '';?>" name="name" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th><?php _e('Slug', 'bannerlid'); ?></th>
                        <td><input type="text" value="<?php echo isset($banner_data) ? $banner_data['slug'] : '';?>" name="slug" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th><?php _e('Banner', 'bannerlid'); ?></th>
                        <td><input type="text" value="<?php echo isset($banner_data) ? $banner_data['file'] : '';?>" name="file" id="file_url" class="regular-text" />
                            <a href="#" id="add-media" class="button">Add banner</a>
                            <div id="banner_preview">
                                <?php if(!empty($banner_data['file'])): ?>
                                    <?php
                                    $banner = new Bannerlid\Banner($banner_data['ID']);
                                    echo $banner->getBannerImage(350);
                                    ?>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Link', 'bannerlid'); ?></th>
                        <td><input type="text" value="<?php echo isset($banner_data) ? $banner_data['url'] : '';?>" name="url" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th><?php _e('Width override', 'bannerlid'); ?></th>
                        <td><input type="text" value="<?php echo isset($banner_data) ? $banner_data['width'] : '';?>" name="width" class="regular-text" />px</td>
                    </tr>
                    <tr>
                        <th><?php _e('Height override', 'bannerlid'); ?></th>
                        <td><input type="text" value="<?php echo isset($banner_data) ? $banner_data['height'] : '';?>" name="height" class="regular-text" />px</td>
                    </tr>
                    <tr>
                        <th><?php _e('Zones', 'bannerlid'); ?></th>
                        <td>
                            <?php    
                            $zone_obj = new Bannerlid\Zones();
                            $zones = $zone_obj->getList();
                            ?>
                            <select name="zone_ids[]" id="zone_ids" multiple="true">
                                <?php if(!empty($zones)) : ?>
                                    <?php foreach($zones as $zone): ?>
                                        <?php in_array($zone['ID'], $sequential) ? $selected = 'selected' : $selected = ''; ?>
                                       <option <?php echo $selected;?> value="<?php echo $zone['ID'];?>"><?php echo $zone['name'];?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Open in new window?', 'bannerlid'); ?></th>
                        <td><input type="checkbox" value="1" name="new_window" <?php echo isset($banner_data['new_window']) == 1 ? 'checked' : '';?> /></td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="hidden" value="banner-lid" name="page" />
                            <input type="hidden" name="subpage" value="edit_banner" />
                            <input type="hidden" name="action" value="banner_process" />
                            <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '';?>" />
                            <?php wp_nonce_field( 'banner_process' ); ?>
                            <input class="button-primary" type="submit" name="submit" value="<?php _e( 'Save' ); ?>" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
        <?php
       
    break;
    default:
        ?>
        <div class="wrap">
            <div id="icon-users" class="icon32"><br/></div>
            <h2><?php echo __('Banners', 'bannerlid'); ?>
                <a href="<?php echo admin_url( 'admin.php');?>?page=banner-lid&amp;subpage=edit_banner" class="add-new-h2">Add New</a>
            </h2>
            <?php
            //Create an instance of our package class...
            $testListTable = new Bannerlid\BannersTable();
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