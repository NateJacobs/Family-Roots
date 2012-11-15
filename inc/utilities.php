<?php

//familyRootsUtilities::init();

/** 
*	Family Roots Utilities
*
*	Utilities class dedicated to the little helper functions needed by the plugin
*
*	@author		Nate Jacobs
*	@date		11/3/12
*	@since		0.1
*/
class familyRootsUtilities
{
	/** 
 	*	Initialize
 	*
 	*	Hook into WordPress and prepare all the methods as necessary.
 	*
 	*	@author		Nate Jacobs
 	*	@date		11/3/12
 	*	@since		0.1
 	*
 	*	@param		null
 	*/
	public static function init()
	{
		
	}

	/** 
	*	Get TNG Config Values
	*
	*	Gets config.php and customconfig.php into an array and passes it on
	*
	*	@author		Nate Jacobs
	*	@date		11/3/12
	*	@since		0.1
	*
	*	@param	null
	*/
	public function get_tng_config()
	{
		$settings = (array) get_option( 'family-roots-settings' );
		
		if( !empty( $settings['tng_path'] ) )
		{
			$trn_path = $settings['tng_path'];
			$config = file_get_contents( $trn_path.'config.php' );
			$config .= file_get_contents( $trn_path.'customconfig.php' );
			return explode( "\n", $config );
		}
		else
		{
			return FALSE;
		}
		
	}
}