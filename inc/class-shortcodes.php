<?php

/** 
 *	Create the person and family shortcodes.
 *
 *	@author		Nate Jacobs
 *	@date		10/19/14
 *	@since		1.0
 */
class FamilyRootsShortcodes {
	/** 
	 *	Hook into WordPress and prepare all the methods as necessary.
	 *
	 *	@author		Nate Jacobs
	 *	@date		10/19/14
	 *	@since		1.0
	 */
	public function __construct() {
		add_shortcode('family_roots_person', [$this, 'family_roots_person']);
		add_shortcode('family_roots_family', [$this, 'family_roots_family']);
	}

	/** 
	 *	Return basic details about a person: Name, age, birth, death, burial.
	 *
	 *	@author		Nate Jacobs
	 *	@date		10/19/14
	 *	@since		1.0
	 *
	 *	@param		array	$attributes	The shortcode attributes
	 */
	public function family_roots_person($attributes) {
		$a = shortcode_atts(['id' => 1, 'class' => 'well well-sm'], $attributes, 'family_roots_person');
		
		if(is_numeric($a['id'])) {
			$person = new TNG_Person($a['id']);
			
			if($person->exists()) {
				$utilities = new FamilyRootsUtilities();
				$response = '<div class="'.$a['class'].'">';
				$response .= '<h4><a href="'.$utilities->get_person_url($person).'">'.$person->get('first_name').' '.$person->get('last_name').'</a></h4>';
				if($utilities->living_allowed($person)) {
					$response .= ' Age: '.$utilities->get_person_age($person->get('birth_date'), $person->get('death_date'));
					
					if(!$person->living) {
						$response .= '<small> Deceased</small>';
					}
					
					$person_birth_place = $person->get('birth_place');
					$birth_place_object = new TNG_Place(null, $person_birth_place);
					$birth_place = !empty($person_birth_place) ?  ' &mdash; <a href="'.$utilities->get_place_url($birth_place_object).'">'.$person->get('birth_place').'</a>' : '';
					$response .= '<br><br><table class="table">';
					$response .= '<tr><td>Birth:</td>';
					$response .= '<td>'.$utilities->get_date_for_display($person->get('birth_date')).$birth_place.'</td></tr>';
					
					if(!$person->living) {
						$person_death_place = $person->get('death_place');
						$death_place_object = new TNG_Place(null, $person_death_place);
						$death_place = !empty($person_death_place) ?  ' &mdash; <a href="'.$utilities->get_place_url($death_place_object).'">'.$person->get('death_place').'</a>' : '';
						$response .= '<tr><td>Death:</td>';
						$response .= '<td>'.$utilities->get_date_for_display($person->get('death_date')).$death_place.'</td></tr>';
					}
					
					$response .= apply_filters('family_roots_person_shortcode_table', '', $person);
					$response .= '</table>';
				}
				
				$response .= '</div>';
			} else {
				$response = '';
			}
		} else {
			$response = '';
		}
		
		return $response;
	}
	
	/** 
	 *	Return basic details about the family.
	 *
	 *	@author		Nate Jacobs
	 *	@date		10/19/14
	 *	@since		1.0
	 *
	 *	@param		array	$attributes	The shortcode attributes
	 */
	public function family_roots_family($attributes) {
		$a = shortcode_atts(['id' => 1, 'class' => 'well well-sm'], $attributes, 'family_roots_family');
		if(is_numeric($a['id'])) {
			$family = new TNG_Family($a['id']);
			
			if($family->exists()) {
				$utilities = new FamilyRootsUtilities();
				$response = '<div class="'.$a['class'].'">';
				$response .= '<h4><a href="'.$utilities->get_family_url($family).'">Family of '.$family->get_father_name().' and '.$family->get_mother_name().'</a></h4>';
				
				if($utilities->living_allowed($family->get('father')) && $utilities->living_allowed($family->get('mother'))) {
					$marriage_place_object = new TNG_Place(null, $family->marriage_place);
					$divorce_place_object = new TNG_Place(null, $family->divorce_place);
					$married = '0000-00-00' != $family->get('marriage_date') ? $utilities->get_date_for_display($family->get('marriage_date')).' &mdash; <a href="'.$utilities->get_place_url($marriage_place_object).'">'.$family->get('marriage_place').'</a>' : '';
					$divorced = '0000-00-00' != $family->get('divorce_date') ? $utilities->get_date_for_display($family->get('divorce_date')).' &mdash; <a href="'.$utilities->get_place_url($divorce_place_object).'">'.$family->get('divorce_place').'</a>' : '';
					
					$response .= '<dl>';
					
					if(!empty($married)) {
						$response .= '<dt>Marriage</dt><dd>'.$married.'</dd>';
					}
					
					if(!empty($divorced)) {
						$response .= '<dt>Divorce</dt><dd>'.$divorced.'</dd>';
					}
					
					if($family->has_children()) {
						$response .= '<dt>Number of Children</dt>';
						$response .= '<dd>'.count($family->get_children()).'</dd>';
					}
					
					$response .= apply_filters('family_roots_family_shortcode_dl', '', $family);
					
					$response .= '</dl>';
				}
				
				$response .= '</div>';
			}
		}
		
		return $response;
	}
}

$family_roots_shortcodes = new FamilyRootsShortcodes();