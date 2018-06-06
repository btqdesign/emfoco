<?php
/**
* Customize section advanced configuration files.
*/


if(!class_exists('Fallsky_Customize_Advanced')){
	class Fallsky_Customize_Advanced extends Fallsky_Customize_Base {
		public function __construct(){
			parent::__construct();
			add_action('wp_ajax_fallsky_sync_post_view', array($this, 'sync_view'));
		}
		public function register_controls($wp_customize){
			if(function_exists('stats_get_from_restapi')){
				// Sections
				$wp_customize->add_section('fallsky_section_advanced', array(
					'title'		=> esc_html__('Advanced', 'fallsky'),
					'priority'	=> 80				
				));

				// Settings
				$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_sync_jetpack_view', array(
					'default' 			=> esc_html__('Start Sync', 'fallsky'),
					'transport' 		=> 'postMessage',
					'sanitize_callback' => 'fallsky_sanitize_empty'
				)));

				// Controls
				$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_sync_jetpack_view', array(
					'type' 			=> 'button',
					'label' 		=> esc_html__('Synchronize the data of post views from JetPack to your website', 'fallsky'),
					'description'	=> esc_html__('Only works when Jetpack is installed and connected to a WordPress.com User ID.', 'fallsky'),
					'section' 		=> 'fallsky_section_advanced',
					'settings' 		=> 'fallsky_sync_jetpack_view',
					'input_attrs'	=> array('action' => 'fallsky_sync_post_view', 'nonce' => wp_create_nonce('fallsky_sync_post_view'))
				)));
			}
		}
		public function sync_view(){
			if(function_exists('stats_get_from_restapi') && wp_verify_nonce($_POST['nonce'], 'fallsky_sync_post_view')){
				$posts = get_posts(array('numberposts' => -1, 'post_type' => 'post'));
				$args = array('days' => -1, 'limit' => -1, 'post_id' => 0);
				if($posts){
					delete_option('stats_cache');
					foreach($posts as $p){
						$pid 	= $p->ID;
						$stat 	= stats_get_from_restapi(array('fields' => 'views'), 'post/' . $pid);
						if(!empty($stat) && !empty($stat->views)){
							update_post_meta($pid, 'loftocean-view-count', intval($stat->views));
						}
					}
				}
				wp_send_json_success('sync done');
			}
			wp_send_json_fail('JetPack not enabled');
		}
		public function customize_js_vars($vars = array()) { 
			return array_merge($vars, array(
				'sync_message' => array(
					'sending'	=> esc_html__('Data is syncing. Please wait. It can take a couple of minutes.', 'fallsky'),
					'done' 		=> esc_html__('Congratulations! Sync is completed.', 'fallsky'),
					'fail'		=> esc_html__('Sorry but unable to sync. Please try again later.', 'fallsky')
				)
			)); 
		}
	}
	new Fallsky_Customize_Advanced();
}