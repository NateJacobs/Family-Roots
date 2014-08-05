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
		$directory = new RecursiveDirectoryIterator( $path,RecursiveDirectoryIterator::SKIP_DOTS );
		$iterator = new RecursiveIteratorIterator( $directory,RecursiveIteratorIterator::LEAVES_ONLY );
		
		// define the files required for a TNG match
		$req_files = ["ahnentafel.php", "genlib.php", "admin_cemeteries.php"];
		
		// loop through all files returned from the search
		foreach($iterator as $fileinfo) {
			// are the files defined above in the return, if so add them to an array
		    if ( in_array( $fileinfo->getFilename(), $req_files ) ) 
		    {
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
			return explode("\n", $config);
		} else {
			return;
		}
		
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
		
		// do you have stuff?
		if(!empty($results)) {
			$db_values = [];
			
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
					// split them on the =
					$value = explode('=', $line);
					// take the first half and make it the key and the second half the value
					$users_table = rtrim(str_replace('"', "", $value[1]), ";");
				}
				
				// is it the $tngconfig['password_type'] value?
				if(substr(trim($line), 12, 13) == 'password_type') {
					// split them on the =
					$value = explode('=', $line);
					// take the first half and make it the key and the second half the value
					$password_type = rtrim(str_replace('"', "", $value[1]), ";");
				}
			}
						
			// update the values in the options table
			update_option('family-roots-tng-db', 
				[
					'host' 			=> trim($db_values['host']),
					'name' 			=> trim($db_values['name']),
					'username' 		=> trim($db_values['username']),
					'password' 		=> trim(trim( $db_values['password'], " '")),
					'users_table'	=> trim($users_table),
					'password_type'	=> trim($password_type)
				]
			);
		}
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