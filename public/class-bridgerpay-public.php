<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.bridgerpay.com/
 * @since      1.0.0
 *
 * @package    Bridgerpay
 * @subpackage Bridgerpay/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Bridgerpay
 * @subpackage Bridgerpay/public
 * @author     BridgerPay <info@bridgerpay.com>
 */

global $current_user;

class Bridgerpay_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	?>

	<?php
		function gen_uuid() {
		    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
		        // 32 bits for "time_low"
		        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

		        // 16 bits for "time_mid"
		        mt_rand( 0, 0xffff ),

		        // 16 bits for "time_hi_and_version",
		        // four most significant bits holds version number 4
		        mt_rand( 0, 0x0fff ) | 0x4000,

		        // 16 bits, 8 bits for "clk_seq_hi_res",
		        // 8 bits for "clk_seq_low",
		        // two most significant bits holds zero and one for variant DCE1.1
		        mt_rand( 0, 0x3fff ) | 0x8000,

		        // 48 bits for "node"
		        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
		    );
		}

		add_action( 'plugins_loaded', 'get_user_info' );

		function get_user_info(){
			$current_user = wp_get_current_user();

			if ( !($current_user instanceof WP_User) )
				return;

				return $current_user;
			// Do the remaining stuff that has to happen once you've gotten your user info
		}

		// Shortcode: [bridgercashier price="" currency="" amount_lock="" button_name=""]
		function create__shortcode($atts) {
			if ( is_admin()){
				return;
			}
			$current_user = get_user_info();

			// echo "<pre>";
			// // var_dump($current_user);
			// echo "</pre>";
			$shippingFirstName = get_user_meta( $current_user->ID, 'first_name', true );

			$bridgerpay_settings_options = get_option( 'bridgerpay_settings_option_name' );

			$baseUrl = $bridgerpay_settings_options['base_url_0'];
			$cashierUrl = $bridgerpay_settings_options['cashier_url_1'];
			$apiKey = $bridgerpay_settings_options['api_key_2'];
			$cashierKey = $bridgerpay_settings_options['cashier_key_3'];
			$username = $bridgerpay_settings_options['username_4'];
			$password = $bridgerpay_settings_options['password_5'];

			// Generate the cashier
			$token = getBridgerPayToken($baseUrl,$username,$password);
			$cashierToken = cashierSession($baseUrl,$apiKey,$cashierKey,$token);

			$atts = shortcode_atts(
				array(
					'price' => '',
					'currency' => '',
					'amount_lock' => '',
					'button_mode' => '',
					'button_name' => '',
					'theme' => '',
					'country' => '',
				),
				$atts,
				'bridgercashier'
			);

			$price = $atts['price'];
			$currency = $atts['currency'];
			$amount_lock = $atts['amount_lock'];
			$button_mode = (!empty($atts['button_mode']) AND !empty($atts['button_name'])) ? "spot" : $atts['button_mode'];
			$button_name = $atts['button_name'];
			$theme = $atts['theme'];
			$country = $atts['country'];

			// ob_start();

			return '<script
						src="'.$cashierUrl.'/cashier"
            data-cashier-key="'.$cashierKey .'"
            data-cashier-token="'. $cashierToken .'"
            data-order-id="'.gen_uuid().'"
            data-currency="'.$currency.'"
            data-direct-payment-method=""
            data-currency-lock="true"
            data-amount-lock="'.$amount_lock.'"
            data-first-name="'.$current_user->first_name.'"
            data-last-name="'.$current_user->last_name.'"
            data-amount="'.$price.'"
            data-email="'.$current_user->user_email.'"
            data-address=""
            data-country="'.$country.'"
            data-state=""
            data-theme="light"
            data-city=""
            data-zip-code=""
            data-phone=""
            data-language="en"
            data-affiliate-id=""
            data-tracking-id=""
            data-platform-id=""
            data-button-mode="'.$button_mode.'"
            data-button-text="'.$button_name.'"
            />
						</script>';

			// $output_string = ob_get_contents();
			// ob_end_clean();
			// return $output_string;
		}
		add_shortcode( 'bridgercashier', 'create__shortcode' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Bridgerpay_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Bridgerpay_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bridgerpay-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Bridgerpay_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Bridgerpay_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/bridgerpay-public.js', array( 'jquery' ), $this->version, false );

	}

}
