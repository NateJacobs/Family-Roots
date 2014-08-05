<?php

/** 
 *	Family Roots Toolbar
 *
 *	@author		Nate Jacobs
 *	@date		10/28/12
 *	@since		0.1
 */
class FamilyRootsToolbar {
	/** 
 	 *	Hook into WordPress and prepare all the methods as necessary.
 	 *
 	 *	@author		Nate Jacobs
 	 *	@date		10/28/12
 	 *	@since		0.1
 	 */
	public function __construct() {
		//add_action('wp_before_admin_bar_render', [$this, 'family_roots_menu']);
	}
	
	/** 
	 *	Family Roots Menu and dispalys links to TNG pages
	 *
	 *	@author		Nate Jacobs
	 *	@date		11/3/12
	 *	@since		0.1
	 */
	public function family_roots_menu() {
		global $wp_admin_bar;
		
		$wp_admin_bar->add_menu([
			'id' => 'family-roots-menu',
			'title' => __('TNG', 'family-roots-integration'),
		]);
		
		$wp_admin_bar->add_menu([
			'id' => 'family-roots-menu-surnames',
			'title' => __('Surnames', 'family-roots-integration'),
			'parent' => 'family-roots-menu'
		]);
	}
}

$family_roots_toolbar = new FamilyRootsToolbar();