<?php
/**
* Theme customize manager class 
*	and theme own customize section/controls/sanitize;
*/

if(!class_exists('Fallsky_Customize_Manager')){
	class Fallsky_Customize_Manager {	
		// Customize root dir
		private $dir = '';
		// Assets version
		private $assets_version = '';
		// Assets root uri;
		private $assets_uri = '';

		function __construct(){
			$this->dir 				= FALLSKY_THEME_INC . 'customize/';
			$this->assets_uri 		= FALLSKY_ASSETS_URI;
			$this->assets_version 	= FALLSKY_ASSETS_VERSION;

			add_action( 'customize_controls_enqueue_scripts', 		array( $this, 'customize_scripts' ), 9999 );
			add_action( 'customize_preview_init',					array( $this, 'preview_scripts' ) );
			add_action( 'customize_controls_print_footer_scripts', 	array( $this, 'footer_scripts' ), 9999 );
			add_action( 'customize_save_after',						array( $this, 'generate_fallback_css' ) ); 

			$this->includes(); // Import theme customize option configs
		}
		/**
		* @description load default theme option values
		*/
		public function load_default_setting_values(){
			require_once $this->dir . 'default-settings.php';
		}
		/**
		* Import the required files 
		*/
		private function includes(){
			$configs_dir = $this->dir . 'configs/';

			require_once $this->dir . 'class-customize-custom.php';
			$this->load_default_setting_values();

			require_once $configs_dir . 'site-identity.php';
			require_once $configs_dir . 'general.php';
			require_once $configs_dir . 'header.php';
			require_once $configs_dir . 'fullscreen-menu.php';
			require_once $configs_dir . 'search-screen.php';
			require_once $configs_dir . 'footer.php';
			require_once $configs_dir . 'sidebar.php';
			require_once $configs_dir . 'home-page.php';
			require_once $configs_dir . 'archive-pages.php';
			require_once $configs_dir . 'single-post.php';
			require_once $configs_dir . 'category-index-page.php';
			require_once $configs_dir . 'woocommerce.php';
			require_once $configs_dir . 'typography.php';
			require_once $configs_dir . 'advertisement.php';
			require_once $configs_dir . 'animation.php';
			require_once $configs_dir . 'popup-signip-form.php';
			require_once $configs_dir . 'advanced.php';
		}
		/**
		* Enqueue script for customize initial
		*/
		public function customize_scripts(){
			$assets_version = $this->assets_version;
			$assets_uri 	= $this->assets_uri;
			$js_root_uri 	= $assets_uri . 'js/';
			$css_root_uri 	= $assets_uri . 'css/';
			$font_root_uri	= $assets_uri . 'fonts/';
			$customize_deps = array('fallsky-widgets', 'jquery-ui-slider', 'customize-controls');

			wp_register_script('fallsky-customize', $js_root_uri . 'customize/fallsky-customize.min.js', $customize_deps, $assets_version, true);
			wp_localize_script('fallsky-customize', 'fallsky_customize', apply_filters('fallsky_customize_js_vars', array()));
			wp_enqueue_script('fallsky-customize');

			wp_enqueue_style('awsomefont', 	$font_root_uri 	. 'font-awesome/css/font-awesome.min.css');
			wp_enqueue_style('jquery-ui', 	$css_root_uri 	. 'jquery-ui/jquery-ui.css');
			wp_enqueue_style('fallsky-customize-style', $css_root_uri . 'customize/fallsky-customizer.css', array(), $assets_version);
		}
		/**
		* Enqueue scirpts for refreshable controls
		*/
		public function preview_scripts(){
			$assets_version = $this->assets_version;
			$assets_uri 	= $this->assets_uri;
			$js_root_uri 	= $assets_uri . 'js/';

			wp_register_script('fallsky-preview', $js_root_uri . 'customize/fallsky-preview.min.js', array('jquery', 'customize-selective-refresh'), $assets_version, true);
			wp_localize_script('fallsky-preview', 'fallsky_preview', apply_filters('fallsky_preview_js_vars', array()));
			wp_enqueue_script('fallsky-preview');
		}
		public function footer_scripts(){
			do_action('admin_print_footer_scripts');
			echo '<style>#mce-modal-block { z-index: 9999998!important; } .mce-menu-align, .mce-popover, .mce-floatpanel { z-index: 9999999 !important; }</style>';
		}
		/**
		* Generate the fallback css when saving customize
		*/
		public function generate_fallback_css($wp_customize){
			$css = apply_filters('fallsky_get_fallback_css', '');
			set_theme_mod('fallsky_fallback_customize_css', $css);
		}
	}
	add_action( 'after_setup_theme', function(){ new Fallsky_Customize_Manager(); } );
}
