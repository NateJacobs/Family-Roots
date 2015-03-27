<?php

/**
 * Return a places object that contains all the information about a specific location.
 *
 * @author  Nate Jacobs
 * @date  3/13/15
 * @since  1.3
 */
class TNG_Locality {
	public $place;
	public $data;
	public $allow_numeric;

	/**
	 * Start up the locality class.
	 *
	 * @author Nate Jacobs
	 * @date 3/13/15
	 * @since 1.3
	 *
	 * @param array $location The location parts.
	 * @param array $allow_numeric Allow the query to return places with numbers in them.
	 */
	public function __construct( $location = null, $allow_numeric = true ) {
		global $tng_db;
		$data = false;

		if ( is_null( $location ) ) {
			return false;
		}
		
		$this->allow_numeric = $allow_numeric;
		
		$this->settings = ['tables' => get_option('family_roots_settings'), 'db' => $tng_db];

		if ( !empty( $location[2] ) ) {
			$data = $this->get_data( $location, -3 );
		} elseif ( !empty( $location[1] ) ) {
			$data = $this->get_data( $location, -2 );
		} else {
			$data = $this->get_data( $location, -1 );
		}
		
		if(!is_null( $data->place ) ){
			$this->data = $data;
			$this->place = $data->place;
			$this->locations = $this->get_locations();
		}
	}

	/**
	 * Return the locality data from the TNG database.
	 *
	 * @author Nate Jacobs
	 * @date 3/13/15
	 * @since 1.3
	 *
	 * @param string $locality The locality to search for.
	 * @param int $offset The count of deliminators to use in the query.
	 */
	public function get_data( $locality, $offset ) {
		$this->offset = $offset;
		$places_table = isset($this->settings['tables']['places_table']) ? $this->settings['tables']['places_table'] : false;

		if (!$places_table) {
			return false;
		}

		$clean_location = $this->clean_location( $locality );
		
		if( $this->allow_numeric ) {
			$place = $this->settings['db']->get_row(
				$this->settings['db']->prepare("SELECT trim(substring_index(place,',',%d)) as place, count(place) as count FROM {$places_table} WHERE trim(substring_index(place,',',%d)) = %s", $offset, $offset, $clean_location)
			);	
		} else {
			$place = $this->settings['db']->get_row(
				$this->settings['db']->prepare("SELECT trim(substring_index(place,',',%d)) as place, count(place) as count FROM {$places_table} WHERE trim(substring_index(place,',',%d)) = %s AND substring_index(place,' ',1) NOT REGEXP '[0-9]+'", $offset, $offset, $clean_location)
			);
		}
		
		return $place;
	}

	/**
	 * Parse the provided location array and replace dashes with spaces and
	 * decode the string if neccessary.
	 *
	 * @author Nate Jacobs
	 * @date 3/13/15
	 * @since 1.3
	 *
	 * @param array $locality The location array to be cleaned up.
	 */
	public function clean_location( $locality ) {
		$clean_location = [];

		foreach ( $locality as $location ) {
			$clean_location[] = urldecode( str_replace( '-', ' ', $location ) );
		}

		$clean_location = implode( ', ', array_filter( array_reverse( $clean_location ) ) );

		return $clean_location;
	}

	/** 
	 * Return a listing of all the locations associated with the locality.
	 *
	 * @author Nate Jacobs
	 * @date 3/13/15
	 * @since 1.3
	 */
	private function get_locations() {
		$places_table = isset($this->settings['tables']['places_table']) ? $this->settings['tables']['places_table'] : false;

		if (!$places_table) {
			return false;
		}
		
		if( $this->allow_numeric ) {
			$locations = $this->settings['db']->get_results(
				$this->settings['db']->prepare("SELECT distinct trim(substring_index(place,',',%d)) as locality, trim(place) as whole_place, count(place) as count FROM {$places_table} WHERE trim(substring_index(place,',',%d)) = '%s' GROUP BY locality ORDER by locality", $this->offset-1, $this->offset, $this->place)
			);
		} else {
			$locations = $this->settings['db']->get_results(
				$this->settings['db']->prepare("SELECT distinct trim(substring_index(place,',',%d)) as locality, trim(place) as whole_place, count(place) as count FROM {$places_table} WHERE trim(substring_index(place,',',%d)) = '%s' AND substring_index(place,' ',1) NOT REGEXP '[0-9]+' GROUP BY locality ORDER by locality", $this->offset-1, $this->offset, $this->place)
			);
		}
		
		$localities = [];
		$utilities = new FamilyRootsUtilities();
		
		foreach($locations as $location) {
			$location_count = substr_count($location->locality, ',');

			if( $location_count > 2 || ( $location->count == '1' && $location->locality === $location->whole_place ) ) {
				$place_object = new TNG_Place(0, $location->whole_place);
				$place_url = $place_object;
			} else {
				$place_object = null;
				$place_url = $location->locality;
			}
			
			$localities[] = (object) [
				'place_name' => $location->locality,
				'place_full_name' => $location->whole_place,
				'place_count' => $location->count,
				'place_object' => $place_object,
				'place_url' => $utilities->get_place_url($place_url)
			];
		}
		
		return $localities;
	}

	/**
	 * Retrieve the value of a property from the places table.
	 *
	 * @author  Nate Jacobs
	 * @date  3/13/15
	 * @since  1.3
	 *
	 * @param  string $key  The property to return.
	 */
	public function __get($key) {
		$value = false;
		if ( isset( $this->data->$key ) ) {
			$value = $this->data->$key;
		}

		return $value;
	}

	/**
	 * Determine whether the key is present in the places table.
	 *
	 * @author  Nate Jacobs
	 * @date  3/13/15
	 * @since  1.3
	 *
	 * @param  string $key  The property to check for.
	 */
	public function __isset($key) {
		$return = false;
		if (isset($this->data->$key) && !empty($this->data->$key)) {
			$return = true;
		}

		return $return;
	}

	/**
	 * Retrieve the value of a property from the places table.
	 *
	 * @author  Nate Jacobs
	 * @date  3/13/15
	 * @since  1.3
	 *
	 * @param  string $key  The property to return.
	 */
	public function get($key) {
		return $this->__get($key);
	}

	/**
	 * Determine whether a property is present from the places table.
	 *
	 * @author  Nate Jacobs
	 * @date  3/13/15
	 * @since  1.3
	 *
	 * @param  string $key  The property to check for.
	 */
	public function has_prop($key) {
		return $this->__isset($key);
	}

	/**
	 * Determine whether the locality exists in the database.
	 *
	 * @author  Nate Jacobs
	 * @date  3/13/15
	 * @since  1.3
	 */
	public function exists() {
		return !empty($this->data);
	}
}