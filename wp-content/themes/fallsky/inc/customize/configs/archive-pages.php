<?php
/**
* Customize section archive_pages configuration files.
*/


if(!class_exists('Fallsky_Customize_Archive_Pages')){
	class Fallsky_Customize_Archive_Pages extends Fallsky_Customize_Base {
		public function __construct(){
			parent::__construct();
			$this->includes();
		}
		private function includes(){
			$dir = FALLSKY_THEME_INC . 'customize/frontend-render/';

			require_once $dir . 'class-archive.php';
		}
		public function show_blog_sections(){
			$page_for_posts = get_option('page_for_posts');
			$page_for_front = get_option('page_on_front');
			return ( 'page' == get_option( 'show_on_front' ) ) && !empty( $page_for_posts ) && !empty( $page_for_front );
		}
		public function register_controls($wp_customize){
			global $fallsky_default_settings;

			// Panel
			$wp_customize->add_panel(new WP_Customize_Panel($wp_customize, 'fallsky_panel_archive', array(
				'title' 	=> esc_html__('Archive Pages', 'fallsky'),
				'priority' 	=> 40
			)));

			// Sections
			$wp_customize->add_section('fallsky_archive_category', array(
				'title' 		=> esc_html__('Category', 'fallsky'),
				'panel' 		=> 'fallsky_panel_archive',
				'description'	=> esc_html__('For instant previews while customizing, please go to a category archive page.', 'fallsky')
			));
			$wp_customize->add_section('fallsky_archive_author', array(
				'title' 		=> esc_html__('Author', 'fallsky'),
				'panel' 		=> 'fallsky_panel_archive',
				'description'	=> esc_html__('For instant previews while customizing, please go to an author\'s archive page.', 'fallsky')
			));
			$wp_customize->add_section('fallsky_archive_search', array(
				'title' 		=> esc_html__('Search', 'fallsky'),
				'panel' 		=> 'fallsky_panel_archive',
				'description'	=> esc_html__('For instant previews while customizing, please go to a search results page.', 'fallsky')
			));
			$wp_customize->add_section('fallsky_archive_tag', array(
				'title' 		=> esc_html__('Tag', 'fallsky'),
				'panel' 		=> 'fallsky_panel_archive',
				'description'	=> esc_html__('For instant previews while customizing, please go to a tag archive page.', 'fallsky')
			));
			$wp_customize->add_section('fallsky_archive_date', array(
				'title' 		=> esc_html__('Date based', 'fallsky'),
				'panel'			=> 'fallsky_panel_archive',
				'description'	=> esc_html__('For instant previews while customizing, please go to a date-based archive page.', 'fallsky')
			));
			$wp_customize->add_section('fallsky_archive_post_format', array(
				'title' 		=> esc_html__('Post Format', 'fallsky'),
				'panel'			=> 'fallsky_panel_archive',
				'description'	=> esc_html__('For instant previews while customizing, please go to a post format archive page.', 'fallsky')
			));
			$wp_customize->add_section('fallsky_archive_blog', array(
				'title' 			=> esc_html__('Blog Page', 'fallsky'),
				'panel'				=> 'fallsky_panel_archive',
				'active_callback'	=> array($this, 'show_blog_sections'),
				'description'		=> sprintf(
					esc_html__('The settings below only works for the static Blog page. Please %sclick here%s for instant previews while customizing.', 'fallsky'),
					'<a href="#" class="redirect-preview-url static-home">',
					'</a>'
				)
			));

			// Settings
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_category_show_page_header', array(
				'default'  			=> $fallsky_default_settings['fallsky_category_show_page_header'],
				'transport'			=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_checkbox'
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_category_show_image', array(
				'default'  			=> $fallsky_default_settings['fallsky_category_show_image'],
				'transport'			=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_checkbox',
				'dependency'		=> array(
					'fallsky_category_show_page_header' => array('value' => array('on'))
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_category_show_subcategory_filter', array(
				'default'   		=> $fallsky_default_settings['fallsky_category_show_subcategory_filter'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_checkbox'
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_category_content', array(
				'default' 			=> $fallsky_default_settings['fallsky_category_content'],
				'transport'			=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_category_subcategory_style', array(
				'default'   		=> $fallsky_default_settings['fallsky_category_subcategory_style'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_choice',
				'dependency'		=> array(
					'fallsky_category_content' => array('value' => array('subcategory'))
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_category_subcategory_layout', array(
				'default'	  	 	=> $fallsky_default_settings['fallsky_category_subcategory_layout'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_choice',
				'dependency'		=> array(
					'fallsky_category_content' => array('value' => array('subcategory'))
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_category_subcategory_show_post_count', array(
				'default'   		=> $fallsky_default_settings['fallsky_category_subcategory_show_post_count'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_checkbox',
				'dependency'		=> array(
					'fallsky_category_content' => array('value' => array('subcategory'))
				)
			)));

			// Tag show header image
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_tag_show_page_header', array(
				'default'  			=> $fallsky_default_settings['fallsky_tag_show_page_header'],
				'transport'			=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_checkbox'
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_tag_show_image', array(
				'default'  			=> $fallsky_default_settings['fallsky_tag_show_image'],
				'transport'			=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_checkbox',
				'dependency'		=> array(
					'fallsky_tag_show_page_header' => array('value' => array('on'))
				)
			)));

			// Controls
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_category_show_page_header', array(
				'type' 			=> 'checkbox',
				'label_first'	=> true,
				'label' 		=> esc_html__('Show page header', 'fallsky'),
				'section' 		=> 'fallsky_archive_category',
				'settings'	 	=> 'fallsky_category_show_page_header'
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_category_show_image', array(
				'type' 				=> 'checkbox',
				'label_first'		=> true,
				'label' 			=> esc_html__('Display category image in page header', 'fallsky'),
				'section' 			=> 'fallsky_archive_category',
				'settings'	 		=> 'fallsky_category_show_image',
				'active_callback'	=> 'fallsky_customize_control_active_cb'
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_category_show_subcategory_filter', array(
				'type' 			=> 'checkbox',
				'label_first'	=> true,
				'label' 		=> esc_html__('Display subcategories filter', 'fallsky'),
				'section' 		=> 'fallsky_archive_category',
				'settings' 		=> 'fallsky_category_show_subcategory_filter'
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_category_content', array(
				'type' 		=> 'select',
				'label' 	=> esc_html__('Show Posts or Sub Categories on Parent Category Page', 'fallsky'),
				'priority'	=> 20,
				'section' 	=> 'fallsky_archive_category',
				'settings' 	=> 'fallsky_category_content',
				'choices' 	=> array(
					'posts'			=> esc_html__('Posts', 'fallsky'),
					'subcategory'	=> esc_html__('Sub Categories', 'fallsky')
				)
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_category_subcategory_style', array(
				'type' 				=> 'select',
				'label' 			=> esc_html__('Sub Categories Style', 'fallsky'),
				'priority'			=> 20,
				'section' 			=> 'fallsky_archive_category',
				'settings' 			=> 'fallsky_category_subcategory_style',
				'active_callback' 	=> 'fallsky_customize_control_active_cb',
				'choices'			=> array(
					'style-rectangle'	=> esc_html__('Rectangle', 'fallsky'),
					'style-circle'		=> esc_html__('Circle', 'fallsky')
				)
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_category_subcategory_layout', array(
				'type' 				=> 'select',
				'label' 			=> esc_html__('Sub Categories Layout', 'fallsky'),
				'priority'			=> 20,
				'section' 			=> 'fallsky_archive_category',
				'settings' 			=> 'fallsky_category_subcategory_layout',
				'active_callback' 	=> 'fallsky_customize_control_active_cb',
				'choices'			=> array(
					'column-2'	=> esc_html__('2 Columns', 'fallsky'),
					'column-3'	=> esc_html__('3 Columns', 'fallsky'),
					'column-4'	=> esc_html__('4 Columns', 'fallsky'),
					'column-5'	=> esc_html__('5 Columns', 'fallsky'),
				)
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_category_subcategory_show_post_count', array(
				'type' 				=> 'checkbox',
				'label_first'		=> true,
				'label' 			=> esc_html__('Show Post Counts', 'fallsky'),
				'priority'			=> 20,
				'section' 			=> 'fallsky_archive_category',
				'settings' 			=> 'fallsky_category_subcategory_show_post_count',
				'active_callback' 	=> 'fallsky_customize_control_active_cb'
			)));

			// Tag show header image
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_tag_show_page_header', array(
				'type' 			=> 'checkbox',
				'label_first'	=> true,
				'label' 		=> esc_html__('Show page header', 'fallsky'),
				'section' 		=> 'fallsky_archive_tag',
				'settings'	 	=> 'fallsky_tag_show_page_header'
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_tag_show_image', array(
				'type' 				=> 'checkbox',
				'label_first'		=> true,
				'label' 			=> esc_html__('Display tag image in page header', 'fallsky'),
				'section' 			=> 'fallsky_archive_tag',
				'settings'	 		=> 'fallsky_tag_show_image',
				'active_callback'	=> 'fallsky_customize_control_active_cb'
			)));

			$sections = array('category', 'author', 'search', 'tag', 'date', 'post_format', 'blog');
			foreach($sections as $sid){
				// Settings
				$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_' . $sid . '_sidebar', array(
					'default'   		=> $fallsky_default_settings['fallsky_' . $sid . '_sidebar'],
					'transport' 		=> 'postMessage',
					'sanitize_callback' => 'fallsky_sanitize_choice'
				)));
				$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_' . $sid . '_posts_layout', array(
					'default'   		=> $fallsky_default_settings['fallsky_' . $sid . '_posts_layout'],
					'transport' 		=> 'refresh',
					'sanitize_callback' => 'fallsky_sanitize_choice'
				)));		
				$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_' . $sid . '_column_list', array(
					'default'   		=> $fallsky_default_settings['fallsky_' . $sid . '_column_list'],
					'transport'			=> 'refresh',
					'sanitize_callback' => 'absint',
					'dependency'		=> array(
						'fallsky_' . $sid . '_posts_layout' => array('value' => array('list'))
					)
				)));		
				$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_' . $sid . '_column_masonry', array(
					'default'   		=> $fallsky_default_settings['fallsky_' . $sid . '_column_masonry'],
					'transport'			=> 'refresh',
					'sanitize_callback' => 'absint',
					'dependency'		=> array(
						'fallsky_' . $sid . '_posts_layout' => array('value' => array('masonry'))
					)
				)));		
				$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_' . $sid . '_column_card', array(
					'default'   		=> $fallsky_default_settings['fallsky_' . $sid . '_column_card'],
					'transport'			=> 'refresh',
					'sanitize_callback' => 'absint',
					'dependency'		=> array(
						'fallsky_' . $sid . '_posts_layout' => array('value' => array('card'))
					)
				)));			
				$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_' . $sid . '_card_color', array(
					'default'   		=> $fallsky_default_settings['fallsky_' . $sid . '_card_color'],
					'transport'			=> 'refresh',
					'sanitize_callback' => 'fallsky_sanitize_choice',
					'dependency'		=> array(
						'fallsky_' . $sid . '_posts_layout' => array('value' => array('card'))
					)
				)));	
				$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_' . $sid . '_column_grid', array(
					'default'   		=> $fallsky_default_settings['fallsky_' . $sid . '_column_grid'],
					'transport'			=> 'refresh',
					'sanitize_callback' => 'absint',
					'dependency'		=> array(
						'fallsky_' . $sid . '_posts_layout' => array('value' => array('grid'))
					)
				)));		
				$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_' . $sid . '_column_overlay', array(
					'default'   		=> $fallsky_default_settings['fallsky_' . $sid . '_column_overlay'],
					'transport'			=> 'refresh',
					'sanitize_callback' => 'absint',
					'dependency'		=> array(
						'fallsky_' . $sid . '_posts_layout' => array('value' => array('overlay'))
					)
				)));		
				$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_' . $sid . '_column_overlay_mix', array(
					'default'   		=> $fallsky_default_settings['fallsky_' . $sid . '_column_overlay_mix'],
					'transport'			=> 'refresh',
					'sanitize_callback' => 'fallsky_sanitize_choice',
					'dependency'		=> array(
						'fallsky_' . $sid . '_posts_layout' => array('value' => array('overlay-mix'))
					)
				)));
				$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_' . $sid . '_image_orientation', array(
					'default'   		=> $fallsky_default_settings['fallsky_' . $sid . '_image_orientation'],
					'transport'			=> 'refresh',
					'sanitize_callback' => 'fallsky_sanitize_choice',
					'dependency'		=> array(
						'fallsky_' . $sid . '_posts_layout' => array( 'value' => array( 'grid') )
					)
				))); 
				$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_' . $sid . '_show_post_meta_excerpt', array(
					'default'   		=> $fallsky_default_settings['fallsky_' . $sid . '_show_post_meta_excerpt'],
					'transport'			=> 'refresh',
					'sanitize_callback' => 'fallsky_sanitize_checkbox',
					'dependency'		=> array(
						'fallsky_' . $sid . '_posts_layout' => array('value' => array('masonry', 'list', 'zigzag', 'grid', 'card'))
					)
				)));
				$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_' . $sid . '_show_read_more_btn', array(
					'default'   		=> $fallsky_default_settings['fallsky_' . $sid . '_show_read_more_btn'],
					'transport'			=> 'refresh',
					'sanitize_callback' => 'fallsky_sanitize_checkbox',
					'dependency'		=> array(
						'fallsky_' . $sid . '_posts_layout' => array('value' => array('masonry', 'zigzag', 'card'))
					)
				)));
				$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_' . $sid . '_masonry_center_text', array(
					'default'   		=> $fallsky_default_settings['fallsky_' . $sid . '_masonry_center_text'],
					'transport'			=> 'refresh',
					'sanitize_callback' => 'fallsky_sanitize_checkbox',
					'dependency'		=> array(
						'fallsky_' . $sid . '_posts_layout' => array( 'value' => array( 'masonry', 'grid' ) )
					)
				)));
				$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_' . $sid . '_post_meta', array(
					'default'   		=> '',
					'transport'			=> 'postMessage',
					'sanitize_callback' => 'fallsky_sanitize_empty'
				)));
				$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_' . $sid . '_show_post_meta_category', array(
					'default'   		=> $fallsky_default_settings['fallsky_' . $sid . '_show_post_meta_category'],
					'transport'			=> 'refresh',
					'sanitize_callback' => 'fallsky_sanitize_checkbox'
				)));
				$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_' . $sid . '_show_post_meta_author', array(
					'default'   		=> $fallsky_default_settings['fallsky_' . $sid . '_show_post_meta_author'],
					'transport'			=> 'refresh',
					'sanitize_callback' => 'fallsky_sanitize_checkbox'
				)));
				$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_' . $sid . '_show_post_meta_date', array(
					'default'   		=> $fallsky_default_settings['fallsky_' . $sid . '_show_post_meta_date'],
					'transport'			=> 'refresh',
					'sanitize_callback' => 'fallsky_sanitize_checkbox'
				)));
				$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_' . $sid . '_show_post_meta_view', array(
					'default'   		=> $fallsky_default_settings['fallsky_' . $sid . '_show_post_meta_view'],
					'transport'			=> 'refresh',
					'sanitize_callback' => 'fallsky_sanitize_checkbox'
				)));
				$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_' . $sid . '_show_post_meta_like', array(
					'default'   		=> $fallsky_default_settings['fallsky_' . $sid . '_show_post_meta_like'],
					'transport'			=> 'refresh',
					'sanitize_callback' => 'fallsky_sanitize_checkbox'
				)));
				$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_' . $sid . '_show_post_meta_comment', array(
					'default'   		=> $fallsky_default_settings['fallsky_' . $sid . '_show_post_meta_comment'],
					'transport'			=> 'refresh',
					'sanitize_callback' => 'fallsky_sanitize_checkbox'
				)));
				$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_' . $sid . '_posts_per_page', array(
					'default'   		=> $fallsky_default_settings['fallsky_' . $sid . '_posts_per_page'],
					'transport' 		=> 'refresh',
					'sanitize_callback' => 'absint'
				)));

				//Controls
				$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_' . $sid . '_sidebar', array(
					'type'		=> 'radio',
					'label'		=> esc_html__('Sidebar Layout', 'fallsky'),
					'settings'	=> 'fallsky_' . $sid . '_sidebar',
					'section'	=> 'fallsky_archive_' . $sid,
					'choices'	=> array(
						''						=> esc_html__('No sidebar', 'fallsky'),
						'with-sidebar-right'	=> esc_html__('Right Sidebar', 'fallsky'),
						'with-sidebar-left'		=> esc_html__('Left Sidebar', 'fallsky')
					)
				)));
				$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_' . $sid . '_posts_layout', array(
					'type'		=> 'select',
					'label'		=> esc_html__('Posts Layout', 'fallsky'),
					'settings'	=> 'fallsky_' . $sid . '_posts_layout',
					'section'	=> 'fallsky_archive_' . $sid,
					'choices'	=> array(
						'masonry'		=> esc_html__('Masonry', 'fallsky'),
						'list'			=> esc_html__('List', 'fallsky'),
						'zigzag'		=> esc_html__('ZigZag', 'fallsky'),
						'grid'			=> esc_html__('Grid', 'fallsky'),
						'card'			=> esc_html__('Card', 'fallsky'),
						'overlay'		=> esc_html__('Overlay', 'fallsky'),
						'overlay-mix'	=> esc_html__('Overlay Mix', 'fallsky')
					)
				)));
				$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_' . $sid . '_column_list', array(
					'type'				=> 'select',
					'label'				=> esc_html__('Columns', 'fallsky'),
					'settings'			=> 'fallsky_' . $sid . '_column_list',
					'section'			=> 'fallsky_archive_' . $sid,
					'active_callback' 	=> 'fallsky_customize_control_active_cb',
					'choices'			=> array(
						'1'	=> esc_html__('1', 'fallsky'),
						'2'	=> esc_html__('2', 'fallsky')
					)
				)));
				$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_' . $sid . '_column_masonry', array(
					'type'				=> 'select',
					'label'				=> esc_html__('Columns', 'fallsky'),
					'settings'			=> 'fallsky_' . $sid . '_column_masonry',
					'section'			=> 'fallsky_archive_' . $sid,
					'active_callback' 	=> 'fallsky_customize_control_active_cb',
					'choices'			=> array(
						'2'	=> esc_html__('2', 'fallsky'),
						'3'	=> esc_html__('3', 'fallsky')
					)
				)));
				$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_' . $sid . '_column_card', array(
					'type'				=> 'select',
					'label'				=> esc_html__('Columns', 'fallsky'),
					'settings'			=> 'fallsky_' . $sid . '_column_card',
					'section'			=> 'fallsky_archive_' . $sid,
					'active_callback' 	=> 'fallsky_customize_control_active_cb',
					'choices'			=> array(
						'1'	=> esc_html__('1', 'fallsky'),
						'2'	=> esc_html__('2', 'fallsky'),
						'3'	=> esc_html__('3', 'fallsky'),
						'4'	=> esc_html__('4', 'fallsky')
					)
				)));
				$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_' . $sid . '_card_color', array(
					'type'				=> 'select',
					'label'				=> esc_html__('Card Color', 'fallsky'),
					'settings'			=> 'fallsky_' . $sid . '_card_color',
					'section'			=> 'fallsky_archive_' . $sid,
					'active_callback' 	=> 'fallsky_customize_control_active_cb',
					'choices'			=> array(
						'light-card'	=> esc_html__('Light', 'fallsky'),
						'dark-card'		=> esc_html__('Dark', 'fallsky')
					)
				)));
				$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_' . $sid . '_column_grid', array(
					'type'				=> 'select',
					'label'				=> esc_html__('Columns', 'fallsky'),
					'settings'			=> 'fallsky_' . $sid . '_column_grid',
					'section'			=> 'fallsky_archive_' . $sid,
					'active_callback' 	=> 'fallsky_customize_control_active_cb',
					'choices'			=> array(
						'2'	=> esc_html__('2', 'fallsky'),
						'3'	=> esc_html__('3', 'fallsky'),
						'4'	=> esc_html__('4', 'fallsky')
					)
				)));
				$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_' . $sid . '_column_overlay', array(
					'type'				=> 'select',
					'label'				=> esc_html__('Columns', 'fallsky'),
					'settings'			=> 'fallsky_' . $sid . '_column_overlay',
					'section'			=> 'fallsky_archive_' . $sid,
					'active_callback' 	=> 'fallsky_customize_control_active_cb',
					'choices'			=> array(
						'1'	=> esc_html__('1', 'fallsky'),
						'2'	=> esc_html__('2', 'fallsky'),
						'3'	=> esc_html__('3', 'fallsky'),
						'4'	=> esc_html__('4', 'fallsky')
					)
				)));
				$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_' . $sid . '_column_overlay_mix', array(
					'type'				=> 'select',
					'label'				=> esc_html__('Columns', 'fallsky'),
					'settings'			=> 'fallsky_' . $sid . '_column_overlay_mix',
					'section'			=> 'fallsky_archive_' . $sid,
					'active_callback' 	=> 'fallsky_customize_control_active_cb',
					'choices'			=> array(
						'1-2-mix'	=> esc_html__('1+2', 'fallsky'),
						'1-4-mix'	=> esc_html__('1+4', 'fallsky'),
						'2-3-mix'	=> esc_html__('2+3', 'fallsky'),
						'1-2-2-mix'	=> esc_html__('1+2+2', 'fallsky')
					)
				)));
				$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_' . $sid . '_image_orientation', array(
					'type'				=> 'select',
					'label'				=> esc_html__( 'Featured Image Orientation', 'fallsky' ),
					'settings'			=> 'fallsky_' . $sid . '_image_orientation',
					'section'			=> 'fallsky_archive_' . $sid,
					'active_callback' 	=> 'fallsky_customize_control_active_cb',
					'choices'			=> array(
						'' 				=> esc_html__( 'Landscape', 'fallsky' ),
						'img-square' 	=> esc_html__( 'Square', 'fallsky' ),
						'img-portrait'	=> esc_html__( 'Portrait', 'fallsky' )
					)
				) ) );
				$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_' . $sid . '_show_post_meta_excerpt', array(
					'type' 				=> 'checkbox',
					'label_first'		=> true,
					'label' 			=> esc_html__('Show post excerpt', 'fallsky'),
					'section' 			=> 'fallsky_archive_' . $sid,
					'settings' 			=> 'fallsky_' . $sid . '_show_post_meta_excerpt',
					'active_callback'	=> 'fallsky_customize_control_active_cb'
				)));
				$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_' . $sid . '_show_read_more_btn', array(
					'type' 				=> 'checkbox',
					'label_first'		=> true,
					'label' 			=> esc_html__('Show Read More button', 'fallsky'),
					'section' 			=> 'fallsky_archive_' . $sid,
					'settings' 			=> 'fallsky_' . $sid . '_show_read_more_btn',
					'active_callback'	=> 'fallsky_customize_control_active_cb'
				)));
				$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_' . $sid . '_masonry_center_text', array(
					'type' 				=> 'checkbox',
					'label_first'		=> true,
					'label' 			=> esc_html__('Center text', 'fallsky'),
					'section' 			=> 'fallsky_archive_' . $sid,
					'settings' 			=> 'fallsky_' . $sid . '_masonry_center_text',
					'active_callback'	=> 'fallsky_customize_control_active_cb'
				)));
				$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_' . $sid . '_page_meta', array(
					'type' 				=> 'multiple_checkbox',
					'label' 			=> esc_html__('Display Selected Post Meta:', 'fallsky'),
					'description_below' => true,
					'section' 			=> 'fallsky_archive_' . $sid,
					'settings' 			=> 'fallsky_' . $sid . '_post_meta',
					'choices' 			=> array( 
						'category' => array(
							'value' 	=> 'on',
							'label' 	=> esc_html__('Category', 'fallsky'),
							'setting' 	=> 'fallsky_' . $sid . '_show_post_meta_category',
						),
						'author' => array(
							'value' 	=> 'on',
							'label' 	=> esc_html__('Author', 'fallsky'),
							'setting' 	=> 'fallsky_' . $sid . '_show_post_meta_author',
						),
						'date' => array(
							'value' 	=> 'on',
							'label' 	=> esc_html__('Publish Date', 'fallsky'),
							'setting' 	=> 'fallsky_' . $sid . '_show_post_meta_date',
						),
						'view' => array(
							'value' 	=> 'on',
							'label' 	=> esc_html__('View Count', 'fallsky'),
							'setting' 	=> 'fallsky_' . $sid . '_show_post_meta_view',
						),
						'like' => array(
							'value' 	=> 'on',
							'label' 	=> esc_html__('Like Count', 'fallsky'),
							'setting' 	=> 'fallsky_' . $sid . '_show_post_meta_like',
						),
						'comment' => array(
							'value' 	=> 'on',
							'label' 	=> esc_html__('Comment Counts', 'fallsky'),
							'setting' 	=> 'fallsky_' . $sid . '_show_post_meta_comment',
						)
					),
				)));
				$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_' . $sid . '_posts_per_page', array(
					'type' 			=> 'number',
					'label' 		=> esc_html__('Number of Posts displayed Per Page', 'fallsky'),
					'input_attrs' 	=> array('min' => 1),
					'section' 		=> 'fallsky_archive_' . $sid,
					'settings' 		=> 'fallsky_' . $sid . '_posts_per_page'
				)));
			}
		}
	}
	new Fallsky_Customize_Archive_Pages();
}