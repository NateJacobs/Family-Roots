<?php

/** 
 *	TNG family class.
 *
 *	@author		Nate Jacobs
 *	@date		8/10/14
 *	@since		1.0
 */
class TNG_Family {
	
	public $ID;
	
	public $parents;
	
	public $children;
	
	private $settings;
	
	/** 
	 *	Start up the family class.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/10/14
	 *	@since		1.0
	 *
	 *	@param		
	 */
	public function __construct($id = 0) {
		global $tng_db;
		
		if(!empty($id) && (!is_numeric($id) && 'F' != substr($id, 0, 1))) {
			$id = 0;
		}
		
		$this->settings = ['tables' => get_option('family-roots-settings'), 'db' => $tng_db];
		
		if($id) {
			$data = $this->get_family_parents($id);
		}
		
		if($data) {
			$this->parents = $data;
			$this->ID = (int) $data->family_id;
			$this->children = $this->get_family_children();
		}
	}
	
	/** 
	 *	Checks if the ID is prefixed with an 'F'.
	 *	If it is not, add a 'F' and return the new id.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/24/14
	 *	@since		1.0
	 *
	 *	@param		string	$id	The family ID.
	 */
	private function add_id_prefix($id) {
		if('F' === substr($id, 0, 1)) {
			$id = $id;
		} else {
			$id = 'F'.$id;
		}
		
		return $id;
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
	protected function get_family_parents($id) {
		$family_table = isset($this->settings['tables']['family_table']) ? $this->settings['tables']['family_table'] : false;
		
		if(!$family_table) {
			return false;
		}
		
		$family = $this->settings['db']->get_row($this->settings['db']->prepare("SELECT * FROM {$family_table} WHERE $family_table.familyID = %s", $this->add_id_prefix($id)));
		
		if(!is_null($family)) {
			return (object) [
				'family_id' => $family->ID,
				'father' => new TNG_Person($family->husband),
				'mother' => new TNG_Person($family->wife),
				'marriage_date' => $family->marrdatetr,
				'marriage_place' => $family->marrplace,
				'divorce_date' => $family->divdatetr,
				'divorce_place' => $family->divplace
			];
		} else {
			return false;
		}
	}
	
	/** 
	 *	
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/24/14
	 *	@since		1.0
	 */
	protected function get_family_children() {
		$children_table = isset($this->settings['tables']['children_table']) ? $this->settings['tables']['children_table'] : false;
		
		if(!$children_table) {
			return false;
		}
		
		$children = $this->settings['db']->get_results($this->settings['db']->prepare("SELECT personID AS person FROM {$children_table} WHERE familyID = %s ORDER BY ordernum", $this->add_id_prefix($this->ID)));
		
		$children = iterator_to_array(new RecursiveIteratorIterator(new RecursiveArrayIterator($children)), FALSE);
		
		if(!empty($children)) {
			foreach($children as $child) {
				$family_children[] = new TNG_Person($child);
			}
		} else {
			$family_children = false;
		}
		
		return $family_children;
	}
	
	/** 
	 *	Retrieve the value of a property from the family table.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/10/14
	 *	@since		1.0
	 *
	 *	@param		string	$key		The property to return.
	 */
	public function __get($key) {
		$value = false;
		if(isset($this->parents->$key)) {
			$value = $this->parents->$key;
		}
		
		return $value;
	}
	
	/** 
	 *	Determine whether the key is present in the family table.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/24/14
	 *	@since		1.0
	 *
	 *	@param		string	$key		The property to check for.
	 */
	public function __isset($key) {
		$return = false;
		if(isset($this->parents->$key) && !empty($this->parents->$key) && '0000-00-00' != $this->parents->$key) {
			$return = true;
		}
		
		return $return;
	}
	
	/** 
	 *	Retrieve the value of a property from the family table.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/24/14
	 *	@since		1.0
	 *
	 *	@param		string	$key		The property to return.
	 */
	public function get($key) {
		return $this->__get($key);
	}
	
	/** 
	 *	Determine whether a property or meta key is set from the family table.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/24/14
	 *	@since		1.0
	 *
	 *	@param		string	$key		The property to check for.
	 */
	public function has_prop($key) {
		return $this->__isset($key);
	}
	
	/** 
	 *	Determine whether the family exists in the database.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/24/14
	 *	@since		1.0
	 */
	public function exists() {
		return !empty($this->ID);
	}
	
	/** 
	 *	Return the parents object.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/24/14
	 *	@since		1.0	
	 */
	public function get_parents() {
		return $this->parents;
	}
	
	/** 
	 *	Return the children object.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/24/14
	 *	@since		1.0	
	 */
	public function get_children() {
		return $this->children;
	}
	
	/** 
	 *	Determine whether a family has children.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/24/14
	 *	@since		1.0	
	 */
	public function has_children() {
		if(!empty($this->children)) {
			return true;
		} else {
			return false;
		}
	}
	
	/** 
	 *	Returns the father's name.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/24/14
	 *	@since		1.0
	 */
	public function get_father_name() {
		if($this->exists()) {
			$father = $this->get('father');
			$name = $father->first_name.' '.$father->last_name;
		} else {
			$name = false;
		}
		
		return $name;
	}
	
	/** 
	 *	Returns the mother's name.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/24/14
	 *	@since		1.0
	 */
	public function get_mother_name() {
		if($this->exists()) {
			$father = $this->get('mother');
			$name = $father->first_name.' '.$father->last_name;
		} else {
			$name = false;
		}
		
		return $name;
	}
}