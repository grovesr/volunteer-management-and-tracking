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
	    self::add_cpt_caps();
	    
	}
	
	private static function add_cpt_caps() {
	    // gets the administrator role
	    $cpts = array(
	        'vmat_hour',
	        'vmat_funding_stream',
	        'vmat_organization',
	        'vmat_volunteer_type'
	    );
	    $admins = get_role( 'administrator' );
	    $editors = get_role( 'editor' );
	    $volunteers = get_role( 'volunteer' );
	    foreach( $cpts as $cpt ) {
	        if( $admins ) {
	            $admins->add_cap( 'edit_' . $cpt . 's' );
	            $admins->add_cap( 'edit_published_' . $cpt . 's' );
	            $admins->add_cap( 'edit_others_' . $cpt . 's' );
	            $admins->add_cap( 'read_private_' . $cpt . 's' );
	            $admins->add_cap( 'delete_' . $cpt .'s' );
	            $admins->add_cap( 'delete_others_' . $cpt .'s' );
	            $admins->add_cap( 'delete_published_' . $cpt .'s' );
	            $admins->add_cap( 'delete_private_' . $cpt .'s' );
	            $admins->add_cap( 'publish_' . $cpt . 's');
	        }
	        
	        if( $editors ) {
	            $editors->add_cap( 'edit_' . $cpt . 's' );
	            $editors->add_cap( 'edit_published_' . $cpt . 's' );
	            $editors->add_cap( 'edit_others_' . $cpt . 's' );
	            $editors->add_cap( 'read_private_' . $cpt . 's' );
	            $editors->add_cap( 'delete_' . $cpt .'s' );
	            $editors->add_cap( 'delete_others_' . $cpt .'s' );
	            $editors->add_cap( 'delete_published_' . $cpt .'s' );
	            $editors->add_cap( 'delete_private_' . $cpt .'s' );
	            $editors->add_cap( 'publish_' . $cpt . 's');
	        }
	        
	        if( $volunteers ) {
	            $volunteers->add_cap( 'edit_' . $cpt . 's' );
	            $volunteers->add_cap( 'edit_published_' . $cpt . 's' );
	            $volunteers->add_cap( 'delete_' . $cpt .'s' );
	            $volunteers->add_cap( 'delete_published_' . $cpt .'s' );
	            $volunteers->add_cap( 'publish_' . $cpt . 's');
	        }
	   
	    }
	}
}