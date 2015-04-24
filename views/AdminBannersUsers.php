<div id="icon-users" class="icon32"><br/></div>
        
<div class="filter_wrapper">
    <?php echo self::getSearchForm(); ?>
</div>

<?php
$stat = new Bannerlid\Stat($_GET);
$user_click_data = $stat->getUsersCounts('banner_click');

if(!empty($user_click_data)): ?>

    <div class="chart_wrapper chart_half">

        <h3><?php _e('User Clicks', 'bannerlid');?></h3>
        
        <?php
        $click_chart = new Bannerlid\DoughnutChart("users", "users_comparisons"); 
        $click_chart->createDataFromRaw($user_click_data);
        $click_chart->showChart();
        ?>

    </div>

<?php else: ?>

    <p class="bannerlid-warning-panel full-width"><?php _e('No user click data available', 'bannerlid'); ?></p>

<?php endif; 

$user_impression_data = $stat->getUsersCounts('banner_impression');

if(!empty($user_impression_data)): ?>

    <div class="chart_wrapper chart_half">

        <h3><?php _e('User Impressions', 'bannerlid');?></h3>
        
        <?php
        $impressions_chart = new Bannerlid\DoughnutChart("User Impressions", "users_impressions"); 
        $impressions_chart->createDataFromRaw($user_impression_data);
        $impressions_chart->showChart();
        ?>

    </div>

<?php else: ?>

    <p class="bannerlid-warning-panel full-width"><?php _e('No user impression data available', 'bannerlid'); ?></p>

<?php endif; ?>

<?php if(!empty($user_click_data) || !empty($user_impression_data)): ?>
<div class="chart_wrapper chart_half" id="legend"></div>
<?php endif; ?>

