<?php

/** 
 *	This class creates the pedigree.
 *	The standard output is an array of names, birth and death date and birth and death place.
 *	The class also allows for the return of the JSON encoded array.
 *
 *	This is very much a work in progress. The class only returns the person, and the persons parents, and persons grandparents at this point.
 *
 *	@author		Nate Jacobs
 *	@date		9/7/14
 *	@since		1.0
 */
class TNG_Pedigree {
	private $utilities;
	private $settings;
	private $pedigree;
	
	/** 
	 *	 Start up the class.
	 *	It only accepts a TNG_Person object for the first variable.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/7/14
	 *	@since		1.0
	 *
	 *	@param		TNG_Person object	$person	A TNG Person object.
	 *	@param		int	$depth	how many levels deep the pedigree should be generated for.
	 */
	public function __construct(TNG_Person $person, $depth = 4) {
		global $tng_db;
		$this->utilities = new FamilyRootsUtilities();
		$this->settings = ['tables' => get_option('family_roots_settings'), 'db' => $tng_db];
		
		$data = $this->build_array($person, $depth);
		
		if($data) {
			$this->pedigree = $data;
		} else {
			$this->pedigree = false;
		}
	}

	/** 
	 *	Create the person's pedigree.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/7/14
	 *	@since		1.0
	 *
	 *	@param		TNG_Person object	$person	The requested person.
	 */
	protected function build_array($person, $depth) {
		$pedigree_array = [
			'name' => $person->get('first_name').' '.$person->get('last_name'),
			'born' => $this->utilities->get_date_for_display($person->get('birth_date'), 'm/d/Y'),
			'died' => $this->utilities->is_living($person->get('living'), $person->get('birth_date')) ? '' : $this->utilities->get_date_for_display($person->get('death_date'), 'm/d/Y'),
			'birthPlace' => $person->get('birth_place'),
			'deathPlace' => $person->get('death_place')
		];
		
		if($person->has_parents()) {
			$parents = $person->get_parents();
			foreach($parents as $key => $parent) {
				if($key == 'father' && !empty($parent)) {
					$father = new TNG_Person($parent);
					$pedigree_array['parents'][0] = [
						'name' => $father->get('first_name').' '.$father->get('last_name'),
						'born' => $this->utilities->get_date_for_display($father->get('birth_date'), 'm/d/Y'),
						'died' => $this->utilities->is_living($father->get('living'), $father->get('birth_date')) ? '' : $this->utilities->get_date_for_display($father->get('death_date'), 'm/d/Y'),
						'birthPlace' => $father->get('birth_place'),
						'deathPlace' => $father->get('death_place')
					];
					
					if($father->has_parents()) {
						$father_parents = $father->get_parents();
						foreach($father_parents as $padre_key => $padre) {
							if($padre_key == 'father' && !empty($padre)) {
								$father_father = new TNG_Person($padre);
								$pedigree_array['parents'][0]['parents'][0] = [
									'name' => $father_father->get('first_name').' '.$father_father->get('last_name'),
									'born' => $this->utilities->get_date_for_display($father_father->get('birth_date'), 'm/d/Y'),
									'died' => $this->utilities->is_living($father_father->get('living'), $father_father->get('birth_date')) ? '' : $this->utilities->get_date_for_display($father_father->get('death_date'), 'm/d/Y'),
									'birthPlace' => $father_father->get('birth_place'),
									'deathPlace' => $father_father->get('death_place')
								];
							}
							
							if($padre_key == 'mother' && !empty($padre)) {
								$father_mother = new TNG_Person($padre);
								$pedigree_array['parents'][0]['parents'][1] = [
									'name' => $father_mother->get('first_name').' '.$father_mother->get('last_name'),
									'born' => $this->utilities->get_date_for_display($father_mother->get('birth_date'), 'm/d/Y'),
									'died' => $this->utilities->is_living($father_mother->get('living'), $father_mother->get('birth_date')) ? '' : $this->utilities->get_date_for_display($father_mother->get('death_date'), 'm/d/Y'),
									'birthPlace' => $father_mother->get('birth_place'),
									'deathPlace' => $father_mother->get('death_place')
								];
							}
						}
					}
				}
				
				if($key == 'mother' && !empty($parent)) {
					$mother = new TNG_Person($parent);
					$pedigree_array['parents'][1] = [
						'name' => $mother->get('first_name').' '.$mother->get('last_name'),
						'born' => $this->utilities->get_date_for_display($mother->get('birth_date'), 'm/d/Y'),
						'died' => $this->utilities->is_living($mother->get('living'), $mother->get('birth_date')) ? '' : $this->utilities->get_date_for_display($mother->get('death_date'), 'm/d/Y'),
						'birthPlace' => $mother->get('birth_place'),
						'deathPlace' => $mother->get('death_place')
					];
					
					if($mother->has_parents()) {
						$mother_parents = $mother->get_parents();
						foreach($mother_parents as $madre_key => $madre) {
							if($madre_key == 'father' && !empty($madre)) {
								$mother_father = new TNG_Person($madre);
								$pedigree_array['parents'][1]['parents'][0] = [
									'name' => $mother_father->get('first_name').' '.$mother_father->get('last_name'),
									'born' => $this->utilities->get_date_for_display($mother_father->get('birth_date'), 'm/d/Y'),
									'died' => $this->utilities->is_living($mother_father->get('living'), $mother_father->get('birth_date')) ? '' : $this->utilities->get_date_for_display($mother_father->get('death_date'), 'm/d/Y'),
									'birthPlace' => $mother_father->get('birth_place'),
									'deathPlace' => $mother_father->get('death_place')
								];
							}
							
							if($madre_key == 'mother' && !empty($madre)) {
								$mother_mother = new TNG_Person($madre);
								$pedigree_array['parents'][1]['parents'][1] = [
									'name' => $mother_mother->get('first_name').' '.$mother_mother->get('last_name'),
									'born' => $this->utilities->get_date_for_display($mother_mother->get('birth_date'), 'm/d/Y'),
									'died' => $this->utilities->is_living($mother_mother->get('living'), $mother_mother->get('birth_date')) ? '' : $this->utilities->get_date_for_display($mother_mother->get('death_date'), 'm/d/Y'),
									'birthPlace' => $mother_mother->get('birth_place'),
									'deathPlace' => $mother_mother->get('death_place')
								];
							}
						}
					}
				}
			}
		}
		
		return $pedigree_array;
	}
	
	/** 
	 *	Return the pedigree as an array.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/12/14
	 *	@since		1.0
	 */
	public function get_pedigree() {
		return $this->pedigree;
	}
	
	/** 
	 *	Return the JSON encoded pedigree.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/7/14
	 *	@since		1.0
	 */
	public function get_pedigree_json() {
		return json_encode($this->pedigree);
	}
}