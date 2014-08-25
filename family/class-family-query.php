<?php

/** 
 *	Create all the family queries to the TNG database.
 *
 *	@author		Nate Jacobs
 *	@date		8/10/14
 *	@since		1.0
 */
class TNG_Family_Query extends FamilyRootsTNGDatabase {
	
	public $data;
	
	/** 
	 *	Connect to the TNG database when the class is instantiated.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/10/14
	 *	@since		1.0
	 */
	public function __construct($query = null) {
		if(!is_null($query)) {
			$this->prepare_query($query);
			$this->query();
		}
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
	public function prepare_query() {
		
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
	public function query() {
		
	}
}