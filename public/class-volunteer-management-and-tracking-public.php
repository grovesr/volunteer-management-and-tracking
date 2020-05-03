<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 */
class Volunteer_Management_And_Tracking_Public {

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
	 * param      string    $plugin_name       The name of the plugin.
	 * param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 */
	public function enqueue_styles( $hook ) {

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

		wp_enqueue_style( $this->plugin_name . '-css-public', plugin_dir_url( __FILE__ ) . 'css/volunteer-management-and-tracking-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . '-css-common', plugin_dir_url( __FILE__ ) . '../common/css/volunteer-management-and-tracking-common.css', array(), $this->version, 'all' );
		// careful about enqueueing css on the frontend !!!!
		//wp_enqueue_style( $this->plugin_name . '-css-bootstrap', plugin_dir_url( __FILE__ ) . '../common/css/bootstrap.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name . '-js-public', plugin_dir_url( __FILE__ ) . 'js/volunteer-management-and-tracking-public.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name . '-js-common', plugin_dir_url( __FILE__ ) . '../common/js/volunteer-management-and-tracking-common.js', array( 'jquery' ), $this->version, false );

	}

}
