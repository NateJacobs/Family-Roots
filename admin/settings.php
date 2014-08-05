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
			<h2><?php _e( 'Family Roots Options', 'family-roots-integration' ); ?></h2>
			<?php
				$settings = get_option( 'family-roots-settings' );

				// is the tng_path setting present? Meaning, upon activation the plugin found the TNG path. If it isn't, display a warning
				if ( empty( $settings['tng_path'] ) ) 
				{
					?>
						<div class='error'><p> <?php _e( 'Your TNG file path could not be determined. Please enter it in the appropriate field below.', 'family-roots-integration' ); ?></p></div>
					<?php
				} 
			?>
			<?php settings_errors(); ?>
			<!-- check and see if an active tab is set, if not, set the general tab to be active -->
			<?php $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general'; ?>
			<h2 class="nav-tab-wrapper">  
            	<a href="?page=family-roots-options&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>"><?php _e( 'General Options', 'family-roots-integration' ); ?></a>  
            	<a href="?page=family-roots-options&tab=users" class="nav-tab <?php echo $active_tab == 'users' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Users & Logins', 'family-roots-integration' ); ?></a>  
            	<a href="?page=family-roots-options&tab=advanced" class="nav-tab <?php echo $active_tab == 'advanced' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Advanced', 'family-roots-integration' ); ?></a>  
            </h2>
			<form action="options.php" method="POST">
				<?php
					// based upon the active tab set which tab contents to display					
					if( $active_tab == 'general' ) {
						settings_fields( 'family-roots-options' );
						do_settings_sections( 'family-roots-options' );
					} elseif( $active_tab == 'users' ) {
						settings_fields( 'family-roots-users' );
						do_settings_sections( 'family-roots-users' );
					} else {
						settings_fields( 'family-roots-advanced' );
						do_settings_sections( 'family-roots-advanced' );
					}
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
		add_settings_field( 
			'tng-admin-url', 
			'URL to TNG Admin', 
			[ $this, 'tng_admin_url_callback' ], 
			'family-roots-options', 
			'tng-settings' 
		);
		
		// WP settings section
		add_settings_section( 
			'wp-settings', 
			'WordPress Settings', 
			[ $this, 'wp_settings_callback' ], 
			'family-roots-options' 
		);
		add_settings_field( 
			'tng-wp-page', 
			'Show TNG content on', 
			[ $this, 'tng_wp_page_callback' ], 
			'family-roots-options', 
			'wp-settings'
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
		
		// advanced settings section
		add_settings_section( 
			'advanced-settings', 
			'Advanced Settings', 
			[ $this, 'advanced_settings_callback' ],
			'family-roots-advanced' 
		);
		
		// register advanced settings section
		register_setting( 
			'family-roots-advanced', 
			'family-roots-advanced-settings'
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
	 *	WP Settings Callback and display WordPress settings.
	 *
	 *	@author		Nate Jacobs
	 *	@date		10/28/12
	 *	@since		0.1
	 */
	public function wp_settings_callback() {
		_e( 'These settings apply to your WordPress installation', 'family-roots-integration' );
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
	 *	Display the TNG Admin URL Path Callback
	 *
	 *	@author		Nate Jacobs
	 *	@date		10/31/12
	 *	@since		0.1
	 */
	public function tng_admin_url_callback() {
		$settings = get_option('family-roots-settings');
		$admin_url = isset($settings['tng_admin_url']) ? esc_attr($settings['tng_admin_url']) : '';
		
		echo "<input class='widefat' type='text' name='family-roots-settings[tng_admin_url]' value='$admin_url' />";
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
	 *	Create the placeholder page for TNG content
	 *
	 *	@author		Nate Jacobs
	 *	@date		11/1/12
	 *	@since		0.1
	 */
	public function tng_wp_page_callback() {
		$settings = get_option('family-roots-settings');
		$tng_wp_page_id = isset($settings['tng_wp_page_id']) ? esc_attr($settings['tng_wp_page_id']) : '';
		
		$page_url = get_permalink($tng_wp_page_id);
		?>
			<p>
				<strong>
					<?php _e('Select an existing page or create a new one', 'family-roots-integration'); ?>
				</strong>
			</p>
		<?php
			// present a list of pages on the WordPress site
			$page_dropdown_args = [
				'show_option_none' => __('Select a page', 'family-roots-integration'),
				'selected' => $tng_wp_page_id, 
				'name' => 'family-roots-settings[tng_wp_page_id]' 
			];
			wp_dropdown_pages($page_dropdown_args);
			echo "<br><br><input type='text' name='family-roots-settings[tng_new_page]' />";
		?>
			<span class='description'><?php _e('Once you save, this will create a new page in WordPress with this name.', 'family-roots-integration'); ?></span>
			<br><br><p><?php _e('In TNG go to Setup -> General Settings -> Site Design and Definition and set the Genealogy URL to: ', 'family-roots-integration'); echo '<strong>'.$page_url.'</strong>';  ?></p>
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
		$output = get_option('family-roots-settings');
		
		// build the new page array
		$page_array = [
			'post_title' => $input['tng_new_page'],
			'post_type' => 'page',
			'post_author' => get_current_user_id(),
			'post_status' => 'publish'
		];
		
		$output['tng_path'] = $input['tng_path'];
		$output['tng_admin_url'] = $input['tng_admin_url'];
		
		// A page was selected from the dropdown list of pages
		if(!empty($input['tng_wp_page_id'])) {
			$output['tng_wp_page_id'] = $input['tng_wp_page_id'];
		}
		
		// A new page is to be created based upon the users input
		if(!empty($input['tng_new_page'])) {
			$page_id = wp_insert_post($page_array, TRUE);
			$output['tng_wp_page_id'] = $page_id;	
		}
		
		return $output;
	}
}

$family_roots_settings = new FamilyRootsSettings();