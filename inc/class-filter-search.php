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
		
		add_action( 'wp_insert_post', [$this, 'remove_transients'], 10, 3);
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
			// get a copy to avoid accidentally changing the real one
			$query =& $wp_query;
			
			$total_found = $query->found_posts;
			$this->search_term = $query->get('s');
						
			// only run the people search if the filter is set to true
			if(apply_filters('family_roots_wp_search_add_people', true)) {
				$this->set_person_query($query);
				$people = $this->person_query();
			}
			
			// returning family search results is not working at this time
			// the total count numbers would be off if it was turned on
			// the search currently only searches on parent names
			if(apply_filters('family_roots_wp_search_add_family', false)) {
				//$this->set_family_query($query);
				//$family = $this->family_query();
			}
			
			// if there are people found as part of the search
			if($this->people_count > 0 ) {
				$transient_key = sanitize_key('tng_search_filter_'.$this->search_term);
				// create a transient to store the total number of posts returned
				$search_transient = get_transient($transient_key);
					
				// only store the total number of posts if on page one of the results				
				if($query->get('paged') == 0) {					
					// the transient is kept for two hour
					if(false === $search_transient) {
						set_transient($transient_key, $total_found.'_'.$this->people_count, HOUR_IN_SECONDS*2);
					}
				}
				
				// if the search transient is set, update the total found count to include the posts
				if($search_transient) {
					$search_totals = explode('_', $search_transient);
					$total_found = $search_totals[0];
				}
				
				// set the total found posts equal to the total number of TNG people and WordPress posts returned
				$wp_query->found_posts = $this->people_count+$total_found;
				// the max pages is the total posts and people divided by the set posts_per_page variable
				$max_pages = $wp_query->found_posts/$wp_query->get('posts_per_page');
				// the max number of pages is the max pages rounded up to the nearest whole number if it is a float otherwise the whole number
				$wp_query->max_num_pages = is_float($max_pages) ? ceil($max_pages) : $max_pages;
				
				// only run if there are not enough search results to fill the pagination limit
				if($wp_query->get('posts_per_page') > count($args)) {					
					// loop through the fake posts created from the people to add to the search results
					foreach($people as $person) {
						$args[] = $person;
					}
				} // end pagination check
			} // end people count check
		} // end main search check
		
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
		
		// get the total number of people found
		$this->people_count = $people->get_total();
		$results = $people->get_results();
		if(!empty($results)) {
			$author_id = get_users(['role' => 'administrator']);
			foreach($people->get_results() as $person) {
				$death = !$this->utility->is_living($person->get('living'), $person->get('birth_date')) ? '&ndash; Death Date: '.$this->utility->get_date_for_display($person->get('death_date')) : '';
				
				$post = new stdClass();
				$post->ID = -1;
                $post->post_author = apply_filters('family_roots_wp_search_author', $author_id[0]->ID);
                $post->post_date = $person->get('change_date');
                $post->post_date_gmt = $person->get('change_date');
                $post->post_content = 'Birth Date: '.$this->utility->get_date_for_display($person->get('birth_date')).' '.$death;
                $post->post_title = $person->get('first_name').' '.$person->get('last_name');
                $post->post_excerpt = 'Birth Date: '.$this->utility->get_date_for_display($person->get('birth_date')).' '.$death;;
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
		// set up default values
		$search_number = $query->get('posts_per_page')-$query->found_posts;
		$number = ($search_number < 0) ? 0 : $search_number;	
		$offset = $query->get('paged')*$query->get('posts_per_page');
		
		// not the first page of search results
		if($query->get('paged') != 0) {
			$transient_key = sanitize_key('tng_search_filter_'.$this->search_term);
			$search_transient = get_transient($transient_key);
			
			// only run if number of posts found is set
			if($search_transient) {
				$posts_per_page = $query->get('posts_per_page');
				
				// get the number of posts displayed on this page
				$posts_displayed = $query->found_posts-$posts_per_page;
				
				$search_totals = explode('_', $search_transient);
				
				// how many over the posts per page
				$posts_over_per_page = $search_totals[0]-$posts_per_page;
				// how many people have been displayed on the previous pages
				$total_people_displayed = $posts_per_page-$posts_over_per_page;
				// how many total search results have been shown
				$total_results_displayed = $total_people_displayed+$search_totals[0];
				// which page was the last combined posts and people results shown on
				$page_only_people_displayed = $total_results_displayed/$posts_per_page;
				// determine the multiple to use depending on the page number						
				$multiple = ($query->get('paged')-$page_only_people_displayed)-1;
				
				// if the number of posts found is greater than the posts per page setting
				if((int) $search_totals[0] > $posts_per_page) {
					// there are still posts being displayed
					if($posts_displayed > 0) {
						$number = $posts_per_page-$posts_displayed;
						$offset = 0;
					} else {
						// use the posts per page setting to determine how many to show on each page
						$number = $posts_per_page;
						// the offset is the number of people already displayed + (the number of posts per page * the page number of the last combined posts and people results page)
						$offset = $total_people_displayed+($posts_per_page*$multiple);
					}
				} else {						
					// use the posts per page setting to determine how many to show on each page
					$number = $posts_per_page;
					// the offset is the number of people already displayed + (the number of posts per page * the page number of the last combined posts and people results page)
					$offset = $total_people_displayed+($posts_per_page*$multiple);
				}
			}
		}
				
		$people = [
			'search' => $this->search_term,
			'number' => $number,
			'offset' => $offset,
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
	}
	
	/** 
	 *	Search for families matching the search term.
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/21/14
	 *	@since		1.0
	 */
	protected function family_query() {
		$family = new TNG_Family_Query($this->family_query_vars);
		$family_results = [];
		
		$this->family_count = $family->get_total();
		$results = $family->get_results();
		if(!empty($results)) {
			$author_id = get_users(['role' => 'administrator']);
			
			foreach($family->get_results() as $fam) {
				$post = new stdClass();
				$post->ID = -1;
                $post->post_author = apply_filters('family_roots_wp_search_author', $author_id[0]->ID);;
                $post->post_date = $fam->get('change_date');
                $post->post_date_gmt = $fam->get('change_date');
                $post->post_content = '';
                $post->post_title = 'Parents: '.$fam->get_father_name().' and '.$fam->get_mother_name();
                $post->post_excerpt = '';
                $post->post_status = 'publish';
                $post->comment_status = 'closed';
                $post->ping_status = 'closed';
                $post->post_password = '';
                $post->post_name = 'genealogy/family/'.$fam->ID;
                $post->to_ping = '';
                $post->pinged = '';
                $post->modified = $fam->get('change_date');
                $post->modified_gmt = $fam->get('change_date');
                $post->post_content_filtered = '';
                $post->post_parent = 0;
                $post->guid = $this->utility->get_family_url($fam);
                $post->menu_order = 0;
                $post->post_type = 'tng_family';
                $post->post_mime_type = '';
                $post->comment_count = 0;
                
                $family_results[] = $post;
			}
		}
		
		return $family_results;
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
		$search_number = $query->get('posts_per_page')-$query->found_posts;
		$number = ($search_number < 0) ? 0 : $search_number;

		$name = explode(' ', $this->search_term);
		$last_name = array_pop($name);
		$first_name = implode(' ', $name);
		
		$family = [
			'child_name' => ['first' => $first_name, 'last' => isset($last_name) ? $last_name : ''],
			'number' => $number,
			'offset' => $query->get('paged')*$query->get('posts_per_page'),
			'orderby' => 'familyID'
		];
		
		$this->family_query_vars = apply_filters('family_roots_pre_wp_search_family_query', $family);
	}
	
	/** 
	 *	Delete all the search transients when a new post is added.
	 *	This ensures the search transient stays up to date.
	 *
	 *	@author		Nate Jacobs
	 *	@date		10/12/14
	 *	@since		1.0
	 *
	 *	@param		int	$post_id		The post ID
	 *	@param		object	$post	The WP_Post object
	 *	@param		bool		$update	Whether this is an existing post being updated or not.
	 */
	public function remove_transients($post_id, $post, $update) {
		if(!$update && (isset($post->post_status) && $post->post_status != 'auto-draft')) {
			global $wpdb;
		    $sql = "SELECT `option_name` AS `name`, `option_value` AS `value`
		            FROM  $wpdb->options
		            WHERE `option_name` LIKE '_transient_tng_search_filter_%'
		            ORDER BY `option_name`";
		
		    $transients = $wpdb->get_results( $sql );
		    
		    if(!empty($transients)) {
				foreach($transients as $transient) {
			    		// get the name minus the _transient_
				    	$name = substr($transient->name, 11);
					delete_transient($name);
			    }
		    }
		}
	}
}

$family_roots_filter_search = new FamilyRootsFilterSearch();