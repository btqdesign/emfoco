<?php
/**
* Customize panel sidebar configuration files.
*/

if(!class_exists('Fallsky_Customize_Sidebar')){
	class Fallsky_Customize_Sidebar extends Fallsky_Customize_Base {
		public function register_controls($wp_customize){
			global $fallsky_default_settings;

			// Section
			$wp_customize->add_section('fallsky_section_sidebar', array(
				'title'    => esc_html__('Sidebar', 'fallsky'),
				'priority' => 30
			));

			// Settings
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_sidebar_enable_sticky', array(
				'default'   		=> $fallsky_default_settings['fallsky_sidebar_enable_sticky'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_checkbox'
			)));

			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_sidebar_design_options_group', array(
				'default'   		=> '',
				'sanitize_callback' => 'fallsky_sanitize_empty'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_sidebar_widgets_style', array(
				'default'  			=> $fallsky_default_settings['fallsky_sidebar_widgets_style'],
				'transport'			=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_sidebar_widgets_bg_color', array(
				'default'   		=> $fallsky_default_settings['fallsky_sidebar_widgets_bg_color'],
				'transport'			=> 'postMessage',
				'sanitize_callback' => 'sanitize_hex_color',
				'dependency'		=> array(
					'fallsky_sidebar_widgets_style' => array('value' => array('with-bg'))
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_sidebar_widgets_border_color', array(
				'default'   		=> $fallsky_default_settings['fallsky_sidebar_widgets_border_color'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'sanitize_hex_color',
				'dependency'		=> array(
					'fallsky_sidebar_widgets_style' => array('value' => array('with-border'))
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_sidebar_widgets_text_color', array(
				'default'   		=> $fallsky_default_settings['fallsky_sidebar_widgets_text_color'],
				'transport'			=> 'postMessage',
				'sanitize_callback' => 'sanitize_hex_color',
				'dependency'		=> array(
					'fallsky_sidebar_widgets_style' => array('value' => array('with-bg', 'with-border'))
				)
			)));

			// Controls
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_sidebar_enable_sticky', array(
				'type' 			=> 'checkbox',
				'label_first'	=> true,
				'label' 		=> esc_html__('Enable Sticky Sidebar', 'fallsky'),
				'description' 	=> esc_html__('Only works when sidebar is shorter than content area and when screen width is larger than 1120 px.', 'fallsky'),
				'section' 		=> 'fallsky_section_sidebar',
				'settings' 		=> 'fallsky_sidebar_enable_sticky'
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_sidebar_design_options_group', array(
				'type' 		=> 'group',
				'label' 	=> esc_html__('Design Options', 'fallsky'),
				'section' 	=> 'fallsky_section_sidebar',
				'settings' 	=> 'fallsky_sidebar_design_options_group',
				'children' 	=> array(
					new Fallsky_Customize_Control($wp_customize, 'fallsky_sidebar_widgets_style', array(
						'type'		=> 'select',
						'label' 	=> esc_html__('Widgets Style', 'fallsky'),
						'section'  	=> 'fallsky_section_sidebar',
						'settings' 	=> 'fallsky_sidebar_widgets_style',
						'choices'	=> array(
							'' 				=> esc_html__('Default', 'fallsky'),
							'with-border'	=> esc_html__('With Border', 'fallsky'),
							'with-bg'		=> esc_html__('With Background', 'fallsky')
						)
					)),
					new WP_Customize_Color_Control($wp_customize, 'fallsky_sidebar_widgets_bg_color', array(
						'label' 			=> esc_html__('Widget Background Color', 'fallsky'),
						'section'  			=> 'fallsky_section_sidebar',
						'settings' 			=> 'fallsky_sidebar_widgets_bg_color',
						'active_callback' 	=> 'fallsky_customize_control_active_cb'
					)),
					new WP_Customize_Color_Control($wp_customize, 'fallsky_sidebar_widgets_border_color', array(
						'label' 			=> esc_html__('Widget Border Color', 'fallsky'),
						'section'  			=> 'fallsky_section_sidebar',
						'settings' 			=> 'fallsky_sidebar_widgets_border_color',
						'active_callback' 	=> 'fallsky_customize_control_active_cb'
					)),
					new WP_Customize_Color_Control($wp_customize, 'fallsky_sidebar_widgets_text_color', array(
						'label' 			=> esc_html__('Widget Text Color', 'fallsky'),
						'section'  			=> 'fallsky_section_sidebar',
						'settings' 			=> 'fallsky_sidebar_widgets_text_color',
						'active_callback' 	=> 'fallsky_customize_control_active_cb'
					))
				)
			)));
		}
		public function get_custom_styles(){
			$styles = array();
			
			$styles['site-sidebar-bg-color'] = fallsky_get_style(
				'fallsky_sidebar_widgets_bg_color',
				'#secondary.sidebar.with-bg .widget',
				'background-color: %s;'
			);
			$styles['site-sidebar-border-color'] = fallsky_get_style(
				'fallsky_sidebar_widgets_border_color',
				'#secondary.sidebar.with-border .widget',
				'border-color: %s;'
			);
			$styles['site-sidebar-text-color'] = fallsky_get_style(
				'fallsky_sidebar_widgets_text_color',
				'#secondary.sidebar.with-bg .widget, #secondary.sidebar.with-border .widget',
				'color: %s;'
			);

			return $styles;
		}
		public function frontend_actions(){
			add_filter('fallsky_show_site_sidebar',		array($this, 'show_site_sidebar'));
			add_action('fallsky_site_sidebar_class', 	array($this, 'site_sidebar_class'));
		}
		public function show_site_sidebar($show = true){
			global $fallsky_is_preview;
			$layout = fallsky_get_page_layout();
			return !empty($layout) || $fallsky_is_preview;	
		}
		public function site_sidebar_class($class){
			global $fallsky_is_preview;
			$layout = fallsky_get_page_layout();
			$style 	= esc_attr(fallsky_get_theme_mod('fallsky_sidebar_widgets_style'));
			empty($style) ? '' : array_push($class, $style);
			empty($layout) && $fallsky_is_preview ? array_push($class, 'hide') : '';

			return $class;
		}
	}
	new Fallsky_Customize_Sidebar();
}