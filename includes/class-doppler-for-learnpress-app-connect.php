<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * This class handles the communication 
 * with the api to connect or disconnect
 * the app integration.
 *  
 * @since 1.0.2
 * @author Hernán Galván <hgalvan@makingsense.com>
 */
class Doppler_For_Learnpress_App_Connect {

	//const INTEGRATION = 'learnpress';
	const INTEGRATION = 'magento';
	const DEBUG_MODE = true;
	private $api_account;
	private $api_key;
	private $api_url;
	private $origin;
	private $api_keys_description;

	/**
	 *	  
	 * @param string	$api_account	Doppler username
	 * @param string	$api_key		Doppler API Key
	 * @param string	$api_url		The Doppler API URL.
	 * @param string	$origin			The authorized origin header parameter (WordPress, WooCommerce, etc.)
	 *
	 */
    public function __construct( $api_account, $api_key, $api_url, $origin ) {
		$this->api_key = $api_key;
		$this->api_account = $api_account;
		$this->api_url = $api_url;
		$this->origin = $origin;
    }
	
	private function get_api_account() {
        return $this->api_account;
    }

    private function get_api_key() {
		return $this->api_key;		
    }

    private function get_api_url() {
        return $this->api_url;
    }

	private function get_origin() {
        return $this->origin;
	}

    /**
     * Header to use in the requests to API.
	 * 
	 * @since 1.0.2
	 * @return string
     */
    private function set_request_header() {
        return array(
            "Accept" => "application/json",
            "Content-Type" => "application/json",
            "X-Doppler-Subscriber-Origin" => $this->get_origin(),
            "Authorization" => "token ". $this->get_api_key(),
        );
	}
	
	/**
	 * Handles the requests to API
	 * 
	 * @since 1.0.2
	 * 
	 * @param array 	$body 	An array with the body to be sent
	 * @param string	$method	The http method to be used.
	 * @return array|object 
	 * 
	 */
	public function do_request( $body = array() , $method ) {
		
		$api_url = $this->get_api_url();
		$account = $this->get_api_account();
		
		if(empty($account) || empty($api_url)) return false;
		
		$url = $api_url . 'accounts/'. $account. '/integrations/' . self::INTEGRATION;
		 return wp_remote_request($url, array(
			'method' => $method,
			'headers'=> $this->set_request_header(),
			'timeout' => 12,
			'body'=> json_encode($body)
		));		
	}

    /**
	 * Send API credentials to Doppler API
	 * to establish connection.
	 * 
	 * @since 1.0.2
	 * @return array|object
	 */
    public function connect() {
        //Generate a random string.
        $token = openssl_random_pseudo_bytes(16);
        $accessToken = bin2hex($token);
		$body = array(
			'accessToken'=> $accessToken, 
			'accountName' => get_site_url(), 
			'refreshToken' => ''
		);
        $response = $this->do_request($body, 'PUT');
        if($response['response']['code']=='200'){
            update_option('dplrlp_accessToken', $accessToken);
        }
		return $response;
    }

	/**
	 * Delete ingregration with Doppler,
	 * remove keys from WC
	 * 
	 * @since 1.0.2
	 * @return array|object
	 */
    public function disconnect(){
		global $wpdb;
		delete_option('dplrlp_accessToken');
		return $this->do_request([], 'DELETE');		
	}

}

