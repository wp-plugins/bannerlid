<div id="icon-users" class="icon32"><br/></div>
        
<div class="filter_wrapper">
    <?php echo self::getSearchForm(); ?>
</div>

<?php 
$stat = new Bannerlid\Stat($_GET);
$zone_data = $stat->getBannerCalendar("banner_click");
if(!empty($zone_data)): ?>

    <div class="chart_wrapper chart_half">
                
        <h3><?php _e('Banner Clicks', 'bannerlid');?></h3>
        <?php
        $chart = new Bannerlid\BarChart("Banner Clicks", "banner_clicks"); 
        $chart->createDataFromRaw($zone_data);
        $chart->showChart();
        ?>

    </div>

<?php else: ?>

    <p class="bannerlid-warning-panel full-width"><?php _e('No click data available', 'bannerlid'); ?></p>

<?php endif; ?>

<?php
$zone_data = $stat->getBannerCalendar("banner_impression");
if(!empty($zone_data)):
?>
    <div class="chart_wrapper chart_half">

        <h3><?php _e('Banner Impressions', 'bannerlid');?></h3>
        <?php
        $chart2 = new Bannerlid\BarChart("Banner Impressions", "banner_impressions"); 
        $chart2->createDataFromRaw($zone_data);
        $chart2->showChart();
        ?>
    </div>

<?php else: ?>
    
    <p class="bannerlid-warning-panel full-width"><?php _e('No click data available', 'bannerlid'); ?></p>

<?php endif; ?>