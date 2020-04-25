<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheeFt and JavaScript.
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
	        'posts_per_page' => get_option( 'vmat_options' )['vmat_posts_per_page'],
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
	
	private function get_events_not_volunteered( $get_args=array() ) {
	    /*
	     * get a list of event posts based on passed-in args
	     */
	    if( ! array_key_exists( 'volunteer', $get_args ) ) {
	        // no volunteer passed in so just return a regular events query
	        $events_query = $this->get_events( $get_args );
	    } else {
	        $volunteered_hours = $get_args['volunteered_hours'];
	        $skip_ids = array_map( function( $hour ) {
	            return get_post_meta( $hour->ID, '_event_id', true );
	        },
	        $volunteered_hours );
	        $get_query = array(
	            'post_type' => $get_args['post_type'],
	            'posts_per_page' => $get_args['posts_per_page'],
	            'paged' => $get_args['epno'],
	            'meta_query' => array(),
	            'post__not_in' => $skip_ids,
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
	        $events_query = new WP_Query( $get_query );
	    }
	    return $events_query;
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
		wp_enqueue_style( $this->plugin_name . '-css-bootstrap', plugin_dir_url( __FILE__ ) . '../common/css/bootstrap.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . 'jquery-ui', 'https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css', array(), $this->version, 'all'  );
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
		wp_enqueue_script( $this->plugin_name . '-js-bootstrap', plugin_dir_url( __FILE__ ) . '../common/js/bootstrap.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( 'jquery-ui-datepicker' );
		// only enqueue ajax scripts where they're needed
		if( 'volunteer-mgmnt_page_vmat_admin_volunteer_participation' == $hook ||
		    'volunteer-mgmnt_page_vmat_admin_volunteers' == $hook ||
		    'volunteer-mgmnt_page_vmat_admin_manage_volunteers' == $hook ) {
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

	}
	
	public function accumulate_messages( $messages=array() ) {
	    $message_display = '';
	    if ( ! empty( $messages ) ) {
	        foreach ( $messages as $message ) {
	            $message_display .= $message . '<br />';
    	    } // accumulate errors
	    }
	    // remove last line break
	    $message = substr_replace( $message, '', -6);
	    return $message_display;
	}
	
	function ajax_add_manage_volunteer_to_event() {
	    global $vmat_plugin;
	    check_ajax_referer( 'vmat_ajax' );
	    $errors = array();
	    if ( ! empty( $_POST['action'] ) ) {
	        $action = $_POST['action'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No action provided in ajax request. Please try again', 'vmattd' );
	    }
	    if ( ! empty( $_POST['notice_id'] ) ) {
	        $notice_id = $_POST['notice_id'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No notice_id provided in ajax request. Please try again', 'vmattd' );
	    }
	    if ( ! empty( $_POST['target'] ) ) {
	        $target = $_POST['target'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No target provided in ajax request. Please try again', 'vmattd' );
	    }
	    if ( ! empty( $_POST['event_id'] ) ) {
	        $event_id = $_POST['event_id'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No event_id provided in ajax request. Please try again', 'vmattd' );
	    }
	    if ( ! empty( $_POST['volunteer_id'] ) ) {
	        $volunteer_id = $_POST['volunteer_id'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No volunteer_id provided in ajax request. Please try again', 'vmattd' );
	    }
	    if ( ! empty( $_POST['display_target'] ) ) {
	        $display_target = $_POST['display_target'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No display_target provided in ajax request. Please try again', 'vmattd' );
	    }
	    if ( empty( $errors ) ) {
	        $volunteer = get_user_by( 'id', $volunteer_id );
	        if ( !$volunteer ) {
	            $errors[] = __('<strong>ERROR</strong>: No volunteer found. Please try again', 'vmattd' );
	        }
	        $event = get_post( $event_id );
	        if ( !$event ) {
	            $errors[] = __('<strong>ERROR</strong>: No event found. Please try again', 'vmattd' );
	        }
            if ( empty( $errors ) ) {
                $event_volunteers = $vmat_plugin->get_common()->get_event_volunteers( array( 'event_id' => $event->ID ) );
                $found_event_volunteer = ! empty( array_filter( $event_volunteers, function( $event_volunteer ) use( $volunteer ) {
                    return $event_volunteer['WP_User']->ID == $volunteer->ID;
                }));
                    if ( $found_event_volunteer ) {
                        $errors[] = __('<strong>ERROR</strong>: Attempted to add ' . $volunteer->display_name . ' to an event, when the volunteer was already added to that event. Try refreshing.', 'vmattd' );
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
                $errors[] = __('<strong>ERROR</strong>: adding  ' . $volunteer->display_name . ' to event.< br/>' . $error_message . '. Please try again', 'vmattd' );
            }
	    }
	    
	    if ( ! empty( $errors ) ) {
	        // ajax request failed
	        $results = array(
	            'ajax_notice' => $this->accumulate_messages( $errors ),
	            'notice_id' => $notice_id,
	        );
	        wp_send_json_error( $results );
	    }
	    $args = array();
	    $args['volunteer']= $volunteer;
	    $args['hours'] = $vmat_plugin->get_common()->get_volunteer_hours( $args );
	    $args['hpno'] = 1;
	    $args['epno'] = 1;
	    $args['posts_per_page'] = get_option( 'vmat_options' )['vmat_posts_per_page'];
	    $args['manage_volunteer_search'] = '';
	    $args['manage_volunteer_events_search'] = '';
	    // generate replacement html for the volunteers and event_volunteers tables
	    $html = $this->ajax_get_manage_volunteer_admin_html( $args );
	    $display_target_html = $vmat_plugin->get_common()->volunteer_display( $volunteer );
	    $message = $event->post_title;
	    $results = array(
	        'notice' => '',
	        'ajax_notice'=> $this->accumulate_messages( array( __( '<strong>SUCCESS</strong>: Added ' . $message , 'vmattd' ) ) ),
	        'notice_id' => $notice_id,
	        'target' => $target,
	        'action' => $action,
	        'html' => $html,
	        'display_target' => $display_target,
	        'display_target_html' => $display_target_html,
	    );
	    // ajax request succeeded
	    wp_send_json_success( $results );
	}
	
	function ajax_add_volunteers_to_event() {
	    global $vmat_plugin;
	    check_ajax_referer( 'vmat_ajax' );
	    $errors = array();
	    if ( ! empty( $_POST['action'] ) ) {
	        $action = $_POST['action'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No action provided in ajax request. Please try again', 'vmattd' );
	    }
	    if ( ! empty( $_POST['notice_id'] ) ) {
	        $notice_id = $_POST['notice_id'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No notice_id provided in ajax request. Please try again', 'vmattd' );
	    }
	    if ( ! empty( $_POST['target'] ) ) {
	        $target = $_POST['target'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No target provided in ajax request. Please try again', 'vmattd' );
	    }
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
	    if ( ! empty( $_POST['display_target'] ) ) {
	        $display_target = $_POST['display_target'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No display_target provided in ajax request. Please try again', 'vmattd' );
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
                        $errors[] = __('<strong>ERROR</strong>: Attempted to add ' . $volunteer->display_name . ' to an event, when the volunteer was already added to that event. Try refreshing.', 'vmattd' );
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
                    $errors[] = __('<strong>ERROR</strong>: adding  ' . $volunteer->display_name . ' to event.< br/>' . $error_message . '. Please try again', 'vmattd' );
                }
            }
	    }
	    
	    if ( ! empty( $errors ) ) {
	        // ajax request failed
	        $results = array(
	            'ajax_notice' => $this->accumulate_messages( $errors ),
	            'notice_id' => $notice_id,
	        );
	        wp_send_json_error( $results );
	    }
	    $args = array();
	    $args['event']= $event;
	    $args['vpno'] = 1;
	    $args['evpno'] = 1;
	    $args['posts_per_page'] = get_option( 'vmat_options' )['vmat_posts_per_page'];
	    $args['volunteers_search'] = '';
	    $args['event_volunteers_search'] = '';
	    $args['volunteers'] = $vmat_plugin->get_common()->get_volunteers_not_added_to_event( $args );;
	    $args['event_volunteers'] = $vmat_plugin->get_common()->get_volunteers_added_to_event( $args );
	    // generate replacement html for the volunteers and event_volunteers tables
	    ob_start();
	    $this->html_part_volunteer_participation_admin( $args );
	    $html = ob_get_clean();
	    $display_target_html = $vmat_plugin->get_common()->event_display( $event );
	    $message = $volunteers[0]->display_name;
	    if ( count( $volunteers ) > 1 ) {
	        $message = count( $volunteers ) . ' Volunteers';
	    }
	    $results = array(
	        'notice' => '',
	        'ajax_notice'=> $this->accumulate_messages( array( __( '<strong>SUCCESS</strong>: Added ' . $message , 'vmattd' ) ) ),
	        'notice_id' => $notice_id,
	        'target' => $target,
	        'action' => $action,
	        'html' => $html,
	        'display_target' => $display_target,
	        'display_target_html' => $display_target_html,
	    );
	    // ajax request succeeded
	    wp_send_json_success( $results );
	}
	
	function ajax_update_volunteer() {
	    global $vmat_plugin;
	    $check = $this->ajax_check_input();
	    $errors = $check['errors'];
	    // filter the errors since we don't need event_id or volunteer
	    $errors = array_filter( $errors, function( $error ) {
	        return strpos( $error, 'event_id' ) == false && 
	               strpos( $error, 'volunteer' ) == false;
	    });
	    
	    $register_notice_id = '';
	    if ( ! empty( $_POST['register_notice_id'] ) ) {
	        $register_notice_id = $_POST['register_notice_id'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No register_notice_id provided in ajax request. Please try again', 'vmattd' );
	    }
	    if ( empty( $errors ) ) {
	        $volunteer_data = $check['volunteer_data']['fields'];
	        $notice_id = $check['notice_id'];
	        $action = $check['action'];
	        $target = $check['target'];
	        $user_fields = array();
	        $user_fields['user_login'] = 'user_login';
	        $user_fields['user_email'] = 'email';
	        $user_fields['first_name'] = 'first_name';
	        $user_fields['last_name'] = 'last_name';
	        // remove empty fields from the registration data
	        $clean_data = array_filter( $volunteer_data, function( $data, $field )  {
	            if( array_key_exists( 'val', $data ) ) {
	                return ! empty ($data['val']);
	            }
	            return false;
	        }, ARRAY_FILTER_USE_BOTH);
	        // register a volunteer 
	        // this provides a mapping between wp and vmat field names
	        // wp_field_name => vmat_field_name
	        $reg_args = array();
	        foreach( $user_fields as $wp_field_name => $vmat_field_name ) {
	            if( array_key_exists( $vmat_field_name, $clean_data ) ) {
	                $reg_args[$wp_field_name] = $clean_data[$vmat_field_name]['val'];
	            }
	        }
	        $reg_args['role'] = 'volunteer';
	        if( ! empty( $volunteer_data['volunteer_id'] ) ) {
	            // update rather than create new
	            $reg_args['ID'] = $volunteer_data['volunteer_id']['val'];
	        }
	        $new_volunteer_id = wp_insert_user( $reg_args );
	        if( is_wp_error( $new_volunteer_id ) ) {
	            foreach( $new_volunteer_id->errors as $error ) {
	                foreach( $error as $error_piece ) {
	                   $errors[] = __('<strong>ERROR</strong>: ' . $error_piece .'. Please try again', 'vmattd' );
	                }
	            }
	        }
	        if( empty( $errors ) ) {
	            $volunteer = get_user_by( 'id', $new_volunteer_id);
	            if( ! $volunteer ) {
	                $errors[] = __('<strong>ERROR</strong>: Problem registering new user. Please try again', 'vmattd' );
	            }
	            if ( empty( $errors ) ) {
    	            // update the user metadata
	                $user_meta_fields = array();
	                foreach( $clean_data as $vmat_field_name => $data ) {
	                    if( ! in_array( $vmat_field_name, $user_fields ) && $vmat_field_name !== 'volunteer_id' ) {
	                        $user_meta_fields[$vmat_field_name] = $clean_data[$vmat_field_name]['val'];
	                    }
	                }
                    foreach( $user_meta_fields as $field => $data ) {
                        $result = update_user_meta($new_volunteer_id, $field, $data);
                        if( is_wp_error( $result ) ) {
                            foreach( $result->errors as $error ) {
                                foreach( $error as $error_piece ) {
                                    $errors[] = __('<strong>ERROR</strong>: ' . $error_piece .'. Please try again', 'vmattd' );
                                }
                            }
                        }
                    }
	            }
	        }
	    }
	    
	    if ( ! empty( $errors ) ) {
	        // ajax request failed
	        $results = array(
	            'ajax_notice' => $this->accumulate_messages( $errors ),
	            'notice_id' => $register_notice_id,
	        );
	        wp_send_json_error( $results );
	    }
	    $html = '';
	    switch( $target ) {
	        case 'vmat_manage_volunteers_table':
	            $args = array();
	            $args['vpno'] = 1;
	            $args['posts_per_page'] = get_option( 'vmat_options' )['vmat_posts_per_page'];
	            $args['manage_volunteers_search'] = '';
	            $html = $this->ajax_get_manage_volunteers_table_html( $args );
	            $update_type = 'Added';
	            break;
	        case 'vmat_manage_volunteer_table':
    	        $args = array();
    	        $args['hpno'] = 1;
    	        $args['posts_per_page'] = get_option( 'vmat_options' )['vmat_posts_per_page'];
    	        $args['manage_hours_search'] = '';
    	        // we are updating so there is a volunteer_id
    	        $args['volunteer'] = get_user_by( 'id', $volunteer_data['volunteer_id']['val'] );
    	        $args['hours'] = $vmat_plugin->get_common()->get_volunteer_hours( $args );
    	        $html = $this->ajax_get_manage_volunteer_table_html( $args );
    	        $update_type = 'Updated';
    	        break;
	        case 'vmat_volunteer_participation_admin':
	            $html = '';
	            $update_type = 'Added';
	        default:
	    }
	    $results = array(
	        'notice' => '',
	        'ajax_notice'=> $this->accumulate_messages( array( __( '<strong>SUCCESS</strong>: ' . $update_type . ' volunteer', 'vmattd' ) ) ),
	        'notice_id' => $notice_id,
	        'target' => $target,
	        'action' => $action,
	        'html' => $html,
	        'volunteer_id' => $volunteer->ID,
	    );
	    // ajax request succeeded
	    wp_send_json_success( $results );
	}
	
	function ajax_check_input() {
	    global $vmat_plugin;
	    check_ajax_referer( 'vmat_ajax' );
	    $return = array();
	    $errors = array();
	    if ( ! empty( $_POST['target'] ) ) {
	        $return['target'] = $_POST['target'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No target provided in ajax request. Please try again', 'vmattd' );
	    }
	    if ( ! empty( $_POST['action'] ) ) {
	        $return['action'] = $_POST['action'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No action provided in ajax request. Please try again', 'vmattd' );
	    }
	    if ( ! empty( $_POST['notice_id'] ) ) {
	        $return['notice_id'] = $_POST['notice_id'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No notice_id provided in ajax request. Please try again', 'vmattd' );
	    }
	    if ( ! empty( $_POST['event_id'] ) ) {
	        $return['event_id'] = $_POST['event_id'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No event_id provided in ajax request. Please try again', 'vmattd' );
	    }
	    if ( ! empty( $_POST['volunteer_data'] ) ) {
	        $return['volunteer_data'] = $_POST['volunteer_data'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No volunteer_data provided in ajax request. Please try again', 'vmattd' );
	    }
	    if ( empty( $errors ) ) {
	        $return['event'] = get_post( $return['event_id'] );
	        if ( !$return['event'] ) {
	            $errors[] = __('<strong>ERROR</strong>: No event found. Please try again', 'vmattd' );
	        }
	        $return['volunteers'] = array();
	        foreach ( $return['volunteer_data'] as $volunteer_id=>$data ) {
	            $volunteer = get_user_by( 'id',  $volunteer_id );
	            if ( !$volunteer ) {
	                $errors[] = __('<strong>ERROR</strong>: No volunteer found. Please try again', 'vmattd' );
	            } else {
	                $return['volunteers'][] = $volunteer;
	            }
	            if ( empty( $errors ) ) {
	                $return['event_volunteers'] = $vmat_plugin->get_common()->get_event_volunteers( array( 'event_id' => $return['event']->ID ) );
	                $found_event_volunteer = ! empty( array_filter( $return['event_volunteers'], function( $event_volunteer ) use( $volunteer ) {
	                    return $event_volunteer['WP_User']->ID == $volunteer->ID;
	                }));
	                    if ( ! $found_event_volunteer ) {
	                        $errors[] = __('<strong>ERROR</strong>: Attempted to remove ' . $volunteer->display_name . ' from an event, when the volunteer was never added to that event. Try refreshing.', 'vmattd' );
	                    }
	            }
	        }
	    }
	    $return['errors'] = $errors;
	    return $return;
	}
	
	
	function ajax_check_remove_volunteers_input() {
	    check_ajax_referer( 'vmat_ajax' );
	    $return = array();
	    $errors = array();
	    if ( ! empty( $_POST['target'] ) ) {
	        $return['target'] = $_POST['target'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No target provided in ajax request. Please try again', 'vmattd' );
	    }
	    if ( ! empty( $_POST['action'] ) ) {
	        $return['action'] = $_POST['action'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No action provided in ajax request. Please try again', 'vmattd' );
	    }
	    if ( ! empty( $_POST['notice_id'] ) ) {
	        $return['notice_id'] = $_POST['notice_id'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No notice_id provided in ajax request. Please try again', 'vmattd' );
	    }
	    if ( ! empty( $_POST['volunteer_ids'] ) ) {
	        $return['volunteer_ids'] = $_POST['volunteer_ids'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No volunteer_ids provided in ajax request. Please try again', 'vmattd' );
	    }
	    if ( empty( $errors ) ) {
	        $return['volunteers'] = array();
	        foreach ( $return['volunteer_ids'] as $volunteer_id ) {
	            $volunteer = get_user_by( 'id',  $volunteer_id );
	            if ( !$volunteer ) {
	                $errors[] = __('<strong>ERROR</strong>: No volunteer found. Please try again', 'vmattd' );
	            } else {
	                $return['volunteers'][] = $volunteer;
	            }
	        }
	    }
	    $return['errors'] = $errors;
	    return $return;
	}
	
	function ajax_check_hours_input() {
	    check_ajax_referer( 'vmat_ajax' );
	    $return = array();
	    $errors = array();
	    if ( ! empty( $_POST['target'] ) ) {
	        $return['target'] = $_POST['target'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No target provided in ajax request. Please try again', 'vmattd' );
	    }
	    if ( ! empty( $_POST['action'] ) ) {
	        $return['action'] = $_POST['action'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No action provided in ajax request. Please try again', 'vmattd' );
	    }
	    if ( ! empty( $_POST['notice_id'] ) ) {
	        $return['notice_id'] = $_POST['notice_id'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No notice_id provided in ajax request. Please try again', 'vmattd' );
	    }
	    if ( ! empty( $_POST['volunteer_id'] ) ) {
	        $return['volunteer_id'] = $_POST['volunteer_id'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No volunteer_id provided in ajax request. Please try again', 'vmattd' );
	    }
	    if ( ! empty( $_POST['volunteer_data'] ) ) {
	        $return['volunteer_data'] = $_POST['volunteer_data'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No volunteer_data provided in ajax request. Please try again', 'vmattd' );
	    }
	    if ( empty( $errors ) ) {
	        $return['volunteer'] = get_user_by( 'id', $return['volunteer_id'] );
	        if ( !$return['volunteer'] ) {
	            $errors[] = __('<strong>ERROR</strong>: No volunteer found. Please try again', 'vmattd' );
	        }
	        $return['hours'] = array();
	        foreach ( $return['volunteer_data'] as $hour_id=>$data ) {
	            $hour = get_post( $hour_id );
	            if ( !$hour ) {
	                $errors[] = __('<strong>ERROR</strong>: No volunteer hour found. Please try again', 'vmattd' );
	            } else {
	                $return['hours'][] = $hour;
	            }
	        }
	    }
	    $return['errors'] = $errors;
	    return $return;
	}
	
	function ajax_get_paginate_vmat_admin_page_data( ) {
	    $errors = array();
	    $result = array();
	    if ( ! empty( $_POST['action'] ) ) {
	        $result['action'] = $_POST['action'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No action provided in ajax request. Please try again', 'vmattd' );
	    }
	    if ( ! empty( $_POST['data'] ) ) {
	        $data = $_POST['data'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No data specified in paginate request. Please try again', 'vmattd' );
	    }
	    if ( empty( $errors ) ) {
	        if ( array_key_exists( 'posts_per_page', $data ) ) {
	            $result['posts_per_page'] = $data['posts_per_page'];
	        } else {
	            $errors[] = __( '<strong>ERROR</strong>: Missing posts_per_page.', 'vmattd' );
	        }
	        if ( ! empty( $data['target'] ) ) {
	            $result['target'] = $data['target'];
	        } else {
	            $errors[] = __('<strong>ERROR</strong>: No target provided in ajax request. Please try again', 'vmattd' );
	        }
	        if ( ! empty( $data['admin_page'] ) ) {
	            $result['admin_page'] = $data['admin_page'];
	        } else {
	            $errors[] = __('<strong>ERROR</strong>: No admin_page specified in paginate request. Please try again', 'vmattd' );
	        }
	        if ( ! empty( $data['notice_id'] ) ) {
	            $result['notice_id'] = $data['notice_id'];
	        } else {
	            $errors[] = __('<strong>ERROR</strong>: No notice_id specified in paginate request. Please try again', 'vmattd' );
	        }
	    }
	    if ( empty( $errors ) ) {
	        switch ( $result['target'] ) {
	            case 'vmat_manage_volunteer_events_table':
	            case 'vmat_events_table':
                    if ( array_key_exists( 'epno', $data ) ) {
                        $result['epno'] = $data['epno'];
                    } else {
                        $errors[] = __( '<strong>ERROR</strong>: Missing epno page indicator.', 'vmattd' );
                    }
                    if ( array_key_exists( 'vmat_org', $data ) ) {
                        $result['vmat_org'] = $data['vmat_org'];
                    } else {
                        $errors[] = __( '<strong>ERROR</strong>: Missing vmat_org filter.', 'vmattd' );
                    }
                    if ( array_key_exists( 'scope', $data ) ) {
                        $result['scope'] = $data['scope'];
                    } else {
                        $errors[] = __( '<strong>ERROR</strong>: Missing scope filter.', 'vmattd' );
                    }
                    if ( array_key_exists( 'events_search', $data ) ) {
                        $result['events_search'] = $data['events_search'];
                    } else {
                        $errors[] = __( '<strong>ERROR</strong>: Missing events_search filter.', 'vmattd' );
                    }
	                break;
	            case 'vmat_manage_volunteers_table':
	                if ( array_key_exists( 'vpno', $data ) ) {
	                    $result['vpno'] = $data['vpno'];
	                } else {
	                    $errors[] = __( '<strong>ERROR</strong>: Missing vpno page indicator.', 'vmattd' );
	                }
	                if ( array_key_exists( 'vmat_org', $data ) ) {
	                    $result['vmat_org'] = $data['vmat_org'];
	                } else {
	                    $errors[] = __( '<strong>ERROR</strong>: Missing vmat_org page indicator.', 'vmattd' );
	                }
	                if ( array_key_exists( 'volunteers_search', $data ) ) {
	                    $result['volunteers_search'] = $data['volunteers_search'];
	                } else {
	                    $errors[] = __( '<strong>ERROR</strong>: Missing volunteers_search filter.', 'vmattd' );
	                }
	                break;
	            case 'vmat_volunteers_table':
	                if ( empty( $data['event_id'] ) ) {
	                    $errors[] = __('<strong>ERROR</strong>: No event_id specified in paginate request. Please try again', 'vmattd' );
	                } else {
	                    $result['event_id'] = absint( $data['event_id'] );
	                    $event = get_post( $result['event_id'] );
	                    if ( !$event ) {
	                        $errors[] = __('<strong>ERROR</strong>: No event found. Please try again', 'vmattd' );
	                    } else {
	                        $result['event'] = $event;
	                        if ( array_key_exists( 'vpno', $data ) ) {
	                            $result['vpno'] = $data['vpno'];
	                        } else {
	                            $errors[] = __( '<strong>ERROR</strong>: Missing vpno page indicator.', 'vmattd' );
	                        }
	                        if ( array_key_exists( 'volunteers_search', $data ) ) {
	                            $result['volunteers_search'] = $data['volunteers_search'];
	                        } else {
	                            $errors[] = __( '<strong>ERROR</strong>: Missing volunteers_search filter.', 'vmattd' );
	                        }
	                    }
	                }
	                break;
	            case 'vmat_event_volunteers_table':
	                if ( empty( $data['event_id'] ) ) {
	                    $errors[] = __('<strong>ERROR</strong>: No event_id specified in paginate request. Please try again', 'vmattd' );
	                } else {
	                    $result['event_id'] = absint( $data['event_id'] );
	                    $event = get_post( $result['event_id'] );
	                    if ( !$event ) {
	                        $errors[] = __('<strong>ERROR</strong>: No event found. Please try again', 'vmattd' );
	                    } else {
	                        $result['event'] = $event;
	                        if ( array_key_exists( 'evpno', $data ) ) {
	                            $result['evpno'] = $data['evpno'];
	                        } else {
	                            $errors[] = __( '<strong>ERROR</strong>: Missing evpno page indicator.', 'vmattd' );
	                        }
	                        if ( array_key_exists( 'event_volunteers_search', $data ) ) {
	                            $result['event_volunteers_search'] = $data['event_volunteers_search'];
	                        } else {
	                            $errors[] = __( '<strong>ERROR</strong>: Missing event_volunteers_search filter.', 'vmattd' );
	                        }
	                    }
	                }
	                break;
	            default:
	        }
	    }
	    $result['errors'] = $errors;
	    return $result;
	}
	
	function ajax_get_events_filter_data( ) {
	    $errors = array();
	    $result = array();
	    $result['epno'] = 1;
	    if ( ! empty( $_POST['notice_id'] ) ) {
	        $result['notice_id'] = $_POST['notice_id'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No notice_id provided in ajax request. Please try again', 'vmattd' );
	    }
	    if ( ! empty( $_POST['target'] ) ) {
	        $result['target'] = $_POST['target'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No target provided in ajax request. Please try again', 'vmattd' );
	    }
	    if ( ! empty( $_POST['action'] ) ) {
	        $result['action'] = $_POST['action'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No action provided in ajax request. Please try again', 'vmattd' );
	    }
	    if ( array_key_exists( 'events_search', $_POST ) ) {
	        $result['events_search'] = $_POST['events_search'];
	    } else {
	        $errors[] = __( '<strong>ERROR</strong>: Missing events_search filter.', 'vmattd' );
	    }
	    if ( array_key_exists( 'vmat_org', $_POST ) ) {
	        $result['vmat_org'] = $_POST['vmat_org'];
	    } else {
	        $errors[] = __( '<strong>ERROR</strong>: Missing vmat_org filter.', 'vmattd' );
	    }
	    if ( array_key_exists( 'scope', $_POST ) ) {
	        $result['scope'] = $_POST['scope'];
	    } else {
	        $errors[] = __( '<strong>ERROR</strong>: Missing scope filter.', 'vmattd' );
	    }
	    $result['posts_per_page'] = get_option( 'vmat_options' )['vmat_posts_per_page'];
	    $result['errors'] = $errors;
	    return $result;
	}
	
	function ajax_get_volunteers_search_data( ) {
	    $errors = array();
	    $result = array();
	    if ( ! empty( $_POST['notice_id'] ) ) {
	        $result['notice_id'] = $_POST['notice_id'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No notice_id provided in ajax request. Please try again', 'vmattd' );
	    }
	    if ( ! empty( $_POST['target'] ) ) {
	        $result['target'] = $_POST['target'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No target provided in ajax request. Please try again', 'vmattd' );
	    }
	    if ( ! empty( $_POST['action'] ) ) {
	        $result['action'] = $_POST['action'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No action provided in ajax request. Please try again', 'vmattd' );
	    }
	    if ( empty( $errors ) ) {
	        switch ( $result['target'] ) {
	            case 'vmat_manage_volunteers_table':
                    if ( array_key_exists( 'volunteers_search', $_POST ) ) {
                        $result['volunteers_search'] = $_POST['volunteers_search'];
                    } else {
                        $errors[] = __( '<strong>ERROR</strong>: Missing volunteers_search filter.', 'vmattd' );
                    }
                    if ( array_key_exists( 'vmat_org', $_POST ) ) {
                        $result['vmat_org'] = $_POST['vmat_org'];
                    } else {
                        $errors[] = __( '<strong>ERROR</strong>: Missing vmat_org filter.', 'vmattd' );
                    }
	                break;
	            case 'vmat_volunteers_table':
	                if ( empty( $_POST['event_id'] ) ) {
	                    $errors[] = __('<strong>ERROR</strong>: No event_id specified in paginate request. Please try again', 'vmattd' );
	                } else {
	                    $result['event_id'] = absint( $_POST['event_id'] );
	                    $event = get_post( $result['event_id'] );
	                    if ( !$event ) {
	                        $errors[] = __('<strong>ERROR</strong>: No event found. Please try again', 'vmattd' );
	                    } else {
	                        $result['event'] = $event;
	                        if ( array_key_exists( 'volunteers_search', $_POST ) ) {
	                            $result['volunteers_search'] = $_POST['volunteers_search'];
	                        } else {
	                            $errors[] = __( '<strong>ERROR</strong>: Missing volunteers_search filter.', 'vmattd' );
	                        }
	                    }
	                }
	                break;
	            case 'vmat_event_volunteers_table':
	                if ( empty( $_POST['event_id'] ) ) {
	                    $errors[] = __('<strong>ERROR</strong>: No event_id specified in paginate request. Please try again', 'vmattd' );
	                } else {
	                    $result['event_id'] = absint( $_POST['event_id'] );
	                    $event = get_post( $result['event_id'] );
	                    if ( !$event ) {
	                        $errors[] = __('<strong>ERROR</strong>: No event found. Please try again', 'vmattd' );
	                    } else {
	                        $result['event'] = $event;
	                        if ( array_key_exists( 'event_volunteers_search', $_POST ) ) {
	                            $result['event_volunteers_search'] = $_POST['event_volunteers_search'];
	                        } else {
	                            $errors[] = __( '<strong>ERROR</strong>: Missing event_volunteers_search filter.', 'vmattd' );
	                        }
	                    }
	                }
	                break;
	            default:
	        }
	    }
	    $result['posts_per_page'] = get_option( 'vmat_options' )['vmat_posts_per_page'];
	    $result['errors'] = $errors;
	    return $result;
	}
	
	function ajax_get_manage_volunteer_search_data( ) {
	    $errors = array();
	    $result = array();
	    if ( ! empty( $_POST['notice_id'] ) ) {
	        $result['notice_id'] = $_POST['notice_id'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No notice_id provided in ajax request. Please try again', 'vmattd' );
	    }
	    if ( ! empty( $_POST['target'] ) ) {
	        $result['target'] = $_POST['target'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No target provided in ajax request. Please try again', 'vmattd' );
	    }
	    if ( ! empty( $_POST['action'] ) ) {
	        $result['action'] = $_POST['action'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No action provided in ajax request. Please try again', 'vmattd' );
	    }
	    if ( array_key_exists( 'manage_volunteer_search', $_POST ) ) {
	        $result['manage_volunteer_search'] = $_POST['manage_volunteer_search'];
	    } else {
	        $errors[] = __( '<strong>ERROR</strong>: Missing manage_volunteer_search filter.', 'vmattd' );
	    }      
	    if ( array_key_exists( 'volunteer_id', $_POST ) ) {
	        $result['volunteer_id'] = $_POST['volunteer_id'];
	    } else {
	        $errors[] = __( '<strong>ERROR</strong>: Missing volunteer_id filter.', 'vmattd' );
	    }
	    if( empty( $errors ) ) {
	        $volunteer = get_user_by( 'id', $result['volunteer_id'] );
	        if ( !$volunteer ) {
	            $errors[] = __('<strong>ERROR</strong>: No volunteer found. Please try again', 'vmattd' );
	        } else {
	            $result['volunteer'] = $volunteer;
	        }
	    }
	    $result['posts_per_page'] = get_option( 'vmat_options' )['vmat_posts_per_page'];
	    $result['errors'] = $errors;
	    return $result;
	}
	
	function ajax_get_manage_volunteers_html( $args=array() ) {
	    global $vmat_plugin;
	    
	    $args['volunteers'] = $vmat_plugin->get_common()->get_volunteers_not_added_to_event( $args );
	    // generate replacement html for the volunteers and event_volunteers tables
	    ob_start();
	    $this->html_part_manage_volunteers_admin( $args );
	    return ob_get_clean();
	}
	
	function ajax_get_event_volunteers_table_html( $args=array() ) {
	    global $vmat_plugin;
	    $args['event_volunteers'] = $vmat_plugin->get_common()->get_volunteers_added_to_event( $args );
	    // generate replacement html for the volunteers and event_volunteers tables
	    ob_start();
	    $this->html_part_volunteer_participation_event_volunteers_table( $args );
	    return ob_get_clean();
	}
	
	function ajax_get_manage_volunteers_table_html( $args=array() ) {
	    global $vmat_plugin;
	    
	    $args['volunteers'] = $vmat_plugin->get_common()->get_volunteers_not_added_to_event( $args );
	    // generate replacement html for the volunteers and event_volunteers tables
	    ob_start();
	    $this->html_part_manage_volunteers_table( $args );
	    return ob_get_clean();
	}
	
	function ajax_get_manage_volunteer_table_html( $args=array() ) {
	    global $vmat_plugin;
	    
	    $args['hours'] = $vmat_plugin->get_common()->get_volunteer_hours( $args );
	    // generate replacement html for the volunteers and event_volunteers tables
	    ob_start();
	    $this->html_part_manage_volunteer_table( $args );
	    return ob_get_clean();
	}
	
	function ajax_get_manage_volunteer_admin_html( $args=array() ) {
	    global $vmat_plugin;
	    
	    $args['hours'] = $vmat_plugin->get_common()->get_volunteer_hours( $args );
	    // generate replacement html for the volunteers and event_volunteers tables
	    ob_start();
	    $this->html_part_manage_volunteer_admin( $args );
	    return ob_get_clean();
	}
	
	function ajax_get_volunteers_table_html( $args=array() ) {
	    global $vmat_plugin;
	    
	    $args['volunteers'] = $vmat_plugin->get_common()->get_volunteers_not_added_to_event( $args );
	    // generate replacement html for the volunteers and event_volunteers tables
	    ob_start();
	    $this->html_part_volunteer_participation_volunteers_table( $args );
	    return ob_get_clean();
	}
	
	function ajax_get_events_table_html( $args=array() ) {
	    $args['post_type'] = EM_POST_TYPE_EVENT;
	    $args['events'] = $this->get_events( $args );
	    
	    // generate replacement html for the volunteers and event_volunteers tables
	    ob_start();
	    $this->html_part_events_table( $args );
	    return ob_get_clean();
	}
	
	function ajax_get_manage_volunteer_events_table_html( $args=array() ) {
	    $args['post_type'] = EM_POST_TYPE_EVENT;
	    $args['events'] = $this->get_events( $args );
	    
	    // generate replacement html for the volunteers and event_volunteers tables
	    ob_start();
	    $this->html_part_manage_volunteer_events_table( $args );
	    return ob_get_clean();
	}
	
	function ajax_remove_volunteers_from_event() {
	    global $vmat_plugin;
	    $check = $this->ajax_check_input();
	    $errors = $check['errors'];
	    if ( ! empty( $_POST['display_target'] ) ) {
	        $display_target = $_POST['display_target'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No display_target provided in ajax request. Please try again', 'vmattd' );
	    }
	    $volunteers = $check['volunteers'];
	    $event = $check['event'];
	    $notice_id = $check['notice_id'];
	    $action = $check['action'];
	    $target = $check['target'];
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
	                        $errors[] = __('<strong>ERROR</strong>: removing  ' . $volunteer->display_name . ' from event.', 'vmattd' );
	                    }
	                }
	            } else {
	                $errors[] = __('<strong>ERROR</strong>: removing  ' . $volunteer->display_name . ' from event (not found).', 'vmattd' );
	            }
	        }
	    }
	    
	    if ( ! empty( $errors ) ) {
	        // ajax request failed
	        $results = array(
	            'ajax_notice' => $this->accumulate_messages( $errors ),
	            'notice_id' => $notice_id,
	            'action' => $action,
	        );
	        wp_send_json_error( $results );
	    }
	    $args = array();
	    $args['event']= $event;
	    $args['vpno'] = 1;
	    $args['evpno'] = 1;
	    $args['posts_per_page'] = get_option( 'vmat_options' )['vmat_posts_per_page'];
	    $args['volunteers_search'] = '';
	    $args['event_volunteers_search'] = '';
	    $args['volunteers'] = $vmat_plugin->get_common()->get_volunteers_not_added_to_event( $args );;
	    $args['event_volunteers'] = $vmat_plugin->get_common()->get_volunteers_added_to_event( $args );
	    ob_start();
	    $this->html_part_volunteer_participation_admin( $args );
	    $html = ob_get_clean();
	    $display_target_html = $vmat_plugin->get_common()->event_display( $event );
	    $message = $volunteers[0]->display_name;
	    if ( count( $volunteers ) > 1 ) {
	        $message = count( $volunteers ) . ' Volunteers';
	    }
	    $results = array(
	        'notice' => '',
	        'ajax_notice'=> $this->accumulate_messages( array( __( '<strong>SUCCESS</strong>: Removed ' . $message , 'vmattd' ) )),
	        'html' => $html,
	        'notice_id' => $notice_id,
	        'action' => $action,
	        'target' => $target,
	        'display_target' => $display_target,
	        'display_target_html' => $display_target_html,
	    );
	    // ajax request succeeded
	    wp_send_json_success( $results );
	}
	
	function ajax_save_event_volunteers_data() {
	    global $vmat_plugin;
	    $check = $this->ajax_check_input();
	    $errors = $check['errors'];
	    if ( ! empty( $_POST['evpno'] ) ) {
	        $check['evpno'] = $_POST['evpno'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No evpno provided in ajax request. Please try again', 'vmattd' );
	    }
	    if ( array_key_exists( 'event_volunteers_search', $_POST ) ) {
	        $check['event_volunteers_search'] = $_POST['event_volunteers_search'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No event_volunteers_search provided in ajax request. Please try again', 'vmattd' );
	    }
	    $volunteers = $check['volunteers'];
	    $volunteer_data = $check['volunteer_data'];
	    $event = $check['event'];
	    $notice_id = $check['notice_id'];
	    $action = $check['action'];
	    $target = $check['target'];
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
	                    $message = $vmat_plugin->get_common()->validate_input( $value );
	                    if ( $message == '' ) {
	                        update_post_meta( $hours_query->post->ID, $key, $value['val'] );
	                    } else {
	                        $errors[] = __('<strong>ERROR</strong>: updating data for  ' . $volunteer->display_name . '. ' . $message, 'vmattd' );
	                    }
	                    
	                }
	            } else {
	                $errors[] = __('<strong>ERROR</strong>: updating data for  ' . $volunteer->display_name . ' (not found).', 'vmattd' );
	            }  
	        }
	    }
	    
	    if ( ! empty( $errors ) ) {
	        // ajax request failed
	        $results = array(
	            'ajax_notice' => $this->accumulate_messages( $errors ),
	            'notice_id' => $notice_id,
	            'action' => $action,
	        );
	        wp_send_json_error( $results );
	    }
	    $args = array();
	    $args['event']= $event;
	    $args['posts_per_page'] = get_option( 'vmat_options' )['vmat_posts_per_page'];
	    $args['evpno'] = $check['evpno'];
	    $args['event_volunteers_search'] = $check['event_volunteers_search'];
	    $html = $this->ajax_get_event_volunteers_table_html( $args );
	    $message = $volunteers[0]->display_name;
	    if ( count( $volunteers ) > 1 ) {
	        $message = count( $volunteers ) . ' Volunteers';
	    }
	    $results = array(
	        'notice' => '',
	        'ajax_notice'=> $this->accumulate_messages( array( __( '<strong>SUCCESS</strong>: Updated ' . $message , 'vmattd' ) ) ),
	        'notice_id' => $notice_id,
	        'action' => $action,
	        'html' => $html,
	        'target' => $target,
	    );
	    // ajax request succeeded
	    wp_send_json_success( $results );
	}
	
	function ajax_save_volunteer_hours_data() {
	    global $vmat_plugin;
	    $check = $this->ajax_check_hours_input();
	    $errors = $check['errors'];
	    if ( ! empty( $_POST['hpno'] ) ) {
	        $check['hpno'] = $_POST['hpno'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No hpno provided in ajax request. Please try again', 'vmattd' );
	    }
	    if ( ! empty( $_POST['display_target'] ) ) {
	        $display_target = $_POST['display_target'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No display_target provided in ajax request. Please try again', 'vmattd' );
	    }
	    $hours = $check['hours'];
	    $volunteer_data = $check['volunteer_data'];
	    $volunteer = $check['volunteer'];
	    $notice_id = $check['notice_id'];
	    $action = $check['action'];
	    $target = $check['target'];
	    if ( empty( $errors ) ) {
	        // Save the meta_data associated with this volounteer hours entry
	        foreach ( $hours as $hour ) {;
                foreach ( $volunteer_data[$hour->ID] as $key=>$value ) {
                    $message = $vmat_plugin->get_common()->validate_input( $value );
                    if ( $message == '' ) {
                        update_post_meta( $hour->ID, $key, $value['val'] );
                    } else {
                        $errors[] = __('<strong>ERROR</strong>: updating hours data for ' . $volunteer->first_name . ' ' . $volunteer->last_name . '. ' . $message, 'vmattd' );
                    }
                    
                }
	        }
	    }
	    
	    if ( ! empty( $errors ) ) {
	        // ajax request failed
	        $results = array(
	            'ajax_notice' => $this->accumulate_messages( $errors ),
	            'notice_id' => $notice_id,
	            'action' => $action,
	        );
	        wp_send_json_error( $results );
	    }
	    $args = array();
	    $args['volunteer']= $volunteer;
	    $args['posts_per_page'] = get_option( 'vmat_options' )['vmat_posts_per_page'];
	    $args['hpno'] = $check['hpno'];
	    $args['manage_hours_search'] = $check['manage_hours_search'];
	    $html = $this->ajax_get_manage_volunteer_table_html( $args );
	    $display_target_html = $vmat_plugin->get_common()->volunteer_display( $volunteer );
	    $message = 'hours for ' . $volunteer->display_name;
	    $results = array(
	        'notice' => '',
	        'ajax_notice'=> $this->accumulate_messages( array( __( '<strong>SUCCESS</strong>: Updated ' . $message , 'vmattd' ) ) ),
	        'notice_id' => $notice_id,
	        'action' => $action,
	        'html' => $html,
	        'target' => $target,
	        'display_target' => $display_target,
	        'display_target_html' => $display_target_html
	    );
	    // ajax request succeeded
	    wp_send_json_success( $results );
	}
	
	function ajax_remove_hours_from_volunteer() {
	    global $vmat_plugin;
	    $check = $this->ajax_check_hours_input();
	    $errors = $check['errors'];
	    if ( ! empty( $_POST['display_target'] ) ) {
	        $display_target = $_POST['display_target'];
	    } else {
	        $errors[] = __('<strong>ERROR</strong>: No display_target provided in ajax request. Please try again', 'vmattd' );
	    }
	    $hours = $check['hours'];
	    $volunteer = $check['volunteer'];
	    $notice_id = $check['notice_id'];
	    $action = $check['action'];
	    $target = $check['target'];
	    if ( empty( $errors ) ) {
	        // Delete vmat_hours posts associated with this event and with post_author == volunteer
	        foreach ( $hours as $hour ) {
                if ( ! wp_delete_post( $hour->ID, true) ) {
                    $errors[] = __('<strong>ERROR</strong>: removing hours from volunteer.', 'vmattd' );
                }
	        }
	    }
	    
	    if ( ! empty( $errors ) ) {
	        // ajax request failed
	        $results = array(
	            'ajax_notice' => $this->accumulate_messages( $errors ),
	            'notice_id' => $notice_id,
	            'action' => $action,
	        );
	        wp_send_json_error( $results );
	    }
	    $args = array();
	    $args['volunteer']= $volunteer;
	    $args['posts_per_page'] = get_option( 'vmat_options' )['vmat_posts_per_page'];
	    $args['hpno'] = 1;
	    $args['epno'] = 1;
	    $args['manage_hours_search'] = '';
	    $args['manage_volunteer_events_search'] = '';
	    $html = $this->ajax_get_manage_volunteer_admin_html( $args );
	    $display_target_html = $vmat_plugin->get_common()->volunteer_display( $volunteer );
	    $message = 'hours for ' . $volunteer->display_name;
	    $results = array(
	        'notice' => '',
	        'ajax_notice'=> $this->accumulate_messages( array( __( '<strong>SUCCESS</strong>: Removed ' . $message , 'vmattd' ) ) ),
	        'notice_id' => $notice_id,
	        'action' => $action,
	        'html' => $html,
	        'target' => $target,
	        'display_target' => $display_target,
	        'display_target_html' => $display_target_html
	    );
	    // ajax request succeeded
	    wp_send_json_success( $results );
	}
	
	function ajax_remove_volunteers() {
	    global $vmat_plugin;
	    $check = $this->ajax_check_remove_volunteers_input();
	    $errors = $check['errors'];
	    $volunteer_ids = $check['volunteer_ids'];
	    $notice_id = $check['notice_id'];
	    $action = $check['action'];
	    $target = $check['target'];
	    if ( empty( $errors ) ) {
	        // Delete vmat_hours posts associated with this event and with post_author == volunteer
	        foreach ( $volunteer_ids as $volunteer_id ) {
	            if ( ! wp_delete_user( $volunteer_id ) ) {
	                $errors[] = __('<strong>ERROR</strong>: removing volunteers.', 'vmattd' );
	            }
	        }
	    }
	    
	    if ( ! empty( $errors ) ) {
	        // ajax request failed
	        $results = array(
	            'ajax_notice' => $this->accumulate_messages( $errors ),
	            'notice_id' => $notice_id,
	            'action' => $action,
	        );
	        wp_send_json_error( $results );
	    }
	    $args = array();
	    $args['volunteers']= $args['volunteers'] = $vmat_plugin->get_common()->get_volunteers_not_added_to_event();
	    $args['posts_per_page'] = get_option( 'vmat_options' )['vmat_posts_per_page'];
	    $args['vpno'] = 1;
	    $args['manage_volunteers_search'] = '';
	    $html = $this->ajax_get_manage_volunteers_table_html( $args );
	    $message = count( $volunteer_ids ) . ' volunteers.';
	    $results = array(
	        'notice' => '',
	        'ajax_notice'=> $this->accumulate_messages( array( __( '<strong>SUCCESS</strong>: Removed ' . $message , 'vmattd' ) ) ),
	        'notice_id' => $notice_id,
	        'action' => $action,
	        'html' => $html,
	        'target' => $target,
	    );
	    // ajax request succeeded
	    wp_send_json_success( $results );
	}
	
	function ajax_set_default_event_volunteers_data() {
	    global $vmat_plugin;
	    $check = $this->ajax_check_input();
	    $errors = $check['errors'];
	    $volunteers = $check['volunteers'];
	    $volunteer_data = $check['volunteer_data'];
	    $event = $check['event'];
	    $notice_id = $check['notice_id'];
	    $action = $check['action'];
	    if ( empty( $errors ) ) {
	        $event_data = $vmat_plugin->get_common()->get_event_data( $event->ID );
	        // Set the meta_data associated with this event and with post_author == volunteer
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
	            if ( ! $hours_query->found_posts ) {
	                $errors[] = __('<strong>ERROR</strong>: updating data for  ' . $volunteer->display_name . ' (not found).', 'vmattd' );
	            }
	        }
	    }
	    
	    if ( ! empty( $errors ) ) {
	        // ajax request failed
	        $results = array(
	            'ajax_notice' => $this->accumulate_messages( $errors ),
	            'notice_id' => $notice_id,
	            'action' => $action,
	        );
	        wp_send_json_error( $results );
	    }
	    $message = $volunteers[0]->display_name;
	    if ( count( $volunteers ) > 1 ) {
	        $message = count( $volunteers ) . ' Volunteers';
	    }
	    $results = array(
	        'notice' => '',
	        'ajax_notice'=> $this->accumulate_messages( array( __( '<strong>SUCCESS</strong>: Set defaults for ' . $message  . '. <strong>Save to keep.</strong>', 'vmattd' ) ) ),
	        'event_data' => $event_data,
	        'volunteer_data' => $volunteer_data,
	        'notice_id' => $notice_id,
	        'action' => $action,
	    );
	    // ajax request succeeded
	    wp_send_json_success( $results );
	}
	
	function ajax_paginate_vmat_admin_page() {
	    check_ajax_referer( 'vmat_ajax' );
	    $args = $this->ajax_get_paginate_vmat_admin_page_data();
	    if ( empty( $args['errors'] ) ) {
            switch ( $args['target'] ) {
                case 'vmat_events_table':
                    $html = $this->ajax_get_events_table_html( $args );
                    break;
                case 'vmat_manage_volunteer_events_table':
                    $html = $this->ajax_get_manage_volunteer_events_table_html( $args );
                    break;
                case 'vmat_volunteers_table':
                    $html = $this->ajax_get_volunteers_table_html( $args );
                    break;
                case 'vmat_event_volunteers_table':
                    $html = $this->ajax_get_event_volunteers_table_html( $args );
                    break;
                case 'vmat_manage_volunteers_table':
                    $html = $this->ajax_get_manage_volunteers_table_html( $args );
                    break;
                default:
                    // default action
            }
        }
	    if ( ! empty( $args['errors'] ) ) {
	        // ajax request failed
	        $results = array(
	            'ajax_notice' => $this->accumulate_messages( $args['errors'] ),
	            'notice_id' => $args['notice_id'],
	            'action' => $args['action'],
	        );
	        wp_send_json_error( $results );
	    }
	    $results = array(
	        'target' => $args['target'],
	        'html' => $html,
	        'action' => $args['action'],
	    );
	    // ajax request succeeded
	    wp_send_json_success( $results );
	}
	
	public function ajax_filter_events() {
	    check_ajax_referer( 'vmat_ajax' );
	    $args = $this->ajax_get_events_filter_data();
	    if ( empty( $args['errors'] ) ) {
	        $html = $this->ajax_get_events_table_html( $args );
	    }
	    if ( ! empty( $args['errors'] ) ) {
	        // ajax request failed
	        $results = array(
	            'ajax_notice' => $this->accumulate_messages( $args['errors'] ),
	            'action' => $args['action'],
	            'notice_id' => $args['notice_id'],
	        );
	        wp_send_json_error( $results );
	    }
	    $results = array(
	        'target' => $args['target'],
	        'html' => $html,
	        'action' => $args['action'],
	        'ajax_notice'=> $this->accumulate_messages( array( __( '<strong>SUCCESS</strong>: Filtered events', 'vmattd' ) ) ),
	        'notice_id' => $args['notice_id'],
	        'target' => $args['target'],
	    );
	    // ajax request succeeded
	    wp_send_json_success( $results );
	}
	
	public function ajax_filter_manage_volunteer_events() {
	    check_ajax_referer( 'vmat_ajax' );
	    $args = $this->ajax_get_events_filter_data();
	    if ( empty( $args['errors'] ) ) {
	        $html = $this->ajax_get_manage_volunteer_events_table_html( $args );
	    }
	    if ( ! empty( $args['errors'] ) ) {
	        // ajax request failed
	        $results = array(
	            'ajax_notice' => $this->accumulate_messages( $args['errors'] ),
	            'action' => $args['action'],
	            'notice_id' => $args['notice_id'],
	        );
	        wp_send_json_error( $results );
	    }
	    $results = array(
	        'target' => $args['target'],
	        'html' => $html,
	        'action' => $args['action'],
	        'ajax_notice'=> $this->accumulate_messages( array( __( '<strong>SUCCESS</strong>: Filtered events', 'vmattd' ) ) ),
	        'notice_id' => $args['notice_id'],
	        'target' => $args['target'],
	    );
	    // ajax request succeeded
	    wp_send_json_success( $results );
	}
	
	public function ajax_search_volunteers() {
	    check_ajax_referer( 'vmat_ajax' );
	    $args = $this->ajax_get_volunteers_search_data();
	    $args['vpno'] = 1;
	    if ( empty( $args['errors'] ) ) {
	        $html = $this->ajax_get_volunteers_table_html( $args );
	    }
	    if ( ! empty( $args['errors'] ) ) {
	        // ajax request failed
	        $results = array(
	            'ajax_notice' => $this->accumulate_messages( $args['errors'] ),
	            'action' => $args['action'],
	            'notice_id' => $args['notice_id'],
	        );
	        wp_send_json_error( $results );
	    }
	    $results = array(
	        'target' => $args['target'],
	        'html' => $html,
	        'action' => $args['action'],
	        'ajax_notice'=> $this->accumulate_messages( array( __( '<strong>SUCCESS</strong>: Filtered volunteers', 'vmattd' ) ) ),
	        'notice_id' => $args['notice_id'],
	        'target' => $args['target'],
	    );
	    // ajax request succeeded
	    wp_send_json_success( $results );
	}
	
	public function ajax_filter_manage_volunteers() {
	    check_ajax_referer( 'vmat_ajax' );
	    $args = $this->ajax_get_volunteers_search_data();
	    $args['vpno'] = 1;
	    if ( empty( $args['errors'] ) ) {
	        $html = $this->ajax_get_manage_volunteers_table_html( $args );
	    }
	    if ( ! empty( $args['errors'] ) ) {
	        // ajax request failed
	        $results = array(
	            'ajax_notice' => $this->accumulate_messages( $args['errors'] ),
	            'action' => $args['action'],
	            'notice_id' => $args['notice_id'],
	        );
	        wp_send_json_error( $results );
	    }
	    $results = array(
	        'target' => $args['target'],
	        'html' => $html,
	        'action' => $args['action'],
	        'ajax_notice'=> $this->accumulate_messages( array( __( '<strong>SUCCESS</strong>: Filtered volunteers', 'vmattd' ) ) ),
	        'notice_id' => $args['notice_id'],
	        'target' => $args['target'],
	    );
	    // ajax request succeeded
	    wp_send_json_success( $results );
	}
	
	public function ajax_search_manage_volunteer() {
	    check_ajax_referer( 'vmat_ajax' );
	    $args = $this->ajax_get_manage_volunteer_search_data();
	    $args['hpno'] = 1;
	    if ( empty( $args['errors'] ) ) {
	        $html = $this->ajax_get_manage_volunteer_table_html( $args );
	    }
	    if ( ! empty( $args['errors'] ) ) {
	        // ajax request failed
	        $results = array(
	            'ajax_notice' => $this->accumulate_messages( $args['errors'] ),
	            'action' => $args['action'],
	            'notice_id' => $args['notice_id'],
	        );
	        wp_send_json_error( $results );
	    }
	    $results = array(
	        'target' => $args['target'],
	        'html' => $html,
	        'action' => $args['action'],
	        'ajax_notice'=> $this->accumulate_messages( array( __( '<strong>SUCCESS</strong>: Filtered volunteers', 'vmattd' ) ) ),
	        'notice_id' => $args['notice_id'],
	        'target' => $args['target'],
	    );
	    // ajax request succeeded
	    wp_send_json_success( $results );
	}
	
	public function ajax_search_event_volunteers() {
	    check_ajax_referer( 'vmat_ajax' );
	    $args = $this->ajax_get_volunteers_search_data();
	    $args['evpno'] = 1;
	    if ( empty( $args['errors'] ) ) {
	        $html = $this->ajax_get_event_volunteers_table_html( $args );
	    }
	    if ( ! empty( $args['errors'] ) ) {
	        // ajax request failed
	        $results = array(
	            'ajax_notice' => $this->accumulate_messages( $args['errors'] ),
	            'action' => $args['action'],
	            'notice_id' => $args['notice_id'],
	        );
	        wp_send_json_error( $results );
	    }
	    $results = array(
	        'target' => $args['target'],
	        'html' => $html,
	        'action' => $args['action'],
	        'ajax_notice'=> $this->accumulate_messages( array( __( '<strong>SUCCESS</strong>: Filtered event volunteers', 'vmattd' ) ) ),
	        'notice_id' => $args['notice_id'],
	        'target' => $args['target'],
	    );
	    // ajax request succeeded
	    wp_send_json_success( $results );
	}
	
	public function admin_main_page() { 
	    add_menu_page(
	        '',
	        'Volunteer Mgmnt',
	        'edit_posts',
	        'vmat_admin_main',
	        array($this, 'html_page_admin_volunteer_participation'),
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
	
	public function admin_manage_volunteers_page() {
	    add_submenu_page(
	        'vmat_admin_main',
	        'Volunteer Management and Tracking - Manage Volunteers',
	        'Manage Volunteers',
	        'edit_users',
	        'vmat_admin_manage_volunteers',
	        array($this, 'html_page_admin_manage_volunteers')
	        );
	}
	
	public function admin_volunteer_participation_page() {
	    add_submenu_page(
	        'vmat_admin_main',
	        'Volunteer Management and Tracking - Volunteer Participation',
	        'Volunteer Participation',
	        'edit_vmat_hours',
	        'vmat_admin_volunteer_participation',
	        array($this, 'html_page_admin_volunteer_participation')
	        );
	}
	
	public function admin_reports_page() {
	    add_submenu_page(
	        'vmat_admin_main',
	        'Volunteer Management and Tracking - Reports',
	        'Reports',
	        'publish_vmat_hours',
	        'vmat_admin_reports',
	        array($this, 'html_page_admin_reports')
	        );
	}
	
	public function admin_settings_page() {
	    add_submenu_page(
	        'vmat_admin_main',
	        'Volunteer Management and Tracking - Settings',
	        'Settings',
	        'manage_options',
	        'vmat_admin_settings',
	        array($this, 'html_page_admin_settings')
	        );
	}
	
	public function admin_help_page() {
	    add_submenu_page(
	        'vmat_admin_main',
	        'Volunteer Management and Tracking - Help',
	        'Help',
	        'manage_options',
	        'vmat_admin_help',
	        array($this, 'html_page_admin_help')
	        );
	}
		
	public function modify_event_list_row_actions( $actions, $post ) {
	    /*
	     * Add custom actions to the quick links that appear in the Events list
	     *     Add Hours - go to VMAT Hours page and add volunteer hours to this event
	     */
	    if( current_user_can( 'edit_vmat_hours' ) ) {
	        if ( $post->post_type == EM_POST_TYPE_EVENT || $post->post_type == 'event-recurring' ) {
	            
	            // Build your links URL.
	            $add_link = admin_url( 'admin.php' );
	            $query_vars = array(
	                'page' => 'vmat_admin_volunteer_participation',
	                'event_id' => $post->ID,
	            );
	            $add_link = add_query_arg( $query_vars, $add_link );
	            
	            
	            // You can check if the current user has some custom rights.
	            // Include a nonce in this link
	            $add_link = wp_nonce_url( add_query_arg( array( 'action' => 'add_hours' ), $add_link ), 'add_event_hours_nonce' );
	            
	            // Add the new quick link.
	            $actions = array_merge( $actions, array(
	                'add' => sprintf( '<a href="%1$s" title="Manage volunteer participatio for event"><span class="vmat-quick-link">%2$s</span></a>',
	                    esc_url( $add_link ),
	                    __( 'Manage Volunteer Participation', 'vmattd' )
	                    )
	            )
	                );
	            
	        }
	    }
	    
	    return $actions;
	}
	
	public function modify_user_list_row_actions( $actions, $user ) {
	    /*
	     * Add custom actions to the quick links that appear in the Events list
	     *     Add Hours - go to VMAT Hours page and add volunteer hours to this event
	     */
	    if( current_user_can( 'edit_users' ) ) {
            // Build your links URL.
            $add_link = admin_url( 'users.php' );
            
            // You can check if the current user has some custom rights.
            // Include a nonce in this link
            $add_link = wp_nonce_url( add_query_arg( 
                array( 
                    'action' => 'add_volunteer_role',
                    'volunteer_id' => $user->ID,
                ), 
                $add_link ), 
                'add_volunteer_role_nonce' );
            // Add the new quick link.
            $actions = array_merge( $actions, array(
                'add' => sprintf( '<a href="%1$s" title="Add volunteer role"><span class="vmat-quick-link">%2$s</span></a>',
                    esc_url( $add_link ),
                    __( 'Add Volunteer Role', 'vmattd' )
                    )
            )
                );
	   }
	   return $actions;
	}
	
	public function add_volunteer_role_action( ) {
	    $screen = get_current_screen();
	    if( 'users' ==  $screen->id ) {
	        $action = '';
	        if( array_key_exists( 'action', $_GET ) ) {
	            $action = $_GET['action'];
	        }
	        if( 'add_volunteer_role' == $action  ) {
                $nonce = '';
                if( array_key_exists( '_wpnonce', $_GET ) ) {
                    $nonce = $_GET['_wpnonce'];
                }
                if( wp_verify_nonce( $nonce, 'add_volunteer_role_nonce' ) &&
                    current_user_can( 'edit_users' ) ) {
                    $volunteer_id = '';
                    if( array_key_exists( 'volunteer_id', $_GET ) ) {
                        $volunteer_id = absint( $_GET['volunteer_id'] );
                    }
                    if ( $volunteer_id ) {
                        $user = get_user_by( 'id', $volunteer_id );
                        if( false !== $user ) {
                            $user->add_role( 'volunteer' );
                        }
                    }
                }
	        }
	    }
	}
	
	public function register_bulk_add_volunteer_role( $bulk_actions) {
	    $bulk_actions['add_volunteer_role'] = __( 'Add Volunteer Role', 'add_volunteer_role');
	    return $bulk_actions;
	}
	
	public function add_volunteer_role_handler( $redirect_to, $doaction, $user_ids ) {
	    $changed = 0;
	    if( $doaction === 'add_volunteer_role' && current_user_can( 'edit_users' ) ) {
	       foreach ( $user_ids as $user_id ) {
	            $wpuser = get_user_by('id', $user_id);
	            if ( $wpuser ) {
                    if( ! in_array('volunteer', $wpuser->roles)) {
                        $wpuser->add_role('volunteer');
                        $changed = $changed + 1;
                    }
	            }
	       }
	       $redirect_to = add_query_arg( 'bulk_added_volunteer_role', $changed, $redirect_to );
	    }
	    return $redirect_to;
	}
	
	public function bulk_add_volunteer_role_admin_notice() {
	    if ( ! empty( $_REQUEST['bulk_added_volunteer_role'] ) ) {
	        $volunteer_role_added_count = intval( $_REQUEST['bulk_added_volunteer_role'] );
	        ?>
	        <div class="notice notice-success is-dismissible">
	        <p>
	        <?php 
	        _e( 'Added Volunteer role to ' . $volunteer_role_added_count . ' Users.', 'vmattd');
	        ?>
	        </p>
	   	    </div>
	   	    <?php 
	    }
	}
	
	public function post_remove_volunteer_action( $user_id ) {
	   // remove vmat_hours posts authored by this user when deleting user
	   $args = array(
	       'nopaging' => true,
	       'post_type' => 'vmat_hours',
	       'author' => $user_id,
	       
	   );
	   $vmat_hours_query = new WP_Query( $args );
	   foreach( $vmat_hours_query->posts as $vmat_hour ) {
	       wp_delete_post( $vmat_hour->ID );
	   }
	}
	
	public function add_volunteer_role_to_new_em_user( $user_id ) {
	    if( array_key_exists( 'action', $_POST ) && 
	        $_POST['action'] == 'booking_add' &&
	        array_key_exists( '_wpnonce', $_POST ) &&
	        wp_verify_nonce( $_POST['_wpnonce'], 'booking_add' ) ) {
	       // add volunteer role to user created from EM Booking
            $wpuser = get_user_by('id', $user_id);
            if ( $wpuser ) {
                if( ! in_array('volunteer', $wpuser->roles)) {
                    $wpuser->add_role('volunteer');
                }
                if( array_key_exists( 'dbem_phone', $_POST ) &&
                    ! empty( $_POST['dbem_phone'] ) ) {
                        update_user_meta( $wpuser->ID, 'vmat_phone_cell', $_POST['dbem_phone'] );
                    }
            }
	    }
	}
	
	public function add_em_org_meta_boxes(){
	    add_meta_box('em-event-orgs', __('Organizations', 'vmattd'), array( $this, 'organizations_meta_box'), EM_POST_TYPE_EVENT, 'side','high');
	    add_meta_box('em-event-orgs', __('Organizations', 'vmattd'), array( $this, 'organizations_meta_box'),'event-recurring', 'side','high');
	}
	
	public function add_org_funding_stream_meta_box(){
	    add_meta_box('organization-funding-streams', __('Funding Streams', 'vmattd'), array( $this, 'funding_streams_meta_box'), 'vmat_organization', 'side','low');
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
	
	public function funding_stream_fields_meta_box( $funding_stream ) {
	    global $vmat_plugin;
	    $user_funding_start_date = '';
	    $user_funding_end_date = '';
	    $iso_funding_start_date = '';
	    $iso_funding_end_date = '';
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
	        $funding_stream_data = $vmat_plugin->get_common()->get_funding_stream_data( $funding_stream->ID );
	        $user_funding_start_date = $funding_stream_data['start_date']; // mm/dd/yyyy
	        $iso_funding_start_date = $funding_stream_data['iso_start_date']; // yyyy-mm-dd
	        $user_funding_end_date = $funding_stream_data['end_date']; // mm/dd/yyyy
	        $iso_funding_end_date = $funding_stream_data['iso_end_date']; // yyyy-mm-dd
	        $fiscal_start_months = get_post_meta( $funding_stream->ID, '_fiscal_start_months', true );
	    }
	    ?>
    	<div class="row">
    		<div class="col-2 vmat-form-label">
        	<label for="funding_start_date">
        		Funding Start Date: <span class="vmat-quick-link">(mm/dd/yyyy)</span>
        	</label>
        	</div>
        	<div class="col vmat-form-field">
        		<input id="datepicker_start" 
        		       type="text" 
        		       name="user_funding_start_date" 
        		       value="<?php echo $user_funding_start_date; ?>" 
        		       autocomplete="off">
        		<input id="vmat_funding_start_date" 
        		       type="hidden" 
        		       name="funding_start_date" 
        		       value="<?php echo $iso_funding_start_date; ?>" 
        		       autocomplete="off">
        	</div>
    	</div>
    	<div class="row">
    		<div class="col-2 vmat-form-label">
        	<label for="funding_end_date">
        		Funding End Date: <span class="vmat-quick-link">(mm/dd/yyyy)</span>
        	</label>
        	</div>
        	<div class="col vmat-form-field">
        		<div class="col vmat-form-field">
        		<input id="datepicker_end" 
        		       type="text" 
        		       name="user_funding_end_date" 
        		       value="<?php echo $user_funding_end_date; ?>" 
        		       autocomplete="off">
        		<input id="vmat_funding_end_date" 
        		       type="hidden" 
        		       name="funding_end_date" 
        		       value="<?php echo $iso_funding_end_date; ?>" 
        		       autocomplete="off">
        	</div>
        	</div>
    	</div>
    	<div class="row">
    		<div class="col-2 vmat-form-label">
        	<label for="fiscal_start_months">
        		Fiscal Start Months:
        	</label>
        	</div>
        	<div class="col vmat-form-field">
            	<fieldset id="fiscal_start_months" >
            		<p><?php _e('Choose the starting month for each fiscal period', 'vmattd')?></p>
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
                </fieldset>
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
	    if ( ! current_user_can( 'edit_vmat_organizations' ) ) {
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

	public function update_funding_stream_fields_meta( $funding_id ) {
	    /*
	     * Updae the funding stream meta data 
	     */
	    global $vmat_plugin;
	    if ( ! current_user_can( 'edit_vmat_funding_streams' ) ) {
	        return false;
	    }
	    if ( array_key_exists(
	        'funding_start_date', $_POST ) &&
	        $vmat_plugin->get_common()->validate_date( $_POST['funding_start_date'], 'Y-m-d' )
	        ) {
	            $funding_start_date = sanitize_text_field( $_POST['funding_start_date'] );
	            update_post_meta( $funding_id, '_funding_start_date', $funding_start_date );
	    } else {
	        delete_post_meta( $funding_id , '_funding_start_date' );
	    } 
	    if ( array_key_exists( 
	        'funding_end_date', $_POST ) &&
	        $vmat_plugin->get_common()->validate_date( $_POST['funding_end_date'], 'Y-m-d' )
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
    
    function add_start_end_dates_column_to_funding_streams( $columns ) {
        $post_type = get_post_type();
        if ( $post_type == 'vmat_funding_stream' &&
            ! empty( $columns['cb'] ) &&
            ! empty( $columns['title'] ) &&
            ! empty( $columns['date'] )
            ) {
            $new_columns = array();
            $new_columns['cb'] = $columns['cb'];
            $new_columns['title'] = $columns['title'];
            $new_columns['begin_end_dates'] = __( 'Begin/End Dates', 'vmattd' );
            $new_columns['date'] = $columns['date'];
            return $new_columns;
        }
        return $columns;
    }
    
    public function fill_start_end_dates_funding_streams_column( $column_name, $funding_stream_id ) {
        global $vmat_plugin;
        $post_type = get_post_type();
        if ( ( $post_type == 'vmat_funding_stream' ) &&
            $column_name == 'begin_end_dates' ) {
                $funding_stream_data = $vmat_plugin->get_common()->get_funding_stream_data( $funding_stream_id );
                $field_data = $funding_stream_data['start_end_string'];
                _e( $field_data );
            }
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
    
    public function settings_init() {
        // register a new setting for "vmat" page
        register_setting( 'vmat', 'vmat_options' );
        
        // register a new section in the "vmat_admin_settings" page
        add_settings_section(
        'vmat_section_view',
            '<h4>' . __( 'View Settings.', 'vmat' ) . '</h4>',
            array( 
                $this, 'section_view_cb' 
	        ),
            'vmat_admin_settings'
	     );
        // register a new section in the "vmat_admin_settings" page
        add_settings_section(
            'vmat_section_email',
            '<h4>' . __( 'Email Settings.', 'vmat' ) . '</h4>',
            array( 
                $this, 
                'section_email_cb' 
	        ),
            'vmat_admin_settings'
	    );
        
        // register a new field in the "section_view" section, inside the "admin_vmat_settings" page
        add_settings_field(
            'vmat_posts_per_page', // as of WP 4.6 this value is used only internally
            // use $args' label_for to populate the id inside the callback
            __( 'Posts Per Page', 'vmattd' ),
            array( 
                $this, 
                'vmat_posts_per_page_cb' ),
            'vmat_admin_settings',
            'vmat_section_view', 
            array(
                'label_for' => 'vmat_posts_per_page',
                'class' => 'vmat_row',
                'custom_data' => 'custom',
            )
        );
        // register a new field in the "section_view" section, inside the "admin_vmat_settings" page
        add_settings_field(
            'vmat_new_user_notification_email', // as of WP 4.6 this value is used only internally
            // use $args' label_for to populate the id inside the callback
            __( 'Send To: Email When New Users Are Created in the Frontend', 'vmattd' ),
            array( 
                $this, 
                'vmat_new_user_notification_email_cb' 
    	    ),
            'vmat_admin_settings',
            'vmat_section_email',
            array(
                'label_for' => 'vmat_new_user_notification_email',
                'class' => 'vmat_row',
                'custom_data' => 'custom',
            )
        );
    }
    
    // view section cb
    
    // section callbacks can accept an $args parameter, which is an array.
    // $args have the following keys defined: title, id, callback.
    // the values are defined at the add_settings_section() function.
    public function section_view_cb( $args ) {
        ?>
         <p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Settings for Admin Views.', 'vmattd' ); ?></p>
         <?php
    }
    
    public function section_email_cb( $args ) {
        ?>
         <p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Settings for Email Notificationss.', 'vmattd' ); ?></p>
         <?php
    }
    
    // posts_per_page field cb
    
    // field callbacks can accept an $args parameter, which is an array.
    // $args is defined at the add_settings_field() function.
    // wordpress has magic interaction with the following keys: label_for, class.
    // the "label_for" key value is used for the "for" attribute of the <label>.
    // the "class" key value is used for the "class" attribute of the <tr> containing the field.
    // you can add custom key value pairs to be used inside your callbacks.
    function vmat_posts_per_page_cb( $args ) {
        // get the value of the setting we've registered with register_setting()
        $options = get_option( 'vmat_options' );
        // output the field
        ?>
         <input type="number" 
         id="<?php echo esc_attr( $args['label_for'] ); ?>"
         data-custom="<?php echo esc_attr( $args['custom_data'] ); ?>"
         name="vmat_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
         value="<?php 
         echo isset( $options[ $args['label_for'] ] ) ? $options[ $args['label_for'] ]: 6;
         ?>"
         >
         <p class="description">
         <?php esc_html_e( 'Set the number of items that show up on each page of table views', 'vmattd' ); ?>
         </p>
         <?php
    }
    
    function vmat_new_user_notification_email_cb( $args ) {
        // get the value of the setting we've registered with register_setting()
        $options = get_option( 'vmat_options' );
        // output the field
        ?>
         <input type="email" 
         id="<?php echo esc_attr( $args['label_for'] ); ?>"
         data-custom="<?php echo esc_attr( $args['custom_data'] ); ?>"
         name="vmat_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
         value="<?php 
         echo isset( $options[ $args['label_for'] ] ) ? $options[ $args['label_for'] ]: '';
         ?>"
         >
         <p class="description">
         <?php esc_html_e( 'Set the email address that gets notification emails when new users are created', 'vmattd' ); ?>
         </p>
         <?php
    }
    
    public function admin_volunteer_participation_prep_args() {
        global $vmat_plugin;
        $warnings = array();
        $infos = array();
        $errors = array();
        $event = null;
        $events = array();
        $orgs = 'None';
        $args = array();
        
        if ( array_key_exists( 'event_id', $_GET )) {
            // the event has been passed-in
            $event = get_post( $_GET['event_id'] );
            if( $event ) {
                // found the event
                $orgs = $vmat_plugin->get_common()->get_event_organizations_string( $event->ID );
                if ( $orgs == 'None' ) {
                    $warning = '<strong>WARNING</strong>: No organizations associated with this event. Volunteer reports will be assigned to Funding Stream "None". <br />';
                    $warning .= 'Click this link to edit the event and add one or more sponsoring organizations if desired. ';
                    $warning .= '<a href="' . add_query_arg( array( 'post'=>$event->ID, 'action'=>'edit' ), admin_url('post.php') ) . '">Edit</a>';
                    $warnings[] = __($warning, 'vmattd' );;
                }
            }
        }
        $args['orgs'] = $orgs;
        $args['event'] = $event;
        $args['events'] = $events;
        $args['posts_per_page'] = $vmat_plugin->get_common()->var_from_get( 'posts_per_page', get_option( 'vmat_options' )['vmat_posts_per_page'] );
        if ( ! $event ) {
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
        } else {
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
        $message = $this->accumulate_messages( $infos );
        $message = $this->accumulate_messages( $warnings );
        $message .= $this->accumulate_messages( $errors );
        if ( $errors ) {
            $message_class = 'vmat-notice-error';
        } elseif ( $warnings ) {
            $message_class = 'vmat-notice-warning';
        } elseif ( $infos ) {
            $message_class = 'vmat-notice-info';
        } else {
            $message_class = '';
        }
        $args['message'] = $message;
        $args['message_class'] = $message_class;
        return $args;
    }
    
    public function admin_manage_volunteers_prep_args() {
        global $vmat_plugin;
        $warnings = array();
        $infos = array();
        $errors = array();
        $args = array();
        $args['volunteer'] = null;
        if ( array_key_exists( 'volunteer_id', $_GET )) {
            // the volunteer has been passed-in
            $args['volunteer'] = get_user_by( 'id',  $_GET['volunteer_id'] );
        }
        $args['posts_per_page'] = $vmat_plugin->get_common()->var_from_get( 'posts_per_page', get_option( 'vmat_options' )['vmat_posts_per_page'] );
        $args['edit_volunteer'] = false;
        if( array_key_exists( 'edit_volunteer', $_GET ) ) {
            $args['edit_volunteer'] = true;
        }
        $args['vpno'] = $vmat_plugin->get_common()->var_from_get( 'vpno', 1 );
        $args['volunteers_search'] = $vmat_plugin->get_common()->var_from_get( 'volunteers_search', '' );
        // processing a volunteers selection table form submission
        $submit_button = $vmat_plugin->get_common()->var_from_get( 'submit_button', '' );
        if ( 'filter_volunteers' == $submit_button || 'search_volunteers' == $submit_button ) {
            $args['vpno'] = 1;
        }
        $args['volunteers'] = $vmat_plugin->get_common()->get_volunteers_not_added_to_event( $args );
        $message = $this->accumulate_messages( $infos );
        $message = $this->accumulate_messages( $warnings );
        $message .= $this->accumulate_messages( $errors );
        if ( $errors ) {
            $message_class = 'vmat-notice-error';
        } elseif ( $warnings ) {
            $message_class = 'vmat-notice-warning';
        } elseif ( $infos ) {
            $message_class = 'vmat-notice-info';
        } else {
            $message_class = '';
        }
        $args['message'] = $message;
        $args['message_class'] = $message_class;
        return $args;
    }
    
    public function admin_volunteers_prep_args() {
        global $vmat_plugin;
        $warnings = array();
        $infos = array();
        $errors = array();
        $args = array();
        $args['vpno'] = $vmat_plugin->get_common()->var_from_get( 'vpno', 1 );
        $args['volunteers_search'] = $vmat_plugin->get_common()->var_from_get( 'volunteers_search', '' );
        // processing a volunteers selection table form submission
        $submit_button = $vmat_plugin->get_common()->var_from_get( 'submit_button', '' );
        if ( 'filter_volunteers' == $submit_button || 'search_volunteers' == $submit_button ) {
            $args['vpno'] = 1;
        }
        $args['volunteers'] = $vmat_plugin->get_common()->get_volunteers_not_added_to_event( $args );
        $message = $this->accumulate_messages( $infos );
        $message = $this->accumulate_messages( $warnings );
        $message .= $this->accumulate_messages( $errors );
        if ( $errors ) {
            $message_class = 'vmat-notice-error';
        } elseif ( $warnings ) {
            $message_class = 'vmat-notice-warning';
        } elseif ( $infos ) {
            $message_class = 'vmat-notice-info';
        } else {
            $message_class = '';
        }
        $args['message'] = $message;
        $args['message_class'] = $message_class;
        return $args;
    }
    
    public function admin_manage_volunteer_prep_args() {
        global $vmat_plugin;
        $warnings = array();
        $infos = array();
        $errors = array();
        $args = array();
        $args['volunteer'] = null;
        if ( array_key_exists( 'volunteer_id', $_GET )) {
            // the volunteer has been passed-in
            $args['volunteer'] = get_user_by( 'id',  $_GET['volunteer_id'] );
        }
        $args['posts_per_page'] = $vmat_plugin->get_common()->var_from_get( 'posts_per_page', get_option( 'vmat_options' )['vmat_posts_per_page'] );
        $args['edit_volunteer'] = false;
        if( array_key_exists( 'edit_volunteer', $_GET ) ) {
            $args['edit_volunteer'] = true;
        }
        // get a single volunteer management table
        $args['hpno'] = $vmat_plugin->get_common()->var_from_get( 'vpno', 1 );
        $args['manage_volunteer_search'] = $vmat_plugin->get_common()->var_from_get( 'manage_volunteer_search', '' );
        // processing a volunteers selection table form submission
        $submit_button = $vmat_plugin->get_common()->var_from_get( 'submit_button', '' );
        if ( 'search_manage_volunteer' == $submit_button ) {
            $args['hpno'] = 1;
        }
        $args['hours'] = $vmat_plugin->get_common()->get_volunteer_hours( $args );
        $message = $this->accumulate_messages( $infos );
        $message = $this->accumulate_messages( $warnings );
        $message .= $this->accumulate_messages( $errors );
        if ( $errors ) {
            $message_class = 'vmat-notice-error';
        } elseif ( $warnings ) {
            $message_class = 'vmat-notice-warning';
        } elseif ( $infos ) {
            $message_class = 'vmat-notice-info';
        } else {
            $message_class = '';
        }
        $args['message'] = $message;
        $args['message_class'] = $message_class;
        return $args;
    }
    
    public function add_nav_to_vmat_cpt_edit_page( $post ) {
        if( get_current_screen()->id == 'edit-vmat_funding_stream' ||
            get_current_screen()->id == 'edit-vmat_organization' ||
            get_current_screen()->id == 'edit-vmat_volunteer_type' ||
            get_current_screen()->id == 'vmat_funding_stream' ||
            get_current_screen()->id == 'vmat_organization' ||
            get_current_screen()->id == 'vmat_volunteer_type') {
            $this->admin_header();
        }
    }
    
    public function admin_notice( $id, $message='', $message_class='' ) {
        $visibility = 'visible';
        if ( $message == '' ) {
            $message = '&nbsp;';
            $visibility = 'hidden';
        }
        ?>
        <div id="<?php echo $id;?>">
        <div class="vmat-notice <?php echo $message_class; ?>" style="visibility:<?php echo $visibility; ?>;">
            <p><?php echo $message; ?></p>
            <span vmat-notice-dismiss class="dashicons-dismiss" title="Dismiss"></span>
        </div>
        </div>
        <?php
    }
    
    public function admin_header( $message='', $message_class='' ) {
        $title_visibility = 'hidden';
        if ( $message == '' ) {
            $title_visibility = 'visible';
        }
        $screen = str_replace( 'edit-', '', get_current_screen()->id );
        $vmat_admin_volunteer_participation_url = add_query_arg(
            'page',
            'vmat_admin_volunteer_participation',
            admin_url('admin.php')
            );
        $vmat_admin_manage_volunteers_url = add_query_arg(
            'page',
            'vmat_admin_manage_volunteers',
            admin_url('admin.php')
            );
        $vmat_admin_reports_url = add_query_arg(
            'page',
            'vmat_admin_reports',
            admin_url('admin.php')
            );
        $vmat_admin_settings_url = add_query_arg(
            'page',
            'vmat_admin_settings',
            admin_url('admin.php')
            );
        $vmat_admin_help_url = add_query_arg(
            'page',
            'vmat_admin_help',
            admin_url('admin.php')
            );
        $vmat_admin_funding_streams_url = add_query_arg(
            'post_type',
            'vmat_funding_stream',
            admin_url('edit.php')
            );
        $vmat_admin_organizations_url = add_query_arg(
            'post_type',
            'vmat_organization',
            admin_url('edit.php')
            );
        $vmat_admin_volunteer_types_url = add_query_arg(
            'post_type',
            'vmat_volunteer_type',
            admin_url('edit.php')
            );
        $help_url = admin_url('admin.php' );
        $help_url = add_query_arg( array(
            'page' => 'vmat_admin_help',
            ),
            $help_url
        );
        
        switch( $screen ) {
            case 'volunteer-mgmnt_page_vmat_admin_volunteer_participation':
                $help_url .= '#volunteer_participation_help';
                $help_url = add_query_arg( 
                    array(
                        'help_section' => 'volunteer_participation_help',
                    ),
                    $help_url
                    );
                break;
            case 'volunteer-mgmnt_page_vmat_admin_manage_volunteers':
                $help_url .= '#manage_volunteers_help';
                $help_url = add_query_arg(
                    array(
                        'help_section' => 'manage_volunteers_help',
                    ),
                    $help_url
                    );
                break;
            case 'volunteer-mgmnt_page_vmat_admin_reports':
                $help_url .= '#reports_help';
                $help_url = add_query_arg(
                    array(
                        'help_section' => 'reports_help',
                    ),
                    $help_url
                    );
                break;
            case 'volunteer-mgmnt_page_vmat_admin_settings':
                $help_url .= '#settings_help';
                $help_url = add_query_arg(
                    array(
                        'help_section' => 'settings_help',
                    ),
                    $help_url
                    );
                break;
            case 'vmat_funding_stream':
                $help_url .= '#funding_streams_help';
                $help_url = add_query_arg(
                    array(
                        'help_section' => 'funding_streams_help',
                    ),
                    $help_url
                    );
                break;
            case 'vmat_organization':
                $help_url .= '#organizations_help';
                $help_url = add_query_arg(
                    array(
                        'help_section' => 'organizations_help',
                    ),
                    $help_url
                    );
                break;
            case 'vmat_volunteer_type':
                $help_url .= '#volunteer_types_help';
                $help_url = add_query_arg(
                    array(
                        'help_section' => 'volunteer_types_help',
                    ),
                    $help_url
                    );
                break;
            case 'volunteer-mgmnt_page_vmat_help':
                $help_url .= '#general_help';
                $help_url = add_query_arg(
                    array(
                        'help_section' => 'general_help',
                    ),
                    $help_url
                    );
                break;
            default:
        }
        ?>
        <div id="vmat_admin_container" class="wrap container clear">
            <div id="vmat_admin_title_wrapper">
            	<div id="vmat_admin_title" style="visibility:<?php echo $title_visibility; ?>">
            		<h1>
            		<?php
            		_e( get_admin_page_title(), 'vmattd' );
            		if( 'volunteer-mgmnt_page_vmat_admin_help' != $screen ) {
            		?>
            		<a id="vmat_help"
            				class="button"
            		        href="<?php echo $help_url; ?>">
            			<?php
            			_e( 'Help' );
            			?>
            		</a>
            		<?php 
            		}
            		?>
            		</h1>    
            	</div>
                <?php
                $this->admin_notice( 'vmat_admin_notice', $message, $message_class );
                ?>
                <?php
                $this->admin_notice( 'vmat_admin_notice_sizer', $message, $message_class );
                ?>
    		</div>
            <div class="wrap">
            	<div id="vmat_admin_nav">
                	  <ul class="nav nav-pills  nav-justified">
                      <li class="nav-item">
                        <a class="nav-link <?php 
                        if( 'volunteer-mgmnt_page_vmat_admin_volunteer_participation' == $screen ) {
                            echo 'active';
                        }
                        ?>" href="<?php 
                        echo $vmat_admin_volunteer_participation_url;
                        ?>"><span class="vmat-quick-link"><?php _e('Vol Participation', 'vmattd'); ?></span></a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link <?php 
                        if( 'volunteer-mgmnt_page_vmat_admin_manage_volunteers' == $screen ) {
                            echo 'active';
                        }
                        ?>" href="<?php 
                        echo $vmat_admin_manage_volunteers_url;
                        ?>"><span class="vmat-quick-link"><?php _e('Manage Vols', 'vmattd'); ?></span></a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link <?php 
                        if( 'volunteer-mgmnt_page_vmat_admin_reports' == $screen ) {
                            echo 'active';
                        }
                        ?>" href="<?php 
                        echo $vmat_admin_reports_url;
                        ?>"><span class="vmat-quick-link"><?php _e('Reports', 'vmattd'); ?></span></a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link <?php 
                        if( 'volunteer-mgmnt_page_vmat_admin_settings' == $screen ) {
                            echo 'active';
                        }
                        ?>" href="<?php 
                        echo $vmat_admin_settings_url;
                        ?>"><span class="vmat-quick-link"><?php _e('Settings', 'vmattd'); ?></span></a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link <?php 
                        if( 'vmat_funding_stream' == $screen ) {
                            echo 'active';
                        }
                        ?>" href="<?php 
                        echo $vmat_admin_funding_streams_url;
                        ?>"><span class="vmat-quick-link"><?php _e('Funding Streams', 'vmattd'); ?></span></a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link <?php 
                        if( 'vmat_organization' == $screen ) {
                            echo 'active';
                        }
                        ?>" href="<?php 
                        echo $vmat_admin_organizations_url;
                        ?>"><span class="vmat-quick-link"><?php _e('Organizations', 'vmattd'); ?></span></a>
                      </li>
                       <li class="nav-item">
                        <a class="nav-link <?php 
                        if( 'vmat_volunteer_type' == $screen ) {
                            echo 'active';
                        }
                        ?>" href="<?php 
                        echo $vmat_admin_volunteer_types_url;
                        ?>"><span class="vmat-quick-link"><?php _e('Vol Types', 'vmattd'); ?></span></a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link <?php 
                        if( 'volunteer-mgmnt_page_vmat_admin_help' == $screen ) {
                            echo 'active';
                        }
                        ?>" href="<?php 
                        echo $vmat_admin_help_url;
                        ?>"><span class="vmat-quick-link"><?php _e('Help', 'vmattd'); ?></span></a>
                      </li>
                    </ul> 
                </div>
            	<?php
    }
    
    public function admin_footer() {
        ?>
        	</div>
        </div>
        <?php
    }
    
    public function ok_cancel_modal() {
        ?>
        <div class="modal fade" id="vmat_ok_cancel_modal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">   
                    
                    <!-- Modal Header -->
                    <div class="modal-header bg-warning">
                        <h4 class="modal-title">Warning!</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    
                    <!-- Modal body -->
                    <div class="modal-body">
                        
                    </div>
                    
                    <!-- Modal footer -->
                    <div class="modal-footer">
                    	<button	id="vmat_ok" type="button" class="btn btn-secondary" data-dismiss="modal">OK</button>
                    	<button	type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                
                </div>
            </div>
        </div>
        <?php 
    }
    
    function get_general_help_text( $help_section='' ) {
        $collapse = ' collapsed';
        $show = '';
        if( $help_section === 'general_help' ) {
            $collapse = '';
            $show = ' show';
        }
        $content = '<div class="card">';
        $content .= '<div class="card-header' . $collapse . '" data-toggle="collapse" data-target="#collapse_general_help">';
        $content .= '<strong id="general_help">
                     General Help. Click to see more information:
                     </strong>';
        $content .= '</div><!-- card-header -->';
        $content .= '<div id="collapse_general_help" class="collapse' . $show . '" data-parent="#help_accordian">';
        $content .= '<div class="card-body">';
        $content .= '<p>';
        $content .= 'The <strong>Volunter Management and Tracking</strong> plugin is designed to provide a means
	                 of managing volunteers and keeping track of events a volunteer has participated in,
                     as well as the number of hours and days a volunteer has been active. The stored volunteer participation
                     data can be used to generate reports detailing volunteer participation. These reports can be
                     tailored to satisfy reporting requirements for <strong>Funding Streams</strong> as well as 
                     being used for a volunteer to track their own volunteer hours.';
        $content .= '</p>';
        $content .= '<p>';
        $content .= 'Volunteers managed in this plugin are standard WordPress Users with additional information associated with them.
                     Volunteers can be created in the same way that WordPress Users are created and also through various
                     plugin-specific means. Registered volunteers will have the user role <strong>"volunteer"</strong> and
                     this gives the same privileges as the <strong>"subscriber"</strong> role in Wordpress as well as additional
                     plugin-specific privileges.';
        $content .= '</p>';
        $content .= '<p>';
        $content .= 'When creating or updating a WordPress User, a <strong>"Volunteer"</strong> checkbox is provided, which causes
                     the user to become a <strong>Volunter Management and Tracking</strong> volunteer with the <strong>"volunteer"</strong> role.
                     Checking this box opens new fields for additional information about a volunteer. 
                     These additional fields are all optional. Once the <strong>"volunteer"</strong> role has been assigned
                     to a user, that user becomes available to the <strong>Volunter Management and Tracking</strong> plugin.';
        $content .= '</p>';
        $content .= '<p>';
        $content .= 'The <strong>Volunter Management and Tracking</strong> plugin is tightly integrated with the 
                     <strong>Events Manager</strong> plugin and allows for tracking of volunteer participation in events.
                     The <strong>Events Manager</strong> plugin has a <strong>Bookings</strong> feature that provides the 
                     ability to track volunteer\'s intention to attend an event. The <strong>Volunter Management and Tracking</strong> plugin
                     takes this further and allows tracking of actual participation in events as well as report generation.
                      When a user "books" an event, the <strong>Events Manager</strong> plugin automaticall creates
                      a new WordPress User or associates the booking with an already created WordPress User. During this process
                      the <strong>Volunter Management and Tracking</strong> plugin associates these new or existing 
                      WordPress Users as <strong>Volunteers</strong> for additional tracking and report generation.';
        $content .= '</p>';
        $content .= '<p>';
        $content .= '<strong>Important!</strong> When <strong>Events Manager</strong> plugin 
                     events are created and <strong>Bookings</strong>
                     are enabled, then a new volunteer that registers a <strong>Booking</strong> 
                     for that event will have a WordPress User account created automatically (if
                     they are not already a registered WordPress User). This is important because the newly
                     created user will be automatically tagged as a <strong>Volunteer</strong> for the
                     <strong>Volunter Management and Tracking</strong> plugin. This automatic creation 
                     of new WordPress Users when booking a registration for an event requires certain
                     settings in the <strong>Events Manager</strong> plugin. To make this works properly
                     go to the <strong>Events Manager->Settings->Bookings->No-User Booking Mode</strong>
                     settings page and ensure that <strong>Enable No-User Booking Mode?</strong> and
                     <strong>Allow bookings with registered emails?</strong> are both set to 
                     <strong>No</strong>.';
        $content .= '</p>';
        $content .= '</div><!-- card body -->';
        $content .= '</div><!-- collapse_volunteer_participation -->';
        $content .= '</div><!-- card -->';
        return $content;
    }
        
    function get_volunteer_participation_help_text( $help_section='' ) {
        $collapse = ' collapsed';
        $show = '';
        if( $help_section === 'volunteer_participation_help' ) {
            $collapse = '';
            $show = ' show';
        }
        $content = '<div class="card">';
        $content .= '<div class="card-header' . $collapse . '" data-toggle="collapse" data-target="#collapse_volunteer_participation">';
        $content .= '<strong id="volunteer_participation_help">
                     The "Vol Participation" screen allows you to do several things. Click to see more information:
                     </strong>';
        $content .= '</div><!-- card-header -->';
        $content .= '<div id="collapse_volunteer_participation" class="collapse' . $show . '" data-parent="#help_accordian">';
        $content .= '<div class="card-body">';
        $content .= '<ul class="vmat-help-list">'; // Volunteer Participation sub items (one <li> for each subscreen)
        // Select Event section
        $content .= '<li>';
        $content .= 'From the <strong>"Select Event:"</strong> screen you can:';
        $content .= '<ul>';
        $content .= '<li>';
        $content .= 'Create a new event using the <strong>"Create New Event"</strong> button.';
        $content .= '</li>';
        $content .= '<li>';
        $content .= 'You can filter the list of events using the following criteria:';
        $content .= '<ul>';
        $content .= '<li>';
        $content .= 'Events associated with an organization.';
        $content .= '</li>';
        $content .= '<li>';
        $content .= 'Event scope (future, past, ...).';
        $content .= '</li>';
        $content .= '<li>';
        $content .= 'Searching for the event name.';
        $content .= '</li>';
        $content .= '</ul>';
        $content .= '</li>';
        $content .= '<li>';
        $content .= 'Once you have found the event of interest, you can click on the event\'s
                     name for more details and to manage volunteer participation for that event 
                     on the <strong>"Selected Event:"</strong> screen.';
        $content .= '</li>';
        $content .= '</ul>';
        $content .= '</li>';
        // Selected Event section
        $content .= '<li>';
        $content .= 'From the <strong>"Selected Event:"</strong> screen you can:';
        $content .= '<ul>';
        $content .= '<li>';
        $content .= 'Go back and select another event using the <strong>"Select Another Event"</strong> button.';
        $content .= '</li>';
        $content .= '<li>';
        $content .= 'Create a new event using the <strong>"Create New Event"</strong> button.';
        $content .= '</li>';
        $content .= '<li>';
        $content .= 'View summary information for the selected event  in the summary box and edit the event by clicking on the 
                     <strong>"Edit"</strong> quick link under the event name.';
        $content .= '</li>';
        $content .= '<li>';
        $content .= 'Under the event summary box are two lists:';
        $content .= '<ul>';
        $content .= '<li>';
        $content .= '<strong>"Add Volunteers to Event:"</strong> on the left. This is a list of volunteers available to 
                     be added to the selected event. You can add multiple checkmarked volunteers using the 
                     <strong>Bulk Add Vols"</strong> button. You can also register a new volunteer and simultaneously 
                     add the newly created user to the event by clicking the <strong>"Add New Vol"</strong> button.';
        $content .= '</li>';
        $content .= '<li>';
        $content .= '<strong>"Manage Event Volunteer Hours:"</strong> on the right. This table can be used to manage the information 
                     for each volunteer associated with the event. You have two options for "bulk" operations on checkmarked 
                     event volunteers <strong>"Remove"</strong> and <strong>"Save"</strong>. Quick links are provided in each 
                     volunteer row to <strong>"Remove"</strong>, <strong>"Save"</strong>, <strong>"Default"</strong> 
                     (sets the volunteer\'s entry to the event\'s default hours and date), and <strong>"Manage Vol"</strong> 
                     (which will send you to the <strong>"Manage Volunteers->Selected Volunteer"</strong> screen) for each volunteer.';
        $content .= '</li>';
        $content .= '</ul>';
        $content .= '</li>';
        $content .= '<li>';
        $content .= 'Each of the two above lists provide a search field to filter the lists.';
        $content .= '</li>';
        $content .= '</ul>'; // end manage volunteers subscreens list
        $content .= '</li>';
        $content .= '</ul>';
        $content .= '</li>';// end the manage volunteers main screen list
        $content .= '</ul>';
        $content .= '</div><!-- card body -->';
        $content .= '</div><!-- collapse_volunteer_participation -->';
        $content .= '</div><!-- card -->';
        return $content;
    }

    function get_manage_volunteers_help_text( $help_section='' ) {
        $collapse = ' collapsed';
        $show = '';
        if( $help_section === 'manage_volunteers_help' ) {
            $collapse = '';
            $show = ' show';
        }
        $content = '<div class="card">';
        $content .= '<div class="card-header' . $collapse . '" data-toggle="collapse" data-target="#collapse_manage_volunteers">';
        $content .= '<strong id="manage_volunteers_help">
                     The "Manage Vols" screen allows you to do several things. Click to see more information:
                     </strong>';
        $content .= '</div>';
        $content .= '<div id="collapse_manage_volunteers" class="collapse' . $show . '" data-parent="#help_accordian">';
        $content .= '<div class="card-body">';
        $content .= '<ul class="vmat-help-list">'; // Manage Volunteers sub items (one <li> for each subscreen)
        // Select a Volunteer section
        $content .= '<li>';
        $content .= 'From the <strong>"Select a Volunteer:"</strong> screen you can:';
        $content .= '<ul>';
        $content .= '<li>';
        $content .= 'Remove one or more volunteers using the <strong>"Bulk Remove Vols"</strong> button. This acts on volunteers checkmarked below.';
        $content .= '</li>';
        $content .= '<li>';
        $content .= 'Add new volunteers using the <strong>"Add New Vol"</strong> button.';
        $content .= '</li>';
        $content .= '<li>';
        $content .= 'View summary stats for each volunteer, including the number of
                     events each volunteer has logged hours to and which of
                     those logged hours have been approved.';
        $content .= '</li>';
        $content .= '<li>';
        $content .= 'You can filter the list of volunteers using the Search box.';
        $content .= '</li>';
        $content .= '<li>';
        $content .= 'Once you have found the volunteer of interest, you can click on the volunteer\'s
                     name for more details and to manage that volunteer on the <strong>"Selected Volunteer:"</strong> screen.';
        $content .= '</li>';
        $content .= '</ul>';
        $content .= '</li>';   
        // Selected Volunteer section
        $content .= '<li>';
        $content .= 'From the <strong>"Selected Volunteer:"</strong> screen you can:';
        $content .= '<ul>';
        $content .= '<li>';
        $content .= 'Go back and select another volunteer using the <strong>"Select Another Volunteer"</strong> button.';
        $content .= '</li>';
        $content .= '<li>';
        $content .= 'View the summary statistics for the selected volunteer.';
        $content .= '</li>';
        $content .= '<li>';
        $content .= 'Edit the selected volunteer\'s profile using the <strong>"Edit Volunteer"</strong> button.';
        $content .= '</li>';
        $content .= '<li>';
        $content .= 'Add volunteer hours for a new event using the <strong>"Add to Event"</strong> button. This brings up a table 
                    of events to select from. From here you can also create a new event using the <strong>"Add New Event"</strong> button.';
        $content .= '</li>';
        $content .= '<li>';
        $content .= 'You can filter the list of events the volunteer has participated in using the Search box.';
        $content .= '</li>';
        $content .= '<li>';
        $content .= 'Event hours can be removed in bulk by checking the desired rows in the table 
                     and then clicking the <strong>"Bulk Remove Hours"</strong> button.';
        $content .= '</li>';
        $content .= '<li>';
        $content .= 'Changes to event hours can be changed in bulk by making changes to the entry fields in the table
                     and then clicking the <strong>"Bulk Save Hours"</strong> button.';
        $content .= '</li>';
        $content .= '<li>';
        $content .= 'Row by row changes can be made using the "quick action" links that become visible when you
                     hover your cursor over the event name in each row. You can <strong>"Remove"</strong> and <strong>"Save"</strong>
                     using those "quick links".';
        $content .= '</li>';
        $content .= '<li>';
        $content .= 'You can also switch to the <strong>"Volunteer Participation: Selected Event"</strong> screen to manage 
                     the selected event\'s volunteer participation by clicking on the <strong>"Manage Particip."</strong> "quick link".';
        $content .= '</li>';
        $content .= '</ul>'; // end manage volunteers subscreens list
        $content .= '</li>';
        $content .= '</ul>'; 
        $content .= '</li>';// end the manage volunteers main screen list
        $content .= '</ul>';
        $content .= '</div><!-- card body --!>';
        $content .= '</div><!-- collapse_manage_volunteers --!>';
        $content .= '</div><!-- card --!>';
        return $content;
    }
    
    function get_reports_help_text( $help_section='' ) {
        $collapse = ' collapsed';
        $show = '';
        if( $help_section === 'reports_help' ) {
            $collapse = '';
            $show = ' show';
        }
        $content = '<div class="card">';
        $content .= '<div class="card-header' . $collapse . '" data-toggle="collapse" data-target="#collapse_reports">';
        $content .= '<strong id="reports_help">
                     The "Reports" screen allows you to generate various reports on volunteer participation. Click to see more information:
                     </strong>';
        $content .= '</div>';
        $content .= '<div id="collapse_reports" class="collapse' . $show . '" data-parent="#help_accordian">';
        $content .= '<div class="card-body">';
        $content .= 'Reports Help content under construction.';
        $content .= '</div><!-- card body --!>';
        $content .= '</div><!-- collapse_reports --!>';
        $content .= '</div><!-- card --!>';
        return $content;
    }
    
    function get_settings_help_text( $help_section='' ) {
        $collapse = ' collapsed';
        $show = '';
        if( $help_section === 'settings_help' ) {
            $collapse = '';
            $show = ' show';
        }
        $content = '<div class="card">';
        $content .= '<div class="card-header' . $collapse . '" data-toggle="collapse" data-target="#collapse_settings">';
        $content .= '<strong id="settings_help">
                     The "Settings" screen allows you to change the settings for this plugin. Click to see more information:
                     </strong>';
        $content .= '</div>';
        $content .= '<div id="collapse_settings" class="collapse' . $show . '" data-parent="#help_accordian">';
        $content .= '<div class="card-body">';
        $content .= '<ul class="vmat-help-list">'; // Settings sub items (one <li> for each section)
        // View Settings
        $content .= '<li>';
        $content .= 'The <strong>"View Settings"</strong> section allows you to configure certain display options:';
        $content .= '<ul>';
        $content .= '<li>';
        $content .= 'The <strong>"Posts Per Page"</strong> settings controls how many rows show up in all table views
                     before a new page is generated.';
        $content .= '</li>';
        $content .= '</ul>';
        $content .= '</li>';
        // Email Settings
        $content .= '<li>';
        $content .= 'The <strong>"Email Settings"</strong> section allows you to configure email notification options:';
        $content .= '<ul>';
        $content .= '<li>';
        $content .= 'The <strong>"Send To: Email When New Users Are Created in the Frontend"</strong> settings determines where 
                     to send notification emails when new volunteers are created from the site\'s frontend. New volunteers 
                     created from the dashboard don\'t generate emails.';
        $content .= '</li>';
        $content .= '</ul>';
        $content .= '</li>';
        $content .= '</ul>'; // end settings sections list
        $content .= '</li>';
        $content .= '</ul>';
        $content .= '</li>';// end the settings main screen list
        $content .= '</ul>';
        $content .= '</div><!-- card body --!>';
        $content .= '</div><!-- collapse_settings --!>';
        $content .= '</div><!-- card --!>';
        return $content;
    }
    
    function get_funding_streams_help_text( $help_section='' ) {
        $collapse = ' collapsed';
        $show = '';
        if( $help_section === 'funding_streams_help' ) {
            $collapse = '';
            $show = ' show';
        }
        $content = '<div class="card">';
        $content .= '<div class="card-header' . $collapse . '" data-toggle="collapse" data-target="#collapse_funding_streams">';
        $content .= '<strong id="funding_streams_help">
                     The "Funding Streams" screen allows you to manage Funding Streams. Click to see more information:
                     </strong>';
        $content .= '</div>';
        $content .= '<div id="collapse_funding_streams" class="collapse' . $show . '" data-parent="#help_accordian">';
        $content .= '<div class="card-body">';
        $content .= '<ul class="vmat-help-list">'; // Funding Streams information
        $content .= '<li>';
        $content .= '<strong>"Funding Streams"</strong> are associated with reporting requirements. A 
                     <strong>"Funding Stream"</strong> has the following aspects:';
        $content .= '<ul>';
        $content .= '<li>';
        $content .= 'A <strong>"Name"</strong> such as "Volunteer Generation Fund".';
        $content .= '</li>';
        $content .= '<li>';
        $content .= 'A <strong>"Funding Start Date"</strong>. This defines the beginning of the period during 
                     which volunteer participation will be tracked for this <strong>"Funding Stream"</strong>.
                     This is an <strong>optional field</strong>. If no start date is defined, volunteer 
                     participation will be tracked for all past events.';
        $content .= '</li>';
        $content .= '<li>';
        $content .= 'A <strong>"Funding End Date"</strong>. This defines the end of the period during
                     which volunteer participation will be tracked for this <strong>"Funding Stream"</strong>.
                     This is an <strong>optional field</strong>. If no end date is defined, volunteer 
                     participation will be tracked indefinitely.';
        $content .= '</li>';
        $content .= '<li>';
        $content .= '<strong>"Fiscal Start Months"</strong> define the periods during the year during which
                     volunteer participation will be tracked for this <strong>"Funding Stream"</strong>.
                     This influences the reports.';
        $content .= '</li>';
        $content .= '</ul>';
        $content .= '</li>';
        $content .= '<li>';
        $content .= '<strong>"Funding Streams"</strong> are associated with <strong>"Organizations"</strong>.
                     See the help section on <strong>"Organizations"</strong> below';
        $content .= '</li>';
        $content .= '</ul>';
        $content .= '</div><!-- card body --!>';
        $content .= '</div><!-- collapse_funding_streams --!>';
        $content .= '</div><!-- card --!>';
        return $content;
    }
    
    function get_organizations_help_text( $help_section='' ) {
        $collapse = ' collapsed';
        $show = '';
        if( $help_section === 'organizations_help' ) {
            $collapse = '';
            $show = ' show';
        }
        $content = '<div class="card">';
        $content .= '<div class="card-header' . $collapse . '" data-toggle="collapse" data-target="#collapse_organizations">';
        $content .= '<strong id="organizations_help">
                     The "Organizations" screen allows you to manage Organizations. Click to see more information:
                     </strong>';
        $content .= '</div>';
        $content .= '<div id="collapse_organizations" class="collapse' . $show . '" data-parent="#help_accordian">';
        $content .= '<div class="card-body">';
        $content .= '<ul class="vmat-help-list">'; // Organizations information
        $content .= '<li>';
        $content .= '<strong>"Organizations"</strong> are associated with events and are meant to portray the
                     group sponsoring the event. Multiple <strong>"Organizations"</strong> can be associated
                     with each event. The association of <strong>"Organization(s)"</strong> with an event
                     is made in the <strong>"Events Manager"</strong> dashboard when creating/editing events. 
                     To associate one or more <strong>"Organizations"</strong> with an event, checkmark the 
                     <strong>"Organization(s)"</strong> in the upper right meta box in the 
                     <strong>"Event Edit"</strong> screen before publishing the event.';
        $content .= '<li>';
        $content .= '<strong>"Organizations"</strong> have a name and can be associated with one or more 
                     <strong>"Funding Streams"</strong>. If an organization is associated with a <strong>"Funding Stream"</strong> 
                     events associated with this <strong>"Organization"</strong> will have their volunteer 
                     participation tracked and made available to report generation';
        $content .= '</li>';
        $content .= '<strong>"Funding Streams"</strong> are associated with an <strong>"Organization"</strong>
                    in the <strong>"Edit Organization"</strong> screen by checkmarking one or more 
                    <strong>"Funding Streams"</strong> in the lower right meta box in the 
                     <strong>"Organization Edit"</strong> screen before publishing the <strong>"Organization"</strong>.';
        $content .= '</li>';
        $content .= '</ul>';
        $content .= '</div><!-- card body --!>';
        $content .= '</div><!-- collapse_organizations --!>';
        $content .= '</div><!-- card --!>';
        return $content;
    }
    
    function get_volunteer_types_help_text( $help_section='' ) {
        $collapse = ' collapsed';
        $show = '';
        if( $help_section === 'volunteer_types_help' ) {
            $collapse = '';
            $show = ' show';
        }
        $content = '<div class="card">';
        $content .= '<div class="card-header' . $collapse . '" data-toggle="collapse" data-target="#collapse_volunteer_types">';
        $content .= '<strong id="volunteer_types_help">
                     The "Volunteer Types" screen allows you to manage Volunteer Types. Click to see more information:
                     </strong>';
        $content .= '</div>';
        $content .= '<div id="collapse_volunteer_types" class="collapse' . $show . '" data-parent="#help_accordian">';
        $content .= '<div class="card-body">';
        $content .= '<ul class="vmat-help-list">'; // Organizations information
        $content .= '<li>';
        $content .= '<strong>"Volunteer Types"</strong> are associated with volunteers and are meant to help 
                     classify volunteers into groups. These classifications can be used in report generation.';
        $content .= '<li>';
        $content .= '<strong>"Volunteer Types"</strong> have a name and can be associated with a volunteer
                     when the volunteer is registered or when updating a volunteer\'s profile. One or more   
                     <strong>"Volunteer Types"</strong> can be checkmarked in the volunteer Register/Update form.';
        $content .= '</li>';
        $content .= '</ul>';
        $content .= '</div><!-- card body --!>';
        $content .= '</div><!-- collapse_volunteer_types --!>';
        $content .= '</div><!-- card --!>';
        return $content;
    }
    
    function html_part_get_events_admin( $args=array() ) {
        global $vmat_plugin;
        $event = $args['event'];
        $event_select_prefix = ' Select ';
        if ( $event ) {
            $event_select_prefix = 'Selected ';
        }
        $add_new_event_href = admin_url( 'post-new.php' );
        $add_new_event_href = add_query_arg(
            array(
                'post_type' => EM_POST_TYPE_EVENT,
            ),
            $add_new_event_href
        );
        ?>
        <div class="row">
            <div class="col">
        		<div class="row">
        		    <div class="col-lg-2">
                		<strong><?php _e( $event_select_prefix . 'Event:', 'vmattd' ); ?></strong>
                	</div>
                		<?php 
                		if( $event ) {
                		    ?>
                		    <div class="col-lg-2">
                    		    <?php 
                    		    echo '<a class="button action vmat-action-btn" href="' . add_query_arg( 'page', 'vmat_admin_volunteer_participation', admin_url( 'admin.php' ) ) . '">' . __('Select another event', 'vmattd') . '</a>';
                    		    ?>
                		    </div>
                		    <?php
                		}
                		?>
                		<div class="col-lg-2">
                    		<a class="button action vmat-action-btn" href="<?php echo $add_new_event_href; ?>">
                    		<?php _e( 'Create New Event', 'vmattd' ); ?>
                    		</a>
                		</div>
        		</div><!-- row -->
        		<div class="row">
        			<div class="col">
        				<?php
            			if ( $event ) {
            			    _e( $vmat_plugin->get_common()->event_display( $event ), 'vmattd');
            			} else {
            			    ?>
            			    <div id="vmat_events_table">
            			    <?php 
            			    $this->html_part_events_table( $args );
            			    ?>
            			    </div>
            			    <?php
            			}
        				?>
        			</div><!--  Selected event display or event selection table -->
        		</div><!-- row -->
          	</div>
      	</div>
    	<?php
    }
    
    function html_part_manage_volunteers_admin( $args=array() ) {
        ?>
        <div class="row">
            <div class="col">
            	<div>
            		<strong><?php _e('Select a Volunteer:')?></strong>
            	</div>
				<div id="vmat_manage_volunteers_table">
				<?php
			    $this->html_part_manage_volunteers_table( $args );
				?>
				</div>
          	</div><!-- row -->
      	</div>
    	<?php
    }
    
    function html_part_manage_volunteer_admin( $args=array() ) {
        global $vmat_plugin;
        ?>
        <div class="row">
            <div class="col">
        		<div id="vmat_manage_volunteer_table">
				<?php
			    $this->html_part_manage_volunteer_table( $args );
				?>
				</div>
				<div id="vmat_manage_volunteer_events_table" style="display:none;">
					<?php 
					$args['epno'] = 1;
					$args['events_search'] = '';
					$args['post_type'] = 'event';
					$args['scope'] = 'future';
					$args['vmat_org'] = '0';
					$args['volunteered_hours'] = $vmat_plugin->get_common()->get_all_volunteer_hours( $args['volunteer'] );
					$args['events'] = $this->get_events_not_volunteered( $args );
					$this->html_part_manage_volunteer_events_table( $args );
					?>
				</div>
          	</div><!-- row -->
      	</div>
    	<?php
    }
    
    function html_part_volunteer_participation_admin( $args=array() ) {
        $event = $args['event'];
        ?>
        <div class="row">
            <div class="col">
        		<input type="hidden" name="page" value="vmat_admin_volunteer_participation">
            	<input type="hidden" name="form_id" value="volunteers-filter">
            	<input type="hidden" name="event_id" value="<?php echo $event->ID; ?>">
        		<div class="row">
        			<div class="col-lg-4">
        				<strong><?php _e( 'Add Volunteers to Event:', 'vmattd' ); ?></strong>
        				<div id="vmat_volunteers_table">
        				<?php
        			    $this->html_part_volunteer_participation_volunteers_table( $args );
        				?>
        				</div>
        			</div><!--  volunteer selection table -->
        			<div class="col">
        				<strong><?php _e( 'Manage Event Volunteer Hours:', 'vmattd' ); ?></strong>
        				<div id="vmat_event_volunteers_table">
        				<?php
        			    $this->html_part_volunteer_participation_event_volunteers_table( $args );
        				?>
        				</div>
        			</div><!--  manage hours table -->
    			</div><!-- row -->
          	</div>
      	</div>
    	<?php
    }
    
    function html_part_register_and_add_volunteer_admin( $args=array() ) {
        ?>
        <div class="row">
        	<div class="col">
        		<?php $this->admin_notice( 'volunteer_registration_status' ); ?>
        	</div>
        </div>
        <div class="row">
            <div class="col">
            	<div class="row">
            		<div class="col-lg-4">
            			<strong><?php _e( 'Register New Volunteer', 'vmattd' ); ?></strong>
            		</div>
            		<div class="col-lg-4">
            			<div class="alignleft">
            				<button id="vmat_register_new_volunteer_for_event" class="button action" type="button" value="register_and_add_new_volunteer_to_event" title="Register and add a new volunteer to the selected event"><?php _e( 'Register/Add to Event', 'vmattd')?></button>
            			</div>
            		</div>
            		<div class="col">
            			<div class="alignleft">
            				<button id="vmat_register_new_volunteer_for_event" class="button action" type="button" value="cancel_volunteer_registration_for_event" title="Cancel registration"><?php _e( 'Cancel', 'vmattd')?></button>
            			</div>
            		</div>
            	</div>
            	<?php $this->html_part_update_volunteer_form( $args ); ?>
          	</div>
      	</div>
    	<?php
    }
    
    function html_part_update_volunteer_admin( $args=array() ) {
        if( $args['volunteer'] ) {
            ?>
            <input type="hidden" name="volunteer_id" value="<?php echo $args['volunteer']->ID; ?>">
            <?php
        }
        ?>
        <div class="row">
        	<div class="col">
        		<?php $this->admin_notice( 'volunteer_update_status' ); ?>
        	</div>
        </div>
        <div class="row">
            <div class="col">
            	<div class="row">
            		<div class="col-lg-4">
            			<strong>
            			<?php if( $args['volunteer'] ) {
            			    _e( 'Update Volunteer', 'vmattd' );
            			} else {
            			    _e( 'Register New Volunteer', 'vmattd' );
            			}
            			?>
            			</strong>
            		</div>
            		<div class="col-lg-4">
            			<div class="alignleft">
            				<button id="vmat_update_volunteer" class="button action" type="button" value="update_volunteer" title="Register/Update a volunteer">
            				<?php if( $args['volunteer'] ) {
            				    _e( 'Update', 'vmattd');
            				} else {
            				    _e( 'Register', 'vmattd');
            				}
            				?></button>
            			</div>
            		</div>
            		<div class="col">
            			<div class="alignleft">
            				<button class="button action" type="button" value="cancel_volunteer_update" title="Cancel update">
            				<?php
            				_e( 'Cancel', 'vmattd')
            				?>
            				</button>
            			</div>
            		</div>
            	</div>
            	<?php $this->html_part_update_volunteer_form( $args['volunteer'] ); ?>
          	</div>
      	</div>
    	<?php
    }
    
    function html_part_update_volunteer_form( $volunteer=null ) {
        global $vmat_plugin;
        ?>
    	<div class="row">
    		<div class="col-lg-6">
        		<?php
        		$vmat_plugin->get_common()->render_wp_required_fields_for_ajax_form_table( $volunteer );
        		?>
        	</div>
        	<div class="col">
        		<?php
        		$vmat_plugin->get_common()->render_common_fields_for_ajax_form_table( $volunteer );
        		?>
        	</div>
        </div>
        <div class="row">
        	<div class="col-lg6">
        		<?php 
        		$vmat_plugin->get_common()->render_volunteer_fields_for_ajax_form_table( $volunteer );
        		?>
        	</div>
		</div>
    	<?php
    }
    
    function html_part_manage_volunteers_table( $args ) {
        /*
         * Display a volunteers table
         *
         */
        global $vmat_plugin;
        $user_query = $args['volunteers'];
        $volunteers = $user_query->results;
        $volunteers_data = $vmat_plugin->get_common()->get_volunteers_data( $volunteers );
        $found_users = $user_query->total_users;
        $page = $args['vpno'];
        $vmat_org = $args['vmat_org'];
        $max_num_pages = ceil( $found_users / $args['posts_per_page'] );
        $search = $args['volunteers_search'];
        
        $page_name = 'vpno';
        $this_page_url = admin_url() . 'admin.php';
        $this_page_url = add_query_arg( 'page', 'vmat_admin_manage_volunteers', $this_page_url );
        $organizations = array(
            0 => __('View all organizations', 'vmattd'),
        );
        foreach ( $vmat_plugin->get_common()->get_post_type('vmat_organization')->posts as $org ) {
            $organizations[$org->ID] = __($org->post_title, 'vmattd');
        }
        $org_pulldown = $vmat_plugin->get_common()->select_options_pulldown(
            'vmat_org',
            $organizations,
            $vmat_org
            );
        $ajax_args = array(
            'volunteers_search' => $search,
            'vmat_org' => $vmat_org,
            'admin_page' => 'vmat_admin_volunteers',
            'posts_per_page' => $args['posts_per_page'],
            'target' => 'vmat_manage_volunteers_table',
            'notice_id' => 'manage_volunteers_status',
        );
        $table_nav = $vmat_plugin->get_common()->ajax_admin_paginate(
            $found_users,
            $page,
            $max_num_pages,
            $page_name,
            $ajax_args
            );
        ?>
    	<div class="row">
        	<div class="col-lg-2">
				<button id="vmat_remove_volunteers" 
				        class="button action vmat-action-btn" 
				        type="button" 
				        value="bulk_remove_volunteers" 
				        title="Remove selected volunteers"
				        disabled>
				        <?php _e( 'Bulk Remove Vols', 'vmattd')?>
				</button>
        	</div>
        	<div class="col-lg-2">
				<button id="vmat_update_volunteer" 
				        class="button action vmat-action-btn" 
				        type="button" 
				        value="show_update_volunteer_form" 
				        title="Add a new volunteer">
				        <?php _e( 'Add New Vol', 'vmattd')?>
				</button>
        	</div>
        	<div class="col">
    			<div class="alignright">
    				<div class="clearable-input">
                	<input type="text" 
                	       id="volunteer-search-input" 
                	       name="manage_volunteers_search" 
                	       placeholder="Search Volunteers"
                	       value="<?php 
                	if ( ! empty( $search) ) {
                	    echo $search;
                	} else {
                	    echo '';
                	}
                	?>" />
                	<span data-clear-input class="dashicons-no-alt" title="Clear"></span>
            	</div>
            	<button class="button" 
        		        name="submit_button" 
        		        type="button" 
        		        value="search_manage_volunteers"><?php _e('Search', 'vmattd'); ?>
        		</button>
    			</div>
    		</div>
        </div>
        <div class="row">
        	<div class="col-lg-4">
        		<?php
            	echo $org_pulldown;
            	?>
            	<button id="manage_volunteers_filter" 
        		        class="button action" 
        		        type="button" 
        		        value="filter_manage_volunteers" >
        		        <?php _e( 'Filter', 'vmattd')?>
        		</button>
        	</div>
        	<div class="col">
        		<div class="tablenav alignright">
        			<?php
        			echo $table_nav;
        			?>
        			<br class="clear"/>
        		</div>
        	</div>
        </div>
        <div class="row">
        	<div class="col pr-1">
        		<?php $this->admin_notice( 'manage_volunteers_status' ); ?>
        	</div>
        </div>
        <div class="row">
        	<div class="col">
        		<table class="widefat" id="vmat_volunteers_table">
        			<thead>
        				<tr>
        					<td class="manage-column column-cb check-column"><input id="vmat_manage_volunteers_select_all" type=checkbox></td>
        					<td class="manage-column"><?php _e( 'Volunteer', 'vmattd' ); ?></td>
        					<td class="manage-column"><?php _e( 'Email', 'vmattd' ); ?></td>
        					<td class="manage-column"><?php _e( 'Orgs', 'vmattd' ); ?></td>
        					<td class="manage-column"><?php _e( 'Events Vol. (apprvd)', 'vmattd' ); ?></td>
        					<td class="manage-column"><?php _e( 'Days Vol. (apprvd)', 'vmattd' ); ?></td>
        					<td class="manage-column"><?php _e( 'Hours Vol. (apprvd)', 'vmattd' ); ?></td>
        					<td class="manage-column"><?php _e( 'Events Vol. (not apprvd)', 'vmattd' ); ?></td>
        					<td class="manage-column"><?php _e( 'Days Vol. (not apprvd)', 'vmattd' ); ?></td>
        					<td class="manage-column"><?php _e( 'Hours Vol. (not apprvd)', 'vmattd' ); ?></td>
        				</tr>
        			</thead>
        			<tbody>
        			<?php 
        			if ( $volunteers ) {
        			    $alternate = 'alternate';
        			    foreach ( $volunteers as $volunteer ) {
        			        echo $vmat_plugin->get_common()->manage_volunteers_row( $volunteer, $volunteers_data[$volunteer->ID], $this_page_url, $alternate );
        			        if ( empty( $alternate ) ) {
        			            $alternate = 'alternate';
        			        } else {
        			            $alternate = '';
        			        }
        			    }
        			} else {
        			    echo '<tr><td colspan="10">';
        			    _e( 'No volunteers found', 'vmattd');
        			    echo '</td></tr>';
        			}
        			?>
        			</tbody>
        		</table>
        	</div>
        </div>
        <?php
    }
    
    function html_part_volunteer_participation_volunteers_table( $args ) {
        /*
         * Display a volunteers table
         *
         */
        global $vmat_plugin;
        $event = $args['event'];
        $user_query = $args['volunteers'];
        $volunteers = $user_query->results;
        $found_users = $user_query->total_users;
        $page = $args['vpno'];
        $max_num_pages = ceil( $found_users / $args['posts_per_page'] );
        $search = $args['volunteers_search'];
        
        $page_name = 'vpno';
        $ajax_args = array(
            'event_id' => $event->ID,
            'volunteers_search' => $search,
            'admin_page' => 'vmat_admin_volunteer_participation',
            'posts_per_page' => $args['posts_per_page'],
            'target' => 'vmat_volunteers_table',
            'notice_id' => 'volunteers_status',
        );
        $table_nav = $vmat_plugin->get_common()->ajax_admin_paginate(
            $found_users,
            $page,
            $max_num_pages,
            $page_name,
            $ajax_args
            );
        ?>
    	<div class="row">
    		<div class="col">
    			<div class="alignright">
    				<div class="clearable-input">
                	<input type="text" 
                	       id="volunteer-search-input" 
                	       name="volunteers_search"
                	       placeholder="Search Available Volunteers" 
                	       value="<?php 
                	if ( ! empty( $search) ) {
                	    echo $search;
                	} else {
                	    echo '';
                	}
                	?>" />
                	<span data-clear-input class="dashicons-no-alt" title="Clear"></span>
            	</div>
            	<button class="button" 
        		        name="submit_button" 
        		        type="button" 
        		        value="search_volunteers"><?php _e('Search', 'vmattd'); ?>
        		</button>
    			</div>
    		</div>
    	</div>
        <div class="row">
        	<div class="col-lg-6">
				<button id="do_volunteers_bulk_action" 
				        class="button action vmat-action-btn" 
				        type="button" 
				        value="bulk_add_volunteers_to_event" disabled>
				        <?php _e( 'Bulk Add Vols ', 'vmattd')?>&raquo;
				</button>
        	</div>
        	<div class="col-lg-6">
				<button id="vmat_register_new_volunteer_for_event" 
				        class="button action vmat-action-btn" 
				        type="button" 
				        value="show_register_and_add_new_volunteer_form" 
				        title="Register and add a new volunteer to the selected event">
				        <?php _e( 'Add New Vol', 'vmattd')?>
				</button>
        	</div>
        </div>
        <div class="row">
        	<div class="col">
        		<div class="tablenav alignright">
        			<?php
        			echo $table_nav;
        			?>
        			<br class="clear"/>
        		</div>
        	</div>
        </div>
        <div class="row">
        	<div class="col pr-1">
        		<?php $this->admin_notice( 'volunteers_status' ); ?>
        	</div>
        </div>
        <div class="row">
        	<div class="col">
        		<table class="widefat" id="vmat_volunteers_table">
        			<thead>
        				<tr>
        					<td class="manage-column column-cb check-column"><input id="vmat_volunteers_select_all" type=checkbox></td>
        					<td class="manage-column"><?php _e( 'User', 'vmattd' ); ?></td>
        					<td class="manage-column"><?php _e( 'Email', 'vmattd' ); ?></td>
        				</tr>
        			</thead>
        			<tbody>
        			<?php 
        			if ( $volunteers ) {
        			    $alternate = 'alternate';
        			    foreach ( $volunteers as $volunteer ) {
        			        echo $vmat_plugin->get_common()->volunteer_participation_volunteers_row( $volunteer, $alternate );
        			        if ( empty( $alternate ) ) {
        			            $alternate = 'alternate';
        			        } else {
        			            $alternate = '';
        			        }
        			    }
        			} else {
        			    echo '<tr><td colspan="3">';
        			    _e( 'No volunteers found', 'vmattd');
        			    echo '</td></tr>';
        			}
        			?>
        			</tbody>
        		</table>
        	</div>
        </div>
        <?php
    }
    
    function html_part_manage_volunteer_table( $args ) {
        /*
         * Display a volunteer table
         *
         */
        global $vmat_plugin;
        $volunteer = $args['volunteer'];
        $hours_query = $args['hours'];
        $found_hours = $hours_query->found_posts;
        $page = $args['hpno'];
        $max_num_pages = ceil( $found_hours / $args['posts_per_page'] );
        $search = $args['manage_volunteer_search'];
        $hours = array();
        foreach ( $hours_query->posts as $hour ) {
            $hours[$hour->ID]= array();
            $hours[$hour->ID]['WP_Post'] = $hour;
            $hours[$hour->ID]['postmeta'] = array_map( function( $meta ) { return $meta[0];}, get_post_meta( $hour->ID ) );
            $hours[$hour->ID]['event'] = get_post( $hours[$hour->ID]['postmeta']['_event_id'] );
        }
        $page_name = 'hpno';
        $this_page_url = admin_url() . 'admin.php';
        $this_page_url = add_query_arg( 
            array(
                'page' => 'vmat_admin_manage_volunteers',
                'volunteer_id' => $volunteer->ID,
            ),
            $this_page_url 
        );
        $ajax_args = array(
            'manage_volunteer_search' => $search,
            'admin_page' => 'vmat_admin_manage_volunteers',
            'posts_per_page' => $args['posts_per_page'],
            'target' => 'vmat_manage_volunteer_table',
            'notice_id' => 'manage_volunteer_status',
        );
        $table_nav = $vmat_plugin->get_common()->ajax_admin_paginate(
            $found_hours,
            $page,
            $max_num_pages,
            $page_name,
            $ajax_args
            );
        ?>
    	<div class="row">
    		<div class="col-lg-2">
    			<button class="button action vmat-action-btn" 
        		        name="submit_button" 
        		        type="button" 
        		        value="show_update_volunteer_form"><?php _e('Edit Volunteer', 'vmattd'); ?>
        		</button>
    		</div>
    		<div class="col-lg-2">
    			<button id="add_event_to_volunteer" 
				        class="button action vmat-action-btn" 
				        type="button" 
				        volunteer_id="<?php echo $volunteer->ID; ?>"
				        value="show_select_event">
				        <?php _e( 'Add Hours to Event', 'vmattd')?>
				</button>
    		</div>
    	</div>
    	<div class="row">
    		<div class="col-lg-2">
    			<div class="actions bulkactions">
    				<button id="hours_bulk_remove" 
    				        class="button action vmat-action-btn" 
    				        type="button"
    				        volunteer_id="<?php echo $volunteer->ID; ?>"
    				        value="bulk_hours_remove"
    				        disabled><?php _e( 'Bulk Remove Hours', 'vmattd')?></button>
    			</div>
        	</div>
        	<div class="col-lg-2">
        		<div class="actions bulkactions">
    				<button id="hours_bulk_save" 
    				        class="button action vmat-action-btn" 
    				        type="button" 
    				        volunteer_id="<?php echo $volunteer->ID; ?>"
    				        value="bulk_hours_save"
    				        disabled>
    				        <?php _e( 'Bulk Save Hours', 'vmattd')?>
    				</button>
    			</div>
        	</div>
        	<div class="col">
    			<div class="alignright">
    				<div class="clearable-input">
                	<input type="text" 
                	       id="manage-volunteer-search-input" 
                	       name="manage_volunteer_search" 
                	       placeholder="Search Events"
                	       value="<?php 
                	if ( ! empty( $search) ) {
                	    echo $search;
                	} else {
                	    echo '';
                	}
                	?>">
                	<span data-clear-input class="dashicons-no-alt" title="Clear"></span>
            	</div>
            	<button class="button" 
        		        name="submit_button" 
        		        type="button" 
        		        volunteer_id="<?php echo $volunteer->ID; ?>"
        		        value="search_manage_volunteer"><?php _e('Search', 'vmattd'); ?>
        		</button>
    			</div>
    		</div>
    	</div>
        <div class="row">
        	<div class="col">
        		<div class="tablenav alignright">
        			<?php
        			echo $table_nav;
        			?>
        			<br class="clear"/>
        		</div>
        	</div>
        </div>
        <div class="row">
        	<div class="col pr-1">
        		<?php $this->admin_notice( 'manage_volunteer_status' ); ?>
        	</div>
        </div>
        <div class="row">
        	<div class="col">
        		<table class="widefat" id="vmat_volunteer_table">
        			<thead>
        				<tr>
        					<td class="manage-column column-cb check-column"><input id="vmat_hours_select_all" type=checkbox></td>
        					<td class="manage-column"><?php _e('Event', 'vmattd' );?></td>
        					<td class="manage-column"><?php _e('Hours/Day', 'vmattd' );?></td>
        					<td class="manage-column"><?php _e('Start (mm/dd/yyyy)', 'vmattd' );?></td>
        					<td class="manage-column"><?php _e('Vol. Days', 'vmattd' );?></td>
        					<td class="vmat-manage-column">
        						<?php _e('Appr', 'vmattd' );?><input id="vmat_hours_approve_all" type=checkbox>
        					</td>
        				</tr>
        			</thead>
        			<tbody>
        			<?php 
        			if ( $hours ) {
        			    $alternate = 'alternate';
        			    foreach ( $hours as $hour ) {
        			        echo $vmat_plugin->get_common()->volunteer_hour_row( $hour, $alternate );
        			        if ( empty( $alternate ) ) {
        			            $alternate = 'alternate';
        			        } else {
        			            $alternate = '';
        			        }
        			    }
        			} else {
        			    echo '<tr><td colspan="6">';
        			    _e( 'No hours found', 'vmattd');
        			    echo '</td></tr>';
        			}
        			?>
        			</tbody>
        		</table>
        	</div>
        </div>
        <?php
    }
    
    function html_part_volunteer_participation_event_volunteers_table( $args ) {
        /*
         * Display a volunteers hours management table
         *
         */
        global $vmat_plugin;
        $event = $args['event'];
        $event_data = $vmat_plugin->get_common()->get_event_data( $event->ID );
        $ev_query = $args['event_volunteers'];
        $volunteers = $ev_query->results;
        $found_users = $ev_query->total_users;
        $page = $args['evpno'];
        $max_num_pages = ceil( $found_users / $args['posts_per_page'] );
        $search = $args['event_volunteers_search'];
        $page_name = 'evpno';
        $ajax_args = array(
            'event_id' => $event->ID,
            'admin_page' => 'vmat_admin_volunteer_participation',
            'event_volunteers_search' => $args['event_volunteers_search'],
            'posts_per_page' => $args['posts_per_page'],
            'target' => 'vmat_event_volunteers_table',
            'notice_id' => 'event_volunteers_status'
        );
        $table_nav = $vmat_plugin->get_common()->ajax_admin_paginate(
            $found_users,
            $page,
            $max_num_pages,
            $page_name,
            $ajax_args
            );
        ?>
    	<div class="row">
    		<div class="col">
    			<div class="alignright">
                	<div class="clearable-input">
                    	<input type="text" 
                    	       id="event-volunteer-search-input" 
                    	       name="event_volunteers_search"
                    	       placeholder="Search Event Volunteers"
                    	       value="<?php 
                    	if ( ! empty( $search) ) {
                    	    echo $search;
                    	} else {
                    	    echo '';
                    	}
                    	?>" />
                    	<span data-clear-input class="dashicons-no-alt" title="Clear"></span>
                	</div>
                	<button class="button" 
            		        name="submit_button" 
            		        type="button" 
            		        value="search_event_volunteers"><?php _e('Search', 'vmattd'); ?>
            		</button>
        		</div>
    		</div><!-- col-md-auto -->
    	</div><!-- row -->
        <div class="row">
        	<div class="col-lg-3">
				<button id="event_volunteers_bulk_remove" 
				        class="button action vmat-action-btn" 
				        type="button" 
				        value="bulk_event_volunteers_remove" 
				        disabled>
				        &laquo;&nbsp;<?php _e( 'Bulk Remove Vols', 'vmattd')?>
				</button>
        	</div>
        	<div class="col-lg-3">
				<button id="event_volunteers_bulk_save" 
				        class="button action vmat-action-btn" 
				        type="button" 
				        value="bulk_event_volunteers_save" disabled>
				        <?php _e( 'Bulk Save Hours', 'vmattd'); ?>
				</button>
        	</div>
        </div>
        <div class="row">
        	<div class="col">
        		<div class="tablenav alignright">
        			<?php
        			echo $table_nav;
        			?>
        			<br class="clear"/>
        		</div>
        	</div>
        </div>
         <div class="row">
        	<div class="col">
        		<?php $this->admin_notice( 'event_volunteers_status' ); ?>
        	</div>
        </div>
        <div class="row">
        	<div class="col">
        		<table class="widefat" id="vmat_event_volunteers_table">
        			<thead>
        				<tr>
        					<td class="manage-column column-cb check-column"><input id="vmat_event_volunteers_select_all" type=checkbox></td>
        					<td class="manage-column"><?php _e('User', 'vmattd' );?></td>
        					<td class="manage-column"><?php _e('Hours/Day (' . $event_data['hours_per_day'] . ')', 'vmattd' );?></td>
        					<td class="manage-column"><?php _e('Start (mm/dd/yyyy)', 'vmattd' );?></td>
        					<td class="manage-column"><?php _e('Vol. Days (' . $event_data['days'] . ')', 'vmattd' );?></td>
        					<td class="vmat-manage-column">
        						<?php _e('Appr', 'vmattd' );?><input id="vmat_event_volunteers_approve_all" type=checkbox>
        					</td>
        				</tr>
        			</thead>
        			<tbody>
        			<?php 
        			if ( $volunteers ) {
        			    $alternate = 'alternate';
        			    foreach ( $volunteers as $volunteer ) {
        			        echo $vmat_plugin->get_common()->volunteer_participation_event_volunteer_row( $volunteer, $event->ID, $alternate );
        			        if ( empty( $alternate ) ) {
        			            $alternate = 'alternate';
        			        } else {
        			            $alternate = '';
        			        }
        			    }
        			} else {
        			    echo '<tr><td colspan="6">';
        			    _e( 'No event volunteers found', 'vmattd');
        			    echo '</td></tr>';
        			}
        			?>
        			</tbody>
        		</table>
        	</div>
        </div>
        <?php
    }
    
    function html_part_events_table( $args ) {
        /*
         * Display an events table
         *
         */
        global $vmat_plugin;
        $events_query = $args['events'];
        $found_posts = $events_query->found_posts;
        $page = $args['epno'];
        $max_num_pages = $events_query->max_num_pages;
        $vmat_org = $args['vmat_org'];
        $scope = $args['scope'];
        $search = $args['events_search'];
        $this_page_url = admin_url() . 'admin.php';
        $this_page_url = add_query_arg( 'page', 'vmat_admin_volunteer_participation', $this_page_url );
        $organizations = array(
            0 => __('View all organizations', 'vmattd'),
        );
        foreach ( $vmat_plugin->get_common()->get_post_type('vmat_organization')->posts as $org ) {
            $organizations[$org->ID] = __($org->post_title, 'vmattd');
        }
        $em_scopes = em_get_scopes();
        $org_pulldown = $vmat_plugin->get_common()->select_options_pulldown(
            'vmat_org',
            $organizations,
            $vmat_org
            );
        $scope_pulldown = $vmat_plugin->get_common()->select_options_pulldown(
            'scope',
            $em_scopes,
            $scope
            );
        $page_name = 'epno';
        $ajax_args = array(
            'admin_page' => 'vmat_admin_volunteer_participation',
            'scope' => $scope,
            'vmat_org' => $vmat_org,
            'events_search' => $search,
            'posts_per_page' => $args['posts_per_page'],
            'target' => 'vmat_events_table',
            'notice_id' => 'events_status',
        );
        $table_nav = $vmat_plugin->get_common()->ajax_admin_paginate(
            $found_posts,
            $page,
            $max_num_pages,
            $page_name,
            $ajax_args
            );
        ?>
        <div class="row">
        	<div class="col-lg-4">
        		<?php
            	echo $org_pulldown;
            	?>
            	<button id="events_filter" 
        		        class="button action" 
        		        type="button" 
        		        value="filter_events" >
        		        <?php _e( 'Filter', 'vmattd')?>
        		</button>
        	</div>
        	<div class="col-lg-4">
        		<?php
            	echo $scope_pulldown;
            	?>
        		<button id="events_filter" 
        		        class="button action" 
        		        type="button" 
        		        value="filter_events" >
        		        <?php _e( 'Filter', 'vmattd')?>
        		</button>
        	</div>
        	<div class="col">
        		<div class="alignright">
        			<div class="clearable-input">
                	<input type="text" 
                	       id="event-search-input" 
                	       name="events_search"
                	       placeholder="Search Events" 
                	       value="<?php 
                	if ( ! empty( $search) ) {
                	    echo $search;
                	} else {
                	    echo '';
                	}
                	?>">
                	<span data-clear-input class="dashicons-no-alt" title="Clear"></span>
            	</div>
            	<button class="button" 
        		        name="submit_button" 
        		        type="button" 
        		        value="search_events"><?php _e('Search', 'vmattd'); ?>
        		</button>
        		</div>
        	</div>
    	</div>
    	<div class="row">
        	<div class="col">
        		<div class="tablenav alignright">
        			<?php
        			echo $table_nav;
        			?>
        			<br class="clear"/>
        		</div>
    		</div>
    	</div>
    	<div class="row">
    		<div class="col pr-1">
        		<?php $this->admin_notice( 'events_status' ); ?>
        	</div>
    	</div>
    	<div class="row">
    		<div class="col">
        		<table class="widefat events-table">
        			<thead>
        				<tr>
        					<th><?php _e( 'Name', 'vmattd' ); ?></th>
        					<th><?php _e( 'Orgs', 'vmattd' ); ?></th>
        					<th><?php _e( 'Volunteers', 'vmattd' ); ?></th>
        					<th><?php _e( 'Location', 'vmattd' ); ?></th>
        					<th><?php _e('Date and time', 'vmattd' ); ?></th>
        				</tr>
        			</thead>
        			<tbody>
        			<?php 
        			if ( $events_query->posts ) {
        			    $alternate = 'alternate';
        			    foreach ( $events_query->posts as $event ) {
        			        echo $vmat_plugin->get_common()->event_row(
        			            $event,
        			            $this_page_url,
        			            $alternate
        			            );
        			        if ( empty( $alternate ) ) {
        			            $alternate = 'alternate';
        			        } else {
        			            $alternate = '';
        			        }
        			    }
        			} else {
        			    echo '<tr><td colspan="5">';
        			    _e( 'No events found', 'vmattd');
        			    echo '</td></tr>';
        			}
        			?>
        			</tbody>
        		</table>
        	</div>
        </div>
        <?php
    }
    
    function html_part_manage_volunteer_events_table( $args ) {
        /*
         * Display an events table
         *
         */
        global $vmat_plugin;
        $volunteer = $args['volunteer'];
        $events_query = $args['events'];
        $found_posts = $events_query->found_posts;
        $page = $args['epno'];
        $max_num_pages = $events_query->max_num_pages;
        $vmat_org = $args['vmat_org'];
        $scope = $args['scope'];
        $search = $args['events_search'];
        $organizations = array(
            0 => __('View all organizations', 'vmattd'),
        );
        foreach ( $vmat_plugin->get_common()->get_post_type('vmat_organization')->posts as $org ) {
            $organizations[$org->ID] = __($org->post_title, 'vmattd');
        }
        $em_scopes = em_get_scopes();
        $org_pulldown = $vmat_plugin->get_common()->select_options_pulldown(
            'vmat_org',
            $organizations,
            $vmat_org
            );
        $scope_pulldown = $vmat_plugin->get_common()->select_options_pulldown(
            'scope',
            $em_scopes,
            $scope
            );
        $page_name = 'epno';
        $ajax_args = array(
            'admin_page' => 'vmat_admin_volunteer_participation',
            'scope' => $scope,
            'vmat_org' => $vmat_org,
            'events_search' => $search,
            'posts_per_page' => $args['posts_per_page'],
            'target' => 'vmat_manage_volunteer_events_table',
            'notice_id' => 'manage_volunteer_events_status',
        );
        $table_nav = $vmat_plugin->get_common()->ajax_admin_paginate(
            $found_posts,
            $page,
            $max_num_pages,
            $page_name,
            $ajax_args
            );
        $add_new_event_href = admin_url( 'post-new.php' );
        $add_new_event_href = add_query_arg( 
            array(
                'post_type' => EM_POST_TYPE_EVENT,
            ),
            $add_new_event_href
        );
        ?>
        <div class="row">
        	<div class="col-lg-3">
        		<strong><?php _e( 'Select Event:', 'vmattd' ); ?></strong>
        	</div>
        	<div class="col-lg-3">
        		<a class="button action vmat-action-btn" href="<?php echo $add_new_event_href; ?>">
        		<?php _e( 'Create New Event', 'vmattd' ); ?>
        		</a>
        	</div>
        	<div class="col-lg-3">
    			<button id="cancel_event_selection" 
    		        class="button action vmat-action-btn" 
    		        type="button" 
    		        value="cancel_event_selection" >
    		        <?php _e( 'Cancel', 'vmattd')?>
    			</button>
        	</div>
        </div>
        <div class="row">
        	<div class="col-lg-4">
        		<?php
            	echo $org_pulldown;
            	?>
            	<button id="manage_volunteer_events_filter" 
        		        class="button action" 
        		        type="button" 
        		        value="filter_manage_volunteer_events" >
        		        <?php _e( 'Filter', 'vmattd')?>
        		</button>
        	</div>
        	<div class="col-lg-4">
        		<?php
            	echo $scope_pulldown;
            	?>
        		<button id="manage_volunteer_events_filter" 
        		        class="button action" 
        		        type="button" 
        		        value="filter_manage_volunteer_events" >
        		        <?php _e( 'Filter', 'vmattd')?>
        		</button>
        	</div>
        	<div class="col">
        		<div class="alignright">
        			<div class="clearable-input">
                	<input type="text" 
                	       id="event-search-input" 
                	       name="manage_volunteer_events_search"
                	       placeholder="Search Events" 
                	       value="<?php 
                	if ( ! empty( $search) ) {
                	    echo $search;
                	} else {
                	    echo '';
                	}
                	?>">
                	<span data-clear-input class="dashicons-no-alt" title="Clear"></span>
            	</div>
            	<button class="button" 
        		        name="submit_button" 
        		        type="button" 
        		        value="search_manage_volunteer_events"><?php _e('Search', 'vmattd'); ?>
        		</button>
        		</div>
        	</div>
    	</div>
    	<div class="row">
        	<div class="col">
        		<div class="tablenav alignright">
        			<?php
        			echo $table_nav;
        			?>
        			<br class="clear"/>
        		</div>
    		</div>
    	</div>
    	<div class="row">
    		<div class="col pr-1">
        		<?php $this->admin_notice( 'manage_volunteer_events_status' ); ?>
        	</div>
    	</div>
    	<div class="row">
    		<div class="col">
        		<table class="widefat events-table">
        			<thead>
        				<tr>
        					<th><?php _e( 'Name', 'vmattd' ); ?></th>
        					<th><?php _e( 'Orgs', 'vmattd' ); ?></th>
        					<th><?php _e( 'Volunteers', 'vmattd' ); ?></th>
        					<th><?php _e( 'Location', 'vmattd' ); ?></th>
        					<th><?php _e('Date and time', 'vmattd' ); ?></th>
        				</tr>
        			</thead>
        			<tbody>
        			<?php 
        			if ( $events_query->posts ) {
        			    $alternate = 'alternate';
        			    foreach ( $events_query->posts as $event ) {
        			        echo $vmat_plugin->get_common()->manage_volunteer_event_row(
        			            $volunteer, 
        			            $event,
        			            $alternate
        			            );
        			        if ( empty( $alternate ) ) {
        			            $alternate = 'alternate';
        			        } else {
        			            $alternate = '';
        			        }
        			    }
        			} else {
        			    echo '<tr><td colspan="5">';
        			    _e( 'No events found', 'vmattd');
        			    echo '</td></tr>';
        			}
        			?>
        			</tbody>
        		</table>
        	</div>
        </div>
        <?php
    }
    
    public function html_page_admin_manage_volunteers() {
        global $vmat_plugin;
        $args = array(
            'message' => '',
            'message_class' => '',
        );
        $args = $this->admin_manage_volunteers_prep_args();
        $this->admin_header( $args['message'], $args['message_class'] );
        ?>  
        <div class="row">
        	<div class="col">
        	<?php 
        	if( ! $args['volunteer'] ) {
            	?>
            	<div id="vmat_manage_volunteers_admin" class="col">
                    <?php 
                    // display the manage volunteers table
                    $this->html_part_manage_volunteers_admin( $args );
                    ?>
                </div>
                <div id="vmat_update_volunteer_admin" style="display:
                    <?php
                    if( $args['edit_volunteer'] ) {
                        echo 'block';
                    } else {
                        echo 'none';
                    }
                    ?>">
            	    <?php 
            	    $this->html_part_update_volunteer_admin( $args );
            	    ?>
        	    </div>
        	    <?php 
        	} else {
        	    ?>
        	    <div>
        	    <?php
    			echo '<a class="button" href="' . add_query_arg( 'page', 'vmat_admin_manage_volunteers', admin_url( 'admin.php' ) ) . '">' . __('Select Another Volunteer', 'vmattd') . '</a>';
    			?>
    			</div>
        		<div>
            		<strong><?php _e('Selected Volunteer:')?></strong>
            	</div>	
            	<?php 
            	echo $vmat_plugin->get_common()->volunteer_display( $args['volunteer'] );
        	} 
        	?>
       		</div>
       	</div>
       	<?php
    	if ( $args['volunteer'] ) {
    	    $args = $this->admin_manage_volunteer_prep_args();
    	    ?>
    		<div class="row vmat-hr">
    		</div>
    	    <div class="row">
    	    	<div class="col">
    	    	     <div id="vmat_manage_volunteer_admin"  style="display:
                    <?php
                    if( $args['edit_volunteer'] ) {
                        echo 'none';
                    } else {
                        echo 'block';
                    }
                    ?>"> 
            	    <?php 
            	    $this->html_part_manage_volunteer_admin( $args );
            	    ?>
            	    </div>
                    <div id="vmat_update_volunteer_admin" style="display:
                    <?php
                    if( $args['edit_volunteer'] ) {
                        echo 'block';
                    } else {
                        echo 'none';
                    }
                    ?>">
                	    <?php 
                	    $this->html_part_update_volunteer_admin( $args );
                	    ?>
        	    	</div>
        	    </div>
    	    </div>
    	    <?php 
        }
        $this->admin_footer();
    }

    public function html_page_admin_volunteer_participation() {
        /*
         * 
         */
        $args = $this->admin_volunteer_participation_prep_args();
        $this->admin_header( $args['message'], $args['message_class'] );
        ?>
        <div class="row">
        	<div id="vmat_select_event_admin" class="col">
                <?php 
                // display the event selection table or the selected event name and organization
                $this->html_part_get_events_admin( $args );
                ?>
            </div>
        </div>  
        <?php 
    	if ( $args['event'] ) {?>
    		<div class="row vmat-hr">
    		</div>
    	    <div class="row">
    	    	<div class="col">
            	    <div id="vmat_volunteer_participation_admin"> 
            	    <?php 
            	    $this->html_part_volunteer_participation_admin( $args );
            	    ?>
            	    </div>
            	    <div id="vmat_register_and_add_volunteer_admin" style="display:none">
            	    <?php 
            	    $this->html_part_register_and_add_volunteer_admin( $args );
            	    ?>
            	    </div>
        	    </div>
    	    </div>
    	    <?php 
        }
        $this->admin_footer();
    }

    public function html_page_admin_reports() {
        $args = array(
            'message' => '',
            'message_class' => '',
        );
        $this->admin_header($args['message'], $args['message_class'] );
        ?>
  		<!-- content here -->
        <?php
        $this->admin_footer();
    }

    public function html_page_admin_settings() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        
        // add error/update messages
        
        // check if the user have submitted the settings
        // wordpress will add the "settings-updated" $_GET parameter to the url
        if ( isset( $_GET['settings-updated'] ) ) {
            // add settings saved message with the class of "updated"
            add_settings_error( 'vmat_messages', 'vmat_message', __( 'Settings Saved', 'vmattd' ), 'updated' );
        }
        
        // show error/update messages
        settings_errors( 'vmat_messages' );
        $args = array(
            'message' => '',
            'message_class' => '',
        );
        $this->admin_header( $args['message'], $args['message_class'] );
        ?>
  		<form action="options.php" method="post">
         <?php
         // output security fields for the registered setting "wporg"
         settings_fields( 'vmat' );
         // output setting sections and their fields
         // (sections are registered for "wporg", each field is registered to a specific section)
         do_settings_sections( 'vmat_admin_settings' );
         // output save settings button
         submit_button( 'Save Settings' );
         ?>
         </form>
        <?php
        $this->admin_footer();
    }
    
    public function html_page_admin_help() {
        $args = array(
            'message' => '',
            'message_class' => '',
        );
        $help_section = 'general_help';
        if( array_key_exists( 'help_section', $_GET ) ) {
            $help_section = $_GET['help_section'];
        }
        $this->admin_header($args['message'], $args['message_class'] );
        $content = '<div class="container">';
        $content .= '<div id="help_accordian">';
        $content .= $this->get_general_help_text( $help_section );
        $content .= $this->get_volunteer_participation_help_text( $help_section );
        $content .= $this->get_manage_volunteers_help_text( $help_section );
        $content .= $this->get_reports_help_text( $help_section );
        $content .= $this->get_settings_help_text( $help_section );
        $content .= $this->get_funding_streams_help_text( $help_section );
        $content .= $this->get_organizations_help_text( $help_section );
        $content .= $this->get_volunteer_types_help_text( $help_section );
        $content .= '</div>';
        $content .= '</div>';
        echo $content;   
        $this->admin_footer();
    }
}
