<?php

/** 
 *	Utilities class dedicated to the little helper functions needed by the plugin
 *
 *	@author		Nate Jacobs
 *	@date		11/3/12
 *	@since		0.1
 */
class FamilyRootsUtilities {
	
	/** 
 	 *	Hook into WordPress and prepare all the methods as necessary.
 	 *
 	 *	@author		Nate Jacobs
 	 *	@date		11/3/12
 	 *	@since		0.1
 	 */
	public function __construct() {
		
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
				
				// is it the $trees_table value?
				if(substr(trim($line), 0, 12) == '$trees_table') {
					$settings['trees_table'] = $this->split_value($line);
				}
				
				// is it the $defaulttree value?
				if(substr(trim($line), 0, 12) == '$defaulttree') {
					$settings['default_tree'] = $this->split_value($line);
				}
				
				// is it the $tngconfig['password_type'] value?
				if(substr(trim($line), 12, 13) == 'password_type') {
					$settings['password_type'] = $this->split_value($line);
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
}

$family_roots_utilities = new FamilyRootsUtilities();