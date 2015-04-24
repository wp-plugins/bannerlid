<div id="icon-users" class="icon32"><br/></div>
        
            
<?php 
$stat = new Bannerlid\Stat();
$zone_data = $stat->getZoneCounts("zone_click");

if(!empty($zone_data)):
?>
    <div class="chart_wrapper chart_half">
         <h3><?php _e('Total Zone Clicks' , 'bannerlid');?></h3>
        <?php
        $chart = new Bannerlid\BarChart("bar", "zone_clicks"); 
        $chart->createDataFromRaw($zone_data);
        $chart->showChart();
        ?>
    </div>

    <div class="chart_wrapper chart_half">
        <h3><?php _e('Total Zone Clicks' , 'bannerlid');?></h3>
        <?php 
        $chart2 = new Bannerlid\DoughnutChart("bar", "zone_clicks2"); 
        $chart2->createDataFromRaw($zone_data);
        $chart2->showChart();
        ?>
    </div>

<?php else: ?>

    <p><?php _e('No data avilable for zone clicks', 'bannerlid'); ?>

<?php endif; ?>

<?php 
$stat = new Bannerlid\Stat();
$zone_data = $stat->getZoneCounts("zone_impression");

if(!empty($zone_data)):
?>
    <div class="chart_wrapper chart_half">
        <h3><?php _e('Total Zone Impressions' , 'bannerlid');?></h3>
        <?php
        $chart3 = new Bannerlid\DoughnutChart("doughnut", "zone_impressions2"); 
        $chart3->createDataFromRaw($zone_data);
        $chart3->showChart();
        ?>
    </div>

    <div class="chart_wrapper chart_half">
        <h3><?php _e('Total Zone Impressions' , 'bannerlid');?></h3>
        <?php 
        $chart4 = new Bannerlid\BarChart("bar", "zone_impressions"); 
        $chart4->createDataFromRaw($zone_data);
        $chart4->showChart();
        ?>
    </div>
<?php else: ?>

    <p><?php _e('No data avilable for zone impressions', 'bannerlid'); ?>

<?php endif; ?>