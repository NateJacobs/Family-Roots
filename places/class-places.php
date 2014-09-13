<?php

/** 
 *	Return a places object that contains all the information about a specific location.
 *
 *	@author		Nate Jacobs
 *	@date		9/13/14
 *	@since		1.0
 */
class TNG_Place extends FamilyRootsTNGDatabase {
	public $ID;
	public $data;
	
	/** 
	 *	Start up the places class.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/13/14
	 *	@since		1.0
	 *
	 *	@param		string	$id		The place ID.
	 *	@param		array	$name	The place name.
	 */
	public function __construct($id=0, $name = null) {
		if(!is_null($name) && $id === 0) {
			$name = $name;
		}
		
		$this->settings = ['tables' => get_option('family-roots-settings'), 'db' => parent::connect()];
		
		if($id) {
			$data = $this->get_data_by('id', (int) $id);
		} else {
			$data = $this->get_data_by('name', $name);
		}
		
		if($data) {
			$this->ID = (int) $data->ID;
			$this->data = $data;
		}
	}

	/** 
	 *	Return the place data from the TNG database.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/13/14
	 *	@since		1.0
	 *
	 *	@param		string	$field	Name or ID to use for searching.
	 *	@param		string	$value	The value to search for.
	 */
	private function get_data_by($field, $value) {
		$places_table = isset($this->settings['tables']['places_table']) ? $this->settings['tables']['places_table'] : false;
		
		if(!$places_table) {
			return false;
		}
		
		if($field === 'id') {
			return $this->settings['db']->get_row( 
				$this->settings['db']->prepare("SELECT ID, place, longitude, latitude, notes FROM {$places_table} WHERE ID = %d", $value)
			);
		} elseif( $field === 'name') {
			return $this->settings['db']->get_row( 
				$this->settings['db']->prepare("SELECT ID, place, longitude, latitude, notes FROM {$places_table} WHERE place = %s", $value)
			);
		}
	}
	
	/** 
	 *	Return an array of all the people with this location as either the birth place, death place or burial place.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/13/14
	 *	@since		1.0
	 *
	 *	@param		string	$type	The type of event to search the person table for a match with the place.
	 */
	private function get_person_by_place($type) {
		if(!$this->exists()) {
			return false;
		}
		
		$person_table = isset($this->settings['tables']['people_table']) ? $this->settings['tables']['people_table'] : false;
		
		if(!$person_table) {
			return  false;
		}
		
		switch($type) {
			case 'birth':
				$field = 'birthplace';
				break;
			case 'death':
				$field = 'deathplace';
				break;
			case 'burial':
				$field = 'burialplace';
				break;
			default:
				$field = 'birthplace';
		}
		
		$places = $this->settings['db']->get_results( 
			$this->settings['db']->prepare("SELECT personID FROM {$person_table} WHERE $field = %s", $this->data->place)
		);
		
		if(empty($places)) {
			return false;
		} else {
			return $places;
		}
	}
	
	/** 
	 *	Return an array of all the familes with this location as the birth place.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/13/14
	 *	@since		1.0
	 *
	 *	@param		string	$type	The type of event to search the person table for a match with the place.
	 */
	private function get_family_by_place($type) {
		if(!$this->exists()) {
			return false;
		}
		
		$family_table = isset($this->settings['tables']['family_table']) ? $this->settings['tables']['family_table'] : false;
		
		if(!$family_table) {
			return  false;
		}
		
		switch($type) {
			case 'marriage':
				$field = 'marrplace';
				break;
			case 'divorce':
				$field = 'divplace';
				break;
			default:
				$field = 'marrplace';
		}
		
		$places = $this->settings['db']->get_results( 
			$this->settings['db']->prepare("SELECT familyID, husband, wife FROM {$family_table} WHERE $field = %s", $this->data->place)
		);
		
		if(empty($places)) {
			return false;
		} else {
			return $places;
		}
	}
	
	/** 
	 *	Retrieve the value of a property from the places table.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/13/14
	 *	@since		1.0
	 *
	 *	@param		string	$key		The property to return.
	 */
	public function __get($key) {
		$value = false;
		if(isset($this->data->$key)) {
			$value = $this->data->$key;
		}
		
		return $value;
	}
	
	/** 
	 *	Determine whether the key is present in the places table.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/13/14
	 *	@since		1.0
	 *
	 *	@param		string	$key		The property to check for.
	 */
	public function __isset($key) {
		$return = false;
		if(isset($this->data->$key) && !empty($this->data->$key)) {
			$return = true;
		}
		
		return $return;
	}
	
	/** 
	 *	Retrieve the value of a property from the places table.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/13/14
	 *	@since		1.0
	 *
	 *	@param		string	$key		The property to return.
	 */
	public function get($key) {
		return $this->__get($key);
	}
	
	/** 
	 *	Determine whether a property is present from the places table.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/13/14
	 *	@since		1.0
	 *
	 *	@param		string	$key		The property to check for.
	 */
	public function has_prop($key) {
		return $this->__isset($key);
	}
	
	/** 
	 *	Determine whether the place exists in the database.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/13/14
	 *	@since		1.0
	 */
	public function exists() {
		return !empty($this->ID);
	}
	
	/** 
	 *	Return a list of personIDs who were born at that location.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/13/14
	 *	@since		1.0
	 */
	public function get_births() {
		return $this->get_person_by_place('birth');
	}
	
	/** 
	 *	Return a list of personIDs who died at that location.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/13/14
	 *	@since		1.0
	 */
	public function get_deaths() {
		return $this->get_person_by_place('death');
	}
	
	/** 
	 *	Return a list of personIDs who are buried at that location.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/13/14
	 *	@since		1.0
	 */
	public function get_burials() {
		return $this->get_person_by_place('burial');
	}
	
	/** 
	 *	Return a list of personIDs who were married at that location.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/13/14
	 *	@since		1.0
	 */
	public function get_marriages() {
		return $this->get_family_by_place('marriage');
	}
	
	/** 
	 *	Return a list of personIDs who were divorced at that location.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/13/14
	 *	@since		1.0
	 */
	public function get_divorces() {
		return $this->get_family_by_place('divorce');
	}
}