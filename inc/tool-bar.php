<?php

familyRootsToolbar::init();

/** 
*	Family Roots Settings
*
*	Uses the settings API to display the settings page.

*
*	@author		Nate Jacobs
*	@date		10/28/12
*	@since		0.1
*/
class familyRootsToolbar
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
		//add_action('wp_before_admin_bar_render', array( __CLASS__, 'family_roots_menu' ) );
	}
	
	/** 
	*	Family Roots Menu
	*
	*	Dispalys links to TNG pages
	*
	*	@author		Nate Jacobs
	*	@date		11/3/12
	*	@since		0.1
	*
	*	@param		
	*/
	public function family_roots_menu()
	{
		global $wp_admin_bar;
		
		$wp_admin_bar->add_menu( array(
			'id' => 'family-roots-menu',
			'title' => __( 'TNG', 'family-roots-integration' ),
		) );
		$wp_admin_bar->add_menu( array(
			'id' => 'family-roots-menu-surnames',
			'title' => __( 'Surnames', 'family-roots-integration' ),
			'parent' => 'family-roots-menu'
		) );
	}
}
