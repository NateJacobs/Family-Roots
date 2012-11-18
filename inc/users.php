<?php

//familyRootsUserManagement::init();

/** 
*	Family Roots User Management
*
*	
*
*	@author		Nate Jacobs
*	@date		11/17/12
*	@since		0.1
*/
class familyRootsUserManagement extends familyRootsTNGDatabase
{
	/** 
	*	Initialize
	*
	*	Hook into WordPress and prepare all the methods as necessary.
	*
	*	@author		Nate Jacobs
	*	@date		11/17/12
	*	@since		0.1
	*
	*	@param		
	*/
	public static function init()
	{
		//add_action( 'delete_user', array( __CLASS__, 'delete_user' ) );
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
	*	Update TNG User
	*
	*	Update an existing user in the TNG database
	*
	*	@author		Nate Jacobs
	*	@date		11/17/12
	*	@since		0.1
	*
	*	@param	null	
	*/
	public function update_user()
	{
		
	}
	
	/** 
	*	Check TNG User
	*
	*	Does the specified user exist in the TNG database?
	*
	*	@author		Nate Jacobs
	*	@date		11/17/12
	*	@since		0.1
	*
	*	@param	int	$user_id	
	*/
	public function check_user( $user_id )
	{
		// get the family-roots-tng-db settings into an array
		$settings = (array) get_option( 'family-roots-tng-db' );
		
		// get tng user id from usermeta table
		$tng_user_id = get_user_meta( $user_id, 'tng_user_id' );

		// is there a tng user id for this WordPress user?
		if( empty( $tng_user_id ) )
		{
			// if not, return false 
			return FALSE;
		}
		else
		{	
			// if so, connect to TNG database and select userID of WordPress user specified
			$users_table = $settings['users_table'];
			$tng_user_id = $tng_user_id[0];

			$tng_db = parent::connect();
			$tng_user = $tng_db->get_row( "SELECT userID FROM $users_table WHERE userID = '$tng_user_id'" );

			// is there a TNG user with that userID?
			if( $tng_user == NULL )
			{
				// if not, delete the usermeta attached to the specificed user as the user does not exist anymore
				delete_user_meta( $user_ID, 'tng_user_id' );
			}
			else
			{
				// if so, return the userID to do other things				
				return $tng_user->userID;
			}	
		}
	}
}