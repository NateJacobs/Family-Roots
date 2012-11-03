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
	*	Iterates through directory looking for three files present in the TNG install.
	*	If the three files are present and have the same path, the TNG file path is saved to the plugin options.
	*
	*	@author		Nate Jacobs
	*	@date		10/28/12
	*	@since		0.1
	*
	*	@todo		Change permalinks
	*
	*	@param		null
	*/
	public function activation()
	{
		$path = dirname( ABSPATH );

		$directory = new RecursiveDirectoryIterator( $path,RecursiveDirectoryIterator::SKIP_DOTS );
		$iterator = new RecursiveIteratorIterator( $directory,RecursiveIteratorIterator::LEAVES_ONLY );
		
		$req_files = array( "ahnentafel.php", "genlib.php", "admin_cemeteries.php" );
		
		foreach ( $iterator as $fileinfo ) 
		{
		    if ( in_array( $fileinfo->getFilename(), $req_files ) ) 
		    {
		        $files[] = $fileinfo->getPath();
		    }
		}
		
		if( count( $files ) == 3 && count( array_unique( $files ) ) == 1 )
		{
			add_option( 'family-roots-settings', array( 'tng_path' => trailingslashit( $files[0] ) ) );
		}
	}
}