<?php

/** 
 *	Create all the family queries to the TNG database.
 *
 *	@author		Nate Jacobs
 *	@date		8/10/14
 *	@since		1.0
 */
class TNG_Family_Query extends FamilyRootsTNGDatabase {
	
	public $db;
	public $settings;
	
	/** 
	 *	Connect to the TNG database when the class is instantiated.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/10/14
	 *	@since		1.0
	 */
	public function __construct($query = null) {
		if(!is_null($query)) {
			$this->db = parent::connect();
			$this->settings = get_option('family-roots-settings');
			
			$this->prepare_query($query);
			$this->query();
		}
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
	
	/** 
	 *	Return all the members of a family.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/10/14
	 *	@since		1.0
	 *
	 *	@param		string	$id	The family ID being requested
	 */
	public function get_family_by_id($id = null) {
		
		if(is_null($id)) {
			return WP_Error('family_id_missing',__('The family ID is missing.','family-roots-integration'));
		}
		
		$family_table = isset($this->settings['family_table']) ? $this->settings['family_table'] : null;
		
		if(is_null($family_table)) {
			return WP_Error('family_table_missing',__('The family table name is missing. Make sure you have set the TNG path in the settings page.','family-roots-integration'));
		}
		
		$family = $this->db->get_row( 
			$this->db->prepare("SELECT * FROM {$family_table} WHERE familyID = %s", $id)
		);
		
		return $family;
	}
}