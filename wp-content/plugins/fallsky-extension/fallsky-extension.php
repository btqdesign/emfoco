<?php
/*
Plugin Name: Fallsky Extension
Plugin URI: http://www.loftocean.com/
Description: Fallsky Theme function extension - Post like, post sharing, gallery slider, Instagram feed and more.
Version: 1.1.3
Author: Loft.Ocean
Author URI: http://www.loftocean.com/
Text Domain: loftocean
Domain Path: /languages
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if(!class_exists('Fallsky_Extension')){
	class Fallsky_Extension {
		private static $instance = null;
		function __construct(){
			$this->define_constants();
			$this->includes();

			add_action('plugins_loaded', 	array($this, 'load_textdomain'));
			add_action('widgets_init', 		array($this, 'register_widget'));
		}
		/**
		* @description Load text domain
		*/
		public function load_textdomain(){
			load_plugin_textdomain('loftocean');
		}
		/*
		* @description Define constant needed
		*/
		private function define_constants(){
			$this->define( 'FALLSKY_PLUGIN_VERSION', 		'1.1.3' );
			$this->define( 'FALLSKY_PLUGIN_DIR', 			plugin_dir_path( __FILE__ ) );
			$this->define( 'FALLSKY_PLUGIN_URI', 			plugins_url( '/', __FILE__ ) );
			$this->define( 'FALLSKY_PLUGIN_ASSETS_VERSION', '20180052001' );
		}
		/*
		* @description Helper function to define constants
		*/
		private function define($name, $value){
			defined($name) ? '' : define($name, $value);
		}
		/**
		* @description include required files
		*/
		private function includes(){
			$inc = FALLSKY_PLUGIN_DIR . 'inc/';

			require_once $inc . 'class-upgrade.php';
			require_once $inc . 'class-privacy.php';
			require_once $inc . 'class-taxonomy-editing-fields.php';

			require_once $inc . 'class-wp-core.php';
			require_once $inc . 'class-image-svg.php';
			require_once $inc . 'class-admin-meta-box.php';
			require_once $inc . 'class-post-meta.php';
			require_once $inc . 'class-shortcode-generator.php';
			require_once $inc . 'widgets/class-widget-instagram.php';
			require_once $inc . 'widgets/class-widget-facebook.php';
			require_once $inc . 'widgets/class-widget-author.php';
			require_once $inc . 'class-related-posts.php';
		}
		/*
		* @description Register widget
		*/
		public function register_widget(){
			register_widget('LoftOcean_Widget_Instagram');
			register_widget('LoftOcean_Widget_Facebook');
			register_widget('LoftOcean_Widget_Author');
		}
		/**
		* @descirption initialize extenstion
		*/
		public static function _instance(){
			if(null === self::$instance){
				self::$instance = new self();
			}
			return self::$instance;
		}
	}
	add_action('fallsky_extension_init', 'Fallsky_Extension::_instance');
}
