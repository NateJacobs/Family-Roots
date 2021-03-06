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
		global $tng_db;
		$this->db = $tng_db;
		$this->settings = get_option('family_roots_settings');
		date_default_timezone_set(get_option('timezone_string'));
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
		
		$files = [];
		
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
		$settings = get_option('family_roots_settings');
		
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
	public function get_tng_db_values() {
		// get array of config.php and customconfig.php
		$results = $this->get_tng_config();
				
		// do we have stuff?
		if(!empty($results)) {
			$db_values = [];
			$settings = [];
			
			$settings = get_option('family_roots_settings');
			
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
	 *	Split the string on a "=" sign and remove the double quotes and ending semi-colon.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/5/14
	 *	@since		1.0
	 *
	 *	@param		string	$line
	 */
	protected function split_value($line) {
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
	 *	@param		string	$living	A 1 to indicate living or a 0 to indicate dead.
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
	public function get_date_for_display($date, $format = 'l, F d, Y') {
		// if the birth date has just a year, e.g. 1950-00-00
		if('00-00' === substr($date, 5, 5) && '0000' != substr($date, 0, 4)) {
			$date_1 = substr_replace($date, "01-01", 5);
			$date = date('Y', strtotime($date_1));
		} else {
			if('0000-00-00' == $date) {
				$date = 'Unknown';
			} else {
				$date = date($format, strtotime($date));
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
	 *	Returns the person url for the site.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/17/14
	 *	@since		1.0
	 *
	 *	@param		int|string|TNG_Person	$person	The person object to get the URL for.
	 */
	public function get_person_url($person) {
		if($person instanceof TNG_Person) {
			$id = $person->ID;
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
	 *	Returns the place url for the site.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/13/14
	 *	@since		1.0
	 *
	 *	@param		int|string|TNG_Place	$place	The place object to get the URL for.
	 */
	public function get_place_url($place) {
		if($place instanceof TNG_Place) {
			$id = $place->ID;
		} elseif(is_numeric($place)) {
			$id = $place;
		} elseif(is_string($place)) {
			$place_array = explode(', ', $place);
			$places = implode( ', ', array_filter( array_reverse( $place_array ) ) );
			$id = strtolower( str_replace( ' ', '-', str_replace(', ', '/', trim( $places ) ) ) );
		} else {
			$id = 0;
		}
		
		return trailingslashit(trailingslashit(home_url()).'genealogy/place/'.$id);
	}
	
	/** 
	 *	Returns the person url for the site.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/17/14
	 *	@since		1.0
	 *
	 *	@param		int|string|TNG_Family	$family	The family object to get the URL for.
	 */
	public function get_family_url($family) {
		if($family instanceof TNG_Family) {
			$id = $family->ID;
		} elseif(is_numeric($family)) {
			$id = $family;
		} elseif('F' === substr($family, 0, 1)) {
			$id = substr($family, 1);
		} else {
			$id = 0;
		}
		
		return trailingslashit(trailingslashit(home_url()).'genealogy/family/'.$id);
	}
	
	/** 
	 *	Determine the person's age.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/17/14
	 *	@since		1.0
	 *
	 *	@param		string	$birth_date	The birthdate of the person.
	 *	@param		string	$death_date	The deathdate of the person.
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
		$father_object = $person->get('father');
		$mother_object = $person->get('mother');
		$father = !empty($father_object) ? new TNG_Person($father_object) : null;
		$mother = !empty($mother_object) ? new TNG_Person($mother_object) : null;
		
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
	 *	Build the pagination HTML.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/6/14
	 *	@since		1.0
	 */
	public function tng_pagination($current_page, $limit, $offset, $total) {
		$total = round($total/$limit);
		
		if($total < 2) {
			return;
		}
		
		$current_page = $current_page === 0 ? 1 : $current_page;
		
		$links = paginate_links(
			[
				'current' => max(1, $current_page),
				'total' => $total,
				'type' => 'array',
				'prev_next' => false
			]
		);
		
		if($links) {
		?>
		<ul class="pagination" id="tng-pagination" class="hidden-print">
			<?php
				// check if the first value of the array is the current page
				if( false !== strpos( $links[0], 'current' ) )
				{
					echo '<li class="disabled"><a href="#">&laquo; Previous</a></li>';
				}
				else
				{
					$previous_page = $current_page-1;
					echo '<li><a href="'.get_pagenum_link().'page/'.$previous_page.'">&laquo; Previous</a></li>';
				}
				// loop through each of the links
				foreach( $links as $key => $link )
				{
					if( false !== strpos( $link, 'current' )  )
					{
						echo '<li class="active"><a href="#">'.$current_page.'</a></li>';
					}
					else
					{
						echo '<li>'.$link.'</li>';
					}
				}
				// check if we are on the last page
				if( $current_page == $total )
				{
					echo '<li class="disabled"><a href="#">Next &raquo;</a></li>';
				}
				else
				{
					$next_page = $current_page+1;
					echo '<li><a href="'.get_pagenum_link().'page/'.$next_page.'">Next &raquo;</a></li>';
				}
			?>
		</ul>
		<?php
		}
	}
	
	/** 
	 *	Return an array of all the places.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/13/14
	 *	@since		1.0
	 */
	public function get_all_places() {
		$places_table = isset($this->settings['places_table']) ? $this->settings['places_table'] : false;
		
		if(!$places_table) {
			return false;
		}
		
		$places = $this->db->get_results("SELECT ID, place, longitude, latitude, notes FROM {$places_table} ORDER BY place");
		
		if(empty($places)) {
			return false;
		} else {
			return $places;
		}
	}
	
	/** 
	 * 
	 *
	 * @author Nate Jacobs
	 * @date 3/7/15
	 * @since 1.0
	 *
	 * @param 
	 */
	public function get_grouped_places_by_us_state() {
		$places_table = isset($this->settings['places_table']) ? $this->settings['places_table'] : false;
		
		if(!$places_table) {
			return false;
		}
		
		$places = $this->db->get_results("SELECT trim(substring_index(place,',',-1)) as location, count(place) as count FROM tng_places WHERE trim(substring_index(place,',',-1)) IN('Alabama','Alaska','Arizona','Arkansas','California','Colorado','Connecticut','Delaware','Florida','Georgia','Hawaii','Idaho','Illinois','Indiana','Iowa','Kansas','Kentucky','Louisiana','Maine','Maryland','Massachusetts','Michigan','Minnesota','Mississippi','Missouri','Montana','Nebraska','Nevada','New Hampshire','New Jersey','New Mexico','New York','North Carolina','North Dakota','Ohio','Oklahoma','Oregon','Pennsylvania','Rhode Island','South Carolina','South Dakota','Tennessee','Texas','Utah','Vermont','Virginia','Washington','West Virginia','Wisconsin','Wyoming','Washington DC') GROUP BY location ORDER by location");
		
		if(empty($places)) {
			return false;
		} else {
			return $places;
		}
	}
	
	/** 
	 *	Check if the current user is allowed to view living person information.
	 *
	 *	@author		Nate Jacobs
	 *	@date		10/19/14
	 *	@since		1.0
	 *
	 *	@param		object	$person	The TNG_Person object
	 */
	public function living_allowed(TNG_Person $person) {
		if(!$person->living) {
			return true;
		} elseif(is_user_logged_in() && $person->living) {
			return true;
		} else {
			return false;
		}
	}
}

$family_roots_utilities = new FamilyRootsUtilities();