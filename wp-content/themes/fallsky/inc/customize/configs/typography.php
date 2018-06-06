<?php
/**
* Customize panel typography configuration files.
*/


if(!class_exists('Fallsky_Customize_Typography')){
	class Fallsky_Customize_Typography extends Fallsky_Customize_Base {
		private $setting_keys = array();
		public function register_controls($wp_customize){
			global $fallsky_default_settings;
			require_once FALLSKY_THEME_INC . 'customize/configs/font-family.php';

			// Panel
			$wp_customize->add_panel(new WP_Customize_Panel($wp_customize, 'fallsky_panel_typography', array(
				'title' 	=> esc_html__('Typography', 'fallsky'),
				'priority' 	=> 60
			)));

			// Sections
			$wp_customize->add_section('fallsky_section_typography_font_family', array(
				'title' => esc_html__('Font Family', 'fallsky'),
				'panel' => 'fallsky_panel_typography'
			));
			$wp_customize->add_section('fallsky_section_typography_heading', array(
				'title' => esc_html__('Heading Text', 'fallsky'),
				'panel' => 'fallsky_panel_typography'
			));
			$wp_customize->add_section('fallsky_section_typography_content', array(
				'title' => esc_html__('Content Text', 'fallsky'),
				'panel' => 'fallsky_panel_typography'
			));
			$wp_customize->add_section('fallsky_section_typography_post_title', array(
				'title' => esc_html__('Post Title', 'fallsky'),
				'panel' => 'fallsky_panel_typography'
			));
			$wp_customize->add_section('fallsky_section_typography_page_title', array(
				'title' => esc_html__('Page Title', 'fallsky'),
				'panel' => 'fallsky_panel_typography'
			));
			$wp_customize->add_section('fallsky_section_typography_widget_title', array(
				'title' => esc_html__('Section & Widget Title', 'fallsky'),
				'panel' => 'fallsky_panel_typography'
			));
			$wp_customize->add_section('fallsky_section_typography_category_links', array(
				'title' => esc_html__('Category Links', 'fallsky'),
				'panel' => 'fallsky_panel_typography'
			));

			// Settings
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_typography_text_font-family', array(
				'default'   		=> $fallsky_default_settings['fallsky_typography_text_font-family'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_typography_heading_font-family', array(
				'default'   		=> $fallsky_default_settings['fallsky_typography_heading_font-family'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			)));

			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_typography_heading_font-weight', array(
				'default'  			=> $fallsky_default_settings['fallsky_typography_heading_font-weight'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_typography_heading_letter-spacing', array(
				'default'   		=> $fallsky_default_settings['fallsky_typography_heading_letter-spacing'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_typography_heading_text-transform', array(
				'default'  	 		=> $fallsky_default_settings['fallsky_typography_heading_text-transform'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_typography_heading_font-style', array(
				'default'  		 	=> $fallsky_default_settings['fallsky_typography_heading_font-style'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_typography_heading_line-height', array(
				'default'   		=> $fallsky_default_settings['fallsky_typography_heading_line-height'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			)));

			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_typography_content_font-size', array(
				'default'   		=> $fallsky_default_settings['fallsky_typography_content_font-size'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'absint'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_typography_content_line-height', array(
				'default'   		=> $fallsky_default_settings['fallsky_typography_content_line-height'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			)));

			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_typography_default_group', array(
				'default'   		=> '',
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_empty'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_typography_post_title_font-weight', array(
				'default'   		=> $fallsky_default_settings['fallsky_typography_post_title_font-weight'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_typography_post_title_letter-spacing', array(
				'default'   		=> $fallsky_default_settings['fallsky_typography_post_title_letter-spacing'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_typography_post_title_text-transform', array(
				'default'   		=> $fallsky_default_settings['fallsky_typography_post_title_text-transform'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_typography_post_title_font-style', array(
				'default'   		=> $fallsky_default_settings['fallsky_typography_post_title_font-style'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			)));

			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_typography_fullwidth_group', array(
				'default'   		=> '',
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_empty'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_typography_fullwidth_post_title_style', array(
				'default'   		=> $fallsky_default_settings['fallsky_typography_fullwidth_post_title_style'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_typography_fullwidth_post_title_font-weight', array(
				'default'   		=> $fallsky_default_settings['fallsky_typography_fullwidth_post_title_font-weight'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice',
				'dependency'		=> array(
					'fallsky_typography_fullwidth_post_title_style' => array('value' => array('custom'))
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_typography_fullwidth_post_title_letter-spacing', array(
				'default'   		=> $fallsky_default_settings['fallsky_typography_fullwidth_post_title_letter-spacing'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice',
				'dependency'		=> array(
					'fallsky_typography_fullwidth_post_title_style' => array('value' => array('custom'))
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_typography_fullwidth_post_title_text-transform', array(
				'default'   		=> $fallsky_default_settings['fallsky_typography_fullwidth_post_title_text-transform'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice',
				'dependency'		=> array(
					'fallsky_typography_fullwidth_post_title_style' => array('value' => array('custom'))
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_typography_fullwidth_post_title_font-style', array(
				'default'   		=> $fallsky_default_settings['fallsky_typography_fullwidth_post_title_font-style'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice',
				'dependency'		=> array(
					'fallsky_typography_fullwidth_post_title_style' => array('value' => array('custom'))
				)
			)));

			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_typography_archive_group', array(
				'default'   		=> '',
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_empty'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_typography_archive_post_title_style', array(
				'default'   		=> $fallsky_default_settings['fallsky_typography_archive_post_title_style'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_typography_archive_post_title_font-weight', array(
				'default'   		=> $fallsky_default_settings['fallsky_typography_archive_post_title_font-weight'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice',
				'dependency'		=> array(
					'fallsky_typography_archive_post_title_style' => array('value' => array('custom'))
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_typography_archive_post_title_letter-spacing', array(
				'default'   		=> $fallsky_default_settings['fallsky_typography_archive_post_title_letter-spacing'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice',
				'dependency'		=> array(
					'fallsky_typography_archive_post_title_style' => array('value' => array('custom'))
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_typography_archive_post_title_text-transform', array(
				'default'   		=> $fallsky_default_settings['fallsky_typography_archive_post_title_text-transform'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice',
				'dependency'		=> array(
					'fallsky_typography_archive_post_title_style' => array('value' => array('custom'))
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_typography_archive_post_title_font-style', array(
				'default'   		=> $fallsky_default_settings['fallsky_typography_archive_post_title_font-style'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice',
				'dependency'		=> array(
					'fallsky_typography_archive_post_title_style' => array('value' => array('custom'))
				)
			)));

			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_typography_single_group', array(
				'default'   		=> '',
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_empty'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_typography_single_post_title_style', array(
				'default'   		=> $fallsky_default_settings['fallsky_typography_single_post_title_style'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_typography_single_post_title_font-weight', array(
				'default'   		=> $fallsky_default_settings['fallsky_typography_single_post_title_font-weight'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice',
				'dependency'		=> array(
					'fallsky_typography_single_post_title_style' => array('value' => array('custom'))
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_typography_single_post_title_letter-spacing', array(
				'default'   		=> $fallsky_default_settings['fallsky_typography_single_post_title_letter-spacing'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice',
				'dependency'		=> array(
					'fallsky_typography_single_post_title_style' => array('value' => array('custom'))
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_typography_single_post_title_text-transform', array(
				'default'   		=> $fallsky_default_settings['fallsky_typography_single_post_title_text-transform'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice',
				'dependency'		=> array(
					'fallsky_typography_single_post_title_style' => array('value' => array('custom'))
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_typography_single_post_title_font-style', array(
				'default'   		=> $fallsky_default_settings['fallsky_typography_single_post_title_font-style'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice',
				'dependency'		=> array(
					'fallsky_typography_single_post_title_style' => array('value' => array('custom'))
				)
			)));

			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_typography_widget_area_group', array(
				'default'   		=> '',
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_empty'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_typography_widget_area_title_style', array(
				'default'   		=> $fallsky_default_settings['fallsky_typography_widget_area_title_style'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_typography_widget_area_title_font-weight', array(
				'default'   		=> $fallsky_default_settings['fallsky_typography_widget_area_title_font-weight'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice',
				'dependency'		=> array(
					'fallsky_typography_widget_area_title_style' => array('value' => array('custom'))
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_typography_widget_area_title_letter-spacing', array(
				'default'   		=> $fallsky_default_settings['fallsky_typography_widget_area_title_letter-spacing'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice',
				'dependency'		=> array(
					'fallsky_typography_widget_area_title_style' => array('value' => array('custom'))
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_typography_widget_area_title_text-transform', array(
				'default'   		=> $fallsky_default_settings['fallsky_typography_widget_area_title_text-transform'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice',
				'dependency'		=> array(
					'fallsky_typography_widget_area_title_style' => array('value' => array('custom'))
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_typography_widget_area_title_font-style', array(
				'default'   		=> $fallsky_default_settings['fallsky_typography_widget_area_title_font-style'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice',
				'dependency'		=> array(
					'fallsky_typography_widget_area_title_style' => array('value' => array('custom'))
				)
			)));

			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_typography_page_title_font-weight', array(
				'default'   		=> $fallsky_default_settings['fallsky_typography_page_title_font-weight'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_typography_page_title_letter-spacing', array(
				'default'   		=> $fallsky_default_settings['fallsky_typography_page_title_letter-spacing'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_typography_page_title_text-transform', array(
				'default'   		=> $fallsky_default_settings['fallsky_typography_page_title_text-transform'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_typography_page_title_font-style', array(
				'default'   		=> $fallsky_default_settings['fallsky_typography_page_title_font-style'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			)));

			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_typography_section_widget_title_font-weight', array(
				'default'   		=> $fallsky_default_settings['fallsky_typography_section_widget_title_font-weight'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_typography_section_widget_title_letter-spacing', array(
				'default'   		=> $fallsky_default_settings['fallsky_typography_section_widget_title_letter-spacing'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_typography_section_widget_title_text-transform', array(
				'default'   		=> $fallsky_default_settings['fallsky_typography_section_widget_title_text-transform'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_typography_section_widget_title_font-style', array(
				'default'  	 		=> $fallsky_default_settings['fallsky_typography_section_widget_title_font-style'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_typography_hide_widget_title_decor', array(
				'default'  	 		=> $fallsky_default_settings['fallsky_typography_hide_widget_title_decor'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_checkbox'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_typography_hide_section_title_decor', array(
				'default'  	 		=> $fallsky_default_settings['fallsky_typography_hide_section_title_decor'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_checkbox'
			)));


			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_typography_category_links_font', array(
				'default'  	 		=> $fallsky_default_settings['fallsky_typography_category_links_font'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_typography_category_links_color', array(
				'default'  	 		=> $fallsky_default_settings['fallsky_typography_category_links_color'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_choice',
				'dependency'		=> array(
					'fallsky_accent_color' => array('value' => array('custom'))
				)
			)));

			// Controls
			$wp_customize->add_control(new WP_Customize_Control($wp_customize, 'fallsky_typography_text_font-family', array(
				'type' 		=> 'select',
				'label' 	=> esc_html__('Text Font Family', 'fallsky'),
				'choices' 	=> $fallsky_google_fonts,
				'section' 	=> 'fallsky_section_typography_font_family',
				'settings'	=> 'fallsky_typography_text_font-family'
			)));
			$wp_customize->add_control(new WP_Customize_Control($wp_customize, 'fallsky_typography_heading_font-family', array(
				'type' 		=> 'select',
				'label' 	=> esc_html__('Heading Font Family', 'fallsky'),
				'choices' 	=> $fallsky_google_fonts,
				'section' 	=> 'fallsky_section_typography_font_family',
				'settings' 	=> 'fallsky_typography_heading_font-family'
			)));

			$wp_customize->add_control(new WP_Customize_Control($wp_customize, 'fallsky_typography_heading_font-weight', array(
				'type' 			=> 'select',
				'label' 		=> esc_html__('Font Weight', 'fallsky'),
				'description' 	=> esc_html__('Please note: not every font supports all the font weight values listed.', 'fallsky'),
				'section' 		=> 'fallsky_section_typography_heading',
				'settings' 		=> 'fallsky_typography_heading_font-weight',				
				'choices' 		=> array(
					'100' => 100,
					'200' => 200,
					'300' => 300,
					'400' => 400,
					'500' => 500,
					'600' => 600,
					'700' => 700,
					'800' => 800,
				),
			)));
			$wp_customize->add_control(new WP_Customize_Control($wp_customize, 'fallsky_typography_heading_letter-spacing', array(
				'type' 		=> 'select',
				'label' 	=> esc_html__('Letter Spacing', 'fallsky'),
				'section' 	=> 'fallsky_section_typography_heading',
				'settings' 	=> 'fallsky_typography_heading_letter-spacing',
				'choices' 	=> array(
					'0' 		=> '0em',
					'0.05em' 	=> '0.05em',
					'0.1em' 	=> '0.1em',
					'0.15em' 	=> '0.15em',
					'0.2em' 	=> '0.2em',
					'0.25em' 	=> '0.25em',
					'0.3em' 	=> '0.3em',
					'0.35em' 	=> '0.35em',
					'0.4em' 	=> '0.4em'
				)
			)));
			$wp_customize->add_control(new WP_Customize_Control($wp_customize, 'fallsky_typography_heading_text-transform', array(
				'type' 		=> 'select',
				'label' 	=> esc_html__('Text Transform', 'fallsky'),
				'section' 	=> 'fallsky_section_typography_heading',
				'settings' 	=> 'fallsky_typography_heading_text-transform',
				'choices' 	=> array(
					'none' 			=> esc_html__('None', 'fallsky'),
					'uppercase' 	=> esc_html__('Uppercase', 'fallsky'),
					'lowercase' 	=> esc_html__('Lowercase', 'fallsky'),
					'capitalize' 	=> esc_html__('Capitalize', 'fallsky')
				)
			)));
			$wp_customize->add_control(new WP_Customize_Control($wp_customize, 'fallsky_typography_heading_font-style', array(
				'type' 		=> 'select',
				'label' 	=> esc_html__('Style', 'fallsky'),
				'section' 	=> 'fallsky_section_typography_heading',
				'settings' 	=> 'fallsky_typography_heading_font-style',
				'choices' 	=> array(
					'normal' => esc_html__('Normal', 'fallsky'),
					'italic' => esc_html__('Italic', 'fallsky')
				)
			)));
			$wp_customize->add_control(new WP_Customize_Control($wp_customize, 'fallsky_typography_heading_line-height', array(
				'type' 		=> 'select',
				'label' 	=> esc_html__('Line Height', 'fallsky'),
				'section' 	=> 'fallsky_section_typography_heading',
				'settings' 	=> 'fallsky_typography_heading_line-height',
				'choices' 	=> array(
					'1.3' 	=> '1.3',
					'1.35' 	=> '1.35',
					'1.4' 	=> '1.4',
					'1.45' 	=> '1.45',
					'1.5' 	=> '1.5',
					'1.55' 	=> '1.55',
					'1.6' 	=> '1.6',
					'1.65' 	=> '1.65',
					'1.7' 	=> '1.7',
					'1.75' 	=> '1.75',
					'1.8' 	=> '1.8',
					'1.85' 	=> '1.85',
					'1.9' 	=> '1.9',
					'1.95' 	=> '1.95',
					'2' 	=> '2',
					'2.05' 	=> '2.05',
					'2.1' 	=> '2.1',
					'2.15' 	=> '2.15',
					'2.2' 	=> '2.2',
					'2.25' 	=> '2.25',
					'2.3' 	=> '2.3',
					'2.35' 	=> '2.35',
					'2.4' 	=> '2.4',
					'2.45' 	=> '2.45',
					'2.5' 	=> '2.5',
					'2.55' 	=> '2.55',
					'2.6' 	=> '2.6',
					'2.65' 	=> '2.65',
					'2.7' 	=> '2.7',
					'2.75' 	=> '2.75',
					'2.8' 	=> '2.8',
					'2.85' 	=> '2.85',
					'2.9' 	=> '2.9',
					'2.95' 	=> '2.95',
					'3' 	=> '3',
				)
			)));

			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_typography_content_font-size', array(
				'type' 			=> 'number_with_unit',
				'label' 		=> esc_html__('Font Size', 'fallsky'),
				'after_text' 	=> 'px',
				'section' 		=> 'fallsky_section_typography_content',
				'settings' 		=> 'fallsky_typography_content_font-size',
				'input_attrs' 	=> array('min' => 10, 'max' => 30)
			)));
			$wp_customize->add_control(new WP_Customize_Control($wp_customize, 'fallsky_typography_content_line-height', array(
				'type' 		=> 'select',
				'label' 	=> esc_html__('Line Height', 'fallsky'),
				'section' 	=> 'fallsky_section_typography_content',
				'settings' 	=> 'fallsky_typography_content_line-height',
				'choices' 	=> array(
					'1.3' 	=> '1.3',
					'1.35' 	=> '1.35',
					'1.4' 	=> '1.4',
					'1.45' 	=> '1.45',
					'1.5' 	=> '1.5',
					'1.55' 	=> '1.55',
					'1.6' 	=> '1.6',
					'1.65' 	=> '1.65',
					'1.7' 	=> '1.7',
					'1.75' 	=> '1.75',
					'1.8' 	=> '1.8',
					'1.85' 	=> '1.85',
					'1.9' 	=> '1.9',
					'1.95' 	=> '1.95',
					'2' 	=> '2',
					'2.05' 	=> '2.05',
					'2.1' 	=> '2.1',
					'2.15' 	=> '2.15',
					'2.2' 	=> '2.2',
					'2.25' 	=> '2.25',
					'2.3' 	=> '2.3',
					'2.35' 	=> '2.35',
					'2.4' 	=> '2.4',
					'2.45' 	=> '2.45',
					'2.5' 	=> '2.5',
					'2.55' 	=> '2.55',
					'2.6' 	=> '2.6',
					'2.65' 	=> '2.65',
					'2.7' 	=> '2.7',
					'2.75' 	=> '2.75',
					'2.8' 	=> '2.8',
					'2.85' 	=> '2.85',
					'2.9' 	=> '2.9',
					'2.95' 	=> '2.95',
					'3' 	=> '3',
				)
			)));

			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_typography_default_group', array(
				'type' 			=> 'group',
				'label' 		=> esc_html__('Default', 'fallsky'),
				'section' 		=> 'fallsky_section_typography_post_title',
				'settings' 		=> 'fallsky_typography_default_group',
				'children'		=> array(
					new WP_Customize_Control($wp_customize, 'fallsky_typography_post_title_font-weight', array(
						'type' 			=> 'select',
						'label' 		=> esc_html__('Font Weight', 'fallsky'),
						'description' 	=> esc_html__('Please note: not every font supports all the font weight values listed.', 'fallsky'),
						'section' 		=> 'fallsky_section_typography_post_title',
						'settings' 		=> 'fallsky_typography_post_title_font-weight',
						'choices' 		=> array(
							'100' => 100,
							'200' => 200,
							'300' => 300,
							'400' => 400,
							'500' => 500,
							'600' => 600,
							'700' => 700,
							'800' => 800,
						)
					)),
					new WP_Customize_Control($wp_customize, 'fallsky_typography_post_title_letter-spacing', array(
						'type' 		=> 'select',
						'label' 	=> esc_html__('Letter Spacing', 'fallsky'),
						'section'	=> 'fallsky_section_typography_post_title',
						'settings' 	=> 'fallsky_typography_post_title_letter-spacing',
						'choices' 	=> array(
							'0' 		=> '0em',
							'0.05em' 	=> '0.05em',
							'0.1em' 	=> '0.1em',
							'0.15em' 	=> '0.15em',
							'0.2em'	 	=> '0.2em',
							'0.25em' 	=> '0.25em',
							'0.3em' 	=> '0.3em',
							'0.35em' 	=> '0.35em',
							'0.4em' 	=> '0.4em'
						)
					)),
					new WP_Customize_Control($wp_customize, 'fallsky_typography_post_title_text-transform', array(
						'type' 		=> 'select',
						'label' 	=> esc_html__('Text Transform', 'fallsky'),
						'section' 	=> 'fallsky_section_typography_post_title',
						'settings' 	=> 'fallsky_typography_post_title_text-transform',
						'choices' 	=> array(
							'none' 			=> esc_html__('None', 'fallsky'),
							'uppercase' 	=> esc_html__('Uppercase', 'fallsky'),
							'lowercase' 	=> esc_html__('Lowercase', 'fallsky'),
							'capitalize' 	=> esc_html__('Capitalize', 'fallsky')
						)
					)),
					new WP_Customize_Control($wp_customize, 'fallsky_typography_post_title_font-style', array(
						'type' 		=> 'select',
						'label' 	=> esc_html__('Style', 'fallsky'),
						'section' 	=> 'fallsky_section_typography_post_title',
						'settings' 	=> 'fallsky_typography_post_title_font-style',
						'choices' 	=> array(
							'normal' => esc_html__('Normal', 'fallsky'),
							'italic' => esc_html__('Italic', 'fallsky')
						)
					))
				)
			)));
			
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_typography_fullwidth_group', array(
				'type' 		=> 'group',
				'label' 	=> esc_html__('Fullwidth Featured Area', 'fallsky'),
				'section' 	=> 'fallsky_section_typography_post_title',
				'settings' 	=> 'fallsky_typography_fullwidth_group',
				'children'	=> array(
					new WP_Customize_Control($wp_customize, 'fallsky_typography_fullwidth_post_title_style', array(
						'type' 		=> 'select',
						'label' 	=> '',
						'section' 	=> 'fallsky_section_typography_post_title',
						'settings' 	=> 'fallsky_typography_fullwidth_post_title_style',
						'choices' 	=> array(
							'default' 	=> esc_html__('Default', 'fallsky'),
							'custom'	=> esc_html__('Custom', 'fallsky')
						)
					)),
					new Fallsky_Customize_Control($wp_customize, 'fallsky_typography_fullwidth_post_title_font-weight', array(
						'type' 				=> 'select',
						'label' 			=> esc_html__('Font Weight', 'fallsky'),
						'description' 		=> esc_html__('Please note: not every font supports all the font weight values listed.', 'fallsky'),
						'section' 			=> 'fallsky_section_typography_post_title',
						'settings' 			=> 'fallsky_typography_fullwidth_post_title_font-weight',
						'active_callback' 	=> 'fallsky_customize_control_active_cb',
						'choices' 			=> array(
							'100' => 100,
							'200' => 200,
							'300' => 300,
							'400' => 400,
							'500' => 500,
							'600' => 600,
							'700' => 700,
							'800' => 800,
						)
					)),
					new WP_Customize_Control($wp_customize, 'fallsky_typography_fullwidth_post_title_letter-spacing', array(
						'type' 				=> 'select',
						'label' 			=> esc_html__('Letter Spacing', 'fallsky'),
						'section'			=> 'fallsky_section_typography_post_title',
						'settings' 			=> 'fallsky_typography_fullwidth_post_title_letter-spacing',
						'active_callback' 	=> 'fallsky_customize_control_active_cb',
						'choices' 			=> array(
							'0' 		=> '0em',
							'0.05em' 	=> '0.05em',
							'0.1em' 	=> '0.1em',
							'0.15em' 	=> '0.15em',
							'0.2em'	 	=> '0.2em',
							'0.25em' 	=> '0.25em',
							'0.3em' 	=> '0.3em',
							'0.35em' 	=> '0.35em',
							'0.4em' 	=> '0.4em'
						)
					)),
					new WP_Customize_Control($wp_customize, 'fallsky_typography_fullwidth_post_title_text-transform', array(
						'type' 				=> 'select',
						'label' 			=> esc_html__('Text Transform', 'fallsky'),
						'section' 			=> 'fallsky_section_typography_post_title',
						'settings' 			=> 'fallsky_typography_fullwidth_post_title_text-transform',
						'active_callback' 	=> 'fallsky_customize_control_active_cb',
						'choices' 			=> array(
							'none' 			=> esc_html__('None', 'fallsky'),
							'uppercase' 	=> esc_html__('Uppercase', 'fallsky'),
							'lowercase' 	=> esc_html__('Lowercase', 'fallsky'),
							'capitalize' 	=> esc_html__('Capitalize', 'fallsky')
						)
					)),
					new WP_Customize_Control($wp_customize, 'fallsky_typography_fullwidth_post_title_font-style', array(
						'type' 				=> 'select',
						'label' 			=> esc_html__('Style', 'fallsky'),
						'section' 			=> 'fallsky_section_typography_post_title',
						'settings' 			=> 'fallsky_typography_fullwidth_post_title_font-style',
						'active_callback' 	=> 'fallsky_customize_control_active_cb',
						'choices' 			=> array(
							'normal' => esc_html__('Normal', 'fallsky'),
							'italic' => esc_html__('Italic', 'fallsky')
						)
					))
				)
			)));

			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_typography_archive_group', array(
				'type' 		=> 'group',
				'label' 	=> esc_html__('Post Archive', 'fallsky'),
				'section' 	=> 'fallsky_section_typography_post_title',
				'settings' 	=> 'fallsky_typography_archive_group',
				'children'	=> array(
					new WP_Customize_Control($wp_customize, 'fallsky_typography_archive_post_title_style', array(
						'type' 		=> 'select',
						'label' 	=> '',
						'section' 	=> 'fallsky_section_typography_post_title',
						'settings' 	=> 'fallsky_typography_archive_post_title_style',
						'choices' 	=> array(
							'default' 	=> esc_html__('Default', 'fallsky'),
							'custom'	=> esc_html__('Custom', 'fallsky')
						)
					)),
					new Fallsky_Customize_Control($wp_customize, 'fallsky_typography_archive_post_title_font-weight', array(
						'type' 				=> 'select',
						'label' 			=> esc_html__('Font Weight', 'fallsky'),
						'description' 		=> esc_html__('Please note: not every font supports all the font weight values listed.', 'fallsky'),
						'section' 			=> 'fallsky_section_typography_post_title',
						'settings' 			=> 'fallsky_typography_archive_post_title_font-weight',
						'active_callback' 	=> 'fallsky_customize_control_active_cb',
						'choices' 			=> array(
							'100' => 100,
							'200' => 200,
							'300' => 300,
							'400' => 400,
							'500' => 500,
							'600' => 600,
							'700' => 700,
							'800' => 800,
						)
					)),
					new WP_Customize_Control($wp_customize, 'fallsky_typography_archive_post_title_letter-spacing', array(
						'type' 				=> 'select',
						'label' 			=> esc_html__('Letter Spacing', 'fallsky'),
						'section'			=> 'fallsky_section_typography_post_title',
						'settings' 			=> 'fallsky_typography_archive_post_title_letter-spacing',
						'active_callback' 	=> 'fallsky_customize_control_active_cb',
						'choices' 			=> array(
							'0' 		=> '0em',
							'0.05em' 	=> '0.05em',
							'0.1em' 	=> '0.1em',
							'0.15em' 	=> '0.15em',
							'0.2em'	 	=> '0.2em',
							'0.25em' 	=> '0.25em',
							'0.3em' 	=> '0.3em',
							'0.35em' 	=> '0.35em',
							'0.4em' 	=> '0.4em'
						)
					)),
					new WP_Customize_Control($wp_customize, 'fallsky_typography_archive_post_title_text-transform', array(
						'type' 				=> 'select',
						'label' 			=> esc_html__('Text Transform', 'fallsky'),
						'section' 			=> 'fallsky_section_typography_post_title',
						'settings' 			=> 'fallsky_typography_archive_post_title_text-transform',
						'active_callback' 	=> 'fallsky_customize_control_active_cb',
						'choices' 			=> array(
							'none' 			=> esc_html__('None', 'fallsky'),
							'uppercase' 	=> esc_html__('Uppercase', 'fallsky'),
							'lowercase' 	=> esc_html__('Lowercase', 'fallsky'),
							'capitalize' 	=> esc_html__('Capitalize', 'fallsky')
						)
					)),
					new WP_Customize_Control($wp_customize, 'fallsky_typography_archive_post_title_font-style', array(
						'type' 				=> 'select',
						'label' 			=> esc_html__('Style', 'fallsky'),
						'section' 			=> 'fallsky_section_typography_post_title',
						'settings' 			=> 'fallsky_typography_archive_post_title_font-style',
						'active_callback' 	=> 'fallsky_customize_control_active_cb',
						'choices' 			=> array(
							'normal' => esc_html__('Normal', 'fallsky'),
							'italic' => esc_html__('Italic', 'fallsky')
						)
					))
				)
			)));

			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_typography_single_group', array(
				'type' 		=> 'group',
				'label' 	=> esc_html__('Single Post', 'fallsky'),
				'section' 	=> 'fallsky_section_typography_post_title',
				'settings' 	=> 'fallsky_typography_single_group',
				'children'	=> array(
					new WP_Customize_Control($wp_customize, 'fallsky_typography_single_post_title_style', array(
						'type' 		=> 'select',
						'label' 	=> '',
						'section' 	=> 'fallsky_section_typography_post_title',
						'settings' 	=> 'fallsky_typography_single_post_title_style',
						'choices' 	=> array(
							'default' 	=> esc_html__('Default', 'fallsky'),
							'custom'	=> esc_html__('Custom', 'fallsky')
						)
					)),
					new Fallsky_Customize_Control($wp_customize, 'fallsky_typography_single_post_title_font-weight', array(
						'type' 				=> 'select',
						'label' 			=> esc_html__('Font Weight', 'fallsky'),
						'description' 		=> esc_html__('Please note: not every font supports all the font weight values listed.', 'fallsky'),
						'section' 			=> 'fallsky_section_typography_post_title',
						'settings' 			=> 'fallsky_typography_single_post_title_font-weight',
						'active_callback' 	=> 'fallsky_customize_control_active_cb',
						'choices' 			=> array(
							'100' => 100,
							'200' => 200,
							'300' => 300,
							'400' => 400,
							'500' => 500,
							'600' => 600,
							'700' => 700,
							'800' => 800,
						)
					)),
					new WP_Customize_Control($wp_customize, 'fallsky_typography_single_post_title_letter-spacing', array(
						'type' 				=> 'select',
						'label' 			=> esc_html__('Letter Spacing', 'fallsky'),
						'section'			=> 'fallsky_section_typography_post_title',
						'settings' 			=> 'fallsky_typography_single_post_title_letter-spacing',
						'active_callback' 	=> 'fallsky_customize_control_active_cb',
						'choices' 			=> array(
							'0' 		=> '0em',
							'0.05em' 	=> '0.05em',
							'0.1em' 	=> '0.1em',
							'0.15em' 	=> '0.15em',
							'0.2em'	 	=> '0.2em',
							'0.25em' 	=> '0.25em',
							'0.3em' 	=> '0.3em',
							'0.35em' 	=> '0.35em',
							'0.4em' 	=> '0.4em'
						)
					)),
					new WP_Customize_Control($wp_customize, 'fallsky_typography_single_post_title_text-transform', array(
						'type' 				=> 'select',
						'label' 			=> esc_html__('Text Transform', 'fallsky'),
						'section' 			=> 'fallsky_section_typography_post_title',
						'settings' 			=> 'fallsky_typography_single_post_title_text-transform',
						'active_callback' 	=> 'fallsky_customize_control_active_cb',
						'choices' 			=> array(
							'none' 			=> esc_html__('None', 'fallsky'),
							'uppercase' 	=> esc_html__('Uppercase', 'fallsky'),
							'lowercase' 	=> esc_html__('Lowercase', 'fallsky'),
							'capitalize' 	=> esc_html__('Capitalize', 'fallsky')
						)
					)),
					new WP_Customize_Control($wp_customize, 'fallsky_typography_single_post_title_font-style', array(
						'type' 				=> 'select',
						'label' 			=> esc_html__('Style', 'fallsky'),
						'section' 			=> 'fallsky_section_typography_post_title',
						'settings' 			=> 'fallsky_typography_single_post_title_font-style',
						'active_callback' 	=> 'fallsky_customize_control_active_cb',
						'choices' 			=> array(
							'normal' => esc_html__('Normal', 'fallsky'),
							'italic' => esc_html__('Italic', 'fallsky')
						)
					))
				)
			)));

			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_typography_widget_area_group', array(
				'type' 		=> 'group',
				'label' 	=> esc_html__('Widget Area', 'fallsky'),
				'section' 	=> 'fallsky_section_typography_post_title',
				'settings' 	=> 'fallsky_typography_widget_area_group',
				'children'	=> array(
					new WP_Customize_Control($wp_customize, 'fallsky_typography_widget_area_title_style', array(
						'type' 		=> 'select',
						'label' 	=> '',
						'section' 	=> 'fallsky_section_typography_post_title',
						'settings' 	=> 'fallsky_typography_widget_area_title_style',
						'choices' 	=> array(
							'default' 	=> esc_html__('Default', 'fallsky'),
							'custom'	=> esc_html__('Custom', 'fallsky')
						)
					)),
					new Fallsky_Customize_Control($wp_customize, 'fallsky_typography_widget_area_title_font-weight', array(
						'type' 				=> 'select',
						'label' 			=> esc_html__('Font Weight', 'fallsky'),
						'description' 		=> esc_html__('Please note: not every font supports all the font weight values listed.', 'fallsky'),
						'section' 			=> 'fallsky_section_typography_post_title',
						'settings' 			=> 'fallsky_typography_widget_area_title_font-weight',
						'active_callback' 	=> 'fallsky_customize_control_active_cb',
						'choices' 			=> array(
							'100' => 100,
							'200' => 200,
							'300' => 300,
							'400' => 400,
							'500' => 500,
							'600' => 600,
							'700' => 700,
							'800' => 800,
						)
					)),
					new WP_Customize_Control($wp_customize, 'fallsky_typography_widget_area_title_letter-spacing', array(
						'type' 				=> 'select',
						'label' 			=> esc_html__('Letter Spacing', 'fallsky'),
						'section'			=> 'fallsky_section_typography_post_title',
						'settings' 			=> 'fallsky_typography_widget_area_title_letter-spacing',
						'active_callback' 	=> 'fallsky_customize_control_active_cb',
						'choices' 			=> array(
							'0' 		=> '0em',
							'0.05em' 	=> '0.05em',
							'0.1em' 	=> '0.1em',
							'0.15em' 	=> '0.15em',
							'0.2em'	 	=> '0.2em',
							'0.25em' 	=> '0.25em',
							'0.3em' 	=> '0.3em',
							'0.35em' 	=> '0.35em',
							'0.4em' 	=> '0.4em'
						)
					)),
					new WP_Customize_Control($wp_customize, 'fallsky_typography_widget_area_title_text-transform', array(
						'type' 				=> 'select',
						'label' 			=> esc_html__('Text Transform', 'fallsky'),
						'section' 			=> 'fallsky_section_typography_post_title',
						'settings' 			=> 'fallsky_typography_widget_area_title_text-transform',
						'active_callback' 	=> 'fallsky_customize_control_active_cb',
						'choices' 			=> array(
							'none' 			=> esc_html__('None', 'fallsky'),
							'uppercase' 	=> esc_html__('Uppercase', 'fallsky'),
							'lowercase' 	=> esc_html__('Lowercase', 'fallsky'),
							'capitalize' 	=> esc_html__('Capitalize', 'fallsky')
						)
					)),
					new WP_Customize_Control($wp_customize, 'fallsky_typography_widget_area_title_font-style', array(
						'type' 				=> 'select',
						'label' 			=> esc_html__('Style', 'fallsky'),
						'section' 			=> 'fallsky_section_typography_post_title',
						'settings' 			=> 'fallsky_typography_widget_area_title_font-style',
						'active_callback' 	=> 'fallsky_customize_control_active_cb',
						'choices' 			=> array(
							'normal' => esc_html__('Normal', 'fallsky'),
							'italic' => esc_html__('Italic', 'fallsky')
						)
					))
				)
			)));

			$wp_customize->add_control(new WP_Customize_Control($wp_customize, 'fallsky_typography_page_title_font-weight', array(
				'type' 			=> 'select',
				'label' 		=> esc_html__('Font Weight', 'fallsky'),
				'description' 	=> esc_html__('Please note: not every font supports all the font weight values listed.', 'fallsky'),
				'section' 		=> 'fallsky_section_typography_page_title',
				'settings' 		=> 'fallsky_typography_page_title_font-weight',
				'choices' 		=> array(
					'100' => 100,
					'200' => 200,
					'300' => 300,
					'400' => 400,
					'500' => 500,
					'600' => 600,
					'700' => 700,
					'800' => 800,
				)
			)));
			$wp_customize->add_control(new WP_Customize_Control($wp_customize, 'fallsky_typography_page_title_letter-spacing', array(
				'type' 		=> 'select',
				'label' 	=> esc_html__('Letter Spacing', 'fallsky'),
				'section' 	=> 'fallsky_section_typography_page_title',
				'settings' 	=> 'fallsky_typography_page_title_letter-spacing',
				'choices' 	=> array(
					'0' 		=> '0em',
					'0.05em' 	=> '0.05em',
					'0.1em' 	=> '0.1em',
					'0.15em' 	=> '0.15em',
					'0.2em' 	=> '0.2em',
					'0.25em' 	=> '0.25em',
					'0.3em' 	=> '0.3em',
					'0.35em' 	=> '0.35em',
					'0.4em' 	=> '0.4em'
				)
			)));
			$wp_customize->add_control(new WP_Customize_Control($wp_customize, 'fallsky_typography_page_title_text-transform', array(
				'type' 		=> 'select',
				'label' 	=> esc_html__('Text Transform', 'fallsky'),
				'section' 	=> 'fallsky_section_typography_page_title',
				'settings' 	=> 'fallsky_typography_page_title_text-transform',
				'choices' 	=> array(
					'none' 			=> esc_html__('None', 'fallsky'),
					'uppercase' 	=> esc_html__('Uppercase', 'fallsky'),
					'lowercase' 	=> esc_html__('Lowercase', 'fallsky'),
					'capitalize' 	=> esc_html__('Capitalize', 'fallsky')
				)
			)));
			$wp_customize->add_control(new WP_Customize_Control($wp_customize, 'fallsky_typography_page_title_font-style', array(
				'type' 		=> 'select',
				'label' 	=> esc_html__('Style', 'fallsky'),
				'section' 	=> 'fallsky_section_typography_page_title',
				'settings' 	=> 'fallsky_typography_page_title_font-style',
				'choices' 	=> array(
					'normal' => esc_html__('Normal', 'fallsky'),
					'italic' => esc_html__('Italic', 'fallsky')
				)
			)));

			$wp_customize->add_control(new WP_Customize_Control($wp_customize, 'fallsky_typography_section_widget_title_font-weight', array(
				'type'	 		=> 'select',
				'label' 		=> esc_html__('Font Weight', 'fallsky'),
				'description' 	=> esc_html__('Please note: not every font supports all the font weight values listed.', 'fallsky'),
				'section' 		=> 'fallsky_section_typography_widget_title',
				'settings' 		=> 'fallsky_typography_section_widget_title_font-weight',
				'choices' 		=> array(
					'100' => 100,
					'200' => 200,
					'300' => 300,
					'400' => 400,
					'500' => 500,
					'600' => 600,
					'700' => 700,
					'800' => 800,
				)
			)));
			$wp_customize->add_control(new WP_Customize_Control($wp_customize, 'fallsky_typography_section_widget_title_letter-spacing', array(
				'type' 		=> 'select',
				'label' 	=> esc_html__('Letter Spacing', 'fallsky'),
				'section' 	=> 'fallsky_section_typography_widget_title',
				'settings' 	=> 'fallsky_typography_section_widget_title_letter-spacing',
				'choices' 	=> array(
					'0' 		=> '0em',
					'0.05em' 	=> '0.05em',
					'0.1em' 	=> '0.1em',
					'0.15em' 	=> '0.15em',
					'0.2em' 	=> '0.2em',
					'0.25em' 	=> '0.25em',
					'0.3em' 	=> '0.3em',
					'0.35em' 	=> '0.35em',
					'0.4em' 	=> '0.4em'
				)
			)));
			$wp_customize->add_control(new WP_Customize_Control($wp_customize, 'fallsky_typography_section_widget_title_text-transform', array(
				'type' 		=> 'select',
				'label' 	=> esc_html__('Text Transform', 'fallsky'),
				'section' 	=> 'fallsky_section_typography_widget_title',
				'settings' 	=> 'fallsky_typography_section_widget_title_text-transform',
				'choices' 	=> array(
					'none' 			=> esc_html__('None', 'fallsky'),
					'uppercase' 	=> esc_html__('Uppercase', 'fallsky'),
					'lowercase' 	=> esc_html__('Lowercase', 'fallsky'),
					'capitalize' 	=> esc_html__('Capitalize', 'fallsky')
				)
			)));
			$wp_customize->add_control(new WP_Customize_Control($wp_customize, 'fallsky_typography_section_widget_title_font-style', array(
				'type' 		=> 'select',
				'label' 	=> esc_html__('Style', 'fallsky'),
				'section' 	=> 'fallsky_section_typography_widget_title',
				'settings' 	=> 'fallsky_typography_section_widget_title_font-style',
				'choices' 	=> array(
					'normal' => esc_html__('Normal', 'fallsky'),
					'italic' => esc_html__('Italic', 'fallsky')
				)
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_typography_hide_widget_title_decor', array(
				'type' 			=> 'checkbox',
				'label' 		=> esc_html__('Hide widget titles\' decor line', 'fallsky'),
				'label_first'	=> true,
				'section' 		=> 'fallsky_section_typography_widget_title',
				'settings' 		=> 'fallsky_typography_hide_widget_title_decor'
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_typography_hide_section_title_decor', array(
				'type' 			=> 'checkbox',
				'label' 		=> esc_html__('Hide home widget titles\' decor line', 'fallsky'),
				'label_first'	=> true,
				'section' 		=> 'fallsky_section_typography_widget_title',
				'settings' 		=> 'fallsky_typography_hide_section_title_decor'
			)));

			$wp_customize->add_control(new WP_Customize_Control($wp_customize, 'fallsky_typography_category_links_font', array(
				'type' 		=> 'select',
				'label' 	=> esc_html__('Categories Font Family', 'fallsky'),
				'section' 	=> 'fallsky_section_typography_category_links',
				'settings' 	=> 'fallsky_typography_category_links_font',
				'choices' 	=> array(
					'heading-font' 	=> esc_html__('Heading Font Family', 'fallsky'),
					'text-font' 	=> esc_html__('Text Font Family', 'fallsky')
				)
			)));
			$wp_customize->add_control(new WP_Customize_Control($wp_customize, 'fallsky_typography_category_links_color', array(
				'type' 				=> 'select',
				'label' 			=> esc_html__('Color', 'fallsky'),
				'section' 			=> 'fallsky_section_typography_category_links',
				'settings' 			=> 'fallsky_typography_category_links_color',
				'active_callback'	=> 'fallsky_customize_control_active_cb',
				'description'		=> esc_html__('Please note: When there is a background image behind it, the category link will always be white.', 'fallsky'),
				'choices' 			=> array(
					'text-color' 	=> esc_html__('Text Color', 'fallsky'),
					'accent-color' 	=> esc_html__('Accent Color', 'fallsky')
				)
			)));
		}
		public function get_css_variables($vars){
			global $fallsky_default_settings;
			$css_vars = array(
				'--heading-font' 	=> 'fallsky_typography_heading_font-family',
				'--body-font' 		=> 'fallsky_typography_text_font-family'
			);
			foreach($css_vars as $var => $id){
				$custom_value = fallsky_get_theme_mod($id);
				if($custom_value != $fallsky_default_settings[$id]){
					$vars[$var] = sprintf('"%s"', $custom_value);
				}
			}
			return $vars;
		}
		protected function get_custom_styles(){
			global $fallsky_default_settings;
			$this->setting_keys = array_keys($fallsky_default_settings);
			$styles = array();

			$styles['typography_heading'] = $this->get_typography_styles(
				'h1, h2, h3, h4, h5, h6', 
				'fallsky_typography_heading', 
				array('font-weight', 'letter-spacing', 'text-transform', 'font-style')
			)
			. $this->get_typography_styles(
				fallsky_get_selector(array(
					'.post-entry h1', 
					'.home-widget .section-content h1', 
					'.post-entry h2', 
					'.home-widget .section-content h2', 
					'.post-entry h3', 
					'.home-widget .section-content h3', 
					'.post-entry h4', 
					'.home-widget .section-content h4', 
					'.post-entry h5', 
					'.home-widget .section-content h5', 
					'.post-entry h6', 
					'.home-widget .section-content h6'
				)),
				'fallsky_typography_heading',
				array('line-height')
			);

			$styles['typography_content'] = $this->get_typography_styles(
				'.post-entry, .home-widget .section-content',
				'fallsky_typography_content',
				array('font-size')
			)
			. $this->get_typography_styles(
				'.post-entry',
				'fallsky_typography_content',
				array('line-height')
			);
			$styles['woocommerce_content'] = $this->get_typography_styles(
				fallsky_get_selector(array(
					'.woocommerce-page #primary .woocommerce-product-details__short-description', 
					'.woocommerce-page #primary .entry-content'
				)),
				'fallsky_typography_content',
				array('font-size', 'line-height')
			);

			$styles['typography_post_title'] = $this->get_typography_styles(
				'.post-title',
				'fallsky_typography_post_title',
				array('font-weight', 'letter-spacing', 'text-transform', 'font-style')
			);
			if('custom' == fallsky_get_theme_mod('fallsky_typography_fullwidth_post_title_style')){
				$styles['typography_fullwidth_post_title'] = $this->get_typography_styles(
					'.featured-section .post .post-title',
					'fallsky_typography_fullwidth_post_title',
					array('font-weight', 'letter-spacing', 'text-transform', 'font-style'),
					true
				);
			}
			if('custom' == fallsky_get_theme_mod('fallsky_typography_archive_post_title_style')){
				$styles['typography_archive_post_title'] = $this->get_typography_styles(
					'.posts .post .post-title',
					'fallsky_typography_archive_post_title',
					array('font-weight', 'letter-spacing', 'text-transform', 'font-style'),
					true
				);
			}
			if('custom' == fallsky_get_theme_mod('fallsky_typography_single_post_title_style')){
				$styles['typography_single_post_title'] = $this->get_typography_styles(
					'.single .post-header .post-header-text .post-title',
					'fallsky_typography_single_post_title',
					array('font-weight', 'letter-spacing', 'text-transform', 'font-style'),
					true
				);
			}
			if('custom' == fallsky_get_theme_mod('fallsky_typography_widget_area_title_style')){
				$styles['typography_widget_area_title'] = $this->get_typography_styles(
					fallsky_get_selector( array( 
						'.widget.widget_rss ul li a.rsswidget', 
						'.widget.widget_recent_entries a', 
						'.widget.widget_recent_comments ul li > a',
						'.widget.fallsky-widget_posts ul li .post-title'
					) ),
					'fallsky_typography_widget_area_title',
					array('font-weight', 'letter-spacing', 'text-transform', 'font-style'),
					true
				);
			}

			$styles['typography_page_title'] = $this->get_typography_styles(
				'.page-title',
				'fallsky_typography_page_title',
				array('font-weight', 'letter-spacing', 'text-transform', 'font-style')
			);

			$styles['typography_section_widget_title'] = $this->get_typography_styles(
				'h5.section-title, .widget-area .widget h5.widget-title, .site-footer > .widget.fallsky-widget_instagram h5.widget-title, .related-posts .related-posts-title, .comments h2.comments-title, .comment-respond h3.comment-reply-title', 
				'fallsky_typography_section_widget_title', 
				array('font-weight', 'letter-spacing', 'text-transform', 'font-style')
			);

			$styles['typography-category-links-font'] = sprintf(
				'.cat-links { font-family: var(%s); }',
				'text-font' == fallsky_get_theme_mod( 'fallsky_typography_category_links_font' ) ? '--body-font' : '--heading-font'
			);
			$primary_color_enabled = ( 'custom' == fallsky_get_theme_mod( 'fallsky_accent_color' ) );
			if( $primary_color_enabled && ( 'accent-color' == fallsky_get_theme_mod( 'fallsky_typography_category_links_color' ) ) ) {
				$styles['typography-category-links-color'] = sprintf(
					'%s { color: var(--primary-color); }',
					fallsky_get_selector( array(
						'.primary-color-enabled .featured-section .top-blocks.style-blocks-1 .cat-links',
						'.primary-color-enabled .posts:not(.layout-overlay) .cat-links',
						'.primary-color-enabled.single .site-content .post-header-text .cat-links'
					) )
				);
			}

			return $styles;
		}
		public function get_fallback_css($styles){
			$fonts = array();
			$fonts['text_font'] = fallsky_get_style(
				'fallsky_typography_text_font-family',
				fallsky_get_selector(array(
					'body',
					'button',
					'input',
					'select',
					'textarea',
					'.ui-widget',
					'.section-title',
					'.widget-title',
					'.related-posts .related-posts-title',
					'.comments h2.comments-title',
					'.comment-respond h3.comment-reply-title',
					'.search-screen .shortcuts-cat span.counts',
					'.error404 .page-404-page-header h1.page-title',
					'blockquote cite',
					'blockquote small',
					'.widget.fallsky-widget_author_list ul li a .post-count'
				)),
				'font-family: "%s";'
			);
			$fonts['heading_font'] = fallsky_get_style(
				'fallsky_typography_heading_font-family',
				fallsky_get_selector(array(
					'.post-title',
					'blockquote',
					'.wp-caption-text',
					'.site-branding .site-title',
					'.fallsky-fullmenu .main-navigation',
					'.fallsky-fullmenu .search input',
					'.search-screen .search input',
					'.search-screen .shortcuts-cat',
					'.comments ol.comment-list li .comment-author b.fn',
					'h1',
					'h2',
					'h3',
					'h4',
					'h5',
					'h6',
					'.post-entry .dropcap:first-letter',
					'.widget.widget_rss ul li a.rsswidget',
					'.widget.widget_recent_entries a',
					'.widget_recent_comments ul li > a',
					'.woocommerce #reviews #comments ol.commentlist li .comment-text p.meta .woocommerce-review__author',
					'.woocommerce-page.woocommerce-cart .cart-empty'
				)),
				'font-family: "%s";'
			);

			$category_links_font_family = ('text-font' == fallsky_get_theme_mod( 'fallsky_typography_category_links_font' ) ) ? 'text' : 'heading';
			$fonts['typography_category_links_font'] = sprintf(
				'.cat-links { font-family: %s; }',
				fallsky_get_theme_mod( "fallsky_typography_{$category_links_font_family}_font-family" )
			);

			$primary_color = fallsky_get_theme_mod('fallsky_accent_custom_color');
			$primary_color_enabled = ( 'custom' == fallsky_module_enabled( 'fallsky_accent_color' ) );
			$category_links_color = fallsky_get_theme_mod( 'fallsky_typography_category_links_color' );
			if( $primary_color_enabled && !empty( $primary_color ) && ( 'accent-color' == $category_links_color ) ) {
				$styles['typography_category_links_color'] = sprintf(
					'%s { color: %s; }',
					fallsky_get_selector( array(
						'.primary-color-enabled .featured-section .top-blocks.style-blocks-1 .cat-links',
						'.primary-color-enabled .posts:not(.layout-overlay) .cat-links',
						'.primary-color-enabled.single .site-content .post-header-text .cat-links'
					) ),
					$primary_color
				);

			}

			return sprintf('%s %s', $styles, implode(' ', $fonts));
		}
		private function get_typography_styles($element, $prefix, $attrs, $no_check = false){
			global $fallsky_default_settings;
			if(!empty($element) && !empty($prefix) && !empty($attrs) && is_array($attrs)){
				$styles = '';
				foreach($attrs as $attr){
					$name = $prefix . '_' . $attr;
					if(in_array($name, $this->setting_keys)){
						$value = esc_attr(fallsky_get_theme_mod($name));
						if($no_check || ($fallsky_default_settings[$name] != $value)){
							$unit = ($attr == 'font-size') ? 'px' : '';
							$styles .= "\n\t" . $attr . ': ' . $value . $unit . ';';
						}
					}
				}
				return empty($styles) ? '' : sprintf("\n%s {%s\n}\n", $element, $styles);
			}
			return '';
		}
		public function frontend_actions(){
			add_action('wp_enqueue_scripts', 	array($this, 'enqueue_scripts'));
			add_action('body_class',			array($this, 'body_class'));
		}
		public function enqueue_scripts(){
			$google_fonts = array(
				fallsky_get_theme_mod('fallsky_typography_text_font-family') 	. ':100,200,300,400,500,600,700,800',
				fallsky_get_theme_mod('fallsky_typography_heading_font-family') . ':100,200,300,400,500,600,700,800'
			);

			$google_fonts = array_unique($google_fonts);
			// Add Google font in safe way. Refer to https://gist.github.com/richtabor/b85d317518b6273b4a88448a11ed20d3
			if(!empty($google_fonts)){
				wp_enqueue_style(
					'fallsky-theme-google-fonts',
					add_query_arg(array('family' => urlencode(implode('|', $google_fonts))), 'https://fonts.googleapis.com/css'),
					array(), 
					FALLSKY_ASSETS_VERSION
				);
			}
		}
		public function body_class($class){
			fallsky_module_enabled('fallsky_typography_hide_widget_title_decor') ? array_push($class, 'hide-widget-title-decor') : '';
			fallsky_module_enabled('fallsky_typography_hide_section_title_decor') ? array_push($class, 'hide-section-title-decor') : '';
			return $class;
		}
	}
	new Fallsky_Customize_Typography();
}


