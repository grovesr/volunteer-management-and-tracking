<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 */
class Volunteer_Management_And_Tracking_Admin {

	/**
	 * The ID of this plugin.
	 *
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * param      string    $plugin_name       The name of this plugin.
	 * param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->load_dependencies();

	}
	
	private function load_dependencies() {
	    /**
	     * The html partials for the admin
	     */
	    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/volunteer-management-and-tracking-admin-display.php';
	    
	    /**
	     * The common html partials
	     */
	    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'common/partials/volunteer-management-and-tracking-common-display.php';
	}

	private function default_get_events_args() {
	    /*
	     * all of the various filtering requirements for retrieving a 
	     * list of events. We array_merge this with any args passed into
	     * get_events() to be sure we have all the args we need
	     */
	    return array(
	        'post_type' => EM_POST_TYPE_EVENT,
	        'scope' => 'future',
	        'pno' => 1,
	        'posts_per_page' => get_option( 'posts_per_page' ),
	        'taxonomy' => '',
	        'field' => '',
	        'terms' => array(),
	        's' => '',
	    );
	}
	
	private function tax_query( $args ) {
	    $tax_query = array(
	        'taxonomy' => $args['taxonomy'],
	        'field' => $args['field'],
	        'terms' => $args['terms'],
 	    );
	    // more taxonomy queries can be included by adding a 'relation' key and
	    // another tax_query array to the array passed back in the return
	    return array('taxonomy_query' => $tax_query);
	}
	
	private function organizations_meta_query( $org ) {
	    /*
	     * Create a WP_Query meta query for event organizations
	     */
	    $meta_query = array();
	    $meta_query['key'] = '_vmat_organizations';
	    $meta_query['value'] = ':' . $org . ';';
	    $meta_query['compare'] = 'LIKE';
	    return $meta_query;
	    
	}
	
	private function scope_meta_query( $scope ) {
	    /*
	     * create a WP_Query meta query for event scope
	     */
	    $meta_query = array();
	    // today
	    $today = date( 'Y-m-d' );
	    // tomorrow
	    $tomorrow = date_create_from_format( 'Y-m-d',  $today );
	    date_add( $tomorrow, date_interval_create_from_date_string( "1 day" ) );
	    $tomorrow = date_format( $tomorrow, 'Y-m-d' );
	    // the beginning of this month
	    $this_month_begin = date( 'Y-m' ) . '-01';
	    // the end of this month
	    $this_month_end = date_create_from_format( 'Y-m-d', $this_month_begin );
	    date_add( $this_month_end, date_interval_create_from_date_string( "1 month -1 day" ) );
	    $this_month_end = date_format( $this_month_end, 'Y-m-d' );
	    // the beginning of next month
	    $next_month_begin = date_create_from_format( 'Y-m-d', $this_month_begin );
	    date_add( $next_month_begin, date_interval_create_from_date_string( "1 month" ) );
	    $next_month_begin = date_format( $next_month_begin, 'Y-m-d' );
	    // the end of this month plus one month
	    $this_month_plus_1_end = date_create_from_format( 'Y-m-d', $this_month_end );
	    date_add( $this_month_plus_1_end, date_interval_create_from_date_string( "1 month" ) );
	    $this_month_plus_1_end = date_format( $this_month_plus_1_end, 'Y-m-d' );
	    // the end of this month plus two months
	    $this_month_plus_2_end = date_create_from_format( 'Y-m-d', $this_month_end );
	    date_add( $this_month_plus_2_end, date_interval_create_from_date_string( "2 months" ) );
	    $this_month_plus_2_end = date_format( $this_month_plus_2_end, 'Y-m-d' );
	    // the end of this month plus three months
	    $this_month_plus_3_end = date_create_from_format( 'Y-m-d', $this_month_end );
	    date_add( $this_month_plus_3_end, date_interval_create_from_date_string( "3 months" ) );
	    $this_month_plus_3_end = date_format( $this_month_plus_3_end, 'Y-m-d' );
	    // the end of this month plus six months
	    $this_month_plus_6_end = date_create_from_format( 'Y-m-d', $this_month_end );
	    date_add( $this_month_plus_6_end, date_interval_create_from_date_string( "6 months" ) );
	    $this_month_plus_6_end = date_format( $this_month_plus_6_end, 'Y-m-d' );
	    // the end of this month plus twelve months
	    $this_month_plus_12_end = date_create_from_format( 'Y-m-d', $this_month_end );
	    date_add( $this_month_plus_12_end, date_interval_create_from_date_string( "12 months" ) );
	    $this_month_plus_12_end = date_format( $this_month_plus_12_end, 'Y-m-d' );
	    
	    $start_date_query = array(
	        'key' =>'_event_start_date',
	        'type' => 'DATE',
	    );
	    $end_date_query = array(
	        'key' =>'_event_end_date',
	        'type' => 'DATE',
	    );
	    switch ( $scope ) {
	        case 'future':
	            // events running today and later
	            $end_date_query['value'] = $today;
	            $meta_query['end_date_clause'] = $end_date_query;
	            $meta_query['end_date_clause']['compare'] = '>=';
	            break;
	        case 'past':
	            // events ending before today
	            $end_date_query['value'] = $today;
	            $meta_query['end_date_clause'] = $end_date_query;
	            $meta_query['end_date_clause']['compare'] = '<';
	            break;
	        case 'today':
	            // events running today
	            $start_date_query['value'] = $today;
	            $end_date_query['value'] = $today;
	            $meta_query['start_date_clause'] = $start_date_query;
	            $meta_query['start_date_clause']['compare'] = '<=';
	            $meta_query['end_date_clause'] = $end_date_query;
	            $meta_query['end_date_clause']['compare'] = '>=';
	            $meta_query['relation'] = 'AND';
	            break;
	        case 'tomorrow':
	            // events running tomorrow
	            $start_date_query['value'] = $tomorrow;
	            $end_date_query['value'] = $tomorrow;
	            $meta_query['start_date_clause'] = $start_date_query;
	            $meta_query['start_date_clause']['compare'] = '<=';
	            $meta_query['end_date_clause'] = $end_date_query;
	            $meta_query['end_date_clause']['compare'] = '>=';
	            $meta_query['relation'] = 'AND';
	            break;
	        case 'month':
	            // events running from the beginning of this month until the end of the month
	            $start_date_query['value'] = $this_month_end;
	            $end_date_query['value'] = $this_month_begin;
	            $meta_query['start_date_clause'] = $start_date_query;
	            $meta_query['start_date_clause']['compare'] = '<=';
	            $meta_query['end_date_clause'] = $end_date_query;
	            $meta_query['end_date_clause']['compare'] = '>=';
	            $meta_query['relation'] = 'AND';
	            break;
	        case 'next-month':
	            // events running next month
	            $start_date_query['value'] = $this_month_plus_1_end;
	            $end_date_query['value'] = $next_month_begin;
	            $meta_query['start_date_clause'] = $start_date_query;
	            $meta_query['start_date_clause']['compare'] = '<=';
	            $meta_query['end_date_clause'] = $end_date_query;
	            $meta_query['end_date_clause']['compare'] = '>=';
	            $meta_query['relation'] = 'AND';
	            break;
	        case '1-months':
	            // events running from today until the end of the next full month
	            $start_date_query['value'] = $this_month_plus_1_end;
	            $end_date_query['value'] = $today;
	            $meta_query['start_date_clause'] = $start_date_query;
	            $meta_query['start_date_clause']['compare'] = '<=';
	            $meta_query['end_date_clause'] = $end_date_query;
	            $meta_query['end_date_clause']['compare'] = '>=';
	            $meta_query['relation'] = 'AND';
	            break;
	        case '2-months':
	            // events running from today until the end of the following 2nd full month
	            $start_date_query['value'] = $this_month_plus_2_end;
	            $end_date_query['value'] = $today;
	            $meta_query['start_date_clause'] = $start_date_query;
	            $meta_query['start_date_clause']['compare'] = '<=';
	            $meta_query['end_date_clause'] = $end_date_query;
	            $meta_query['end_date_clause']['compare'] = '>=';
	            $meta_query['relation'] = 'AND';
	            break;
	        case '3-months':
	            // events running from today until the end of the following 3rd full month
	            $start_date_query['value'] = $this_month_plus_3_end;
	            $end_date_query['value'] = $today;
	            $meta_query['start_date_clause'] = $start_date_query;
	            $meta_query['start_date_clause']['compare'] = '<=';
	            $meta_query['end_date_clause'] = $end_date_query;
	            $meta_query['end_date_clause']['compare'] = '>=';
	            $meta_query['relation'] = 'AND';
	            break;
	        case '6-months':
	            // events running from today until the end of the following 6th full month
	            $start_date_query['value'] = $this_month_plus_6_end;
	            $end_date_query['value'] = $today;
	            $meta_query['start_date_clause'] = $start_date_query;
	            $meta_query['start_date_clause']['compare'] = '<=';
	            $meta_query['end_date_clause'] = $end_date_query;
	            $meta_query['end_date_clause']['compare'] = '>=';
	            $meta_query['relation'] = 'AND';
	            break;
	        case '12-months':
	            // events running from today until the end of the following 12th full month
	            $start_date_query['value'] = $this_month_plus_12_end;
	            $end_date_query['value'] = $today;
	            $meta_query['start_date_clause'] = $start_date_query;
	            $meta_query['start_date_clause']['compare'] = '<=';
	            $meta_query['end_date_clause'] = $end_date_query;
	            $meta_query['end_date_clause']['compare'] = '>=';
	            $meta_query['relation'] = 'AND';
	            break;
	        default:
	            $meta_query = array();
	    }
	    return $meta_query;
	}
	
	private function get_events( $get_args=array() ) {
	    /*
	     * get a list of event posts based on passed-in args
	     */
	    //$get_args = array_merge($this->default_get_events_args(), $args);
	    $get_query = array(
	        'post_type' => $get_args['post_type'],
	        'posts_per_page' => $get_args['posts_per_page'],
	        'paged' => $get_args['epno'],
	        'meta_query' => array(),
	    );
	    if ( 'all' != $get_args['scope']) {
	        // do a scope query filter
	        $get_query['meta_query'][] = $this->scope_meta_query( $get_args['scope'] );
	    }
	    if ( '0' != $get_args['vmat_org']) {
	        // do a _vmat_organizations meta query filter
	        $get_query['meta_query'][] = $this->organizations_meta_query( $get_args['vmat_org'] );
	    }
	    if ( ! empty( $get_args['taxonomy'] ) ) {
	        // do a taxonomy query filter
	        $get_query['tax_query'] = $this->tax_query( $get_args );
	    }
	    if ( ! empty( $get_args['events_search'] ) ) {
	        // do a keyword search guery filter
	        $get_query['s'] = $get_args['events_search'];
	    }
	    return new WP_Query( $get_query );
	}
	
	/**
	 * Register the stylesheets for the admin area.
	 *
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Volunteer_Management_And_Tracking_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Volunteer_Management_And_Tracking_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
	    
		wp_enqueue_style( $this->plugin_name . '-css-admin', plugin_dir_url( __FILE__ ) . 'css/volunteer-management-and-tracking-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . '-css-common', plugin_dir_url( __FILE__ ) . '../common/css/volunteer-management-and-tracking-common.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . '-css-bootstrap-grid', plugin_dir_url( __FILE__ ) . '../common/css/bootstrap-grid.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 */
	public function enqueue_scripts( $hook ) {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Volunteer_Management_And_Tracking_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Volunteer_Management_And_Tracking_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name . '-js-admin', plugin_dir_url( __FILE__ ) . 'js/volunteer-management-and-tracking-admin.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name . '-js-common', plugin_dir_url( __FILE__ ) . '../common/js/volunteer-management-and-tracking-common.js', array( 'jquery' ), $this->version, false );
		// don't enqueue ajax scripts on the frontend
		if( 'volunteer-mgmnt_page_vmat_admin_hours' != $hook ) {
		  return;
		}
		
		//wp_deregister_script('heartbeat');
		wp_enqueue_script( 'ajax-script',
		    plugin_dir_url( __FILE__ ) . 'js/volunteer-management-and-tracking-admin-ajax.js',
		    array( 'jquery' )
		    );
		$ajax_nonce = wp_create_nonce( 'vmat_ajax' );
		wp_localize_script( 
		    'ajax-script', 'my_ajax_obj', 
		    array(
		        'ajax_url' => admin_url( 'admin-ajax.php' ),
		        'nonce'    => $ajax_nonce,
		    ) 
		);

	}
	
	public function accumulate_messages( $messages=array(), $class='notice-error' ) {
	    $message_display = '';
	    if ( ! empty( $messages ) ) {
	        $message_display .= '<div class="notice ' . $class . ' is-dismissible">';
	        foreach ( $messages as $message ) {
	            $message_display .= '<p>' . $message . '</p>';
    	    } // accumulate errors
    	    $message_display .= '<button type="button" class="notice-dismiss">';
    	    $message_display .= '<span class="screen-reader-text">Dismiss this notice.</span>';
    	    $message_display .= '</button>';
    	    $message_display .= '</div>';
	    }
	    return $message_display;
	}
	
	function ajax_add_volunteers_to_event() {
	    global $vmat_plugin;
	    check_ajax_referer( 'vmat_ajax' );
	    $errors = array();
	    if ( ! empty( $_POST['event_id'] ) ) {
	        $event_id = $_POST['event_id'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No event_id provided in ajax request. Please try again', 'vmattd' );
	    }
	    if ( ! empty( $_POST['volunteer_ids'] ) ) {
	        $volunteer_ids = $_POST['volunteer_ids'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No volunteer_id provided in ajax request. Please try again', 'vmattd' );
	    }
	    if ( empty( $errors ) ) {
	        $event = get_post( $event_id );
	        if ( !$event ) {
	            $errors[] = __('<strong>ERROR</strong>: No event found. Please try again', 'vmattd' );
	        }
	        $volunteers = array();
	        foreach ( $volunteer_ids as $volunteer_id ) {
	            $volunteer = get_user_by( 'id',  $volunteer_id );
	            if ( !$volunteer ) {
	                $errors[] = __('<strong>ERROR</strong>: No volunteer found. Please try again', 'vmattd' );
	            } else {
	                $volunteers[] = $volunteer;
	            }
	            if ( empty( $errors ) ) {
	                $event_volunteers = $vmat_plugin->get_common()->get_event_volunteers( array( 'event_id' => $event->ID ) );
	                $found_event_volunteer = ! empty( array_filter( $event_volunteers, function( $event_volunteer ) use( $volunteer ) {
	                    return $event_volunteer['WP_User']->ID == $volunteer->ID;
	                }));
                    if ( $found_event_volunteer ) {
                        $errors[] = __('<strong>ERROR</strong>: Attempted to add ' . $volunteer->display_name . ' to an event, when the volunteer was already added to that event', 'vmattd' );
                    }
	            }
	        }
	    }
	    if ( empty( $errors ) ) {
	        // get the meta data associated with this event
	        $event_meta = get_post_meta( $event->ID, '', true);
            // save the default hours associated with this volunteer
            $end_time = date_create_from_format('H:i:s', $event_meta['_event_end_time'][0]);
            $begin_time=date_create_from_format('H:i:s', $event_meta['_event_start_time'][0]);
            $hours_per_day = $end_time->diff($begin_time)->h;
            // Create a new vmat_hours post with the appropriate default information
            foreach ( $volunteers as $volunteer ) {
                $hours = wp_insert_post( array(
                    'post_author' => $volunteer->ID,
                    'post_type' => 'vmat_hours',
                    'post_status' => 'publish',
                    'post_title' => $event->post_title,
                    'meta_input' => array(
                        '_event_id' => $event->ID,
                        '_volunteer_start_date' => $event_meta['_event_start_date'][0],
                        '_hours_per_day' => $hours_per_day,
                        '_volunteer_num_days' => 0,
                        '_approved' => false,
                    )
                ) );
                if ( is_wp_error( $hours ) || $hours == 0 ) {
                    $error_message = '';
                    if ( is_wp_error( $hours ) ) {
                        $error_message = $hours->get_error_message();
                    }
                    $errors[] = __('<strong>ERROR</strong>: Error adding  ' . $volunteer->display_name . ' to event.< br/>' . $error_message . '. Please try again', 'vmattd' );
                }
            }
	    }
	    
	    if ( ! empty( $errors ) ) {
	        // ajax request failed
	        $results = array(
	            'notice' => $this->accumulate_messages(
	             $errors,
	             'notice-error' ),
	        );
	        wp_send_json_error( $results );
	    }
	    $args = array();
	    $args['event']= $event;
	    $args['vpno'] = 1;
	    $args['evpno'] = 1;
	    $args['posts_per_page'] = get_option( 'posts_per_page' );
	    $args['volunteers_search'] = '';
	    $args['event_volunteers_search'] = '';
	    $args['volunteers'] = $vmat_plugin->get_common()->get_volunteers_not_added_to_event( $args );;
	    $args['event_volunteers'] = $vmat_plugin->get_common()->get_volunteers_added_to_event( $args );
	    // generate replacement html for the volunteers and event_volunteers tables
	    ob_start();
	    vmat_manage_volunteers_admin( $args );
	    $html = ob_get_clean();
	    $message = $volunteers[0]->display_name;
	    if ( count( $volunteers ) > 1 ) {
	        $message = count( $volunteers ) . ' Volunteers';
	    }
	    $results = array(
	        'notice' => '',
	        'success_notice'=> $this->accumulate_messages( 
	            array( __( 'Added ' . $message , 'vmattd' ) ),
	            'notice-success'),
	        'html' => $html,
	    );
	    // ajax request succeeded
	    wp_send_json_success( $results );
	}
	
	function ajax_check_input() {
	    global $vmat_plugin;
	    check_ajax_referer( 'vmat_ajax' );
	    $event = null;
	    $volunteers = array();
	    $volunteer_data = array();
	    $errors = array();
	    if ( ! empty( $_POST['event_id'] ) ) {
	        $event_id = $_POST['event_id'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No event_id provided in ajax request. Please try again', 'vmattd' );
	    }
	    if ( ! empty( $_POST['volunteer_data'] ) ) {
	        $volunteer_data = $_POST['volunteer_data'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No volunteer_data provided in ajax request. Please try again', 'vmattd' );
	    }
	    if ( empty( $errors ) ) {
	        $event = get_post( $event_id );
	        if ( !$event ) {
	            $errors[] = __('<strong>ERROR</strong>: No event found. Please try again', 'vmattd' );
	        }
	        $volunteers = array();
	        foreach ( $volunteer_data as $volunteer_id=>$data ) {
	            $volunteer = get_user_by( 'id',  $volunteer_id );
	            if ( !$volunteer ) {
	                $errors[] = __('<strong>ERROR</strong>: No volunteer found. Please try again', 'vmattd' );
	            } else {
	                $volunteers[] = $volunteer;
	            }
	            if ( empty( $errors ) ) {
	                $event_volunteers = $vmat_plugin->get_common()->get_event_volunteers( array( 'event_id' => $event->ID ) );
	                $found_event_volunteer = ! empty( array_filter( $event_volunteers, function( $event_volunteer ) use( $volunteer ) {
	                    return $event_volunteer['WP_User']->ID == $volunteer->ID;
	                }));
	                    if ( ! $found_event_volunteer ) {
	                        $errors[] = __('<strong>ERROR</strong>: Attempted to remove ' . $volunteer->display_name . ' from an event, when the volunteer was never added to that event', 'vmattd' );
	                    }
	            }
	        }
	    }
	    return array( 
	        'errors' => $errors, 
	        'volunteers' => $volunteers,
	        'volunteer_data' => $volunteer_data,
	        'event' => $event,
	    );
	}
	
	function ajax_get_paginate_vmat_admin_hours_data( $event_id=0 ) {
	    $errors = array();
	    $result = array();
	    if ( array_key_exists( 'posts_per_page', $_POST['data'] ) ) {
	        $result['posts_per_page'] = $_POST['data']['posts_per_page'];
	    } else {
	        $errors[] = __( '<strong>Error</strong>: Missing posts_per_page.', 'vmattd' );
	    }
	    if ( $event_id ) {
	        $event = get_post( $event_id );
	        if ( !$event ) {
	            $errors[] = __('<strong>ERROR</strong>: No event found. Please try again', 'vmattd' );
	        }
	        // set up the args for the manage_volunteers tables
	        $result['event'] = $event;   
	        if ( array_key_exists( 'vpno', $_POST['data'] ) ) {
	            $result['vpno'] = $_POST['data']['vpno'];
	        } else {
	            $errors[] = __( '<strong>Error</strong>: Missing vpno page indicator.', 'vmattd' );
	        }
	        if ( array_key_exists( 'evpno', $_POST['data'] ) ) {
	            $result['evpno'] = $_POST['data']['evpno'];
	        } else {
	            $errors[] = __( '<strong>Error</strong>: Missing evpno page indicator.', 'vmattd' );
	        }
	        if ( array_key_exists( 'volunteers_search', $_POST['data'] ) ) {
	            $result['volunteers_search'] = $_POST['data']['volunteers_search'];
	        } else {
	            $errors[] = __( '<strong>Error</strong>: Missing volunteers_search filter.', 'vmattd' );
	        }
	    } else {
	        // set up the args for the select event table
	        if ( array_key_exists( 'epno', $_POST['data'] ) ) {
	            $result['epno'] = $_POST['data']['epno'];
	        } else {
	            $errors[] = __( '<strong>Error</strong>: Missing epno page indicator.', 'vmattd' );
	        }
	        if ( array_key_exists( 'vmat_org', $_POST['data'] ) ) {
	            $result['vmat_org'] = $_POST['data']['vmat_org'];
	        } else {
	            $errors[] = __( '<strong>Error</strong>: Missing vmat_org filter.', 'vmattd' );
	        }
	        if ( array_key_exists( 'scope', $_POST['data'] ) ) {
	            $result['scope'] = $_POST['data']['scope'];
	        } else {
	            $errors[] = __( '<strong>Error</strong>: Missing scope filter.', 'vmattd' );
	        }
	        if ( array_key_exists( 'events_search', $_POST['data'] ) ) {
	            $result['events_search'] = $_POST['data']['events_search'];
	        } else {
	            $errors[] = __( '<strong>Error</strong>: Missing events_search filter.', 'vmattd' );
	        }
	    }
	    $result['errors'] = $errors;
	    return $result;
	}
	
	function ajax_get_manage_volunteers_html( $args=array() ) {
	    global $vmat_plugin;
	    
	    $args['volunteers'] = $vmat_plugin->get_common()->get_volunteers_not_added_to_event( $args );;
	    $args['event_volunteers'] = $vmat_plugin->get_common()->get_volunteers_added_to_event( $args );
	    // generate replacement html for the volunteers and event_volunteers tables
	    ob_start();
	    vmat_manage_volunteers_admin( $args );
	    return ob_get_clean();
	}
	
	function ajax_get_select_event_html( $args=array() ) {
	    global $vmat_plugin;
	    $args['post_type'] = EM_POST_TYPE_EVENT;
	    $args['events'] = $this->get_events( $args );
	    
	    // generate replacement html for the volunteers and event_volunteers tables
	    ob_start();
	    vmat_get_events_admin( $args );
	    return ob_get_clean();
	}
	
	function ajax_remove_volunteers_from_event() {
	    $check = $this->ajax_check_input();
	    $errors = $check['errors'];
	    $volunteers = $check['volunteers'];
	    $event = $check['event'];
	    if ( empty( $errors ) ) {
	        // Delete vmat_hours posts associated with this event and with post_author == volunteer
	        foreach ( $volunteers as $volunteer ) {
	            $hours_args = array(
	                'nopaging' => true,
	                'author' => $volunteer->ID,
	                'post_type' => 'vmat_hours',
	                'meta_query' => array(
	                    array(
	                        'key' => '_event_id',
    	                    'value' => $event->ID,
	                    )
	                )
	            );
	            $hours_query = new WP_Query( $hours_args );
	            if ( $hours_query->found_posts ) {
	                foreach ( $hours_query->posts as $vmat_hours ) {
	                    if ( ! wp_delete_post( $vmat_hours->ID, true) ) {
	                        $errors[] = __('<strong>ERROR</strong>: Error removing  ' . $volunteer->display_name . ' from event.', 'vmattd' );
	                    }
	                }
	            } else {
	                $errors[] = __('<strong>ERROR</strong>: Error removing  ' . $volunteer->display_name . ' from event (not found).', 'vmattd' );
	            }
	        }
	    }
	    
	    if ( ! empty( $errors ) ) {
	        // ajax request failed
	        $results = array(
	            'notice' => $this->accumulate_messages(
	                $errors,
	                'notice-error' ),
	        );
	        wp_send_json_error( $results );
	    }
	    $args = array();
	    $args['event']= $event;
	    $args['vpno'] = 1;
	    $args['evpno'] = 1;
	    $args['posts_per_page'] = get_option( 'posts_per_page' );
	    $args['volunteers_search'] = '';
	    $args['event_volunteers_search'] = '';
	    $html = $this->ajax_get_manage_volunteers_html( $args );
	    $message = $volunteers[0]->display_name;
	    if ( count( $volunteers ) > 1 ) {
	        $message = count( $volunteers ) . ' Volunteers';
	    }
	    $results = array(
	        'notice' => '',
	        'success_notice'=> $this->accumulate_messages(
	            array( __( 'Removed ' . $message , 'vmattd' ) ),
	            'notice-success'),
	        'html' => $html,
	    );
	    // ajax request succeeded
	    wp_send_json_success( $results );
	}
	
	function ajax_save_event_volunteers_data() {
	    $check = $this->ajax_check_input();
	    $errors = $check['errors'];
	    $volunteers = $check['volunteers'];
	    $volunteer_data = $check['volunteer_data'];
	    $event = $check['event'];
	    if ( empty( $errors ) ) {
	        // Save the meta_data associated with this event and with post_author == volunteer
	        foreach ( $volunteers as $volunteer ) {
	            $hours_args = array(
	                'nopaging' => true,
	                'author' => $volunteer->ID,
	                'post_type' => 'vmat_hours',
	                'meta_query' => array(
	                    array(
	                        'key' => '_event_id',
	                        'value' => $event->ID,
	                    )
	                )
	            );
	            $hours_query = new WP_Query( $hours_args );
	            if ( $hours_query->found_posts ) {
	                foreach ( $volunteer_data[$volunteer->ID] as $key=>$value ) {
	                    update_post_meta( $hours_query->post->ID, $key, $value );
	                }
	            } else {
	                $errors[] = __('<strong>ERROR</strong>: Error updating data for  ' . $volunteer->display_name . ' (not found).', 'vmattd' );
	            }  
	        }
	    }
	    
	    if ( ! empty( $errors ) ) {
	        // ajax request failed
	        $results = array(
	            'notice' => $this->accumulate_messages(
	                $errors,
	                'notice-error' ),
	        );
	        wp_send_json_error( $results );
	    }
	    $args = array();
	    $args['event']= $event;
	    $args['vpno'] = 1;
	    $args['evpno'] = 1;
	    $args['posts_per_page'] = get_option( 'posts_per_page' );
	    $args['volunteers_search'] = '';
	    $args['event_volunteers_search'] = '';
	    $html = $this->ajax_get_manage_volunteers_html( $args );
	    $message = $volunteers[0]->display_name;
	    if ( count( $volunteers ) > 1 ) {
	        $message = count( $volunteers ) . ' Volunteers';
	    }
	    $results = array(
	        'notice' => '',
	        'success_notice'=> $this->accumulate_messages(
	            array( __( 'Updated ' . $message , 'vmattd' ) ),
	            'notice-success'),
	        'html' => $html,
	    );
	    // ajax request succeeded
	    wp_send_json_success( $results );
	}
	
	function ajax_approve_volunteers_hours_for_event() {
	    global $vmat_plugin;
	    $check = $this->ajax_check_input();
	    $errors = $check['errors'];
	    $volunteers = $check['volunteers'];
	    $event = $check['event'];
	    if ( empty( $errors ) ) {
	        // Save the meta_data associated with this event and with post_author == volunteer
	        foreach ( $volunteers as $volunteer ) {
	            $hours_args = array(
	                'nopaging' => true,
	                'author' => $volunteer->ID,
	                'post_type' => 'vmat_hours',
	                'meta_query' => array(
	                    array(
	                        'key' => '_event_id',
	                        'value' => $event->ID,
	                    )
	                )
	            );
	            $hours_query = new WP_Query( $hours_args );
	            if ( $hours_query->found_posts ) {
	               update_post_meta( $hours_query->post->ID, '_approved', 1 );
	            } else {
	                $errors[] = __('<strong>ERROR</strong>: Error updating data for  ' . $volunteer->display_name . ' (not found).', 'vmattd' );
	            }
	            
	        }
	    }
	    
	    if ( ! empty( $errors ) ) {
	        // ajax request failed
	        $results = array(
	            'notice' => $this->accumulate_messages(
	                $errors,
	                'notice-error' ),
	        );
	        wp_send_json_error( $results );
	    }
	    $args = array();
	    $args['event']= $event;
	    $args['vpno'] = 1;
	    $args['evpno'] = 1;
	    $args['posts_per_page'] = get_option( 'posts_per_page' );
	    $args['volunteers_search'] = '';
	    $args['event_volunteers_search'] = '';
	    $html = $this->ajax_get_manage_volunteers_html( $args );
	    $message = $volunteers[0]->display_name;
	    if ( count( $volunteers ) > 1 ) {
	        $message = count( $volunteers ) . ' Volunteers';
	    }
	    $results = array(
	        'notice' => '',
	        'success_notice'=> $this->accumulate_messages(
	            array( __( 'Updated ' . $message , 'vmattd' ) ),
	            'notice-success'),
	        'html' => $html,
	    );
	    // ajax request succeeded
	    wp_send_json_success( $results );
	}
	
	function ajax_set_default_volunteers_hours_for_event() {
	    global $vmat_plugin;
	    $check = $this->ajax_check_input();
	    $errors = $check['errors'];
	    $volunteers = $check['volunteers'];
	    $event = $check['event'];
	    if ( empty( $errors ) ) {
	        $event_data = $vmat_plugin->get_common()->get_event_data( $event->ID );
	        // Save the meta_data associated with this event and with post_author == volunteer
	        foreach ( $volunteers as $volunteer ) {
	            $hours_args = array(
	                'nopaging' => true,
	                'author' => $volunteer->ID,
	                'post_type' => 'vmat_hours',
	                'meta_query' => array(
	                    array(
	                        'key' => '_event_id',
	                        'value' => $event->ID,
	                    )
	                )
	            );
	            $hours_query = new WP_Query( $hours_args );
	            if ( $hours_query->found_posts ) {
	                update_post_meta( $hours_query->post->ID, '_volunteer_start_date', $event_data['start_date'] );
	                update_post_meta( $hours_query->post->ID, '_hours_per_day', $event_data['hours_per_day'] );
	                update_post_meta( $hours_query->post->ID, '_volunteer_num_days', $event_data['days'] );
	                update_post_meta( $hours_query->post->ID, '_volunteer_start_date', $event_data['start_date'] );
	            } else {
	                $errors[] = __('<strong>ERROR</strong>: Error updating data for  ' . $volunteer->display_name . ' (not found).', 'vmattd' );
	            }
	        }
	    }
	    
	    if ( ! empty( $errors ) ) {
	        // ajax request failed
	        $results = array(
	            'notice' => $this->accumulate_messages(
	                $errors,
	                'notice-error' ),
	        );
	        wp_send_json_error( $results );
	    }
	    $args = array();
	    $args['event']= $event;
	    $args['vpno'] = 1;
	    $args['evpno'] = 1;
	    $args['posts_per_page'] = get_option( 'posts_per_page' );
	    $args['volunteers_search'] = '';
	    $args['event_volunteers_search'] = '';
	    $html = $this->ajax_get_manage_volunteers_html( $args );
	    $message = $volunteers[0]->display_name;
	    if ( count( $volunteers ) > 1 ) {
	        $message = count( $volunteers ) . ' Volunteers';
	    }
	    $results = array(
	        'notice' => '',
	        'success_notice'=> $this->accumulate_messages(
	            array( __( 'Updated ' . $message , 'vmattd' ) ),
	            'notice-success'),
	        'html' => $html,
	    );
	    // ajax request succeeded
	    wp_send_json_success( $results );
	}
	
	public function ajax_paginate_vmat_admin_hours() {
	    check_ajax_referer( 'vmat_ajax' );
	    $data = array();
	    $event_id = 0;
	    $errors = array();
	    if ( ! empty( $_POST['data'] ) ) {
	        $data = $_POST['data'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No data specified in paginate request. Please try again', 'vmattd' );
	    }
	    if ( empty( $errors ) ) {
	        if ( ! empty( $data['admin_page'] ) ) {
	            $admin_page = $data['admin_page'];
	        } else {
	            $errors[] = __('<strong>ERROR</strong>: No admin_page specified in paginate request. Please try again', 'vmattd' );
	        }
	        if ( empty( $errors ) ) {
	            switch ( $admin_page ) {
	                case 'vmat_admin_hours':
	                    if ( ! empty( $data['event_id'] ) ) {
	                        $event_id = absint( $data['event_id'] );
	                    }
	                    if ( ! $event_id ) {
	                        $target = '#vmat_select_event_admin';
	                        $args = $this->ajax_get_paginate_vmat_admin_hours_data( $event_id );
	                        if ( array_key_exists( 'errors' , $args ) ) {
	                            $errors = array_merge( $errors, $args['errors'] );
	                        }
	                        if ( empty( $errors ) ) {
	                            $html = $this->ajax_get_select_event_html( $args );
	                        }
	                    } else {
	                        $target = '#vmat_manage_volunteers_admin';
	                        $args = $this->ajax_get_paginate_vmat_admin_hours_data( $event_id );
	                        if ( array_key_exists( 'errors' , $args ) ) {
	                            $errors = array_merge( $errors, $args['errors'] );
	                        }
	                        if ( empty( $errors ) ) {
	                            $html = $this->ajax_get_manage_volunteers_html( $args );
	                        }
	                    }
	                    break;
	                default:
	                    // default action
	            }
	        }
	    }
	    if ( ! empty( $errors ) ) {
	        // ajax request failed
	        $results = array(
	            'notice' => $this->accumulate_messages(
	                $errors,
	                'notice-error' ),
	        );
	        wp_send_json_error( $results );
	    }
	    $results = array(
	        'target' => $target,
	        'html' => $html,
	    );
	    // ajax request succeeded
	    wp_send_json_success( $results );
	}
	
	public function admin_main_page() { 
	    add_menu_page(
	        '',
	        'Volunteer Mgmnt',
	        'manage_options',
	        'vmat_admin_main',
	        array($this, 'vmat_admin_dashboard_html'),
	        'dashicons-groups',
	        26
	    );
	}
	
	public function remove_admin_main_submenu() {
	    remove_submenu_page(
	        'vmat_admin_main',
	        'vmat_admin_main'
	        );
	}
	
	public function remove_admin_hours_menu() {
	    remove_menu_page(
	        'edit.php?post_type=vmat_hours'
	        );
	}
	
	public function remove_admin_organization_menu() {
	    remove_menu_page(
	        'edit.php?post_type=vmat_organization'
	        );
	}
	
	public function remove_admin_funding_stream_menu() {
	    remove_menu_page(
	        'edit.php?post_type=vmat_funding_stream'
	        );
	}
	
	public function admin_dashboard_page() {
	    add_submenu_page(
	        'vmat_admin_main',
	        'Volunteer Management and Tracking - Dashboard',
	        'Dashboard',
	        'manage_options',
	        'vmat_admin_dashboard',
	        array($this, 'vmat_admin_dashboard_html')
	        );
	}
	
	public function admin_volunteers_page() {
	    add_submenu_page(
	        'vmat_admin_main',
	        'Volunteer Management and Tracking - Volunteers',
	        'Volunteers',
	        'manage_options',
	        'vmat_admin_volunteers',
	        array($this, 'vmat_admin_volunteers_html')
	        );
	}
	
	public function admin_hours_page() {
	    add_submenu_page(
	        'vmat_admin_main',
	        'Volunteer Management and Tracking - Volunteer Hours',
	        'Add Hours',
	        'manage_options',
	        'vmat_admin_hours',
	        array($this, 'vmat_admin_hours_html')
	        );
	}
	
	public function admin_reports_page() {
	    add_submenu_page(
	        'vmat_admin_main',
	        'Volunteer Management and Tracking - Reports',
	        'Reports',
	        'manage_options',
	        'vmat_admin_reports',
	        array($this, 'vmat_admin_reports_html')
	        );
	}
	
	public function admin_settings_page() {
	    add_submenu_page(
	        'vmat_admin_main',
	        'Volunteer Management and Tracking - Settings',
	        'Settings',
	        'manage_options',
	        'vmat_admin_settings',
	        array($this, 'vmat_admin_settings_html')
	        );
	}
	
	public function admin_organizations_page() {
	    add_submenu_page(
	        'vmat_admin_main',
	        'Volunteer Management and Tracking - Organizations',
	        'Organizations',
	        'manage_options',
	        'edit.php?post_type=vmat_organization'
	        );
	}
	
	public function admin_funding_streams_page() {
	    add_submenu_page(
	        'vmat_admin_main',
	        'Volunteer Management and Tracking - Funding Streams',
	        'Funding Streams',
	        'manage_options',
	        'edit.php?post_type=vmat_funding_stream'
	        );
	}
	
	public function modify_event_list_row_actions( $actions, $post ) {
	    /*
	     * Add custom actions to the quick links that appear in the Events list
	     *     Add Hours - go to VMAT Hours page and add volunteer hours to this event
	     */
	    if ( $post->post_type == EM_POST_TYPE_EVENT || $post->post_type == 'event-recurring' ) {
	        
	        // Build your links URL.
	        $add_link = admin_url( 'admin.php' );
	        $query_vars = array(
	            'page' => 'vmat_admin_hours',
	            'event_id' => $post->ID,
	        );
	        $add_link = add_query_arg( $query_vars, $add_link );
	        
	        
	        // You can check if the current user has some custom rights.
            // Include a nonce in this link
            $add_link = wp_nonce_url( add_query_arg( array( 'action' => 'add_hours' ), $add_link ), 'add_event_hours_nonce' );
            
            // Add the new Copy quick link.
            $actions = array_merge( $actions, array(
                'add' => sprintf( '<a href="%1$s" title="Add volunteer hours">%2$s</a>',
                    esc_url( $add_link ),
                    esc_html( __( 'Add Hours', 'vmattd' ) )
                    )
	            ) 
                );
	            
	    }
	    
	    return $actions;
	}
	
	public function add_em_org_meta_boxes(){
	    add_meta_box('em-event-orgs', __('Organizations', 'vmattd'), array( $this, 'organizations_meta_box'), EM_POST_TYPE_EVENT, 'side','high');
	    add_meta_box('em-event-orgs', __('Organizations', 'vmattd'), array( $this, 'organizations_meta_box'),'event-recurring', 'side','high');
	}
	
	public function add_org_funding_stream_meta_box(){
	    add_meta_box('organization-funding-streams', __('Funding Streams', 'vmattd'), array( $this, 'funding_streams_meta_box'), 'vmat_organization', 'side','low');
	}
	
	public function add_organization_fields_meta_box(){
	    add_meta_box('organization-fields', 'Additional Information', array( $this, 'organization_fields_meta_box'), 'vmat_organization', 'normal','low');
	}
	
	public function add_funding_stream_fields_meta_box(){
	    add_meta_box('funding-stream-fields', 'Additional Information', array( $this, 'funding_stream_fields_meta_box'), 'vmat_funding_stream', 'normal','low');
	}
	
	public function organizations_meta_box( $event ) {
	    $this->link_post_meta_box('vmat_organization', $event);
	}
	
	public function funding_streams_meta_box( $organization ) {
	    $this->link_post_meta_box('vmat_funding_stream', $organization);
	}
	
	public function link_post_meta_box( $type, $link_to_post ) {
	    /*
	     * Generic function to create a meta box for selecting multiple $type
	     * CPT posts to link to $link_to_post in the post edit page
	     */
	    if ( ! $type ) {
	        return false;
	    }
	    global $vmat_plugin;
	    $posts_to_link = array();
	    $selected_posts_to_link = array();
	    $unselected_posts_to_link = array();
	    foreach ( $vmat_plugin->get_common()->get_post_type($type)->posts as $post_to_link ) {
	        if ( in_array( absint($post_to_link->ID), get_post_meta( $link_to_post->ID, '_' . $type . 's' , true ) ) ) {
	            $selected_posts_to_link[] = array(
	                'id' => $post_to_link->ID,
	                'name' => __($post_to_link->post_title, 'vmattd')
	            );
	        } else {
	            $unselected_posts_to_link[] = array(
	                'id' => $post_to_link->ID,
	                'name' => __($post_to_link->post_title, 'vmattd')
	            );
	        }
	    }
	    foreach( $selected_posts_to_link as $post_to_link ) {
	        $posts_to_link[] = $post_to_link;
	    }
	    foreach( $unselected_posts_to_link as $post_to_link ) {
	        $posts_to_link[] = $post_to_link;
	    }
	    ?>
	    <div id="<?php echo $type; ?>s_checklist" class="tabs-panel">
	    <?php
	    foreach( $posts_to_link as $post_to_link ){
	        ?>
            <label>
            <input type="checkbox" name="<?php echo $type; ?>s[]" 
            value="<?php echo $post_to_link['id']; ?>" 
            <?php if ( in_array( absint($post_to_link['id']), get_post_meta( $link_to_post->ID, '_' . $type . 's' , true ) ) ) { echo 'checked="checked"';} ?> /> 
            <?php echo $post_to_link['name'] ?>
            </label><br />          
            <?php
        }
        ?>
        </div>
        <?php
	}
	public function organization_fields_meta_box( $organization ) {
    	?>
    	<label for="post_name">
    		Slug:
    		<input type="text" name="post_name" size="30" value="<?php echo $organization->post_name; ?>" id="post_name" spellcheck="true" autocomplete="off">
    	</label>
    	<?php 
	}
	
	public function funding_stream_fields_meta_box( $funding_stream ) {
	    $funding_start_date = '';
	    $funding_end_date = '';
	    $fiscal_start_months = array();
	    $month_names = array(
	        'Jan',
	        'Feb',
	        'Mar',
	        'Apr',
	        'May',
	        'Jun',
	        'Jul',
	        'Aug',
	        'Sep',
	        'Oct',
	        'Nov',
	        'Dec'
	    );
	    if ($funding_stream ) {
	        $funding_start_date = get_post_meta( $funding_stream->ID, '_funding_start_date', true );
	        $funding_end_date = get_post_meta( $funding_stream->ID, '_funding_end_date', true );
	        $fiscal_start_months = get_post_meta( $funding_stream->ID, '_fiscal_start_months', true );
	    }
	    ?>
    	<div class="row">
    		<div class="col-2 vmat-form-label">
        	<label for="funding_start_date">
        		Funding Start Date:
        	</label>
        	</div>
        	<div class="col vmat-form-field">
        		<input type="date" name="funding_start_date" value="<?php echo $funding_start_date; ?>" id="funding_start_date" autocomplete="off">
        	</div>
    	</div>
    	<div class="row">
    		<div class="col-2 vmat-form-label">
        	<label for="funding_end_date">
        		Funding End Date:
        	</label>
        	</div>
        	<div class="col vmat-form-field">
        		<input type="date" name="funding_end_date" value="<?php echo $funding_end_date; ?>" id="funding_end_date" autocomplete="off">
        	</div>
    	</div>
    	<div class="row">
    		<div class="col-2 vmat-form-label">
        	<label for="fiscal_start_months">
        		Fiscal Start Months:
        	</label>
        	</div>
        	<div class="col vmat-form-field">
            	<fieldset id="vmat_fiscal_start_months" >
            		<legend><?php _e('Choose the staring month for each fiscal period', 'vmattd')?></legend>
                    	<?php
                    	foreach ( $month_names as $key => $month ) {
                    	    $checked = '';
                    	    if ( in_array( $month, $fiscal_start_months ) ) {
                    	        $checked = ' checked';
                    	    }
                    	   $month_input = '<input type="checkbox" ';
                    	   $month_input .= 'id="vmat_' . esc_attr($month) .'" ';
                    	   $month_input .= 'name="fiscal_start_months[]" ';
                    	   $month_input .= 'value="' . $month . '"' . $checked . '>';
                    	   $month_input .= __($month, 'vmattd') . '</input>';
                    	   echo $month_input;
                    	}
                    	?>
                	</legend>
                </fieldset>
        	</div>
    	</div>
    	<div class="row">
	    	<div class="col-2 vmat-form-label">
            	<label for="post_name">
            		Slug:        		
            	</label>
            </div>
            <div class="col vmat-form-field">
            	<input type="text" name="post_name" size="30" value="<?php echo $funding_stream->post_name; ?>" id="post_name" autocomplete="off" required>
            </div>
    	</div>
    	<?php 
	}
	
	public function update_event_organizations_meta( $event_id ) {
	    /*
	     * Updae the event meta data by adding any selected organization ids
	     * to the event meta data
	     */
	    if ( ! current_user_can( 'edit_events' ) ) {
	        return false;
	    }
	    if ( array_key_exists( 'vmat_organizations', $_POST ) ) {
	        $organizations = array_map( absint, $_POST[ 'vmat_organizations' ] );
	        update_post_meta( $event_id, '_vmat_organizations', $organizations );
	    } else {
	        // no organization was selected, so we should delete any existing organization
	        // in the meta data
	        delete_post_meta( $event_id, '_vmat_organizations' );
	    }
	}
	
	public function update_org_funding_streams_meta( $org_id ) {
	    /*
	     * Updae the organization meta data by adding any selected funding stream ids
	     * to the organization meta data
	     */
	    if ( ! current_user_can( 'edit_posts' ) ) {
	        return false;
	    }
	    if ( array_key_exists( 'vmat_funding_streams', $_POST ) ) {
	        $funding_streams = array_map( absint, $_POST[ 'vmat_funding_streams' ] );
	        update_post_meta( $org_id, '_vmat_funding_streams', $funding_streams );
	    } else {
	        // no funding stream was selected, so we should delete any existing funding stream
	        // in the meta data
	        delete_post_meta( $org_id, '_vmat_funding_streams' );
	    }
	}
	
	public function update_organization_fields_meta( $org_id ) {
	    /*
	     * Updae the organization meta data 
	     */
	    if ( ! current_user_can( 'edit_posts' ) ) {
	        return false;
	    }
	    /*
	     * do something here if we decide to store meta data for funding streams
	     */
	    
	}
	
	public function update_funding_stream_fields_meta( $funding_id ) {
	    /*
	     * Updae the funding stream meta data 
	     */
	    global $vmat_plugin;
	    if ( ! current_user_can( 'edit_posts' ) ) {
	        return false;
	    }
	    if ( array_key_exists(
	        'funding_start_date', $_POST ) &&
	        $vmat_plugin->get_common()->validate_date( $_POST['funding_start_date'] )
	        ) {
	            $funding_start_date = sanitize_text_field( $_POST['funding_start_date'] );
	            update_post_meta( $funding_id, '_funding_start_date', $funding_start_date );
	    } else {
	        delete_post_meta( $funding_id , '_funding_start_date' );
	    } 
	    if ( array_key_exists( 
	        'funding_end_date', $_POST ) &&
	        $vmat_plugin->get_common()->validate_date( $_POST['funding_end_date'] )
	        ) {
	        $funding_end_date = sanitize_text_field( $_POST['funding_end_date'] );
	        update_post_meta( $funding_id, '_funding_end_date', $funding_end_date );
	    } else {
	        delete_post_meta( $funding_id , '_funding_end_date' );
	    }
	    if ( array_key_exists( 'fiscal_start_months', $_POST ) ) {
	        $fiscal_start_months = $_POST['fiscal_start_months'];
	        $valid = true;
	        foreach ( $fiscal_start_months as $month ) {
	            if ( ! $vmat_plugin->get_common()->validate_month( $month ) ) {
	               $valid = false;
	               break;
	            }
	        }
	        if ( $valid ) {
	            $fiscal_start_months = array_map( sanitize_text_field, $fiscal_start_months );
	            update_post_meta( $funding_id, '_fiscal_start_months', $fiscal_start_months );
	        } else {
	           return false;
	        }
	    } else {
	        delete_post_meta( $funding_id , '_fiscal_start_months' );
	    }
	}
	
	public function organization_filtering( $post_type ){
	    /*
	     * Provide an organizations dropdown to filter event _vmat_organizations
	     * meta data
	     */
	    if( EM_POST_TYPE_EVENT != $post_type && 
	        'event-recurring' != $post_type) {
	        return;
	    }
	    $selected = '';
	    $request_attr = 'vmat_org';
	    if ( isset($_REQUEST[$request_attr]) ) {
	        $selected = $_REQUEST[$request_attr];
	    }
	    $organizations = get_posts(
	        array(
	            'nopaging' => true,
	            'post_type' => 'vmat_organization',
	        )
	        );
	    
	    //build a custom dropdown list of values to filter by
	    echo '<select id="vmat_org" name="vmat_org">';
	    echo '<option value="0">' . __( 'Show all organizations', 'vmattd' ) . ' </option>';
	    foreach($organizations as $org){
	        $select = '';
	        if ( $selected == $org->ID ) {
	            $select = ' selected="selected"';
	        }
	        echo '<option value="' . $org->ID . '"' . $select . '>' . $org->post_title . ' </option>';
	    }
	    echo '</select>';
	}

	public function filter_request_query( $query ) {
	    //modify the query only if is admin and main query.
	    if( !(is_admin() AND $query->is_main_query()) ){
	        return $query;
	    }
	    //we want to modify the query for the targeted custom post and filter option
	    if( ! ( (
	    EM_POST_TYPE_EVENT == $query->query['post_type'] ||
	    'event-recurring' == $query->query['post_type'] ) && 
	    isset($_REQUEST['vmat_org']) ) ) {
            return $query;
        }
        //for the default value of our filter no modification is required
        if(0 == $_REQUEST['vmat_org']){
          return $query;
        }
       // modify the meta query_vars. to parse the serialized _vmat_organizations
       // array by using LIKE with the org_id preceded by : and followed by ;
        $query->query_vars['meta_query'][] = array(array(
          'key' => '_vmat_organizations',
          'value' => ':' . $_REQUEST['vmat_org'] . ';',
          'compare' => 'LIKE',
        ));
        return $query;
    }
    
    function add_orgs_column_to_em( $columns ) {
        $post_type = get_post_type();
        if ( $post_type == EM_POST_TYPE_EVENT || $post_type == 'event-recurring' ) {
            $new_columns = array(
                'Orgs' => esc_html__( 'Orgs', 'vmattd' ),
            );
            return array_merge( $columns, $new_columns );
        }
        return $columns;
    }

    public function fill_event_orgs_column( $column_name, $event_id ) {
        global $vmat_plugin;
        $post_type = get_post_type();
        if ( ( $post_type == EM_POST_TYPE_EVENT || $post_type == 'event-recurring' ) && 
             $column_name == 'Orgs' ) {
            _e( $vmat_plugin->get_common()->get_event_organizations_string( $event_id ));
        }
    }
    
    public function admin_hours_prep_args() {
        global $vmat_plugin, $wpdb;
        $form_id = $vmat_plugin->get_common()->var_from_get( 'form_id', '' );
        $notices = array();
        $errors = array();
        $event = null;
        $events = array();
        $volunteers = array();
        $orgs = 'None';
        $args = array();
        
        if ( array_key_exists( 'event_id', $_GET )) {
            // the event has been passed-in
            $event = get_post( $_GET['event_id'] );
            if( $event ) {
                // found the event
                $orgs = $vmat_plugin->get_common()->get_event_organizations_string( $event->ID );
                if ( $orgs == 'None' ) {
                    $notice = '<strong>WARNING</strong>: No organizations associated with this event. Volunteer reports will be assigned to Funding Stream "None". <br />';
	                $notice .= 'Click this link to edit the event to add one or more sponsoring organizations ';
	                $notice .= '<a href="' . add_query_arg( array( 'post'=>$event->ID, 'action'=>'edit' ), admin_url('post.php') ) . '">Edit</a>';
                    $notices[] = __($notice, 'vmattd' );;
                }
            }
        }
        $args['orgs'] = $orgs;
        $args['event'] = $event;
        $args['events'] = $events;
        $args['posts_per_page'] = $vmat_plugin->get_common()->var_from_get( 'posts_per_page', get_option( 'posts_per_page' ) );
        if ( ! $event && ( ( 'events-filter' == $form_id ) || '' == $form_id ) ) {
            $args['epno'] = $vmat_plugin->get_common()->var_from_get( 'epno', 1 );
            // processing an events selection table filter form submission
            $args['events_search'] = $vmat_plugin->get_common()->var_from_get( 'events_search', '' );
            $args['post_type'] = 'event';
            $args['scope'] = $vmat_plugin->get_common()->var_from_get( 'scope', 'future' );
            $args['vmat_org'] = $vmat_plugin->get_common()->var_from_get( 'vmat_org', '0' );
            $args['taxonomy'] = $vmat_plugin->get_common()->var_from_get( 'taxonomy', '' );
            $args['tax_field'] = $vmat_plugin->get_common()->var_from_get( 'tax_field', 'term_id' );
            $args['tax_terms'] = explode( ',', $vmat_plugin->get_common()->var_from_get( 'tax_terms', '' ) );
            $submit_button = $vmat_plugin->get_common()->var_from_get( 'submit_button', '' );
            if ( 'filter_events' == $submit_button || 'search_events' == $submit_button ) {
                $args['epno'] = 1;
            }
            $args['events'] = $this->get_events( $args );
        }
        
        if ( $event && ( 'volunteers-filter' == $form_id ) ) {
            $args['vpno'] = $vmat_plugin->get_common()->var_from_get( 'vpno', 1 );
            $args['volunteers_search'] = $vmat_plugin->get_common()->var_from_get( 'volunteers_search', '' );
            // processing a volunteers selection table form submission
            $submit_button = $vmat_plugin->get_common()->var_from_get( 'submit_button', '' );
            if ( 'filter_volunteers' == $submit_button || 'search_volunteers' == $submit_button ) {
                $args['vpno'] = 1;
            }
            $args['volunteers'] = $vmat_plugin->get_common()->get_volunteers_not_added_to_event( $args );
            // get the event volunteers, nopaging=false
            $args['evpno'] = $vmat_plugin->get_common()->var_from_get( 'evpno', 1 );
            $args['event_volunteers_search'] = $vmat_plugin->get_common()->var_from_get( 'event_volunteers_search', '' );
            $args['event_volunteers'] = $vmat_plugin->get_common()->get_volunteers_added_to_event( $args );
        }
        $message = $this->accumulate_messages( $notices, 'notice-warning');
        $message .= $this->accumulate_messages( $errors, 'notice-error');
        $args['notices'] = $message;
        // remove empty query vars
        //$args = array_filter( $args,
        //function( $v, $k ) {
        //    return $v != '';
        //}, ARRAY_FILTER_USE_BOTH);
        return $args;
    }
    
    public function admin_header( $notices='' ) {
        ?>
        <div id="vmat_admin_container" class="wrap container">
        <h1></h1>
        <div class="wrap">
        <div id="vmat_admin_notice"><?php echo $notices; ?></div>
        <?php
    }
    
    public function admin_footer() {
        ?>
        </div>
        </div>
        <?php
    }
    
    public function vmat_admin_dashboard_html() {
        $args = array(
            'notices' => '',            
        );
	    $this->admin_header( $args['notices'] );
	    ?>
  		<!-- content here -->
        <?php
        $this->admin_footer();
    }
    
    public function vmat_admin_volunteers_html() {
        $args = array(
            'notices' => '',
        );
        $this->admin_header( $args['notices'] );
        ?>
  		<!-- content here -->
        <?php
        $this->admin_footer();
    }

    public function vmat_admin_hours_html() {
        /*
         * 
         */
        $args = $this->admin_hours_prep_args();
        $this->admin_header( $args['notices'] );
        ?>
        <div class="row">
        	<div id="vmat_select_event_admin" class="col">
                <?php 
                // display the event selection table or the selected event name and organization
                vmat_get_events_admin( $args );
                ?>
            </div>
        </div>  
        <?php 
    	if ( $args['event'] ) {?>
    		<div class="row vmat-hr">
    		</div>
    	    <div class="row">
        	    <div id="vmat_manage_volunteers_admin" class="col"> <?php 
        	    vmat_manage_volunteers_admin( $args );
        	    ?>
        	    </div>
    	    </div>
    	    <?php 
        }
        $this->admin_footer();
    }

    public function vmat_admin_reports_html() {
        $args = array(
            'notices' => '',
        );
        $this->admin_header( $args['notices'] );
        ?>
  		<!-- content here -->
        <?php
        $this->admin_footer();
    }

    public function vmat_admin_settings_html() {
        $args = array(
            'notices' => '',
        );
        $this->admin_header( $args['notices'] );
        ?>
  		<!-- content here -->
        <?php
        $this->admin_footer();
    }
}
