<?php

namespace Bannerlid;

/**
 * Provides methods for settings and accessing stats
 *
 * @author Barry Mason
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
		$this->db->insert( $this->getTableName(), array( 'action' => $action, 'actioned_id' => $actioned_id,'user_id' => $user_id, 'user_ip' => $user_ip, 'country' => $country, 'browser' => $browser, 'created' => current_time('mysql', 1)));

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
	public function addBannerStat($type, $banner_id, $user_id, $user_ip, $browser){
		if(get_option('bannerlid-collect-stats') != 'true')
			return;
		$this->add($type, $banner_id, $user_id, $user_ip, '', $browser['name']);
	}
}

?>