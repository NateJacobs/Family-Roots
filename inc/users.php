<?php

/** 
 *	Family Roots User Management
 *
 *	@author		Nate Jacobs
 *	@date		11/17/12
 *	@since		0.1
 */
class FamilyRootsUserManagement extends familyRootsTNGDatabase {
	/** 
	 *	Hook into WordPress and prepare all the methods as necessary.
	 *
	 *	@author		Nate Jacobs
	 *	@date		11/17/12
	 *	@since		0.1
	 */
	public function __construct() {
		// get users settings
		$settings = get_option('family-roots-users-settings');
		$sync_users = isset($settings['sync_users']) ? $settings['sync_users'] : '';
		
		// if the combined user management checkbox is checked, create and delete users from the TNG db
		if($sync_users == 'on')
		{
			add_action('delete_user', [ $this, 'delete_user' ]);
			add_action('user_register', [ $this, 'create_user' ]);
		}
	}
	
	/** 
	 *	Create a user in the TNG database
	 *
	 *	@author		Nate Jacobs
	 *	@date		11/17/12
	 *	@since		0.1
	 *
	 *	@param	int	$user_id		The newly created user's id	
	 */
	public function create_user($user_id) {
		// take the user id and create the user
	}
		
	/** 
	 *	Update an existing user in the TNG database
	 *
	 *	@author		Nate Jacobs
	 *	@date		11/17/12
	 *	@since		0.1
	 */
	public function update_user() {
		
	}
	
	/** 
	 *	Delete an existing user in the TNG database
	 *
	 *	@author		Nate Jacobs
	 *	@date		11/17/12
	 *	@since		0.1
	 *
	 *	@param	int	$user_id	
	 */
	public function delete_user($user_id) {
		$is_user = $this->check_user($user_id);
		$error = '';
		
		if($is_user) {
			// get the family-roots-tng-db settings into an array
			$settings = get_option('family-roots-tng-db');
			
			// connect to TNG database and delete user specified
			$users_table = $settings['users_table'];
			$tngdb = parent::connect();
			
			$tng_user = $tngdb->query( 
				$tngdb->prepare("DELETE FROM $users_table WHERE userID = %d", $is_user)
			);
			
			if($tng_user != 1) {
				$error = new WP_Error('tng_delete_user', __("The user was not deleted", 'family-roots-integration'));
			}
			
			if(is_wp_error($error)) {
				echo '<div id="message" class="error"><p>' . $error->get_error_message() . '</p></div>';
			}
		}
	}
	
	/** 
	 *	Check if the specified WordPress user exists in the TNG database.
	 *
	 *	@author		Nate Jacobs
	 *	@date		11/17/12
	 *	@since		0.1
	 *
	 *	@param	int	$user_id	
	 */
	public function check_user($user_id) {
		// get the family-roots-tng-db settings into an array
		$settings = get_option('family-roots-tng-db');
		
		// get tng user id from usermeta table
		$tng_user_id = get_user_meta($user_id, 'tng_user_id');

		// is there a tng user id for this WordPress user?
		if(empty($tng_user_id)) {
			// if not, return false 
			return FALSE;
		} else {	
			// if so, connect to TNG database and select userID of WordPress user specified
			$users_table = $settings['users_table'];
			$tng_user_id = $tng_user_id[0];

			$tngdb = parent::connect();
			$tng_user = $tngdb->get_row("SELECT userID FROM $users_table WHERE userID = '$tng_user_id'");

			// is there a TNG user with that userID?
			if($tng_user == NULL) {
				// if not, delete the usermeta attached to the specificed user as the user does not exist anymore
				delete_user_meta( $user_id, 'tng_user_id' );
				
				// return false
				return false;
			} else {
				// if so, return the userID to do other things				
				return $tng_user->userID;
			}	
		}
	}
}

$family_roots_user = new FamilyRootsUserManagement();