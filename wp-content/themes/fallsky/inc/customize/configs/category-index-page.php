<?php
/**
* Customize section category index page configuration files.
*/


if(!class_exists('Fallsky_Customize_Category_Index_Page')){
	class Fallsky_Customize_Category_Index_Page extends Fallsky_Customize_Base {
		public function __construct(){
			parent::__construct();
			$this->includes();
			add_filter('admin_init', 			array($this, 'settings_api_init'));
			add_filter('display_post_states', 	array($this, 'add_display_post_states'), 10, 2);

			add_action('edit_form_after_title', array($this, 'posts_page_notice'));
		}
		private function includes(){
			global $fallsky_is_preview;
			if(!is_admin() || wp_doing_ajax() || $fallsky_is_preview){
				$dir = FALLSKY_THEME_INC . 'customize/frontend-render/';
				require_once $dir . 'class-category-index.php';
				new Fallsky_Category_Index_Page_Render();
			}
		}
		public function posts_page_notice(){
			global $post;
			$page_id = fallsky_get_category_index_page_id();
			if($post && is_admin() && ($post->ID === $page_id)){
				printf(
					'<div class="notice notice-warning inline"><p>%s</p></div>',
					esc_html__('This is the Category Index page. It is a special archive that lists your post categories. Content entered in the editor below will not display on the page.', 'fallsky')
				);
			}
		}
		/**
		* Add a post display state for special WC pages in the page list table.
		*
		* @param array   $post_states An array of post display states.
		* @param WP_Post $post        The current post object.
		*/
		public function add_display_post_states($post_states, $post){
			if(get_option('fallsky_category_index_page_id') == $post->ID){
				$post_states['fallsky_category_index_page_id'] = esc_html__('Category Index Page', 'fallsky');
			}
			return $post_states;
		}
		public function settings_api_init(){
			add_settings_field(
				'fallsky_category_index_page_id',
				esc_html__('Category Index Page', 'fallsky'),
				array($this, 'render_category_index_page_field'),
				'reading'
			);
		 	register_setting('reading', 'fallsky_category_index_page_id');
		}
		public function render_category_index_page_field(){
			$page_id = get_option('fallsky_category_index_page_id');
			wp_dropdown_pages(array(
				'name' 				=> 'fallsky_category_index_page_id', 
				'show_option_none' 	=> esc_html__('&mdash; Select &mdash;', 'fallsky'), 
				'option_none_value' => '0', 
				'selected' 			=> $page_id
			));
			$static_pages = fallsky_get_static_pages();
			if(!empty($page_id) && ('page' == get_option('show_on_front')) && in_array($page_id, $static_pages)){
				printf(
					'<div id="category-index-page-warning" class="error inline"><p>%s</p></div>',
					sprintf(esc_html__('%sWarning:%s the page should not be the same as Homepage/Posts page!', 'fallsky'), '<strong>', '</strong>')
				);
			}
		}
		public function register_controls($wp_customize){
			global $fallsky_default_settings;

			// Sections
			$wp_customize->add_section('fallsky_section_category_index', array(
				'title' 		=> esc_html__('Category Index Page', 'fallsky'),
				'priority'		=> 50,
				'description'	=> sprintf(
					esc_html__('Please %sclick here%s to pick your category index page.', 'fallsky'), 
					'<a href="#" data-control-id="fallsky_category_index_page_id" class="show-control">', 
					'</a>'
				)
			));

			// Settings
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_category_index_sidebar', array(
				'default'   		=> $fallsky_default_settings['fallsky_category_index_sidebar'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_category_index_categories', array(
				'default'   		=> $fallsky_default_settings['fallsky_category_index_categories'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_category_index_style', array(
				'default'   		=> $fallsky_default_settings['fallsky_category_index_style'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_category_index_layout', array(
				'default'	  	 	=> $fallsky_default_settings['fallsky_category_index_layout'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_category_index_show_post_count', array(
				'default'   		=> $fallsky_default_settings['fallsky_category_index_show_post_count'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_checkbox'
			)));

			// Controls
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_category_index_sidebar', array(
				'type'		=> 'radio',
				'label'		=> esc_html__('Sidebar Layout', 'fallsky'),
				'settings'	=> 'fallsky_category_index_sidebar',
				'section'	=> 'fallsky_section_category_index',
				'choices'	=> array(
					''						=> esc_html__('No sidebar', 'fallsky'),
					'with-sidebar-right'	=> esc_html__('Right Sidebar', 'fallsky'),
					'with-sidebar-left'		=> esc_html__('Left Sidebar', 'fallsky')
				)
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_category_index_categories', array(
				'type' 		=> 'select',
				'label' 	=> esc_html__('Choose Categories', 'fallsky'),
				'section' 	=> 'fallsky_section_category_index',
				'settings'	=> 'fallsky_category_index_categories',
				'choices'	=> array(
					'all' => esc_html__('All Categories', 'fallsky'),
					'top' => esc_html__('Top Level Parent Categories', 'fallsky')
				)
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_category_index_style', array(
				'type' 		=> 'select',
				'label' 	=> esc_html__('Category Style', 'fallsky'),
				'section' 	=> 'fallsky_section_category_index',
				'settings' 	=> 'fallsky_category_index_style',
				'choices'	=> array(
					'style-rectangle'	=> esc_html__('Rectangle', 'fallsky'),
					'style-circle'		=> esc_html__('Circle', 'fallsky')
				)
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_category_index_layout', array(
				'type' 		=> 'select',
				'label' 	=> esc_html__('Category Layout', 'fallsky'),
				'section' 	=> 'fallsky_section_category_index',
				'settings' 	=> 'fallsky_category_index_layout',
				'choices'	=> array(
					'column-2'	=> esc_html__('2 Columns', 'fallsky'),
					'column-3'	=> esc_html__('3 Columns', 'fallsky'),
					'column-4'	=> esc_html__('4 Columns', 'fallsky'),
					'column-5'	=> esc_html__('5 Columns', 'fallsky'),
				)
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_category_index_show_post_count', array(
				'type' 			=> 'checkbox',
				'label_first'	=> true,
				'label' 		=> esc_html__('Show Post Counts', 'fallsky'),
				'section' 		=> 'fallsky_section_category_index',
				'settings' 		=> 'fallsky_category_index_show_post_count'
			)));

			$wp_customize->add_setting('fallsky_category_index_page_id', array(
				'default'   		=> 0,
				'type' 				=> 'option',
				'capability' 		=> 'manage_options',
				'sanitize_callback'	=> 'absint'
			) );

			$wp_customize->add_control('fallsky_category_index_page_id', array(
				'label' 			=> esc_html__('Category Index Page', 'fallsky'),
				'section' 			=> 'static_front_page',
				'type' 				=> 'dropdown-pages',
				'allow_addition' 	=> true
			));
		}
		public function customize_js_vars($vars = array()){ 
			$vars['category_index_error_message'] = esc_html__('Category Index Page must be different from Homepage/Posts page .', 'fallsky');
			return $vars; 
		}
	}
	new Fallsky_Customize_Category_Index_Page();
}