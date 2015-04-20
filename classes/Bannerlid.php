<?php

namespace Bannerlid;

/**
 * The core plugin class.
 *
 * Delegates to other classes and gets the ball rolling 
 * 
 *
 * @since      1.0.0
 * @package    Bannerlid
 * @subpackage Bannerlid/classes
 * @author     Weblid <barrywebla@googlemail.com>
 */
class Bannerlid {

	/**
	 * @var The single instance of the class
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string $version The current version of the plugin.
	 */
	protected $version;


	/**
	 * The zone model object used to interface with the zones data in the db
	 *
	 * @since 1.0.0
	 * @access public
	 * @var object $zones Zone object
	 */
	public $zones;

	/**
	 * The banners model object used to interface with the banners data in the db
	 *
	 * @since 1.0.0
	 * @access public
	 * @var object $banners banners object
	 */
	public $banners;

	/**
	 * The adminPages class which is used to set up Wordpress cms pages
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public $admin_page;

	/**
	 * The frontend class which handles front facing functionality
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public $frontend;


	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since 1.0.0
	 * @return string The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}


	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since 1.0.0
	 * @return string The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Let's get the party started! Set up Bannerlid info vars and call 
	 * any functions to start loading our environment.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'Bannerlid';
		$this->version = '1.1.0';
		$this->environment();

	}

	/**
	 * Create a single instance of the class
	 *
	 * @since 1.0.0
	 * @access private
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * Load the required dependencies for this plugin.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function load_dependencies() {
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'classes/AdminPages.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'classes/Template.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'classes/Banners.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'classes/BannersTable.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'classes/Chart.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'classes/CountryFinder.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'classes/Frontend.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'classes/Options.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'classes/Stats.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'classes/Zones.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'classes/ZonesTable.php';
	}

	/**
	 * Call functions which setup our environment such as creating 
	 * Wordpress pages as well as the frontend actions
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function environment(){

		add_action( 'admin_init', array($this, 'load_admin_js') );
		add_action( 'admin_init', array($this, 'load_admin_css') );
		add_action( 'admin_init', array($this, 'textdomains') );

		add_filter('upload_mimes',array($this, 'allow_mimes'));

		$this->load_dependencies();
		$this->loadAdminPages();
		$this->load_frontend();
	}

	/**
	 * Callback to load localization info
	 *
	 * @since 1.0.0
	 * @see environment()
	 * @access public
	 */
	public function textdomains(){
		load_plugin_textdomain('bannerlid', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}
	/**
	 * Callback to load admin side javascript files needed in the management 
	 * of the zones and banners.
	 *
	 * @since 1.0.0
	 * @see environment()
	 * @access public
	 */
	public function load_admin_js() {
		wp_enqueue_media();
        wp_enqueue_script('media_button', plugins_url( '../js/media.js', __FILE__ ), array('jquery'), $this->version, true);
        wp_enqueue_script('jquery_ui', 'https://code.jquery.com/ui/1.11.4/jquery-ui.min.js', array('jquery'), '1.11.4', true);
        wp_enqueue_script('bannerlid-sorting', plugins_url( '../js/sorting.js', __FILE__ ), array('jquery', 'jquery_ui'), $this->version, true);    

        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script('jquery-ui-timepicker', plugins_url( '../js/Timepicker-1.5.2/timepicker.js', __FILE__ ), array('jquery', 'jquery-ui-datepicker'), $this->version, true);
        wp_enqueue_script('bannerlid-forms', plugins_url( '../js/form.js', __FILE__ ), array('jquery', 'jquery_ui'), $this->version, true);    

        wp_enqueue_script('chart_js', plugins_url( '../js/Chart.js-master/Chart.js', __FILE__ ), array('jquery'), $this->version, true);
        wp_enqueue_script('bannerlid-chart', plugins_url( '../js/chart.js', __FILE__ ), array('jquery', 'chart_js'), $this->version, true);

    }

	/**
	 * Callback to load admin side css files needed in the styling 
	 * of the zones and banners management.
	 *
	 * @since 1.0.0
	 * @see environment()
	 * @access public
	 */
    public function load_admin_css() {
    	wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
    	wp_enqueue_style('timepicker-style', plugins_url( '../js/Timepicker-1.5.2/timepicker.css', __FILE__ ));
		wp_enqueue_style( 'bannerlid-admin-css', plugins_url( '../assets/css/admin.css', __FILE__ ), null, $this->version );

    }

	/**
	 * Sets up the pages we need in the wp-admin to manage the banners. This
	 * creates the models we need in our admin pages and passes in the models
	 * objects as dependencies and then uses the adminPage class to set up the 
	 * pages
	 *
	 * @since 1.0.0
	 * @see environment()
	 * @access private
	 */
	private function loadAdminPages(){

		$this->banners_obj = new Banners();
		$admin_page = new AdminPage('Banners', 'bannerlid', 'AdminBanners', $this->banners_obj);
		$admin_page->register();

		$this->zones_obj = new Zones();
		$zones_page = new AdminSubPage('bannerlid', 'Zones', 'bannerlid-zones', 'AdminZones', $this->zones_obj );
		$zones_page->register();
		/*
		$this->options_obj = new Options();
		$zones_page = new AdminSubPage('bannerlid', 'Options', 'bannerlid-options', 'AdminOptions', $this->options_obj );
		$zones_page->register();
		*/
		$admin_page = new AdminSubPage('bannerlid', 'Stats', 'bannerlid-stats', 'AdminStats', null );
		$admin_page->register();


	}

	/**
	 * Instantiate the fornend class which outputs banners / zones and tracks
	 * stats in the frontend.
	 *
	 * @since 1.0.0
	 * @see environment()
	 * @access private
	 */
	private function load_frontend() {
		$this->frontend = Frontend::instance();
	}

	/**
	* Callback to allow the upload of swf files
	*
	* @see environment()
	* @access public 
	* @return a list of acceptable upload file types
	*/
	public function allow_mimes($mimes) {
		
		//if(get_option('bannerlid-enable-flash') != 'true')
		//	return;

		$mimes['swf'] = 'application/x-shockwave-flash';
		return $mimes;
	}



}