<?php
/**
* Customize section site-identity configuration files.
*/

if(!class_exists('Fallsky_Customize_Site_Identity')){
	class Fallsky_Customize_Site_Identity extends Fallsky_Customize_Base {
		public function register_controls($wp_customize){
			global $fallsky_default_settings;

			$wp_customize->get_section('title_tagline')->priority 		= 0;
			$wp_customize->get_setting('blogname')->transport 			= 'postMessage';
			$wp_customize->get_setting('blogdescription')->transport  	= 'postMessage';

			if(!empty($wp_customize->selective_refresh) && ($wp_customize->selective_refresh instanceof WP_Customize_Selective_Refresh)){
				$wp_customize->get_setting('custom_logo')->transport = 'postMessage';
				$wp_customize->selective_refresh->remove_partial('custom_logo');
				$wp_customize->selective_refresh->add_partial('custom_logo', array(
					'settings' 				=> array('custom_logo', 'fallsky_site_logo_width', 'fallsky_transparent_site_header_logo'),
					'selector' 				=> '.custom-logo-link',
					'render_callback' 		=> 'fallsky_the_custom_logo',
					'container_inclusive' 	=> true,
				));
			}

			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_site_logo_width', array(
				'default'   		=> $fallsky_default_settings['fallsky_site_logo_width'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'absint',
				'dependency' 		=> array(
					'custom_logo' => array('value' => array(''), 'operator' => 'not in')
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_transparent_site_header_logo', array(
				'default'   		=> $fallsky_default_settings['fallsky_transparent_site_header_logo'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'absint',
				'dependency' 		=> array(
					'custom_logo' => array('value' => array(''), 'operator' => 'not in')
				)
			)));

			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_site_logo_width', array(
				'type' 				=> 'number_with_unit',
				'priority' 			=> 9,
				'label' 			=> esc_html__('Logo Width', 'fallsky'),
				'after_text' 		=> 'px',
				'section' 			=> 'title_tagline',
				'settings' 			=> 'fallsky_site_logo_width',
				'active_callback' 	=> 'fallsky_customize_control_active_cb'
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_transparent_site_header_logo', array(
				'type' 				=> 'image_id',
				'priority' 			=> 9,
				'label' 			=> esc_html__('Logo for transparent site header (optional)', 'fallsky'),
				'section' 			=> 'title_tagline',
				'settings' 			=> 'fallsky_transparent_site_header_logo',
				'active_callback' 	=> 'fallsky_customize_control_active_cb'
			)));
		}
	}
	new Fallsky_Customize_Site_Identity();
}