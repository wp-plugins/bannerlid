<?php

namespace Bannerlid;

/**
 * This class helps create Wordpress CMS pages
 *
 *
 * @since      1.0.0
 * @package    Bannerlid
 * @subpackage Bannerlid/classes
 */


/**
 * Helps create top level Wordpress CMS page
 *
 * @since      1.0.0
 * @author     Barry Mason <barrywebla@googlemail.com>
 */
class AdminPage {

    /**
    * Lower case alphanumeric unique id
    *
    * @var $slug
    */
    private $slug;

    /**
    * Title of the page
    *
    * @var $title
    */
    private $title;
    
    /**
    * The html template file (should be in views dir)
    * and should not have .php extension in string
    *
    * @var $html
    */
    private $html;
    
    /**
    * The model object containing the business logic methods
    *
    * @var $dataobj
    */
    private $dataobj;

    /**
    * Intantiate the page with the dependant data
    *
    * @access public
    * @return self object
    */
    public function __construct($title, $slug, $html, $dataobj){
        $this->slug = $slug;
        $this->title = $title;
        $this->html = $html;
        $this->dataobj = $dataobj;

        return $this;
    }

    /**
    * Getter for $this->slug
    *
    * @access public
    * @return (str) slug
    */
    public function getSlug(){
        return $this->slug;
    }

    /**
    * Getter for $this->title
    *
    * @access public
    * @return (str) title
    */
    public function getTitle(){
        return $this->title;
    }

    /**
    * Callback function which calls the Wordpress function to register
    * the page in the Wordpress systsem.
    *
    * @access public
    * @return void
    */
    public function addPage()
    {
        add_menu_page( __($this->getTitle(), $this->getSlug()), __($this->getTitle(), $this->getSlug()), 'manage_options', $this->getSlug(), array($this, 'render'), 'dashicons-media-interactive', 60 );
    }

    /**
    * Adds the actions we need to set up the admin page
    *
    * @access public
    * @return void
    */
    public function register(){
        add_action( 'admin_menu', array($this, 'addPage' ));
    }

    /**
    * Outputs the html template as physical html
    *
    * @access public
    * @return void
    */
    public function render()
    {
        echo Template::get($this->html, $this->dataobj);
    }

}

/**
 * Helps create sub pages in the Wordpress CMS. Extends
 * the AdminPage class which must always be instantiated 
 * as we can't have sub pages without the main page.
 *
 * @since      1.0.0
 * @author     Barry Mason <barrywebla@googlemail.com>
 */
class AdminSubPage extends AdminPage{

    /**
    * Reference the parent classes slug - this
    * is the parent page slug as we are ceating sub 
    * pages here.
    *
    * @access private
    * @var $parent_slug
    */
    private $parent_slug;

    /**
    * Get the sub page details and call the parent class
    * construct to set up our page variables. 
    *
    * @access public
    * @return (AdminSubPage) 
    */
    public function __construct($parent_slug, $title, $slug, $html, $dataobj){
        $this->parent_slug = $parent_slug;
        parent::__construct($title, $slug, $html, $dataobj);
        return $this;
    }

    /**
    * Callback function which calls the Wordpress function to register
    * the subpage in the Wordpress systsem.
    *
    * @access public
    * @return void
    */
    public function addPage()
    {
        add_submenu_page($this->parent_slug, __($this->getTitle(), $this->getSlug()), __($this->getTitle(), $this->getSlug()), 'manage_options', $this->getSlug(), array($this, 'render'));
    }

    /**
    * Adds the actions we need to set up the admin subpage
    *
    * @access public
    * @return void
    */
    public function register(){
        add_action( 'admin_menu', array($this, 'addPage' ));
    }

}

?>