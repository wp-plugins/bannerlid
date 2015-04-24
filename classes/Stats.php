<?php

namespace Bannerlid;

/**
 * Provides methods for settings and accessing stats
 *
 * @package    Bannerlid
 * @subpackage Bannerlid/classes
 * @author Weblid <barrywebla@googlemail.com>
 */
class Stats {

	/**
	* Name of the table in the db the stats are stored (without prefix)
	*/
	private $table = 'bannerlid_stats';

	/**
	* Wordpress db dependency
	*/
	protected $db;

	/**
	* Constructor sets up dependencies
	*
	* @see 'public $db'
	* @access public
	*/
	public function __construct(){
		global $wpdb;
		$this->db = $wpdb;
	}

	/**
	* Gets the table name for the stats
	*
	* @access private
	*/
	protected function getTableName(){
		return $this->db->base_prefix . $this->table;
	}

	/**
	* Adds a new stat to the stat table
	*
	* @access public
	* @return void
	*/
	public function add($action, $actioned_id, $user_id, $user_ip, $country, $browser){
		$this->db->insert( $this->getTableName(), 
			array( 'action' => $action, 'actioned_id' => $actioned_id,'user_id' => $user_id, 'user_ip' => $user_ip, 'country' => $country, 'browser' => $browser, 'created' => current_time('mysql', 1)),
			array( '%s', '%d', '%d', '%s', '%s', '%s' , '%s' ) 
		);

	}

	/**
	* Deletes a record from the stats db
	*
	* @access public
	* @param int id of the record to delete
	* @return array - List of all of the current zones
	*/
	public function delete($id){
		$this->db->delete( $this->getTableName(), array( 'ID' => $id ), array( '%d' ) );
	}

	/**
	* Deletes a record from the stats db
	*
	* @access public
	* @param int id of the banner_id to delete stats from
	* @return void
	*/
	public function deleteByBanner($id){
		$this->db->delete( $this->getTableName(), array( 'actioned_id' => $id, 'action' => 'banner_click' ), array( '%d', '%s' ) );
		$this->db->delete( $this->getTableName(), array( 'actioned_id' => $id, 'action' => 'banner_impression' ), array( '%d', '%s' ) );
	}

	/**
	* Deletes a record from the stats db
	*
	* @access public
	* @param int id of the zone_id to delete stats from
	* @return void
	*/
	public function deleteByZone($id){
		$this->db->delete( $this->getTableName(), array( 'actioned_id' => $id, 'action' => 'zone_impression' ), array( '%d', '%s' ) );
		$this->db->delete( $this->getTableName(), array( 'actioned_id' => $id, 'action' => 'zone_click' ), array( '%d', '%s' ) );
	}

	/**
	* Wrapper for $this->add
	*
	* @access public
	* @param int id of the record to delete
	* @return array - List of all of the current zones
	*/	
	public function addBannerStat($type, $banner_id, $user_id, $user_ip, $browser, $country=null){
		//if(get_option('bannerlid-collect-stats') != 'true')
		//	return;
		$this->add($type, $banner_id, $user_id, $user_ip, $country, $browser['name']);
	}
}


/**
 * This class gets the stats we need to build each chart.
 * It extends the core class Stats which is
 * a dependency and acts as the business layer (Though this
 * may not be needed to be extended).
 *
 * @since      1.0.0
 * @package    Bannerlid
 * @subpackage Bannerlid/classes
 * @author     Barry Mason <barrywebla@googlemail.com>
 */

class Stat extends Stats {


	/**
	 * Are we filtering with dates?
	 */
	private $do_filter = false;
	/**
	 * If this is set to true, the results will not count 
	 * multiple clicks / impressions from the same IP.
	 *
	 * @since 1.0.0
	 */
	public $filter_id;

	/**
	 * If this is set to true, the results will not count 
	 * multiple clicks / impressions from the same IP.
	 *
	 * @since 1.0.0
	 */
	public $filter_unique_ips = false;

	/**
	 * Contains the start date of any date filters
	 * in YYYY-MM-DD HH:II:SS 
	 *
	 * @since 1.0.0
	 */
	public $filter_timelength;

	/**
	 * Contains the end date of any date filters
	 * in YYYY-MM-DD HH:II:SS 
	 *
	 * @since 1.0.0
	 */
	public $filter_date_end;

	/**
	 * In what date 'clumps' should we group data
	 * together? daily/monthly
	 *
	 * @since 1.0.0
	 */
	public $filter_timeframe;

	/**
	 * Construct function and call parent construct. Also sets
	 * up & santizes member variables from $post param. This post 
	 * is the global posted by the filter forms.
	 *
	 * @since 1.0.0
	 * @param (array) $post - $_POST var from filter form
	 */
	public function __construct($post=null){

		parent::__construct();

		if(!is_null($post)){
			$this->do_filter = true;
			$this->filter_id = intval($post['id']);
		
			isset($post['timeframe']) ? $this->filter_timeframe = sanitize_text_field($post['timeframe']) : $this->filter_timeframe = 'daily';
			isset($post['timelength']) ? $this->filter_timelength = intval($post['timelength']) : $this->filter_timelength = 14;
			isset($post['end_date']) ? $this->filter_date_end = sanitize_text_field($post['end_date']) : $this->filter_date_end = date("Y-m-d");
			isset($post['unique_ip']) ? $this->filter_unique_ips = true : $this->filter_unique_ips = false;
		}
	}

	/**
	 * Returns filter info to help search by date and returns 
	 * the 'and' sql string as well as start and end dates 
	 * 
	 * @param (int) $i - The number of days/months removed from the end date to get the start date
	 * @return (array) Filter data
	 */ 
	private function getTimeframeFilterSql($i=null){

		if(!$this->do_filter)
			return; 

		is_null($this->filter_date_end) ? $end_date = date("Y-m-d") : $end_date = $this->filter_date_end;	
		$this->filter_unique_ips == true ? $distinct = "DISTINCT" : $distinct = null;

		if($this->filter_timeframe == "daily"){
				$i_date = date('Y-m-d', strtotime('-'.$i.' day', strtotime($end_date)) );
				$i_year = null;
				$i_month = null;
				$and_query = "AND DATE(s.created) = '".$i_date."' ";
		}
		
		if($this->filter_timeframe == "monthly"){

			$i_date = date('Y-m-d', strtotime('-'.$i.' month', strtotime($end_date)) );
			$i_year = date('Y', strtotime('-'.$i.' month', strtotime($end_date)) );
			$i_month = date('m', strtotime('-'.$i.' month', strtotime($end_date)) );
			$and_query = "AND MONTH(s.created) = '".$i_month."' ";
			$and_query .= "AND YEAR(s.created) = '".$i_year."' ";
		}

		$this->human_start = date("d/m/Y", strtotime($i_date));
		$this->human_end = date("d/m/Y", strtotime($this->filter_date_end));

		return array(
			"and_query" => $and_query, 
			"distinct" => $distinct,
			"i_date" => $i_date,
			"i_year" => $i_year,
			"i_month" => $i_month,
		);
	}

	/**
	 * Get's banner data from multiple banners at once. This
	 * is used to build charts for comparing the number of actions
	 * on one banner to the number of actions with another. 
	 *
	 * @since 1.0.0
	 * @param (string) $action - The action we're looking for from the db such as 'click_banner'
 	 * @return (array) Data
	 */
	public function getBannerCounts($action){

		$table = $this->getTableName();
		
		$query = "SELECT COUNT(b.ID) as total, s.ID, s.action, s.actioned_id, b.name, b.ID FROM $table AS s " . "\n";
		$query .= "LEFT JOIN ".$this->db->base_prefix."bannerlid_banners b ON s.actioned_id = b.ID " . "\n";
		$query .= "WHERE s.action = '%s' AND b.ID > 0 " . "\n";
		$query .= "GROUP BY s.actioned_id " . "\n";

		$sql = $this->db->prepare( $query, $action);		
		$banner_clicks = $this->db->get_results($sql);

		return $banner_clicks;
	}

	/**
	 * Gets the data we need for our filtered charts. This looks at the class's 
	 * member filters and creates and executes the sql query.
	 *
	 * @since 1.0.0
	 * @param (string) $action - The action we're looking for from the db 
	 * @param (string) $joined_table - The table name of the main data (zones or banners)
 	 * @return (array) Data
	*/
	public function getBannerCalendar($action, $joined_table = 'bannerlid_banners'){

		$table = $this->getTableName();		

		$results = array();
		for($i = 0; $i < $this->filter_timelength; $i++){
			
			$filter_globals = $this->getTimeframeFilterSql($i);
			$and_query = $filter_globals['and_query'];
			$distinct = $filter_globals['distinct'];

			$query = "SELECT DATE(s.created) as name, COUNT($distinct s.user_ip) as total, s.ID, s.action, s.actioned_id, s.user_ip, b.ID " . "\n";  
			$query .= "FROM $table AS s " . "\n";  
			$query .= "LEFT JOIN ".$this->db->base_prefix.$joined_table . " b ON s.actioned_id = b.ID "."\n";
			$query .= "WHERE s.action = %s AND s.actioned_id = %d ";
			$query .= $and_query;
		
			$sql = $this->db->prepare( $query, $action, $this->filter_id);		
			$banner_clicks = $this->db->get_row($sql);
			
			if($this->filter_timeframe == "daily")
				$banner_clicks->name = date('d M', strtotime($filter_globals['i_date']));
			
			if($this->filter_timeframe == "monthly")
				$banner_clicks->name = date('M Y', strtotime($filter_globals['i_date']));

			$results[] = $banner_clicks;
			
		}

		return array_reverse( $results );
	}

	/**
	 * Get's zone data from multiple zones at once. This
	 * is used to build charts for comparing the number of actions
	 * on one zone to the number of actions with another. 
	 *
	 * @since 1.0.0
	 * @param (string) $action - The action we're looking for from the db such as 'click_zone'
 	 * @return (array) Data
	*/
	public function getZoneCounts($action){

		$table = $this->getTableName();

		$query = "SELECT COUNT(b.ID) as total, s.ID, s.action, s.actioned_id, b.name, b.ID FROM $table AS s " . "\n";
		$query .= "LEFT JOIN ".$this->db->base_prefix."bannerlid_zones b ON s.actioned_id = b.ID " . "\n";
		$query .= "WHERE s.action = '%s' AND b.ID > 0 " . "\n";
		$query .= "GROUP BY s.actioned_id " . "\n";

		$sql = $this->db->prepare( $query, $action);		
		$zone_clicks = $this->db->get_results($sql);

		return $zone_clicks;
	}

	/**
	 * Gets the total counts of the different browsers used to access banners
	 * and zones.
	 * @since 1.0.0
	 * @return (array) Data
	*/
	public function getBrowserCounts(){

		$table = $this->getTableName();

		$query = "SELECT COUNT(s.browser) as total, s.ID, s.browser as name " . "\n";  
		$query .= "FROM $table AS s " . "\n";  
		$query .= "GROUP BY s.browser"."\n";

		$browser_stats = $this->db->get_results($query);

		return $browser_stats;
	}

	/**
	* Gets the total counts of the different registered wordpress users
	* who've seen/clicked banenrs/zones
	* @since 1.0.0
	* @return (array) Data
	*/
	public function getUsersCounts($action='banner_click'){

		$table = $this->getTableName();
		$results = array();
		
		$filter_globals = $this->getTimeframeFilterSql($this->filter_timelength);

		$query = "SELECT DATE(s.created), COUNT(s.actioned_id) as total, s.ID, s.action, s.actioned_id, s.user_ip, u.ID, u.user_login AS name " . "\n";  
		$query .= "FROM $table AS s " . "\n";  
		$query .= "LEFT JOIN ".$this->db->base_prefix . "users u ON s.user_id = u.ID "."\n";
		$query .= "WHERE s.action = '".$action."' AND s.user_id > 0 "."\n";
		
		if($this->filter_id > 0)
			$query .= "AND s.actioned_id = " .$this->filter_id . " "."\n";
		if($this->filter_timelength > 0)
			$query .= "AND DATE(s.created) >= '" . $filter_globals['i_date'] . "' "."\n";
		if($this->filter_date_end > 0)
			$query .= "AND DATE(s.created) <= '" . $this->filter_date_end . "' "."\n";
	
		$query .= "GROUP BY s.user_id";
		
		$user_stats = $this->db->get_results($query);

		return $user_stats;
		
	}

	/**
	 * Gets the total counts of the different countries of origin
	 * who've seen/clicked banenrs/zones
	 * @since 1.0.0
	 * @param $actions (string) Actions to include such as 'banner_click'
	 * @return (array) Data
	*/
	public function getCountryCounts($action='banner_click'){

		$table = $this->getTableName();
		$results = array();
		
		$filter_globals = $this->getTimeframeFilterSql($this->filter_timelength);

		$query = "SELECT COALESCE(NULLIF(s.country, ''), 'Unknown') AS name, DATE(s.created), COUNT(s.actioned_id) as total, s.ID, s.action, s.actioned_id, s.user_ip " . "\n";  
		$query .= "FROM $table AS s " . "\n";  
		$query .= "WHERE s.action = '".$action."' AND s.actioned_id = $this->filter_id "."\n";

		if($this->filter_timelength > 0)
			$query .= "AND DATE(s.created) >= '" . $filter_globals['i_date'] . "' "."\n";

		if($this->filter_date_end > 0)
			$query .= "AND DATE(s.created) <= '" . $this->filter_date_end . "' "."\n";
	
		$query .= "GROUP BY s.country";

		$country_stats = $this->db->get_results($query);
	
		return $country_stats;
		
	}

}
?>