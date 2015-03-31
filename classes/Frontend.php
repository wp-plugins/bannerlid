<?php

namespace Bannerlid;

/**
 * Methods for showing banners / zones and collecting stats through
 * hooks and actions. 
 *
 * @since      1.0.0
 * @author     Barry Mason <barrywebla@googlemail.com>
 */
class Frontend {

	/**
	 * @var The single instance of the class
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * @var Holds a instance of wpdb for scope access
	 * @since 1.0.0
	 */
	private $db;

	/**
	 * @var Dependency injected banners model
	 * @since 1.0.0
	 */
	private $banners_obj;

	/**
	 * @var Dependency injected zones model
	 * @since 1.0.0
	 */
	private $zones_obj;


	/**
	 * Instantiates the frontend class and takes it's dependencies
	 * as arguments which will be set in this class
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		
		global $wpdb;
		$this->db = $wpdb;

		$this->stats = new Stats($wpdb);

		$this->banners_obj = new Banners();
		$this->zones_obj = new Zones();
		
		$this->setupShortcodes();
		$this->setupActions();

	}

	/**
	 * Create a single instance of the class
	 *
	 * @since 1.0.0
	 * @access public
	 * @param (Banners) Instance of Banners model class
	 * @param (Zones) Instance of Zones model class
	 * @return instance of slef class
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Registers shortcodes with Wordpress and designates 
	 * methods to action
	 *
	 * @since 1.0.0
	 * @access public
	*/
	public function setupShortcodes(){
		add_shortcode("banner", array($this, "showBanner"));
		add_shortcode("zone", array($this, "showZone"));
	}

	/**
	 * Registers actions 
	 *
	 * @since 1.0.0
	 * @access public
	*/
	public function setupActions(){
		add_action('wp_loaded',array($this, "doRedirect"));
	}

	/**
	 * Outputs a zone's banners 
	 *
	 * @since 1.0.0
	 * @see setupShortcodes()
	 * @param (array) $atts  Attributes as sent with shortcode
	 * @access public
	 * @return (str) Zone html
	*/
	public function showZone($atts){

		isset($atts['width']) ? $width =  $atts['width'] : $width = null;
		isset($atts['height']) ? $height =  $atts['height'] : $height = null;

		$zone = new Zone($atts);
		
		$output = '';

		$banner_list = $zone->getBanners();

		if(empty($banner_list)){
			return;
		}

		//
		// If it's a randomized zone then we'll sliace a random value 
		// from our banner array
		//
		if($zone->data['type'] == "randomize"){
			$random = rand(0, count($zone->getBanners()));
			$banner_list = array_slice($zone->getBanners(), $random-1, 1);
		}
		// Add the impression statistic
		$this->stats->addBannerStat("zone_impression", $zone->data['ID'], get_current_user_id(), $this->getClientIp(), $this->getClientBrowser());

		do_action('bannerlid_showzone', $zone );

		//
		// Output the banners
		//
		foreach($banner_list as $banner){
			$banner_row = $this->banners_obj->get($banner['banner_id']);		
			$output .= $this->showBanner(array("id" => intval($banner_row['ID']), "width" => $width, "height" => $height));
		}

		return $output;
	}

	/**
	 * Returns the html for a banner. This is a wrapper for
	 * the getBannerImage() function in the banners class. This
	 * method is concerned with the linking of the image whereas
	 * the getBannerImage() is concerned with the img/flash
	 *
	 * @since 1.0.0
	 * @access public
	 * @param (array) $atts Attributes as sent with shortcode
	 * @return (str) Bnner html
	*/
	public function showBanner($atts){
		
		isset($atts['width']) ? $width =  $atts['width'] : $width = null;
		isset($atts['height']) ? $height =  $atts['height'] : $height = null;

		$banner = new Banner($atts);
		$banner->data['new_window'] == 1 ? $new_window = 'target = "_blank"' : $new_window='';
		$this->stats->addBannerStat("banner_impression", $banner->data['ID'], get_current_user_id(), $this->getClientIp(), $this->getClientBrowser());

		do_action('bannerlid_showbanner', $banner );

		if(!empty($banner->data['url']))
			$link = $this->makeLink($banner->data['ID']);	

		//
		// If we have a flash file
		//
		if($banner->getFileType() == 'swf'){

			//
			// If there is a link then we need to make sure the a tag is placed on 
			// top of the flash object or the click will not be registered.
			//
			if(isset($link)) $link_style = 'display: inline-block; position: relative; z-index: 1;';

			if(isset($link_style)){
				return '<a style="'.$link_style.'" href="'.$link.'" '.$new_window.'><span>'.$banner->getBannerImage($width, $height).'</span></a>';
			} 
			//
			// If there is no link then we don't wrap the flash object and the 
			// flash file internal link will be clickable.
			//
			else {
				return $banner->getBannerImage($width, $height);
			}
		}
		//
		// If we don't a flash file
		// 
		else {
			if(isset($link)){
				return '<a href="'.$link.'" '.$new_window.'>'.$banner->getBannerImage($width, $height).'</a>';
			} else {
				return $banner->getBannerImage($width, $height);
			}
		}
		
	}

	/**
	 * Returns the conversion link to place on banners to point them
	 * to our middle page which tracks click and then forwards use to 
	 * correct site. 
	 *
	 * @since 1.0.0
	 * @access public
	 * @param (int) $banner_id ID of clicked banner
	 * @return (str) href link
	*/
	public function makeLink($banner_id){
		$link = home_url() . '?bannerlidlink='.$banner_id;
		return $link;
	}

	/**
	 * Funcion called from hook. This acts as the middle man conversion
	 * page which adds the click data to the database and then forwards
	 * user to the correct page. 
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	*/
	public function doRedirect(){

		if(!isset($_GET['bannerlidlink']))
			return;

		$banner_id = $_GET['bannerlidlink'];
		$banner = new Banner($banner_id);
		
		if(empty($banner->data['url']))
			return;

		$this->stats->addBannerStat("banner_click", $banner_id, get_current_user_id(), $this->getClientIp(), $this->getClientBrowser());
		do_action('bannerlid_redirect', $banner );
		
		wp_redirect($banner->data['url']);
		exit();
	}

	/**
	 * Retrieves client's IP address
	 *
	 * @since 1.0.0
	 * @access private
	 * @return (str) Users' IP
	*/
	private function getClientIp(){
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			    $user_ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			    $user_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
			    $user_ip = $_SERVER['REMOTE_ADDR'];
		}
		return $user_ip;
	}

	/**
	 * Retrieves client's browser
	 *
	 * @since 1.0.0
	 * @access private
	 * @return (str) Users' IP
	*/
	private function getClientBrowser() 
	{ 
	    $u_agent = $_SERVER['HTTP_USER_AGENT']; 
	    $bname = 'Unknown';
	    $platform = 'Unknown';
	    $version= "";

	    if (preg_match('/linux/i', $u_agent)) {
	        $platform = 'linux';
	    }
	    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
	        $platform = 'mac';
	    }
	    elseif (preg_match('/windows|win32/i', $u_agent)) {
	        $platform = 'windows';
	    }
	    
	    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) { 
	        $bname = 'Internet Explorer'; 
	        $ub = "MSIE"; 
	    } 
	    elseif(preg_match('/Firefox/i',$u_agent)) { 
	        $bname = 'Mozilla Firefox'; 
	        $ub = "Firefox"; 
	    } 
	    elseif(preg_match('/Chrome/i',$u_agent)) { 
	        $bname = 'Google Chrome'; 
	        $ub = "Chrome"; 
	    } 
	    elseif(preg_match('/Safari/i',$u_agent)) { 
	        $bname = 'Apple Safari'; 
	        $ub = "Safari"; 
	    } 
	    elseif(preg_match('/Opera/i',$u_agent)) { 
	        $bname = 'Opera'; 
	        $ub = "Opera"; 
	    } 
	    elseif(preg_match('/Netscape/i',$u_agent)) { 
	        $bname = 'Netscape'; 
	        $ub = "Netscape"; 
	    } 

	    $known = array('Version', $ub, 'other');
	    $pattern = '#(?<browser>' . join('|', $known) .
	    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
	    if (!preg_match_all($pattern, $u_agent, $matches)) { }
	    
	    $i = count($matches['browser']);
	    if ($i != 1) {
	        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
	            $version= $matches['version'][0];
	        }
	        else {
	            $version= $matches['version'][1];
	        }
	    }
	    else {
	        $version= $matches['version'][0];
	    }
	    
	    if ($version==null || $version=="") {$version="?";}
	
	    return array(
	        'userAgent' => $u_agent,
	        'name'      => $bname,
	        'version'   => $version,
	        'platform'  => $platform,
	        'pattern'    => $pattern
	    );
	} 
	
}

?>