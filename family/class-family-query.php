<?php

/** 
 *	Create all the family queries to the TNG database.
 *
 *	@author		Nate Jacobs
 *	@date		8/10/14
 *	@since		1.0
 */
class TNG_Family_Query extends FamilyRootsTNGDatabase {
	
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
		if(!is_null($query)) {
			$this->settings = ['tables' => get_option('family-roots-settings'), 'db' => parent::connect()];
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
	 *	@param		array	$query	The requested variables.
	 */
	public function prepare_query() {
		
	}
	
	/** 
	 *	
	 *
	 *	@author		Nate Jacobs
	 *	@date		8/10/14
	 *	@since		1.0
	 *
	 *	@param		
	 */
	public function query() {
		
	}
}