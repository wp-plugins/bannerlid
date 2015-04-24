<div id="icon-users" class="icon32"><br/></div>

<div class="filter_wrapper">
    <?php echo self::getSearchForm(); ?>
</div>

<?php
$stat = new Bannerlid\Stat($_GET);
$country_data = $stat->getCountryCounts('zone_click');

if(!empty($country_data)): ?>

    <div class="chart_wrapper chart_half" id="legend">
    </div>

    <div class="chart_wrapper chart_half">
        <h3><?php _e('Country Comparisons', 'bannerlid');?></h3>
        <?php
        $chart2 = new Bannerlid\DoughnutChart("countries", "country_comparisons"); 
        $chart2->createDataFromRaw($country_data);
        $chart2->showChart();
        ?>
    </div>

<?php else: ?>
    <p><?php _e('No click data available', 'bannerlid'); ?></p>
<?php endif; ?>