<?php

/** 
 *	
 *
 *	@author		Nate Jacobs
 *	@date		9/21/14
 *	@since		1.0
 */
class FamilyRootsFilterSearch {
	/** 
	 *	 
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/21/14
	 *	@since		1.0	
	 */
	public function __construct() {
		add_filter('the_posts', [$this, 'filter_search']);
	}

	/** 
	 *	
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/21/14
	 *	@since		1.0
	 */
	public function filter_search($args) {
		global $wp_query;
			
		if($wp_query->is_search && $wp_query->is_main_query()) {
			$people = new TNG_Person_Query(
				[
					'search' => $wp_query->get('s'),
					'number' => $wp_query->get('posts_per_page'),
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
				]
			);
			
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
				$wp_query->max_num_pages = ceil($wp_query->found_posts/$wp_query->get('posts_per_page'));
			}
		}
		
		return $args;
	}

	/** 
	 *	
	 *
	 *	@author		Nate Jacobs
	 *	@date		9/21/14
	 *	@since		1.0
	 *
	 *	@param		
	 */
	protected function set_query() {
		
	}
}

$family_roots_filter_search = new FamilyRootsFilterSearch();