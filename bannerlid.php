<?php 
/**
 * This is our core file where we instantiate the main class which runs our
 * plugin and also define a few important global functions and activation
 * and deactivation hooks.
 *
 * @since             1.0.0
 * @package           Bannerlid
 *
 * @wordpress-plugin
 * Plugin Name:       Bannerlid
 * Description:       Simple banner/advert management system with stats and flash support.
 * Version:           1.1.1
 * Author:            Weblid
 * Author URI:        http://www.weblid.net
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bannerlid
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once plugin_dir_path( __FILE__ ) . 'classes/Bannerlid.php';



/****************************************
*  POINT OF ENTRY
*****************************************/
/**
 * Returns the main instance of bannerlid which put the 
 * app wheel in motion
 *
 * @since  1.0.0
 * @return Bannerlid
 */
function Bannerlid() {
	return Bannerlid\Bannerlid::instance();
}
$bannerlid = Bannerlid();
define('BANNERLID_VERSION', $bannerlid->get_version());


/****************************************
*  GLOBAL FUNCTIONS
*****************************************/
/**
 * Global function for users to call in their template. 
 *
 * @since  1.0.0
 * @param $banner (int/str) The integer or the slug of the banner to show
 * @param $params (array) Array of parameters to pass
 * @return void
 */
function BannerlidBanner($params) {
	$frontend = new Bannerlid\Frontend();
	echo $frontend->showBanner($params);
}

/**
 * Global function for users to call in their template. 
 *
 * @since  1.0.0
 * @param $banner (int/str) The integer or the slug of the banner to show
 * @param $params (array) Array of parameters to pass
 * @return void
 */
function BannerlidZone($params) {
	$frontend = new Bannerlid\Frontend();
	echo $frontend->showZone($params);
}

/****************************************
*  ACTIVATION HOOKS
*****************************************/

/**
 * Creates the necessary mysql tables for the plugin 
 *
 * @see register_activation_hook()
 */
function bannerlid_install() {
   	global $wpdb;
 	
   	check_system();

 	$banners_table = $wpdb->base_prefix . 'bannerlid_banners';

 	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	if($wpdb->get_var("show tables like '$banners_table'") != $banners_table) 
	{
		$sql = "CREATE TABLE " . $banners_table . " (
			`ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  	`name` varchar(64) NOT NULL,
		  	`slug` varchar(64) NOT NULL,
		  	`file` varchar(256) NOT NULL,
		  	`url` varchar(128) NOT NULL,
		  	`new_window` tinyint(3) unsigned NOT NULL,
		  	`width` smallint(6) unsigned NULL,
		  	`height` smallint(6) unsigned NULL,
		  	`live_date` datetime NOT NULL,
		  	`end_date` datetime NOT NULL,
		  	`published` tinyint(3) unsigned NOT NULL,
		  	PRIMARY KEY (`ID`)
		);";
		
		dbDelta($sql);
	}

	$relations_table = $wpdb->base_prefix . 'bannerlid_relations';

	if($wpdb->get_var("show tables like '$relations_table'") != $relations_table) 
	{
		$sql = "CREATE TABLE " . $relations_table . " (
			`ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`zone_id` int(10) unsigned NOT NULL,
			`banner_id` int(10) unsigned NOT NULL,
			`position` int(11) NOT NULL,
			PRIMARY KEY (`ID`),
			KEY `zone_id` (`zone_id`,`banner_id`),
			KEY `banner_id` (`banner_id`)
		);";
		
		dbDelta($sql);
	}

	$stats_table = $wpdb->base_prefix . 'bannerlid_stats';

	if($wpdb->get_var("show tables like '$stats_table'") != $stats_table) 
	{
		$sql = "CREATE TABLE " . $stats_table . " (
			`ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
		    `action` varchar(64) NOT NULL,
		    `actioned_id` int(10) unsigned NOT NULL,
		    `user_id` int(10) unsigned NOT NULL,
		    `user_ip` varchar(64) NOT NULL,
		    `country` varchar(64) NOT NULL,
		    `browser` varchar(128) NOT NULL,
		    `created` datetime NOT NULL,
		    PRIMARY KEY (`ID`),
		    KEY `actioned_id` (`actioned_id`,`user_id`)
		);";
		
		dbDelta($sql);
	}

	$zones_table = $wpdb->base_prefix . 'bannerlid_zones';

	if($wpdb->get_var("show tables like '$zones_table'") != $zones_table) 
	{
		$sql = "CREATE TABLE " . $zones_table . " (
			`ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`type` varchar(16) NOT NULL,
			`name` varchar(64) NOT NULL,
			`slug` varchar(64) NOT NULL,
			`description` text NOT NULL,
			`created` datetime NOT NULL,
			PRIMARY KEY (`ID`)
		);";
		
		dbDelta($sql);
	}

	$pages_table = $wpdb->base_prefix . 'bannerlid_banner_post_relations';

	if($wpdb->get_var("show tables like '$pages_table'") != $pages_table) 
	{
		$sql = "CREATE TABLE " . $pages_table . " (
			`ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`banner_id` int(10) unsigned NOT NULL,
			`post_id` int(10) unsigned NOT NULL,
			PRIMARY KEY (`ID`),
			KEY `banner_id` (`banner_id`,`post_id`)
		);";
		
		dbDelta($sql);
	}

	add_option('bannerlid-collect-stats', 'true');
	add_option('bannerlid-enable-flash', 'true');
	add_site_option('bannerlid-version', BANNERLID_VERSION);
 
}
// run the install scripts upon plugin activation
register_activation_hook(__FILE__,'bannerlid_install');


function bannerlid_remove() {
   
	delete_option('bannerlid-collect-stats');
	delete_option('bannerlid-enable-flash');
	delete_site_option('bannerlid-version');
 
}
// run the install scripts upon plugin activation
register_deactivation_hook(__FILE__,'bannerlid_remove');

/**
 * Perfoprmed at installation, this just checks the user
 * has the correct version of PHP and has GD library installed
 */
function check_system(){
	if(!function_exists('getimagesize')){
		die("<p>".__("GD Library must be installed", "bannerlid")."</p>");
	}
	if (version_compare(phpversion(), "5.2.0", "<")) { 
	  die("<p>".__("PHP 5.3 or above must be installed", "bannerlid")."</p>");
	} 
}
?>