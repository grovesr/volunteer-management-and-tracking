<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup aspects of the plugin common to public, admin,
 * and login
 *
 */
  
function vmat_event_organizations_dropdown ( $selected=0 ){
    /*
     * Add an Organizations dropdown filter to the Events list in the admin
     */
    $event_organizations_args = array(
        'show_option_all'  => __('View all organizations', 'vmattd'),
        'orderby'           => __('NAME', 'vmattd'),
        'order'             => __('ASC', 'vmattd'),
        'name'              => __('event_organizations_admin_filter', 'vmattd'),
        'taxonomy'          => __('vmat_organization', 'vmattd'),
        'echo'              => 0,
    );
    
    //if we have an organization format already selected, ensure that its value is set to be selected
    if( $selected ){
        $event_organizations_args['selected'] = sanitize_text_field( $selected );
    }
    
    return wp_dropdown_categories($event_organizations_args);
}

function vmat_event_scopes_pulldown( $scope = 'future' ) {
    $page = '';
    $page .= '<select name="scope">';
    foreach ( em_get_scopes() as $key => $value ) {
        $selected = "";
        if ($key == $scope)
            $selected = "selected='selected'";
            $page .= "<option value='$key' $selected>$value</option>  ";
    }
    $page .= '</select>';
    return $page;
}


/**
 * Retreives table of events belonging to user
 * @param array $args
 */

function vmat_em_get_events_admin( $args = array() ){
    ob_start();
    vmat_em_events_admin($args);
    return ob_get_clean();
}

