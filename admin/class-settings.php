<?php

/** 
 *	Family Roots Settings
 *
 *	Uses the settings API to display the settings page.
 *
 *	@author		Nate Jacobs
 *	@date		10/28/12
 *	@since		0.1
 */
class FamilyRootsSettings {

	/** 
 	 *	Initialize
 	 *
 	 *	Hook into WordPress and prepare all the methods as necessary.
 	 *
 	 *	@author		Nate Jacobs
 	 *	@date		10/28/12
 	 *	@since		0.1
 	 */
	public function __construct() {
		// add admin menu
		add_action('admin_menu', [$this, 'settings_menu']);
		// add settings, sections, fields
		add_action('admin_menu', [$this, 'settings_init']);
		
		$this->settings = get_option('family_roots_settings');
	}
	
	/** 
	*	TNG Settings Menu
	*
	*	Create the submenu link under the Settings menu
	*
	*	@author		Nate Jacobs
	*	@date		10/28/12
	*	@since		0.1
	*/
	public function settings_menu() {
		// add plugins options page to the WordPress Settings menu as a submenu link
		add_options_page(
			__('Family Roots - TNG Integration', 'family-roots'),
			'Family Roots',
			'manage_options',
			'family-roots-options',
			[$this, 'family_roots_options_page']
		);
	}

	/** 
	 *	Family Roots Options Page
	 *
	 *	Dispalys the Plugin Options page
	 *
	 *	@author		Nate Jacobs
	 *	@date		10/28/12
	 *	@since		0.1
	 */
	public function family_roots_options_page() {
		?>
		<div class="wrap">
			<h2><?php _e('Family Roots Options', 'family-roots'); ?></h2>
			<?php
				// is the tng_path setting present? Meaning, upon activation the plugin found the TNG path. If it isn't, display a warning
				if(empty($this->settings['tng_path'])) {
					?>
						<div class='error'><p> <?php _e('Your TNG file path could not be determined. Please enter it in the appropriate field below.', 'family-roots'); ?></p></div>
					<?php
				} 
			?>
			<form action="options.php" method="POST">
				<?php
					settings_fields('family-roots-options');
					do_settings_sections('family-roots-options');
					submit_button();
				?>
			</form>
		</div>
		<?php
	}
	
	/** 
	 *	Initialize Settings API
	 *
	 *	@author		Nate Jacobs
	 *	@date		10/28/12
	 *	@since		0.1
	 */
	public function settings_init() {
		// TNG settings section
		add_settings_section(
			'tng-settings',
			__('TNG Settings', 'family-roots'),
			[$this, 'tng_settings_callback'],
			'family-roots-options'
		);
		add_settings_field(
			'tng-path',
			__('Path to TNG Files', 'family-roots'),
			[$this, 'tng_path_callback'],
			'family-roots-options',
			'tng-settings'
		);
		
		// TNG database section
		add_settings_section(
			'tng-database',
			__('TNG Database Values', 'family-roots'),
			[$this, 'tng_database_section_callback'],
			'family-roots-options'
		);
		add_settings_field(
			'tng-database-host',
			__('Host', 'family-roots'),
			[$this, 'tng_database_host_callback'],
			'family-roots-options',
			'tng-database'
		);
		add_settings_field(
			'tng-database-name',
			__('Database Name', 'family-roots'),
			[$this, 'tng_database_name_callback'],
			'family-roots-options',
			'tng-database'
		);
		add_settings_field(
			'tng-database-username',
			__('Username', 'family-roots'),
			[$this, 'tng_database_user_name_callback'],
			'family-roots-options',
			'tng-database'
		);
		add_settings_field(
			'tng-database-password',
			__('Password', 'family-roots'),
			[$this, 'tng_database_password_callback'],
			'family-roots-options',
			'tng-database'
		);
		
		// TNG table names section
		add_settings_section(
			'tng-table-names',
			__('TNG Database Table Names', 'family-roots'),
			[$this, 'tng_database_name_section_callback'],
			'family-roots-options'
		);
		add_settings_field(
			'tng-database-table-names',
			__('Table Names', 'family-roots'),
			[$this, 'tng_database_table_names_callback'],
			'family-roots-options',
			'tng-table-names'
		);
		
		// misc TNG config values
		add_settings_section(
			'tng-misc-values',
			__('TNG Config Values', 'family-roots'),
			[$this, 'tng_database_config_section_callback'],
			'family-roots-options'
		);
		add_settings_field(
			'tng-misc-config-values',
			__('Misc Config Values', 'family-roots'),
			[$this, 'tng_database_misc_config_callback'],
			'family-roots-options',
			'tng-misc-values'
		);
		
		// register tng and wp settings sections
		register_setting( 
			'family-roots-options', 
			'family_roots_settings', 
			[$this, 'family_roots_validate']
		);
	}
	
	/** 
	 *	TNG Settings Callback and Display TNG settings.
	 *
	 *	@author		Nate Jacobs
	 *	@date		10/28/12
	 *	@since		0.1
	 */
	public function tng_settings_callback() {
		_e('These settings apply to your TNG installation', 'family-roots');

	}
	
	/** 
	 *	Output the database values section description.
	 *
	 *	@author		Nate Jacobs
	 *	@date		10/26/14
	 *	@since		1.0
	 */
	public function tng_database_section_callback() {
		_e('The database values for TNG. If these are empty, ensure the path is correct above and press the "Save Changes" button below to automatically get the values.', 'family-roots');
	}
	
	/** 
	 *	Output the database table section description.
	 *
	 *	@author		Nate Jacobs
	 *	@date		11/1/14
	 *	@since		1.0
	 */
	public function tng_database_name_section_callback() {
		_e('The database table names for TNG. If these are empty, ensure the path is correct above and press the "Save Changes" button below to automatically get the values.', 'family-roots');
	}
	
	/** 
	 *	Output the misc config section description.
	 *
	 *	@author		Nate Jacobs
	 *	@date		11/7/14
	 *	@since		1.0
	 */
	public function tng_database_config_section_callback() {
		_e('If these are empty, ensure the path is correct above and press the "Save Changes" button below to automatically get the values.', 'family_roots');
	}
	
	/** 
	 *	Display the TNG File Path Callback.
	 *
	 *	@author		Nate Jacobs
	 *	@date		10/28/12
	 *	@since		0.1
	 */
	public function tng_path_callback() {
		$tng_path = isset($this->settings['tng_path']) ? esc_attr($this->settings['tng_path']) : '';
		
		echo "<input class='widefat' type='text' name='family_roots_settings[tng_path]' value='$tng_path' />";
	}
	
	/** 
	 *	Output the host value.
	 *
	 *	@author		Nate Jacobs
	 *	@date		10/26/14
	 *	@since		1.0
	 */
	public function tng_database_host_callback() {
		$host = isset($this->settings['host']) ? esc_attr($this->settings['host']) : '';
		
		?>
		<input class="widefat" type="text" name="family_roots_settings[host]" value="<?php echo $host; ?>">
		<?php
	}
	
	/** 
	 *	Output the database name value.
	 *
	 *	@author		Nate Jacobs
	 *	@date		10/26/14
	 *	@since		1.0
	 */
	public function tng_database_name_callback() {
		$name = isset($this->settings['name']) ? esc_attr($this->settings['name']) : '';
		
		?>
		<input class="widefat" type="text" name="family_roots_settings[name]" value="<?php echo $name; ?>">
		<?php
	}
	
	/** 
	 *	Output the username value.
	 *
	 *	@author		Nate Jacobs
	 *	@date		10/26/14
	 *	@since		1.0
	 */
	public function tng_database_user_name_callback() {
		$username = isset($this->settings['username']) ? esc_attr($this->settings['username']) : '';
		
		?>
		<input class="widefat" type="text" name="family_roots_settings[username]" value="<?php echo $username; ?>">
		<?php
	}
	
	/** 
	 *	Output the password value.
	 *
	 *	@author		Nate Jacobs
	 *	@date		10/26/14
	 *	@since		1.0
	 */
	public function tng_database_password_callback() {
		$password = isset($this->settings['password']) ? esc_attr($this->settings['password']) : '';
		
		?>
		<input class="widefat" type="text" name="family_roots_settings[password]" value="<?php echo $password; ?>">
		<?php
	}
	
	/** 
	 *	Output the table name values.
	 *
	 *	@author		Nate Jacobs
	 *	@date		11/1/14
	 *	@since		1.0
	 */
	public function tng_database_table_names_callback() {
		?>
		<label><?php _e('People Table', 'family_roots'); ?></label>
		<input class="widefat" type="text" name="family_roots_settings[people_table]" value="<?php echo isset($this->settings['people_table']) ? esc_attr($this->settings['people_table']) : ''; ?>">
		<br><br>
		<label><?php _e('Family Table', 'family_roots'); ?></label>
		<input class="widefat" type="text" name="family_roots_settings[family_table]" value="<?php echo isset($this->settings['family_table']) ? esc_attr($this->settings['family_table']) : ''; ?>">
		<br><br>
		<label><?php _e('Children Table', 'family_roots'); ?></label>
		<input class="widefat" type="text" name="family_roots_settings[children_table]" value="<?php echo isset($this->settings['children_table']) ? esc_attr($this->settings['children_table']) : '';?>">
		<br><br>
		<label><?php _e('Places Table', 'family_roots'); ?></label>
		<input class="widefat" type="text" name="family_roots_settings[places_table]" value="<?php echo isset($this->settings['places_table']) ? esc_attr($this->settings['places_table']) : ''; ?>">
		<br><br>
		<label><?php _e('Sources Table', 'family_roots'); ?></label>
		<input class="widefat" type="text" name="family_roots_settings[sources_table]" value="<?php echo isset($this->settings['sources_table']) ? esc_attr($this->settings['sources_table']) : ''; ?>">
		<br><br>
		<label><?php _e('Events Table', 'family_roots'); ?></label>
		<input class="widefat" type="text" name="family_roots_settings[events_table]" value="<?php echo isset($this->settings['events_table']) ? esc_attr($this->settings['events_table']) : ''; ?>">
		<br><br>
		<label><?php _e('Event Types Table', 'family_roots'); ?></label>
		<input class="widefat" type="text" name="family_roots_settings[eventtypes_table]" value="<?php echo isset($this->settings['eventtypes_table']) ? esc_attr($this->settings['eventtypes_table']) : ''; ?>">
		<br><br>
		<label><?php _e('Tree Table', 'family_roots'); ?></label>
		<input class="widefat" type="text" name="family_roots_settings[trees_table]" value="<?php echo isset($this->settings['trees_table']) ? esc_attr($this->settings['trees_table']) : ''; ?>">
		<br><br>
		<label><?php _e('Default Tree', 'family_roots'); ?></label>
		<input class="widefat" type="text" name="family_roots_settings[default_tree]" value="<?php echo isset($this->settings['default_tree']) ? esc_attr($this->settings['default_tree']) : ''; ?>">
		<br><br>
		<label><?php _e('Note Links Table', 'family_roots'); ?></label>
		<input class="widefat" type="text" name="family_roots_settings[notelinks_table]" value="<?php echo isset($this->settings['notelinks_table']) ? esc_attr($this->settings['notelinks_table']) : ''; ?>">
		<br><br>
		<label><?php _e('XNote Table', 'family_roots'); ?></label>
		<input class="widefat" type="text" name="family_roots_settings[xnotes_table]" value="<?php echo isset($this->settings['xnotes_table']) ? esc_attr($this->settings['xnotes_table']) : ''; ?>">
		<br><br>
		<label><?php _e('Users Table', 'family_roots'); ?></label>
		<input class="widefat" type="text" name="family_roots_settings[users_table]" value="<?php echo isset($this->settings['users_table']) ? esc_attr($this->settings['users_table']) : ''; ?>">
		<br><br>
		<label><?php _e('Media Table', 'family_roots'); ?></label>
		<input class="widefat" type="text" name="family_roots_settings[media_table]" value="<?php echo isset($this->settings['media_table']) ? esc_attr($this->settings['media_table']) : ''; ?>">
		<br><br>
		<label><?php _e('Media Links Table', 'family_roots'); ?></label>
		<input class="widefat" type="text" name="family_roots_settings[media_links_table]" value="<?php echo isset($this->settings['media_links_table']) ? esc_attr($this->settings['media_links_table']) : ''; ?>">
		<?php
	}
	
	/** 
	 *	Render the input fields for the misc config values from TNG.
	 *
	 *	@author		Nate Jacobs
	 *	@date		11/7/14
	 *	@since		1.0
	 */
	public function tng_database_misc_config_callback() {
		?>
		<label><?php _e('TNG URL', 'family_roots'); ?></label>
		<input class="widefat" type="text" name="family_roots_settings[tng_domain]" value="<?php echo isset($this->settings['tng_domain']) ? esc_attr($this->settings['tng_domain']) : ''; ?>">
		<br><br>
		<label><?php _e('Photo Directory', 'family_roots'); ?></label>
		<input class="widefat" type="text" name="family_roots_settings[tng_domain]" value="<?php echo isset($this->settings['photo_dir']) ? esc_attr($this->settings['photo_dir']) : ''; ?>">
		<?php
	}
	
	/** 
	 *	Family Roots Validation called when the settings page is saved.
	 *
	 *	@author		Nate Jacobs
	 *	@date		11/1/12
	 *	@since		0.1
	 *
	 *	@param	array	$input
	 */
	public function family_roots_validate($input) {
		$utilities = new FamilyRootsUtilities();
		$output = $utilities->get_tng_db_values();
		
		foreach($input as $key => $text) {
			if(isset($text) && !empty($text)) {
				$output[$key] = sanitize_text_field($text);
			}
		}
		
		return $output;
	}
}

$family_roots_settings = new FamilyRootsSettings();