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
	global $tng_db;
	
	$person_table = isset($settings['people_table']) ? $settings['people_table'] : false;
		
	if(!$person_table) {
		return false;
	}
	
	return $tng_db->get_results("SELECT DISTINCT lastname FROM {$person_table} WHERE lastname IS NOT NULL");
}

/** 
 *	Return a surname tag cloud.
 *
 *	@author		Nate Jacobs
 *	@date		9/3/14
 *	@since		1.0
 *
 *	@param		int	$threshold	The minimum number of people with a surname required to show in tag cloud.
 */
function family_roots_get_lastname_cloud($threshold = 15) {
	$settings = get_option('family-roots-settings');
	global $tng_db;
		
	$person_table = isset($settings['people_table']) ? $settings['people_table'] : false;
	
	if(!$person_table) {
		return  false;
	}
	
	$last_names = $tng_db->get_results("SELECT lastname FROM {$person_table} WHERE lastname IS NOT NULL AND lastname != ' '");
	
	$output = array_map(function ($last_names) { return $last_names->lastname; }, $last_names);
	$last_names = implode(' ', $output);
	
	$frequency = [];
	
	foreach(str_word_count($last_names, 1) as $word) {
		// For each word found in the frequency table, increment its value by one
		array_key_exists($word, $frequency) ? $frequency[ $word ]++ : $frequency[ $word ] = 0;
	}
	
	$minFontSize = 12;
	$maxFontSize = 30;
	
	$minimumCount = min(array_values($frequency));
	$maximumCount = max(array_values($frequency));
	$spread = $maximumCount - $minimumCount;
	$cloudHTML = '';
	$cloudTags = [];
 
	$spread == 0 && $spread = 1;
 
	foreach($frequency as $tag => $count)
	{
		if($count > $threshold) {
			$size = $minFontSize + ($count - $minimumCount) * ($maxFontSize - $minFontSize) / $spread;
			$cloudTags[] = '<a style="font-size: ' . floor($size) . 'px' 
			. '" class="surname_cloud" href="'.home_url('genealogy/lastname/').$tag 
			. '" title="'.$count.' people">' 
			. htmlspecialchars(stripslashes($tag)).'</a>';
		}
	}
 
	return join(' ', $cloudTags);
}

/** 
 *	Return an array of all the people with the specified last name.
 *
 *	@author		Nate Jacobs
 *	@date		9/4/14
 *	@since		1.0
 *
 *	@param		string	$last_name	The last name to search for.
 */
function family_roots_get_people_from_last_name($vars) {
	$defaults = [
		'search_columns' => ['last_name'],
		'fields' => 'all'
	];
	
	$args = wp_parse_args($vars, $defaults);
	
	$search = new TNG_Person_Query($args);
	
	return $search;
}

/** 
 *	Return the url for the photo requested.
 *
 *	@author		Nate Jacobs
 *	@date		9/5/14
 *	@since		1.0
 *
 *	@param		string	$file_name	The media file name.
 */
function family_roots_get_photo_url($file_name) {
	$settings = get_option('family-roots-settings');
	$photo_dir = isset($settings['photo_dir']) ? $settings['photo_dir'] : false;
	$tng_domain = isset($settings['tng_domain']) ? $settings['tng_domain'] : false;
	
	if(!$photo_dir) {
		return  false;
	}
	
	return trailingslashit($tng_domain).trailingslashit($photo_dir).rawurlencode($file_name);
}

/** 
 *	Return the first photo for a person.
 *
 *	@author		Nate Jacobs
 *	@date		9/6/14
 *	@since		1.0
 *
 *	@param		object	$person	A TNG_Person object
 */
function family_roots_get_person_photo($person) {
	$media = $person->get_media();
	
	foreach($media as $item) {
		if('photos' == $item->media_type) {
			$photos[] = $item->media_path;
		}
	}
	
	if(empty($photos)) {
		$url = false;
	} else {
		$url = family_roots_get_photo_url($photos[0]);
	}
	
	return $url;
}