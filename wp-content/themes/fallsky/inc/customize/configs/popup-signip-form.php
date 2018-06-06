<?php
/**
* Customize section header configuration files.
*/

if(!class_exists('Fallsky_Customize_Popup_Signup_Form')){
	class Fallsky_Customize_Popup_Signup_Form extends Fallsky_Customize_Base {
		public function register_controls($wp_customize){
			global $fallsky_default_settings;

			// Sections
			$wp_customize->add_section('fallsky_section_popup_signup_form', array(
				'title'		=> esc_html__('Popup Signup Form', 'fallsky'),
				'priority' 	=> 75
			));

			// Setting
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_popup_signup_form_code', array(
				'default'   		=> $fallsky_default_settings['fallsky_popup_signup_form_code'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_html'
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_popup_signup_form_note', array(
				'default'   		=> '',
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_empty'
			)));

			// Control
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_popup_signup_form_code', array(
				'type' 			=> 'textarea',
				'label' 		=> '',
				'section' 		=> 'fallsky_section_popup_signup_form',
				'settings' 		=> 'fallsky_popup_signup_form_code',
				'description'	=> sprintf(
					esc_html__('We support MailChimp\'s default functionality to add a popup signup form to your site. Please log into your MailChimp account and read %sthis article%s to know how to create a popup signup form. And then copy and paste the form code into the box below:', 'fallsky'),
					'<a href="https://kb.mailchimp.com/lists/signup-forms/add-a-pop-up-signup-form-to-your-website" target="_blank">',
					'</a>'
				)
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_popup_signup_form_note', array(
				'type' 			=> 'notes',
				'label' 		=> '',
				'description' 	=> esc_html__('Please note, the form will only pop up in the front-end. It will not show in the preview area.', 'fallsky'),
				'section' 		=> 'fallsky_section_popup_signup_form',
				'settings' 		=> 'fallsky_popup_signup_form_note'
			)));
		}
		public function frontend_actions(){
			add_action('wp_footer', array($this, 'signup_form'), 9999);
		}
		public function signup_form(){
			global $fallsky_is_preview;
			$code = fallsky_get_theme_mod('fallsky_popup_signup_form_code');
			if(!empty($code) && !$fallsky_is_preview){
				print($code);
			}
		}
	}
	new Fallsky_Customize_Popup_Signup_Form();
}