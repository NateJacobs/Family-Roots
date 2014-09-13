<?php

/** 
 *	Return a places object that contains all the information about a specific location.
 *
 *	@author		Nate Jacobs
 *	@date		9/13/14
 *	@since		1.0
 */
class TNG_Places extends FamilyRootsTNGDatabase {
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
			return  false;
		}
		
		if($field === 'id') {
			return  $this->settings['db']->get_row( 
				$this->settings['db']->prepare("SELECT ID, place, longitude, latitude, notes FROM {$places_table} WHERE ID = %d", $value)
			);
		} elseif( $field === 'name') {
			return  $this->settings['db']->get_row( 
				$this->settings['db']->prepare("SELECT ID, place, longitude, latitude, notes FROM {$places_table} WHERE place = %s", $value)
			);
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
}