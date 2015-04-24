<?php

namespace Bannerlid;

/**
 * Consumes the http://ip-api.com/ API to find country based
 * on users' IP address.
 *
 * @package    Bannerlid
 * @subpackage Bannerlid/classes
 * @author Weblid
 */
class CountryFinder {

	private $ip;
	private $endpoint = 'http://ip-api.com/json/';
	public $response;

	/**
	* Constructor sets up dependencies
	* @see 'public $db'
	* @access public
	*/
	public function __construct($ip){
		$this->ip = $ip;
		$this->doCountryCode();

	}


	public function doCountryCode(){
		$this->response = (array)json_decode(file_get_contents($this->endpoint . $this->ip));
		if(is_array($this->response)){
			return true;
		}else {
			return false;
		}
	}

	public function getCountryString(){
		if($this->response['status'] != 'fail'){
			$country = $country_finder->response['country'];
		} else {
			$country = "";
		}	
		return $country;
	}
	
}


?>