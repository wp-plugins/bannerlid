<div id="icon-users" class="icon32"><br/></div>

<?php 
$stat = new Bannerlid\Stat();
$browser_data = $stat->getBrowserCounts();
if(!empty($browser_data)):
?>
    <div class="chart_wrapper chart_half">
        <h3><?php echo __('Total Browser Statistics', 'bannerlid'); ?></h3>
        <?php
        $chart = new Bannerlid\BarChart("bar", "zone_clicks"); 
        $chart->createDataFromRaw($browser_data);
        $chart->showChart();
        ?>
    </div>

    <div class="chart_wrapper chart_half">
        <h3><?php echo __('Total Browser Statistics', 'bannerlid'); ?></h3>
        <?php 
        $chart2 = new Bannerlid\DoughnutChart("bar", "zone_clicks2"); 
        $chart2->createDataFromRaw($browser_data);
        $chart2->showChart();
        ?>
    </div>

<?php else: ?>

    <p><?php _e('No browser data avilable', 'bannerlid'); ?>

<?php endif; ?>