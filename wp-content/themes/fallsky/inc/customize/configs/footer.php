<?php
/**
* Customize panel site footer configuration files.
*/

if ( ! class_exists( 'Fallsky_Customize_Footer' ) ) {
	class Fallsky_Customize_Footer extends Fallsky_Customize_Base {
		private $new_tab = false;
		public function register_controls( $wp_customize ) {
			global $fallsky_default_settings;

			// Panel
			$wp_customize->add_panel( 'fallsky_panel_site_footer', array(
				'title'    => esc_html__( 'Site Footer', 'fallsky' ),
				'priority' => 25
			) );

			// Sections
			$wp_customize->add_section( 'fallsky_section_site_footer_content', array(
				'title'	=> esc_html__( 'Content Options', 'fallsky' ),
				'panel' => 'fallsky_panel_site_footer'
			) );
			$wp_customize->add_section( 'fallsky_section_site_footer_design', array(
				'title'	=> esc_html__( 'Design Options', 'fallsky' ),
				'panel' => 'fallsky_panel_site_footer'
			) );

			// Settings
			if ( class_exists( 'Fallsky_Extension' ) ) {	
				$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_site_footer_instagram_group', array(
					'default' 			=> '',
					'transport'			=> 'postMessage',
					'sanitize_callback' => 'fallsky_sanitize_empty'
				) ) );
				$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_site_footer_enable_instagram', array(
					'default'   		=> $fallsky_default_settings['fallsky_site_footer_enable_instagram'],
					'transport' 		=> 'postMessage',
					'sanitize_callback' => 'fallsky_sanitize_checkbox'
				) ) );
				$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_site_footer_instagram_username', array(
					'default'   		=> $fallsky_default_settings['fallsky_site_footer_instagram_username'],
					'transport' 		=> 'postMessage',
					'sanitize_callback' => 'sanitize_text_field',
					'dependency'		=> array(
						'fallsky_site_footer_enable_instagram' => array( 'value' => array( 'on' ) )
					)
				) ) );
				$wp_customize->selective_refresh->add_partial( 'fallsky_site_footer_instagram_username', array(
					'settings'				=> array( 'fallsky_site_footer_instagram_group', 'fallsky_site_footer_instagram_username' ),
					'selector'				=> '#fallsky-site-footer-instagram',
					'render_callback'		=> array( $this, 'instagram_widget' ),
					'container_inclusive' 	=> true
				) );
				$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_site_footer_instagram_title', array(
					'default'   		=> $fallsky_default_settings['fallsky_site_footer_instagram_title'],
					'transport' 		=> 'postMessage',
					'sanitize_callback' => 'sanitize_text_field',
					'dependency' 		=> array(
						'fallsky_site_footer_enable_instagram' => array( 'value' => array( 'on' ) )
					)
				) ) );
				$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_site_footer_instagram_title_layout', array(
					'default'   		=> $fallsky_default_settings['fallsky_site_footer_instagram_title_layout'],
					'transport' 		=> 'postMessage',
					'sanitize_callback' => 'fallsky_sanitize_choice',
					'dependency' 		=> array(
						'fallsky_site_footer_enable_instagram' 	=> array( 'value' => array( 'on' ) ),
						'fallsky_site_footer_instagram_title'	=> array( 'value' => array( '' ), 'operator' => 'not in' )
					)
				) ) );
				$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_site_footer_instagram_columns', array(
					'default'   		=> $fallsky_default_settings['fallsky_site_footer_instagram_columns'],
					'transport' 		=> 'refresh',
					'sanitize_callback' => 'absint',
					'dependency' 		=> array(
						'fallsky_site_footer_enable_instagram' => array( 'value' => array( 'on' ) )
					)
				) ) );
				$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_site_footer_instagram_fullwidth', array(
					'default'   		=> $fallsky_default_settings['fallsky_site_footer_instagram_fullwidth'],
					'transport' 		=> 'postMessage',
					'sanitize_callback' => 'fallsky_sanitize_checkbox',
					'dependency' 		=> array(
						'fallsky_site_footer_enable_instagram' => array( 'value' => array( 'on' ) )
					)
				) ) );
				$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_site_footer_instagram_space', array(
					'default'   		=> $fallsky_default_settings['fallsky_site_footer_instagram_space'],
					'transport' 		=> 'postMessage',
					'sanitize_callback' => 'absint',
					'dependency' 		=> array(
						'fallsky_site_footer_enable_instagram' => array( 'value' => array( 'on' ) )
					)
				) ) );
				$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_site_footer_instagram_new_tab', array(
					'default'   		=> $fallsky_default_settings['fallsky_site_footer_instagram_new_tab'],
					'transport' 		=> 'refresh',
					'sanitize_callback' => 'fallsky_sanitize_checkbox',
					'dependency' 		=> array(
						'fallsky_site_footer_enable_instagram' => array( 'value' => array( 'on' ) )
					)
				) ) );
			}

			$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_site_footer_bottom_group', array(
				'default' 			=> '',
				'sanitize_callback' => 'fallsky_sanitize_empty'
			) ) );
			$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_site_footer_bottom_layout', array(
				'default'   		=> $fallsky_default_settings['fallsky_site_footer_bottom_layout'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			) ) );
			$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_site_footer_bottom_enable_menu', array(
				'default'   		=> $fallsky_default_settings['fallsky_site_footer_bottom_enable_menu'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_checkbox'
			) ) );
			$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_site_footer_bottom_menu_type', array(
				'default'   		=> $fallsky_default_settings['fallsky_site_footer_bottom_menu_type'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_choice',
				'dependency'		=> array(
					'fallsky_site_footer_bottom_enable_menu' => array( 'value' => array( 'on' ) )
				)
			) ) );
			$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_site_footer_bottom_menu', array(
				'default'   		=> $fallsky_default_settings['fallsky_site_footer_bottom_menu'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_choice',
				'dependency'		=> array(
					'fallsky_site_footer_bottom_enable_menu' 	=> array( 'value' => array( 'on' ) ),
					'fallsky_site_footer_bottom_menu_type'		=> array( 'value' => array( 'nav' ) )
				)
			) ) );
			$wp_customize->selective_refresh->add_partial( 'fallsky_site_footer_bottom_menu', array(
				'selector'				=> '.footer-bottom .preview-footer-bottom-menu',
				'render_callback'		=> array( $this, 'show_footer_bottom_menu' ),
				'container_inclusive' 	=> true,
				'settings'				=> array(
					'fallsky_site_footer_bottom_group', 
					'fallsky_site_footer_bottom_menu', 
					'fallsky_site_footer_bottom_menu_type'
				)
			) );
			$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_site_footer_bottom_text', array(
				'default'   		=> $fallsky_default_settings['fallsky_site_footer_bottom_text'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'wp_kses_post'
			) ) );

			$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_site_footer_bg_image', array(
				'default'   		=> $fallsky_default_settings['fallsky_site_footer_bg_image'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'absint'
			) ) );
			$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_site_footer_bg_size', array(
				'default'			=> $fallsky_default_settings['fallsky_site_footer_bg_size'],
				'transport'			=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_choice',
				'dependency' 		=> array(
					'fallsky_site_footer_bg_image' => array( 'value' => array( '' ), 'operator' => 'not in' )
				)
			) ) );
			$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_site_footer_bg_repeat', array(
				'default'   		=> $fallsky_default_settings['fallsky_site_footer_bg_repeat'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_checkbox',
				'dependency' 		=> array(
					'fallsky_site_footer_bg_image' => array( 'value' => array( '' ), 'operator' => 'not in' )
				)
			) ) );
			$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_site_footer_bg_position_x', array(
				'default'			=> $fallsky_default_settings['fallsky_site_footer_bg_position_x'],
				'transport'			=> 'postMessage',
				'sanitize_callback' => 'sanitize_text_field',
				'dependency' 		=> array(
					'fallsky_site_footer_bg_image' => array( 'value' => array( '' ), 'operator' => 'not in' )
				)
			) ) );
			$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_site_footer_bg_position_y', array(
				'default'  			=> $fallsky_default_settings['fallsky_site_footer_bg_position_y'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'sanitize_text_field',
				'dependency' 		=> array(
					'fallsky_site_footer_bg_image' => array( 'value' => array( '' ), 'operator' => 'not in' )
				)
			) ) );
			$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_site_footer_bg_attachment', array(
				'default'			=> $fallsky_default_settings['fallsky_site_footer_bg_attachment'],
				'transport'			=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_checkbox',
				'dependency' 		=> array(
					'fallsky_site_footer_bg_image' => array( 'value' => array( '' ), 'operator' => 'not in' )
				)
			) ) );
			$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_site_footer_color_scheme', array(
				'default' 			=> $fallsky_default_settings['fallsky_site_footer_color_scheme'],
				'transport'			=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			) ) );
			$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_site_footer_bg_color', array(
				'default'   		=> $fallsky_default_settings['fallsky_site_footer_bg_color'],
				'transport'			=> 'postMessage',
				'sanitize_callback' => 'sanitize_hex_color'
			) ) );
			$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_site_footer_text_color', array(
				'default'   		=> $fallsky_default_settings['fallsky_site_footer_text_color'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'sanitize_hex_color'
			) ) );
			$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_site_footer_bottom_color', array(
				'default' 			=> $fallsky_default_settings['fallsky_site_footer_bottom_color'],
				'transport'			=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			) ) );
			$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_site_footer_bottom_custom_bg_color', array(
				'default'   		=> $fallsky_default_settings['fallsky_site_footer_bottom_custom_bg_color'],
				'transport'			=> 'refresh',
				'sanitize_callback' => 'sanitize_hex_color',
				'dependency'		=> array(
					'fallsky_site_footer_bottom_color' => array( 'value' => array( 'custom' ) )
				)
			) ) );
			$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_site_footer_bottom_custom_text_color', array(
				'default'   		=> $fallsky_default_settings['fallsky_site_footer_bottom_custom_text_color'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'sanitize_hex_color',
				'dependency'		=> array(
					'fallsky_site_footer_bottom_color' => array( 'value' => array( 'custom' ) )
				)
			) ) );

			// Controls
			if ( class_exists( 'Fallsky_Extension' ) ) {
				$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_site_footer_instagram_group', array(
					'type' 		=> 'group',
					'label' 	=> esc_html__( 'Instagram', 'fallsky' ),
					'section' 	=> 'fallsky_section_site_footer_content',
					'settings' 	=> 'fallsky_site_footer_instagram_group',
					'children'	=> array(
						new Fallsky_Customize_Control( $wp_customize, 'fallsky_site_footer_enable_instagram', array(
							'type' 			=> 'checkbox',
							'label_first'	=> true,
							'label' 		=> esc_html__( 'Display Instagram Feed', 'fallsky' ),
							'section' 		=> 'fallsky_section_site_footer_content',
							'settings' 		=> 'fallsky_site_footer_enable_instagram'
						) ),
						new Fallsky_Customize_Control( $wp_customize, 'fallsky_site_footer_instagram_username', array(
							'type' 				=> 'text',
							'label' 			=> esc_html__( 'Instagram Account', 'fallsky' ),
							'section' 			=> 'fallsky_section_site_footer_content',
							'settings' 			=> 'fallsky_site_footer_instagram_username',
							'active_callback' 	=> 'fallsky_customize_control_active_cb',
							'input_attrs' 		=> array(
								'placeholder'=> esc_html__( 'Enter your Instagram Account', 'fallsky' )
							)
						) ),
						new Fallsky_Customize_Control( $wp_customize, 'fallsky_site_footer_instagram_title', array(
							'type'				=> 'text',
							'label' 			=> esc_html__( 'Title (optional)', 'fallsky' ),
							'section' 			=> 'fallsky_section_site_footer_content',
							'settings' 			=> 'fallsky_site_footer_instagram_title',
							'active_callback' 	=> 'fallsky_customize_control_active_cb',
							'input_attrs' 		=> array(
								'placeholder'=> esc_html__( 'Title (optional)', 'fallsky' )
							)
						) ),
						new Fallsky_Customize_Control( $wp_customize, 'fallsky_site_footer_instagram_title_layout', array(
							'type' 				=> 'select',
							'label' 			=> esc_html__('Title Layout', 'fallsky'),
							'section' 			=> 'fallsky_section_site_footer_content',
							'settings' 			=> 'fallsky_site_footer_instagram_title_layout',
							'active_callback' 	=> 'fallsky_customize_control_active_cb',
							'choices'			=> array(
								''			=> esc_html__( 'Default', 'fallsky' ),
								'overlay'	=> esc_html__( 'Overlay', 'fallsky' )
							)
						) ),
						new Fallsky_Customize_Control( $wp_customize, 'fallsky_site_footer_instagram_columns', array(
							'type' 				=> 'number',
							'label' 			=> esc_html__( 'Display x photos (4 to 8)', 'fallsky' ),
							'section' 			=> 'fallsky_section_site_footer_content',
							'settings' 			=> 'fallsky_site_footer_instagram_columns',
							'active_callback' 	=> 'fallsky_customize_control_active_cb',
							'input_attrs' 		=> array( 'min' => 4, 'max' => 8 )
						) ),
						new Fallsky_Customize_Control( $wp_customize, 'fallsky_site_footer_instagram_fullwidth', array(
							'type' 				=> 'checkbox',
							'label_first'		=> true,
							'label' 			=> esc_html__( 'Make it fullwidth', 'fallsky' ),
							'section' 			=> 'fallsky_section_site_footer_content',
							'settings' 			=> 'fallsky_site_footer_instagram_fullwidth',
							'active_callback' 	=> 'fallsky_customize_control_active_cb'
						) ),
						new Fallsky_Customize_Control( $wp_customize, 'fallsky_site_footer_instagram_space', array(
							'type' 				=> 'number_slider',
							'label' 			=> esc_html__( 'Space around each photo', 'fallsky' ),
							'section' 			=> 'fallsky_section_site_footer_content',
							'settings' 			=> 'fallsky_site_footer_instagram_space',
							'active_callback' 	=> 'fallsky_customize_control_active_cb',
							'after_text'		=> esc_html__( 'px', 'fallsky' ),
							'input_attrs'		=> array(
								'data-min'	=> '0',
								'data-max'	=> '30',
								'data-step'	=> '5'
							)
						) ),
						new Fallsky_Customize_Control( $wp_customize, 'fallsky_site_footer_instagram_new_tab', array(
							'type' 				=> 'checkbox',
							'label_first'		=> true,
							'label' 			=> esc_html__( 'Open links in new tab', 'fallsky' ),
							'section' 			=> 'fallsky_section_site_footer_content',
							'settings' 			=> 'fallsky_site_footer_instagram_new_tab',
							'active_callback' 	=> 'fallsky_customize_control_active_cb'
						) )
					)
				) ) );
			}

			$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_site_footer_bottom_group', array(
				'type' 		=> 'group',
				'label' 	=> esc_html__( 'Footer Bottom', 'fallsky' ),
				'section' 	=> 'fallsky_section_site_footer_content',
				'settings'	=> 'fallsky_site_footer_bottom_group',
				'children' 	=> array(
					new Fallsky_Customize_Control( $wp_customize, 'fallsky_site_footer_bottom_layout', array(
						'type' 		=> 'select',
						'label' 	=> esc_html__( 'Layout', 'fallsky' ),
						'section' 	=> 'fallsky_section_site_footer_content',
						'settings' 	=> 'fallsky_site_footer_bottom_layout',
						'choices'	=> array(
							''			=> esc_html__( '1 Column', 'fallsky' ),
							'column-2'	=> esc_html__( '2 Columns', 'fallsky' )
						)
					) ),
					new Fallsky_Customize_Control( $wp_customize, 'fallsky_site_footer_bottom_enable_menu', array(
						'type' 			=> 'checkbox',
						'label_first'	=> true,
						'label' 		=> esc_html__( 'Display a menu', 'fallsky' ),
						'section' 		=> 'fallsky_section_site_footer_content',
						'settings' 		=> 'fallsky_site_footer_bottom_enable_menu'
					) ),
					new Fallsky_Customize_Control( $wp_customize, 'fallsky_site_footer_bottom_menu_type', array(
						'type'				=> 'select',
						'section' 			=> 'fallsky_section_site_footer_content',
						'settings' 			=> 'fallsky_site_footer_bottom_menu_type',
						'active_callback'	=> 'fallsky_customize_control_active_cb',
						'choices'			=> array(
							'social' 	=> esc_html__( 'Social Menu', 'fallsky' ),
							'nav' 		=> esc_html__( 'Navigation Menu', 'fallsky' )
						),
						'description'		=> sprintf(
							esc_html__( 'You can choose your social menu to display social icons here. %sClick here%s to know how to setup the social menu.', 'fallsky' ),
							sprintf('<a href="%s" target="_blank">', 'https://www.loftocean.com/fallsky/social/display-social-media-icons-on-your-site/'),
							'</a>'
						)
					) ),
					new Fallsky_Customize_Control( $wp_customize, 'fallsky_site_footer_bottom_menu', array(
						'type'				=> 'select',
						'section' 			=> 'fallsky_section_site_footer_content',
						'settings' 			=> 'fallsky_site_footer_bottom_menu',
						'active_callback'	=> 'fallsky_customize_control_active_cb',
						'choices'			=> fallsky_get_menus()
					) ),
					new Fallsky_Customize_Control( $wp_customize, 'fallsky_site_footer_bottom_text', array(
						'type' 		=> 'textarea',
						'label' 	=> esc_html__( 'Footer Text', 'fallsky' ),
						'section' 	=> 'fallsky_section_site_footer_content',
						'settings' 	=> 'fallsky_site_footer_bottom_text'
					) )
				)
			) ) );


			$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_site_footer_bg_image', array(
				'type' 		=> 'image_id',
				'label' 	=> esc_html__( 'Footer Background Image', 'fallsky' ),
				'section' 	=> 'fallsky_section_site_footer_design',
				'settings' 	=> 'fallsky_site_footer_bg_image'
			) ) );
			$wp_customize->add_control( new WP_Customize_Background_Position_Control( $wp_customize, 'fallsky_site_footer_bg_position', array(
				'label' 			=> esc_html__( 'Image Position', 'fallsky' ),
				'section'			=> 'fallsky_section_site_footer_design',
				'active_callback' 	=> 'fallsky_customize_control_active_cb',
				'settings' 			=> array(
					'x' => 'fallsky_site_footer_bg_position_x',
					'y' => 'fallsky_site_footer_bg_position_y'
				)
			) ) );
			$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_site_footer_bg_size', array(
				'type' 				=> 'select',
				'label' 			=> esc_html__( 'Size', 'fallsky' ),
				'section' 			=> 'fallsky_section_site_footer_design',
				'settings' 			=> 'fallsky_site_footer_bg_size',
				'active_callback' 	=> 'fallsky_customize_control_active_cb',
				'choices' 			=> array(
					'auto' 		=> esc_html__( 'Original', 'fallsky' ),
					'contain' 	=> esc_html__( 'Fit to Screen', 'fallsky' ),
					'cover'		=> esc_html__( 'Fill Screen', 'fallsky' )
				)
			) ) );
			$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_site_footer_bg_repeat', array(
				'type' 				=> 'checkbox',
				'label' 			=> esc_html__( 'Repeat', 'fallsky' ),
				'section' 			=> 'fallsky_section_site_footer_design',
				'settings' 			=> 'fallsky_site_footer_bg_repeat',
				'active_callback' 	=> 'fallsky_customize_control_active_cb'
			) ) );
			$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_site_footer_bg_attachment', array(
				'type' 				=> 'checkbox',
				'label' 			=> esc_html__( 'Scroll with Page', 'fallsky' ),
				'section' 			=> 'fallsky_section_site_footer_design',
				'settings'			=> 'fallsky_site_footer_bg_attachment',
				'active_callback' 	=> 'fallsky_customize_control_active_cb'
			) ) );
			$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_site_footer_color_scheme', array(
				'type' 		=> 'radio',
				'label' 	=> esc_html__( 'Color Scheme', 'fallsky' ),
				'section' 	=> 'fallsky_section_site_footer_design',
				'settings' 	=> 'fallsky_site_footer_color_scheme',
				'choices'	=> array(
					'light-color'	=> esc_html__( 'Light', 'fallsky' ),
					'dark-color'	=> esc_html__( 'Dark', 'fallsky' )
				)
			) ) );
			$wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'fallsky_site_footer_bg_color', array(
				'label'    => esc_html__( 'Background Color', 'fallsky' ),
				'section'  => 'fallsky_section_site_footer_design',
				'settings' => 'fallsky_site_footer_bg_color'
			) ) );
			$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'fallsky_site_footer_text_color', array(
				'label'    => esc_html__( 'Text Color', 'fallsky' ),
				'section'  => 'fallsky_section_site_footer_design',
				'settings' => 'fallsky_site_footer_text_color'
			) ) );
			$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_site_footer_bottom_color', array(
				'type' 		=> 'select',
				'label' 	=> esc_html__( 'Footer Bottom Color', 'fallsky' ),
				'section' 	=> 'fallsky_section_site_footer_design',
				'settings' 	=> 'fallsky_site_footer_bottom_color',
				'choices'	=> array(
					'inherit'	=> esc_html__( 'Inherit', 'fallsky' ),
					'custom'	=> esc_html__( 'Custom', 'fallsky' )
				)
			) ) );
			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'fallsky_site_footer_bottom_custom_bg_color', array(
				'label'    			=> esc_html__( 'Footer Bottom Background Color', 'fallsky' ),
				'section'  			=> 'fallsky_section_site_footer_design',
				'settings' 			=> 'fallsky_site_footer_bottom_custom_bg_color',
				'active_callback' 	=> 'fallsky_customize_control_active_cb'
			) ) );
			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'fallsky_site_footer_bottom_custom_text_color', array(
				'label'   		 	=> esc_html__( 'Footer Bottom Text Color', 'fallsky' ),
				'section' 		 	=> 'fallsky_section_site_footer_design',
				'settings' 			=> 'fallsky_site_footer_bottom_custom_text_color',
				'active_callback' 	=> 'fallsky_customize_control_active_cb'
			) ) );
		}
		protected function get_custom_styles() {
			global $fallsky_default_settings, $fallsky_is_preview;
			$styles = array();

			$styles['site-footer-bg-color'] = fallsky_get_style(
				'fallsky_site_footer_bg_color',
				'#page .site-footer',
				'background-color: %s;'
			);
			$styles['site-footer-text-color'] = fallsky_get_style(
				'fallsky_site_footer_text_color',
				'#page .site-footer',
				'color: %s;'
			);
			// Background Image for ".search-screen > .container"
			$bg_image_id = absint( fallsky_get_theme_mod( 'fallsky_site_footer_bg_image' ) );
			if ( ! empty( $bg_image_id ) || $fallsky_is_preview ) {
				$image = empty( $bg_image_id ) ? false : fallsky_get_image_src( $bg_image_id, 'fallsky_large', false );
				if( $image || $fallsky_is_preview ) {
					$position_x 	= esc_attr( fallsky_get_theme_mod( 'fallsky_site_footer_bg_position_x' ) );
					$position_y 	= esc_attr( fallsky_get_theme_mod( 'fallsky_site_footer_bg_position_y' ) );
					$size 			= esc_attr( fallsky_get_theme_mod( 'fallsky_site_footer_bg_size' ) );
					$repeat 		= fallsky_module_enabled( 'fallsky_site_footer_bg_repeat' );
					$attachment 	= fallsky_module_enabled( 'fallsky_site_footer_bg_attachment' );

					if ( ! empty( $bg_image_id ) ) {
						$styles['site-footer-bg-image'] = sprintf(
							'#page .site-footer { %s }',
							sprintf( 'background-image: url(%s);', esc_url_raw( $image ) )
						);
					}
					$styles['site-footer-bg-image-size'] = sprintf(
						'#page .site-footer { %s }',
						sprintf( 'background-size: %s;', $size )
					);
					$styles['site-footer-bg-image-repeat'] = sprintf(
						'#page .site-footer { %s }',
						sprintf( 'background-repeat: %s;', ( $repeat ? 'repeat' : 'no-repeat' ) )
					);
					$styles['site-footer-bg-image-attachment'] = sprintf(
						'#page .site-footer { %s }',
						sprintf( 'background-attachment: %s;', ( $attachment ? 'scroll' : 'fixed' ) )
					);
					$styles['site-footer-bg-image-position-x'] = sprintf(
						'#page .site-footer { %s }',
						sprintf( 'background-position-x: %s;', $position_x )
					);
					$styles['site-footer-bg-image-position-y'] = sprintf(
						'#page .site-footer { %s }',
						sprintf( 'background-position-y: %s;', $position_y )
					);
				}
			}

			if ( class_exists( 'Fallsky_Extension' ) ) {
				$padding = absint(fallsky_get_theme_mod('fallsky_site_footer_instagram_space'));
				if ( ! empty( $padding ) ) {
					$styles['site-footer-instagram-ul'] = sprintf(
						'#fallsky-site-footer-instagram > ul { %s }',
						sprintf('margin: %spx;', -$padding)
					);
					$styles['site-footer-instagram-li'] = sprintf(
						'#fallsky-site-footer-instagram > ul > li { %s }',
						sprintf( 'padding: %spx;', $padding )
					);
				}
			}

			if ( 'custom' == fallsky_get_theme_mod( 'fallsky_site_footer_bottom_color' ) ) {
				$custom_bottom_bg_color 	= fallsky_get_theme_mod( 'fallsky_site_footer_bottom_custom_bg_color' );
				$custom_bottom_text_color 	= fallsky_get_theme_mod( 'fallsky_site_footer_bottom_custom_text_color' );
				$footer_bottom_colors		= sprintf(
					'%s%s', 
					empty($custom_bottom_bg_color) ? '' : sprintf('background: %s; ', $custom_bottom_bg_color),
					empty($custom_bottom_text_color) ? '' : sprintf('color: %s; ', $custom_bottom_text_color)
				);
				if ( ! empty( $footer_bottom_colors ) ) {
					$styles['site-footer-bottom-custom-color'] = sprintf( '#page .site-footer .footer-bottom { %s}', $footer_bottom_colors );
				}
			}

			return $styles;
		}
		public function frontend_actions() {
			add_action( 'fallsky_site_footer', 		array( $this, 'site_footer_content' ) );
			add_filter( 'fallsky_site_footer_class', array( $this, 'site_footer_class' ) );
		}
		public function site_footer_class( $class ) {
			array_push( $class, esc_attr( fallsky_get_theme_mod( 'fallsky_site_footer_color_scheme' ) ) );
			return $class;
		}
		private function get_instagram_html( $username, $number ) {
			global $fallsky_is_preview;
			$new_tab = $this->new_tab;
			if ( $fallsky_is_preview ) {
				$feeds = apply_filters('loftocean_instagram_feed', array(), $username, 8);
				if(!empty($feeds) && is_array($feeds)){
					$list_wrap 	= '<ul%s>%s</ul>';
					$item_wrap 	= '<li%s%s><a href="%s"%s><div class="feed-bg" style="background-image: url(%s);"></a></li>'; 
					$padding 	= absint(fallsky_get_theme_mod('fallsky_site_footer_instagram_space'));
					$li_padding = $ul_margin = '';
					$target 	= $new_tab ? ' target="_blank"' : ''; 
					if(!empty($padding)){
						$li_padding = sprintf(' style="padding: %spx;"', $padding);
						$ul_margin	= sprintf(' style="margin: -%spx;"', $padding);
					}
					array_walk($feeds, function(&$item, $key, $args){ 
						$item = sprintf(
							$args['template'], 
							$key < $args['number'] ? '' : ' class="hide"',
							$args['padding'],
							$item['link'],
							$args['target'],
							$item['small']
						);
					}, array( 'template' => $item_wrap, 'number' => $number, 'padding' => $li_padding, 'target' => $target ) );

					return sprintf($list_wrap, $ul_margin, implode('', $feeds));
				}
			}
			else{
				return apply_filters( 'loftocean_instagram_html', '', $username, $number, $new_tab );
			}
		}
		public function instagram_widget(){
			global $fallsky_is_preview;

			$username 		= esc_attr( fallsky_get_theme_mod( 'fallsky_site_footer_instagram_username' ) );
			$show_instagram = fallsky_module_enabled( 'fallsky_site_footer_enable_instagram' );
			if ( class_exists( 'LoftOcean_Widget_Instagram' ) && ( $fallsky_is_preview || $show_instagram ) && !empty( $username ) ) {
				$this->new_tab = fallsky_module_enabled( 'fallsky_site_footer_instagram_new_tab' );
				$target 	= $this->new_tab ? ' target="_blank"' : '';
				$username 	= strtolower( $username );
				$username 	= str_replace( '@', '', $username );
				$columns	= intval( fallsky_get_theme_mod( 'fallsky_site_footer_instagram_columns' ) );
				$columns 	= in_array($columns, array(4, 5, 6, 7, 8)) ? $columns : 6;
				$feed_html 	= $this->get_instagram_html( $username, $columns );
				$attrs 		= sprintf( 'data-user="%s" data-limit="%s" data-new-tab="%s"', $username, $columns, $this->new_tab );
				if ( ! empty( $feed_html ) ) {
					$title_text		= fallsky_get_theme_mod( 'fallsky_site_footer_instagram_title' );
					$title_hide 	= empty( $title_text ) && $fallsky_is_preview ? ' hide' : '';
					$title_class 	= ( 'overlay' == fallsky_get_theme_mod( 'fallsky_site_footer_instagram_title_layout' ) ) ? ' overlay-title' : ''; 
					$by_ajax 		= fallsky_module_enabled( 'fallsky_instagram_render_type' );
					printf(
						'<div id="fallsky-site-footer-instagram" class="widget fallsky-widget_instagram column-%s%s%s"%s%s>%s%s</div>',
						$columns,
						fallsky_module_enabled('fallsky_site_footer_instagram_fullwidth') ? ' fullwidth' : '',
						$fallsky_is_preview && !$show_instagram ? ' hide' : '',
						$fallsky_is_preview ? sprintf(' data-columns="%s"', $columns) : '',
						$by_ajax ? $attrs : '',
						! empty( $title_text ) || $fallsky_is_preview ? sprintf(
							'<h5 class="widget-title%s%s"><a href="%s"%s>%s</a></h5>', 
							$title_class,
							$title_hide, 
							esc_url( sprintf( 'https://www.instagram.com/%s', $username ) ), 
							$target,
							esc_html( $title_text )
						) : '',
						$by_ajax ? '' : fallsky_sanitize_html( $feed_html )
					);
				}
			}
		}
		public function show_footer_bottom_menu() {
			global $fallsky_is_preview;
			$menu_id 		= absint( fallsky_get_theme_mod( 'fallsky_site_footer_bottom_menu' ) );
			$menu_type 		= esc_attr( fallsky_get_theme_mod( 'fallsky_site_footer_bottom_menu_type' ) );
			$nav_menu 		= ( empty( $menu_id ) ? false : wp_get_nav_menu_object( $menu_id ) );
			$show_menu 		= fallsky_module_enabled( 'fallsky_site_footer_bottom_enable_menu' );
			$menu_hide		= $fallsky_is_preview && !$show_menu ? ' hide' : '';
			$preview_class 	= $fallsky_is_preview ? ' preview-footer-bottom-menu' : '';

			if ( $show_menu || $fallsky_is_preview ) {
				$show_empty = true;
				if ( ( 'social' == $menu_type ) ) {
					ob_start();
					the_widget( 'Fallsky_Widget_Social', array() );
					$social = ob_get_clean();
					if ( ! empty( $social ) ) {
						$show_empty = false;
						printf(
							'<div class="footer-social%s%s">%s</div>',
							$menu_hide,
							$preview_class,
							$social
						);
					}
				} else {
					if ( $nav_menu ) {
						$show_empty = false;
						printf(
							'<div class="footer-bottom-menu%s%s"><div class="widget widget_nav_menu">%s</div></div>',
							$menu_hide,
							$preview_class,
							wp_nav_menu( array( 'menu' => $nav_menu, 'echo' => false, 'container_class' => '', 'fallback_cb' => '', 'depth' => 1 ) )
						);
					}
				}
				if ( $show_empty ) {
					echo '<div class="preview-footer-bottom-menu hide"></div>';
				}
			}
		}
		private function show_footer_bottom_text() {
			global $fallsky_is_preview;
			$text 		= wp_kses_post( fallsky_get_theme_mod( 'fallsky_site_footer_bottom_text' ) );
			$has_text	= ! empty( $text );
			$text_hide 	= $fallsky_is_preview && !$has_text ? ' hide' : '';

			if ( $fallsky_is_preview || $has_text ) {
				printf(
					'<div class="footer-site-info%s"><div class="widget widget_text"><div class="textwidget">%s</div></div></div>',
					$text_hide,
					$has_text ? $text : ''
				);
			}
		}
		public function site_footer_content() { 
			$bottom_columns = fallsky_get_theme_mod( 'fallsky_site_footer_bottom_layout' ); 
			$bottom_class 	= empty( $bottom_columns ) ? '' : sprintf( ' %s', $bottom_columns ); ?>

			<footer id="colophon"<?php fallsky_site_footer_class(); ?>>
				<?php if ( is_active_sidebar( 'footer-column-1' ) || is_active_sidebar( 'footer-column-2' ) || is_active_sidebar( 'footer-column-3' ) ) : ?>
				<div class="widget-area">
					<div class="container">
						<div class="widget-area-row">
						<?php for ( $i = 1; $i < 4; $i++ ) : ?>
							<?php if ( is_active_sidebar( 'footer-column-' . $i ) ) : ?>
								<div class="widget-area-column">
								<?php dynamic_sidebar( 'footer-column-' . $i ); ?>
								</div>
							<?php endif; ?>
						<?php endfor; ?>
						</div>
					</div>
				</div>
				<?php endif; ?>
				<?php $this->instagram_widget(); ?>
						
				<div class="footer-bottom<?php print( $bottom_class ); ?>">
					<div class="container">
						<?php $this->show_footer_bottom_menu(); ?>
						<?php $this->show_footer_bottom_text(); ?>
					</div>
				</div>
				
			</footer> <?php
		}
	}
	new Fallsky_Customize_Footer();
}