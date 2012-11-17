<?php

//familyRootsTNGDatabase::init();

/** 
*	TNG Database Connection
*
*	Class used to handle all interactions with the TNG database
*
*	@author		Nate Jacobs
*	@date		11/3/12
*	@since		0.1
*/
class familyRootsTNGDatabase
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
	*	Get TNG DB Values
	*
	*	Retrieves the database values and users table name from config.php and customconfig.php
	*
	*	@author		Nate Jacobs
	*	@date		11/3/12
	*	@since		0.1
	*
	*	@param	null
	*
	*	@todo		Add error handling
	*/
	protected function get_tng_db_values()
	{
		// get array of config.php and customconfig.php
		$results = familyRootsUtilities::get_tng_config();
		
		// do you have stuff?
		if( !empty( $results ) )
		{
			$db_values = array();
			
			// loop through each line and find all the values that start with $database_ or $users_table
			foreach( $results as $line ) 
			{
				// is it the $database_ value?
				if( substr( trim( $line ), 0, 10 ) == '$database_' )
				{
					// split them on the =
					$key = substr( trim( strstr( $line, '=', TRUE ) ), 10 );
					$value = explode( '=', $line );
					// take the first half and make it the key and the second half the value
					$db_values[$key] = rtrim( str_replace('"', "", $value[1] ), ";" );
				}
				
				// is it the $users_table value?
				if( substr( trim( $line ), 0, 12 ) == '$users_table' )
				{
					// split them on the =
					$value = explode( '=', $line );		
					// take the first half and make it the key and the second half the value
					$users_table = rtrim( str_replace('"', "", $value[1] ), ";" );
				}
			}
						
			// update the values in the options table
			update_option( 'family-roots-tng-db', 
				array( 
					'host' 			=> trim( $db_values['host'] ), 
					'name' 			=> trim( $db_values['name'] ), 
					'username' 		=> trim( $db_values['username'] ), 
					'password' 		=> trim( trim( $db_values['password'], " '" ) ), 
					'users_table'	=> trim( $users_table )
				) 
			);
		}
	}
	
	/** 
	*	TNG Database Connection
	*
	*	Create the connection to the TNG database
	*
	*	@author		Nate Jacobs
	*	@date		11/4/12
	*	@since		0.1
	*
	*	@param	null	
	*/
	protected function connect()
	{
		// get the tng db values from the wp options table
		$settings = (array) get_option( 'family-roots-tng-db' );

		try
		{
			// create a new wpdb object
			$tng_db = new wpdb( $settings['username'], $settings['password'], $settings['name'], $settings['host'] );
			// now return it
			return $tng_db;
		}
		catch( Exception $e )
		{
			// any problems?
			echo $e->getMessage();
		}
	}
}