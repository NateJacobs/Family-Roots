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
		add_action('wp_before_admin_bar_render', [$this, 'family_roots_menu']);
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
		
		$wp_admin_bar->add_node([
			'id' => 'family-roots-menu',
			'title' => __('Genealogy', 'family-roots'),
		]);
		
		$wp_admin_bar->add_node([
			'id' => 'family-roots-menu-surnames',
			'title' => __('Last names', 'family-roots'),
			'parent' => 'family-roots-menu',
			'href' => home_url('genealogy/lastnames/')
		]);
		
		$wp_admin_bar->add_node([
			'id' => 'family-roots-menu-places',
			'title' => __('Places', 'family-roots'),
			'parent' => 'family-roots-menu',
			'href' => home_url('genealogy/places/')
		]);
	}
}

$family_roots_toolbar = new FamilyRootsToolbar();