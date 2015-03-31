<?php
namespace Bannerlid;

/**
 * This class deals with parsing the content for the wp-admin pages.
 * 
 * @since      1.0.0
 * @package    Bannerlid
 * @author     Barry Mason <barrywebla@googlemail.com>
 */

class Template {
	/**
	* Gets the html template from the views folder and returns 
	* it as a variable. 
	*
	* @access public
	* @param (str) $template Name of template to load discluding the extension 
	* @param (object) $dataobj The model which retrieves the data for the view. 
	* @return void
	*/
	public static function get($template, $dataobj){
		ob_start();
		include plugin_dir_path( dirname( __FILE__ ) ) . 'views/'.$template.'.php';
		return ob_get_clean();
	}

}


?>