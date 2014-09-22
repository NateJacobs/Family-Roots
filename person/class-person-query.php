<?php

/** 
 *	TNG Person Query Class.
 *
 *	@author		Nate Jacobs
 *	@date		8/10/14
 *	@since		1.0
 */
class TNG_Person_Query {
	
	public $query_vars;
	
	public $query_fields;
	
	public $query_where;
	
	public $query_orderby;
	
	public $query_limit;
	
	private $total_people = 0;
	
	private $results;
	
	private $settings;
	
	/** 
	 *	Start up the person query class.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/10/14
	 *	@since		1.0
	 *
	 *	@param		array	$query	The requested variables
	 */
	public function __construct($query = null) {
		global $tng_db;
		if(!empty($query)) {
			$this->settings = ['tables' => get_option('family-roots-settings'), 'db' => $tng_db];
			$this->prepare_query($query);
			$this->query();
		}
	}
	
	/** 
	 *	Prepare the query.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/31/14
	 *	@since		1.0
	 *
	 *	@param		array	$query	The requested variables.
	 */
	public function prepare_query($query) {	
		if(empty($this->query_vars) || ! empty($query)) {
			$this->query_limit = null;
			$this->query_vars = wp_parse_args( $query, [
				'include' => [],
				'exclude' => [],
				'search' => '',
				'search_columns' => [],
				'orderby' => 'last_name',
				'order' => 'ASC',
				'offset' => '',
				'number' => '',
				'fields' => 'all',
				'count_total' => true
			]);
			
			do_action( 'pre_get_tng_people', $this );
			
			$qv =& $this->query_vars;
			
			if(is_array($qv['fields'])) {
				$qv['fields'] = array_unique($qv['fields']);
	
				$this->query_fields = [];
				foreach($qv['fields'] as $field) {
					switch($field) {
						case 'first_name': $field = 'firstname'; break;
						case 'birth_place': $field = 'birthplace'; break;
						case 'birth_date': $field = 'birthdatetr'; break;
						case 'death_place': $field = 'deathplace'; break;
						case 'death_date': $field = 'deathdatetr'; break;
						case 'burial_place': $field = 'burialplace'; break;
						case 'burial_date': $field = 'burialdate'; break;
						default: $field = 'personID'; break;
					};
					
					$this->query_fields[] = $field;
				}
				$this->query_fields = implode(',', $this->query_fields);
			} elseif('all' == $qv['fields']) {
				$this->query_fields = "*";
			} else {
				$this->query_fields = "personID";
			}
			
			if(isset($qv['count_total']) && $qv['count_total']) {
				$this->query_fields = 'SQL_CALC_FOUND_ROWS '.$this->query_fields;
			}
			
			$person_table = isset($this->settings['tables']['people_table']) ? $this->settings['tables']['people_table'] : false;
			
			$this->query_from = "FROM $person_table";
			$this->query_where = "WHERE 1=1";
			
			if(isset($qv['orderby'])) {
				$allowed_orderby = [
					'first_name',
					'last_name',
					'birth_place',
					'birth_date',
					'death_place',
					'death_date',
					'burial_place',
					'burial_date'
				];
				
				if(in_array($qv['orderby'], $allowed_orderby)) {
					switch($qv['orderby']) {
						case 'first_name': $orderby = 'firstname'; break;
						case 'last_name': $orderby = 'lastname'; break;
						case 'birth_place': $orderby = 'birthplace'; break;
						case 'birth_date': $orderby = 'birthdatetr'; break;
						case 'death_place': $orderby = 'deathplace'; break;
						case 'death_date': $orderby = 'deathdatetr'; break;
						case 'burial_place': $orderby = 'burialplace'; break;
						case 'burial_date': $orderby = 'burialdate'; break;
						default: $orderby = 'personID'; break;
					}
				} else {
					$orderby = 'personID';
				}
			}
			
			if(empty($orderby)) {
				$orderby = 'personID';
			}
			
			$qv['order'] = isset( $qv['order'] ) ? strtoupper( $qv['order'] ) : '';
			if ( 'ASC' == $qv['order'] ) {
				$order = 'ASC';
			} else {
				$order = 'DESC';
			}
			
			$this->query_orderby = "ORDER BY $orderby $order";
			
			if(isset($qv['number']) && $qv['number']) {
				if($qv['offset']) {
					$this->query_limit = $this->settings['db']->prepare("LIMIT %d, %d", $qv['offset'], $qv['number']);
				}
				else {
					$this->query_limit = $this->settings['db']->prepare("LIMIT %d", $qv['number']);
				}
			}
			
			$search = '';
			if(isset($qv['search'])) {
				$search = trim($qv['search']);
			}
			
			if($search) {
				$leading_wild = (ltrim($search, '*') != $search);
				$trailing_wild = (rtrim($search, '*') != $search);
				
				if($leading_wild && $trailing_wild ) {
					$wild = 'both';
				} elseif($leading_wild) {
					$wild = 'leading';
				} elseif($trailing_wild) {
					$wild = 'trailing';
				} else {
					$wild = false;
				}
					
				if($wild) {
					$search = trim($search, '*');
				}
				
				$search_columns = [];
				if($qv['search_columns']) {
					$columns = [
						'first_name',
						'last_name',
						'birth_place',
						'birth_date',
						'death_place',
						'death_date',
						'burial_place',
						'burial_date'
					];
					
					$search_columns = array_intersect($qv['search_columns'], $columns);
				}
				
				if(!$search_columns) {
					$search_columns = ['lastname', 'firstname'];
				}
	
				$this->query_where .= $this->get_search_sql($search, $search_columns, $wild);
			}
			
			if(!empty($qv['include'])) {
				array_walk($qv['include'], function(&$item) { 
					if('I' === substr(strtoupper($item), 0, 1)) {
						$item = $item;
					} else {
						$item = 'I'.$item;
					}
				});
				
				$ids = implode(',', wp_parse_id_list($qv['include']));
				$this->query_where .= " AND personID IN ($ids)";
			} elseif(!empty($qv['exclude'])) {
				array_walk($qv['exclude'], function(&$item) { 
					if('I' === substr(strtoupper($item), 0, 1)) {
						$item = $item;
					} else {
						$item = 'I'.$item;
					}
				});
				
				$ids = implode(',', wp_parse_id_list($qv['exclude']));
				$this->query_where .= " AND personID NOT IN ($ids)";
			}
		}
	}
	
	/** 
	 *	Run the query.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/5/14
	 *	@since		1.0
	 */
	public function query() {
		$qv =& $this->query_vars;

		$query = "SELECT $this->query_fields $this->query_from $this->query_where $this->query_orderby $this->query_limit";
		
		if(is_array($qv['fields']) || 'all' == $qv['fields']) {
			$this->results = $this->settings['db']->get_results($query);
		} else {
			$this->results = $this->settings['db']->get_col($query);
		}
		
		if(isset($qv['count_total']) && $qv['count_total']) {
			$this->total_people = $this->settings['db']->get_var("SELECT FOUND_ROWS()");
		}
		
		if(!$this->results) {
			return;
		}
		
		if('all' == $qv['fields']) {
			foreach($this->results as $key => $user) {
				$this->results[$key] = new TNG_Person($user->personID);
			}
		}
	}
	
	/** 
	 *	Build the search sql statement
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/5/14
	 *	@since		1.0
	 *
	 *	@param		string	$string	search string.
	 *	@param		array	$cols	The search columns.
	 *	@param		bool		$wild	If there are any wild cards used in the search.
	 */
	protected function get_search_sql( $string, $cols, $wild = false ) {
		$searches = [];
		$leading_wild = ('leading' == $wild || 'both' == $wild) ? '%' : '';
		$trailing_wild = ('trailing' == $wild || 'both' == $wild) ? '%' : '';
		$like = $leading_wild.$this->settings['db']->esc_like($string).$trailing_wild;

		$date_fields = [
			'birth_date',
			'death_date',
			'burial_date'
		];
		
		foreach($cols as $col) {
			if('ID' == $col || in_array($col, $date_fields)) {
				switch($col) {
					case 'birth_date': $searches[] = $this->settings['db']->prepare("birthdatetr = %s", $string); break;
					case 'death_date': $searches[] = $this->settings['db']->prepare("deathdatetr = %s", $string); break;
					case 'burial_date': $searches[] = $this->settings['db']->prepare("burialdate = %s", $string); break;
					default:
						if('I' === substr(strtoupper($string), 0, 1)) {
							$string = $string;
						} else {
							$string = 'I'.$string;
						}
						$searches[] = $this->settings['db']->prepare("personID = %s", $string); 
						break;
				}
			} else {
				$tng_field_name = str_replace('_', '', $col);
				$searches[] = $this->settings['db']->prepare( "$tng_field_name LIKE %s", $like );
			}
		}
		
		return ' AND (' . implode(' OR ', $searches) . ')';
	}
	
	/** 
	 *	Return all the people found.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/5/14
	 *	@since		1.0
	 */
	public function get_results() {
		return $this->results;
	}
	
	/** 
	 *	Return the total number of people found.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/10/14
	 *	@since		1.0
	 */
	public function get_total() {
		return (int) $this->total_people;
	}
}