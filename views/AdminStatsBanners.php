<div id="icon-users" class="icon32"><br/></div>

<?php 
$stat = new Bannerlid\Stat();
$banner_data = $stat->getBannerCounts("banner_click");
if(!empty($banner_data)):
    ?>

    <div class="chart_wrapper chart_half">
        <h3><?php _e('Total Banner Clicks' , 'bannerlid');?></h3>
        <?php
        $chart = new Bannerlid\BarChart("bar", "banner_clicks"); 
        $chart->createDataFromRaw($banner_data);
        $chart->showChart();
        ?>
    </div>

    <div class="chart_wrapper chart_half">
        <h3><?php _e('Total Banner Clicks' , 'bannerlid');?></h3>
        <?php 
        $chart2 = new Bannerlid\DoughnutChart("bar", "banner_clicks2"); 
        $chart2->createDataFromRaw($banner_data);
        $chart2->showChart();
        ?>
    </div>

<?php else: ?>
        
    <p><?php _e('No data avilable for banner clicks', 'bannerlidstats'); ?>

<?php endif; ?> 


<?php 
$stat = new Bannerlid\Stat();
$banner_data = $stat->getBannerCounts("banner_impression");
if(!empty($banner_data)):
?>

    <div class="chart_wrapper chart_half">
         <h3><?php _e('Total Banner Impressions' , 'bannerlid');?></h3>
        <?php
        $chart2 = new Bannerlid\DoughnutChart("bar", "banner_impressions2"); 
        $chart2->createDataFromRaw($banner_data);
        $chart2->showChart();
        ?>
    </div>

    <div class="chart_wrapper chart_half">
        <h3><?php _e('Total Banner Impressions' , 'bannerlid');?></h3>
        <?php 
        $chart = new Bannerlid\BarChart("bar", "banner_impressions"); 
        $chart->createDataFromRaw($banner_data);
        $chart->showChart();
        ?>
    </div>

<?php else: ?>
        
    <p><?php _e('No data avilable for banner impressions', 'bannerlidstats'); ?>

<?php endif; ?> 