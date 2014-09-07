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
		add_rewrite_tag('%tng_type%', '([a-zA-Z]*)');
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
			$theme_template = locate_template('tng-person-page.php');
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
			$theme_template = locate_template('tng-family-page.php');
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
			$theme_template = locate_template('tng-lastname-page.php');
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
			$theme_template = locate_template('tng-lastnames-page.php');
			if(!empty($theme_template)) {
				$template = $theme_template;
			} else {
				$template = FAMROOTS_TEMPLATES.'tng-lastnames-page.php';
			}
		}
		
		return $template;
	}
}

$family_roots_rewrite = new FamilyRootsRewriteTemplate();