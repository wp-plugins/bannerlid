<div id="icon-users" class="icon32"><br/></div>
        
<div class="filter_wrapper">
    <?php echo self::getSearchForm(); ?>
</div>

<?php
$stat = new Bannerlid\Stat($_GET);
$user_data = $stat->getUsersCounts('zone_click');

if(!empty($user_data)):
?>
    <div class="chart_wrapper chart_half">

        <h3><?php _e('User Comparisons', 'bannerlid');?></h3>
        
        <?php
        $chart2 = new Bannerlid\DoughnutChart("users", "users_comparisons"); 
        $chart2->createDataFromRaw($user_data);
        $chart2->showChart();
        ?>
    </div>

    <div class="chart_wrapper chart_half" id="legend">

    </div>

<?php
else:
?>
    <p><?php _e('No click data available', 'bannerlid'); ?></p>
<?php endif; ?>