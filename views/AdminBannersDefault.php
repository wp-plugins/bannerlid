<div class="wrap">
    <div id="icon-users" class="icon32"><br/></div>
    <h2><?php echo __('Banners', 'bannerlid'); ?>
        <a href="<?php echo admin_url( 'admin.php');?>?page=bannerlid&amp;subpage=edit_banner" class="add-new-h2">Add New</a>
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