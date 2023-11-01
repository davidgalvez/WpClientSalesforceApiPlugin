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
}