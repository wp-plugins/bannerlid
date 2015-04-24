<?php
if($id){
    $banner_data = $dataobj->get($id);
    $relations = $dataobj->getRelations($id);
    $sequential=array();
    $b = array_map(function($val) use (&$sequential){ $sequential[] = $val['zone_id']; }, $relations);  

    $post_relations = $dataobj->getPostRelations($id);
    $post_sequential=array();
    if(!empty( $post_relations))
       $c = array_map(function($val) use (&$post_sequential){ $post_sequential[] = $val['post_id']; }, $post_relations);  
}
?>

<div class="wrap">
    <div id="icon-users" class="icon32"><br/></div>
<h2><?php _e('Add / Edit Banner', 'bannerlid');?></h2>

    <div id="poststuff">     
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <div class="postbox">
                        <div class="inside">
                            
                            <form method="get" action="<?php echo admin_url( 'admin.php'); ?>">
                                <table class="bannerlid-zone-edit-form form-table">
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
                                            <th><?php _e('Publish date', 'bannerlid'); ?></th>
                                            <td><input type="text" value="<?php echo isset($banner_data) ? $banner_data['live_date'] : '';?>" id="publish_date" name="live_date" class="regular-text" /></td>
                                        </tr>
                                        <tr>
                                            <th><?php _e('Unpublish date', 'bannerlid'); ?></th>
                                            <td><input type="text" value="<?php echo isset($banner_data) ? $banner_data['end_date'] : '';?>" id="unpublish_date" name="end_date" class="regular-text" /></td>
                                        </tr>
                                        <tr>
                                            <th><?php _e('Select posts/pages to show on (If none are selected the banner will be shown on all)', 'bannerlid'); ?></th>
                                            <td>
                                                <select name="banner_posts[]" id="banner_posts" multiple="true" style="height: 200px">
                                                    <?php 
                                                    $posts = get_posts();
                                                    ?>
                                                    <?php if(!empty($posts)) : ?>
                                                        <?php foreach($posts as $post): ?>
                                                            <?php in_array($post->ID, $post_sequential) ? $selected = 'selected' : $selected = ''; ?>
                                                            <option <?php echo $selected;?> value="<?php echo $post->ID;?>"><?php echo $post->post_name;?></option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <td>
                                                <input type="hidden" value="bannerlid" name="page" />
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
                        </div> 
                    </div> 
                </div> 
            </div> 
            
            <!-- sidebar -->
            <?php 
            if($id): ?>
            <div id="postbox-container-1" class="postbox-container">
                <div class="meta-box-sortables">
                    <div class="postbox">
                        <h3><span><?php _e('Add banner to content area', 'bannerlid');?></span></h3>
                        <div class="inside">
                            <p><?php _e('To insert this banner into a content area, add the following code into the editor.', 'bannerlid');?></p>
                            <p><strong><?php _e('[banner="'.$id.'"]', 'bannerlid');?></strong></p>
                            <p><?php _e('You can override the width and height by adding attributes to the shortcode like so.', 'bannerlid');?></p>
                            <p><strong><?php _e('[banner="'.$id.'" width="300" height="200"]', 'bannerlid');?></strong></p>
                        </div>
                        <h3><span><?php _e('Add banner to template', 'bannerlid');?></span></h3>
                        <div class="inside">
                            <p><?php _e('To insert this banner into your template, enter the following PHP code into your template.', 'bannerlid');?></p>
                            <p><strong>
                            <?php _e('&lt;?php <br/>', 'bannerlid');?>
                            <?php _e('$params = array("id" => '.$id.'); <br/>', 'bannerlid');?>
                            <?php _e('BannerlidBanner($params); <br/>', 'bannerlid');?>
                            <?php _e('?&gt;', 'bannerlid');?>
                            </strong></p>
                            
                        </div>  
                        <h3><span><?php _e('Direct hyperlink', 'bannerlid');?></span></h3>
                        <div class="inside">
                            <p><?php _e('The following link will track clicks for this banner. You can use this on external banners to keep track of clicks in Bannerlid.', 'bannerlid');?></p>
                            <p><strong>
                            <?php echo Bannerlid\Frontend::makeLink($id); ?>
                            </strong></p>
                        </div>  
                    </div> 
                </div> 
            </div> 
        <?php endif; ?>
        </div> 
        <br class="clear">
    </div> 
</div> 




