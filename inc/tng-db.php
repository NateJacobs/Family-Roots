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
		$results = familyRootsUtilities::get_tng_config();
		
		if( !empty( $results ) )
		{
			$db_values = array();
			
			foreach( $results as $line ) 
			{
				if( substr( trim( $line ), 0, 10 ) == '$database_' )
				{
					$key = substr( trim( strstr( $line, '=', TRUE ) ), 10 );
					$value = explode( '=', $line );
					$db_values[$key] = $value[1];
				}
			}
			
			foreach( $results as $line )
			{
				if( substr( trim( $line ), 0, 12 ) == '$users_table' )
				{
					$value = explode( '=', $line );
					$users_table = $value[1];
				}
			}
			
			update_option( 'family-roots-tng-db', 
				array( 
					'host' 			=> $db_values['host'], 
					'name' 			=> $db_values['name'], 
					'username' 		=> $db_values['username'], 
					'password' 		=> $db_values['password'], 
					'users_table'	=> $users_table 
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
		$settings = (array) get_option( 'family-roots-tng-db' );
		
		try
		{
			$tng_db = new wpdb( $settings['username'], $settings['password'], $settings['name'], $settings['host'] );
			if( $tng_db )
			{
				return $tng_db;
			}
		}
		catch( Exception $e )
		{
			throw new Exception( 'Didn\'t Work' );
			echo $e->getMessage();
		}
	}
}