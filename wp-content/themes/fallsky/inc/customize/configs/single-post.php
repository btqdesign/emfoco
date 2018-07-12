<?php
/**
* Customize section single post configuration files.
*/

if ( ! class_exists( 'Fallsky_Customize_Posts' ) ) {
	class Fallsky_Customize_Posts extends Fallsky_Customize_Base {
		public function register_controls( $wp_customize ) {
			global $fallsky_default_settings;

			$has_mc4wp = function_exists( 'mc4wp' );

			$wp_customize->add_section( 'fallsky_section_single_post', array(
				'title'    => esc_html__( 'Single Post', 'fallsky' ),
				'priority' => 45
			) );

			// Settings
			$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_single_post_default_template', array(
				'default'   		=> $fallsky_default_settings['fallsky_single_post_default_template'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			) )) ;
			$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_single_post_default_sidebar', array(
				'default'   		=> $fallsky_default_settings['fallsky_single_post_default_sidebar'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			) ) );
			$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_sticky_post_nav_group', array(
				'default'   		=> '',
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_empty'
			) ) );
			$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_sticky_post_nav', array(
				'default'  		 	=> $fallsky_default_settings['fallsky_sticky_post_nav'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_checkbox'
			) ) );
			$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_post_nav_color_scheme', array(
				'default'   		=> $fallsky_default_settings['fallsky_post_nav_color_scheme'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_choice',
				'dependency'		=> array(
					'fallsky_sticky_post_nav' => array('value' => array('on'))
				)
			) ) );
			$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_post_nav_bg_color', array(
				'default'   		=> $fallsky_default_settings['fallsky_post_nav_bg_color'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'sanitize_hex_color',
				'dependency'		=> array(
					'fallsky_sticky_post_nav' => array('value' => array('on'))
				)
			) ) );
			$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_single_post_show_category', array(
				'default' 			=> $fallsky_default_settings['fallsky_single_post_show_category'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_checkbox'
			) ) );
			$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_single_post_show_tags', array(
				'default'   		=> $fallsky_default_settings['fallsky_single_post_show_tags'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_checkbox'
			) ) );
			if ( class_exists( 'Fallsky_Extension' ) ) {
				$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_single_post_sharing_group', array(
					'default'   		=> '',
					'transport'			=> 'postMessage',
					'sanitize_callback' => 'fallsky_sanitize_empty'
				) ) );
				$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_single_post_show_sharing_buttons', array(
					'default'   		=> $fallsky_default_settings['fallsky_single_post_show_sharing_buttons'],
					'transport' 		=> 'refresh',
					'sanitize_callback' => 'fallsky_sanitize_checkbox'
				) ) );
				$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_single_post_show_sharing_buttons_on_mobile', array(
					'default'   		=> $fallsky_default_settings['fallsky_single_post_show_sharing_buttons_on_mobile'],
					'transport' 		=> 'postMessage',
					'sanitize_callback' => 'fallsky_sanitize_checkbox',
					'dependency'		=> array(
						'fallsky_single_post_show_sharing_buttons' => array( 'value' => array( 'on' ) )
					)
				) ) );
				$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_single_post_facebook_sharing', array(
					'default'   		=> $fallsky_default_settings['fallsky_single_post_facebook_sharing'],
					'transport' 		=> 'refresh',
					'sanitize_callback' => 'fallsky_sanitize_checkbox',
					'dependency'		=> array(
						'fallsky_single_post_show_sharing_buttons' => array( 'value' => array( 'on' ) )
					)
				) ) );
				$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_single_post_twitter_sharing', array(
					'default'   		=> $fallsky_default_settings['fallsky_single_post_twitter_sharing'],
					'transport' 		=> 'refresh',
					'sanitize_callback' => 'fallsky_sanitize_checkbox',
					'dependency'		=> array(
						'fallsky_single_post_show_sharing_buttons' => array( 'value' => array( 'on' ) )
					)
				) ) );
				$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_single_post_pinterest_sharing', array(
					'default'   		=> $fallsky_default_settings['fallsky_single_post_pinterest_sharing'],
					'transport' 		=> 'refresh',
					'sanitize_callback' => 'fallsky_sanitize_checkbox',
					'dependency'		=> array(
						'fallsky_single_post_show_sharing_buttons' => array( 'value' => array( 'on' ) )
					)
				) ) );
				$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_single_post_google_plus_sharing', array(
					'default'   		=> $fallsky_default_settings['fallsky_single_post_google_plus_sharing'],
					'transport' 		=> 'refresh',
					'sanitize_callback' => 'fallsky_sanitize_checkbox',
					'dependency'		=> array(
						'fallsky_single_post_show_sharing_buttons' => array( 'value' => array( 'on' ) )
					)
				) ) );
			}
			$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_single_post_footer_meta_group', array(
				'default'   		=> '',
				'transport'			=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_empty'
			) ) );
			$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_single_post_show_author', array(
				'default'  			=> $fallsky_default_settings['fallsky_single_post_show_author'],
				'transport'			=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_checkbox'
			) ) );
			$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_single_post_show_date', array(
				'default'   		=> $fallsky_default_settings['fallsky_single_post_show_date'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_checkbox'
			) ) );
			$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_single_post_show_view', array(
				'default'   		=> $fallsky_default_settings['fallsky_single_post_show_view'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_checkbox'
			) ) );
			$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_single_post_show_like', array(
				'default'   		=> $fallsky_default_settings['fallsky_single_post_show_like'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_checkbox'
			) ) );
			$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_single_post_show_comment_count', array(
				'default'   		=> $fallsky_default_settings['fallsky_single_post_show_comment_count'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_checkbox'
			) ) );		
			$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_single_post_show_author_info_box', array(
				'default' 			=> $fallsky_default_settings['fallsky_single_post_show_author_info_box'],
				'transport'			=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_checkbox'
			) ) );
			$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_single_post_show_signup_form', array(
				'default' 			=> $fallsky_default_settings['fallsky_single_post_show_signup_form'],
				'transport'			=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_checkbox'
			) ) );
			$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_single_post_signup_form_id', array(
				'default'			=> $fallsky_default_settings['fallsky_single_post_signup_form_id'],
				'transport'			=> 'refresh',
				'sanitize_callback'	=> 'fallsky_sanitize_choice',
				'dependency'		=> array(
					'fallsky_single_post_show_signup_form' => array( 'value' => array( 'on' ) )
				)
			) ) );
			$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_single_post_show_pagination', array(
				'default'   		=> $fallsky_default_settings['fallsky_single_post_show_pagination'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_checkbox'
			) ) );
			if ( class_exists( 'Fallsky_Extension' ) ) {
				$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_single_related_group', array(
					'default'  		 	=> '',
					'transport' 		=> 'postMessage',
					'sanitize_callback' => 'fallsky_sanitize_empty'
				) ) );
				$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_single_post_show_related_posts', array(
					'default'   		=> $fallsky_default_settings['fallsky_single_post_show_related_posts'],
					'transport' 		=> 'refresh',
					'sanitize_callback' => 'fallsky_sanitize_checkbox'
				) ) );
				$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_single_post_related_posts_title', array(
					'default'  			=> $fallsky_default_settings['fallsky_single_post_related_posts_title'],
					'transport' 		=> 'postMessage',
					'sanitize_callback' => 'sanitize_text_field',
					'dependency'		=> array(
						'fallsky_single_post_show_related_posts' => array( 'value' => array( 'on' ) )
					)
				) ) );
				$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_single_post_related_posts_by', array(
					'default'   		=> $fallsky_default_settings['fallsky_single_post_related_posts_by'],
					'transport' 		=> 'refresh',
					'sanitize_callback' => 'fallsky_sanitize_mutiple_choices',
					'dependency'		=> array(
						'fallsky_single_post_show_related_posts' => array( 'value' => array( 'on' ) )
					)
				) ) );
				$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_single_post_related_post_number', array(
					'default'   		=> $fallsky_default_settings['fallsky_single_post_related_post_number'],
					'transport' 		=> 'refresh',
					'sanitize_callback' => 'fallsky_sanitize_choice',
					'dependency'		=> array(
						'fallsky_single_post_show_related_posts' => array( 'value' => array( 'on' ) )
					)
				) ) );
			}

			// Controls
			$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_single_post_default_template', array(
				'type' 		=> 'radio',
				'label' 	=> esc_html__( 'Default Post Template', 'fallsky' ),
				'settings'	=> 'fallsky_single_post_default_template',
				'section'	=> 'fallsky_section_single_post',
				'choices' 	=> array(
					'post-template-1' => esc_html__( 'Template 1', 'fallsky' ),
					'post-template-2' => esc_html__( 'Template 2', 'fallsky' ),
					'post-template-3' => esc_html__( 'Template 3', 'fallsky' ),
					'post-template-4' => esc_html__( 'Template 4', 'fallsky' ),
					'post-template-5' => esc_html__( 'Template 5', 'fallsky' ),
					'post-template-6' => esc_html__( 'Template 6', 'fallsky' ),
					'post-template-7' => esc_html__( 'Template 7', 'fallsky' )
				)
			) ) );
			$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_single_post_default_sidebar', array(
				'type' 			=> 'radio',
				'label' 		=> esc_html__('Default Sidebar Layout', 'fallsky'),
				'settings'		=> 'fallsky_single_post_default_sidebar',
				'section'		=> 'fallsky_section_single_post',
				'choices' 		=> array(
					'' 						=> esc_html__( 'No sidebar', 'fallsky' ),
					'with-sidebar-left' 	=> esc_html__( 'Left sidebar', 'fallsky' ),
					'with-sidebar-right'	=> esc_html__( 'Right sidebar', 'fallsky' )
				)
			) ) );
			$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_sticky_post_nav_group', array(
				'type' 		=> 'group',
				'label' 	=> esc_html__( 'Sticky Post Nav', 'fallsky' ),
				'section' 	=> 'fallsky_section_single_post',
				'settings' 	=> 'fallsky_sticky_post_nav_group',
				'children'	=> array(
					new Fallsky_Customize_Control( $wp_customize, 'fallsky_sticky_post_nav', array(
						'type'			=> 'checkbox',
						'label_first'	=> true,
						'label'			=> esc_html__( 'Display Sticky Post Nav on top', 'fallsky' ),
						'section' 		=> 'fallsky_section_single_post',
						'settings' 		=> 'fallsky_sticky_post_nav'
					) ),
					new Fallsky_Customize_Control( $wp_customize, 'fallsky_post_nav_color_scheme', array(
						'type' 				=> 'radio',
						'label' 			=> esc_html__( 'Color Scheme', 'fallsky' ),
						'section' 			=> 'fallsky_section_single_post',
						'settings' 			=> 'fallsky_post_nav_color_scheme',
						'active_callback'	=> 'fallsky_customize_control_active_cb',
						'choices'			=> array(
							'light-color'	=> esc_html__( 'Light', 'fallsky' ),
							'dark-color'	=> esc_html__( 'Dark', 'fallsky' )
						)
					) ),
					new WP_Customize_Color_Control( $wp_customize, 'fallsky_post_nav_bg_color', array(
						'label' 			=> esc_html__( 'Background Color', 'fallsky' ),
						'section' 			=> 'fallsky_section_single_post',
						'settings' 			=> 'fallsky_post_nav_bg_color',
						'active_callback'	=> 'fallsky_customize_control_active_cb',
					) )
				)
			) ) );
			$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_single_post_show_category', array(
				'type' 			=> 'checkbox',
				'label_first'	=> true,
				'label' 		=> esc_html__( 'Display Categories in post header', 'fallsky' ),
				'section' 		=> 'fallsky_section_single_post',
				'settings' 		=> 'fallsky_single_post_show_category'
			) ) );
			$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_single_post_show_tags', array(
				'type' 			=> 'checkbox',
				'label_first'	=> true,
				'label' 		=> esc_html__( 'Display Tags after post content', 'fallsky' ),
				'choices' 		=> array( 'on' => '' ),
				'section' 		=> 'fallsky_section_single_post',
				'settings' 		=> 'fallsky_single_post_show_tags'
			) ) );

			if ( class_exists( 'Fallsky_Extension' ) ) {
				$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_single_post_sharing_group', array(
					'type' 		=> 'group',
					'label' 	=> esc_html__( 'Post Sharing', 'fallsky' ),
					'section' 	=> 'fallsky_section_single_post',
					'settings' 	=> 'fallsky_single_post_sharing_group',
					'children'	=> array(
						new Fallsky_Customize_Control( $wp_customize, 'fallsky_single_post_show_sharing_buttons', array(
							'type' 			=> 'checkbox',
							'label_first'	=> true,
							'label' 		=> esc_html__( 'Display Side Post Share Buttons', 'fallsky' ),
							'description' 	=> esc_html__( 'Please note: the Side Post Share Buttons only show when screen is wider than 768px.', 'fallsky' ),
							'section' 		=> 'fallsky_section_single_post',
							'settings' 		=> 'fallsky_single_post_show_sharing_buttons'
						) ),
						new Fallsky_Customize_Control( $wp_customize, 'fallsky_single_post_show_sharing_buttons_on_mobile', array(
							'type' 				=> 'checkbox',
							'label_first'		=> true,
							'label' 			=> esc_html__( 'Also display Post Share Button on small screens (sticky to screen bottom)', 'fallsky' ),
							'section' 			=> 'fallsky_section_single_post',
							'settings' 			=> 'fallsky_single_post_show_sharing_buttons_on_mobile',
							'active_callback'	=> 'fallsky_customize_control_active_cb'
						) ),
						new Fallsky_Customize_Control( $wp_customize, 'fallsky_single_post_facebook_sharing', array(
							'type' 				=> 'checkbox',
							'label' 			=> esc_html__( 'Facebook', 'fallsky' ),
							'section' 			=> 'fallsky_section_single_post',
							'settings' 			=> 'fallsky_single_post_facebook_sharing',
							'active_callback'	=> 'fallsky_customize_control_active_cb'
						) ),
						new Fallsky_Customize_Control( $wp_customize, 'fallsky_single_post_twitter_sharing', array(
							'type' 				=> 'checkbox',
							'label' 			=> esc_html__( 'Twitter', 'fallsky' ),
							'section' 			=> 'fallsky_section_single_post',
							'settings' 			=> 'fallsky_single_post_twitter_sharing',
							'active_callback'	=> 'fallsky_customize_control_active_cb'
						) ),
						new Fallsky_Customize_Control( $wp_customize, 'fallsky_single_post_pinterest_sharing', array(
							'type' 				=> 'checkbox',
							'label' 			=> esc_html__( 'Pinterest', 'fallsky' ),
							'section' 			=> 'fallsky_section_single_post',
							'settings'	 		=> 'fallsky_single_post_pinterest_sharing',
							'active_callback'	=> 'fallsky_customize_control_active_cb'
						) ),
						new Fallsky_Customize_Control( $wp_customize, 'fallsky_single_post_google_plus_sharing', array(
							'type' 				=> 'checkbox',
							'label'				=> esc_html__( 'Google+', 'fallsky' ),
							'section' 			=> 'fallsky_section_single_post',
							'settings' 			=> 'fallsky_single_post_google_plus_sharing',
							'active_callback'	=> 'fallsky_customize_control_active_cb'
						) )
					)
				) ) );
			}
			$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_single_post_footer_meta_group', array(
				'type' 			=> 'group',
				'label' 		=> esc_html__( 'Footer Meta', 'fallsky' ),
				'description' 	=> esc_html__( 'Display Selected Footer Meta', 'fallsky' ),
				'section' 		=> 'fallsky_section_single_post',
				'settings' 		=> 'fallsky_single_post_footer_meta_group',
				'children' 		=> array(
					new Fallsky_Customize_Control( $wp_customize, 'fallsky_single_post_show_author', array(
						'type' 		=> 'checkbox',
						'label' 	=> esc_html__( 'Author Name', 'fallsky' ),
						'section' 	=> 'fallsky_section_single_post',
						'settings' 	=> 'fallsky_single_post_show_author'
					) ),
					new Fallsky_Customize_Control( $wp_customize, 'fallsky_single_post_show_date', array(
						'type' 		=> 'checkbox',
						'label' 	=> esc_html__( 'Publish Date', 'fallsky' ),
						'section' 	=> 'fallsky_section_single_post',
						'settings' 	=> 'fallsky_single_post_show_date'
					) ),
					new Fallsky_Customize_Control( $wp_customize, 'fallsky_single_post_show_view', array(
						'type' 		=> 'checkbox',
						'label' 	=> esc_html__( 'View Counts', 'fallsky' ),
						'section' 	=> 'fallsky_section_single_post',
						'settings' 	=> 'fallsky_single_post_show_view'
					) ),
					new Fallsky_Customize_Control( $wp_customize, 'fallsky_single_post_show_like', array(
						'type' 		=> 'checkbox',
						'label' 	=> esc_html__( 'Like Counts', 'fallsky' ),
						'section' 	=> 'fallsky_section_single_post',
						'settings' 	=> 'fallsky_single_post_show_like'
					) ),
					new Fallsky_Customize_Control( $wp_customize, 'fallsky_single_post_show_comment_count', array(
						'type' 		=> 'checkbox',
						'label' 	=> esc_html__( 'Comment Counts', 'fallsky' ),
						'section' 	=> 'fallsky_section_single_post',
						'settings' 	=> 'fallsky_single_post_show_comment_count'
					) )
				)
			) ) );
			$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_single_post_show_author_info_box', array(
				'type' 			=> 'checkbox',
				'label_first'	=> true,
				'label' 		=> esc_html__( 'Display Author Info Box', 'fallsky' ),
				'description'	=> esc_html__( 'Please note: the author\'s biographical info need to have some content, or the author info box will not show.', 'fallsky' ),
				'section' 		=> 'fallsky_section_single_post',
				'settings' 		=> 'fallsky_single_post_show_author_info_box'
			) ) );
			$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_single_post_show_signup_form', array(
				'type' 			=> 'checkbox',
				'label_first'	=> true,
				'label' 		=> esc_html__( 'Display Signup Form', 'fallsky' ),
				'section' 		=> 'fallsky_section_single_post',
				'settings' 		=> 'fallsky_single_post_show_signup_form',
				'description' 	=> function_exists( 'mc4wp' ) ? '' 
					: esc_html__( 'Please make sure you have installed and activated the plugin "MailChimp for WordPress".', 'fallsky' ),
			) ) );
			if ( $has_mc4wp ) {
				$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_single_post_signup_form_id', array(
					'type' 				=> 'select',
					'section' 			=> 'fallsky_section_single_post',
					'settings' 			=> 'fallsky_single_post_signup_form_id',
					'choices'			=> fallsky_mc4w_forms(),
					'active_callback' 	=> 'fallsky_customize_control_active_cb'
				) ) );
			}
			$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_single_post_show_pagination', array(
				'type' 			=> 'checkbox',
				'label_first'	=> true,
				'label' 		=> esc_html__( 'Display Post Pagination', 'fallsky' ),
				'section' 		=> 'fallsky_section_single_post',
				'settings' 		=> 'fallsky_single_post_show_pagination'
			) ) );
			if ( class_exists( 'Fallsky_Extension' ) ) {
				$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_single_related_group', array(
					'type' 		=> 'group',
					'label' 	=> esc_html__( 'Related Posts', 'fallsky' ),
					'section' 	=> 'fallsky_section_single_post',
					'settings'	=> 'fallsky_single_related_group',
					'children'	=> array(
						new Fallsky_Customize_Control( $wp_customize, 'fallsky_single_post_show_related_posts', array(
							'type' 			=> 'checkbox',
							'label_first'	=> true,
							'label' 		=> esc_html__( 'Display Related Posts', 'fallsky' ),
							'section' 		=> 'fallsky_section_single_post',
							'settings'		=> 'fallsky_single_post_show_related_posts'
						) ),
						new Fallsky_Customize_Control( $wp_customize, 'fallsky_single_post_related_posts_title', array(
							'type' 				=> 'text',
							'label' 			=> esc_html__( 'Related Posts Title', 'fallsky' ),
							'input_attrs' 		=> array( 'placeholder' => esc_html__( 'e.g. You May Also Like', 'fallsky' ) ),
							'section' 			=> 'fallsky_section_single_post',
							'settings' 			=> 'fallsky_single_post_related_posts_title',
							'active_callback' 	=> 'fallsky_customize_control_active_cb'
						) ),
						new Fallsky_Customize_Control( $wp_customize, 'fallsky_single_post_related_posts_by', array(
							'type' 				=> 'select',
							'label' 			=> esc_html__( 'Pick Posts by', 'fallsky' ),
							'section' 			=> 'fallsky_section_single_post',
							'settings' 			=> 'fallsky_single_post_related_posts_by',
							'active_callback' 	=> 'fallsky_customize_control_active_cb',
							'choices' 			=> array(
								'category' 	=> esc_html__( 'Category', 'fallsky' ),
								'tag'	 	=> esc_html__( 'Tag', 'fallsky' ),
								'author' 	=> esc_html__( 'Author', 'fallsky' )
							)
						) ),
						new Fallsky_Customize_Control( $wp_customize, 'fallsky_single_post_related_post_number', array(
							'type' 				=> 'select',
							'label' 			=> esc_html__( 'How many related posts will be displayed?', 'fallsky' ),
							'section' 			=> 'fallsky_section_single_post',
							'settings' 			=> 'fallsky_single_post_related_post_number',
							'active_callback' 	=> 'fallsky_customize_control_active_cb',
							'choices' 			=> array(
								3 	=> esc_html__( '3', 'fallsky' ),
								6 	=> esc_html__( '6', 'fallsky' )
							)
						) )
					)
				) ) );
			}	
		}
		protected function get_custom_styles() {
			global $fallsky_default_settings;
			$styles = array();

			$styles['post-nav-bg-color'] = fallsky_get_style(
				'fallsky_post_nav_bg_color',
				'#page .post-nav',
				'background-color: %s;'
			);
			return $styles;
		}
		public function frontend_actions() {
			if ( is_singular( array( 'post' ) ) ) {
				require_once FALLSKY_THEME_INC . 'customize/frontend-render/class-single-post.php';
			}
		}
	}
	new Fallsky_Customize_Posts();
}