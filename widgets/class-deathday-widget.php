<?php

add_action('widgets_init', function(){
     register_widget('FamilyRootsDeathsWidget');
});

class FamilyRootsDeathsWidget extends WP_Widget {
	
	/** 
	 *	Hook into WordPress and prepare all the methods as necessary.
	 *
	 *	@author		Nate Jacobs
	 *	@date		10/25/14
	 *	@since		1.0
	 */
	public function __construct() {
		parent::__construct(
			'family_roots_deathday_widget',
			__('Family Roots Deaths', 'family-roots'),
			['description' => __('A list of deaths from TNG for the current day.', 'family-roots')]
		);
	}
	
	/** 
	 *	Outputs the content of the widget.
	 *
	 *	@author		Nate Jacobs
	 *	@date		10/25/14
	 *	@since		1.0
	 *
	 *	@param		array $args     Widget arguments.
	 * 	@param 		array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		date_default_timezone_set(get_option('timezone_string'));
		$query_args = [
			'date_columns' => [
				'death_date'
			],
			'date_search' => [
				[
					'month' => date('m'),
					'day' => date('d')
				]
			]
		];
		
		$birthdays = new TNG_Person_Query($query_args);
		
		echo $args['before_widget'];
		
		if (!empty( $instance['title'])) {
			echo $args['before_title'].apply_filters('widget_title', $instance['title']).$args['after_title'];
		}
		
		if($birthdays->get_total() > 0) {
			$utilities = new FamilyRootsUtilities();
			
			$ul_class = apply_filters('family_roots_widget_ul_class', 'list-group');
			$li_class = apply_filters('family_roots_widget_li_class', 'list-group-item');
			
			echo '<ul class="'.$ul_class.'">';
			
			foreach($birthdays->get_results() as $person) {
				echo '<li class="'.$li_class.'"><a href="'.$utilities->get_person_url($person).'">'.$person->get('first_name').' '.$person->get('last_name').'</a></li>';
			}
			
			echo '</ul>';
		} else {
			echo apply_filters('family_roots_widget_no_results_before', '<div class="panel-body">');
			_e('There are no recorded deaths today', 'family-roots');
			echo apply_filters('family_roots_widget_no_results_after', '</div>');
		}
		
		echo $args['after_widget'];
	}
	
	/** 
	 *	Outputs the options form on admin.
	 *
	 *	@author		Nate Jacobs
	 *	@date		10/25/14
	 *	@since		1.0
	 *
	 *	@param		array $instance The widget options
	 */
	public function form($instance) {
		if(isset($instance['title'])) {
			$title = $instance['title'];
		} else {
			$title = __('Deaths', 'family-roots');
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'family-roots'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
		</p>
		<?php
	}
	
	/** 
	 *	Processing widget options on save.
	 *
	 *	@author		Nate Jacobs
	 *	@date		10/25/14
	 *	@since		1.0
	 *
	 *	@param		array $new_instance Values just sent to be saved.
	 * 	@param 		array $old_instance Previously saved values from database.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = [];
		$instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';

		return $instance;
	}
}