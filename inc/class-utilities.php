<?php

/** 
 *	Utilities class dedicated to the little helper functions needed by the plugin
 *
 *	@author		Nate Jacobs
 *	@date		11/3/12
 *	@since		0.1
 */
class FamilyRootsUtilities {
	
	public $db;
	public $settings;
	
	/** 
 	 *	Hook into WordPress and prepare all the methods as necessary.
 	 *
 	 *	@author		Nate Jacobs
 	 *	@date		11/3/12
 	 *	@since		0.1
 	 */
	public function __construct() {
		$this->db = new FamilyRootsTNGDatabase();
		$this->settings = get_option('family-roots-settings');
	}
	
	/** 
	 *	Iterates through directory looking for three files present in the TNG install.
	 *	If the three files are present and have the same path, the TNG file path is saved to the plugin options.
	 *
	 *	@author		Nate Jacobs
	 *	@date		11/17/12
	 *	@since		0.1
	 */
	public function get_path() {
		// get the directory above the WordPress install
		$path = dirname(ABSPATH);
		
		// define options for recursive iterator
		$directory = new RecursiveDirectoryIterator($path,RecursiveDirectoryIterator::SKIP_DOTS);
		$iterator = new RecursiveIteratorIterator($directory,RecursiveIteratorIterator::LEAVES_ONLY);
		
		// define the files required for a TNG match
		$req_files = ["ahnentafel.php", "genlib.php", "admin_cemeteries.php"];
		
		// loop through all files returned from the search
		foreach($iterator as $fileinfo) {
			// are the files defined above in the return, if so add them to an array
		    if(in_array($fileinfo->getFilename(), $req_files)) {
		        $files[] = $fileinfo->getPath();
		    }
		}
		
		// after looping through all the files check and see if there are three files and they all have the identical path
		if(count($files) == 3 && count(array_unique($files)) == 1) {
			// if they do, return the path
			return trailingslashit($files[0]);
		}
	}

	/** 
	 *	Gets the TNG config values config.php and customconfig.php into an array and passes it on
	 *
	 *	@author		Nate Jacobs
	 *	@date		11/3/12
	 *	@since		0.1
	 */
	private function get_tng_config() {
		// get the tng file path
		$settings = get_option('family-roots-settings');
		
		// if the tng file path is present
		if(!empty($settings['tng_path'])) {
			$trn_path = $settings['tng_path'];
			// get contents of config.php
			$config = file_get_contents($trn_path.'config.php');
			// get contents of customconfig.php
			$config .= file_get_contents($trn_path.'customconfig.php');
			// return everything in an array split by new lines
			$config_array = explode("\n", $config);
		} else {
			$config_array = false;
		}
		
		return $config_array;
	}
	
	/** 
	 *	Retrieves the database values, users table name and password hashing type 
	 *	from config.php and customconfig.php.
	 *
	 *	@author		Nate Jacobs
	 *	@date		11/3/12
	 *	@since		0.1
	 *
	 *	@todo		Add error handling
	 */
	public function get_tng_db_values()
	{
		// get array of config.php and customconfig.php
		$results = $this->get_tng_config();
				
		// do we have stuff?
		if(!empty($results)) {
			$db_values = [];
			$settings = [];
			
			$settings = get_option('family-roots-settings');
			
			// loop through each line and find all the values that start with $database_ or $users_table
			foreach($results as $line) {
				// is it the $database_ value?
				if(substr(trim($line), 0, 10) == '$database_') {
					// split them on the =
					$key = substr(trim(strstr($line, '=', TRUE)), 10);
					$value = explode('=', $line);
					// take the first half and make it the key and the second half the value
					$db_values[$key] = rtrim(str_replace('"', "", $value[1]), ";");
				}
				
				// is it the $users_table value?
				if(substr(trim($line), 0, 12) == '$users_table') {
					$settings['users_table'] = $this->split_value($line);
				}
				
				// is it the $people_table value?
				if(substr(trim($line), 0, 13) == '$people_table') {
					$settings['people_table'] = $this->split_value($line);
				}
				
				// is it the $families_table value?
				if(substr(trim($line), 0, 15) == '$families_table') {
					$settings['family_table'] = $this->split_value($line);
				}
				
				// is it the $children_table value?
				if(substr(trim($line), 0, 15) == '$children_table') {
					$settings['children_table'] = $this->split_value($line);
				}
				
				// is it the $places_table value?
				if(substr(trim($line), 0, 13) == '$places_table') {
					$settings['places_table'] = $this->split_value($line);
				}
				
				// is it the $sources_table value?
				if(substr(trim($line), 0, 14) == '$sources_table') {
					$settings['sources_table'] = $this->split_value($line);
				}
				
				// is it the $events_table value?
				if(substr(trim($line), 0, 13) == '$events_table') {
					$settings['events_table'] = $this->split_value($line);
				}
				
				// is it the $eventtypes_table value?
				if(substr(trim($line), 0, 17) == '$eventtypes_table') {
					$settings['eventtypes_table'] = $this->split_value($line);
				}
				
				// is it the $notelinks_table value?
				if(substr(trim($line), 0, 16) == '$notelinks_table') {
					$settings['notelinks_table'] = $this->split_value($line);
				}
				
				// is it the $xnotes_table value?
				if(substr(trim($line), 0, 13) == '$xnotes_table') {
					$settings['xnotes_table'] = $this->split_value($line);
				}
				
				// is it the $trees_table value?
				if(substr(trim($line), 0, 12) == '$trees_table') {
					$settings['trees_table'] = $this->split_value($line);
				}
				
				// is it the $defaulttree value?
				if(substr(trim($line), 0, 12) == '$defaulttree') {
					$settings['default_tree'] = $this->split_value($line);
				}
				
				// is it the $tngconfig['media_table'] value?
				if(substr(trim($line), 0, 12) == '$media_table') {
					$settings['media_table'] = $this->split_value($line);
				}
				
				// is it the $tngconfig['medialinks_table'] value?
				if(substr(trim($line), 0, 17) == '$medialinks_table') {
					$settings['media_links_table'] = $this->split_value($line);
				}
				
				// is it the $tngconfig['password_type'] value?
				if(substr(trim($line), 12, 13) == 'password_type') {
					$settings['password_type'] = $this->split_value($line);
				}
				
				// is it the $tngconfig['tngdomain'] value?
				if(substr(trim($line), 0, 10) == '$tngdomain') {
					$settings['tng_domain'] = $this->split_value($line);
				}
				
				// is it the $tngconfig['photopath'] value?
				if(substr(trim($line), 0, 10) == '$photopath') {
					$settings['photo_dir'] = $this->split_value($line);
				}
			}
						
			$settings['host'] = trim($db_values['host']);
			$settings['name'] = trim($db_values['name']);
			$settings['username'] = trim($db_values['username']);
			$settings['password'] = trim(trim( $db_values['password'], " '"));
			
			return $settings;
		}
	}
	
	/** 
	 *	
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/5/14
	 *	@since		1.0
	 *
	 *	@param		string	$line
	 */
	protected function split_value($line)
	{
		// split on the =
		$value = explode('=', $line);
		// remove the double quotes and ending semi-colon
		$trimmed_value = rtrim(str_replace('"', "", $value[1]), ";");
		
		return trim($trimmed_value);
	}
	
	/** 
	 *	This method is used to obfuscate sensative strings. No real security if the source code is compromised.
	 *
	 *	@author		Nate Jacobs
	 *	@date		12/31/12
	 *	@since		0.1
	 *
	 *	@param	string	$string	string to encrypt or decrypt
	 *	@param	string	$method string that indicates if the method should encrypt or decrypt	
	 */
	public function obfuscate($string, $method = 'encrypt') {
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$h_key = hash('sha256', NONCE_KEY, TRUE);
		
		switch($method) {
			case 'encrypt':
				$string = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $h_key, $string, MCRYPT_MODE_ECB, $iv));
				break;
			case 'decrypt':
				$string = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $h_key, base64_decode($string), MCRYPT_MODE_ECB, $iv));
				break;
		}
		
		return $string;
	}
	
	/** 
	 *	Test if a person is living or not.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/17/14
	 *	@since		1.0
	 *
	 *	@param		string	$living	A 1 to indicatge living or a 0 to indicate dead.
	 */
	public function is_living($living, $birth_date) {
		if('0' == $living && '0000-00-00' != $birth_date){
			return false;
		} else {
			return true;
		}
	}
	
	/** 
	 *	Returns a nicely formatted date with day of week, month, day and year.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/17/14
	 *	@since		1.0
	 *
	 *	@param		string	$date	The date to format.
	 */
	public function get_date_for_display($date) {
		// if the birth date has just a year, e.g. 1950-00-00
		if('00-00' === substr($date, 5, 5) && '0000' != substr($date, 0, 4)) {
			$date_1 = substr_replace($date, "01-01", 5);
			$date = date('Y', strtotime($date_1));
		} else {
			if('0000-00-00' == $date) {
				$date = 'Unknown';
			} else {
				$date = date('l, F d, Y', strtotime($date));
			}
		}
		
		return $date;
	}
	
	/** 
	 *	Returns the gender full string.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/17/14
	 *	@since		1.0
	 *
	 *	@param		string	$sex		The single character representation of the person's sex.
	 */
	public function get_sex_for_display($sex) {
		switch($sex) {
			case 'M':
				$gender = 'Male';
				break;
			case 'F':
				$gender = 'Female';
				break;
			default:
				$gender = 'Unknown';
				break;
		}
		
		return $gender;
	}
	
	/** 
	 *	
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/17/14
	 *	@since		1.0
	 *
	 *	@param		
	 */
	public function get_child_type($sex) {
		switch($sex) {
			case 'M':
				$type = 'Son';
				break;
			case 'F':
				$type = 'Daughter';
				break;
			default:
				$type = 'Unknown';
				break;
		}
		
		return $type;
	}
	
	/** 
	 *	
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/17/14
	 *	@since		1.0
	 *
	 *	@param		
	 */
	public function get_person_url($person) {
		if($person instanceof TNG_PERSON) {
			$id = substr($person->get('person_id'), 1);
		} elseif(is_numeric($person)) {
			$id = $person;
		} elseif('I' === substr($person, 0, 1)) {
			$id = substr($person, 1);
		} else {
			$id = 0;
		}
		
		return trailingslashit(trailingslashit(home_url()).'genealogy/person/'.$id);
	}
	
	/** 
	 *	
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/17/14
	 *	@since		1.0
	 *
	 *	@param		
	 */
	public function get_person_age($birth_date, $death_date) {
		// set default age
		$age = 'Unknown';
		
		// if there is no birth date, can't calculate age
		if('0000-00-00' == $birth_date) {
			return $age;
		}
		
		// if the birth date has just a year, e.g. 1950-00-00
		if('00-00' === substr($birth_date, 5, 5) && '0000' != substr($birth_date, 0, 4)) {
			$birth_date = substr_replace($birth_date, "01-01", 5);
		}
		
		// if the death date has just a year, e.g. 1950-00-00
		if('00-00' === substr($death_date, 5, 5) && '0000' != substr($death_date, 0, 4)){
			$death_date = substr_replace($death_date, "01-01", 5);
		}
		
		$from = new DateTime($birth_date);
		
		// if the death date is unknown, use today
		if('0000-00-00' == $death_date) {
			$to = new DateTime('today');
		} else {
			$to = new DateTime($death_date);
		}
		
		$current_age = $from->diff($to)->y;
		
		// check if the person would be more than 120 years old
		if('0000-00-00' == $death_date) {
			// if so, return the age as unknown
			if(115 < $current_age) {
				$age = 'Unknown';
			} elseif(0 === $current_age) {
				$age = $from->diff($to)->m.' months';
			}else {
				$age = $current_age;
			}
		} else {
			$age = $current_age;
		}
		
		
		return $age;
	}
	
	/** 
	 *	Return the parent string.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/23/14
	 *	@since		1.0
	 *
	 *	@param		object	$person	The TNG_Person object
	 */
	public function get_parent_template($person) {
		$father = !empty($person->get('father')) ? new TNG_Person($person->get('father')) : null;
		$mother = !empty($person->get('mother')) ? new TNG_Person($person->get('mother')) : null;
		
		$father_name = is_null($father) ? null : '<a href="'.$this->get_person_url($father).'">'.$father->get('first_name').' '.$father->get('last_name').'</a>';
		$mother_name = is_null($mother) ? null : '<a href="'.$this->get_person_url($mother).'">'.$mother->get('first_name').' '.$mother->get('last_name').'</a>';

		$child_type = $this->get_child_type($person->get('sex'));
		
		if(is_null($father_name)) {
			$parents = $child_type.' of '.$mother_name;
		} elseif(is_null($mother_name)) {
			$parents = $child_type.' of '.$father_name;
		} else {
			$parents = $child_type.' of '.$father_name.' and '.$mother_name;
		}
		
		return $parents;
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
	public function get_lastname_cloud($threshold = 15)
	{	
		$person_table = isset($this->settings['people_table']) ? $$this->settings['people_table'] : false;
		
		if(!$person_table) {
			return  false;
		}
		
		$last_names = $this->db->connect()->get_results("SELECT lastname FROM {$person_table} WHERE lastname IS NOT NULL AND lastname != ' '");
		
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
	public function get_people_from_last_name($vars) {
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
	public function get_photo_url($file_name) {
		$photo_dir = isset($this->settings['photo_dir']) ? $this->settings['photo_dir'] : false;
		$tng_domain = isset($this->settings['tng_domain']) ? $this->settings['tng_domain'] : false;
		
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
	public function get_person_photo($person) {
		$media = $person->get_media();
		
		foreach($media as $item) {
			if('photos' == $item->media_type) {
				$photos[] = $item->media_path;
			}
		}
		
		if(empty($photos)) {
			$url = false;
		} else {
			$url = $this->get_photo_url($photos[0]);
		}
		
		return $url;
	}
}

$family_roots_utilities = new FamilyRootsUtilities();