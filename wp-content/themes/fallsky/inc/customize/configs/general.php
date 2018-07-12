<?php
/**
* Customize section general configuration files.
*/

if ( ! class_exists( 'Fallsky_Customize_General' ) ) {
	class Fallsky_Customize_General extends Fallsky_Customize_Base {
		public function __construct() {
			parent::__construct();
			add_filter( 'loftocean_instagram_render_type', array( $this, 'render_instagram_by' ) );
		}
		public function register_controls( $wp_customize ) {
			global $fallsky_default_settings;
			$post_list_layouts = array(
				'masonry' 	=> esc_html__( 'Masonry', 'fallsky' ), 
				'list_1col' => esc_html__( 'List 1 Column', 'fallsky' ), 
				'list_2col' => esc_html__( 'List 2 Columns', 'fallsky' ), 
				'zigzag' 	=> esc_html__( 'Zigzag', 'fallsky' ), 
				'grid' 		=> esc_html__( 'Grid', 'fallsky' ), 
				'card' 		=> esc_html__( 'Card', 'fallsky' )
			);

			// // Panel
			$wp_customize->add_panel( 'fallsky_panel_general', array(
				'title'    => esc_html__( 'General', 'fallsky' ),
				'priority' => 5
			) );
			// Sections
			$wp_customize->add_section( 'fallsky_section_general_layouts', array(
				'title' => esc_html__( 'Site Layout', 'fallsky' ),
				'panel' => 'fallsky_panel_general'
			) );
			$wp_customize->add_section( 'fallsky_section_general_posts', array(
				'title' => esc_html__( 'Posts General Options', 'fallsky' ),
				'panel' => 'fallsky_panel_general'
			) );
			$wp_customize->add_section( 'fallsky_section_general_colors', array(
				'title' => esc_html__( 'General Colors', 'fallsky' ),
				'panel' => 'fallsky_panel_general'
			) );
			$wp_customize->add_section( 'fallsky_section_general_image_slider', array(
				'title'	=> esc_html__( 'Image Sliders', 'fallsky' ),
				'panel' => 'fallsky_panel_general'
			) );

			// Settings
			$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_page_group_background', array(
				'default'			=> '',
				'sanitize_callback' => 'fallsky_sanitize_empty'
			) ) );
			$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_page_background_image', array(
				'default'   		=> $fallsky_default_settings['fallsky_page_background_image'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'absint'
			) ) );
			$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_page_background_position_x', array(
				'default'			=> $fallsky_default_settings['fallsky_page_background_position_x'],
				'transport'			=> 'postMessage',
				'sanitize_callback' => 'sanitize_text_field',
				'dependency' 		=> array(
					'fallsky_page_background_image' => array( 'value' => array( '' ), 'operator' => 'not in' )
				)
			) ) );
			$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_page_background_position_y', array(
				'default'   		=> $fallsky_default_settings['fallsky_page_background_position_y'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'sanitize_text_field',
				'dependency' 		=> array(
					'fallsky_page_background_image' => array( 'value' => array( '' ), 'operator' => 'not in' )
				)
			) ) );
			$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_page_background_size', array(
				'default'  			=> $fallsky_default_settings['fallsky_page_background_size'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_choice',
				'dependency' 		=> array(
					'fallsky_page_background_image' => array( 'value' => array( '' ), 'operator' => 'not in' )
				)
			) ) );
			$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_page_background_repeat', array(
				'default'			=> $fallsky_default_settings['fallsky_page_background_repeat'],
				'transport'			=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_checkbox',
				'dependency' 		=> array(
					'fallsky_page_background_image' => array( 'value' => array( '' ), 'operator' => 'not in' )
				)
			) ) );
			$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_page_background_attachment', array(
				'default'			=> $fallsky_default_settings['fallsky_page_background_attachment'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_checkbox',
				'dependency' 		=> array(
					'fallsky_page_background_image' => array( 'value' => array( '' ), 'operator' => 'not in' )
				)
			) ) );
			$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_site_layout', array(
				'default' 			=> $fallsky_default_settings['fallsky_site_layout'],
				'transport'			=> 'refresh',
				'sanitize_callback'	=> 'fallsky_sanitize_choice'
			) ) );
			$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_site_layout_options_group', array(
				'default'   		=> '',
				'sanitize_callback' => 'fallsky_sanitize_empty',
				'dependency' 		=> array(
					'fallsky_site_layout' => array( 'value' => array( 'site-layout-boxed', 'site-layout-frame' ) )
				)
			) ) );
			$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_site_container_width', array(
				'default'   		=> $fallsky_default_settings['fallsky_site_container_width'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			) ) );


			$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_read_more_text', array(
				'default'			=> $fallsky_default_settings['fallsky_read_more_text'],
				'transport'			=> 'refresh',
				'sanitize_callback'	=> 'sanitize_text_field'
			) ) );
			$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_pagination_style', array(
				'default'			=> $fallsky_default_settings['fallsky_pagination_style'],
				'transport'			=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			) ) );

			$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_comment_group', array(
				'default'   		=> '',
				'transport'			=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_empty'
			) ) );
			$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_comment_fold_reply_form', array(
				'default'   		=> $fallsky_default_settings['fallsky_comment_fold_reply_form'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_checkbox'
			) ) );
			$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_comment_location', array(
				'default'			=> $fallsky_default_settings['fallsky_comment_location'],
				'transport'			=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			) ) );

			$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_post_excerpt_length_group', array(
				'default'   		=> '',
				'transport'			=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_empty'
			) ) );

			$wp_customize->add_setting( new WP_Customize_Setting($wp_customize, 'fallsky_site_color_scheme', array(
				'default'			=> $fallsky_default_settings['fallsky_site_color_scheme'],
				'transport'			=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			) ) );
			$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_accent_color', array(
				'default'			=> $fallsky_default_settings['fallsky_accent_color'],
				'transport'			=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			) ) );
			$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_accent_custom_color', array(
				'default'			=> $fallsky_default_settings['fallsky_accent_custom_color'],
				'transport'			=> 'refresh',
				'sanitize_callback' => 'sanitize_hex_color',
				'dependency'		=> array(
					'fallsky_accent_color' => array( 'value' => array( 'custom' ) )
				)
			) ) );
			$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_accent_color_notes', array(
				'default'			=> '',
				'transport'			=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_empty'
			) ) );

			$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_group_tweak_colors', array(
				'default'			=> '',
				'transport'			=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_empty'
			) ) );
			$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_light_color_scheme_custom_bg', array(
				'default'			=> $fallsky_default_settings['fallsky_light_color_scheme_custom_bg'],
				'transport'			=> 'refresh',
				'sanitize_callback' => 'sanitize_hex_color'
			) ) );
			$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_light_color_scheme_custom_text', array(
				'default'			=> $fallsky_default_settings['fallsky_light_color_scheme_custom_text'],
				'transport'			=> 'refresh',
				'sanitize_callback' => 'sanitize_hex_color'
			) ) );
			$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_light_color_scheme_custom_content', array(
				'default'			=> $fallsky_default_settings['fallsky_light_color_scheme_custom_content'],
				'transport'			=> 'refresh',
				'sanitize_callback' => 'sanitize_hex_color'
			) ) );
			$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_dark_color_scheme_custom_bg', array(
				'default'			=> $fallsky_default_settings['fallsky_dark_color_scheme_custom_bg'],
				'transport'			=> 'refresh',
				'sanitize_callback' => 'sanitize_hex_color'
			) ) );
			$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_dark_color_scheme_custom_text', array(
				'default'			=> $fallsky_default_settings['fallsky_dark_color_scheme_custom_text'],
				'transport'			=> 'refresh',
				'sanitize_callback' => 'sanitize_hex_color'
			) ) );
			$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_dark_color_scheme_custom_content', array(
				'default'			=> $fallsky_default_settings['fallsky_dark_color_scheme_custom_content'],
				'transport'			=> 'refresh',
				'sanitize_callback' => 'sanitize_hex_color'
			) ) );

			$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_mobile_image_slider_arrows_style', array(
				'default'			=> $fallsky_default_settings['fallsky_mobile_image_slider_arrows_style'],
				'transport'			=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			) ) );

			// Controls
			$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_page_group_background', array(
				'type' 		=> 'group',
				'label' 	=> esc_html__( 'Site Background Image', 'fallsky' ),
				'section' 	=> 'fallsky_section_general_layouts',
				'settings' 	=> 'fallsky_page_group_background',
				'priority'	=> 3,
				'children'	=> array(
					new Fallsky_Customize_Control( $wp_customize, 'fallsky_page_background_image', array(
						'type' 		=> 'image_id',
						'label' 	=> esc_html__( 'Site Background Image', 'fallsky' ),
						'section' 	=> 'fallsky_section_general_layouts',
						'settings' 	=> 'fallsky_page_background_image',
						'priority' 	=> 3,
					) ),
					new WP_Customize_Background_Position_Control( $wp_customize, 'fallsky_page_background_position', array(
						'label' 			=> esc_html__( 'Image Position', 'fallsky' ),
						'section'			=> 'fallsky_section_general_layouts',
						'priority' 			=> 3,
						'active_callback' 	=> 'fallsky_customize_control_active_cb',
						'settings' 			=> array(
							'x' => 'fallsky_page_background_position_x',
							'y' => 'fallsky_page_background_position_y'
						)
					) ),
					new Fallsky_Customize_Control( $wp_customize, 'fallsky_page_background_size', array(
						'type' 				=> 'select',
						'label' 			=> esc_html__( 'Image Size', 'fallsky' ),
						'section' 			=> 'fallsky_section_general_layouts',
						'settings' 			=> 'fallsky_page_background_size',
						'priority' 			=> 3,
						'active_callback' 	=> 'fallsky_customize_control_active_cb',
						'choices' 			=> array(
							'auto' 		=> esc_html__( 'Original', 'fallsky' ),
							'contain' 	=> esc_html__( 'Fit to Screen', 'fallsky' ),
							'cover'		=> esc_html__( 'Fill Screen', 'fallsky' )
						)
					) ),
					new Fallsky_Customize_Control( $wp_customize, 'fallsky_page_background_repeat', array(
						'type' 				=> 'checkbox',
						'label' 			=> esc_html__( 'Repeat Background Image', 'fallsky' ),
						'section' 			=> 'fallsky_section_general_layouts',
						'settings' 			=> 'fallsky_page_background_repeat',
						'priority' 			=> 3,
						'active_callback' 	=> 'fallsky_customize_control_active_cb'
					) ),
					new Fallsky_Customize_Control( $wp_customize, 'fallsky_page_background_attachment', array(
						'type' 				=> 'checkbox',
						'label' 			=> esc_html__( 'Scroll with Page', 'fallsky' ),
						'section' 			=> 'fallsky_section_general_layouts',
						'settings'			=> 'fallsky_page_background_attachment',
						'priority' 			=> 3,
						'active_callback' 	=> 'fallsky_customize_control_active_cb'
					) )
				)
			) ) );
			$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_site_layout', array(
				'type' 		=> 'select',
				'label'		=> esc_html__( 'Site Layout', 'fallsky' ),
				'section' 	=> 'fallsky_section_general_layouts',
				'settings'	=> 'fallsky_site_layout',
				'priority' 	=> 3,
				'choices'	=> array(
					'site-layout-fullwidth' => esc_html__( 'Fullwidth', 'fallsky' ),
					'site-layout-boxed' 	=> esc_html__( 'Boxed', 'fallsky' ),
					'site-layout-frame' 	=> esc_html__( 'Frame', 'fallsky' )
				)
			) ) );
			$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_site_layout_options_group', array(
				'type' 				=> 'title_only',
				'label' 			=> esc_html__( 'Options', 'fallsky' ),
				'section' 			=> 'fallsky_section_general_layouts',
				'settings'			=> 'fallsky_site_layout_options_group',
				'priority' 			=> 3,
				'active_callback' 	=> 'fallsky_customize_control_active_cb'
			) ) );
			$this->layout_option_settings( $wp_customize );

			$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_site_container_width', array(
				'type' 			=> 'select',
				'label'			=> esc_html__( 'Inner Container Width', 'fallsky' ),
				'description'	=> sprintf(
					esc_html__( 'This option determines the maximum width of the inner content area when the screen is narrower than 1440 pixels. For more information, please %sclick here%s.', 'fallsky' ),
					'<a href="http://www.loftocean.com/fallsky/style/inner-container-width/" target="_blank">',
					'</a>'
				),
				'section'		=> 'fallsky_section_general_layouts',
				'settings' 		=> 'fallsky_site_container_width',
				'choices' 		=> array(
					'' 					=> esc_html__( 'Normal', 'fallsky' ),
					'wide-container' 	=> esc_html__( 'Wide', 'fallsky' )
				)
			) ) );

			$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_read_more_text', array(
				'type' 		=> 'text',
				'label' 	=> esc_html__( 'Read More Button text', 'fallsky' ),
				'section' 	=> 'fallsky_section_general_posts',
				'settings' 	=> 'fallsky_read_more_text'
			) ) );
			$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_pagination_style', array(
				'type' 		=> 'select',
				'label'		=> esc_html__( 'Pagination Style', 'fallsky' ),
				'section'	=> 'fallsky_section_general_posts',
				'settings' 	=> 'fallsky_pagination_style',
				'choices' 	=> array(
					'next-prev' 	=> esc_html__( 'Next/Prev Links', 'fallsky' ),
					'page-number' 	=> esc_html__( 'With Page Number', 'fallsky' ),
					'ajax-more' 	=> esc_html__( 'Load More Button', 'fallsky' ),
					'ajax-infinite'	=> esc_html__( 'Infinite Scroll', 'fallsky' )
				)
			) ) );

			$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_comment_group', array(
				'type' 		=> 'group',
				'label' 	=> esc_html__( 'Comments Options', 'fallsky' ),
				'section' 	=> 'fallsky_section_general_posts',
				'settings'	=> 'fallsky_comment_group',
				'children'	=> array(
					new Fallsky_Customize_Control( $wp_customize, 'fallsky_comment_fold_reply_form', array(
						'type' 			=> 'checkbox',
						'label_first'	=> true,
						'label' 		=> esc_html__( 'Fold the reply form by default', 'fallsky' ),
						'section' 		=> 'fallsky_section_general_posts',
						'settings'		=> 'fallsky_comment_fold_reply_form'
					) ),
					new Fallsky_Customize_Control( $wp_customize, 'fallsky_comment_location', array(
						'type' 		=> 'select',
						'label'		=> esc_html__( 'Comments location on single post', 'fallsky' ),
						'section'	=> 'fallsky_section_general_posts',
						'settings' 	=> 'fallsky_comment_location',
						'choices' 	=> array(
							'after_post_content' => esc_html__( 'After post content', 'fallsky' ),
							'after_main_content' => esc_html__( 'After posts pagination & related posts', 'fallsky' )
						)
					) )
				)
			) ) );

			$layout_excerpt_length = array();
			foreach ( $post_list_layouts as $layout => $label ) {
				$layout_id = sprintf( 'fallsky_post_excerpt_length_for_layout_%s', $layout );
				$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, $layout_id, array(
					'default'   		=> $fallsky_default_settings[$layout_id],
					'transport' 		=> 'refresh',
					'sanitize_callback' => 'absint'
				) ) );
				array_push( $layout_excerpt_length, new Fallsky_Customize_Control( $wp_customize, $layout_id, array(
						'type'			=> 'number_slider',
						'label' 		=> $label,
						'after_text'	=> esc_html__( 'words', 'fallsky' ),
						'section' 		=> 'fallsky_section_general_posts',
						'settings' 		=> $layout_id,
						'input_attrs'	=> array(
							'data-min'	=> '10',
							'data-max'	=> '60',
							'data-step'	=> '5'
						)
					) )
				);
			}
			$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_post_excerpt_length_group', array(
				'type' 			=> 'group',
				'label' 		=> esc_html__( 'Post Excerpt Length', 'fallsky' ),
				'description' 	=> esc_html__( 'When choosing "Overlay" or "Overlay Mix", post excerpt will not display.', 'fallsky' ),
				'section' 		=> 'fallsky_section_general_posts',
				'settings'		=> 'fallsky_post_excerpt_length_group',
				'children'		=> $layout_excerpt_length
			) ) );

			$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_site_color_scheme', array(
				'type' 		=> 'radio',
				'label'		=> esc_html__( 'Site Color Scheme', 'fallsky' ),
				'section'	=> 'fallsky_section_general_colors',
				'settings' 	=> 'fallsky_site_color_scheme',
				'choices' 	=> array(
					'light-color'	=> esc_html__( 'Light', 'fallsky' ),
					'dark-color'	=> esc_html__( 'Dark', 'fallsky' )
				)
			) ) );
			$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_accent_color', array(
				'type' 		=> 'select',
				'label'		=> esc_html__( 'Accent Color', 'fallsky' ),
				'section'	=> 'fallsky_section_general_colors',
				'settings' 	=> 'fallsky_accent_color',
				'choices' 	=> array(
					'none' 		=> esc_html__( 'None, just black & white', 'fallsky' ),
					'custom'	=> esc_html__( 'Yes, I want to choose an accent color', 'fallsky' )
				)
			) ) );
			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'fallsky_accent_custom_color', array(
				'label'				=> '',
				'section' 			=> 'fallsky_section_general_colors',
				'settings'			=> 'fallsky_accent_custom_color',
				'active_callback'	=> 'fallsky_customize_control_active_cb'
			) ) );
			$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_accent_color_notes', array(
				'type' 		=> 'notes',
				'section'	=> 'fallsky_section_general_colors',
				'settings' 	=> 'fallsky_accent_color_notes',
				'description'	=> sprintf(
					esc_html__( 'To change site header/footer/homepage/sidebar\'s colors, please go to their sections and find "Design Options". For more details please check %sthis article%s.', 'fallsky' ), 
					sprintf('<a href="%s" target="_blank">', 'https://www.loftocean.com/fallsky/style/how-to-change-site-colors/'),
					'</a>'
				)
			) ) );
			$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_group_tweak_colors', array(
				'type' 		=> 'group',
				'label' 	=> esc_html__( 'Tweak Colors', 'fallsky' ),
				'section' 	=> 'fallsky_section_general_colors',
				'settings'	=> 'fallsky_group_tweak_colors',
				'children' 	=> array(
					new WP_Customize_Color_Control( $wp_customize, 'fallsky_light_color_scheme_custom_bg', array(
						'label'		=> esc_html__( 'Light Scheme Background Color', 'fallsky' ),
						'section'	=> 'fallsky_section_general_colors',
						'settings'	=> 'fallsky_light_color_scheme_custom_bg'
					) ),
					new WP_Customize_Color_Control( $wp_customize, 'fallsky_light_color_scheme_custom_text', array(
						'label'		=> esc_html__( 'Light Scheme Text Color', 'fallsky' ),
						'section'	=> 'fallsky_section_general_colors',
						'settings'	=> 'fallsky_light_color_scheme_custom_text'
					) ),
					new WP_Customize_Color_Control( $wp_customize, 'fallsky_light_color_scheme_custom_content', array(
						'label'			=> esc_html__( 'Light Scheme Content Color', 'fallsky' ),
						'section'		=> 'fallsky_section_general_colors',
						'settings'		=> 'fallsky_light_color_scheme_custom_content',
						'description' 	=> esc_html__( 'For post/page main content text, except headings', 'fallsky' )
					) ),
					new WP_Customize_Color_Control( $wp_customize, 'fallsky_dark_color_scheme_custom_bg', array(
						'label'    => esc_html__( 'Dark Scheme Background Color', 'fallsky' ),
						'section'  => 'fallsky_section_general_colors',
						'settings' => 'fallsky_dark_color_scheme_custom_bg'
					) ),
					new WP_Customize_Color_Control( $wp_customize, 'fallsky_dark_color_scheme_custom_text', array(
						'label'    => esc_html__( 'Dark Scheme Text Color', 'fallsky' ),
						'section'  => 'fallsky_section_general_colors',
						'settings' => 'fallsky_dark_color_scheme_custom_text'
					) ),
					new WP_Customize_Color_Control( $wp_customize, 'fallsky_dark_color_scheme_custom_content', array(
						'label'			=> esc_html__( 'Dark Scheme Content Color', 'fallsky' ),
						'section'		=> 'fallsky_section_general_colors',
						'settings'		=> 'fallsky_dark_color_scheme_custom_content',
						'description'	=> esc_html__( 'For post/page main content text, except headings', 'fallsky' )
					))
				)
			) ) );

			$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_mobile_image_slider_arrows_style', array(
				'type' 		=> 'select',
				'label'		=> esc_html__( 'Slider Arrows - Mobile', 'fallsky' ),
				'section'	=> 'fallsky_section_general_image_slider',
				'settings' 	=> 'fallsky_mobile_image_slider_arrows_style',
				'choices' 	=> array(
					'' 					=> esc_html__( 'Hide slider arrows on mobile devices', 'fallsky' ),
					'display-on-mobile' => esc_html__( 'Display slider arrows on mobile devices', 'fallsky' )
				)
			) ) );

			$this->instagram_settings( $wp_customize );
		}
		private function layout_option_settings( $wp_customize ) {
			global $fallsky_default_settings;
			$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_site_layout_boxed_width', array(
				'default'   		=> $fallsky_default_settings['fallsky_site_layout_boxed_width'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'absint',
				'dependency' 		=> array(
					'fallsky_site_layout' => array( 'value' => array( 'site-layout-boxed' ) )
				)
			) ) );

			$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_site_layout_boxed_width', array(
				'type' 				=> 'number_with_unit',
				'label' 			=> esc_html__( 'Site Max Width', 'fallsky' ),
				'after_text' 		=> 'px',
				'input_attrs' 		=> array( 'min' => 1 ),
				'section' 			=> 'fallsky_section_general_layouts',
				'settings' 			=> 'fallsky_site_layout_boxed_width',
				'priority' 			=> 3,
				'active_callback' 	=> 'fallsky_customize_control_active_cb'
			) ) );

			$bg_image = $wp_customize->get_control( 'background_image' );
			$bg_preset = $wp_customize->get_control( 'background_preset' );
			$bg_position = $wp_customize->get_control( 'background_position' );
			$bg_position_x = $wp_customize->get_control( 'background_position_x' );
			$bg_size = $wp_customize->get_control( 'background_size' );
			$bg_repeat = $wp_customize->get_control( 'background_repeat' );
			$bg_attachment = $wp_customize->get_control( 'background_attachment' );
			$bg_color = $wp_customize->get_control( 'background_color');

			if ( ! empty( $bg_image ) && ( $bg_image instanceof WP_Customize_Control ) ) {
				$bg_image->section 			= 'fallsky_section_general_layouts';
				$bg_image->priority 		= 5;
				$bg_image->active_callback 	= 'fallsky_customize_control_active_cb';

				$wp_customize->remove_setting( 'background_image' );
				$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'background_image', array(
					'default' 			=> get_theme_support( 'custom-background', 'default-image' ),
					'theme_supports'	=> 'custom-background',
					'transport' 		=> 'postMessage',
					'sanitize_callback' => array( $wp_customize, '_sanitize_background_setting' ),
					'dependency'		=> array(
						'fallsky_site_layout' 	=> array( 'value' => array( 'site-layout-boxed', 'site-layout-frame' ) )
					)
				) ) );
			}

			if ( ! empty( $bg_preset ) && ( $bg_preset instanceof WP_Customize_Control ) ) {
				$bg_preset->section 		= 'fallsky_section_general_layouts';
				$bg_preset->priority 		= 5;
				$bg_preset->active_callback = 'fallsky_customize_control_active_cb';

				$wp_customize->remove_setting( 'background_preset' );
				$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'background_preset', array(
					'default'        	=> get_theme_support( 'custom-background', 'default-preset' ),
					'theme_supports' 	=> 'custom-background',
					'transport' 		=> 'postMessage',
					'sanitize_callback' => array( $wp_customize, '_sanitize_background_setting' ),
					'dependency'		=> array(
						'fallsky_site_layout' 	=> array( 'value' => array( 'site-layout-boxed', 'site-layout-frame' ) ),
						'background_image' 		=> array( 'value' => array( '' ), 'operator' => 'not in' )
					)
				) ) );
			}

			if ( ! empty( $bg_position ) && ( $bg_position instanceof WP_Customize_Control ) ) {
				$bg_position->section 			= 'fallsky_section_general_layouts';
				$bg_position->priority 			= 5;
				$bg_position->active_callback 	= 'fallsky_customize_control_active_cb';

				$wp_customize->remove_setting( 'background_position_x' );
				$wp_customize->remove_setting( 'background_position_y' );
				$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'background_position_x', array(
					'default'			=> get_theme_support( 'custom-background', 'default-position-x' ),
					'theme_supports'	=> 'custom-background',
					'transport' 		=> 'postMessage',
					'sanitize_callback' => array( $wp_customize, '_sanitize_background_setting' ),
					'dependency'		=> array(
						'fallsky_site_layout' 	=> array( 'value' => array( 'site-layout-boxed', 'site-layout-frame' ) ),
						'background_image' 		=> array( 'value' => array( '' ), 'operator' => 'not in' ),
						'background_preset' 	=> array( 'value' => array( 'default' ), 'operator' => 'not in' )
					)
				) ) );

				$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'background_position_y', array(
					'default'        	=> get_theme_support( 'custom-background', 'default-position-y' ),
					'theme_supports' 	=> 'custom-background',
					'transport' 		=> 'postMessage',
					'sanitize_callback' => array( $wp_customize, '_sanitize_background_setting' ),
					'dependency'		=> array(
						'fallsky_site_layout' 	=> array( 'value' => array( 'site-layout-boxed', 'site-layout-frame' ) ),
						'background_image'		=> array( 'value' => array( '' ), 'operator' => 'not in' ),
						'background_preset' 	=> array( 'value' => array( 'default' ), 'operator' => 'not in' )
					)
				) ) );
			}

			if ( ! empty( $bg_position_x ) && ( $bg_position_x instanceof WP_Customize_Control ) ) {
				$bg_position_x->section 		= 'fallsky_section_general_layouts';
				$bg_position_x->priority 		= 5;
				$bg_position_x->active_callback = 'fallsky_customize_control_active_cb';

				$wp_customize->remove_setting( 'background_position_x' );
				$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'background_position_x', array(
					'default'			=> get_theme_support( 'custom-background', 'default-position-x' ),
					'theme_supports'	=> 'custom-background',
					'transport' 		=> 'postMessage',
					'sanitize_callback' => array( $wp_customize, '_sanitize_background_setting' ),
					'dependency'		=> array(
						'fallsky_site_layout' 	=> array( 'value' => array( 'site-layout-boxed', 'site-layout-frame' ) ),
						'background_image' 		=> array( 'value' => array( '' ), 'operator' => 'not in' ),
						'background_preset' 	=> array( 'value' => array( 'default' ), 'operator' => 'not in' )
					)
				) ) );
			}

			if ( ! empty( $bg_size ) && ( $bg_size instanceof WP_Customize_Control ) ) {
				$bg_size->section 			= 'fallsky_section_general_layouts';
				$bg_size->priority 			= 5;
				$bg_size->active_callback 	= 'fallsky_customize_control_active_cb';

				$wp_customize->remove_setting( 'background_size' );
				$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'background_size', array(
					'default'        	=> get_theme_support( 'custom-background', 'default-size' ),
					'theme_supports'	=> 'custom-background',
					'transport' 		=> 'postMessage',
					'sanitize_callback' => array( $wp_customize, '_sanitize_background_setting' ),
					'dependency'		=> array(
						'fallsky_site_layout' 	=> array( 'value' => array( 'site-layout-boxed', 'site-layout-frame' ) ),
						'background_image' 		=> array( 'value' => array( '' ), 'operator' => 'not in' ),
						'background_preset' 	=> array( 'value' => array( 'custom' ) )
					)
				) ) );
			}

			if ( ! empty( $bg_repeat ) && ( $bg_repeat instanceof WP_Customize_Control ) ) {
				$bg_repeat->section 		= 'fallsky_section_general_layouts';
				$bg_repeat->priority 		= 5;
				$bg_repeat->active_callback = 'fallsky_customize_control_active_cb';

				$wp_customize->remove_setting( 'background_repeat' );
				$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'background_repeat', array(
					'default'           => get_theme_support( 'custom-background', 'default-repeat' ),
					'sanitize_callback' => array( $wp_customize, '_sanitize_background_setting' ),
					'transport' 		=> 'postMessage',
					'theme_supports'    => 'custom-background',
					'dependency'		=> array(
						'fallsky_site_layout' 	=> array( 'value' => array( 'site-layout-boxed', 'site-layout-frame' ) ),
						'background_image' 		=> array( 'value' => array( '' ), 'operator' => 'not in' ),
						'background_preset' 	=> array( 'value' => array( 'custom', 'fit' ) )
					)
				) ) );
			}

			if ( ! empty( $bg_attachment ) && ( $bg_attachment instanceof WP_Customize_Control ) ) {
				$bg_attachment->section 		= 'fallsky_section_general_layouts';
				$bg_attachment->priority 		= 5;
				$bg_attachment->active_callback = 'fallsky_customize_control_active_cb';

				$wp_customize->remove_setting( 'background_attachment' );
				$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'background_attachment', array(
					'default'           => get_theme_support( 'custom-background', 'default-attachment' ),
					'sanitize_callback' => array( $wp_customize, '_sanitize_background_setting' ),
					'theme_supports'    => 'custom-background',
					'transport' 		=> 'postMessage',
					'dependency'		=> array(
						'fallsky_site_layout' 	=> array( 'value' => array( 'site-layout-boxed', 'site-layout-frame' ) ),
						'background_image' 		=> array( 'value' => array( '' ), 'operator' => 'not in' ),
						'background_preset'		=> array( 'value' => array( 'custom', 'repeat' ) )
					)
				) ) );
			}

			if ( ! empty( $bg_color ) && ( $bg_color instanceof WP_Customize_Control ) ) {
				$bg_color->section 			= 'fallsky_section_general_layouts';
				$bg_color->priority 		= 4;
				$bg_color->active_callback 	= 'fallsky_customize_control_active_cb';

				$wp_customize->remove_setting( 'background_color' );
				$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'background_color', array(
					'default'        		=> get_theme_support( 'custom-background', 'default-color' ),
					'theme_supports' 		=> 'custom-background',
					'sanitize_callback'   	=> 'sanitize_hex_color_no_hash',
					'sanitize_js_callback'	=> 'maybe_hash_hex_color',
					'transport' 			=> 'postMessage',
					'dependency'			=> array(
						'fallsky_site_layout' 	=> array( 'value' => array( 'site-layout-boxed', 'site-layout-frame' ) )
					)
				) ) );
			}
			$wp_customize->remove_section( 'colors' );
			$wp_customize->remove_section( 'background_image' );
		}
		private function instagram_settings( $wp_customize ) {
			if ( class_exists( 'Fallsky_Extension' ) ) {
				global $fallsky_default_settings;

				$wp_customize->add_section( 'fallsky_section_general_instagram', array(
					'title'	=> esc_html__( 'Instagram', 'fallsky' ),
					'panel' => 'fallsky_panel_general'
				) );

				$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_instagram_clear_cache', array(
					'default'			=> esc_html__( 'Clear Cache', 'fallsky' ),
					'transport'			=> 'postMessage',
					'sanitize_callback' => 'fallsky_sanitize_empty'
				) ) );
				$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_instagram_render_type', array(
					'default'			=> $fallsky_default_settings['fallsky_instagram_render_type'],
					'transport'			=> 'postMessage',
					'sanitize_callback' => 'fallsky_sanitize_checkbox'
				) ) );

				$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_instagram_clear_cache', array(
					'type' 			=> 'button',
					'label'			=> esc_html__( 'Clear Instagram Cache', 'fallsky' ),
					'description'	=> esc_html__( 'By default, the Instagram cache will exist for up to 2 hours. To manually clear the cache, please click the button below.', 'fallsky' ),
					'section'		=> 'fallsky_section_general_instagram',
					'settings' 		=> 'fallsky_instagram_clear_cache'
				) ) );
				$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_instagram_render_type', array(
					'type' 			=> 'checkbox',
					'label_first'	=> true,
					'label'			=> esc_html__( 'Load Instagram pictures dynamically with AJAX', 'fallsky' ),
					'description' 	=> esc_html__( 'Recommend enabling this option if any caching plugins are used on your site.', 'fallsky' ),
					'section'		=> 'fallsky_section_general_instagram',
					'settings' 		=> 'fallsky_instagram_render_type',
				) ) );
			}
		}
		public function customize_js_vars( $vars = array() ) {
			return array_merge( $vars, array( 
				'site-layout-boxed' => array(
					'fallsky_site_layout_options_group'	=> esc_html__( 'Boxed Layout Options', 'fallsky' ),
					'background_image' 					=> esc_html__( 'Outer Area Background Image', 'fallsky' ),
					'background_position' 				=> esc_html__( 'Outer Area Background Image Position', 'fallsky' ),
					'background_position_x'				=> esc_html__( 'Outer Area Background Image Position', 'fallsky' ),
					'background_size'					=> esc_html__( 'Outer Area Background Image Size', 'fallsky' ),
					'background_color'					=> esc_html__( 'Outer Area Background Color', 'fallsky' )
				),
				'site-layout-frame' => array(
					'fallsky_site_layout_options_group' => esc_html__( 'Frame Layout Options', 'fallsky' ),
					'background_image' 					=> esc_html__( 'Frame Background Image', 'fallsky' ),
					'background_position'				=> esc_html__( 'Frame Background Image Position', 'fallsky' ),
					'background_position_x'				=> esc_html__( 'Frame Background Image Position', 'fallsky' ),
					'background_size' 					=> esc_html__( 'Frame Background Image Size', 'fallsky' ),
					'background_color'					=> esc_html__( 'Frame Background Color', 'fallsky' )
				),
				'clear-instagram-cache'	=> array(
					'sending' 	=> esc_attr__( 'Request Sending ...', 'fallsky' ),
					'done'		=> esc_attr__( 'Clear Cache', 'fallsky' )
				)
			) );
		}
		public function frontend_js_vars( $vars = array() ) {
			$vars['mobile_slider_arrow_style'] = fallsky_get_theme_mod( 'fallsky_mobile_image_slider_arrows_style' );
			return $vars;
		}
		protected function get_custom_styles() {
			global $fallsky_default_settings, $fallsky_is_preview;

			$styles = array();

			// Bckground Image for #page
			$page_bg_image_id = absint( fallsky_get_theme_mod( 'fallsky_page_background_image' ) );
			if ( ! empty( $page_bg_image_id ) || $fallsky_is_preview ) {
				$image = empty( $page_bg_image_id ) ? false : fallsky_get_image_src( $page_bg_image_id, 'full', false );
				if ( $image || $fallsky_is_preview ) {
					$position_x 	= esc_attr( fallsky_get_theme_mod( 'fallsky_page_background_position_x' ) );
					$position_y 	= esc_attr( fallsky_get_theme_mod( 'fallsky_page_background_position_y' ) );
					$size 			= esc_attr( fallsky_get_theme_mod( 'fallsky_page_background_size' ) );
					$repeat 		= fallsky_module_enabled( 'fallsky_page_background_repeat' );
					$attachment 	= fallsky_module_enabled( 'fallsky_page_background_attachment' );

					if ( ! empty( $image ) ) {
						$styles['page-bg-image'] = sprintf(
							'#page { %s }',
							sprintf( 'background-image: url(%s);', esc_url_raw( $image ) )
						);
					}

					$styles['page-bg-image-size'] = sprintf(
						'#page { %s }',
						sprintf( 'background-size: %s;', $size )
					);
					$styles['page-bg-image-repeat'] = sprintf(
						'#page { %s }',
						sprintf( 'background-repeat: %s;', ( $repeat ? 'repeat' : 'no-repeat' ) )
					);
					$styles['page-bg-image-attachment'] = sprintf(
						'#page { %s }',
						sprintf( 'background-attachment: %s;', ( $attachment ? 'scroll' : 'fixed' ) )
					);
					$styles['page-bg-image-position-x'] = sprintf(
						'#page { %s }',
						sprintf( 'background-position-x: %s;', $position_x )
					);
					$styles['page-bg-image-position-y'] = sprintf(
						'#page { %s }',
						sprintf( 'background-position-y: %s;', $position_y )
					);
				}
			}

			// Site width for site layout 'site-layout-boxed'
			if ( ( 'site-layout-boxed' == esc_attr(fallsky_get_theme_mod( 'fallsky_site_layout' ) ) ) || $fallsky_is_preview ) {
				$site_width = absint( fallsky_get_theme_mod( 'fallsky_site_layout_boxed_width' ) );
				$styles['site-width'] = sprintf(
					'%s { %s }', 
					fallsky_get_selector( array(
						'body.site-layout-boxed #page',
						'body.site-layout-boxed .post-nav', 
						'body.site-layout-boxed .site-header', 
						'body.site-layout-boxed .site-header.site-header-layout-6 .site-header-menu', 
						'body.site-layout-boxed .site-header .site-header-menu .main-navigation li.mega-menu > ul.sub-menu'
					) ),
					sprintf( 'max-width: %spx;', $site_width )
				);
			}

			return $styles;
		}
		public function get_css_variables( $vars ) {
			global $fallsky_default_settings;
			$css_vars = array(
				'--primary-color' 			=> 'fallsky_accent_custom_color',
				'--light-bg-color' 			=> 'fallsky_light_color_scheme_custom_bg',
				'--light-text-color' 		=> 'fallsky_light_color_scheme_custom_text',
				'--light-content-color' 	=> 'fallsky_light_color_scheme_custom_content',
				'--dark-bg-color' 			=> 'fallsky_dark_color_scheme_custom_bg',
				'--dark-text-color'			=> 'fallsky_dark_color_scheme_custom_text',
				'--dark-content-color' 		=> 'fallsky_dark_color_scheme_custom_content',
				'--boxed-inner-width-half'	=> 'fallsky_site_layout_boxed_width'
			);
			foreach ( $css_vars as $var => $id ) {
				$custom_value = fallsky_get_theme_mod( $id );
				if ( strtolower( $custom_value ) != strtolower( $fallsky_default_settings[ $id ] ) ) {
					$vars[$var] = ( 'fallsky_site_layout_boxed_width' == $id ) ? sprintf( '%spx', ( $custom_value / 2 ) ) : $custom_value;
				}
			}
			return $vars;
		}
		public function get_fallback_css( $styles ) {
			global $fallsky_default_settings;
			$colors = array();
			// Primary color
			$primary_color 			= fallsky_get_theme_mod( 'fallsky_accent_custom_color' );
			$default_primary_color 	= $fallsky_default_settings['fallsky_accent_custom_color'];
			if ( ( 'custom' == fallsky_get_theme_mod( 'fallsky_accent_color' ) ) && ( strtolower( $primary_color ) != strtolower( $default_primary_color ) ) ) {
				$primary_rgba = $this->hex2rgba( $primary_color, '0.3' );
				$colors['primary_color'] = sprintf(
					'%s { %s }',
					fallsky_get_selector(array(
						'.primary-color-enabled #page .button.lo-button',
						'.primary-color-enabled a',
						'.primary-color-enabled blockquote:before',
						'.no-touch .primary-color-enabled .author-social ul.social-nav li a:hover',
						'.primary-color-enabled .comments ol.comment-list li .comment-metadata',
						'.no-touch .primary-color-enabled .comments ol.comment-list li a.comment-reply-link:hover',
						'.no-touch .primary-color-enabled .widget a:not(.button):hover',
						'.no-touch .primary-color-enabled .posts:not(.layout-overlay) .post .post-title a:hover',
						'.primary-color-enabled .widget-area .widget .textwidget a:not(.button)',
						'.primary-color-enabled .price ins',
						'.primary-color-enabled .woocommerce-error:before',
						'.primary-color-enabled .woocommerce-info:before',
						'.primary-color-enabled .woocommerce-message:before',
						'.primary-color-enabled.woocommerce #reviews #comments ol.commentlist li .comment-text p.meta time'
					) ),
					sprintf( 'color: %s;', $primary_color )
				);
				$colors['primary_background_color'] = sprintf(
					'%s { %s }',
					fallsky_get_selector( array(
						'.primary-color-enabled .site-header nav ul.primary-menu > li.button > a',
						'.primary-color-enabled #page .button',
						'.primary-color-enabled #page input[type="submit"]',
						'.primary-color-enabled mark',
						'.primary-color-enabled .cat-links a:before',
						'.primary-color-enabled .pagination .page-numbers.current:after',
						'.no-touch .primary-color-enabled .widget.widget_calendar table#wp-calendar tbody a:hover',
						'.primary-color-enabled #page .post-entry .highlight:not(.bottomline)',
						'.no-touch .woocommerce-page.woocommerce-cart.primary-color-enabled #page .site-content .woocommerce a.button.checkout-button:hover',
						'.no-touch .woocommerce.primary-color-enabled #payment #place_order:hover',
						'.no-touch .woocommerce-page.primary-color-enabled #payment #place_order:hover',
						'body.primary-color-enabled #page #respond input#submit',
						'body.primary-color-enabled #page #respond input#submit.alt',
						'.primary-color-enabled .woocommerce.widget.widget_price_filter .price_slider_wrapper .ui-widget-content:before',
						'.primary-color-enabled .woocommerce.widget.widget_price_filter .ui-slider .ui-slider-range',
						'.primary-color-enabled .woocommerce.widget.widget_price_filter .ui-slider .ui-slider-handle',
						'.primary-color-enabled .post-entry ul li:before', 
						'.primary-color-enabled .post-entry ol li:before', 
						'.primary-color-enabled .comment-content ul li:before', 
						'.primary-color-enabled .comment-content ol li:before', 
						'.primary-color-enabled .custom-content ul li:before', 
						'.primary-color-enabled .custom-content ol li:before'
					) ),
					sprintf( 'background: %s;', $primary_color )
				);

				$colors['primary_slider3_border_bottom_color'] = sprintf(
					'%s { %s }',
					'.primary-color-enabled .featured-section .top-slider.style-slider-3 .slick-dots li.slick-active:before', 
					sprintf( 'border-bottom-color: %s;', $primary_color )
				);

				$colors['primary_woocommerce_border_color'] = sprintf(
					'%s { %s }',
					fallsky_get_selector( array(
						'.primary-color-enabled .woocommerce-error',
						'.primary-color-enabled .woocommerce-info',
						'.primary-color-enabled .woocommerce-message'
					) ),
					sprintf( 'border-color: %s;', $primary_color )
				);

				$colors['primary_site_header_color'] = sprintf(
					'@media screen and (min-width: 1120px) { %s { %s } }',
					fallsky_get_selector( array(
						'.primary-color-enabled .site-header .site-header-menu .main-navigation ul.sub-menu li.current-menu-item > a',
						'.primary-color-enabled .site-header .site-header-menu .main-navigation ul.sub-menu li.current-menu-ancestor > a'
					) ),
					sprintf( 'color: %s;', $primary_color )
				);

				$colors['primary_tag_cloud_comment_color'] = sprintf(
					' %s { %s } ',
					fallsky_get_selector( array(
						'.no-touch .single.primary-color-enabled #primary > article .post-tag-cloud .tagcloud a:hover', 
						'.single.primary-color-enabled #primary > article .post-tag-cloud .tagcloud a:focus',
						'.no-touch .primary-color-enabled .comments .click-to-reply:hover span'
					) ),
					sprintf( 'border-bottom-color: %s;', $primary_color )
				);
			}

			// Light color scheme
			$light_bg_color 	= fallsky_get_theme_mod( 'fallsky_light_color_scheme_custom_bg' );
			$light_text_color 	= fallsky_get_theme_mod( 'fallsky_light_color_scheme_custom_text' );
			if ( strtolower( $light_bg_color ) == strtolower( $fallsky_default_settings['fallsky_light_color_scheme_custom_bg'] ) ) {
				$light_bg_color = false;
			}
			if ( strtolower( $light_text_color ) == strtolower( $fallsky_default_settings['fallsky_light_color_scheme_custom_text'] ) ) {
				$light_text_color = false;
			}

			if ( $light_text_color ) {
				$colors['light_text_color'] = sprintf(
					' %s { %s } ',
					fallsky_get_selector( array(
						'.light-color',
						'.home-widget.light-color',
						'.post-entry h1',
						'.post-entry h2',
						'.post-entry h3',
						'.post-entry h4',
						'.post-entry h5',
						'.post-entry h6',
						'.post-entry form',
						'.post-entry blockquote',
						'.post-entry .authors-list .author-social ul.social-nav li a',
						'.woocommerce-page #primary .woocommerce-product-details__short-description h1',
						'.woocommerce-page #primary .woocommerce-product-details__short-description h2',
						'.woocommerce-page #primary .woocommerce-product-details__short-description h3',
						'.woocommerce-page #primary .woocommerce-product-details__short-description h4',
						'.woocommerce-page #primary .woocommerce-product-details__short-description h5',
						'.woocommerce-page #primary .woocommerce-product-details__short-description h6',
						'.woocommerce-page #primary .woocommerce-product-details__short-description form',
						'.woocommerce-page #primary .entry-content h1',
						'.woocommerce-page #primary .entry-content h2',
						'.woocommerce-page #primary .entry-content h3',
						'.woocommerce-page #primary .entry-content h4',
						'.woocommerce-page #primary .entry-content h5',
						'.woocommerce-page #primary .entry-content h6',
						'.woocommerce-page #primary .entry-content form'
					) ),
					sprintf( 'color: %s;', $light_text_color )
				);

				$colors['light_twitter_color_after'] = sprintf(
					' %s { %s } ',
					fallsky_get_selector( array(
						'.post-entry a.tweet-it:after'
					) ),
					sprintf( ' border-top-color: %s;', $light_text_color )
				);
			}
			if ( $light_bg_color ) {
				$colors['light_bg_color'] = sprintf(
					' %s { %s } ',
					fallsky_get_selector( array(
						'.light-color',
						'.light-color #page',
						'.home-widget.light-color',
						'.single.post-template-2.light-color .featured-media-section + .main #primary > article',
						'article .post-entry .gallery-slider.fullscreen',
						'.loftocean-popup-sliders .popup-slider.gallery-slider.fullscreen'
					) ),
					sprintf( 'background-color: %s;', $light_bg_color )
				);
			}
			if ( $light_text_color || $light_bg_color ) {
				$colors['light_twitter_color_before'] = sprintf(
					' %s {%s%s } ',
					fallsky_get_selector( array(
						'.post-entry a.tweet-it:before'
					) ),
					empty( $light_text_color ) ? '' : sprintf( ' background: %s;', $light_text_color ),
					empty( $light_bg_color ) ? '' : sprintf( ' color: %s;', $light_bg_color )
				);
			}

			$colors['light_content_color'] = fallsky_get_style(
				'fallsky_light_color_scheme_custom_content',
				fallsky_get_selector( array(
					'.post-entry',
					'.error404 .page-404-container p',
					'.comments #respond p.comment-notes',
					'.comments ol.comment-list li .comment-content p',
					'.single #primary .author-bio .author-bio-text',
					'.woocommerce #reviews #comments ol.commentlist li .comment-text .description p',
					'.woocommerce-page #primary .woocommerce-product-details__short-description',
					'.woocommerce-page #primary .entry-content'
				) ),
				'color: %s;'
			);

			// Dark color scheme
			$dark_bg_color 		= fallsky_get_theme_mod( 'fallsky_dark_color_scheme_custom_bg' );
			$dark_text_color 	= fallsky_get_theme_mod( 'fallsky_dark_color_scheme_custom_text' );
			if ( strtolower( $dark_bg_color ) == strtolower( $fallsky_default_settings['fallsky_dark_color_scheme_custom_bg'] ) ) { 
				$dark_bg_color = false;
			}
			if ( strtolower( $dark_text_color ) == strtolower( $fallsky_default_settings['fallsky_dark_color_scheme_custom_text'] ) ) {
				$dark_text_color = false;
			}

			if ( $dark_text_color ) {
				$colors['dark_text_color'] = sprintf(
					' %s { %s } ',
					fallsky_get_selector( array(
						'.dark-color',
						'.home-widget.dark-color',
						'.dark-color .post-entry h1',
						'.dark-color .post-entry h2',
						'.dark-color .post-entry h3',
						'.dark-color .post-entry h4',
						'.dark-color .post-entry h5',
						'.dark-color .post-entry h6',
						'.dark-color .post-entry form',
						'.dark-color .post-entry blockquote',
						'.dark-color .post-entry .authors-list .author-social ul.social-nav li a',
						'.woocommerce-page.dark-color #primary .woocommerce-product-details__short-description h1',
						'.woocommerce-page.dark-color #primary .woocommerce-product-details__short-description h2',
						'.woocommerce-page.dark-color #primary .woocommerce-product-details__short-description h3',
						'.woocommerce-page.dark-color #primary .woocommerce-product-details__short-description h4',
						'.woocommerce-page.dark-color #primary .woocommerce-product-details__short-description h5',
						'.woocommerce-page.dark-color #primary .woocommerce-product-details__short-description h6',
						'.woocommerce-page.dark-color #primary .woocommerce-product-details__short-description form',
						'.woocommerce-page.dark-color #primary .entry-content h1',
						'.woocommerce-page.dark-color #primary .entry-content h2',
						'.woocommerce-page.dark-color #primary .entry-content h3',
						'.woocommerce-page.dark-color #primary .entry-content h4',
						'.woocommerce-page.dark-color #primary .entry-content h5',
						'.woocommerce-page.dark-color #primary .entry-content h6',
						'.woocommerce-page.dark-color #primary .entry-content form'
					) ),
					sprintf( 'color: %s;', $dark_text_color )
				);

				$colors['dark_twitter_color_after'] = sprintf(
					' %s { %s } ',
					fallsky_get_selector( array(
						'.dark-color .post-entry a.tweet-it:after'
					) ),
					sprintf( ' border-top-color: %s;', $light_text_color )
				);
			}
			if ( $dark_bg_color ) {
				$colors['dark_bg_color'] = sprintf(
					' %s { %s } ',
					fallsky_get_selector( array(
						'.dark-color',
						'.dark-color #page',
						'.home-widget.dark-color',
						'.single.post-template-2.dark-color .featured-media-section + .main #primary > article',
						'.dark-color article .post-entry .gallery-slider.fullscreen',
						'.dark-color .loftocean-popup-sliders .popup-slider.gallery-slider.fullscreen'
					) ),
					sprintf( 'background-color: %s;', $dark_bg_color )
				);
			}
			if ( $dark_text_color || $dark_bg_color ) {
				$colors['dark_button_color'] = sprintf(
					' %s {%s%s } ',
					fallsky_get_selector( array(
						'.dark-color .post-entry a.tweet-it:before'
					) ),
					empty( $dark_text_color ) ? '' : sprintf( ' background: %s;', $dark_text_color ),
					empty( $dark_bg_color ) ? '' : sprintf( ' color: %s;', $dark_bg_color )
				);
			}

			$colors['dark_content_color'] = fallsky_get_style(
				'fallsky_dark_color_scheme_custom_content',
				fallsky_get_selector( array(
					'.dark-color .post-entry',
					'.error404.dark-color .page-404-container p',
					'.dark-color .comments #respond p.comment-notes',
					'.dark-color .comments ol.comment-list li .comment-content p',
					'single.dark-color #primary .author-bio .author-bio-text',
					'.woocommerce.dark-color .woocommerce #reviews #comments ol.commentlist li .comment-text .description p',
					'.woocommerce-page.dark-color #primary .woocommerce-product-details__short-description',
					'.woocommerce-page.dark-color #primary .entry-content'
				) ),
				'color: %s;'
			);
			return sprintf( '%s %s ', $styles, implode( ' ', $colors ) );
		}
		public function render_instagram_by( $by ) { 
			return fallsky_module_enabled( 'fallsky_instagram_render_type' ) ? 'ajax' : '';
		}
		public function frontend_actions() {
			add_action( 'body_class', array( $this, 'body_class' ) );
		}
		public function body_class( $class ) {
			$primary_color_enabled = fallsky_get_theme_mod( 'fallsky_accent_color' ) == 'custom';
			// If enabled primary color. If so add the primary-color-enabled class
			$primary_color_enabled ? array_push( $class, 'primary-color-enabled' ) : '';
			// Add site color class
			array_push( $class, esc_attr( fallsky_get_theme_mod( 'fallsky_site_color_scheme' ) ) );

			// Add site layout class
			$site_layout = esc_attr( fallsky_get_theme_mod( 'fallsky_site_layout' ) );
			array_push( $class, $site_layout );
			if ( $site_layout == 'site-layout-fullwidth' ) {
				$class = array_diff($class, array('custom-background'));
			}

			$container_width = fallsky_get_theme_mod( 'fallsky_site_container_width' );
			if ( ! empty( $container_width ) ) {
				array_push( $class, $container_width );
			}

			return $class;
		}
		private function hex2rgba( $hex, $opacity ) {
			if ( preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', $hex ) ) {
				$hex2dec = array(
					'1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5, '6' => 6, '7' => 7, '8' => 8,
					'9' => 9, '0' => 0, 'a' => 10, 'b' => 11, 'c' => 12, 'd' => 13, 'e' => 14, 'f' => 15
				);
				$hex = strtolower( substr( $hex, 1 ) );
				if ( strlen( $hex ) == 3 ) {
					$r = ( $hex2dec[ $hex[0] ] * 16 + $hex2dec[ $hex[0] ] );
					$g = ( $hex2dec[ $hex[1] ] * 16 + $hex2dec[ $hex[1] ] );
					$b = ( $hex2dec[ $hex[2] ] * 16 + $hex2dec[ $hex[2] ] );
				} else {
					$r = ( $hex2dec[ $hex[0] ] * 16 + $hex2dec[ $hex[1] ] );
					$g = ( $hex2dec[ $hex[2] ] * 16 + $hex2dec[ $hex[3] ] );
					$b = ( $hex2dec[ $hex[4] ] * 16 + $hex2dec[ $hex[5] ] );
				}
				return sprintf( 'rgba(%s, %s, %s, %s)', $r, $g, $b, $opacity );
			}
			return false;
		}
	}
	function fallsky_customize_general_set_autofocus() {
		global $wp_customize;
		if ( ( 'WP_Customize_Manager' == get_class( $wp_customize ) ) && isset( $_REQUEST['autofocus'] ) ) {
			$autofocus = $_REQUEST['autofocus'];
			if ( ! empty( $autofocus['control'] ) ) { 
				$control = $autofocus['control'];
				$autofocus['control'] = ( $control == 'background_image' ) ? 'fallsky_page_group_background' : $control;
			}
			$wp_customize->set_autofocus( wp_unslash( $autofocus ) );
		}
	}
	add_action( 'customize_controls_print_footer_scripts', 'fallsky_customize_general_set_autofocus' );
	new Fallsky_Customize_General();
}