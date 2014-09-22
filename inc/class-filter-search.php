<?php

/** 
 *	Filter the default search results from WordPress to include TNG people.
 *
 *	@author		Nate Jacobs
 *	@date		9/21/14
 *	@since		1.0
 */
class FamilyRootsFilterSearch {
	
	private $person_query_vars;
	private $utility;
	private $people_count = 0;
	private $family_count = 0;
	private $search_term;
	
	/** 
	 *	Hook into WordPress and prepare all the methods as necessary. 
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/21/14
	 *	@since		1.0	
	 */
	public function __construct() {
		$this->utility = new FamilyRootsUtilities();
		
		add_filter('the_posts', [$this, 'filter_search'], 999);
	}

	/** 
	 *	Return the additional results from the TNG database when a search is completed.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/21/14
	 *	@since		1.0
	 */
	public function filter_search($args) {
		global $wp_query;
		
		// only run if the search main query
		if($wp_query->is_search && $wp_query->is_main_query()) {
			// only run if there are not enough search results to fill the pagination limit
			if($wp_query->get('posts_per_page') > count($args)) {
				
				$query =& $wp_query;
				
				$total_found = $query->found_posts;
				$this->search_term = $query->get('s');
				
				// include people in search results
				if(apply_filters('family_roots_wp_search_add_people', true)) {
					$this->set_person_query($query);
					$people = $this->person_query();
					foreach($people as $person) {
						$args[] = $person;
					}
					
					$total_found = $this->people_count+$total_found;
				}
				
				if(apply_filters('family_roots_wp_search_add_family', false)) {
					$this->set_family_query($query);
					$family = $this->family_query();
					foreach($family as $fam) {
						$args[] = $fam;
					}
					
					$total_found = $this->family_count+$total_found;
				}
				
				$wp_query->found_posts = $total_found;
				$max_pages = $wp_query->found_posts/$wp_query->get('posts_per_page');
				$wp_query->max_num_pages = is_float($max_pages) ? floor($max_pages) : $max_pages;
			}
		}
		
		return $args;
	}

	/** 
	 *	Search for people matching the search term.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/21/14
	 *	@since		1.0
	 */
	public function person_query() {		
		$people = new TNG_Person_Query($this->person_query_vars);
		$people_results = [];
		if(!empty($people->get_results())) {
			$this->people_count = $people->get_total();
			foreach($people->get_results() as $person) {
				$post = new stdClass();
				$post->ID = -1;
                $post->post_author = 1;
                $post->post_date = $person->get('change_date');
                $post->post_date_gmt = $person->get('change_date');
                $post->post_content = '';
                $post->post_title = $person->get('first_name').' '.$person->get('last_name');
                $post->post_excerpt = '';
                $post->post_status = 'publish';
                $post->comment_status = 'closed';
                $post->ping_status = 'closed';
                $post->post_password = '';
                $post->post_name = 'genealogy/person/'.$person->ID;
                $post->to_ping = '';
                $post->pinged = '';
                $post->modified = $person->get('change_date');
                $post->modified_gmt = $person->get('change_date');
                $post->post_content_filtered = '';
                $post->post_parent = 0;
                $post->guid = $this->utility->get_person_url($person);
                $post->menu_order = 0;
                $post->post_type = 'tng_person';
                $post->post_mime_type = '';
                $post->comment_count = 0;
                
                $people_results[] = $post;
			}
		}
		
		return $people_results;
	}
	
	/** 
	 *	Create the person query arguments.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/21/14
	 *	@since		1.0
	 *
	 *	@param		object	$wp_query	The WP_Query object.
	 */
	protected function set_person_query($query) {
		$search_number = $query->get('posts_per_page')-$query->found_posts;
		$number = ($search_number < 0) ? 0 : $search_number;
		
		$people = [
			'search' => $this->search_term,
			'number' => $number,
			'offset' => $query->get('paged')*$query->get('posts_per_page'),
			'orderby' => 'first_name',
			'search_columns' => [
				'first_name',
				'last_name',
				'birth_place',
				'birth_date',
				'death_place',
				'death_date',
				'burial_place',
				'burial_date'
			]
		];
		
		$this->person_query_vars = apply_filters('family_roots_pre_wp_search_person_query', $people);
		
		return $this->person_query_vars;
	}
	
	/** 
	 *	Search for families matching the search term.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/21/14
	 *	@since		1.0
	 */
	protected function family_query() {
		
	}
	
	/** 
	 *	Create the family query arguments.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/21/14
	 *	@since		1.0
	 *
	 *	@param		object	$wp_query	The WP_Query object.
	 */
	protected function set_family_query($query) {
		
	}
}

$family_roots_filter_search = new FamilyRootsFilterSearch();