<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       www.fromdoppler.com
 * @since      1.0.0
 *
 * @package    Doppler_For_Learnpress
 * @subpackage Doppler_For_Learnpress/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Doppler_For_Learnpress
 * @subpackage Doppler_For_Learnpress/admin
 * @author     Doppler <hgalvan@makingsense.com>
 */
class Doppler_For_Learnpress_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	private $version;

	private $doppler_service;

	private $admin_notice;
	
	private $success_message;

	private $error_message;
	
	private $required_doppler_version;

	private $origin;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $doppler_service ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->doppler_service = $doppler_service;
		$this->success_message = false;
		$this->error_message = false;
		$this->required_doppler_version = '2.1.4';
		$this->origin = $this->set_origin();
		$this->set_credentials();

	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	public function set_error_message($message) {
		$this->error_message = $message;
	}

	public function set_success_message($message) {
		$this->success_message = $message;
	}

	public function get_error_message() {
		return $this->error_message;
	}

	public function get_success_message() {
		return $this->success_message;
	}
	
	public function get_required_doppler_version(){
		return $this->required_doppler_version;
	}

	public function set_origin() {
		if(method_exists($this->doppler_service,'set_origin')){
			$this->doppler_service->set_origin('Learnpress');
		}
	}

	public function display_error_message() {
		if($this->get_error_message()!=''):
		?>
		<div id="displayErrorMessage" class="messages-container blocker">
			<p><?php echo $this->get_error_message(); ?></p>
		</div>
		<?php
		endif;
	}

	public function display_success_message() {
		if($this->get_success_message()!=''):
		?>
		<div id="displaySuccessMessage" class="messages-container info">
			<p><?php echo $this->get_success_message(); ?></p>
		</div>
		<?php
		endif;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'wp-jquery-ui-dialog' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/doppler-for-learnpress-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/doppler-for-learnpress-admin.js', array( 'jquery', 'jquery-ui-dialog', 'Doppler'), $this->version, false );
		wp_localize_script( $this->plugin_name, 'dplrlp_object_string', array( 
			'Syncrhonizing'   	=> __( 'We\'re synchronizing your Subscribers with your Doppler List.', 'doppler-for-learnpress' ),	
			'newListSuccess'    => __( 'The List has been created correctly.', 'doppler-for-learnpress'),
			'selectAList'		=> __( 'Select the Doppler List where you want to import Subscribers of your course. When synchronized, those customers already registered and future customers will be sent automatically.', 'doppler-for-learnpress')							 				
		) ); 
	}
	
	public function dplrlp_check_parent() {
		if ( !is_plugin_active( 'doppler-form/doppler-form.php' ) ) {
			$this->admin_notice = array( 'error', __('Ouch! <strong>Doppler for LearnPress</strong> requires the <a href="https://wordpress.org/plugins/doppler-form/">Doppler Forms</a> plugin to be installed and active.', 'doppler-for-learnpress') );
			$this->deactivate();
		}else if( version_compare( get_option('dplr_version'), $this->get_required_doppler_version(), '<' ) ){
			$this->admin_notice = array( 'error', sprintf(__('Ouch! <strong>Doppler for LearnPress</strong> requires at least <strong>Doppler Forms v%s</strong> to be active. Please <a href="%splugins.php">upgrade</a> Doppler Forms.', 'doppler-for-learnpress'),$this->get_required_doppler_version(),admin_url()));
			$this->deactivate();
		}
	}

	private function deactivate() {
		deactivate_plugins( DOPPLER_FOR_LEARNPRESS_PLUGIN_FILE ); 
	}
	
	private function is_plugin_allowed() {
		$version = get_option('dplr_version');
		if( class_exists('DPLR_Doppler') && class_exists('LearnPress') && version_compare($version, $this->get_required_doppler_version(), '>=') ){
			return true;
	    }
		return false;
	}

	private function write_log($log) {
        if (true === WP_DEBUG) {
            if (is_array($log) || is_object($log)) {
                error_log(print_r($log, true));
            } else {
                error_log($log);
            }
        }
    }

	/**
	 * Set the credentials to doppler service
	 * before running api calls.
	 */
	private function set_credentials(){
		$options = get_option('dplr_settings');
		if ( empty($options) ) {
			return;
		}
		$this->doppler_service->setCredentials(array(	
			'api_key' => $options['dplr_option_apikey'], 
			'user_account' => $options['dplr_option_useraccount'])
		);
	}
	
	/**
	 * Register the admin menu
	 * 
	 * @since 1.0.0
	 */
	public function dplr_init_menu() {
		if($this->is_plugin_allowed()):
			add_submenu_page(
				'doppler_forms_menu',
				__('Doppler for LearnPress', 'dopppler-for-learnpress'),
				__('Doppler for LearnPress', 'doppler-for-learnpress'),
				'manage_options',
				'doppler_learnpress_menu',
				array($this, 'dplr_learnpress_admin')
			);
	    endif;
	}

	/**
	 * Register the admin page
	 * 
	 * @since 1.0.0
	 */
	public function dplr_learnpress_admin() {
		include "partials/doppler-for-learnpress-admin-display.php";
	}

	/**
	 * Synch subscribers
	 * 
	 * @since 1.0.0
	 */
	public function dplr_learnpress_synch() {

		if(empty($_POST['list_id'])) wp_die();
		
		$list_id = intval($_POST['list_id']);		
		$students = $this->get_students();
		//If students if empty dont't synch, but continue to save list.
		if(empty($students)){
			echo json_encode(array('error'=>'1', 'errCode'=>'NoStudentsFound'));
			wp_die();
		}
		$subscriber_resource = $this->doppler_service->getResource( 'subscribers' );
		$this->set_origin();
		$result = $subscriber_resource->importSubscribers( $list_id, $this->get_subscribers_for_import($students) )['body'];
		
		//Synch also mapped courses.

		//Check if course is mapped for registering subscriptions and subscribe.
		$map = get_option('dplr_learnpress_courses_map');
	
		if( !empty($map) ){
			foreach($map as $mapped_course){
					$course_id = $mapped_course['course_id'];
					$students = $this->get_students_from_course ($course_id);
					$result = $subscriber_resource->importSubscribers( $mapped_course['list_id'], $this->get_subscribers_for_import($students) )['body'];
			}
		}
		
		echo $result;
		wp_die();
	}

	function clear_buyers_list() {
		update_option( 'dplr_learnpress_subscribers_list', array('buyers','') );
		wp_die();
	}

	/**
	 * Prepares students array form database result
	 * to be sent to the api in another array.
	 */
	private function get_subscribers_for_import( $students ) {
		return array('items'=> array_map( array($this,'get_student_fields'), $students) , 'fields' => array());
	}

	/**
	 * Extract user email to an array
	 * from learnPress student object
	 * for later use with API
	 */
	private function get_student_fields( $student ) {
		return array( 'email'=>$student->user_email, "fields" => array() );
	}

	/**
	 * Subscribe customer to list after 
	 * course subscription from fromt-end
	 * 
	 * @since 1.0.0
	 */
	/*
	public function dplr_after_customer_subscription( $order_id ) {
		echo 'dplr_after_customer_subscription';
		die();
		$order = new LP_Order( $order_id );
		$lists = get_option('dplr_learnpress_subscribers_list');
		if(!empty($lists)){
			$list_id = $lists['buyers'];
			$order = new LP_Order( $order_id );
			$user_data = get_userdata($order->user_id);
			$user_email = $user_data->data->user_email;
			$this->set_credentials(); 
			$this->subscribe_customer( $list_id, $user_email, array() );
		}
	}*/

	/**
	 * Subscribe customer/s to list after 
	 * course subscription from backend-end or backend.
	 * 
	 * @since 1.0.0
	 */
	public function dplr_after_order_completed( $order_id ) {
		$order = new LP_Order( $order_id );
		$order_items = $order->get_items();

		if(empty($order_items)) return;

		if( $order->has_status( 'completed' ) && !$order->is_child() ){
			$users = get_post_meta( $order_id, '_user_id', true);
			$user_email = get_post_meta( $order_id, '_checkout_email', true);
			if(!empty($users)){

				//Subscribe to global buyers list.
				$lists = get_option('dplr_learnpress_subscribers_list');
				$list_id = $lists['buyers'];
				$this->subscribe_user_or_users($users, $list_id);
				
				//Check if course is mapped for registering subscriptions and subscribe.
				$map = get_option('dplr_learnpress_courses_map');
				if( !empty($map) ){
					foreach($map as $mapped_course){
						foreach($order_items as $k=>$order_item){
							$course_id = $order_item['course_id'];
							if($mapped_course['action_id'] == '1' && $mapped_course['course_id'] == $course_id){
								//Subscribe user or users
								$this->subscribe_user_or_users($users, $mapped_course['list_id']);
							}
						}
					}
				}
			}
			// added else for guests who didn't check the register checkbox, so that they get
			// added to doppler list anyway.
			else{
				$lists = get_option('dplr_learnpress_subscribers_list');
				$list_id = $lists['buyers'];
				$this->subscribe_user_or_users($users, $list_id, $user_email);

				$map = get_option('dplr_learnpress_courses_map');
				if( !empty($map) ){
					foreach($map as $mapped_course){
						foreach($order_items as $k=>$order_item){
							$course_id = $order_item['course_id'];
							if($mapped_course['action_id'] == '1' && $mapped_course['course_id'] == $course_id){
								//Subscribe user or users
								$this->subscribe_user_or_users($users, $mapped_course['list_id'], $user_email);
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Subscribe a single user or multiple users to a list.
	 * @param users | array or int
	 * @param list_id | string
	 */
	private function subscribe_user_or_users($users, $list_id, $email = '') {
		if(is_array($users)){
			foreach($users as $k=>$user_id){
				$user_email = get_userdata($user_id)->data->user_email;
				$this->subscribe_customer( $list_id, $user_email, array() );
			}
		}else{
			// if $users == "0" is for guest users who did not tick the checkbox, meaning they did not choose to register
			// but we need to have them on the Doppler List anyway.
			if($users == "0"){
				$user_email = $email;
				$this->subscribe_customer( $list_id, $user_email, array() );
			}
			else{
				$user_id = $users;
				$user_email = get_userdata($user_id)->data->user_email;
				$this->subscribe_customer( $list_id, $user_email, array() );
			}
		}
	}

	/**
	 * Send email and fields to a Doppler List
	 * 
	 * @since 1.0.0
	 */
	private function subscribe_customer( $list_id, $email, $fields ) {
		if( !empty($list_id) && !empty($email) ){
			$subscriber['email'] = $email;
			$subscriber['fields'] = $fields; 
			$subscriber_resource = $this->doppler_service->getResource('subscribers');
			
			$this->set_credentials();
			
			$this->set_origin();

			$result = $subscriber_resource->addSubscriber($list_id, $subscriber);
			//$this->write_log($result . __LINE__);
		}
	}

	/**
	* Update Subscribers count
	*
	* After synchronizing update 
	* the subscribers counter
	* next to the lists selector.
	* 
	* @since 1.0.0
	*/
	public function update_subscribers_count() {
		$c_count = 0;
		$b_count = 0;
		$list_resource = $this->doppler_service->getResource( 'lists' );

		$b_list_id = get_option('dplr_learnpress_subscribers_list')['buyers'];
		if(!empty($b_list_id)){
			$b_count = $list_resource->getList($b_list_id)->subscribersCount;
		}
		echo json_encode(array('contacts'=>$c_count, 'buyers'=>$b_count));
		exit();
	}

	/**
	 * Get students who
	 * bougth courses and orders 
	 * are completed.
	 * 
	 * @since 1.0.0
	 */
  	private function get_students() {
		global $wpdb;
		$query = "SELECT u.user_email, u.user_nicename FROM $wpdb->posts p 
		JOIN {$wpdb->prefix}postmeta pm ON p.ID = pm.post_id
		JOIN {$wpdb->prefix}users u ON u.id = pm.meta_value
		WHERE p.post_type = 'lp_order'AND
		p.post_status = 'lp-completed' AND
		pm.meta_key = '_user_id'
		GROUP BY u.id
		";
		return  $wpdb->get_results($query);
	}

	/** Get students from a specific course */
	private function get_students_from_course ($course_id) {
		global $wpdb;
		$query = "SELECT u.user_email, u.user_nicename FROM $wpdb->posts p 
		JOIN {$wpdb->prefix}postmeta pm ON p.ID = pm.post_id 
		JOIN {$wpdb->prefix}users u ON u.id = pm.meta_value 
		JOIN {$wpdb->prefix}learnpress_order_items oi ON p.id = oi.order_id 
		JOIN {$wpdb->prefix}learnpress_order_itemmeta oim ON oim.learnpress_order_item_id = oi.order_item_id 
		WHERE p.post_type = 'lp_order' AND p.post_status = 'lp-completed' 
		AND pm.meta_key = '_user_id' AND oim.meta_key = '_course_id' AND oim.meta_value = $course_id
		GROUP BY u.id
		";
		return  $wpdb->get_results($query);
	}

	/**
	 * Get list of published courses
	 * @return object | null
	 */
	private function get_courses(){
		global $wpdb;
		$courses   = $wpdb->get_results(
			$wpdb->prepare(
					"SELECT p.ID, p.post_title FROM $wpdb->posts p
					WHERE post_type = '%s' AND post_status = '%s' ORDER BY p.post_title",
					"lp_course", "publish" 
				)
		);
		return $courses;
	}
	

	public function check_active_list($list_id, $lists) {
		if(!empty($lists) && !empty($list_id)){
			if(!isset($lists[$list_id])){
				$this->set_error_message(__('Ouch! The selected List was deleted from Doppler. Please select another one.', 'doppler-for-learnpress'));
			}
		}
	}

	public function dplr_map_course(){
		if( empty($_POST['course_id']) || empty($_POST['list_id']) || empty($_POST['action_id']) ) return false;
		
		$map = get_option('dplr_learnpress_courses_map');
	
		if($map !== false) $dplr_courses_map = $map;

		$assoc_exists = false;
		foreach($map as $mapped_course){
			if($mapped_course['course_id']==$_POST['course_id'] && $mapped_course['action_id']==$_POST['action_id']){
				$assoc_exists = true;
				break;
			}
		}
		
		//avoid repeated course/action association.
		if($assoc_exists){
			wp_send_json_error(array('error'=>0,'message'=>'Duplicated association'));
		}

		//TODO: action_id is always 1 atm. 
		//a course plus an action have an associated list
		//$dplr_courses_map[][$_POST['course_id']][$_POST['action_id']] = $_POST['list_id'];
		$dplr_courses_map[] = array(
							'course_id'=>$_POST['course_id'],
							'action_id'=>$_POST['action_id'],
							'list_id'=>$_POST['list_id']
						);
		if(update_option( 'dplr_learnpress_courses_map', $dplr_courses_map )){
			//Map, then synch!
			$students = $this->get_students_from_course ($_POST['course_id']);
			$subscriber_resource = $this->doppler_service->getResource('subscribers');
			$result = $subscriber_resource->importSubscribers( $_POST['list_id'], $this->get_subscribers_for_import($students) )['body'];
			wp_send_json_success();
		}
		wp_die();
	}
	/**
	 * Removes Course association from Mapped courses.
	 */
	public function dplr_delete_course_association(){
		if(empty($_POST['association'])) return false;
		$aux = explode('-', $_POST['association']);
		$course_id = $aux[0];
		$action_id = $aux[1];
		
		$map = get_option('dplr_learnpress_courses_map');
		foreach($map as $key => $mapped_course){
			if( $mapped_course['course_id'] == $course_id && $mapped_course['action_id'] == $action_id){
				unset($map[$key]);
			}
		}
		update_option('dplr_learnpress_courses_map', $map);
		echo '1';
		wp_die();
	}

	/**
	 * Get lists
	 * 
	 * @since 1.0.0
	 */
	public function get_alpha_lists() {
		$list_resource = $this->doppler_service->getResource('lists');
		$dplr_lists = $list_resource->getAllLists();
		if( is_array($dplr_lists) && count($dplr_lists)>0 ){
			foreach($dplr_lists as $k=>$v){
			  if(is_array($v)):
				foreach($v as $i=>$j){
				  $dplr_lists_aux[$j->listId] = array('name'=>trim($j->name), 'subscribersCount'=>$j->subscribersCount);
				}
			  endif;
			}
			$dplr_lists_arr = $dplr_lists_aux;
		}
		return $dplr_lists_arr;
	}

	/**
	 * Shows an admin message, 
	 * set $this->admin_notice = array( $class, $text), 
	 * where class is success, warning, etc.
	 * 
	 * @since 1.0.0
	 * 
	 */
	public function show_admin_notice() {
		$class = $this->admin_notice[0];
		$text = $this->admin_notice[1];
		if( !empty($class) && !empty($text) ){
			?>
				<div class="notice notice-<?php echo $class?> is-dismissible">
					<p><?php echo $text ?></p>
				</div>
			<?php
		}
	}

	/**
	 * Validate subscribers lists
	 */
	private function validate_subscribers_list( $list) {
		return is_array($list) && array_key_exists('buyers',$list);
	}

	/**
	 * Sanitize list array
	 */
	private function sanitize_subscribers_list( $list ) {
		return array_filter($list,'is_numeric');
	}

	/**
	 * TODO: Maybe syncrhonize graduated.
	 */
	public function dplr_after_course_finished( $course_id, $user_id, $return ) {
		
	}

	/**
	* Define custom API endpoint
	*/
   public function dplrlp_custom_endpoint( $controllers ) {
	   //Register abadoned cart endpoint.
	   register_rest_route( 'learnpress/v1', 'courses', array(
		   'methods' => 'GET',
		   'callback' => array($this, 'get_published_courses')
	   ));
   }

   public function get_published_courses() {
	$accessToken = get_option('dplrlp_accessToken');
	if( !empty($accessToken) && !empty($_SERVER['PHP_AUTH_PW'])
		&& ($_SERVER['PHP_AUTH_PW'] === $accessToken) ){
		$courses_crud = $this->get_courses();
		foreach($courses_crud as $course){
			$courses[] = array( 'id' => $course->ID,
								'name' => $course->post_title,
								'permalink' => get_permalink($course->ID),
								'featured_image' => get_the_post_thumbnail_url($course->ID), 
								'description' => get_post_field('post_content', $course->ID));
		}
		return $courses;
	}else{
		return array("code"=>"learnpress_rest_cannot_view","message"=>"forbidden","data"=>array("status"=>401));
	}
   }

}