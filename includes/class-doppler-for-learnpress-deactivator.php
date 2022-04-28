<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Fired during plugin deactivation
 *
 * @link       www.fromdoppler.com
 * @since      1.0.0
 *
 * @package    Doppler_For_Learnpress
 * @subpackage Doppler_For_Learnpress/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Doppler_For_Learnpress
 * @subpackage Doppler_For_Learnpress/includes
 * @author     Doppler <hgalvan@makingsense.com>
 */
class Doppler_For_Learnpress_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

		/**
		 * On deactivation delete integration with APP.
		 * If APP couldn't delete stops deactivation
		 * and shows message.
		 */
		// $options = get_option('dplr_settings');
		// $has_consumer_secret = get_option('dplrlp_access_token');

		// if( empty($options['dplr_option_useraccount']) || empty($options['dplr_option_apikey']) ||
		// 	empty($has_consumer_secret) ) return false;
		
		// $doppler_app_connect = new Doppler_For_Learnpress_App_Connect(
		// 	$options['dplr_option_useraccount'], $options['dplr_option_apikey'],
		// 	DOPPLER_LEARNPRESS_API_URL, DOPPLER_FOR_LEARNPRESS_ORIGIN
		// );

		// $response = $doppler_app_connect->disconnect();
		
		// if($response['response']['code'] == 400){
		// 	$body = json_decode(wp_remote_retrieve_body($response));
		// 	//If integration doesn't exists go on with deactivation...
		// 	if($body->errorCode != 41){
		// 		$err_message = '';
		// 		if($body->errorCode == 40){
		// 			$err_message = _('Please delete associated campaings in Doppler before deactivating.', 'doppler-for-learnpress');
		// 		}
		// 		Doppler_For_Learnpress_Admin_Notice::display_error(
		// 			__("<strong>Doppler For Learnpress wasn't deactivated.</strong>".$err_message, "doppler-for-learnpress")
		// 		);
		// 		header("Location: ".admin_url('plugins.php'));
		// 		exit();
		// 	}
		// }

	}

}
