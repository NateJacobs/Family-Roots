<?php

/**
 *	Plugin Name: 	Family Roots
 *	Description: 	The Next Generation of Genealogy Site Building and WordPress Integration.
 *	Version: 		0.1
 *	Date:			10/28/12
 *	Author:			Nate Jacobs
 *	Author URI:		https://github.com/NateJacobs
 */
 
// This plugin builds upon the work by Mark Barnes in his TNG Wordpress Integration Plugin.
// The original can be found at http://www.4-14.org.uk/wordpress-plugins/tng.

familyRootsLoad::init();

/** 
*	Family Roots Load
*
*	This class sets up all the required files and constants for the plugin. 
*
*	@author		Nate Jacobs
*	@date		10/28/12
*	@since		0.1
*/
class familyRootsLoad
{
	/** 
 	*	Initialize
 	*
 	*	Hook into WordPress and prepare all the methods as necessary.
 	*
 	*	@author		Nate Jacobs
 	*	@date		10/28/12
 	*	@since		0.1
 	*
 	*	@param		null
 	*/
  	public static function init()
 	{
 		add_action( 'plugins_loaded', array( __CLASS__, 'constants' ), 1 );
		add_action( 'plugins_loaded', array( __CLASS__, 'includes' ), 2 );
		add_action( 'plugins_loaded', array( __CLASS__, 'admin' ), 3 );
		add_action( 'init', array( __CLASS__, 'localization' ) );
		register_activation_hook( __FILE__, array( __CLASS__, 'activation' ) );
		register_deactivation_hook( __FILE__, array( __CLASS__, 'deletion' ) );
 	}
 	
 	/** 
 	*	Localization
 	*
 	*	Add support for localization
 	*
 	*	@author		Nate Jacobs
 	*	@date		10/28/12
 	*	@since		1.0
 	*
 	*	@param		
 	*/
 	public function localization() 
 	{
  		load_plugin_textdomain( 'family-roots-integration', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' ); 
	}
 	
	/**
	 *	Plugin Constants
	 *
	 *	Constants used throughout plugin are defined for later use.
	 *
	 *	@author		Nate Jacobs
	 *	@date		10/28/12
	 *	@since 		0.1
	 *
	 *	@param		null
	 */	
	public function constants() 
	{
		define( 'FAMROOTS_VERSION', '0.1' );
		define( 'FAMROOTS_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
		define( 'FAMROOTS_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );
		define( 'FAMROOTS_INCLUDES', FAMROOTS_DIR.trailingslashit( 'inc' ) );
		define( 'FAMROOTS_ADMIN', FAMROOTS_DIR.trailingslashit( 'admin' ) );
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
	 *
	 *	@param		null
	 */
	public function includes()
	{
		require_once( FAMROOTS_INCLUDES . 'tool-bar.php' );
		require_once( FAMROOTS_INCLUDES . 'tng-db.php' );
		require_once( FAMROOTS_INCLUDES . 'utilities.php' );
		require_once( FAMROOTS_INCLUDES . 'users.php' );
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
	 *
     *	@param		null
	 */
	public function admin()
	{
		if ( is_admin() ) 
		{
			require_once( FAMROOTS_ADMIN . 'settings.php' );
		}
	}
	
	/** 
	*	Activation
	*
	*	Runs the method when the plugin is activated.
	*	Attempts to locate the TNG file directory/path.
	*	If the path is found: update the wp options db and get tng db values.
	*	Add tng capabilities to WordPress administrator roles. Add new TNG roles
	*
	*	@author		Nate Jacobs
	*	@date		10/28/12
	*	@since		0.1
	*
	*	@param		null
	*/
	public function activation()
	{
		include_once( plugin_dir_path( __FILE__ )."/inc/utilities.php" );
		
		$path = familyRootsUtilities::get_path();
		
		// if the path is not empty, add option to db
		if( !empty( $path ) )
		{
			add_option( 'family-roots-settings', array( 'tng_path' => $path ) );
			familyRootsUtilities::get_tng_db_values();
		}
		
		// this last section might be merged into the tng-user-management plugin
		// get the admin role
		$role = get_role( 'administrator' );
		
		// if there is an admin role
		if ( !empty( $role ) ) 
		{
			// add the tng admin capabilities to it
			/*
$role->add_cap( 'tng_add_all' );
			$role->add_cap( 'tng_edit_all' );
			$role->add_cap( 'tng_delete_all' );
			$role->add_cap( 'tng_view_living' );
			$role->add_cap( 'tng_view_private' );
			$role->add_cap( 'tng_dl_gedcom' );
			$role->add_cap( 'tng_dl_pdf' );
			$role->add_cap( 'tng_view_lds' );
			$role->add_cap( 'tng_edit_profile' );
*/
		}
		
		// add new WordPress roles to match the TNG roles with the applicable capabilities
		/*
add_role( 'Guest', 'guest' );
		add_role( 'Submitter', 'subm', array( 'tng_submit_edit' => true  ) );
		add_role( 'Contributor', 'contrib', array( 'tng_add_all' => true ) );
		add_role( 'Editor', 'editor', array(  
			'tng_add_all' 		=> true,
			'tng_edit_all' 		=> true,
			'tng_delete_all'	=> true
		));
		add_role( 'Media Contributor', 'mcontrib', array( 'tng_add_media' => true ) );
		add_role( 'Media Editor', 'meditor', array(  
			'tng_add_media' 	=> true,
			'tng_edit_media' 	=> true,
			'tng_delete_media' 	=> true
		));
*/
	}
	
	/** 
	*	Deletion
	*
	*	Runs the method when the plugin is deleted.
	*
	*	@author		Nate Jacobs
	*	@date		11/20/12
	*	@since		0.1
	*
	*	@param	null	
	*/
	public function deletion()
	{
		// remove family-roots-settings, family-roots-users-settings, family-roots-advanced-settings
		// remove roles and capabilities
	}
}