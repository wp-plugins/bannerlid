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