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
				$settings = get_option('family-roots-settings');

				// is the tng_path setting present? Meaning, upon activation the plugin found the TNG path. If it isn't, display a warning
				if(empty($settings['tng_path'])) {
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
		
		// register tng and wp settings sections
		register_setting( 
			'family-roots-options', 
			'family-roots-settings', 
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
		
		$output['tng_path'] = $input['tng_path'];
		
		return $output;
	}
}

$family_roots_settings = new FamilyRootsSettings();