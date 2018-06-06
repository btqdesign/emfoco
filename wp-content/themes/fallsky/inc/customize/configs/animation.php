<?php
/**
* Customize section animation configuration files.
*/


if(!class_exists('Fallsky_Customize_Animation')){
	class Fallsky_Customize_Animation extends Fallsky_Customize_Base {
		public function register_controls($wp_customize){
			global $fallsky_default_settings;

			// Section
			$wp_customize->add_section(new WP_Customize_Section($wp_customize, 'fallsky_section_animation', array(
				'title' 	=> esc_html__('Animations', 'fallsky'),
				'priority' 	=> 70
			)));

			// Settings
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_animation_notes', array(
				'default'  			=> '',
				'transport'			=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_empty'
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_enable_parallax_on_homepage_fullwidth_area', array(
				'default'  			=> $fallsky_default_settings['fallsky_enable_parallax_on_homepage_fullwidth_area'],
				'transport'			=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_checkbox'
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_enable_parallax_on_page_header', array(
				'default'  			=> $fallsky_default_settings['fallsky_enable_parallax_on_page_header'],
				'transport'			=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_checkbox'
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_enable_parallax_on_post_header', array(
				'default'  			=> $fallsky_default_settings['fallsky_enable_parallax_on_post_header'],
				'transport'			=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_checkbox'
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_enable_parallax_on_zigzag_post_archive', array(
				'default'  			=> $fallsky_default_settings['fallsky_enable_parallax_on_zigzag_post_archive'],
				'transport'			=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_checkbox'
			)));

			// Controls
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_animation_notes', array(
				'type' 			=> 'notes',
				'description' 	=> esc_html__('Parallax effect only works when screen width is larger than 1024px.', 'fallsky'),
				'section' 		=> 'fallsky_section_animation',
				'settings' 		=> 'fallsky_animation_notes'
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_enable_parallax_on_homepage_fullwidth_area', array(
				'type' 				=> 'checkbox',
				'label_first'		=> true,
				'label' 			=> esc_html__('Enable parallax effect while scrolling for Homepage Fullwidth Featured Section', 'fallsky'),
				'section' 			=> 'fallsky_section_animation',
				'settings' 			=> 'fallsky_enable_parallax_on_homepage_fullwidth_area'
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_enable_parallax_on_page_header', array(
				'type' 				=> 'checkbox',
				'label_first'		=> true,
				'label' 			=> esc_html__('Enable parallax effect while scrolling for Page Header', 'fallsky'),
				'section' 			=> 'fallsky_section_animation',
				'settings' 			=> 'fallsky_enable_parallax_on_page_header'
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_enable_parallax_on_post_header', array(
				'type' 				=> 'checkbox',
				'label_first'		=> true,
				'label' 			=> esc_html__('Enable parallax effect while scrolling for Post Header', 'fallsky'),
				'section' 			=> 'fallsky_section_animation',
				'settings' 			=> 'fallsky_enable_parallax_on_post_header'
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_enable_parallax_on_zigzag_post_archive', array(
				'type' 				=> 'checkbox',
				'label_first'		=> true,
				'label' 			=> esc_html__('Enable parallax effect while scrolling for ZigZag post archive', 'fallsky'),
				'section' 			=> 'fallsky_section_animation',
				'settings' 			=> 'fallsky_enable_parallax_on_zigzag_post_archive'
			)));
		}
		public function frontend_js_vars($vars = array()){
			$parallax = array();
			if(fallsky_module_enabled('fallsky_enable_parallax_on_homepage_fullwidth_area')){
				array_push($parallax, 'homepage-fullwidth');
			}
			if(fallsky_module_enabled('fallsky_enable_parallax_on_page_header')){
				array_push($parallax, 'page-header');
			}
			if(fallsky_module_enabled('fallsky_enable_parallax_on_post_header')){
				array_push($parallax, 'post-header');
			}
			if(fallsky_module_enabled('fallsky_enable_parallax_on_zigzag_post_archive')){
				array_push($parallax, 'post-list-zigzag');
			}

			return array_merge($vars, array('parallax_effect' => $parallax));
		}
	}
	new Fallsky_Customize_Animation();
}