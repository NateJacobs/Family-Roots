<?php

/** 
 *	
 *
 *	@author		Nate Jacobs
 *	@date		8/10/14
 *	@since		1.0
 */
class TNG_Person_Query extends FamilyRootsTNGDatabase {
	/** 
	 *	 
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/10/14
	 *	@since		1.0
	 *
	 *	@param		
	 */
	public function __construct() {
		
	}
	
	/** 
	 *	Get the database connection and family-roots settings.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/17/14
	 *	@since		1.0
	 */
	private function get_db_settings() {
		return ['tables' => get_option('family-roots-settings'), 'db' => parent::connect()];
	}
	
	/** 
	 *	
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/10/14
	 *	@since		1.0
	 *
	 *	@param		
	 */
	public function __get($key) {
		
	}
}