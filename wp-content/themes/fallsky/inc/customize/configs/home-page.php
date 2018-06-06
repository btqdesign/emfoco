<?php
/**
* Customize section home page configuration files.
*/

if(!class_exists('Fallsky_Customize_Homepage')){
	class Fallsky_Customize_Homepage extends Fallsky_Customize_Base {
		private $featured_render 						= null;
		private $main_area_render						= null;
		private $static_homepage_content_from_options  	= array();
		private $static_homepage_content_from_default 	= '';
		private $is_customize_homepage_content			= null;
		public function __construct(){
			parent::__construct();
			$this->includes();
			$this->static_homepage_content_from_default = 'page';
			$this->static_homepage_content_from_options = array(
				'page' 		=> esc_html__('From Page Content', 'fallsky'),
				'customize' => esc_html__('From Customizer > Homepage Section', 'fallsky')
			);
			add_filter('tiny_mce_before_init', 								array($this, 'mce_editor_settings'), 999, 2);
			add_filter('admin_init', 										array($this, 'settings_api_init'));
			add_filter('fallsky_is_static_homepage_content_from_customize', array($this, 'is_homepage_content_from_customize'));
		}
		private function includes(){
			$dir = FALLSKY_THEME_INC . 'customize/frontend-render/';

			require_once $dir . 'class-homepage-featured-area.php';
			require_once $dir . 'class-homepage-main-area.php';
			$this->featured_render 	= new Fallsky_Customize_Homepage_Featured_Frontend_Render();
			$this->main_area_render = new Fallsky_Customize_Homepage_Main_Area_Frontend_Render();

			global $wp_customize;
			if(!empty($wp_customize)){
				$components = apply_filters('customize_loaded_components', array('widgets', 'nav_menus'), $wp_customize);
				if(in_array('widgets', $components)){
					require_once  FALLSKY_THEME_INC . 'customize/class-customize-homepage-area.php';		
					new Fallsky_Customize_Homepage_Area($wp_customize, 'fallsky_section_home_main_area_1', 'fallsky_homepage_main_area');
				}
			}
		}
		public function settings_api_init(){
			add_settings_field(
				'fallsky_static_homepage_content',
				esc_html__('Static Homepage Content', 'fallsky'),
				array($this, 'render_static_homepage_content'),
				'reading'
			);
		 	register_setting('reading', 'fallsky_static_homepage_content', array('sanitize_callback' => array($this, 'sanitize_static_homepage_content')));
		}
		public function render_static_homepage_content(){
			$from 			= get_option('fallsky_static_homepage_content', $this->static_homepage_content_from_default);
			$page_on_front 	= get_option('page_on_front');
			$front 			= get_option('show_on_front');
			$options 		= array();
			foreach($this->static_homepage_content_from_options as $v => $l){
				$options[] = sprintf( '<option value="%s" %s>%s</option>', $v, selected($from, $v, false), $l );
			}
			printf(
				'<select name="%1$s" id="%1$s"%2$s>%3$s</select>%4$s',
				'fallsky_static_homepage_content',
				empty($page_on_front) || ('posts' == $front) ? ' disabled' : '',
				implode('', $options),
				sprintf('<p class="description">%s</p>', esc_html__('For static homepage only', 'fallsky'))
			);
		}
		public function sanitize_static_homepage_content($value){
			$keys = array_keys($this->static_homepage_content_from_options);
			return in_array($value, $keys) ? $value : $this->static_homepage_content_from_default;
		}
		public function is_homepage_content_from_customize($from){
			if(!isset($this->is_homepage_content_from_customize)){
				$this->is_homepage_content_from_customize = ('customize' == get_option('fallsky_static_homepage_content', $this->static_homepage_content_from_default));
			}
			return $this->is_homepage_content_from_customize;
		}
		public function show_front_sections(){
			$front_page_id 		= get_option('page_on_front');
			$posts_page_id 		= get_option('page_for_posts');
			$is_static_page 	= ('page' == get_option('show_on_front'));
			$is_static_front   	= $is_static_page && !empty($front_page_id);
			$is_static_blog 	= $is_static_page && empty( $front_page_id ) && !empty( $posts_page_id );
			$is_from_customize 	= apply_filters('fallsky_is_static_homepage_content_from_customize', false);
			return ($is_static_front && $is_from_customize) || !$is_static_page || $is_static_blog;
		}
		public function show_static_homepage_content_from_options(){
			$page_on_front = get_option('page_on_front');
			return !empty($page_on_front);
		}
		public function register_controls($wp_customize){
			global $fallsky_default_settings;

			$panel_description = sprintf(
				esc_html__('For static front page, the sections "Fullwidth Featured Area" and "Home Main Content Area" only work when choosing the content from "Customize". You can %sclick here%s to change the setting.', 'fallsky'),
				'<a href="#" data-control-id="fallsky_static_homepage_content" class="show-control">', 
				'</a>'
			);
			// Panel
			$wp_customize->add_panel(new WP_Customize_Panel($wp_customize, 'fallsky_panel_home', array(
				'title' 		=> esc_html__('Home Page', 'fallsky'),
				'priority' 		=> 35,
				'description' 	=> $panel_description
			)));

			// Sections
			$wp_customize->add_section('fallsky_section_fullwidth_featured_area', array(
				'title' 			=> esc_html__('Fullwidth Featured Area', 'fallsky'),
				'panel' 			=> 'fallsky_panel_home',
				'active_callback'	=> array($this, 'show_front_sections')
			));
			$wp_customize->add_section('fallsky_section_home_main_area_1', array(
				'title' 			=> esc_html__('Home Main Content Area', 'fallsky'),
				'panel' 			=> 'fallsky_panel_home',
				'active_callback'	=> array($this, 'show_front_sections')
			));

			// Settings
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_home_show_fullwidth_featured_area', array(
				'default'   		=> $fallsky_default_settings['fallsky_home_show_fullwidth_featured_area'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_checkbox'
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_home_fullwidth_featured_area_type', array(
				'default'   		=> $fallsky_default_settings['fallsky_home_fullwidth_featured_area_type'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice',
				'dependency' 		=> array(
					'fallsky_home_show_fullwidth_featured_area' => array('value' => array('on'))
				)
			)));
			$wp_customize->selective_refresh->add_partial('fallsky_home_featured_posts_maybe_refresh', array(
				'selector'				=> '.featured-section',
				'render_callback'		=> array($this->featured_render, 'customizer_selective_refresh_show_content'),
				'container_inclusive' 	=> true,
				'settings'				=> array(
					'fallsky_home_show_fullwidth_featured_area',
					'fallsky_home_posts_slider_post_number',
					'fallsky_home_posts_by',
					'fallsky_home_categories'
				)
			));
			$wp_customize->selective_refresh->add_partial('fallsky_home_featured_posts', array(
				'selector'				=> '.featured-section',
				'render_callback'		=> array($this->featured_render, 'show_content'),
				'container_inclusive' 	=> true,
				'settings'				=> array(
					'fallsky_home_posts_blocks_style',
					'fallsky_home_posts_slider_style'
				)
			));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_home_posts_slider_style', array(
				'default'   		=> $fallsky_default_settings['fallsky_home_posts_slider_style'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_choice',
				'dependency' 		=> array(
					'fallsky_home_show_fullwidth_featured_area' => array('value' => array('on')),
					'fallsky_home_fullwidth_featured_area_type'	=> array('value' => array('posts-slider'))
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_home_posts_slider_post_number', array(
				'default'   		=> $fallsky_default_settings['fallsky_home_posts_slider_post_number'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'absint',
				'dependency' 		=> array(
					'fallsky_home_show_fullwidth_featured_area' => array('value' => array('on')),
					'fallsky_home_fullwidth_featured_area_type'	=> array('value' => array('posts-slider'))
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_home_posts_slider_auto_play', array(
				'default'   		=> $fallsky_default_settings['fallsky_home_posts_slider_auto_play'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_checkbox',
				'dependency' 		=> array(
					'fallsky_home_show_fullwidth_featured_area' => array('value' => array('on')),
					'fallsky_home_fullwidth_featured_area_type'	=> array('value' => array('posts-slider'))
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_home_posts_slider_auto_play_pause_duration', array(
				'default'   		=> $fallsky_default_settings['fallsky_home_posts_slider_auto_play_pause_duration'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'absint',
				'dependency' 		=> array(
					'fallsky_home_show_fullwidth_featured_area' => array('value' => array('on')),
					'fallsky_home_fullwidth_featured_area_type'	=> array('value' => array('posts-slider')),
					'fallsky_home_posts_slider_auto_play'		=> array('value' => array('on'))
				)
			)));
			$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_home_posts_slider_pause_on_hover', array(
				'default'   		=> $fallsky_default_settings['fallsky_home_posts_slider_pause_on_hover'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_checkbox',
				'dependency' 		=> array(
					'fallsky_home_show_fullwidth_featured_area' => array( 'value' => array( 'on' ) ),
					'fallsky_home_fullwidth_featured_area_type'	=> array( 'value' => array( 'posts-slider' ) ),
					'fallsky_home_posts_slider_auto_play'		=> array( 'value' => array( 'on' ) )
				)
			) ) );
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_home_posts_content_options_group', array(
				'default'   		=> '',
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_empty',
				'dependency' 		=> array(
					'fallsky_home_show_fullwidth_featured_area' => array('value' => array('on')),
					'fallsky_home_fullwidth_featured_area_type'	=> array('value' => array('posts-slider'))
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_home_posts_slider_hide_category', array(
				'default'   		=> $fallsky_default_settings['fallsky_home_posts_slider_hide_category'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_checkbox',
				'dependency' 		=> array(
					'fallsky_home_show_fullwidth_featured_area' => array('value' => array('on')),
					'fallsky_home_fullwidth_featured_area_type'	=> array('value' => array('posts-slider'))
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_home_posts_hide_excerpt', array(
				'default'   		=> $fallsky_default_settings['fallsky_home_posts_hide_excerpt'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_checkbox',
				'dependency' 		=> array(
					'fallsky_home_show_fullwidth_featured_area' => array('value' => array('on')),
					'fallsky_home_fullwidth_featured_area_type'	=> array('value' => array('posts-slider'))
				)
			)));

			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_home_posts_blocks_style', array(
				'default'   		=> $fallsky_default_settings['fallsky_home_posts_blocks_style'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_choice',
				'dependency' 		=> array(
					'fallsky_home_show_fullwidth_featured_area' => array('value' => array('on')),
					'fallsky_home_fullwidth_featured_area_type'	=> array('value' => array('posts-blocks'))
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_home_posts_block_options_group', array(
				'default'   		=> '',
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_empty',
				'dependency' 		=> array(
					'fallsky_home_show_fullwidth_featured_area' => array('value' => array('on')),
					'fallsky_home_fullwidth_featured_area_type'	=> array('value' => array('posts-blocks')),
					'fallsky_home_posts_blocks_style'			=> array('value' => array('style-blocks-1', 'style-blocks-2'))
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_home_posts_block_hide_category', array(
				'default'   		=> $fallsky_default_settings['fallsky_home_posts_block_hide_category'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_checkbox',
				'dependency' 		=> array(
					'fallsky_home_show_fullwidth_featured_area' => array('value' => array('on')),
					'fallsky_home_fullwidth_featured_area_type'	=> array('value' => array('posts-blocks')),
					'fallsky_home_posts_blocks_style'			=> array('value' => array('style-blocks-1', 'style-blocks-2'))
				)
			)));

			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_home_pick_posts_group', array(
				'default'   		=> '',
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_empty',
				'dependency' 		=> array(
					'fallsky_home_show_fullwidth_featured_area' => array('value' => array('on')),
					'fallsky_home_fullwidth_featured_area_type'	=> array('value' => array('posts-blocks', 'posts-slider'))
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_home_posts_by', array(
				'default'   		=> $fallsky_default_settings['fallsky_home_posts_by'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_choice',
				'dependency' 		=> array(
					'fallsky_home_show_fullwidth_featured_area' => array('value' => array('on')),
					'fallsky_home_fullwidth_featured_area_type'	=> array('value' => array('posts-blocks', 'posts-slider'))
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_home_categories', array(
				'default'   		=> $fallsky_default_settings['fallsky_home_categories'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_mutiple_choices',
				'dependency'		=> array(
					'fallsky_home_show_fullwidth_featured_area' => array('value' => array('on')),
					'fallsky_home_fullwidth_featured_area_type'	=> array('value' => array('posts-blocks', 'posts-slider')),
					'fallsky_home_posts_by'						=> array('value' => array('category'))
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_home_exclude_posts_from_main_content', array(
				'default'   		=> $fallsky_default_settings['fallsky_home_exclude_posts_from_main_content'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_checkbox',
				'dependency' 		=> array(
					'fallsky_home_show_fullwidth_featured_area' => array('value' => array('on')),
					'fallsky_home_fullwidth_featured_area_type'	=> array('value' => array('posts-blocks', 'posts-slider'))
				)
			)));

			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_home_custom_content_editor', array(
				'default'   		=> $fallsky_default_settings['fallsky_home_custom_content_editor'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_html',
				'dependency' 		=> array(
					'fallsky_home_show_fullwidth_featured_area' => array('value' => array('on')),
					'fallsky_home_fullwidth_featured_area_type'	=> array('value' => array('custom'))
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_home_custom_content_design_group', array(
				'default'   		=> '',
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_empty',
				'dependency' 		=> array(
					'fallsky_home_show_fullwidth_featured_area' => array('value' => array('on')),
					'fallsky_home_fullwidth_featured_area_type'	=> array('value' => array('custom'))
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_home_custom_content_bg_color', array(
				'default'   		=> $fallsky_default_settings['fallsky_home_custom_content_bg_color'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'sanitize_hex_color',
				'dependency' 		=> array(
					'fallsky_home_show_fullwidth_featured_area' => array('value' => array('on')),
					'fallsky_home_fullwidth_featured_area_type'	=> array('value' => array('custom'))
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_home_custom_content_bg_image', array(
				'default'   		=> $fallsky_default_settings['fallsky_home_custom_content_bg_image'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'absint',
				'dependency' 		=> array(
					'fallsky_home_show_fullwidth_featured_area' => array('value' => array('on')),
					'fallsky_home_fullwidth_featured_area_type'	=> array('value' => array('custom'))
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_home_custom_content_bg_size', array(
				'default'			=> $fallsky_default_settings['fallsky_home_custom_content_bg_size'],
				'transport'			=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_choice',
				'dependency' 		=> array(
					'fallsky_home_custom_content_bg_image' 		=> array('value' => array('', 0, '0'), 'operator' => 'not in'),
					'fallsky_home_show_fullwidth_featured_area' => array('value' => array('on')),
					'fallsky_home_fullwidth_featured_area_type'	=> array('value' => array('custom'))
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_home_custom_content_bg_repeat', array(
				'default'   		=> $fallsky_default_settings['fallsky_home_custom_content_bg_repeat'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_checkbox',
				'dependency' 		=> array(
					'fallsky_home_custom_content_bg_image' 		=> array('value' => array('', 0, '0'), 'operator' => 'not in'),
					'fallsky_home_show_fullwidth_featured_area' => array('value' => array('on')),
					'fallsky_home_fullwidth_featured_area_type'	=> array('value' => array('custom'))
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_home_custom_content_bg_position_x', array(
				'default'			=> $fallsky_default_settings['fallsky_home_custom_content_bg_position_x'],
				'transport'			=> 'postMessage',
				'sanitize_callback' => 'sanitize_text_field',
				'dependency' 		=> array(
					'fallsky_home_custom_content_bg_image' 		=> array('value' => array('', 0, '0'), 'operator' => 'not in'),
					'fallsky_home_show_fullwidth_featured_area' => array('value' => array('on')),
					'fallsky_home_fullwidth_featured_area_type'	=> array('value' => array('custom'))
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_home_custom_content_bg_position_y', array(
				'default'  			=> $fallsky_default_settings['fallsky_home_custom_content_bg_position_y'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'sanitize_text_field',
				'dependency' 		=> array(
					'fallsky_home_custom_content_bg_image' 		=> array('value' => array('', 0, '0'), 'operator' => 'not in'),
					'fallsky_home_show_fullwidth_featured_area' => array('value' => array('on')),
					'fallsky_home_fullwidth_featured_area_type'	=> array('value' => array('custom'))
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_home_custom_content_bg_attachment', array(
				'default'			=> $fallsky_default_settings['fallsky_home_custom_content_bg_attachment'],
				'transport'			=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_checkbox',
				'dependency' 		=> array(
					'fallsky_home_custom_content_bg_image' 		=> array('value' => array('', 0, '0'), 'operator' => 'not in'),
					'fallsky_home_show_fullwidth_featured_area' => array('value' => array('on')),
					'fallsky_home_fullwidth_featured_area_type'	=> array('value' => array('custom'))
				)
			)));

			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_home_custom_content_bg_video', array(
				'default'			=> $fallsky_default_settings['fallsky_home_custom_content_bg_video'],
				'transport'			=> 'postMessage',
				'sanitize_callback' => 'absint',
				'validate_callback' => array($wp_customize, '_validate_header_video'),
				'dependency' 		=> array(
					'fallsky_home_show_fullwidth_featured_area' => array('value' => array('on')),
					'fallsky_home_fullwidth_featured_area_type'	=> array('value' => array('custom'))
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_home_custom_content_external_bg_video', array(
				'default'			=> $fallsky_default_settings['fallsky_home_custom_content_external_bg_video'],
				'transport'			=> 'postMessage',
				'sanitize_callback' => 'esc_url_raw',
				'validate_callback' => array($wp_customize, '_validate_external_header_video'),
				'dependency' 		=> array(
					'fallsky_home_show_fullwidth_featured_area' => array('value' => array('on')),
					'fallsky_home_fullwidth_featured_area_type'	=> array('value' => array('custom'))
				)
			)));
			$wp_customize->selective_refresh->add_partial('fallsky_home_custom_content_bg_video', array(
				'selector'				=> '.featured-section.style-custom .section-bg',
				'render_callback'		=> array($this->featured_render, 'bg_video_settings'),
				'settings'				=> array('fallsky_home_custom_content_bg_video', 'fallsky_home_custom_content_external_bg_video'),
				'container_inclusive' 	=> true,
			));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_home_custom_content_text_color', array(
				'default'			=> $fallsky_default_settings['fallsky_home_custom_content_text_color'],
				'transport'			=> 'postMessage',
				'sanitize_callback' => 'sanitize_hex_color',
				'dependency' 		=> array(
					'fallsky_home_show_fullwidth_featured_area' => array('value' => array('on')),
					'fallsky_home_fullwidth_featured_area_type'	=> array('value' => array('custom'))
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_home_custom_content_height_type', array(
				'default'			=> $fallsky_default_settings['fallsky_home_custom_content_height_type'],
				'transport'			=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice',
				'dependency' 		=> array(
					'fallsky_home_show_fullwidth_featured_area' => array('value' => array('on')),
					'fallsky_home_fullwidth_featured_area_type'	=> array('value' => array('custom'))
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_home_custom_content_height', array(
				'default'			=> $fallsky_default_settings['fallsky_home_custom_content_height'],
				'transport'			=> 'postMessage',
				'sanitize_callback' => 'absint',
				'dependency' 		=> array(
					'fallsky_home_show_fullwidth_featured_area' => array('value' => array('on')),
					'fallsky_home_fullwidth_featured_area_type'	=> array('value' => array('custom')),
					'fallsky_home_custom_content_height_type'	=> array('value' => array('custom'))
				)
			)));

			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_homepage_main_area', array(
				'default'			=> $fallsky_default_settings['fallsky_homepage_main_area'],
				'transport'			=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_array'
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_home_sidebar', array(
				'default'			=> $fallsky_default_settings['fallsky_home_sidebar'],
				'transport'			=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			)));

			// Controls
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_home_show_fullwidth_featured_area', array(
				'type' 				=> 'checkbox',
				'label_first'	 	=> true,
				'label' 			=> esc_html__('Display Featured Content', 'fallsky'),
				'section' 			=> 'fallsky_section_fullwidth_featured_area',
				'settings' 			=> 'fallsky_home_show_fullwidth_featured_area',
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_home_fullwidth_featured_area_type', array(
				'type' 				=> 'select',
				'label'				=> esc_html__('Type', 'fallsky'),
				'section' 			=> 'fallsky_section_fullwidth_featured_area',
				'settings' 			=> 'fallsky_home_fullwidth_featured_area_type',
				'active_callback' 	=> 'fallsky_customize_control_active_cb',
				'choices'			=> array(
					'posts-slider'	=> esc_html__('Posts Slider', 'fallsky'),
					'posts-blocks'	=> esc_html__('Posts Blocks', 'fallsky'),
					'custom'		=> esc_html__('Custom Content', 'fallsky'),
				)
			)));

			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_home_posts_slider_style', array(
				'type' 				=> 'radio',
				'with_bg'			=> true,
				'wrap_id'			=> 'fallsky_featured_slider',
				'label'				=> esc_html__('Slider Style', 'fallsky'),
				'section' 			=> 'fallsky_section_fullwidth_featured_area',
				'settings' 			=> 'fallsky_home_posts_slider_style',
				'active_callback' 	=> 'fallsky_customize_control_active_cb',
				'choices'			=> array(
					'style-slider-1' => esc_html__('Slider 1', 'fallsky'),
					'style-slider-2' => esc_html__('Slider 2', 'fallsky'),
					'style-slider-3' => esc_html__('Slider 3', 'fallsky')
				)
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_home_posts_slider_post_number', array(
				'type'				=> 'number',
				'label' 			=> esc_html__('Show at most x posts in slider', 'fallsky'),
				'input_attrs'		=> array('min' => 1, 'max' => 8),
				'section' 			=> 'fallsky_section_fullwidth_featured_area',
				'settings' 			=> 'fallsky_home_posts_slider_post_number',
				'active_callback' 	=> 'fallsky_customize_control_active_cb'
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_home_posts_slider_auto_play', array(
				'type'				=> 'checkbox',
				'label_first'		=> true,
				'label' 			=> esc_html__('Auto play the slider', 'fallsky'),
				'section' 			=> 'fallsky_section_fullwidth_featured_area',
				'settings' 			=> 'fallsky_home_posts_slider_auto_play',
				'active_callback' 	=> 'fallsky_customize_control_active_cb'
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_home_posts_slider_auto_play_pause_duration', array(
				'type'				=> 'number_slider',
				'label' 			=> esc_html__('Autoplay pause duration', 'fallsky'),
				'after_text'		=> esc_html__('second(s)', 'fallsky'),
				'section' 			=> 'fallsky_section_fullwidth_featured_area',
				'settings' 			=> 'fallsky_home_posts_slider_auto_play_pause_duration',
				'active_callback' 	=> 'fallsky_customize_control_active_cb',
				'input_attrs'		=> array(
					'data-min'	=> '3',
					'data-max'	=> '8',
					'data-step'	=> '1'
				)
			)));
			$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_home_posts_slider_pause_on_hover', array(
				'type'				=> 'checkbox',
				'label_first'		=> true,
				'label' 			=> esc_html__( 'Pause on hover', 'fallsky' ),
				'section' 			=> 'fallsky_section_fullwidth_featured_area',
				'settings' 			=> 'fallsky_home_posts_slider_pause_on_hover',
				'active_callback' 	=> 'fallsky_customize_control_active_cb'
			) ) );

			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_home_posts_blocks_style', array(
				'type' 				=> 'radio',
				'with_bg'			=> true,
				'wrap_id'			=> 'fallsky_featured_blocks',
				'label' 			=> esc_html__('Blocks Style', 'fallsky'),
				'section' 			=> 'fallsky_section_fullwidth_featured_area',
				'settings' 			=> 'fallsky_home_posts_blocks_style',
				'active_callback' 	=> 'fallsky_customize_control_active_cb',
				'choices' => array(
					'style-blocks-1' => esc_html__('Blocks 1', 'fallsky'),
					'style-blocks-2' => esc_html__('Blocks 2', 'fallsky'),
					'style-blocks-3' => esc_html__('Blocks 3', 'fallsky')
				),
			)));

			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_home_posts_content_options_group', array(
				'type' 				=> 'group',
				'label'				=> esc_html__('Content Options', 'fallsky'),
				'section' 			=> 'fallsky_section_fullwidth_featured_area',
				'settings' 			=> 'fallsky_home_posts_content_options_group',
				'active_callback' 	=> 'fallsky_customize_control_active_cb',
				'children'			=> array(
					new Fallsky_Customize_Control($wp_customize, 'fallsky_home_posts_slider_hide_category', array(
						'type'				=> 'checkbox',
						'label_first'		=> true,
						'label' 			=> esc_html__('Hide categories', 'fallsky'),
						'section' 			=> 'fallsky_section_fullwidth_featured_area',
						'settings' 			=> 'fallsky_home_posts_slider_hide_category',
						'active_callback' 	=> 'fallsky_customize_control_active_cb'
					)),
					new Fallsky_Customize_Control($wp_customize, 'fallsky_home_posts_hide_excerpt', array(
						'type'				=> 'checkbox',
						'label_first'		=> true,
						'label' 			=> esc_html__('Hide post excerpt', 'fallsky'),
						'section' 			=> 'fallsky_section_fullwidth_featured_area',
						'settings' 			=> 'fallsky_home_posts_hide_excerpt',
						'active_callback' 	=> 'fallsky_customize_control_active_cb'
					))
				)
			)));

			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_home_posts_block_options_group', array(
				'type' 				=> 'group',
				'label'				=> esc_html__('Content Options', 'fallsky'),
				'section' 			=> 'fallsky_section_fullwidth_featured_area',
				'settings' 			=> 'fallsky_home_posts_block_options_group',
				'active_callback' 	=> 'fallsky_customize_control_active_cb',
				'children'			=> array(
					new Fallsky_Customize_Control($wp_customize, 'fallsky_home_posts_block_hide_category', array(
						'type'				=> 'checkbox',
						'label_first'		=> true,
						'label' 			=> esc_html__('Hide categories', 'fallsky'),
						'section' 			=> 'fallsky_section_fullwidth_featured_area',
						'settings' 			=> 'fallsky_home_posts_block_hide_category',
						'active_callback' 	=> 'fallsky_customize_control_active_cb'
					))
				)
			)));

			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_home_pick_posts_group', array(
				'type' 				=> 'group',
				'label' 			=> esc_html__('Pick Posts', 'fallsky'),
				'section' 			=> 'fallsky_section_fullwidth_featured_area',
				'settings' 			=> 'fallsky_home_pick_posts_group',
				'active_callback' 	=> 'fallsky_customize_control_active_cb',
				'children'			=> array(
					new Fallsky_Customize_Control($wp_customize, 'fallsky_home_posts_by', array(
						'type' 				=> 'select',
						'label' 			=> '',
						'section' 			=> 'fallsky_section_fullwidth_featured_area',
						'settings' 			=> 'fallsky_home_posts_by',
						'active_callback' 	=> 'fallsky_customize_control_active_cb',
						'choices' 			=> array(
							'category'	=> esc_html__('By Category', 'fallsky'),
							'featured'	=> esc_html__('Featured Posts', 'fallsky'),
							'likes'		=> esc_html__('Most Liked', 'fallsky'),
							'views'		=> esc_html__('Most Viewed', 'fallsky'),
							'comments' 	=> esc_html__('Most Commented', 'fallsky')
						)
					)),
					new Fallsky_Customize_Control($wp_customize, 'fallsky_home_categories', array(
						'type' 				=> 'multiple_selection',
						'label' 			=> esc_html__('Choose Categories', 'fallsky'),
						'choices' 			=> fallsky_get_terms('category'),
						'section' 			=> 'fallsky_section_fullwidth_featured_area',
						'settings'			=> 'fallsky_home_categories',
						'active_callback'	=> 'fallsky_customize_control_active_cb'
					)),
					new Fallsky_Customize_Control($wp_customize, 'fallsky_home_exclude_posts_from_main_content', array(
						'type' 				=> 'checkbox',
						'label_first'		=> true,
						'label'				=> esc_html__('Exclude the posts from Latest Posts Archive in Homepage Main Content Area', 'fallsky'),
						'section' 			=> 'fallsky_section_fullwidth_featured_area',
						'settings' 			=> 'fallsky_home_exclude_posts_from_main_content',
						'active_callback'	=> 'fallsky_customize_control_active_cb'
					))
				)
			)));

			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_home_custom_content_editor', array(
				'type' 				=> 'mce_editor',
				'label'				=> esc_html__('Add content', 'fallsky'),
				'section' 			=> 'fallsky_section_fullwidth_featured_area',
				'settings' 			=> 'fallsky_home_custom_content_editor',
				'active_callback'	=> 'fallsky_customize_control_active_cb'
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_home_custom_content_design_group', array(
				'type' 				=> 'group',
				'label' 			=> esc_html__('Design Options', 'fallsky'),
				'section' 			=> 'fallsky_section_fullwidth_featured_area',
				'settings' 			=> 'fallsky_home_custom_content_design_group',
				'active_callback' 	=> 'fallsky_customize_control_active_cb',
				'children'			=> array(
					new WP_Customize_Color_Control($wp_customize, 'fallsky_home_custom_content_bg_color', array(
						'label' 			=> esc_html__('Background Color', 'fallsky'),
						'section' 			=> 'fallsky_section_fullwidth_featured_area',
						'settings' 			=> 'fallsky_home_custom_content_bg_color',
						'active_callback'	=> 'fallsky_customize_control_active_cb'
					)),
					new Fallsky_Customize_Control($wp_customize, 'fallsky_home_custom_content_bg_image', array(
						'type' 				=> 'image_id',
						'label' 			=> esc_html__('Background Image', 'fallsky'),
						'section' 			=> 'fallsky_section_fullwidth_featured_area',
						'settings' 			=> 'fallsky_home_custom_content_bg_image',
						'active_callback'	=> 'fallsky_customize_control_active_cb'
					)),
					new WP_Customize_Background_Position_Control($wp_customize, 'fallsky_home_custom_content_bg_position', array(
						'label' 			=> esc_html__('Image Position', 'fallsky'),
						'section'			=> 'fallsky_section_fullwidth_featured_area',
						'active_callback' 	=> 'fallsky_customize_control_active_cb',
						'settings' 			=> array(
							'x' => 'fallsky_home_custom_content_bg_position_x',
							'y' => 'fallsky_home_custom_content_bg_position_y'
						)
					)),
					new Fallsky_Customize_Control($wp_customize, 'fallsky_home_custom_content_bg_size', array(
						'type' 				=> 'select',
						'label' 			=> esc_html__('Size', 'fallsky'),
						'section' 			=> 'fallsky_section_fullwidth_featured_area',
						'settings' 			=> 'fallsky_home_custom_content_bg_size',
						'active_callback' 	=> 'fallsky_customize_control_active_cb',
						'choices' 			=> array(
							'auto' 		=> esc_html__('Original', 'fallsky'),
							'contain' 	=> esc_html__('Fit to Screen', 'fallsky'),
							'cover'		=> esc_html__('Fill Screen', 'fallsky')
						)
					)),
					new Fallsky_Customize_Control($wp_customize, 'fallsky_home_custom_content_bg_repeat', array(
						'type' 				=> 'checkbox',
						'label' 			=> esc_html__('Repeat', 'fallsky'),
						'section' 			=> 'fallsky_section_fullwidth_featured_area',
						'settings' 			=> 'fallsky_home_custom_content_bg_repeat',
						'active_callback' 	=> 'fallsky_customize_control_active_cb'
					)),
					new Fallsky_Customize_Control($wp_customize, 'fallsky_home_custom_content_bg_attachment', array(
						'type' 				=> 'checkbox',
						'label' 			=> esc_html__('Scroll with Page', 'fallsky'),
						'section' 			=> 'fallsky_section_fullwidth_featured_area',
						'settings'			=> 'fallsky_home_custom_content_bg_attachment',
						'active_callback' 	=> 'fallsky_customize_control_active_cb',
						'description'		=> sprintf(
							esc_html__('Please tick this option when you have enabled %s"parallax effect while scrolling for Homepage Fullwidth Featured Section"%s.', 'fallsky'),
							'<a href="#" class="show-control" data-control-id="fallsky_enable_parallax_on_homepage_fullwidth_area">',
							'</a>'
						)
					)),
					new WP_Customize_Media_Control($wp_customize, 'fallsky_home_custom_content_bg_video', array(
						'label'				=> esc_html__('Background Video', 'fallsky'),
						'description'		=> sprintf(
							esc_html__('Upload your video in %1$s format and minimize its file size for best results.', 'fallsky'),
							'<code>.mp4</code>'
						),
						'section'			=> 'fallsky_section_fullwidth_featured_area',
						'settings'			=> 'fallsky_home_custom_content_bg_video',
						'mime_type'			=> 'video',
						'active_callback' 	=> 'fallsky_customize_control_active_cb',
					)),
					new Fallsky_Customize_Control($wp_customize, 'fallsky_home_custom_content_external_bg_video', array(
						'type'          	=> 'url',
						'description'		=> esc_html__('Or, enter a YouTube URL:', 'fallsky'),
						'section'			=> 'fallsky_section_fullwidth_featured_area',
						'settings'			=> 'fallsky_home_custom_content_external_bg_video',
						'active_callback' 	=> 'fallsky_customize_control_active_cb',
					)),
					new WP_Customize_Color_Control($wp_customize, 'fallsky_home_custom_content_text_color', array(
						'label' 			=> esc_html__('Text Color', 'fallsky'),
						'section' 			=> 'fallsky_section_fullwidth_featured_area',
						'settings' 			=> 'fallsky_home_custom_content_text_color',
						'active_callback' 	=> 'fallsky_customize_control_active_cb'
					)),
					new Fallsky_Customize_Control($wp_customize, 'fallsky_home_custom_content_height_type', array(
						'type'				=> 'select',
						'label' 			=> esc_html__('Height Option', 'fallsky'),
						'section' 			=> 'fallsky_section_fullwidth_featured_area',
						'settings'			=> 'fallsky_home_custom_content_height_type',
						'active_callback'	=> 'fallsky_customize_control_active_cb',
						'choices'			=> array(
							'screenheight' 	=> esc_html__('Screen Height', 'fallsky'),
							'custom'		=> esc_html__('Custom', 'fallsky')
						)
					)),
					new Fallsky_Customize_Control($wp_customize, 'fallsky_home_custom_content_height', array(
						'type' 				=> 'number_with_unit',
						'label' 			=> esc_html__('Custom Height', 'fallsky'),
						'section' 			=> 'fallsky_section_fullwidth_featured_area',
						'after_text'		=> esc_html__('px', 'fallsky'),
						'settings' 			=> 'fallsky_home_custom_content_height',
						'active_callback' 	=> 'fallsky_customize_control_active_cb',
						'input_attrs'		=> array('min' => 1)
					))
				)
			)));

			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_home_sidebar', array(
				'type' 				=> 'radio',
				'label' 			=> esc_html__('Sidebar Layout', 'fallsky'),
				'description_below'	=> true,
				'section' 			=> 'fallsky_section_home_main_area_1',
				'settings' 			=> 'fallsky_home_sidebar',
				'priority'			=> 0,
				'choices' 			=> array(
					'' 						=> esc_html__('No Sidebar', 'fallsky'),
					'with-sidebar-left' 	=> esc_html__('Left Sidebar', 'fallsky'),
					'with-sidebar-right' 	=> esc_html__('Right Sidebar', 'fallsky')
				)
			)));

			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_homepage_main_area', array(
				'type'		=> 'homepage_area',
				'label' 	=> esc_html__('Home Widgets', 'fallsky'),
				'section' 	=> 'fallsky_section_home_main_area_1',
				'settings'	=> 'fallsky_homepage_main_area',
			)));

			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_static_homepage_content', array(
				'default'   		=> $this->static_homepage_content_from_default,
				'transport'			=> 'refresh',
				'type' 				=> 'option',
				'capability' 		=> 'manage_options',
				'sanitize_callback'	=> array($this, 'sanitize_static_homepage_content'),
				'dependency'		=> array(
					'page_on_front' => array('value' => array('', 0), 'operator' => 'not in'),
					'show_on_front'	=> array('value' => array('page'))
				)
			)));

			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_static_homepage_content', array(
				'type'				=> 'select',
				'label' 			=> esc_html__('Static Homepage Content', 'fallsky'),
				'section' 			=> 'static_front_page',
				'settings'			=> 'fallsky_static_homepage_content',
				'allow_addition' 	=> true,
				'active_callback'	=> 'fallsky_customize_control_active_cb',
				'choices'			=> $this->static_homepage_content_from_options
			)));
		}
		public function mce_editor_settings($mceInit, $editor_id){
			if($editor_id == 'fallsky_home_custom_content_editor'){
				$mceInit['wp_skip_init'] = true;
			}
			return $mceInit;
		}
		public function customize_js_vars($vars = array()){
			$editor_ids = isset($vars['editor_ids']) ? $vars['editor_ids'] : array();
			array_push($editor_ids, 'fallsky_home_custom_content_editor');
			$vars['editor_ids'] = $editor_ids;
			return $vars;
		}
		public function frontend_actions(){
			if(fallsky_is_front_page()){
				add_action('fallsky_before_main_content', 			array($this->featured_render, 'show_content'));
				add_action('fallsky_main_content', 					array($this->main_area_render, 'show_content'));
				add_filter('fallsky_site_header_class', 			array($this, 'site_header_class'));
				add_filter('fallsky_page_layout', 					array($this->main_area_render, 'page_layout'), 9999);
				add_filter('fallsky_is_transparent_site_header', 	array($this, 'is_transparent_site_header'));
			}
		}
		public function site_header_class($class){
			fallsky_module_enabled('fallsky_home_transparent_site_header') ? array_push($class, 'transparent') : '';
			return $class;
		}
		/*
		* Test if cucrent page is transparent site header
		* @param array of boolean, ['show on frontend', 'not show on frontend, but show on customize preview page']
		* @return array of boolean
		*/
		public function is_transparent_site_header($is = array(false, false)){
			return fallsky_module_enabled('fallsky_home_transparent_site_header') ? array(true, false) : $is;
		}
		public function frontend_js_vars($vars = array()){
			$vars = $this->featured_render->frontend_js_vars($vars);

			return $vars;
		}
		public function get_custom_styles(){
			if(!fallsky_is_front_page()){
				return array();
			}

			global $fallsky_is_preview, $fallsky_default_settings;
			$styles = array();

			$featured_area_type = fallsky_get_theme_mod('fallsky_home_fullwidth_featured_area_type');
			if(('custom' == $featured_area_type) || $fallsky_is_preview){
				$styles['home-custom-content-bg-color'] = fallsky_get_style(
					'fallsky_home_custom_content_bg_color',
					'.featured-section.style-custom .section-bg',
					'background-color: %s;'
				);
				$styles['home-custom-content-text-color'] = fallsky_get_style(
					'fallsky_home_custom_content_text_color',
					'.featured-section.style-custom',
					'color: %s;'
				);
				$custom_heigth 		= intval(fallsky_get_theme_mod('fallsky_home_custom_content_height'));
				$is_custom_height 	= (esc_attr(fallsky_get_theme_mod('fallsky_home_custom_content_height_type')) == 'custom');
				$styles['home-custom-content-height'] = fallsky_get_style(
					'fallsky_home_custom_content_height',
					'.featured-section.style-custom .custom-content',
					$is_custom_height ? 'min-height: %spx;' : 'min-height: %s;',
					$is_custom_height ? $custom_heigth : '100vh'
				);
				$bg_image_id = absint( fallsky_get_theme_mod( 'fallsky_home_custom_content_bg_image' ) );
				if( !empty( $bg_image_id) || $fallsky_is_preview ) {
					$image = empty( $bg_image_id ) ? false : fallsky_get_image_src( $bg_image_id, 'full', false );
					if($image || $fallsky_is_preview){
						$position_x 	= esc_attr(fallsky_get_theme_mod('fallsky_home_custom_content_bg_position_x'));
						$position_y 	= esc_attr(fallsky_get_theme_mod('fallsky_home_custom_content_bg_position_y'));
						$size 			= esc_attr(fallsky_get_theme_mod('fallsky_home_custom_content_bg_size'));
						$repeat 		= fallsky_module_enabled('fallsky_home_custom_content_bg_repeat');
						$attachment 	= fallsky_module_enabled('fallsky_home_custom_content_bg_attachment');

						$styles['home-custom-content-bg-size'] = sprintf(
							'.featured-section.style-custom .section-bg .section-bg-img { %s }',
							sprintf('background-size: %s;', $size)
						);
						$styles['home-custom-content-bg-repeat'] = sprintf(
							'.featured-section.style-custom .section-bg .section-bg-img { %s }',
							sprintf('background-repeat: %s;', ($repeat ? 'repeat' : 'no-repeat'))
						);
						$styles['home-custom-content-bg-attachment'] = sprintf(
							'.featured-section.style-custom .section-bg .section-bg-img { %s }',
							sprintf('background-attachment: %s;', ($attachment ? 'scroll' : 'fixed'))
						);
						$styles['home-custom-content-bg-position-x'] = sprintf(
							'.featured-section.style-custom .section-bg .section-bg-img { %s }',
							sprintf('background-position-x: %s;', $position_x)
						);
						$styles['home-custom-content-bg-position-y'] = sprintf(
							'.featured-section.style-custom .section-bg .section-bg-img { %s }',
							sprintf('background-position-y: %s;', $position_y)
						);
					}
				}
			}

			return $styles;
		}
	}
	new Fallsky_Customize_Homepage();
}