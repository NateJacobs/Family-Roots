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
			'Family Roots - TNG Integration',
			'Family Roots',
			'manage_options',
			'family-roots-options',
			[ $this, 'family_roots_options_page' ]
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
			<h2><?php _e('Family Roots Options', 'family-roots-integration'); ?></h2>
			<?php
				$settings = get_option('family-roots-settings');

				// is the tng_path setting present? Meaning, upon activation the plugin found the TNG path. If it isn't, display a warning
				if(empty($settings['tng_path'])) {
					?>
						<div class='error'><p> <?php _e('Your TNG file path could not be determined. Please enter it in the appropriate field below.', 'family-roots-integration'); ?></p></div>
					<?php
				} 
			?>
			<?php $query = new TNG_Family_Query(); ?>
			<?php echo '<pre>';
			var_dump( $query->get_family_by_id('f1225') );
			echo '</pre>'; ?>
			<form action="options.php" method="POST">
				<?php
					settings_fields( 'family-roots-options' );
					do_settings_sections( 'family-roots-options' );
					
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
			'TNG Settings', 
			[ $this, 'tng_settings_callback' ], 
			'family-roots-options' 
		);
		add_settings_field( 
			'tng-path', 
			'Path to TNG Files', 
			[ $this, 'tng_path_callback' ], 
			'family-roots-options', 
			'tng-settings' 
		);
		
		// register tng and wp settings sections
		register_setting( 
			'family-roots-options', 
			'family-roots-settings', 
			[ $this, 'family_roots_validate' ]
		);
		
		// users section
		add_settings_section( 
			'user-settings', 
			'User Settings', 
			[ $this, 'user_settings_callback' ], 
			'family-roots-users' 
		);
		add_settings_field( 
			'sync-user-management', 
			'Combine TNG and WordPress user management?', 
			[ $this, 'user_sync_callback' ], 
			'family-roots-users', 
			'user-settings'
		);
		
		// register users and logins section
		register_setting( 
			'family-roots-users', 
			'family-roots-users-settings'
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
		_e( 'These settings apply to your TNG installation', 'family-roots-integration' );

	}
		
	/** 
	 *	User Settings and display user settings.
	 *
	 *	@author		Nate Jacobs
	 *	@date		11/3/12
	 *	@since		0.1
	 */
	public function user_settings_callback() {
		_e( 'WordPress Users and TNG Users Integration', 'family-roots-integration' );
	}
	
	/** 
	 *	Advanced Settings.
	 *
	 *	@author		Nate Jacobs
	 *	@date		11/3/12
	 *	@since		0.1
	 *
	 *	@todo		add method to update TNG DB values
	 */
	public function advanced_settings_callback() {
		_e( 'Advanced', 'family-roots-integration' );
	}
	
	/** 
	 *	Display the TNG File Path Callback.
	 *
	 *	@author		Nate Jacobs
	 *	@date		10/28/12
	 *	@since		0.1
	 */
	public function tng_path_callback() {
		$settings = get_option('family-roots-settings');
		$tng_path = isset($settings['tng_path']) ? esc_attr($settings['tng_path']) : '';
		
		echo "<input class='widefat' type='text' name='family-roots-settings[tng_path]' value='$tng_path' />";
	}
	
	/** 
	 *	Combine User Management
	 *
	 *	@author		Nate Jacobs
	 *	@date		11/17/12
	 *	@since		0.1
	 */
	public function user_sync_callback() {
		$settings = get_option('family-roots-users-settings');
		$checked = isset($settings['sync_users']) ? $checked = ' checked="checked" ' : '';
		
		echo "<input ".$checked." type='checkbox' name='family-roots-users-settings[sync_users]' />";
		echo "<span class='description'> If this option is checked, all of the user management shall be done from WordPress</span>";
		
	//if($options['chkbox1']) { $checked = ' checked="checked" '; }
	//echo "<input ".$checked." id='plugin_chk1' name='plugin_options[chkbox1]' type='checkbox' />";
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
		//$output = get_option('family-roots-settings');
		
		$utilities = new FamilyRootsUtilities();
		$output = $utilities->get_tng_db_values();
		
		$output['tng_path'] = $input['tng_path'];
		
		return $output;
	}
}

$family_roots_settings = new FamilyRootsSettings();