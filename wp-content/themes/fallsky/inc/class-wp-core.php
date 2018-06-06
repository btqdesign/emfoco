<?php
/**
* Load the wp core features/supports/widgets/menus
*/

if(!class_exists('Fallsky_WP_Core')){
	class Fallsky_WP_Core{
		public function __construct(){
			add_action('after_setup_theme', 	array($this, 'wp_core'));
			add_action('after_setup_theme', 	array($this, 'register_menus'));
			add_action('after_setup_theme', 	array($this, 'image_sizes'));
			add_action('widgets_init', 			array($this, 'register_sidebars'));
			add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));

			add_filter('theme_page_templates', 	array($this, 'exclude_page_templates'), 1000, 4);
			add_filter('fallsky_post_formats', 	array($this, 'post_formats'));
			add_filter('post_class',			array($this, 'post_class'), 9999);
		}
		// Add WP core supports and content width
		public function wp_core(){
			// set default content width to 1200px
			$GLOBALS['content_width'] = 1200;
			// Add default posts and comments RSS feed links to head.
			add_theme_support('automatic-feed-links');

			// Let WordPress manage the document title.
			add_theme_support('title-tag');

			// Enable support for Post Thumbnails on posts and pages.
			add_theme_support('post-thumbnails');
			set_post_thumbnail_size(1200, 9999);

			// Set up the WordPress core custom background feature.
			add_theme_support('custom-background', array(
				'default-color' 	=> 'F6F6F6',
				'wp-head-callback' 	=> '_custom_background_cb'
			));

			// Enable support for custom header
			add_theme_support('custom-header', apply_filters('fallsky_custom_header_args', array(
				'default-image' => '',
				'width' 		=> 1920,
				'height' 		=> 300,
				'flex-width' 	=> true,
				'flex-height' 	=> true
			)));

			/*
			 * Switch default core markup for search form, comment form, and comments
			 * to output valid HTML5.
			 */
			add_theme_support('html5', array(
				'comment-form',
				'comment-list',
				'gallery',
				'caption'
			));

			/*
			 * Enable support for custom logo.
			 */
			add_theme_support('custom-logo', array(
				'height'      => 80,
				'width'       => 240,
				'flex-height' => true,
				'flex-width'  => true
			));

			/*
			 * Enable support for Post Formats.
			 */
			add_theme_support('post-formats', apply_filters('fallsky_post_formats', array()));

			// Indicate widget sidebars can use selective refresh in the Customizer.
			add_theme_support('customize-selective-refresh-widgets');

			/*
			 * This theme styles the visual editor to resemble the theme style, specifically font, colors, icons, and column width.
			 */
			add_editor_style('style.css');
		}
		// Register custom image size
		public function image_sizes(){
			add_image_size('fallsky_large', 		1920, 9999, false);
			add_image_size('fallsky_medium_large', 	1440, 9999, false);
			add_image_size('fallsky_medium', 		1200, 9999, false);
			add_image_size('fallsky_small_medium', 	768, 9999, false);
			add_image_size('fallsky_small', 		600, 9999, false);
		}
		/**
		* Enqueue admin scripts
		*/
		public function admin_enqueue_scripts(){
			$version 	= FALLSKY_ASSETS_VERSION;
			$script_uri = FALLSKY_ASSETS_URI . 'js/admin/fallsky-functions.min.js';
			wp_enqueue_media();
			wp_enqueue_script('fallsky-functions-lib', $script_uri, array('jquery'), $version, true);
		}
		// Register menus
		public function register_menus(){
			// This theme uses wp_nav_menu() in two locations.
			register_nav_menus(array(
				'primary' 	=> esc_html__('Primary Menu', 'fallsky'),
				'secondary' => esc_html__('Secondary Menu', 'fallsky'),
				'social' 	=> esc_html__('Social Menu', 'fallsky')
			));
		}
		// Register sidebars
		public function register_sidebars(){
			register_sidebar(array(
				'name'          => esc_html__('Main Sidebar', 'fallsky'),
				'id'            => 'main-sidebar',
				'description'   => esc_html__('Add widgets here to appear in your main sidebar.', 'fallsky'),
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h5 class="widget-title">',
				'after_title'   => '</h5>'
			));
			register_sidebar(array(
				'name' 			=> esc_html__('Footer Column 1', 'fallsky'),
				'id' 			=> 'footer-column-1',
				'description'   => '',
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h5 class="widget-title">',
				'after_title'   => '</h5>'
			));
			register_sidebar(array(
				'name' 			=> esc_html__('Footer Column 2', 'fallsky'),
				'id' 			=> 'footer-column-2',
				'description'   => '',
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h5 class="widget-title">',
				'after_title'   => '</h5>'
			));
			register_sidebar(array(
				'name' 			=> esc_html__('Footer Column 3', 'fallsky'),
				'id' 			=> 'footer-column-3',
				'description'   => '',
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h5 class="widget-title">',
				'after_title'   => '</h5>'
			));
		}
		/**
		* Hides the custom post template for pages on WordPress 4.6 and older
		*
		* @param array $post_templates Array of page templates. Keys are filenames, values are translated names.
		* @param object theme class object
		* @param object post class object
		* @param string post type
		* @return array Filtered array of page templates.
		*/
		public function exclude_page_templates($post_templates, $theme, $post, $post_type){
			$front_page_id 	= fallsky_get_page_on_front();
			$pages = array(get_option('fallsky_category_index_page_id'));
			if(!empty($front_page_id)){
				array_push($pages, $front_page_id);
			}
			$pages = array_filter($pages);
			if($post && !empty($pages) && in_array($post->ID, $pages)){
				$post_templates = array();
			}
			return $post_templates;
		}
		/***
		* Post formats supported by this theme
		*/
		public function post_formats($format = array()){
			return array_merge($format, array(
				'standard',
				'video',
				'gallery',
				'audio'
			));
		}
		/**
		* Remove hentry class name from post class
		* @param array 
		* @return array
		*/
		public function post_class($class){
			wp_doing_ajax() ? array_push($class, 'list-post') : '';
			post_password_required() && has_post_thumbnail() ? array_push($class, 'has-post-thumbnail') : '';
			return array_diff($class, array('hentry', 'h-entry'));
		}
	}
	new Fallsky_WP_Core();
}
