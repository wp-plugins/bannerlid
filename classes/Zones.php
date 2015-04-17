<?php

namespace Bannerlid;

/**
 * Provides methods for settings and accessing zone data
 *
 * @package    Bannerlid
 * @subpackage Bannerlid/classes
 * @author Weblid
 */
class Zones {

	/**
	* Name of the table in the db the zones are stored (without prefix)
	*/
	private $table = 'bannerlid_zones';

	/**
	* Wordpress db dependency
	*/
	private $db;

	/**
	* Constructor sets up dependencies
	* @see 'public $db'
	* @access public
	*/
	public function __construct(){
		global $wpdb;
		$this->db = $wpdb;
	}

	/**
	* Gets the table name for the banner zones
	* @access public
	*/
	public function getTableName(){
		return $this->db->base_prefix . $this->table;
	}

	/**
	* Gets a list of the current zones in the database
	* @access public
	* @return array - List of all of the current zones
	*/
	public function add($type, $name, $slug=null, $description){
		$data = array('type' => $type, 'name' => $name, 'slug' => $slug,'description' => $description);
		$this->db->insert( $this->getTableName(), $data);
		
	}

	/**
	* Updates a zone by it's id
	* @access public
	* @return array - List of all of the current zones
	*/
	public function update($id, $type, $name, $slug=null, $description){
		$result = $this->db->update( $this->getTableName(), 
			array('type' => $type, 'name' => $name, 'slug' => $slug,'description' => $description), 
			array('ID' => $id), 
			array('%s', '%s', '%s', '%s'), 
			array('%d') 
		);
		return $result;
	}

	/**
	* Deletes a record from the banners db
	* @access public
	* @param int id of the record to delete
	* @return array - List of all of the current zones
	*/
	public function delete($id){
		$this->db->delete( $this->getTableName(), array( 'ID' => $id ), array( '%d' ) );
	}


	/**
	* Gets a single zone by it's id
	* @access public
	* @param int id of the zone to get
	* @return array - List of all of the current zones
	*/
	public function get($id){
		$result = $this->db->get_row("SELECT * FROM ".$this->getTableName() . " WHERE ID = " . $id, ARRAY_A); 
		return $result;
	}

	/**
	* Gets a single banner by it's slug
	* @access public
	* @param str slug of the banner to get
	* @return array - Single banner
	*/
	public function getBySlug($slug){
		$result = $this->db->get_row("SELECT * FROM ".$this->getTableName() . " WHERE slug = '" . $slug . "'", ARRAY_A); 
		return $result;
	}

	/**
	* Gets a list of the current zones in the database
	* @access public
	* @return array - List of all of the current zones
	*/
	public function getList(){
		$results = $this->db->get_results("SELECT * FROM ".$this->getTableName(), ARRAY_A); 
		return $results;
	}
}

/**
* Extends the Zones class and provides another level of abstraction 
* to zone functions. This class provides a blueprint for a real world
* zone as opposed to Zones which provides the business functions
 * @since      1.0.0
 * @package    Bannerlid
 * @author     Barry Mason <barrywebla@googlemail.com>
 */
class Zone extends Zones {

	/**
	* Holds the raw db schema data for an individual
	* zone row
	*
	* @access public
	*/
	public $data;

	public $banners;

	/**
	* Constructor - takes in the id or sluf of a zone
	* and sets gets the data from the database and sets
	* it in $this->data
	*
	* @access public
	*/
	public function __construct($zone_id){
		
		parent::__construct();

		if(is_array($zone_id)){
			$zone_id = $zone_id['id'];
		}
		if(is_int($zone_id)){
			$this->data  = $this->get($zone_id);
		}
		if(is_string($zone_id)){
			$zone_slug = $zone_id;
			$this->data = $this->getBySlug($zone_slug);
		}

		$this->data = $this->get($zone_id);
		$this->setBannerArray();
	
	}

	public function setBannerArray(){
		$banners = new Banners();
		$banner_list = $banners->getRelationsByZone($this->data['ID']);
		$this->banners = $banner_list;
	}

	public function getBanners(){
		return $this->banners;
	}
}

?>