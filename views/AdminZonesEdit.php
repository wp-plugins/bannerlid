<?php
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
                                            <input type="hidden" value="bannerlid-zones" name="page" />
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
                <?php if($id): ?>
                    <div class="meta-box-sortables">
                        <div class="postbox">
                            <h3><span><?php _e('Add zone to content area', 'bannerlid');?></span></h3>
                            <div class="inside">
                                <p><?php _e('To insert this zone into a content area, add the following code into the editor.', 'bannerlid');?></p>
                                <p><strong><?php _e('[zone="'.$id.'"]', 'bannerlid');?></strong></p>        
                            </div>
                            <h3><span><?php _e('Add zone to template', 'bannerlid');?></span></h3>
                            <div class="inside">
                                <p><?php _e('To insert this banner into your template, enter the following PHP code into your template.', 'bannerlid');?></p>
                                <p><strong>
                                <?php _e('&lt;?php <br/>', 'bannerlid');?>
                                <?php _e('$params = array("id" => '.$id.'); <br/>', 'bannerlid');?>
                                <?php _e('BannerlidZone($params); <br/>', 'bannerlid');?>
                                <?php _e('?&gt;', 'bannerlid');?>
                                </strong></p>
                                
                            </div>  
                        </div> 
                    </div> 
                <?php endif; ?>
            </div> 

        </div> 
        <br class="clear">
    </div> 
</div> 