<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.bridgerpay.com/
 * @since             1.0.0
 * @package           Bridgerpay
 *
 * @wordpress-plugin
 * Plugin Name:       BridgerPay
 * Plugin URI:        bridgerpay
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            BridgerPay
 * Author URI:        https://www.bridgerpay.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bridgerpay
 * Domain Path:       /languages
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
define( 'BRIDGERPAY_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-bridgerpay-activator.php
 */
function activate_bridgerpay() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bridgerpay-activator.php';
	Bridgerpay_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-bridgerpay-deactivator.php
 */
function deactivate_bridgerpay() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bridgerpay-deactivator.php';
	Bridgerpay_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_bridgerpay' );
register_deactivation_hook( __FILE__, 'deactivate_bridgerpay' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-bridgerpay.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */

 // Get Token from BridgerPay API
  function getBridgerPayToken($base_url,$username,$password){
		$response = wp_remote_post( $base_url."/v1/auth/login", array(
			'method' => 'POST',
			'timeout' => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array('Content-Type' => 'application/json','cache-control' => 'no-cache'),
			'body' => array('user_name' => $username, 'password' => $password),
			'cookies' => array()
	    )
		);

		if (!is_wp_error( $response ) ) {
			$tmp = json_decode($response);
   		return $tmp->result->access_token->token;
		}
 }



 // Cashier Session Function
  function cashierSession($base_url,$api_key,$cashier_key,$token){
		$response = wp_remote_post( $base_url."/v1/cashier/session/create/".$api_key, array(
			'method' => 'POST',
			'timeout' => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array('Content-Type' => 'application/json','cache-control' => 'no-cache',"Authorization" => "Bearer ".$token),
			'body' => array("cashier_key" => $cashier_key),
			'cookies' => array()
	    )
		);

		if (!is_wp_error( $response ) ) {
			$tmp = json_decode($response);
			return $tmp->result->cashier_token;
		}
 }

function run_bridgerpay() {

	$plugin = new Bridgerpay();
	$plugin->run();

}
run_bridgerpay();
