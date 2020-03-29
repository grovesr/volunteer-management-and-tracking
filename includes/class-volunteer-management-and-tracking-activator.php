<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 */
class Volunteer_Management_And_Tracking_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 */
    
	public static function activate() {
	    if ( ! get_role('volunteer') ) {
	        add_role( 'volunteer', 'Volunteer', array( 'read' => true ) );
	    }
	}
}