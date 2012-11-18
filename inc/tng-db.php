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
	
	/** 
	*	Create TNG User
	*
	*	Create a user in the TNG database
	*
	*	@author		Nate Jacobs
	*	@date		11/17/12
	*	@since		0.1
	*
	*	@param	null	
	*/
	public function create_user()
	{
		
	}
	
	/** 
	*	Delete TNG User
	*
	*	Delete an existing user in the TNG database
	*
	*	@author		Nate Jacobs
	*	@date		11/17/12
	*	@since		0.1
	*
	*	@param	null	
	*/
	public function delete_user()
	{
		
	}
	
	/** 
	*	Check TNG User
	*
	*	Does the specified user exist in the TNG database yet?
	*
	*	@author		Nate Jacobs
	*	@date		11/17/12
	*	@since		0.1
	*
	*	@param	null	
	*/
	public function check_user()
	{
		
	}
}