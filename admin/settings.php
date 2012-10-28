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
			
			<div class="icon32" id="icon-family-roots"><img src='<?php echo FAMROOTS_URI . "family-roots-icon" ?>'><br /></div>
			<h2>Family Roots Options</h2>
			<?php settings_errors(); ?>
			<!-- check and see if an active tab is set, if not, set the general tab to be active -->
			<?php $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general'; ?>
			<h2 class="nav-tab-wrapper">  
            	<a href="?page=family-roots-options&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>">General Options</a>  
            	<a href="?page=family-roots-options&tab=users" class="nav-tab <?php echo $active_tab == 'users' ? 'nav-tab-active' : ''; ?>">Users & Logins</a>  
            </h2> 
			<form action="options.php" method="POST">
				<?php
					if( $active_tab == 'general' )
					{
						settings_fields( 'family-roots-general' );
						do_settings_sections( 'family-roots-options' );
						submit_button();
					}
					else
					{
						
					};
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
		register_setting( 'family-roots-general', 'basic-settings' );
		add_settings_section( 'tng-settings', 'TNG Settings', array( __CLASS__, 'tng_settings_callback' ), 'family-roots-options' );
		add_settings_section( 'wp-settings', 'WordPress Settings', array( __CLASS__, 'wp_settings_callback' ), 'family-roots-options' );
		add_settings_field( 'tng-path', 'Path to TNG Files', array( __CLASS__, 'tng_path_callback' ), 'family-roots-options', 'tng-settings' );
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
		echo 'These settings apply to your TNG installation';
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
		echo 'These settings apply to your WordPress installation';
	}
	
	/** 
	*	TNG File Path Callback
	*
	*	Display the TNG File Path field
	*
	*	@author		Nate Jacobs
	*	@date		10/28/12
	*	@since		0.1
	*
	*	@param		null
	*/
	public function tng_path_callback()
	{
		$setting = esc_attr( get_option( 'basic-settings' ) );
		echo "<input class='widefat' type='text' name='basic-settings' value='$setting' />";
	}
}