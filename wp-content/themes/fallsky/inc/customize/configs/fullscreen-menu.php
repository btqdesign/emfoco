<?php
/**
* Customize panel fullscreen menu configuration files.
*/

if(!class_exists('Fallsky_Customize_Menu')){
	class Fallsky_Customize_Menu extends Fallsky_Customize_Base {
		public function register_controls($wp_customize){
			global $fallsky_default_settings;

			// Panel
			$wp_customize->add_panel('fallsky_panel_menu', array(
				'title'    => esc_html__('Fullscreen Menu', 'fallsky'),
				'priority' => 15
			));

			// Sections
			$wp_customize->add_section('fallsky_section_fullscreen_menu_content', array(
				'title'	=> esc_html__('Content Options', 'fallsky'),
				'panel'	=> 'fallsky_panel_menu'
			));
			$wp_customize->add_section('fallsky_section_fullscreen_menu_design', array(
				'title'	=> esc_html__('Design Options', 'fallsky'),
				'panel'	=> 'fallsky_panel_menu'
			));

			// Settings
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_fullscreen_menu_notes', array(
				'default'   		=> '',
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_empty'
			)));

			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_fullscreen_menu_show_search_form', array(
				'default'   		=> $fallsky_default_settings['fallsky_fullscreen_menu_show_search_form'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_checkbox'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_fullscreen_menu_show_social_menu', array(
				'default'   		=> $fallsky_default_settings['fallsky_fullscreen_menu_show_social_menu'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_checkbox'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_fullscreen_menu_copyright_text', array(
				'default'   		=> $fallsky_default_settings['fallsky_fullscreen_menu_copyright_text'],
				'transport'			=> 'postMessage',
				'sanitize_callback' => 'wp_kses_post'
			)));

			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_fullscreen_menu_bg_color', array(
				'default'  			=> $fallsky_default_settings['fallsky_fullscreen_menu_bg_color'],
				'transport'			=> 'postMessage',
				'sanitize_callback' => 'sanitize_hex_color'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_fullscreen_menu_text_color', array(
				'default'   		=> $fallsky_default_settings['fallsky_fullscreen_menu_text_color'],
				'transport'			=> 'postMessage',
				'sanitize_callback' => 'sanitize_hex_color'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_fullscreen_menu_bg_image', array(
				'default'   		=> $fallsky_default_settings['fallsky_fullscreen_menu_bg_image'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'absint'
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_fullscreen_menu_bg_size', array(
				'default'			=> $fallsky_default_settings['fallsky_fullscreen_menu_bg_size'],
				'transport'			=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_choice',
				'dependency' 		=> array(
					'fallsky_fullscreen_menu_bg_image' => array('value' => array(''), 'operator' => 'not in')
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_fullscreen_menu_bg_repeat', array(
				'default'   		=> $fallsky_default_settings['fallsky_fullscreen_menu_bg_repeat'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_checkbox',
				'dependency' 		=> array(
					'fallsky_fullscreen_menu_bg_image' => array('value' => array(''), 'operator' => 'not in')
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_fullscreen_menu_bg_position_x', array(
				'default'			=> $fallsky_default_settings['fallsky_fullscreen_menu_bg_position_x'],
				'transport'			=> 'postMessage',
				'sanitize_callback' => 'sanitize_text_field',
				'dependency' 		=> array(
					'fallsky_fullscreen_menu_bg_image' => array('value' => array(''), 'operator' => 'not in')
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_fullscreen_menu_bg_position_y', array(
				'default'  			=> $fallsky_default_settings['fallsky_fullscreen_menu_bg_position_y'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'sanitize_text_field',
				'dependency' 		=> array(
					'fallsky_fullscreen_menu_bg_image' => array('value' => array(''), 'operator' => 'not in')
				)
			)));

			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_fullscreen_menu_enable_overlay', array(
				'default'  			=> $fallsky_default_settings['fallsky_fullscreen_menu_enable_overlay'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_checkbox',
				'dependency' 		=> array(
					'fallsky_fullscreen_menu_bg_image' => array('value' => array(''), 'operator' => 'not in')
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_fullscreen_menu_overlay_color', array(
				'default'  			=> $fallsky_default_settings['fallsky_fullscreen_menu_overlay_color'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'sanitize_hex_color',
				'dependency' 		=> array(
					'fallsky_fullscreen_menu_bg_image' 			=> array('value' => array('',), 'operator' => 'not in'),
					'fallsky_fullscreen_menu_enable_overlay' 	=> array('value' => array('on'))
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_fullscreen_menu_overlay_opacity', array(
				'default'  			=> $fallsky_default_settings['fallsky_fullscreen_menu_overlay_opacity'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'absint',
				'dependency' 		=> array(
					'fallsky_fullscreen_menu_bg_image' 			=> array('value' => array('',), 'operator' => 'not in'),
					'fallsky_fullscreen_menu_enable_overlay' 	=> array('value' => array('on'))
				)
			)));

			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_fullscreen_menu_no_border', array(
				'default'			=> $fallsky_default_settings['fallsky_fullscreen_menu_no_border'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_checkbox'
			)));

			// Controls
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_fullscreen_menu_content_notes', array(
				'type' 			=> 'notes',
				'description' 	=> esc_html__('Click on the hamburger menu button in site header to preview', 'fallsky'),
				'section' 		=> 'fallsky_section_fullscreen_menu_content',
				'settings' 		=> 'fallsky_fullscreen_menu_notes'
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_fullscreen_menu_show_search_form', array(
				'type' 					=> 'checkbox',
				'label_first'		 	=> true,
				'label' 				=> esc_html__('Display Search Form', 'fallsky'),
				'section' 				=> 'fallsky_section_fullscreen_menu_content',
				'settings' 				=> 'fallsky_fullscreen_menu_show_search_form'
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_fullscreen_menu_show_social_menu', array(
				'type'					=> 'checkbox',
				'label_first'		 	=> true,
				'label' 				=> esc_html__('Display Social Menu', 'fallsky'),
				'section'  				=> 'fallsky_section_fullscreen_menu_content',
				'settings' 				=> 'fallsky_fullscreen_menu_show_social_menu',
				'description'			=> sprintf(
					esc_html__('%sClick here%s to know how to setup the social menu.', 'fallsky'), 
					sprintf('<a href="%s" target="_blank">', 'https://www.loftocean.com/fallsky/social/display-social-media-icons-on-your-site/'),
					'</a>'
				)
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_fullscreen_menu_copyright_text', array(
				'type'		=> 'textarea',
				'label' 	=> esc_html__('Copyright Text', 'fallsky'),
				'section'  	=> 'fallsky_section_fullscreen_menu_content',
				'settings' 	=> 'fallsky_fullscreen_menu_copyright_text'
			)));

			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_fullscreen_menu_design_notes', array(
				'type' 			=> 'notes',
				'description' 	=> esc_html__('Click on the hamburger menu button in site header to preview', 'fallsky'),
				'section' 		=> 'fallsky_section_fullscreen_menu_design',
				'settings' 		=> 'fallsky_fullscreen_menu_notes'
			)));
			$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'fallsky_fullscreen_menu_bg_color', array(
				'label' 			=> esc_html__('Background Color', 'fallsky'),
				'section'  			=> 'fallsky_section_fullscreen_menu_design',
				'settings' 			=> 'fallsky_fullscreen_menu_bg_color'
			)));
			$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'fallsky_fullscreen_menu_text_color', array(
				'label' 	=> esc_html__('Text Color', 'fallsky'),
				'section'  	=> 'fallsky_section_fullscreen_menu_design',
				'settings' 	=> 'fallsky_fullscreen_menu_text_color'
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_fullscreen_menu_bg_image', array(
				'type' 		=> 'image_id',
				'label' 	=> esc_html__('Background Image', 'fallsky'),
				'section' 	=> 'fallsky_section_fullscreen_menu_design',
				'settings' 	=> 'fallsky_fullscreen_menu_bg_image'
			)));
			$wp_customize->add_control(new WP_Customize_Background_Position_Control($wp_customize, 'fallsky_fullscreen_menu_bg_position', array(
				'label' 			=> esc_html__('Image Position', 'fallsky'),
				'section'			=> 'fallsky_section_fullscreen_menu_design',
				'active_callback' 	=> 'fallsky_customize_control_active_cb',
				'settings' 			=> array(
					'x' => 'fallsky_fullscreen_menu_bg_position_x',
					'y' => 'fallsky_fullscreen_menu_bg_position_y'
				)
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_fullscreen_menu_bg_size', array(
				'type' 				=> 'select',
				'label' 			=> esc_html__('Size', 'fallsky'),
				'section' 			=> 'fallsky_section_fullscreen_menu_design',
				'settings' 			=> 'fallsky_fullscreen_menu_bg_size',
				'active_callback' 	=> 'fallsky_customize_control_active_cb',
				'choices' 			=> array(
					'auto' 		=> esc_html__('Original', 'fallsky'),
					'contain' 	=> esc_html__('Fit to Screen', 'fallsky'),
					'cover'		=> esc_html__('Fill Screen', 'fallsky')
				)
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_fullscreen_menu_bg_repeat', array(
				'type' 				=> 'checkbox',
				'label' 			=> esc_html__('Repeat', 'fallsky'),
				'section' 			=> 'fallsky_section_fullscreen_menu_design',
				'settings' 			=> 'fallsky_fullscreen_menu_bg_repeat',
				'active_callback' 	=> 'fallsky_customize_control_active_cb'
			)));

			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_fullscreen_menu_enable_overlay', array(
				'type' 					=> 'checkbox',
				'label_first'		 	=> true,
				'label' 				=> esc_html__('Add an overlay', 'fallsky'),
				'section' 				=> 'fallsky_section_fullscreen_menu_design',
				'settings' 				=> 'fallsky_fullscreen_menu_enable_overlay',
				'active_callback' 		=> 'fallsky_customize_control_active_cb'
			)));
			$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'fallsky_fullscreen_menu_overlay_color', array(
				'label' 			=> esc_html__('Overlay color', 'fallsky'),
				'section'  			=> 'fallsky_section_fullscreen_menu_design',
				'settings' 			=> 'fallsky_fullscreen_menu_overlay_color',
				'active_callback' 	=> 'fallsky_customize_control_active_cb'
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_fullscreen_menu_overlay_opacity', array(
				'type' 				=> 'number_slider',
				'label' 			=> esc_html__('Overlay opacity', 'fallsky'),
				'section' 			=> 'fallsky_section_fullscreen_menu_design',
				'settings' 			=> 'fallsky_fullscreen_menu_overlay_opacity',
				'active_callback' 	=> 'fallsky_customize_control_active_cb',
				'after_text'		=> esc_html__('%', 'fallsky'),
				'input_attrs'		=> array(
					'data-min'	=> '0',
					'data-max'	=> '100',
					'data-step'	=> '1'
				)
			)));

			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_fullscreen_menu_no_border', array(
				'type' 					=> 'checkbox',
				'label_first'			=> true,
				'label' 				=> esc_html__('Remove border', 'fallsky'),
				'section' 				=> 'fallsky_section_fullscreen_menu_design',
				'settings' 				=> 'fallsky_fullscreen_menu_no_border'
			)));
		}
		protected function get_custom_styles(){
			global $fallsky_default_settings, $fallsky_is_preview;
			$styles = array();

			$styles['fullscreen-menu-bg-color'] = fallsky_get_style(
				'fallsky_fullscreen_menu_bg_color',
				'.fallsky-fullmenu .fullscreen-bg',
				'background-color: %s;'
			);
			$styles['fullscreen-menu-text-color'] = fallsky_get_style(
				'fallsky_fullscreen_menu_text_color',
				'.fallsky-fullmenu .container',
				'color: %s;'
			);
			// Background Image for ".fallsky-fullmenu > .container"
			$fullscreen_menu_bg_image_id = absint(fallsky_get_theme_mod('fallsky_fullscreen_menu_bg_image'));
			if(!empty($fullscreen_menu_bg_image_id) || $fallsky_is_preview){
				$image = empty( $fullscreen_menu_bg_image_id ) ? false : fallsky_get_image_src( $fullscreen_menu_bg_image_id, 'fallsky_large', false );
				if($image || $fallsky_is_preview){
					$position_x 	= esc_attr(fallsky_get_theme_mod('fallsky_fullscreen_menu_bg_position_x'));
					$position_y 	= esc_attr(fallsky_get_theme_mod('fallsky_fullscreen_menu_bg_position_y'));
					$size 			= esc_attr(fallsky_get_theme_mod('fallsky_fullscreen_menu_bg_size'));
					$repeat 		= fallsky_module_enabled('fallsky_fullscreen_menu_bg_repeat');

					if(!empty($image)){
						$styles['fullscreen-menu-bg-image'] = sprintf(
							'.fallsky-fullmenu .fullscreen-bg { %s }',
							sprintf('background-image: url(%s);', esc_url_raw($image))
						);
					}

					$styles['fullscreen-menu-bg-image-size'] = sprintf(
						'.fallsky-fullmenu .fullscreen-bg { %s }',
						sprintf('background-size: %s;', $size)
					);
					$styles['fullscreen-menu-bg-image-repeat'] = sprintf(
						'.fallsky-fullmenu .fullscreen-bg { %s }',
						sprintf('background-repeat: %s;', ($repeat ? 'repeat' : 'no-repeat'))
					);
					$styles['fullscreen-menu-bg-image-position-x'] = sprintf(
						'.fallsky-fullmenu .fullscreen-bg { %s }',
						sprintf('background-position-x: %s;', $position_x)
					);
					$styles['fullscreen-menu-bg-image-position-y'] = sprintf(
						'.fallsky-fullmenu .fullscreen-bg { %s }',
						sprintf('background-position-y: %s;', $position_y)
					);

					$enable_overlay 	= fallsky_module_enabled('fallsky_fullscreen_menu_enable_overlay');
					$overlay_opacity 	= absint(fallsky_get_theme_mod('fallsky_fullscreen_menu_overlay_opacity'));
					if($enable_overlay || $fallsky_is_preview){
						$styles['fullscreen-menu-overlay-color'] = fallsky_get_style(
							'fallsky_fullscreen_menu_overlay_color',
							'.fallsky-fullmenu.has-overlay .fullscreen-bg:after',
							'background: %s;'
						);
						if($overlay_opacity != $fallsky_default_settings['fallsky_fullscreen_menu_overlay_opacity']){
							$styles['fullscreen-menu-overlay-opacity'] = sprintf(
								'.fallsky-fullmenu.has-overlay .fullscreen-bg:after { %s }',
								sprintf('opacity: %s;', $overlay_opacity / 100)
							);
						}
					}
				}
			}

			return $styles;
		}
		public function frontend_actions(){
			add_filter('fallsky_fullscreen_site_header_class', 		array($this, 'fullscreen_site_header_class'));
			add_action('fallsky_fullscreen_site_header_content', 	array($this, 'fullscreen_site_header_content'));
		}
		public function fullscreen_site_header_class( $class ) {
			fallsky_module_enabled( 'fallsky_fullscreen_menu_no_border' ) ? array_push( $class, 'no-border' ) : '';
			$fullscreen_menu_bg_image_id = absint( fallsky_get_theme_mod( 'fallsky_fullscreen_menu_bg_image' ) );
			if( !empty( $fullscreen_menu_bg_image_id ) ) {
				$background_image 	= empty( $fullscreen_menu_bg_image_id ) ? false : fallsky_get_image_src( $fullscreen_menu_bg_image_id, 'fallsky_large', false );
				$enable_overlay 	= fallsky_module_enabled( 'fallsky_fullscreen_menu_enable_overlay' );
				if( $background_image && $enable_overlay ) {
					array_push( $class, 'has-overlay' );
				}
			}
			return $class;
		}
		public function fullscreen_site_header_content(){
			global $fallsky_is_preview;

			$show_search_form 	= fallsky_module_enabled('fallsky_fullscreen_menu_show_search_form');
			$show_social_menu 	= fallsky_module_enabled('fallsky_fullscreen_menu_show_social_menu');
			$copyright 		  	= fallsky_get_theme_mod('fallsky_fullscreen_menu_copyright_text');
			$search_hide		= $fallsky_is_preview && !$show_search_form ? ' hide' : '';
			$social_hide		= $fallsky_is_preview && !$show_social_menu ? ' hide' : '';
			$copyright_hide		= $fallsky_is_preview && empty($copyright) 	? ' hide' : '';

			// Show main menu in fullscreen site header
			if(has_nav_menu('primary')){
				wp_nav_menu(array(
					'theme_location' 	=> 'primary',
					'container' 		=> 'nav',
					'container_id' 		=> '',
					'container_class' 	=> 'main-navigation',
					'menu_id' 			=> 'fullscreen-menu-main',
					'menu_class' 		=> 'primary-menu',
					'depth' 			=> 3,
					'walker' 			=> new Fallsky_Walker_Fullscreen_Nav_Menu()
				));
			}
			// Show search form in fullscreen site header
			if($fallsky_is_preview || $show_search_form){
				printf(
					'<div class="search%s">%s</div>',
					$search_hide,
					get_search_form(false)
				);
			}
			// Show secondary menu in fullscreen site header
			if(has_nav_menu('secondary')){ 
				fallsky_secondary_nav(array(
					'menu_id' 		=> 'fullscreen-menu-secondary',
					'container_id' 	=> '',
				)); 
			} 
			// Show social menu in fullscreen site header
			if($fallsky_is_preview || $show_social_menu){
				fallsky_social_menu(array(
					'menu_id' 			=> 'fullscreen-menu-social', 
					'container_class'	=> sprintf('social-navigation%s', $social_hide)
				));
			}
			// Show copyright text in fullscreen site header
			if($fallsky_is_preview || !empty($copyright)){
				printf(
					'<div class="text%s">%s</div>',
					$copyright_hide,
					wp_kses_post($copyright)
				);
			}
		}
	}
	new Fallsky_Customize_Menu();
}