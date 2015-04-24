<?php

namespace Bannerlid;

/**
 * Provides methods for settings and accessing option data
 *
 * @package    Bannerlid
 * @subpackage Bannerlid/classes
 * @author Weblid
 */
class Options {

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

	
}


?>