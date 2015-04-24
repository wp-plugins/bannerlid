

<div id="icon-users" class="icon32"><br/></div>
<h2><?php echo __('Single Zone', 'bannerlid'); ?></h2>

<div class="filter_wrapper">
    <?php echo self::getSearchForm(); ?>
</div>

<?php 
$stat = new Bannerlid\Stat($_GET);
$zone_data = $stat->getBannerCalendar("zone_click", "bannerlid_zones");
if(!empty($zone_data)):
?>
    <div class="chart_wrapper chart_half">
    <h3><?php _e('Zone Clicks' , 'bannerlid');?></h3>
    <?php
    $chart = new Bannerlid\BarChart("bar", "zone_clicks"); 
    $chart->createDataFromRaw($zone_data);
    $chart->showChart();
    ?>
    </div>
<?php
endif;

$zone_data = $stat->getBannerCalendar("zone_impression", "bannerlid_zones");
if(!empty($zone_data)):
?>
    <div class="chart_wrapper chart_half">
    <h3><?php _e('Zone Impressions' , 'bannerlid');?></h3>
    <?php
    $chart2 = new Bannerlid\BarChart("bar2", "zone_clicks2"); 
    $chart2->createDataFromRaw($zone_data);
    $chart2->showChart();
    ?>
    </div>
<?php
endif;