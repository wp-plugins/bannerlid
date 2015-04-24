<?php

$output = '';


//
// Show the errors
//
settings_errors('stats');
?>
<div class="wrap">
    
<?php
self::showTabs(array("Banners", "Zones", "Users", "Browsers"));
//
// Pages html
//
switch($subpage){
    default:
    case "Banners":
        include self::getViewPath() . 'AdminStatsBanners.php';
    break;
    case "Zones":
        include self::getViewPath() . 'AdminStatsZones.php';
    break;
    case "Users":
        include self::getViewPath() . 'AdminStatsUsers.php';
    break;
    case "Browsers":
        include self::getViewPath() . 'AdminStatsBrowsers.php';
    break;
}
?>
</div>