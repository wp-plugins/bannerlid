<div id="icon-users" class="icon32"><br/></div>
        
<?php 
$stat = new Bannerlid\Stat();
$user_data = $stat->getUsersCounts('banner_click');
if(!empty($user_data)):
?>

    <div class="chart_wrapper chart_full">
        <h3><?php echo __('Total User Banner Clicks', 'bannerlid'); ?></h3>      
        <?php
        $chart = new Bannerlid\BarChart("bar", "banner_clicks"); 
        $chart->createDataFromRaw($user_data);
        $chart->showChart();
        ?>
    </div>

<?php else: ?>

    <p><?php _e('No data avilable for users\' clicks', 'bannerlid'); ?>

<?php endif; 

$user_impression_data = $stat->getUsersCounts('banner_impression');
if(!empty($user_impression_data)):
?>

    <div class="chart_wrapper chart_full">
        <h3><?php echo __('Total User Banner Impressions', 'bannerlid'); ?></h3>      
        <?php
        $chart = new Bannerlid\BarChart("bar", "banner_impressions"); 
        $chart->createDataFromRaw($user_impression_data);
        $chart->showChart();
        ?>
    </div>

<?php else: ?>

    <p><?php _e('No data avilable for users\' impressions', 'bannerlid'); ?>

<?php endif; ?>