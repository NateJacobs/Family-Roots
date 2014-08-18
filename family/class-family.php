<?php

// get by family id, husband id, wife id
// return array with TNG_Person objects 

/** 
 *	TNG family class.
 *
 *	@author		Nate Jacobs
 *	@date		8/10/14
 *	@since		1.0
 */
class TNG_Family extends FamilyRootsTNGDatabase {
	
	public $ID;
	
	public $data;
	
	public $parents;
	
	public $spouse;
	
	public $children;
	
	/** 
	 *	 
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/10/14
	 *	@since		1.0
	 *
	 *	@param		
	 */
	public function __construct($id = 0) {
		if(!empty($id) && !is_numeric($id)) {
			$id = 0;
		}
		
		if($id) {
			$data = get_family_data($id);
		}
		
		if($data) {
			$this->data = $data;
		}
	}
	
	/** 
	 *	Retrieve all the data about the family.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/17/14
	 *	@since		1.0
	 *
	 *	@param		string	$id	The family ID.
	 */
	public function get_family_data($id) {
		
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