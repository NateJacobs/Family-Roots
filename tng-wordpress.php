<?php

/**
 *	Plugin Name: 	Family Roots
 *	Description: 	The Next Generation Genealogy and WordPress Integration.
 *	Version: 		0.1
 *	Date:			10/28/12
 *	Author:			Nate Jacobs
 *	Author URI:		http://natejacobs.org
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
		register_activation_hook( __FILE__, array( __CLASS__, 'activation' ) );
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
		define( 'FAMROOTS_VERSION', '1.0' );
		define( 'FAMROOTS_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
		define( 'FAMROOTS_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );
		define( 'FAMROOTS_INCLUDES', FAMROOTS_DIR.trailingslashit( 'inc' ) );
		define( 'FAMROOTS_ADMIN', FAMROOTS_DIR.trailingslashit( 'admin' ) );
	}
	
	/**
	 *	Include Files
	 *
	 *	Lists the files used for plugin actions in the includes folder.
	 *	They are stored in the inc folder
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
	}
	
	/**
	 *	Admin Files
	 *
	 *	Lists the files used for plugin actions in the admin dashboard. 
	 *	They are stored in the admin folder.
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
	*
	*	@author		Nate Jacobs
	*	@date		10/28/12
	*	@since		0.1
	*
	*	@todo		Change permalinks
	*	@todo		Add new page called genealogy
	*
	*	@param		null
	*/
	public function activation()
	{
		
	}
}