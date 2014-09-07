<?php

/** 
 *	Return a list of all the unique last names in the tree.
 *
 *	@author		Nate Jacobs
 *	@date		9/6/14
 *	@since		1.0
 */
function family_roots_unique_last_names() {
	$settings = get_option('family-roots-settings');
	$db = new FamilyRootsTNGDatabase();
	$connect = $db->connect();
	
	$person_table = isset($settings['people_table']) ? $settings['people_table'] : false;
		
	if(!$person_table) {
		return false;
	}
	
	return $connect->get_results("SELECT DISTINCT lastname FROM {$person_table} WHERE lastname IS NOT NULL");
}