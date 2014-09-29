<?php

/** 
 *	Create all the family queries to the TNG database.
 *
 *	@author		Nate Jacobs
 *	@date		8/10/14
 *	@since		1.0
 */
class TNG_Family_Query {
	
	public $query_vars;
	
	public $query_fields;
	
	public $query_where;
	
	public $query_orderby;
	
	public $query_limit;
	
	private $settings;
	
	private $total_family = 0;
	
	private $results;
	
	/** 
	 *	Connect to the TNG database when the class is instantiated.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/10/14
	 *	@since		1.0
	 */
	public function __construct($query = null) {
		global $tng_db;
		if(!is_null($query)) {
			$this->settings = ['tables' => get_option('family-roots-settings'), 'db' => $tng_db];
			$this->prepare_query($query);
			$this->query();
		}
	}
	
	/** 
	 *	Prepare the query.
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/10/14
	 *	@since		1.0
	 *
	 *	@param		array	$query	{
	 *		Optional. Array of Query parameters
	 *		
	 *		@type array		$include	The specific family ID to include from the results.
	 *		@type array		$exclude	The specific fmaily ID to exclude from the results.
	 *		@type string		$search		The search term.
	 *		@type array		$search_columns	The columns to search with the search terms.
	 *							[ID, marriage_date, divorce_date, marriage_place, divorce_place, husband, wife]
	 *		@type string		$orderby	The column to use to sort the results: ID, husband, wife, marriage_place.
	 *		@type string		$order		How to order the results: ASC, DESC.
	 *		@type int		$offset		The number of familes to offset before retrieval.
	 *		@type int		$number		The total number of familes to return.
	 *		@type bool		$count_total	Whether or not the total number of families found should be returned.
	 *		@type array		$child_in	An array of children IDs.
	 *		@type array		$parent_in	An array of parent IDs.
	 *		@type array		$husband_in	An array of husband IDs.
	 *		@type array		$wife_in	An array of wife IDs.
	 *		@type array		$child_name	An array of children names: [[first, last], [first, last]].
	 *		@type array		$parent_name	An array of parent names: [[first, last], [first, last]].
	 *		@type array		$husband_name	An array of husband names: [[first, last], [first, last]].
	 *		@type array		$wife_name	An array of wife names: [[first, last], [first, last]].
	 *	}
	 */
	public function prepare_query($query) {
		if(empty($this->query_vars) || ! empty($query)) {
			$this->query_limit = null;
			$this->query_vars = wp_parse_args( $query, [
				'include' => [],
				'exclude' => [],
				'search' => '',
				'search_columns' => [],
				'orderby' => 'familyID',
				'order' => 'ASC',
				'offset' => '',
				'number' => '',
				'child_in' => [],
				'parent_in' => [],
				'husband_in' => [],
				'wife_in' => [],
				'child_name' => [],
				'parent_name' => [],
				'husband_name' => [],
				'wife_name' => [],
				'fields' => 'all',
				'count_total' => true
			]);
			
			do_action( 'family_roots_pre_get_family', $this );
			
			$qv =& $this->query_vars;
			
			if(is_array($qv['fields'])) {
				$qv['fields'] = array_unique($qv['fields']);
	
				$this->query_fields = [];
				foreach($qv['fields'] as $field) {
					switch($field) {
						case 'marriage_place': $field = 'marrplace'; break;
						case 'divorce_place': $field = 'divplace'; break;
						case 'ID': $field = 'familyID'; break;
						case 'husband': $field = 'husband'; break;
						case 'wife': $field = 'wife'; break;
						case 'marriage_date': $field = 'marrdatetr'; break;
						case 'divorce_date': $field = 'divorcedatetr'; break;
						default: $field = 'familyID'; break;
					};
					
					$this->query_fields[] = $field;
				}
				$this->query_fields = implode(',', $this->query_fields);
			} elseif('all' == $qv['fields']) {
				$this->query_fields = "*";
			} else {
				$this->query_fields = "familyID";
			}
			
			if(isset($qv['count_total']) && $qv['count_total']) {
				$this->query_fields = 'SQL_CALC_FOUND_ROWS '.$this->query_fields;
			}
			
			$family_table = isset($this->settings['tables']['family_table']) ? $this->settings['tables']['family_table'] : false;
			
			$this->query_from = "FROM $family_table";
			$this->query_where = "WHERE 1=1";
			
			if(isset($qv['orderby'])) {
				$allowed_orderby = [
					'ID',
					'husband',
					'wife',
					'marriage_place'
				];
				
				if(in_array($qv['orderby'], $allowed_orderby)) {
					switch($qv['orderby']) {
						case 'ID': $orderby = 'familyID'; break;
						case 'husband': $orderby = 'husband'; break;
						case 'wife': $orderby = 'wife'; break;
						case 'marriage_place': $orderby = 'marrplace'; break;
						default: $orderby = 'familyID'; break;
					}
				} else {
					$orderby = 'familyID';
				}
			}
			
			if(empty($orderby)) {
				$orderby = 'familyID';
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
						'ID',
						'marriage_date',
						'divorce_date',
						'marriage_place',
						'divorce_place',
						'husband',
						'wife'
					];
					
					$search_columns = array_intersect($qv['search_columns'], $columns);
				}
				
				if(!$search_columns) {
					$search_columns = ['husband', 'wife'];
				}
	
				$this->query_where .= $this->get_search_sql($search, $search_columns, $wild);
			}
			
			// child_in
			if(!empty($qv['child_in'])) {
				foreach($qv['child_in'] as $child) {
					$children_ids = $this->child_in($child);
					
					if($children_ids) {
						$qv['include'][] = $children_ids;
					}
				}
			}
			
			// child_name
			if(!empty($qv['child_name'])) {
				$family_ids = $this->child_name($qv['child_name']);
				
				if($family_ids) {
					foreach($family_ids as $family) {
						$qv['include'][] = $family->familyID;
					}
				}
			}
			
			// parent_name
			if(!empty($qv['parent_name'])) {
				$family_ids = $this->parent_name($qv['parent_name']);
				
				if($family_ids) {
					foreach($family_ids as $family) {
						$qv['parent_in'][] = $family->personID;
					}
				}
			}
			
			// husband_name
			if(!empty($qv['husband_name'])) {
				$family_ids = $this->parent_name($qv['husband_name']);
				
				if($family_ids) {
					foreach($family_ids as $family) {
						$qv['husband_in'][] = $family->personID;
					}
				}
			}
			
			// wife_name
			if(!empty($qv['wife_name'])) {
				$family_ids = $this->parent_name($qv['wife_name']);
				
				if($family_ids) {
					foreach($family_ids as $family) {
						$qv['wife_in'][] = $family->personID;
					}
				}
			}
			
			if(!empty($qv['include'])) {
				array_walk($qv['include'], function(&$item) { 
					if('F' === substr(strtoupper($item), 0, 1)) {
						$item = $item;
					} else {
						$item = 'F'.$item;
					}
				});
				
				$ids = implode("', '", $qv['include']);
				$this->query_where .= " AND familyID IN ('" .$ids. "')";
			} elseif(!empty($qv['exclude'])) {
				array_walk($qv['exclude'], function(&$item) { 
					if('F' === substr(strtoupper($item), 0, 1)) {
						$item = $item;
					} else {
						$item = 'F'.$item;
					}
				});
				
				$ids = implode("', '", $qv['exclude']);
				$this->query_where .= " AND familyID NOT IN ('" .$ids. "')";
			}
			
			if(!empty($qv['husband_in'])){
				array_walk($qv['husband_in'], function(&$item) { 
					if('I' === substr(strtoupper($item), 0, 1)) {
						$item = $item;
					} else {
						$item = 'I'.$item;
					}
				});
				
				$ids = implode("', '", $qv['husband_in']);
				$this->query_where .= " AND husband IN ('" .$ids. "')";
			}
			
			if(!empty($qv['wife_in'])){
				array_walk($qv['wife_in'], function(&$item) { 
					if('I' === substr(strtoupper($item), 0, 1)) {
						$item = $item;
					} else {
						$item = 'I'.$item;
					}
				});
				
				$ids = implode("', '", $qv['wife_in']);
				$this->query_where .= " AND wife IN ('" .$ids. "')";
			}
			
			if(!empty($qv['parent_in'])){
				array_walk($qv['parent_in'], function(&$item) { 
					if('I' === substr(strtoupper($item), 0, 1)) {
						$item = $item;
					} else {
						$item = 'I'.$item;
					}
				});
				
				$ids = implode("', '", $qv['parent_in']);
				$this->query_where .= " AND wife IN ('" .$ids. "')";
				$this->query_where .= " OR husband IN ('" .$ids. "')";
			}
		};
	}
	
	/** 
	 *	Run the actual query.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/28/14
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
			foreach($this->results as $key => $family) {
				$this->results[$key] = new TNG_Family($family->familyID);
			}
		}
	}
	
	/** 
	 *	Build the search sql statement
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/28/14
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
			'marriage_date',
			'divorce_date'
		];
		
		foreach($cols as $col) {
			if('ID' == $col || in_array($col, $date_fields)) {
				switch($col) {
					case 'marriage_date': $searches[] = $this->settings['db']->prepare("marrdatetr = %s", $string); break;
					case 'divorce_date': $searches[] = $this->settings['db']->prepare("divdatetr = %s", $string); break;
					default:
						if('I' === substr(strtoupper($string), 0, 1)) {
							$string = $string;
						} else {
							$string = 'I'.$string;
						}
						$searches[] = $this->settings['db']->prepare("familyID = %s", $string); 
						break;
				}
			} else {
				switch($col) {
					case 'divorce_place': $searches[] = $this->settings['db']->prepare( "divplace LIKE %s", $like ); break;
					case 'marriage_place': $searches[] = $this->settings['db']->prepare( "marrplace LIKE %s", $like ); break;
					case 'husband':
						if('I' === substr(strtoupper($string), 0, 1)) {
							$string = $string;
						} else {
							$string = 'I'.$string;
						}
						$searches[] = $this->settings['db']->prepare( "husband = %s", $string );
						break;
					case 'wife':
						if('I' === substr(strtoupper($string), 0, 1)) {
							$string = $string;
						} else {
							$string = 'I'.$string;
						}
						$searches[] = $this->settings['db']->prepare( "wife = %s", $string );
						break;
					default:
						if('I' === substr(strtoupper($string), 0, 1)) {
							$string = $string;
						} else {
							$string = 'I'.$string;
						}
						$searches[] = $this->settings['db']->prepare("familyID = %s", $string); 
						break;
				}
			}
		}
		
		return ' AND (' . implode(' OR ', $searches) . ')';
	}
	
	/** 
	 *	Get the family ID of the child by the child ID.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/28/14
	 *	@since		1.0
	 *
	 *	@param		int|string	$id	The ID of the child.
	 */
	private function child_in($id) {
		$children_table = isset($this->settings['tables']['children_table']) ? $this->settings['tables']['children_table'] : false;
		
		if('I' === substr(strtoupper($id), 0, 1)) {
			$id = $id;
		} else {
			$id = 'I'.$id;
		}
		
		$family_id = $this->settings['db']->get_results($this->settings['db']->prepare("SELECT familyID FROM {$children_table} WHERE personID = %s", $id));
		
		return $family_id[0]->familyID;
	}
	
	/** 
	 *	Get the family ID of the person by the person name.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/28/14
	 *	@since		1.0
	 *
	 *	@param		array	$name	The first name and last name of the person to look for.
	 */
	private function child_name($name) {
		$person_table = isset($this->settings['tables']['people_table']) ? $this->settings['tables']['people_table'] : false;
		
		$first = $this->get_name_sql($name['first']);
		$last = $this->get_name_sql($name['last']);
		
		$family_ids = $this->settings['db']->get_results($this->settings['db']->prepare("SELECT famc AS familyID FROM {$person_table} WHERE firstname = %s AND lastname = %s", $first, $last));
		
		return $family_ids;
	}
	
	/** 
	 *	Get the family ID of the person by the person name.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/28/14
	 *	@since		1.0
	 *
	 *	@param		array	$name	The first name and last name of the person to look for.
	 */
	private function parent_name($name) {
		$person_table = isset($this->settings['tables']['people_table']) ? $this->settings['tables']['people_table'] : false;
		
		$first = $this->get_name_sql($name['first']);
		$last = $this->get_name_sql($name['last']);
		
		$person_ids = $this->settings['db']->get_results($this->settings['db']->prepare("SELECT personID FROM {$person_table} WHERE firstname LIKE %s AND lastname LIKE %s", $first, $last));
		
		return $person_ids;
	}
	
	/** 
	 *	Allow wildcard search to be used for name.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/28/14
	 *	@since		1.0
	 *
	 *	@param		string	$name	The name to use to create the LIKE statement.
	 */
	private function get_name_sql($name) {
		$leading_wild = (ltrim($name, '*') != $name);
		$trailing_wild = (rtrim($name, '*') != $name);
		
		if($leading_wild && $trailing_wild) {
			$wild = 'both';
		} elseif($leading_wild) {
			$wild = 'leading';
		} elseif($trailing_wild) {
			$wild = 'trailing';
		} else {
			$wild = false;
		}
			
		if($wild) {
			$name = trim($name, '*');
		}
		
		$leading_wild = ('leading' == $wild || 'both' == $wild) ? '%' : '';
		$trailing_wild = ('trailing' == $wild || 'both' == $wild) ? '%' : '';
		return $leading_wild.$this->settings['db']->esc_like($name).$trailing_wild;
	}
	
	/** 
	 *	Return all the families found.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/28/14
	 *	@since		1.0
	 */
	public function get_results() {
		return $this->results;
	}
	
	/** 
	 *	Return the total number of families found.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/28/14
	 *	@since		1.0
	 */
	public function get_total() {
		return (int) $this->total_family;
	}
}
