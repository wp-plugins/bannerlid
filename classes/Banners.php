<?php

namespace Bannerlid;

/**
 * Provides methods for settings and accessing zone data
 *
 * @since      1.0.0
 * @package    Bannerlid
 * @subpackage Bannerlid/classes
 * @author     Weblid <barrywebla@googlemail.com>
 */
class Banners {

	/**
	* Name of the table in the db the zones are stored (without prefix)
	*/
	private $table = 'bannerlid_banners';

	/**
	* Name of the table that holds the banner/zone relation
	*/
	private $relation_table = 'bannerlid_relations';

		/**
	* Name of the table that holds the banner/zone relation
	*/
	private $banner_post_relation_table = 'bannerlid_banner_post_relations';

	/**
	* Wordpress db dependency
	*/
	private $db;

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
	* Gets the table name for the banner zones
	*
	* @access public
	*/
	public function getTableName(){
		return $this->db->base_prefix . $this->table;
	}

	/**
	* Gets the table name for the banner / zones relations
	*
	* @access public
	*/
	public function getRelationTableName(){
		return $this->db->base_prefix . $this->relation_table;
	}

	/**
	* Gets the table name for the banner / posts relations
	*
	* @access public
	*/
	public function getPostsRelationTableName(){
		return $this->db->base_prefix . $this->banner_post_relation_table;
	}

	/**
	* Gets a single banner by it's id
	*
	* @access public
	* @param (int) $id of the banner to get
	* @return array - Single banner
	*/
	public function get($id){
		$query = $this->db->prepare("SELECT * FROM ".$this->getTableName() . " WHERE ID = %d", $id);
		$result = $this->db->get_row($query, ARRAY_A); 
		return $result;
	}

	/**
	* Gets a single banner by it's slug
	*
	* @access public
	* @param (str) $slug of the banner to get
	* @return array - Single banner
	*/
	public function getBySlug($slug){
		$query = $this->db->prepare("SELECT * FROM ".$this->getTableName() . " WHERE slug = '%s'", $slug);
		$result = $this->db->get_row($query, ARRAY_A); 
		return $result;
	}

	/**
	* Gets a list of the current banners in the database
	*
	* @access public
	* @return array - List of all of the current zones
	*/
	public function getList(){
		$results = $this->db->get_results("SELECT * FROM ".$this->getTableName(), ARRAY_A); 
		return $results;
	}

	/**
	* Adds a banner to the database
	*
	* @access public
	* @return $last_id (int) ID of last inserted banner
	*/
	public function add($name, $slug=null, $file, $url=null, $new_window=null, $width=null, $height=null, $live_date=null, $end_date=null){
		
		$data = array( 
			'name' => $name, 
			'slug' => $slug,
			'file' => $file, 
			'url' => $url, 
			'new_window' => $new_window, 
			'width' => $width, 
			'height' => $height, 
			'live_date' => $live_date, 
			'end_date' => $end_date
		);

		$this->db->insert( $this->getTableName(), $data, array('%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s',));
		return $this->db->insert_id;
	}

	/**
	* Adds data to the post relations table to signify which pages a banner
	* is to be shown on if not shown on all
	*
	* @access public
	* @param (int) $id of the banner
	* @param (array) $post_ids arra of the post ids to show the banner on
	* @return void
	*/
	public function addPostRelations($id, $post_ids){
		if(!empty($post_ids))
		{
			foreach($post_ids as $post)
			{
				$this->db->insert( $this->getPostsRelationTableName(), array( 'banner_id' => $id, 'post_id' => $post), array('%d', '%d'));	
			}
		}
	}

	/**
	* Deletes the relations from the banner/post relations
	* @access public
	* @param $banner_id The ID of the banners to delete
	* @return void
	*/
	public function deletePostRelations($banner_id){
		$this->db->delete( $this->getPostsRelationTableName(), array( 'banner_id' => $banner_id ), array( '%d' ) );
	}

	/**
	* Get's array of posts that the banner is related to
	*
	* @access public
	* @param (int) $banner_id The ID of the banner
	* @return void
	*/
	public function getPostRelations($banner_id){

		$query = $this->db->prepare("SELECT * FROM ".$this->getPostsRelationTableName() . " WHERE banner_id = %d", $banner_id);
		$results = $this->db->get_results($query, ARRAY_A); 
		if(!empty($results)){
			return $results;
		} else {
			return false;
		}
		
	}

	/**
	* Adds records to the zone / banner relation table which tells
	* the system which banners are in which zones.
	*
	* @access public
	* @param (int) $id of the banner
	* @param (array) $zone_ids arra of the zone_ids to put the banner in
	* @return void
	*/
	public function addRelations($id, $zone_ids){
		if(!empty($zone_ids))
		{
			foreach($zone_ids as $zone)
			{
				$this->db->insert( $this->getRelationTableName(), array( 'banner_id' => $id, 'zone_id' => $zone), array('%d', '%d'));	
			}
		}
	}

	/**
	* Get's array of zone relations from given banner_id
	*
	* @access public
	* @param (int) $banner_id The ID of the banner
	* @return void
	*/
	public function getRelations($banner_id){

		$query = $this->db->prepare("SELECT * FROM ".$this->getRelationTableName() . " WHERE banner_id = %d ORDER BY position, id ASC", $banner_id);
		$results = $this->db->get_results($query, ARRAY_A); 
		return $results;
	}

	/**
	* Get's array of zone relations from given zone id
	*
	* @access public
	* @param $banner_id The ID of the banner
	* @return void
	*/
	public function getRelationsByZone($zone_id){
		$query = $this->db->prepare("SELECT * FROM ".$this->getRelationTableName() . " WHERE zone_id = '%d' ORDER BY position, id ASC", $zone_id);
		$results = $this->db->get_results($query, ARRAY_A); 
		return $results;
	}

	/**
	* Takes an array of relation IDs in a particular order. This function 
	* will cycle through the relations and update their position according
	* to whatever order hey are passed to the function. 
	* 
	* @access public
	* @param $banner_id The ID of the banner
	* @return void
	*/

	public function updateZonePositions(array $positions){
	
		if(!empty($positions))
		{
			$i = 1;
			foreach($positions as $position){
				$result = $this->db->update( $this->getRelationTableName(), 
					array('position' => $i), 
					array('ID' => $position), 
					array('%d'), 
					array('%d') 
				);
				$i++;
			}
		}
	}

	/**
	* Deletes the relations from the table with a given banner id
	* @access public
	* @param $banner_id The ID of the banners to delete
	* @return void
	*/
	public function deleteRelations($banner_id){
		$this->db->delete( $this->getRelationTableName(), array( 'banner_id' => $banner_id ), array( '%d' ) );
	}

	/**
	* Updates a banner by it's id
	*
	* @access public
	* @return bool
	*/
	public function update($id, $name, $slug=null, $file, $url=null, $new_window=null, $width=null, $height=null, $live_date=null, $end_date=null){
		$result = $this->db->update( $this->getTableName(), 
			array( 'name' => $name, 'slug' => $slug,'file' => $file, 'url' => $url, 'new_window' => $new_window, 'width' => $width, 'height' => $height, 'live_date' => $live_date, 'end_date' => $end_date), 
			array('ID' => $id), 
			array('%s', '%s', '%s', '%s', '%d'), 
			array('%d') 
		);
		return $result;
	}

	/**
	* Deletes a record from the banners db
	*
	* @access public
	* @param int id of the record to delete
	* @return array - List of all of the current zones
	*/
	public function delete($id){
		$this->db->delete( $this->getTableName(), array( 'id' => $id ), array( '%d' ) );
	}

}


/**
 * Extends the Banners class and provides another level of abstraction 
 * to banner functions. This class provides a blueprint for a real world
 * banner as opposed to Banners which provides the business functions
 *
 * @since      1.0.0
 * @package    Bannerlid
 * @author     Barry Mason <barrywebla@googlemail.com>
 */
class Banner extends Banners {

	/**
	* Holds the raw db schema data for an individual
	* banner row
	*
	* @access public
	*/
	public $data;

	/**
	* Identifies the type of banner by it's file extension
	*
	* @access public
	*/
	public $type;

	/**
	* Constructor - takes in the id or sluf of a banner
	* and sets gets the data from the database and sets
	* it in $this->data
	*
	* @access public
	*/
	public function __construct($banner_id){
		
		parent::__construct();

		if(is_array($banner_id))
		{
			$banner_id = $banner_id['id'];
		}

		if(is_int($banner_id))
		{
			$this->data  = $this->get($banner_id);
		}

		if(is_string($banner_id))
		{
			$banner_slug = $banner_id;
			$this->data = $this->getBySlug($banner_slug);
		}

		$this->data = $this->get($banner_id);
		$this->setFileType();
	}

	/**
	* Extracts the extension from the filename and sets it
	*
	* @see $this->type
	* @access public
	*/
	public function setFileType(){
		$parts = explode(".", $this->data['file']);
		$this->type = strtolower(array_pop($parts));
	}

	/**
	* Getter for the file type
	*
	* @see $this->type
	* @access public
	*/
	public function getFileType(){
		return $this->type;
	}

	/**
	* Checks the banner/post relation table to 
	* see if the is limited to which pages it's on
	*
	* @access public
	*/
	public function checkOnPage(){
		$post_id = get_the_ID();
		
		$relations = $this->getPostRelations($this->data['ID']);
		$post_sequential=array();
    	if(!empty($relations))
	    	$c = array_map(function($val) use (&$post_sequential){ $post_sequential[] = $val['post_id']; }, $relations);  
		if(in_array($post_id, $post_sequential) || empty($post_sequential)){
			return true;
		} else {
			return false;
		}
		
	}

	/**
	* Checks if the banner is live according to publish/unpublish dates
	*
	* @access public
	* @param int id of the banner
	* @return bool - Published or not
	*/
	public function checkBannerPublished(){
		$now = date("Y-m-d H:i:s");
		
		$this->data['live_date'] == "0000-00-00 00:00:00" || $this->data['live_date'] < $now ? $publish = true : $publish = false;
		$this->data['end_date'] == "0000-00-00 00:00:00" || $this->data['end_date'] > $now ? $unpublish = true : $unpublish = false;

		if($publish && $unpublish)
			return true;
		else
			return false;
	}

	/**
	* Gets the image or the flash file of the banner
	*
	* @access public
	* @param $width (int) Acts as a width override. If value is sent, it will override default width
	* @param $height (int) Acts as a height override. If value is sent, it will override default width
	* @return Banner html
	*/
	public function getBannerImage($width=null, $height=null){

        if($this->data['file'])
        {

			$output = '';        	
        	
        	//
        	// Check the publish dates of the banner
        	//	
        	if(!$this->checkBannerPublished())
        		return;

        	//
        	// If it's not a flash file then output as image
        	//
            if($this->getFileType() !== "swf")
            { 

            	//
            	// See if we have any overriding h/w dimensions and if we have
            	// then we apply an inline css style
            	//
            	if(!is_null($width) || !is_null($height))
            	{
            		$width > 1 ? $width = 'max-width:'.$width . 'px; ' : $width = null;
            		$height > 1 ? $height = 'max-height:'.$height . 'px; ' : $height = null;
            	} 
            	else 
            	{
            		if($this->data['width'] > 1) $width = 'max-width: '.$this->data['width'] . "px";
            		if($this->data['height'] > 1) $height = 'max-height: '.$this->data['height'] . "px";
            	}

            	//Output html
                $output .= '<img src="'.$this->data['file'].'" class="bannerlid-banner bannerlid-img" style="'.$width.$height.'" alt="'.$this->data['name'].'" />'. "\n";

            } 
        	//
        	// If it is a flash file
        	//
            else {

            	// Get the proper dimensions of the flash file
            	$swf_info = getimagesize($this->data['file']);
            	$width > 1 ? $width = $width : $width = $swf_info[0];
            	$height > 1 ? $height = $width : $height = $swf_info[1];

            	// If there is a url then we need to put the flash file under our clickable div
            	if(!empty($this->data['url']))
            	{
	            	$style = 'position: relative; z-index: -1; pointer-events: none;"';
            	}
            	
            	//Output html
                $output .= '<object width="'.$width.'" height="'.$height.'" style="'.$style.'">' . "\n";
                $output .= '<param name="movie" value="'.$this->data['file'].'"></embed>' . "\n";
                $output .= '</object>'. "\n";

            }

            return $output;
        }
	}

	/**
	* Gets the banner preview 
	*
	* @access public
	* @return Preview html
	*/
	public function showPreview(){

        if($this->data['file'])
        {
        	$output = '';
            if($this->getFileType() !== "swf")
            { 
                $output .= '<img src="'.$this->data['file'].'" class="banner-preview" style="max-width: 200px; max-height: 50px;" alt="'.__('Banner Preview', 'bannerlid') .'" />&nbsp;';
            } 
            else 
            {
                $output .= '<img src="'.plugins_url( '/assets/img/swf_icon.png', dirname(__FILE__) ).'" class="banner-preview" height="50px" alt="'.__('Banner Preview', 'bannerlid') .'" />&nbsp;';
            }
            return $output;
        }
	}
}


?>