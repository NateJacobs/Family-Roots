<?php

/**
 *	Plugin Name: 	Family Roots
 *	Description: 	The Next Generation of Genealogy Site Building and WordPress Integration.
 *	Version: 		0.1
 *	Date:			10/28/12
 *	Author:			Nate Jacobs
 *	Author URI:		https://github.com/NateJacobs
 */

/** 
 *	This class sets up all the required files and constants for the plugin. 
 *
 *	@author		Nate Jacobs
 *	@date		10/28/12
 *	@since		0.1
 */
class FamilyRootsLoad {
	/** 
 	 *	Hook into WordPress and prepare all the methods as necessary.
 	 *
 	 *	@author		Nate Jacobs
 	 *	@date		10/28/12
 	 *	@since		0.1
 	 */
  	public function __construct() {
 		add_action('plugins_loaded', [$this, 'constants'], 1);
		add_action('plugins_loaded', [$this, 'includes'], 2);
		add_action('plugins_loaded', [$this, 'admin'], 3);
		register_activation_hook(__FILE__, [$this, 'activation']);
		register_deactivation_hook(__FILE__, [$this, 'deletion']);
 	}
 	
	/**
	 *	Plugin Constants
	 *
	 *	Constants used throughout plugin are defined for later use.
	 *
	 *	@author		Nate Jacobs
	 *	@date		10/28/12
	 *	@since 		0.1
	 */	
	public function constants() {
		define('FAMROOTS_DIR', trailingslashit(plugin_dir_path(__FILE__)));
		define('FAMROOTS_URI', trailingslashit(plugin_dir_url(__FILE__)));
		define('FAMROOTS_INCLUDES', FAMROOTS_DIR.trailingslashit('inc'));
		define('FAMROOTS_ADMIN', FAMROOTS_DIR.trailingslashit('admin'));
		define('FAMROOTS_TEMPLATES', FAMROOTS_DIR.trailingslashit('templates'));
		define('FAMROOTS_FAMILY', FAMROOTS_DIR.trailingslashit('family'));
		define('FAMROOTS_PERSON', FAMROOTS_DIR.trailingslashit('person'));
	}
	
	/**
	 *	Include Files
	 *
	 *	Lists the files used for plugin actions in the includes folder.
	 *	They are stored in the inc folder. These files are able to be accessed from both front and back end of site.
	 *
	 *	@author		Nate Jacobs
	 *	@date		10/28/12
	 *	@since 		0.1
	 */
	public function includes() {
		require_once(FAMROOTS_INCLUDES . 'class-tool-bar.php');
		require_once(FAMROOTS_INCLUDES . 'class-tng-db.php');
		require_once(FAMROOTS_INCLUDES . 'class-rewrite-template.php');
		require_once(FAMROOTS_INCLUDES . 'class-utilities.php');
		require_once(FAMROOTS_FAMILY . 'class-family-query.php');
		require_once(FAMROOTS_FAMILY . 'class-family.php');
		//require_once(FAMROOTS_INCLUDES . 'users.php');
	}
	
	/**
	 *	Admin Files
	 *
	 *	Lists the files used for plugin actions in the admin dashboard. 
	 *	They are stored in the admin folder. These files are only able to accessed from the back end of the site
	 *
	 *	@author		Nate Jacobs
	 *	@date		10/28/12
	 *	@since 		0.1
	 */
	public function admin() {
		if(is_admin()) {
			require_once(FAMROOTS_ADMIN . 'class-settings.php');
		}
	}
	
	/** 
	 *	Runs the method when the plugin is activated.
	 *	Attempts to locate the TNG file directory/path.
	 *	If the path is found: update the wp options db and get tng db values.
	 *	Add tng capabilities to WordPress administrator roles. Add new TNG roles
	 *
	 *	@author		Nate Jacobs
	 *	@date		10/28/12
	 *	@since		0.1
	 */
	public function activation() {
		include_once(plugin_dir_path(__FILE__).'/inc/utilities.php');
		flush_rewrite_rules();
		
		$utilities = new FamilyRootsUtilities();
		
		$path = $utilities->get_path();
		
		// if the path is not empty, add option to db
		if(!empty($path)) {
			add_option('family-roots-settings', ['tng_path' => trailingslashit($path)]);
			$utilities->get_tng_db_values();
		}		
	}
	
	/** 
	 *	Runs the method when the plugin is deleted.
	 *
	 *	@author		Nate Jacobs
	 *	@date		11/20/12
	 *	@since		0.1
	 */
	public function deletion() {
		// remove family-roots-settings, family-roots-users-settings, family-roots-advanced-settings
		// remove roles and capabilities
	}
}

$family_roots_load = new FamilyRootsLoad();