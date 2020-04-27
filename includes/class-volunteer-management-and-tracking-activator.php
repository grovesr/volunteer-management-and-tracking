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
	    $current_options = get_option( 'vmat_options' );
	    if( ! array_key_exists( 'vmat_posts_per_page', $current_options ) ) {
	        if( ! $current_options ) {
	            $current_options = array(
	                'vmat_posts_per_page' => 6,
	            );
	        } else {
	            $current_options['vmat_posts_per_page'] = 6;
	        }
	        add_option( 'vamt_options' , $current_options );
	    }
	    
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