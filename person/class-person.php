<?php

/** 
 *	Return an object that contains the person information including events, children, parents, and partners from the TNG database.
 *
 *	@author		Nate Jacobs
 *	@date		8/10/14
 *	@since		1.0
 */
class TNG_Person extends FamilyRootsTNGDatabase {
	
	public $ID;
	
	public $data;
	
	public $events;
	
	public $parents;
	
	public $partners;
	
	public $children;
	
	/** 
	 *	Start up the person class. 
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/10/14
	 *	@since		1.0
	 *
	 *	@param		string	$id		The person ID.
	 *	@param		array	$name	The person's first and last name
	 */
	public function __construct($id = 0, $name = []) {
		if(!empty($id) && !is_numeric($id)) {
			$name = $id;
			$id = 0;
		}
		
		if($id) {
			$data = $this->get_data_by('id', $id);
		} else {
			$data = $this->get_data_by('name', $name);
		}
		
		if($data) {
			$this->data = $data;
			$this->ID = $data->ID;
			$this->events = $this->get_events_by_id();
			unset($this->events[0]);
			$this->parents = $this->get_person_parents();
			$this->children = $this->get_person_children();
			$this->partners = $this->get_person_partners();
		}
	}

	/** 
	 *	Return the person data from the TNG database.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/10/14
	 *	@since		1.0
	 *
	 *	@param		string	$field	Name or ID to use for searching.
	 *	@param		string	$value	The value to search for.
	 */
	private function get_data_by($field, $value) {
		$settings = $this->get_db_settings();
		
		$person_table = isset($settings['tables']['people_table']) ? $settings['tables']['people_table'] : false;
		
		if(!$person_table) {
			return  false;
		}
		
		if('id' === $field) {
			if(!is_numeric($value)) {
				return false;
			}
			
			if($value < 1) {
				return  false;
			}
			
			if('I' === substr($value, 0, 1)) {
				$value = $value;
			} else {
				$value = 'I'.$value;
			}
			
			return  $settings['db']->get_row( 
				$settings['db']->prepare("SELECT ID, personID AS person_id, lastname AS last_name, firstname AS first_name, birthdatetr AS birth_date, sex, birthplace AS birth_place, deathdatetr AS death_date, deathplace AS death_place, burialdatetr AS burial_date, burialplace AS burial_place, baptdatetr AS baptism_date, baptplace AS baptism_place, changedate AS change_date, nickname, title, prefix, suffix, famc AS family_id, living FROM {$person_table} WHERE personID = %s", $value)
			);
		} elseif('name' === $field) {
			return  $settings['db']->get_row( 
				$settings['db']->prepare("SELECT * FROM {$person_table} WHERE firstname = %s AND lastname = %s", sanitize_user($value['first']), sanitize_user($value['last']))
			);
		}
	}
	
	/** 
	 *	Retrieve all the person's events.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/17/14
	 *	@since		1.0
	 */
	private function get_events_by_id() {
		$settings = $this->get_db_settings();
		
		$event_table = isset($settings['tables']['events_table']) ? $settings['tables']['events_table'] : false;
		
		if(!$event_table) {
			return false;
		}
		
		$event_type_table = isset($settings['tables']['eventtypes_table']) ? $settings['tables']['eventtypes_table'] : false;
		
		return  $settings['db']->get_results($settings['db']->prepare("SELECT t1.eventdatetr AS event_date, t1.eventplace AS event_place, t1.age, t1.info, t2.eventtypeID AS event_type_id, t2.description, t2.display FROM {$event_table} AS t1 LEFT JOIN {$event_type_table} AS t2 ON t1.eventtypeID = t2.eventtypeID WHERE persfamID = %s", $this->data->person_id));
	}
	
	/** 
	 *	Retrieve the person's parents.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/17/14
	 *	@since		1.0
	 */
	public function get_person_parents() {
		$settings = $this->get_db_settings();
		
		$family_table = isset($settings['tables']['family_table']) ? $settings['tables']['family_table'] : false;
		
		if(!$family_table) {
			return false;
		}
		
		if(!empty($this->data->family_id)) {
			$family = $settings['db']->get_row($settings['db']->prepare("SELECT * FROM {$family_table} WHERE familyID = %s", $this->data->family_id));
			return (object) [
				'father' => substr($family->husband, 1),
				'mother' => substr($family->wife, 1),
				'marriage_date' => $family->marrdatetr,
				'marriage_place' => $family->marrplace
			];
		} else {
			return false;
		}
	}
	
	/** 
	 *	Retrieve all the person's children
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/17/14
	 *	@since		1.0
	 */
	public function get_person_children() {
		$settings = $this->get_db_settings();
		
		$family_table = isset($settings['tables']['family_table']) ? $settings['tables']['family_table'] : false;
		
		if(!$family_table) {
			return false;
		}
		
		$family = $settings['db']->get_results($settings['db']->prepare("SELECT familyID FROM {$family_table} WHERE husband = %s OR wife = %s", $this->data->person_id, $this->data->person_id));
		
		if(!empty($family)) {
			$children_table = isset($settings['tables']['children_table']) ? $settings['tables']['children_table'] : false;
			
			if(!$children_table) {
				return false;
			}
			
			foreach($family as $family_group) {
				$children_ids[] = $settings['db']->get_results($settings['db']->prepare("SELECT personID AS person FROM {$children_table} WHERE familyID = %s", $family_group->familyID));
			}
			
			$children = iterator_to_array(new RecursiveIteratorIterator(new RecursiveArrayIterator($children_ids)), FALSE);
		} else {
			$children = false;
		}
		
		return $children;
	}
	
	/** 
	 *	Retrieve all the person's spouses and partners.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/17/14
	 *	@since		1.0
	 */
	public function get_person_partners() {
		$settings = $this->get_db_settings();
		
		$family_table = isset($settings['tables']['family_table']) ? $settings['tables']['family_table'] : false;
		
		if(!$family_table) {
			return false;
		}
		$family_unit = $settings['db']->get_results($settings['db']->prepare("SELECT * FROM {$family_table} WHERE husband = %s OR wife = %s", $this->data->person_id, $this->data->person_id));
		
		foreach($family_unit as $key => $family) {
			if($this->data->person_id === $family->husband) {
				$partner[$key]['person_id'] = $family->wife;
				$partner[$key]['family_id'] = $family->familyID;
				$new_partner[] = (object) $partner[$key];
			} elseif($this->data->person_id === $family->wife) {
				$partner[$key]['person_id'] = $family->husband;
				$partner[$key]['family_id'] = $family->familyID;
				$new_partner[] = (object) $partner[$key];
			}
		}
		
		return $new_partner;
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
	 *	Retrieve the value of a property from the person or events table.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/10/14
	 *	@since		1.0
	 *
	 *	@param		string	$key		The property to return.
	 */
	public function __get($key) {
		$value = false;
		if(isset($this->data->$key)) {
			$value[] = $this->data->$key;
		} elseif(isset($this->parents->$key)) {
			$value[] = $this->parents->$key;
		} else {
			foreach($this->events as $event) {
				if(isset($event->$key)) {
					$value[] = $event->$key;
				}
			}
		}
		
		return $value;
	}
	
	/** 
	 *	Determine whether the key is present.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/17/14
	 *	@since		1.0
	 *
	 *	@param		string	$key		The property to check for.
	 */
	public function __isset($key) {
		$return = false;
		if(isset($this->data->$key)) {
			$return = true;
		} elseif(isset($this->parents->$key)) {
			$return = true;
		} elseif(isset($this->events)) {
			foreach($this->events as $event) {
				if(isset($event->$key)) {
					$return = true;
				}
			}
		}
		
		return $return;
	}
	/** 
	 *	Retrieve the value of a property from the person or events table.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/17/14
	 *	@since		1.0
	 *
	 *	@param		string	$key		The property to return.
	 */
	public function get($key) {
		return $this->__get($key);
	}
	
	/** 
	 *	Determine whether a property or meta key is set from the users and usermeta tables.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/17/14
	 *	@since		1.0
	 *
	 *	@param		string	$key		The property to check for.
	 */
	public function has_prop($key) {
		return $this->__isset($key);
	}
	
	/** 
	 *	Determine whether a person has parents.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/17/14
	 *	@since		1.0	
	 */
	public function has_parents() {
		if(!empty($this->parents)) {
			return true;
		} else {
			return false;
		}
	}
	
	/** 
	 *	Determine whether the user exists in the database.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/17/14
	 *	@since		1.0
	 */
	public function exists() {
		return !empty($this->ID);
	}
	
	/** 
	 *	Return an array of all the events.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/17/14
	 *	@since		1.0	
	 */
	public function get_events() {
		return $this->events;
	}
	
	/** 
	 *	Return the parents object.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/17/14
	 *	@since		1.0	
	 */
	public function get_parents() {
		return $this->parents;
	}
	
	/** 
	 *	Return the children array.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/17/14
	 *	@since		1.0	
	 */
	public function get_children() {
		return $this->children;
	}
}