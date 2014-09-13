<?php

/** 
 *	Manage and load all of the assets necessary for the pedigree chart.
 *
 *	@author		Nate Jacobs
 *	@date		9/12/14
 *	@since		1.0
 */
class FamilyRootsAssets {
	/** 
	 *	Hook into WordPress and prepare all the methods as necessary.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/12/14
	 *	@since		1.0
	 */
	public function __construct() {
		add_action('wp_enqueue_scripts', [$this, 'register_d3js']);
		add_action('wp_enqueue_scripts', [$this, 'tng_person_page_scripts']);
	}

	/** 
	 *	Register the necessary script for the pedigree chart. 
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/12/14
	 *	@since		1.0
	 */
	public function register_d3js() {
		wp_register_script('d3js', 'http://d3js.org/d3.v3.min.js', [], '3', true);
		wp_register_script('family-roots-pedigree-chart', FAMROOTS_ASSETS.'js/pedigree.js', ['d3js'], '1.0.0', true);
		wp_register_style('family-roots-pedigree-css', FAMROOTS_ASSETS.'css/pedigree.css');
	}
	
	/** 
	 *	Enqueue the necessary scripts only on the tng_person page template.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/12/14
	 *	@since		1.0
	 */
	public function tng_person_page_scripts() {
		$type = get_query_var('tng_type');
		if(isset($type) && 'person' === $type) {
			wp_enqueue_script('family-roots-pedigree-chart');
			wp_enqueue_style('family-roots-pedigree-css');
		}
	}
}

$family_roots_assets = new FamilyRootsAssets();