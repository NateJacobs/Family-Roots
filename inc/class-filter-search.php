<?php

/** 
 *	Filter the default search results from WordPress to include TNG people.
 *
 *	@author		Nate Jacobs
 *	@date		9/21/14
 *	@since		1.0
 */
class FamilyRootsFilterSearch {
	
	public $query_vars;
	
	/** 
	 *	Hook into WordPress and prepare all the methods as necessary. 
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/21/14
	 *	@since		1.0	
	 */
	public function __construct() {
		add_filter('the_posts', [$this, 'filter_search']);
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
				
				$this->set_person_query($wp_query);
				
				$people = new TNG_Person_Query($this->query_vars);
				
				$utilities = new FamilyRootsUtilities();
				$people_results = $people->get_results();
				
				if(!empty($people_results)) {
					foreach($people_results as $person) {
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
		                $post->guid = $utilities->get_person_url($person);
		                $post->menu_order = 0;
		                $post->post_type = 'tng_person';
		                $post->post_mime_type = '';
		                $post->comment_count = 0;
						
						$args[] = $post;
					}
					
					$wp_query->found_posts = $people->get_total()+$wp_query->found_posts;
					$max_pages = $wp_query->found_posts/$wp_query->get('posts_per_page');
					$wp_query->max_num_pages = is_float($max_pages) ? floor($max_pages) : $max_pages;
				}
			}
		}
		
		return $args;
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
	protected function set_person_query($wp_query) {
		$search_number = $wp_query->get('posts_per_page')-$wp_query->found_posts;
		$number = ($search_number < 0) ? 0 : $search_number;
		
		$people = [
			'search' => $wp_query->get('s'),
			'number' => $number,
			'offset' => $wp_query->get('paged')*$wp_query->get('posts_per_page'),
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
		
		$this->query_vars = apply_filters('family_roots_pre_wp_search_person_query', $people);
		
		return $this->query_vars;
	}
}

$family_roots_filter_search = new FamilyRootsFilterSearch();