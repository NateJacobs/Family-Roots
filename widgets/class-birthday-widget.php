<?php

add_action('widgets_init', function(){
     register_widget('FamilyRootsBirthdayWidget');
});

class FamilyRootsBirthdayWidget extends WP_Widget {
	
	/** 
	 *	Hook into WordPress and prepare all the methods as necessary.
	 *
	 *	@author		Nate Jacobs
	 *	@date		10/25/14
	 *	@since		1.0
	 */
	public function __construct() {
		parent::__construct(
			'foo_widget',
			__('Family Roots Birthdays', 'family_roots'),
			['description' => __('A list of birthdays from TNG for the current day.', 'family_roots')]
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
		echo $args['before_widget'];
		if (!empty( $instance['title'])) {
			echo $args['before_title'].apply_filters('widget_title', $instance['title']).$args['after_title'];
		}
		echo __( 'Hello, World!', 'family_roots' );
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
			$title = __('Birthdays', 'family_roots');
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'family_roots'); ?></label> 
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