<div id="icon-users" class="icon32"><br/></div>
        
<div class="filter_wrapper">
    <?php echo self::getSearchForm(); ?>
</div>


<?php
$stat = new Bannerlid\Stat($_GET);
$country_data = $stat->getCountryCounts('banner_click');

if(!empty($country_data)): ?>

    <div class="chart_wrapper chart_half">

        <h3><?php _e('Countries by clicks', 'bannerlid');?></h3>
        
        <?php
        $country_chart = new Bannerlid\DoughnutChart("countries", "country_comparisons"); 
        $country_chart->createDataFromRaw($country_data);
        $country_chart->showChart();
        ?>
    </div>

<?php else: ?>

    <p class="bannerlid-warning-panel full-width"><?php _e('No country click data available', 'bannerlid'); ?></p>

<?php endif; ?>

<?php

$impression_data = $stat->getCountryCounts('banner_impression');

if(!empty($impression_data)): ?>

    <div class="chart_wrapper chart_half">

        <h3><?php _e('Countries by impressions', 'bannerlid');?></h3>
        
        <?php
        $impression_chart = new Bannerlid\DoughnutChart("Impressions", "country_impressions"); 
        $impression_chart->createDataFromRaw($impression_data);
        $impression_chart->showChart();
        ?>
    </div>

<?php else: ?>

    <p class="bannerlid-warning-panel full-width"><?php _e('No country impression data available', 'bannerlid'); ?></p>

<?php endif; ?>

<?php if(!empty($country_data) || !empty($impression_data)): ?>
<div class="chart_wrapper chart_half" id="legend"></div>
<?php endif; ?>