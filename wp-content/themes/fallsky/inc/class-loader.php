<?php
/*
* Defined the loader class for theme
*/

if ( ! class_exists( 'Fallsky_Loader' ) ) {
	class Fallsky_Loader {
		// The loader instance
		private static $instance = false;
		// WordPress Version chanllenged
		private $wp_version_chanllenged = '4.7';

		function __construct() {
			$this->constants();
			add_action( 'after_setup_theme', array( $this, 'load_text_domain' ) );

			// Fallsky is running under WordPress 4.5 or above
			if ( version_compare( $GLOBALS['wp_version'], $this->wp_version_chanllenged, '<' ) ) {
				$this->back_compat();
			} else { // Let's roll
				$this->load_extension();
				$this->includes();
			}
		}
		/**
		* @description load text domain
		*/
		public function load_text_domain() {
			load_theme_textdomain( 'fallsky' );
		}
		/*
		* @description deactivate theme and activate the default theme
		*	and display error message 
		*/
		private function back_compat() {
			require_once FALLSKY_THEME_INC . 'class-back-compat.php';
			new Fallsky_Back_Compat( $this->wp_version_chanllenged );
		}
		/**
		* @description define global constants
		*/
		private function constants() {
			$this->define( 'FALLSKY_THEME_VERSION', 	'1.1.7' );
			$this->define( 'FALLSKY_THEME_URI', 		get_template_directory_uri() . '/' );
			$this->define( 'FALLSKY_THEME_DIR', 		get_template_directory() . '/' );
			$this->define( 'FALLSKY_THEME_INC', 		FALLSKY_THEME_DIR . 'inc/' );
			$this->define( 'FALLSKY_ASSETS_URI', 		FALLSKY_THEME_URI . 'assets/' );
			$this->define( 'FALLSKY_ASSETS_VERSION', 	'2018070781' );
		}
		/**
		* @description helper function to actually define constant
		*/
		private function define( $name, $value ) {
			defined( $name ) ? '' : define( $name, $value );
		}
		/**
		* @description load theme extension, this can be from plugins or extensions
		*/
		private function load_extension() {
			do_action( 'fallsky_extension_init' );
		}
		/**
		* @description import the files required 
		*/
		private function includes() {
			$inc_dir = FALLSKY_THEME_INC;

			require_once $inc_dir . 'class-upgrader.php';
			require_once $inc_dir . 'class-wp-core.php';
			require_once $inc_dir . 'function.php';
			require_once $inc_dir . 'widgets/class-fallsky-widget.php';
			require_once $inc_dir . 'class-meta-box.php';
			require_once $inc_dir . 'plugins/envato-market.php';
			require_once $inc_dir . 'plugins/one-click-demo-import-config.php';
			require_once $inc_dir . 'plugins/tgm-plugin-activation-config.php';
			require_once $inc_dir . 'widgets/normal/class-widget-category.php';
			require_once $inc_dir . 'widgets/normal/class-widget-profile.php';
			require_once $inc_dir . 'widgets/normal/class-widget-banner.php';
			require_once $inc_dir . 'widgets/normal/class-widget-post.php';
			require_once $inc_dir . 'widgets/normal/class-widget-social.php';

			// For theme customize
			require_once $inc_dir . 'customize/class-customize-manager.php';

			// For frontend rendering
			require_once $inc_dir . 'function-templates.php';
			require_once $inc_dir . 'class-frontend.php';
			require_once $inc_dir . 'class-frontend-options.php';
			require_once $inc_dir . 'class-post-list.php';

			// For multilingual
			require_once $inc_dir . 'multilingual/class-polylang.php'; 
		}
		/**
		* @description instance Loader class
		*	there can only be one instance of loader
		* @return class Loader
		*/
		public static function _instance() {
			if ( false === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}
	}
	// Initialize theme
	Fallsky_Loader::_instance();
}
