<?php
namespace Bannerlid;

/**
 * This class deals with parsing the content for the wp-admin pages.
 * 
 * @since      1.0.0
 * @package    Bannerlid
 * @subpackage Bannerlid/classes
 * @author     Weblid <barrywebla@googlemail.com>
 */

class Template {


	public static function getViewPath(){
		return plugin_dir_path( dirname( __FILE__ ) ) . 'views/';
	}

	/**
	 * Returns the common search form used to filter banners and zones
	 *
	 * @access private
	 * @return (string) form filter html
	 */
	private static function getSearchForm(){

		ob_start();
		include self::getViewPath() . 'FormFilter.php';
        return ob_get_clean();
	}

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

		isset($_GET['subpage']) ? $subpage = $_GET['subpage'] : $subpage = null;
		isset($_GET['action']) ? $action = $_GET['action'] : $action = null;
		isset($_GET['id']) ? $id = $_GET['id'] : $id = null;

		ob_start();
		include self::getViewPath() .$template.'.php';
		return ob_get_clean();
	
	}

	/**
	 * Outputs admin tabs 
	 *
	 * @access public
	 * @param (array) $tabs - Array of strings to declare tab titles
	 * @return void
	 */
	public static function showTabs($tabs){

		if(!empty($tabs)): 
			$i = 0;
			?>
			<h2 class="nav-tab-wrapper">
		        <?php foreach ($tabs as $option_tab) :

		            $nav_class = 'nav-tab';
		            $page = $_GET['page'];
		        	   
		            if ( (isset($_GET['subpage']) && $option_tab == $_GET['subpage']) || (!isset($_GET['subpage']) && $i == 0) ) {
		                $nav_class .= ' nav-tab-active'; 
		                $tab_forms[] = $option_tab; 
		            }
		            
		            $link = menu_page_url( $page , false);
		            if(isset($option_tab))
		            	$link .= '&amp;subpage=' . esc_attr($option_tab);
		            if(isset($_GET['id']))
		            	$link .= '&amp;id=' . esc_attr($_GET['id']);
		        	?>              
		        	<a class="<?php echo $nav_class; ?>" href="<?php echo $link; ?>"><?php esc_attr_e($option_tab); ?></a>
		        <?php 
		        $i++;
		        endforeach; ?>
	    	</h2>
    	<?php endif; 
	}

}


?>