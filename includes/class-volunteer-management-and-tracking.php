<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 */
class Volunteer_Management_And_Tracking {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     */
    protected $version;

    /**
     * common functions class
     *
     */
    protected $common;

    /**
     * admin functions class
     *
     */
    protected $admin;
    
    /**
     * public functions class
     *
     */
    protected $public;
    
    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     */
    public function __construct() {
        if ( defined( 'VOLUNTEER_MANAGEMENT_AND_TRACKING_VERSION' ) ) {
            $this->version = VOLUNTEER_MANAGEMENT_AND_TRACKING_VERSION;
        } else {
            $this->version = '0.0.0';
        }
        $this->plugin_name = 'volunteer-management-and-tracking';

        $this->load_dependencies();
        $this->set_locale();
        if ( is_admin() ) {
            $this->define_admin_hooks();
        } else {
            $this->define_public_hooks();
        }

    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Volunteer_Management_And_Tracking_Loader. Orchestrates the hooks of the plugin.
     * - Volunteer_Management_And_Tracking_i18n. Defines internationalization functionality.
     * - Volunteer_Management_And_Tracking_Admin. Defines all hooks for the admin area.
     * - Volunteer_Management_And_Tracking_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-volunteer-management-and-tracking-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-volunteer-management-and-tracking-i18n.php';
        
        /**
         * The class responsible for registering all actions that occur accross public and admin areas
         * of the site.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'common/class-volunteer-management-and-tracking-common.php';
        
        if ( is_admin() ) {
            /**
             * The class responsible for defining all actions that occur in the admin area.
             */
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-volunteer-management-and-tracking-admin.php';
        } else {
            /**
             * The class responsible for defining all actions that occur in the public-facing
             * side of the site.
             */
             require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-volunteer-management-and-tracking-public.php';
        }

        $this->loader = new Volunteer_Management_And_Tracking_Loader();
        $this->common = new Volunteer_Management_And_Tracking_Common( $this->get_plugin_name(), $this->get_version() );
        if ( is_admin() ) {
            $this->admin = new Volunteer_Management_And_Tracking_Admin( $this->get_plugin_name(), $this->get_version() );
        } else {
            $this->public = new Volunteer_Management_And_Tracking_Public( $this->get_plugin_name(), $this->get_version() );
        }
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Volunteer_Management_And_Tracking_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     */
    private function set_locale() {

        $plugin_i18n = new Volunteer_Management_And_Tracking_i18n();

        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

    }


    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     */
    private function define_admin_hooks() {
        
        $this->loader->add_action( 'admin_enqueue_scripts', $this->admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $this->admin, 'enqueue_scripts' );
        
        // ok/cancel modal
        $this->loader->add_action( 'admin_footer', $this->admin, 'ok_cancel_modal' );
        
        // Add ajax handlers. the hook has wp_ajax_ajax because the function called begins with ajax_
        $this->loader->add_action( 'wp_ajax_ajax_add_volunteers_to_event', $this->admin, 'ajax_add_volunteers_to_event' );
        $this->loader->add_action( 'wp_ajax_ajax_add_manage_volunteer_to_event', $this->admin, 'ajax_add_manage_volunteer_to_event' );
        $this->loader->add_action( 'wp_ajax_ajax_remove_volunteers_from_event', $this->admin, 'ajax_remove_volunteers_from_event' );
        $this->loader->add_action( 'wp_ajax_ajax_save_event_volunteers_data', $this->admin, 'ajax_save_event_volunteers_data' );
        $this->loader->add_action( 'wp_ajax_ajax_approve_volunteers_hours_for_event', $this->admin, 'ajax_approve_volunteers_hours_for_event' );
        $this->loader->add_action( 'wp_ajax_ajax_set_default_event_volunteers_data', $this->admin, 'ajax_set_default_event_volunteers_data' );
        $this->loader->add_action( 'wp_ajax_ajax_paginate_vmat_admin_page', $this->admin, 'ajax_paginate_vmat_admin_page' );
        $this->loader->add_action( 'wp_ajax_ajax_filter_events', $this->admin, 'ajax_filter_events' );
        $this->loader->add_action( 'wp_ajax_ajax_filter_manage_volunteer_events', $this->admin, 'ajax_filter_manage_volunteer_events' );
        $this->loader->add_action( 'wp_ajax_ajax_search_volunteers', $this->admin, 'ajax_search_volunteers' );
        $this->loader->add_action( 'wp_ajax_ajax_filter_manage_volunteers', $this->admin, 'ajax_filter_manage_volunteers' );
        $this->loader->add_action( 'wp_ajax_ajax_search_manage_volunteer', $this->admin, 'ajax_search_manage_volunteer' );
        $this->loader->add_action( 'wp_ajax_ajax_search_event_volunteers', $this->admin, 'ajax_search_event_volunteers' );
        $this->loader->add_action( 'wp_ajax_ajax_update_volunteer', $this->admin, 'ajax_update_volunteer' );
        $this->loader->add_action( 'wp_ajax_ajax_save_volunteer_hours_data', $this->admin, 'ajax_save_volunteer_hours_data' );
        $this->loader->add_action( 'wp_ajax_ajax_remove_hours_from_volunteer', $this->admin, 'ajax_remove_hours_from_volunteer' );
        $this->loader->add_action( 'wp_ajax_ajax_remove_volunteers', $this->admin, 'ajax_remove_volunteers' );
        $this->loader->add_action( 'wp_ajax_ajax_associate_event_bookings_with_volunteers', $this->admin, 'ajax_associate_event_bookings_with_volunteers' );
        
        // add vmat settings page
        $this->loader->add_action( 'admin_init', $this->admin, 'settings_init' );
        
        // render the volunteer fields for the wp-admin/user-new.php form
        $this->loader->add_action('user_new_form', $this->common, 'render_volunteer_fields_form_table');       
        // render the volunteer fields from the wp-admin/user-edit.php form
        $this->loader->add_action('edit_user_profile', $this->common, 'render_populated_volunteer_fields_form_table');
        // render the volunteer fields on the wp-admin/profile.php form
        $this->loader->add_action('show_user_profile', $this->common, 'render_populated_volunteer_fields_form_table');
        
        // check for errors in volunteer fields from wp-admin/user-new.php or wp-admin/user-edit.php or wp-admin/profile.php forms
        $this->loader->add_action('user_profile_update_errors', $this->common, 'volunteer_registration_errors_action');
        // check for errors in common fields from wp-admin/user-new.php or wp-admin/user-edit.php or wp-admin/profile.php forms
        $this->loader->add_action('user_profile_update_errors', $this->common, 'common_registration_errors_action');
        
        // update the volunteer fields from the wp-admin/profile.php form
        $this->loader->add_action('personal_options_update', $this->common, 'update_volunteer_user_meta');
        // update the volunteer role on the wp-admin/profile.php form
        $this->loader->add_action('personal_options_update', $this->common, 'add_volunteer_user_role');
        // update the volunteer fields from the wp-admin/user-edit.php form
        $this->loader->add_action('edit_user_profile_update', $this->common, 'update_volunteer_user_meta');
        // update the volunteer fields from the wp-admin/user-new.php form
        $this->loader->add_action('edit_user_created_user', $this->common, 'update_volunteer_user_meta');
        
        // add volunteer role to new users created during em booking and add default volunteer hours to event 
        $this->loader->add_action('user_register', $this->admin, 'add_new_booking_volunteer_to_em_event');
        // add logged in user's default volunteer hours to em event associated to a new em booking
        $this->loader->add_action('em_booking_add', $this->admin, 'add_existing_volunteer_to_em_event', 10, 3 );
        
        // dashboard page
        $this->loader->add_action('admin_menu', $this->admin, 'admin_main_page', 5);
        // manage volunteer participation page
        $this->loader->add_action('admin_menu', $this->admin, 'admin_volunteer_participation_page', 6);
        // manage volunteers list page
        $this->loader->add_action('admin_menu', $this->admin, 'admin_manage_volunteers_page', 7);
        // reports page
        $this->loader->add_action('admin_menu', $this->admin, 'admin_reports_page', 8);
        // settings page
        $this->loader->add_action('admin_menu', $this->admin, 'admin_settings_page', 9);
        // settings page
        $this->loader->add_action('admin_menu', $this->admin, 'admin_help_page', 10);
        // remove the submenu auto-generated from the main menu
        $this->loader->add_action('admin_menu', $this->admin, 'remove_admin_main_submenu');
        
        // add nav tabs to the top of the all vmat cpt edit post pages
        $this->loader->add_action('all_admin_notices', $this->admin, 'add_nav_to_vmat_cpt_edit_page');
        
        // add set role to volunteer to users bulk actions
        $this->loader->add_filter( 'bulk_actions-users', $this->admin, 'register_bulk_add_volunteer_role' );
        $this->loader->add_filter( 'handle_bulk_actions-users', $this->admin, 'add_volunteer_role_handler', 10, 3 );
        $this->loader->add_action('admin_notices', $this->admin, 'bulk_add_volunteer_role_admin_notice');
        
        // add Add quick actions to Users list
        $this->loader->add_filter('user_row_actions', $this->admin, 'modify_user_list_row_actions', 10, 2);
        // Add volunteer row action handler
        $this->loader->add_action('in_admin_header', $this->admin, 'add_volunteer_actions');     
        
        // add Add Hours quick action to Events posts
        $this->loader->add_filter('post_row_actions', $this->admin, 'modify_event_list_row_actions', 10, 2);
        
        // Add organization filtering to Events
        $this->loader->add_action('restrict_manage_posts',$this->admin, 'organization_filtering',10);
        $this->loader->add_filter( 'parse_query', $this->admin, 'filter_request_query' , 10);
        
        // Add Organizations selection meta box to Events edit page
        $this->loader->add_action('add_meta_boxes', $this->admin, 'add_em_org_meta_boxes');
        // Save organizations meta box data into event meta data
        $this->loader-> add_action('save_post_event',$this->admin, 'update_event_organizations_meta',1,2);
        
        // add Orgs column to events listing
        $this->loader->add_filter( 'manage_posts_columns', $this->admin, 'add_orgs_column_to_em' );
        $this->loader->add_action( 'manage_posts_custom_column', $this->admin, 'fill_event_orgs_column', 10, 2);
        
        // add columns to vmat_funding_streams listing
        $this->loader->add_filter( 'manage_vmat_funding_stream_posts_columns', $this->admin, 'add_columns_to_funding_streams' );
        $this->loader->add_action( 'manage_vmat_funding_stream_posts_custom_column', $this->admin, 'fill_funding_streams_columns', 10, 2);
        
        // add columns to vmat_organizations listing
        $this->loader->add_filter( 'manage_vmat_organization_posts_columns', $this->admin, 'add_columns_to_organizations' );
        $this->loader->add_action( 'manage_vmat_organization_posts_custom_column', $this->admin, 'fill_organizations_columns', 10, 2);
        
        // Add Funding Streams selection meta box to Organizations edit page
        $this->loader->add_action('add_meta_boxes', $this->admin, 'add_org_funding_stream_meta_box');
        // Save Funding Stream meta box data into Organization meta data
        $this->loader-> add_action('save_post_vmat_organization',$this->admin, 'update_org_funding_streams_meta',1,2);
        
        // Add additional information fields meta box to Organizations edit page
        $this->loader->add_action('add_meta_boxes', $this->admin, 'add_organization_fields_meta_box');
        // Save additional information fields meta box data into Organizations meta data
        $this->loader->add_action('save_post_vmat_organization',$this->admin, 'update_organization_fields_meta',1,2);
        
        // Add additional information fields meta box to Funding Streams edit page
        $this->loader->add_action('add_meta_boxes', $this->admin, 'add_funding_stream_fields_meta_box');
        // Save additional information fields meta box data into Funding Streams meta data
        $this->loader->add_action('save_post_vmat_funding_stream',$this->admin, 'update_funding_stream_fields_meta',1,2);
        
        // action to remove vmat_hours from user when user is deleted
        $this->loader->add_action( 'delete_user', $this->admin, 'post_remove_volunteer_action');
        
        // action to remove either post or postmeta associated with a post when the post is deleted
        //$this->loader->add_action( 'after_delete_post', $this->admin, 'post_remove_post_action' );
        
        // register custom post type Hours
        $this->loader->add_action('init', $this->common , 'register_hours_post_type');
        // register custom post type Funding Streams
        $this->loader->add_action('init', $this->common , 'register_funding_stream_post_type');
        // register custom post type Organizations
        $this->loader->add_action('init', $this->common , 'register_organization_post_type');
        // register custom post type Volunteer Type
        $this->loader->add_action('init', $this->common , 'register_volunteer_type_post_type');
        
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     */
    private function define_public_hooks() {

        $this->loader->add_action( 'wp_enqueue_scripts', $this->public, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $this->public, 'enqueue_scripts' );
        /*
         * we enqueue the login scripts from the public side
         */
        $this->loader->add_action( 'login_enqueue_scripts', $this->public, 'enqueue_scripts');
        /*
         * Add further action hooks for the public side
         */
        // render the common fields for the wp-login.php?action=register form
        $this->loader->add_action('register_form', $this->common, 'render_common_fields_div');
        // render the volunteer fields for the wp-login.php?action=register form
        $this->loader->add_action('register_form', $this->common, 'render_volunteer_fields_div');
        
        // check for errors on fields other than user_login and user_email from the wp-login.php?action=register form
        $this->loader->add_filter('registration_errors', $this->common, 'volunteer_registration_errors_filter');
        $this->loader->add_filter('registration_errors', $this->common, 'common_registration_errors_filter');
        
        // update the volunteer user meta fields from the wp-login.php?action=register form
        $this->loader->add_action('user_register', $this->common, 'update_volunteer_user_meta');
        // update any common user fields from the wp-login.php?action=register form
        $this->loader->add_action('user_register', $this->common, 'update_common_user_meta');
        // update the volunteer role on the wp-login.php?action=register form
        $this->loader->add_action('register_new_user', $this->common, 'add_volunteer_user_role');
        
        // register custom post type Hours
        $this->loader->add_action('init', $this->common , 'register_hours_post_type');
        // register custom post type Funding Streams
        $this->loader->add_action('init', $this->common , 'register_funding_stream_post_type');
        // register custom post type Organizations
        $this->loader->add_action('init', $this->common , 'register_organization_post_type');

    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     */
    public function get_version() {
        return $this->version;
    }
    
    /**
     * The reference to the common class that describes functionality common
     * to admin and public.
     *
     */
    public function get_common() {
        return $this->common;
    }
    
    /**
     * The reference to the admin class that describes functionality in the admin
     *
     */
    public function get_admin() {
        return $this->admin;
    }
    
    /**
     * The reference to the admin class that describes functionality on the public side
     *
     */
    public function get_public() {
        return $this->public;
    }

}
