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

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 */
	public function enqueue_scripts() {

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

	}
	
	public function top_level_options_page() {
	    $res = add_menu_page(
	        'Volunteer Management and Tracking',
	        'Volunteer Options',
	        'manage_options',
	        'vmat_top_level_options',
	        'vmat_top_level_options_page_html',
	        'dashicons-groups',
	        20
	    );
	}

}
