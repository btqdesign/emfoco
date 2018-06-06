<?php
/**
* Main class for frontend 
*/

if(!class_exists('Fallsky_Frontend')){
	class Fallsky_Frontend {
		public function __construct(){
			add_action('wp', 								array($this, 'wp'));
			add_action('fallsky_global', 					array($this, 'globals'));

			add_filter('loftocean_instagram_widget_class', 	function(){ return 'fallsky-widget_instagram'; });
			add_filter('loftocean_instagram_widget_name',	function(){ return esc_html__('Fallsky Instagram', 'fallsky'); });
			add_filter('loftocean_facebook_widget_name', 	function(){ return esc_html__('Fallsky Facebook', 'fallsky'); });
			add_filter('loftocean_author_widget_name', 		function(){ return esc_html__('Fallsky Author List', 'fallsky'); });
			add_filter('loftocean_author_widget_class', 	function(){ return 'fallsky-widget_author_list'; });
			add_filter('comment_post_redirect', 			array($this, 'comment_post_redirect'), 10, 2);
			add_filter('get_comments_pagenum_link', 		array($this, 'comment_page_link'));
		}
		public function wp(){
			do_action('fallsky_global');

			$this->includes();

			add_filter('body_class', 						array($this, 'body_class'), 999);
			add_filter('fallsky_frontend_js_vars', 			array($this, 'frontend_js_vars'));
			add_filter('fallsky_custom_styles', 			array($this, 'custom_styles'), 1);

			add_action('wp_enqueue_scripts', 	array($this, 'enqueue_scripts'), 1);
			add_action('wp_enqueue_scripts', 	array($this, 'enqueue_custom_styles'), 1000);
			add_action( 'wp_footer', 			array( $this, 'output_fallback_styles' ) );
			add_action('fallsky_site_header_main_elements', array($this, 'site_header_main_elements'), 10);
		}
		/**
		* @description define global variables
		*/
		public function globals(){
			global $fallsky_is_preview, $fallsky_archive_type;
			$fallsky_is_preview 	= is_customize_preview();
			$fallsky_archive_type 	= fallsky_is_front_page() ? 'home' 
				: ( is_home() ? 'blog' 
					: ( is_search() ? 'search' 
						: ( is_tag() ? 'tag' 
							: ( is_category() ? 'category' 
								: ( is_author() ? 'author' 
									: ( is_date() ? 'date' 
										: ( is_tax( 'post_format' ) ? 'post_format' 
											: false ) ) ) ) ) ) );
		}
		/**
		* @description include required files
		*/
		private function includes(){
			$inc_dir = FALLSKY_THEME_INC;
			require_once $inc_dir . 'class-walker-menu.php';

			if(is_attachment()){
				require_once $inc_dir . 'customize/frontend-render/class-attachment.php';
			}
			else{
				require_once $inc_dir . 'customize/frontend-render/class-page.php';
			}
		}
		/** 
		* @description add js variables for frontend
		* @param array current variable list
		* @return array variable list
		*/
		public function frontend_js_vars($vars){
			$vars['error_text'] = array(
				'no_list_found' 	=> esc_js(esc_html__('No post list found.', 'fallsky')),
				'no_media_found'	=> esc_js(esc_html__('No image found', 'fallsky')),
				'no_widget_found'	=> esc_js(esc_html__('No Widget found', 'fallsky'))
			);

			return $vars;
		}
		/**
		* Generate the css variable custom style
		*/
		public function custom_styles($styles){
			$css_vars = apply_filters('fallsky_get_css_variables', array());
			if(!empty($css_vars) && is_array($css_vars)){
				$items = array();
				foreach($css_vars as $var => $val){
					if(!empty($var) && !empty($val)){
						$items[] = sprintf('%s: %s;', $var, $val);
					}
				}
				$styles .= sprintf(':root { %s } ', implode(' ', $items));
			}
			return $styles;
		}
		/**
		* @description enqueue script for frontend
		*/
		public function enqueue_scripts(){
			$theme_uri		= FALLSKY_THEME_URI;
			$asset_uri		= FALLSKY_ASSETS_URI;
			$asset_version 	= FALLSKY_ASSETS_VERSION;
			$load_video_api = false;
			$theme_js_deps 	= array('jquery', 'slick');

			$theme_style_deps = apply_filters('fallsky_theme_css_deps', array());
			wp_enqueue_style('fallsky-theme-style', $theme_uri . 'style.css', $theme_style_deps, $asset_version);
			wp_enqueue_style('awsome-font', 		$asset_uri . 'fonts/font-awesome/css/font-awesome.min.css');
			wp_enqueue_style('elegant-font', 		$asset_uri . 'fonts/elegant-font/style.css');
			wp_enqueue_style('slick',				$asset_uri . 'libs/slick/slick.css', array(), '1.6.0'); 

			if(is_singular(array('post'))){
				$pid = get_the_ID();
				$has_background_video = apply_filters('loftocean_has_background_video', false, $pid);
				if($has_background_video){
					if(apply_filters('loftocean_has_vimeo_bg_video', false, $pid)){
						wp_enqueue_script('vimeo-api', 'https://player.vimeo.com/api/player.js');
					}
					wp_enqueue_script('fallsky-single-video', $asset_uri . 'js/frontend/fallsky-single-post-video.js', array('jquery'));
					$theme_js_deps = array_merge($theme_js_deps, array('fallsky-single-video'));
				}
			}

			wp_enqueue_script('slick', 				$asset_uri . 'libs/slick/slick.min.js', 		array('jquery'), '1.8.0', true);
			wp_enqueue_script('modernizr', 			$asset_uri . 'js/libs/modernizr.min.js', 		array(), '3.3.1');
			wp_enqueue_script('html5shiv', 			$asset_uri . 'js/libs/html5shiv.min.js', 		array(), '3.7.3');
			wp_script_add_data('html5shiv', 		'conditional', 'lt IE 9');	

			if(is_singular()){
				array_push($theme_js_deps, 'justified-gallery');
				array_push($theme_js_deps, 'jquery-fitvids');
				wp_enqueue_script('jquery-fitvids', 	$asset_uri . 'js/libs/jquery.fitvids.min.js', array('jquery'), '1.1', true);
				wp_enqueue_script('justified-gallery', 	$asset_uri . 'libs/justified-gallery/jquery.justifiedGallery.min.js', array('jquery'), '3.6.5', true);
				wp_enqueue_style('justified-gallery',	$asset_uri . 'libs/justified-gallery/justifiedGallery.min.css', array(), '3.6.3');
				if(comments_open()){
					wp_enqueue_script('comment-reply');
				}
			}

			wp_register_script('fallsky-bg-video', $asset_uri . 'js/frontend/fallsky-renderVideo.min.js', array('jquery'), $asset_version, true);
			wp_register_script('fallsky-theme-script', $asset_uri . 'js/frontend/fallsky-main.js', $theme_js_deps, $asset_version, true);
			wp_localize_script('fallsky-theme-script', 'fallsky', apply_filters('fallsky_frontend_js_vars', array()));
			wp_enqueue_script('fallsky-theme-script');
		}
		/**
		* @description enqueue styles generated from customization
		*/
		public function enqueue_custom_styles(){
			global $fallsky_is_preview;
			$custom_css = $fallsky_is_preview ? $this->custom_styles('') :  apply_filters('fallsky_custom_styles', '');
			// Add customizer related custom styles. Only print css variables if in customize preview iframe currently.
			$custom_css = trim($custom_css); 
			if(!empty($custom_css)){
				$default_dep = 'fallsky-theme-style';
 				wp_add_inline_style(
					apply_filters('fallsky_inline_style_handler', $default_dep), 
					$custom_css
				);
			}
		}
		/**
		* @description add extra class name to <body>
		* @param array class name list
		* @return array class name list
		*/
		public function body_class($class){
			if(apply_filters('fallsky_is_page_header_with_bg', false)){
				array_push($class, (is_singular('post') ? 'post-header-with-bg' : 'page-header-with-bg'));
			}
			if(fallsky_is_front_page()){
				$remove = array('archive', 'post-type-archive', 'post-type-archive-post');
				$class = array_diff($class, $remove);
				$class = array_merge($class, array('front-page', 'home'));
			}
			if(has_nav_menu('secondary')){
				array_push($class, 'has-secondary-menu');
			}
			return $class;
		}
		/**
		* @description add elements to site header main area
		*	1. Site branding
		*/
		public function site_header_main_elements(){
			fallsky_site_branding();
		}
		/**
		* Redirect to previous post with comment anchor
		*/
		public function comment_post_redirect($location, $comment){
			if(!empty($_REQUEST['comment_post_ID']) && (false !== get_post_status($_REQUEST['comment_post_ID']))){
				return sprintf('%s#comment-section', get_permalink($_REQUEST['comment_post_ID']), $comment->comment_ID);
			}
			return $location;
		}
		/**
		* Change the url fragment for comments
		*/
		public function comment_page_link($result){
			$result = str_replace('#comments', '#comment-section', $result);
			return preg_match('/(cpage=\d|comment-page-\d)/i', $result) ? $result : add_query_arg('no_custom', 1, $result);
		}
		/**
		* Output fallback custom styles for browsers which not support css variable
		*/
		public function output_fallback_styles() {
			global $fallsky_is_preview;
			$asset_uri		= FALLSKY_ASSETS_URI;
			$asset_version 	= FALLSKY_ASSETS_VERSION;
			$styles = sprintf(
				'%s%s%s',
				sprintf(
					'<link rel="stylesheet" id="fallsky-theme-fallback-style" href="%s?ver=%s" type="text/css" media="all">',
					$asset_uri . 'css/frontend/fallsky-theme-fallback.css', 
					$asset_version
				),
				apply_filters( 'fallsky_fallback_styles', ''),
				sprintf(
					'<style id="fallsky-theme-fallback-custom-style" type="text/css">%s</div>',
					$fallsky_is_preview ? apply_filters('fallsky_get_fallback_css', '') : fallsky_get_theme_mod('fallsky_fallback_customize_css')
				)
			);

			printf(
				'<script type="text/html" id="fallsky-tmpl-fallback-styles" data-dependency="%s">%s</script>',
				apply_filters( 'fallsky_inline_style_handler', 'fallsky-theme-style' ),
				$styles
			);
		}
	}
	new Fallsky_Frontend();
}
