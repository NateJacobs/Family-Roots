<?php

/** 
 *	Handles all the URL rewrites and loading of template files.
 *
 *	@author		Nate Jacobs
 *	@date		8/16/14
 *	@since		1.0
 */
class FamilyRootsRewriteTemplate {
	/** 
	 *	Hook into WordPress and prepare all the methods as necessary.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/16/14
	 *	@since		1.0
	 */
	public function __construct() {
		add_action('init', [$this, 'person_rewrite']);
		add_filter('template_include', [$this, 'person_template']);
		add_filter('template_include', [$this, 'family_template']);
		add_filter('template_include', [$this, 'surname_template']);
		add_filter('template_include', [$this, 'surnames_template']);
		add_filter('template_include', [$this, 'place_template']);
		add_filter('template_include', [$this, 'places_template']);
	}

	/** 
	 *	Add rewrite rules for person and family pages.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/16/14
	 *	@since		1.0
	 */
	public function person_rewrite() {
		add_rewrite_rule(
		    '^genealogy/place/([0-9a-zA-Z%-]*)/([a-zA-Z%-]*)/([a-zA-Z%-]*)',
		    'index.php?tng_type=place&tng_place_id=$matches[1]&tng_locality_1=$matches[2]&tng_locality_2=$matches[3]',
		    'top'
		);
		
		add_rewrite_rule(
		    '^genealogy/place/([0-9a-zA-Z%-]*)/([a-zA-Z%-]*)',
		    'index.php?tng_type=place&tng_place_id=$matches[1]&tng_locality_1=$matches[2]',
		    'top'
		);
		
		add_rewrite_rule(
		    '^genealogy/([^/]*)/([0-9a-zA-Z%-]*)/page/([0-9]*)',
		    'index.php?tng_type=$matches[1]&tng_$matches[1]_id=$matches[2]&tng_page=$matches[3]',
		    'top'
		);
		
		add_rewrite_rule(
		    '^genealogy/([^/]*)/([0-9a-zA-Z%-]*)',
		    'index.php?tng_type=$matches[1]&tng_$matches[1]_id=$matches[2]',
		    'top'
		);
		
		add_rewrite_rule(
		    '^genealogy/([^/]*)',
		    'index.php?tng_type=$matches[1]',
		    'top'
		);
		
		add_rewrite_tag('%tng_person_id%', '([0-9]*)');
		add_rewrite_tag('%tng_family_id%', '([0-9]*)');
		add_rewrite_tag('%tng_lastname_id%', '([0-9a-zA-Z%-]*)');
		add_rewrite_tag('%tng_place_id%', '([0-9]*)');
		add_rewrite_tag('%tng_type%', '([a-zA-Z]*)');
		add_rewrite_tag('%tng_locality_1%', '([a-zA-Z]*)');
		add_rewrite_tag('%tng_locality_2%', '([a-zA-Z]*)');
		add_rewrite_tag('%tng_page%', '([0-9]*)');
	}
	
	/** 
	 *	Check for the existence of the TNG person page if the query variable [tng_type] is set to person.
	 *	If the theme file is located in parent or child theme load that before the plugin version.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/16/14
	 *	@since		1.0
	 *
	 *	@param		string	$template	The original template to load.
	 */
	public function person_template($template) {
		$type = get_query_var('tng_type');
		
		if(isset($type) && 'person' === $type) {
			$theme_template = locate_template('family-roots/tng-person-page.php');
			if(!empty($theme_template)) {
				$template = $theme_template;
			} else {
				$template = FAMROOTS_TEMPLATES.'tng-person-page.php';
			}
		}
		
		return $template;
	}
	
	/** 
	 *	Check for the existence of the TNG family page if the query variable [tng_type] is set to family.
	 *	If the theme file is located in parent or child theme load that before the plugin version.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/16/14
	 *	@since		1.0
	 *
	 *	@param		string	$template	The original template to load.
	 */
	public function family_template($template) {
		$type = get_query_var('tng_type');
		
		if(isset($type) && 'family' === $type) {
			$theme_template = locate_template('family-roots/tng-family-page.php');
			if(!empty($theme_template)) {
				$template = $theme_template;
			} else {
				$template = FAMROOTS_TEMPLATES.'tng-family-page.php';
			}
		}
		
		return $template;
	}
	
	/** 
	 *	Check for the existence of the TNG lastname page if the query variable [tng_type] is set to lastname.
	 *	If the theme file is located in parent or child theme load that before the plugin version.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/4/14
	 *	@since		1.0
	 *
	 *	@param		string	$template	The original template to load.
	 */
	public function surname_template($template) {
		$type = get_query_var('tng_type');
		
		if(isset($type) && 'lastname' === $type) {
			$theme_template = locate_template('family-roots/tng-lastname-page.php');
			if(!empty($theme_template)) {
				$template = $theme_template;
			} else {
				$template = FAMROOTS_TEMPLATES.'tng-lastname-page.php';
			}
		}
		
		return $template;
	}
	
	/** 
	 *	Check for the existence of the TNG lastnames page if the query variable [tng_type] is set to lastnames.
	 *	If the theme file is located in parent or child theme load that before the plugin version.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/6/14
	 *	@since		1.0
	 *
	 *	@param		string	$template	The original template to load.
	 */
	public function surnames_template($template) {
		$type = get_query_var('tng_type');
		
		if(isset($type) && 'lastnames' === $type) {
			$theme_template = locate_template('family-roots/tng-lastnames-page.php');
			if(!empty($theme_template)) {
				$template = $theme_template;
			} else {
				$template = FAMROOTS_TEMPLATES.'tng-lastnames-page.php';
			}
		}
		
		return $template;
	}
	
	/** 
	 *	Check for the existence of the TNG place page if the query variable [tng_type] is set to place.
	 *	If the theme file is located in parent or child theme load that before the plugin version.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/4/14
	 *	@since		1.0
	 *
	 *	@param		string	$template	The original template to load.
	 */
	public function place_template($template) {
		$type = get_query_var('tng_type');
		$id = get_query_var('tng_place_id');
		$place_id = (int) $id;

		if(isset($type) && 'place' === $type) {
			if($place_id) {
				$theme_template = locate_template('family-roots/tng-place-page.php');
			} else {
				$theme_template = locate_template('family-roots/tng-locality-page.php');
			}
			
			if(!empty($theme_template)) {
				$template = $theme_template;
			} else {
				if($place_id) {
					$template = FAMROOTS_TEMPLATES.'tng-place-page.php';
				} else {
					$template = FAMROOTS_TEMPLATES.'tng-locality-page.php';
				}
			}
		}
		
		return $template;
	}
	
	/** 
	 *	Check for the existence of the TNG places page if the query variable [tng_type] is set to places.
	 *	If the theme file is located in parent or child theme load that before the plugin version.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/13/14
	 *	@since		1.0
	 *
	 *	@param		string	$template	The original template to load.
	 */
	public function places_template($template) {
		$type = get_query_var('tng_type');
				
		if(isset($type) && 'places' === $type) {
			$theme_template = locate_template('family-roots/tng-places-page.php');
			if(!empty($theme_template)) {
				$template = $theme_template;
			} else {
				$template = FAMROOTS_TEMPLATES.'tng-places-page.php';
			}
		}
		
		return $template;
	}
}

$family_roots_rewrite = new FamilyRootsRewriteTemplate();