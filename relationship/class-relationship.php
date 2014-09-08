<?php

/** 
 *	A class for determining blood relationships between members of a family.
 *
 *	@author		Nate Jacobs
 *	@date		9/7/14
 *	@since		1.0
 */
class TNG_Relationship extends FamilyRootsTNGDatabase {
	
	public $relationship;
	public $person_1_id;
	public $person_2_id;
	private $settings;
	
	/** 
	 *	 Begin the process of determining the relationship.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/7/14
	 *	@since		1.0
	 *
	 *	@param		int|string	$person_a_id		The id of the first person
	 *	@param		int|string	$person_b_id		The id of the second person
	 */
	public function __construct($person_a_id = 0, $person_b_id = 0) {
		if($person_a_id === 0 || $person_b_id === 0) {
			$this->relationship = false;
		}
		
		$data = false;
		
		// if both contain data
		if($person_a_id && $person_b_id) {
			$this->settings = ['tables' => get_option('family-roots-settings'), 'db' => parent::connect()];
			$this->person_1_id = $this->format_prefix($person_a_id);
			$this->person_2_id = $this->format_prefix($person_b_id);
			$data = $this->calculate_relationship();
		}
		
		if($data) {
			$this->relationship = $data;
		} else {
			$this->relationship = false;
		}
	}
	
	/** 
	 *	Run through the relationships and determine the blood relation.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/7/14
	 *	@since		1.0
	 */
	protected function calculate_relationship() {
		if ($this->person_1_id == $this->person_2_id) {
			return 'self';
		}
		
		$lca = $this->lowest_common_ancestor();
		
		if (!$lca) {
			return false;
		}
		
		$a_dist = $lca[1];
		$b_dist = $lca[2];
		
		$a_gen = $this->get_gender($this->person_1_id);
		
		// direct descendent - parent
		if($a_dist == 0) {
			$rel = $a_gen->sex == 'M' ? 'father' : 'mother';
			return $this->aggrandize_relationship($rel, $b_dist);
		}
		// direct descendent - child
		if($b_dist == 0) {
			$rel = $a_gen->sex == 'M' ? 'son' : 'daughter';
			return $this->aggrandize_relationship($rel, $a_dist);
		}
		
		// equal distance - siblings / cousins
		if($a_dist == $b_dist) {
			switch($a_dist) {
				case 1:
					return $a_gen->sex == 'M' ? 'brother' : 'sister';
					break;
				case 2:
					return 'cousin';
					break;
				default:
					return $this->ordinal_suffix($a_dist - 2).' cousin';
			}
		}
		
		// aunt or uncle
		if($a_dist == 1) {
			$rel = $a_gen->sex == 'M' ? 'uncle' : 'aunt';
			return $this->aggrandize_relationship($rel, $b_dist, 1);
		}
		
		// niece or nephew
		if($b_dist == 1) {
			$rel = $a_gen->sex == 'M' ? 'nephew' : 'niece';
			return $this->aggrandize_relationship($rel, $a_dist, 1);
		}
		
		// cousins - generationally removed
		$cous_ord = min($a_dist, $b_dist) - 1;
		$cous_gen = abs($a_dist - $b_dist);
		
		return $this->ordinal_suffix($cous_ord).' cousin '.$this->pluralize($cous_gen, 'time', 'times').' removed';
	}
	
	/** 
	 *	Find the lowest common ancestor.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/7/14
	 *	@since		1.0
	 */
	protected function lowest_common_ancestor() {
		// get all the shared ancestors
		$common_ancestors = $this->common_ancestors($this->person_1_id, $this->person_2_id);
		
		if(!$common_ancestors) {
			return false;
		}
		
		$least_distance = -1;
		$ld_index = -1;
		
		// loop through and get the one with the least distance
		foreach ($common_ancestors as $i => $c_anc) {
			if(!empty($c_anc[0])) {
				$distance = $c_anc[1] + $c_anc[2];
				if ($least_distance < 0 || $least_distance > $distance) {
					$least_distance = $distance;
					$ld_index = $i;
				}
			}
		}
		
		return $ld_index >= 0 ? $common_ancestors[$ld_index] : false;
	}
	
	/** 
	 *	Find all the common ancestors.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/7/14
	 *	@since		1.0
	 */
	protected function common_ancestors() {
		$common_ancestors = [];
		
		// get the ancestors of both persons
		$a_ancestors = $this->get_ancestors($this->person_1_id);
		$b_ancestors = $this->get_ancestors($this->person_2_id);
		
		if(!$a_ancestors || !$b_ancestors) {
			return false;
		}
		
		foreach ($a_ancestors as $a_anc) {
			foreach ($b_ancestors as $b_anc) {
				if ($a_anc[0] == $b_anc[0]) {
					$common_ancestors[] = [$a_anc[0], $a_anc[1], $b_anc[1]];
					break 1;
				}
			}
		}
		
		return $common_ancestors;
	}
	
	/** 
	 *	
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/7/14
	 *	@since		1.0
	 *
	 *	@param		string	$person	The id of the person.
	 *	@param		int	$dist	The current distance of the ancestor.
	 */
	protected function get_ancestors($person, $dist = 0) {
		$ancestors = [];
		
		// self
		$ancestors[] = [$person, $dist];
		
		// parents
		$parents = $this->get_parents($person);
		
		if(!$parents) {
			return false;
		}
		
		foreach($parents as $par) {
			if($par !== 0) {
				$par_ancestors = $this->get_ancestors($par, $dist + 1);
				foreach($par_ancestors as $par_anc) {
					$ancestors[] = $par_anc;
				}
			}
		}
		
		return $ancestors;
	}
	
	/** 
	 *	Return the parents of the person requested.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/7/14
	 *	@since		1.0
	 *
	 *	@param		string	$id	The id of the person.
	 */
	public function get_parents($id) {
		$parents = [0,0];
		
		// get the family ID
		$person_table = isset($this->settings['tables']['people_table']) ? $this->settings['tables']['people_table'] : false;
		
		if(!$person_table) {
			return false;
		}
		
		$family_id = $this->settings['db']->get_row( 
			$this->settings['db']->prepare("SELECT famc FROM {$person_table} WHERE personID = %s", $id)
		);
		
		// if there is a family ID get the parents
		if(!empty($family_id)) {
			$family_table = isset($this->settings['tables']['family_table']) ? $this->settings['tables']['family_table'] : false;
			
			$family = $this->settings['db']->get_row($this->settings['db']->prepare("SELECT husband, wife FROM {$family_table} WHERE familyID = %s", $family_id->famc));
			
			if(is_null($family)) {
				$parents = [0,0];
			} else {
				$parents = [$family->husband, $family->wife];
			}
		}
		
		return $parents;
	}
	
	/** 
	 *	Format the person ID so it can be used in all the database queries.
	 *	The TNG database expects the personID to have an I in front of the number.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/7/14
	 *	@since		1.0
	 *
	 *	@param		int|string	$id	The id of the person.
	 */
	protected function format_prefix($id) {
		if('I' === substr($id, 0, 1)) {
			$id = $id;
		} else {
			$id = 'I'.$id;
		}
		
		return $id;
	}
	
	/** 
	 *	Determine if the string should be plural or not.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/7/14
	 *	@since		1.0
	 *
	 *	@param		int	$count	The number.
	 *	@param		string	$singular	The singular string.
	 *	@param		string	$plural	The plural string.
	 */
	protected function pluralize($count, $singular, $plural) {
		return $count.' '.($count == 1 || $count == -1 ? $singular : $plural);
	}
	
	/** 
	 *	Return the sex of the person requested.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/7/14
	 *	@since		1.0
	 *
	 *	@param		string	$id	The ID of the person.
	 */
	protected function get_gender($id) {
		$person_table = isset($this->settings['tables']['people_table']) ? $this->settings['tables']['people_table'] : false;
		
		if(!$person_table) {
			return false;
		}
		
		$gender = $this->settings['db']->get_row( 
			$this->settings['db']->prepare("SELECT sex FROM {$person_table} WHERE personID = %s", $id)
		);
		
		if(!empty($gender)) {
			$gender = $gender;
		} else {
			$gender = 'M';
		}
		
		return $gender;
	}
	
	/** 
	 *	Add the appropriate prefix to the relationship.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/7/14
	 *	@since		1.0
	 *
	 *	@param		string	$rel		The single generation relationship.
	 *	@param		int	$dist	The generational distance between persons.
	 *	@param		int	$offset
	 */
	protected function aggrandize_relationship($rel, $dist, $offset = 0) {
		$dist -= $offset;
		
		switch($dist) {
			case 1:
				return $rel;
				break;
			case 2:
				return 'grand'.$rel;
				break;
			case 3:
				return 'great grand'.$rel;
				break;
			default:
				return $this->ordinal_suffix($dist - 2).' great grand'.$rel;
		}
	}
	
	/** 
	 *	Add the appropriate suffix to the relationship.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/7/14
	 *	@since		1.0
	 *
	 *	@param		int	$number	The number.
	 *	@param		bool		$super
	 */
	protected function ordinal_suffix($number, $super = false) {
		if($number % 100 > 10 && $number %100 < 14) {
			$os = 'th';
		} elseif($number == 0) {
			$os = '';
		} else {
			$last = substr($number, -1, 1);
		
			switch($last) {
				case "1":
					$os = 'st';
					break;
				case "2":
					$os = 'nd';
					break;
				case "3":
					$os = 'rd';
					break;
				default:
					$os = 'th';
			}
		}
		
		$os = $super ? '<sup>'.$os.'</sup>' : $os;
		
		return $number.$os;
	}
	
	/** 
	 *	Get the relationship string.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/7/14
	 *	@since		1.0
	 */
	public function get_relation() {
		return $this->relationship;
	}
	
	/** 
	 *	Return the TNG_Person object for the two people
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/7/14
	 *	@since		1.0
	 */
	public function get_people() {
		$person_1 = new TNG_Person($this->person_1_id);
		$person_2 = new TNG_Person($this->person_2_id);
		
		return [$person_1, $person_2];
	}
}