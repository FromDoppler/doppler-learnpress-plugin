<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Fired during plugin activation
 *
 * @link       www.fromdoppler.com
 * @since      1.0.0
 *
 * @package    Doppler_For_Learnpress
 * @subpackage Doppler_For_Learnpress/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Doppler_For_Learnpress
 * @subpackage Doppler_For_Learnpress/includes
 * @author     Doppler <hgalvan@makingsense.com>
 */
class Doppler_For_Learnpress_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		update_option('dplrlp_version', DOPPLER_FOR_LEARNPRESS_VERSION);

		/**
		 * Doppler App Integration.
		 * 
		 * If plugin in installed for the 1st time (dplrlp_access_token is empty)
		 * we ignore app integration becouse it will be performed on first sync.
		 * 
		 * If for some reason dplrlp_access_token has a value, it means plugin was
		 * deactivated and re-activated. On deactivation Integration was DELETED, so we
		 * are goint to re-activate and regenerate the keys.
		 * ** TESTING ALWAYS CONNECT ON ACTIVATION **
		 */
		/*
		 $options = get_option('dplr_settings');
		
		$DopplerAppConnect = new Doppler_For_Learnpress_App_Connect(
			$options['dplr_option_useraccount'], $options['dplr_option_apikey'],
			DOPPLER_LEARNPRESS_API_URL, DOPPLER_FOR_LEARNPRESS_ORIGIN
		);
		$response = $DopplerAppConnect->connect();
		//check $response['response']['code']==200
		*/
	}


}
