# Plugin Workflow

The user activates the plugin

* Activation method runs and checks for the TNG file path using the get_path method in the Utilities class
	* If found:
		* The path is saved to the family-roots-settings option_name in the WordPress options table
		* Runs the get_tng_db_values method in the Utilities class and gets the following from config.php and customconfig.php in the TNG install
			* db host
			* db name
			* db user
			* db password
			* users table name
			* password type