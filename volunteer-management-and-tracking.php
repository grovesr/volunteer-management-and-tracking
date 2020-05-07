<?php
/*
 Plugin Name:       Volunteer Management And Tracking
 Plugin URI:        https://ulstercorps.org/admin/admin.php?page=vmat_admin_volunteer_participation
 Description:       Track volunteers and associate them with Events Manager Events.
 Version:           1.0.2
 Requires at least: 5.3
 Requires PHP:      5.3
 Author:            Rob Groves
 Author URI:        mailto:robgroves0@gmail.com
 License:           GPL v2 or later
 License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 Text Domain:       vmatd
 Domain Path:       /languages
 License:     GPL2

{Plugin Name} is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

{Plugin Name} is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with {Plugin Name}. If not, see {License URI}.
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'VOLUNTEER_MANAGEMENT_AND_TRACKING_VERSION', '1.0.2' );

/*
 * plugin author email
 */
define( 'PLUGIN_AUTHOR_EMAIL', 'robgroves0@gmail.com' );

/*
 * Define dependent plugins required in order to use this plugin
 */
global $vmat_plugin;

$dependent_plugins = [
    'events-manager/events-manager.php' => 'https://wordpress.org/plugins/events-manager/',
    'multiple-roles/multiple-roles.php' => 'https://wordpress.org/plugins/multiple-roles/',
];
$options = get_option( 'vmat_options' );
if( isset( $options['vmat_enable_volunteer_imports'] ) && $options['vmat_enable_volunteer_imports'] == true ) {
    $dependent_plugins['import-users-from-csv-with-meta/import-users-from-csv-with-meta.php'] = 'https://wordpress.org/plugins/import-users-from-csv-with-meta/';
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-volunteer-management-and-tracking-activator.php
 */
function activate_volunteer_management_and_tracking() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-volunteer-management-and-tracking-activator.php';
    Volunteer_Management_And_Tracking_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-volunteer-management-and-tracking-deactivator.php
 */
function deactivate_volunteer_management_and_tracking() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-volunteer-management-and-tracking-deactivator.php';
    Volunteer_Management_And_Tracking_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_volunteer_management_and_tracking' );
register_deactivation_hook( __FILE__, 'deactivate_volunteer_management_and_tracking' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-volunteer-management-and-tracking.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 */
function run_volunteer_management_and_tracking() {
    $plugin = new Volunteer_Management_And_Tracking();
    $plugin->run();
    return $plugin;
}

function check_for_prerequisites($dependent_plugins) {
    $errors = '';
    foreach ( $dependent_plugins as $plugin => $plugin_url ) {
        if ( is_admin() &&  ! is_plugin_active( $plugin ) ) {
            $errors .= '<p>' .
                __( 'The Volunteer Management and Tracking plugin requires the ' . $plugin . ' plugin (' . 
                   '<a href="' . $plugin_url . '">WordPress plugin page</a>' . ').', 'vmattd' ) .
                '</p>';
        } // output an error if missing
    } // for each dependent plugin
    if ( ! empty( $errors ) ) {
        add_action( 'admin_notices',
            function() use ( $errors ) {
                echo '<div class="notice notice-error is-dismissible">';
                echo $errors;
                echo '<p>' . __('To fix this either activate the missing plugin(s) or deactivate the Volunteer Management and Tracking plugin.', 'vmattd') .
                     '</p></div>';
            }
        );
        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
    }
}

// check for prerequisites and if not satisified, show admin error
add_action('admin_init', 
    function() use ( $dependent_plugins ) {
        check_for_prerequisites( $dependent_plugins ); 
    }
);
	
$vmat_plugin = run_volunteer_management_and_tracking();
