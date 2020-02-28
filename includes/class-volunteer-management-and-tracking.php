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
     * common class
     *
     */
    protected $common;

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
            if ( ! is_admin() ) {
                require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-volunteer-management-and-tracking-public.php';
            }
        }

        $this->loader = new Volunteer_Management_And_Tracking_Loader();
        $this->common = new Volunteer_Management_And_Tracking_Common( $this->get_plugin_name(), $this->get_version() );

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

        $plugin_admin = new Volunteer_Management_And_Tracking_Admin( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        
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
        
        // the top level admin dashboard
        $this->loader->add_action('admin_menu', $plugin_admin, 'top_level_options_page');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     */
    private function define_public_hooks() {

        $plugin_public = new Volunteer_Management_And_Tracking_Public( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
        /*
         * we enqueue the login scripts from the public side
         */
        $this->loader->add_action( 'login_enqueue_scripts', $plugin_public, 'enqueue_scripts');
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

}
