<?php

/** 
*	TNG Database Connection
*
*	Class used to handle interactions with the TNG database
*
*	@author		Nate Jacobs
*	@date		11/3/12
*	@since		0.1
*/
class FamilyRootsTNGDatabase {

	/** 
	 *	Hook into WordPress and prepare all the methods as necessary.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/5/14
	 *	@since		1.0
	 */
	public function __construct() {
		global $tng_db;
		
		$tng_db = $this->connect();
	}
	
	/** 
	 *	Create the connection to the TNG database
	 *
	 *	@author		Nate Jacobs
	 *	@date		11/4/12
	 *	@since		0.1
	 * 
	 * 	@todo	Create better error handling
	 */
	public function connect() {
		// get the tng db values from the wp options table
		$settings = get_option('family_roots_settings');

		try {
			// create a new wpdb object and return it
			$tng_db = new wpdb( $settings['username'], $settings['password'], $settings['name'], $settings['host'] );
			return $tng_db;
		} catch( Exception $e ) {
			// any problems?
			$error = new WP_Error(500,$e->getMessage());
			return $error;
		}
	}
}

$family_roots_db = new FamilyRootsTNGDatabase();