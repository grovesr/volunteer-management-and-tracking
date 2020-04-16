<?php

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 */
class Volunteer_Management_And_Tracking_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 */
    public static function deactivate() {
        if ( get_role('volunteer') ) {
            remove_role( 'volunteer', 'Volunteer', array( 'read' => true ) );
        }
        self::remove_cpt_caps();
        
    }
    
    private static function remove_cpt_caps() {
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
                $admins->remove_cap( 'edit_' . $cpt . 's' );
                $admins->remove_cap( 'edit_published_' . $cpt . 's' );
                $admins->remove_cap( 'edit_others_' . $cpt . 's' );
                $admins->remove_cap( 'read_private_' . $cpt . 's' );
                $admins->remove_cap( 'delete_' . $cpt .'s' );
                $admins->remove_cap( 'delete_others_' . $cpt .'s' );
                $admins->remove_cap( 'delete_published_' . $cpt .'s' );
                $admins->remove_cap( 'delete_private_' . $cpt .'s' );
                $admins->remove_cap( 'publish_' . $cpt . 's');
            }
            if( $editors ) {
                $editors->remove_cap( 'edit_' . $cpt . 's' );
                $editors->remove_cap( 'edit_published_' . $cpt . 's' );
                $editors->remove_cap( 'edit_others_' . $cpt . 's' );
                $editors->remove_cap( 'read_private_' . $cpt . 's' );
                $editors->remove_cap( 'delete_' . $cpt .'s' );
                $editors->remove_cap( 'delete_others_' . $cpt .'s' );
                $editors->remove_cap( 'delete_published_' . $cpt .'s' );
                $editors->remove_cap( 'delete_private_' . $cpt .'s' );
                $editors->remove_cap( 'publish_' . $cpt . 's');
            }
            
            if( $volunteers ) {
                $volunteers->remove_cap( 'edit_' . $cpt . 's' );
                $volunteers->remove_cap( 'edit_published_' . $cpt . 's' );
                $volunteers->remove_cap( 'delete_' . $cpt .'s' );
                $volunteers->remove_cap( 'delete_published_' . $cpt .'s' );
                $volunteers->remove_cap( 'publish_' . $cpt . 's');
            }
            
        }
    }

}
