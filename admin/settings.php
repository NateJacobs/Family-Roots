<?php

familyRootsSettings::init();

/** 
*	Family Roots Settings
*
*	Uses the settings API to display the settings page.

*
*	@author		Nate Jacobs
*	@date		10/28/12
*	@since		0.1
*/
class familyRootsSettings
{
	/** 
 	*	Initialize
 	*
 	*	Hook into WordPress and prepare all the methods as necessary.
 	*
 	*	@author		Nate Jacobs
 	*	@date		10/28/12
 	*	@since		0.1
 	*
 	*	@param		null
 	*/
	public static function init()
	{
		// add admin menu
		add_action( 'admin_menu', array( __CLASS__, 'settings_menu' ) );
		// add settings, sections, fields
		add_action( 'admin_menu', array( __CLASS__, 'settings_init' ) );
	}
	
	/** 
	*	TNG Settings Menu
	*
	*	Create the submenu link under the Settings menu
	*
	*	@author		Nate Jacobs
	*	@date		10/28/12
	*	@since		0.1
	*
	*	@param		null
	*/
	public function settings_menu()
	{
		add_options_page( 'Family Roots - TNG Integration', 'Family Roots', 'manage_options', 'family-roots-options', array( __CLASS__, 'family_roots_options_page' ) );
	}

	/** 
	*	Family Roots Options Page
	*
	*	Dispalys the Plugin Options page
	*
	*	@author		Nate Jacobs
	*	@date		10/28/12
	*	@since		0.1
	*
	*	@param		null
	*/
	public function family_roots_options_page()
	{
		?>

		<div class="wrap">
			<div class="icon32" id="icon-family-roots"><img src='<?php echo FAMROOTS_URI . "family-roots-icon.png" ?>'><br /></div>
			<h2><?php _e( 'Family Roots Options', 'family-roots-integration' ); ?></h2>
			<?php
				$settings = (array) get_option( 'family-roots-settings' );
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
            </h2>
			<form action="options.php" method="POST">
				<?php
					settings_fields( 'family-roots-integration' );
					if( $active_tab == 'general' )
					{
						do_settings_sections( 'family-roots-options' );
					}
					else
					{
						
					};
					submit_button();
				?>
			</form>
		</div>
		
		<?php
	}
	
	/** 
	*	Initialize Settings API
	*
	*	Register with the Settings API
	*
	*	@author		Nate Jacobs
	*	@date		10/28/12
	*	@since		0.1
	*
	*	@param		null
	*/
	public function settings_init()
	{
		register_setting( 'family-roots-integration', 'family-roots-settings' );
		add_settings_section( 'tng-settings', 'TNG Settings', array( __CLASS__, 'tng_settings_callback' ), 'family-roots-options' );
		add_settings_section( 'wp-settings', 'WordPress Settings', array( __CLASS__, 'wp_settings_callback' ), 'family-roots-options' );
		add_settings_field( 'tng-path', 'Path to TNG Files', array( __CLASS__, 'tng_path_callback' ), 'family-roots-options', 'tng-settings' );
		add_settings_field( 'tng-admin-url', 'URL to TNG Admin', array( __CLASS__, 'tng_admin_url_callback' ), 'family-roots-options', 'tng-settings' );
	}
	
	/** 
	*	TNG Settings Callback
	*
	*	Display TNG settings
	*
	*	@author		Nate Jacobs
	*	@date		10/28/12
	*	@since		0.1
	*
	*	@param		null
	*/
	public function tng_settings_callback()
	{
		_e( 'These settings apply to your TNG installation', 'family-roots-integration' );
	}
	
	/** 
	*	WP Settings Callback
	*
	*	Display WordPress settings
	*
	*	@author		Nate Jacobs
	*	@date		10/28/12
	*	@since		0.1
	*
	*	@param		null
	*/
	public function wp_settings_callback()
	{
		_e( 'These settings apply to your WordPress installation', 'family-roots-integration' );
	}
	
	/** 
	*	TNG File Path Callback
	*
	*	Display the TNG File path field
	*
	*	@author		Nate Jacobs
	*	@date		10/28/12
	*	@since		0.1
	*
	*	@param		null
	*/
	public function tng_path_callback()
	{
		$settings = (array) get_option( 'family-roots-settings' );
		$trn_path = isset( $settings['tng_path'] ) ? esc_attr( $settings['tng_path'] ) : '';
		
		echo "<input class='widefat' type='text' name='family-roots-settings[tng_path]' value='$trn_path' />";
	}
	
	/** 
	*	TNG Admin URL Path Callback
	*
	*	Display the TNG Admin URL path field
	*
	*	@author		Nate Jacobs
	*	@date		10/31/12
	*	@since		0.1
	*
	*	@param		null
	*/
	public function tng_admin_url_callback()
	{
		$settings = (array) get_option( 'family-roots-settings' );
		$trn_path = isset( $settings['tng_admin_url'] ) ? esc_attr( $settings['tng_admin_url'] ) : '';
		
		echo "<input class='widefat' type='text' name='family-roots-settings[tng_admin_url]' value='$trn_path' />";
	}	
}