<?php
/**
 * @package wpSfApiCliCli
 */
/*
Plugin Name: Wordpress Salesforce Client API integrator
Plugin URI: https://github.com/davidgalvez/wpSalesforceApiLoad
Description: Loads leads info via an API endpoint to integrate Clients Apis and connect to Salesforce to sinc cases creation.
Version: 1.0.0
Author: David Galvez
Author URI: https://davidgalvez.github.io/
License: GPL3 or later
License URI: http://www.gnu.org/licenses/gpl.html
Text Domain: wpSalesforceClientLoad
*/

defined('ABSPATH') or die("Not allowed access!!");

defined('ABSPATH') or die("Acceso restringido");





// Define path and URL to the ACF plugin.
define( 'MY_ACF_PATH', plugin_dir_path(__FILE__) . '/includes/acf/' );
define( 'MY_ACF_URL', plugin_dir_url(__FILE__) . '/includes/acf/' );

// Include the ACF plugin.
include_once( MY_ACF_PATH . 'acf.php' );

// Customize the url setting to fix incorrect asset URLs.
add_filter('acf/settings/url', 'my_acf_settings_url');
function my_acf_settings_url( $url ) {
    return MY_ACF_URL;
}

// (Optional) Hide the ACF admin menu item.
add_filter('acf/settings/show_admin', '__return_false');

// When including the PRO plugin, hide the ACF Updates menu
add_filter('acf/settings/show_updates', '__return_false', 100);

 



if(file_exists(dirname(__FILE__)."/vendor/autoload.php"))
{
  require_once(dirname(__FILE__)."/vendor/autoload.php");
}

use wpSfApiCli\Base\Activate;
use wpSfApiCli\Base\Deactivate;
/**
 * Metodo que se ejecuta en la activación del plugin
 */
function wpSfApiCli_activate_plugin() {
    Activate::activate();
}


/**
 * Metodo que se ejecuta en la desactivación del plugin
 */
function wpSfApiCli_deactivate_plugin() {
	    Deactivate::deactivate();
}

register_activation_hook( __FILE__, 'wpSfApiCli_activate_plugin' );
register_deactivation_hook( __FILE__, 'wpSfApiCli_deactivate_plugin' );

/**
 * Init plugin services
 */

if ( class_exists( 'wpSfApiCli\\Init' ) ) {
	wpSfApiCli\Init::register_services(plugin_basename(__FILE__));
}else{
  echo "-----------------------------Error Loading composer----------------------";
}

include('includes/CustomFields.php');