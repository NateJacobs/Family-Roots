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
	
	private $settings;
	
	public $partners;
	
	public $children;
	
	public $siblings;
	
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
		if(!empty($id) && (!is_numeric($id) && 'I' != substr($id, 0, 1))) {
			$name = $id;
			$id = 0;
		}
		
		$this->settings = ['tables' => get_option('family-roots-settings'), 'db' => parent::connect()];
		
		if($id) {
			$data = $this->get_data_by('id', $id);
		} else {
			$data = $this->get_data_by('name', $name);
		}
		
		if($data) {
			$this->data = $data;
			$this->ID = (int) $data->ID;
			$this->events = $this->get_person_events();
			$this->parents = $this->get_person_parents();
			$this->children = $this->get_person_children();
			$this->partners = $this->get_person_partners();
			$this->siblings = $this->get_person_siblings();
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
		$person_table = isset($this->settings['tables']['people_table']) ? $this->settings['tables']['people_table'] : false;
		
		if(!$person_table) {
			return  false;
		}
		
		if('id' === $field) {
			if('I' === substr($value, 0, 1)) {
				$value = $value;
			} else {
				$value = 'I'.$value;
			}
			
			return $this->settings['db']->get_row( 
				$this->settings['db']->prepare("SELECT ID, personID AS person_id, lastname AS last_name, firstname AS first_name, birthdatetr AS birth_date, sex, birthplace AS birth_place, deathdatetr AS death_date, deathplace AS death_place, burialdatetr AS burial_date, burialplace AS burial_place, baptdatetr AS baptism_date, baptplace AS baptism_place, changedate AS change_date, nickname, title, prefix, suffix, famc AS family_id, living FROM {$person_table} WHERE personID = %s", $value)
			);
		} elseif('name' === $field) {
			return $this->settings['db']->get_row( 
				$this->settings['db']->prepare("SELECT ID, personID AS person_id, lastname AS last_name, firstname AS first_name, birthdatetr AS birth_date, sex, birthplace AS birth_place, deathdatetr AS death_date, deathplace AS death_place, burialdatetr AS burial_date, burialplace AS burial_place, baptdatetr AS baptism_date, baptplace AS baptism_place, changedate AS change_date, nickname, title, prefix, suffix, famc AS family_id, living FROM {$person_table} WHERE firstname = %s AND lastname = %s", sanitize_user($value['first']), sanitize_user($value['last']))
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
	private function get_person_events() {
		$event_table = isset($this->settings['tables']['events_table']) ? $this->settings['tables']['events_table'] : false;
		
		if(!$event_table) {
			return false;
		}
		
		$event_type_table = isset($this->settings['tables']['eventtypes_table']) ? $this->settings['tables']['eventtypes_table'] : false;
		$note_links_table = isset($this->settings['tables']['notelinks_table']) ? $this->settings['tables']['notelinks_table'] : false;
		$xnotes_table = isset($this->settings['tables']['xnotes_table']) ? $this->settings['tables']['xnotes_table'] : false;
		
		return $this->settings['db']->get_results($this->settings['db']->prepare("SELECT t1.eventdatetr AS event_date, t1.eventplace AS event_place, t1.age, t1.info, t2.eventtypeID AS event_type_id, t2.description, t2.display, t4.note FROM {$event_table} AS t1 LEFT JOIN {$event_type_table} AS t2 ON t1.eventtypeID = t2.eventtypeID LEFT JOIN {$note_links_table} AS t3 ON t3.eventID = t1.eventID LEFT JOIN {$xnotes_table} AS t4 on t3.xnoteID = t4.ID WHERE t1.persfamID = %s", $this->data->person_id));
	}
	
	/** 
	 *	Retrieve the person's parents.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/17/14
	 *	@since		1.0
	 */
	public function get_person_parents() {
		$family_table = isset($this->settings['tables']['family_table']) ? $this->settings['tables']['family_table'] : false;
		
		if(!$family_table) {
			return false;
		}
		
		if(!empty($this->data->family_id)) {
			$family = $this->settings['db']->get_row($this->settings['db']->prepare("SELECT * FROM {$family_table} WHERE familyID = %s", $this->data->family_id));
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
		$family_table = isset($this->settings['tables']['family_table']) ? $this->settings['tables']['family_table'] : false;
		
		if(!$family_table) {
			return false;
		}
		
		$family = $this->settings['db']->get_results($this->settings['db']->prepare("SELECT familyID FROM {$family_table} WHERE husband = %s OR wife = %s", $this->data->person_id, $this->data->person_id));
		
		if(!empty($family)) {
			$children_table = isset($this->settings['tables']['children_table']) ? $this->settings['tables']['children_table'] : false;
			
			if(!$children_table) {
				return false;
			}
			
			foreach($family as $family_group) {
				$children_ids[] = $this->settings['db']->get_results($this->settings['db']->prepare("SELECT personID AS person FROM {$children_table} WHERE familyID = %s ORDER BY ordernum", $family_group->familyID));
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
		$family_table = isset($this->settings['tables']['family_table']) ? $this->settings['tables']['family_table'] : false;
		
		if(!$family_table) {
			return false;
		}
		$family_unit = $this->settings['db']->get_results($this->settings['db']->prepare("SELECT * FROM {$family_table} WHERE husband = %s OR wife = %s", $this->data->person_id, $this->data->person_id));
		
		if(!empty($family_unit)) {		
			foreach($family_unit as $key => $family) {
				if($this->data->person_id === $family->husband) {
					$partner[$key]['person_id'] = $family->wife;
				} elseif($this->data->person_id === $family->wife) {
					$partner[$key]['person_id'] = $family->husband;
				}
				
				$partner[$key]['family_id'] = $family->familyID;
				$partner[$key]['marriage_date'] = $family->marrdatetr;
				$partner[$key]['marriage_place'] = $family->marrplace;
				$partner[$key]['divorce_date'] = $family->divdatetr;
				$partner[$key]['divorce_place'] = $family->divplace;
				
				$new_partner[] = (object) $partner[$key];
			}
		} else {
			$new_partner = false;
		}
		return $new_partner;
	}
	
	/** 
	 *	Retrieve all the person's siblings.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/23/14
	 *	@since		1.0
	 */
	public function get_person_siblings() {
		if($this->has_parents()) {
			$parents = $this->get_parents();
			
			$family_table = isset($this->settings['tables']['family_table']) ? $this->settings['tables']['family_table'] : false;
			
			if(!$family_table) {
				return false;
			}
			
			$father = 'I' === substr($parents->father, 0, 1) ? $parents->father : 'I'.$parents->father;
			$mother = 'I' === substr($parents->mother, 0, 1) ? $parents->mother : 'I'.$parents->mother;
			
			$family = $this->settings['db']->get_results($this->settings['db']->prepare("SELECT familyID FROM {$family_table} WHERE husband = %s OR wife = %s", $father, $mother));
			
			if(!empty($family)) {
				$children_table = isset($this->settings['tables']['children_table']) ? $this->settings['tables']['children_table'] : false;
				
				if(!$children_table) {
					return false;
				}
				
				foreach($family as $family_group) {
					$sibling_ids[] = $this->settings['db']->get_results($this->settings['db']->prepare("SELECT personID AS person FROM {$children_table} WHERE familyID = %s ORDER BY ordernum", $family_group->familyID));
				}
				
				$siblings = iterator_to_array(new RecursiveIteratorIterator(new RecursiveArrayIterator($sibling_ids)), FALSE);
				
				// remove the current person
				$person_key = array_search($this->data->person_id, $siblings);
				unset($siblings[$person_key]);
			} else {
				$siblings = false;
			}
			
			return $siblings;
		}
	}
	
	/** 
	 *	Get all the notes for the person.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/23/14
	 *	@since		1.0
	 */
	public function get_notes() {
		$note_links_table = isset($this->settings['tables']['notelinks_table']) ? $this->settings['tables']['notelinks_table'] : false;
		$xnotes_table = isset($this->settings['tables']['xnotes_table']) ? $this->settings['tables']['xnotes_table'] : false;
		$event_table = isset($this->settings['tables']['events_table']) ? $this->settings['tables']['events_table'] : false;
		$event_type_table = isset($this->settings['tables']['eventtypes_table']) ? $this->settings['tables']['eventtypes_table'] : false;
				
		if(!$note_links_table) {
			return false;
		}
		
		$notes = $this->settings['db']->get_results($this->settings['db']->prepare("SELECT display, $xnotes_table.note as note, $note_links_table.eventID as eventID, $note_links_table.xnoteID as xnoteID, $note_links_table.ID as ID, noteID FROM {$note_links_table} LEFT JOIN {$xnotes_table} on $note_links_table.xnoteID = $xnotes_table.ID LEFT JOIN {$event_table} ON $note_links_table.eventID = $event_table.eventID LEFT JOIN {$event_type_table} on $event_type_table.eventtypeID = $event_table.eventtypeID WHERE $note_links_table.persfamID = %s ORDER BY eventdatetr, $event_type_table.ordernum, tag, $note_links_table.ordernum, ID", $this->data->person_id));
		
		return $notes;
	}
	
	/** 
	 *	Get all the media for the person.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/5/14
	 *	@since		1.0
	 */
	public function get_media() {
		$media_links_table = isset($this->settings['tables']['media_links_table']) ? $this->settings['tables']['media_links_table'] : false;
		
		$media_table = isset($this->settings['tables']['media_table']) ? $this->settings['tables']['media_table'] : false;
		
		if(!$media_links_table) {
			return false;
		}
		
		$media = $this->settings['db']->get_results($this->settings['db']->prepare("SELECT $media_table.path AS media_path, $media_table.thumbpath AS thumb_path, $media_table.mediatypeID AS media_type, $media_table.description FROM {$media_links_table} LEFT JOIN {$media_table} ON $media_table.mediaID = $media_links_table.mediaID WHERE $media_links_table.personID = %s", $this->data->person_id));
		
		return $media;
	}
	
	/** 
	 *	Retrieve the value of a property from the person or parents table.
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
			$value = $this->data->$key;
		} elseif(isset($this->parents->$key)) {
			$value = $this->parents->$key;
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
	 *	Return the children array.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/17/14
	 *	@since		1.0	
	 */
	public function get_children() {
		return $this->children;
	}
	
	/** 
	 *	Determine whether a person has children.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/17/14
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
	 *	Return the partner array.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/23/14
	 *	@since		1.0
	 */
	public function get_partners() {
		return $this->partners;
	}
	
	/** 
	 *	Determine whether a person has parents.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/17/14
	 *	@since		1.0	
	 */
	public function has_partners() {
		if(!empty($this->partners)) {
			return true;
		} else {
			return false;
		}
	}
	
	/** 
	 *	Return the siblings array.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/27/14
	 *	@since		1.0
	 */
	public function get_siblings() {
		return $this->siblings;
	}
	
	/** 
	 *	Determine whether a person has siblings.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/27/14
	 *	@since		1.0	
	 */
	public function has_siblings() {
		if(!empty($this->siblings)) {
			return true;
		} else {
			return false;
		}
	}
}