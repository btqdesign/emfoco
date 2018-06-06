<?php
/**
* Prevents theme from running on WordPress versions prior to the version specified
*/

if(!class_exists('Fallsky_Back_Compat')){
	class Fallsky_Back_Compat {
		private $message;
		private $challenge_version = '';
		public function __construct($challenge_version){
			$this->challenge_version = $challenge_version;
			$this->message = sprintf(
				esc_html__(
					'Fallsky requires at least WordPress version %1$s. You are running version %2$s. Please upgrade and try again.', 
					'fallsky'
				), 
				$this->challenge_version, 
				$GLOBALS['wp_version']
			);

			add_action('after_switch_theme', array($this, 'switch_theme'));
			add_action('load-customize.php', array($this, 'customize'));
			add_action('template_redirect', array($this, 'preview'));
		}
		/**
		* @description switches to the default theme if failed the requirements
		*/
		public function switch_theme() {
			switch_theme(WP_DEFAULT_THEME, WP_DEFAULT_THEME);
			unset($_GET['activated']);
			add_action('admin_notices', array($this, 'upgrade_notice'));
		}
		/**
		 * @description add a message for unsuccessful theme switch
		 */
		function upgrade_notice(){
			printf(
				'<div class="error"><p>%s</p></div>', 
				$this->message
			);
		}
		/**
		 * @description prevent the Customizer from being loaded on the old version WordPress
		 */
		public function customize() {
			wp_die($this->message, '', array(
				'back_link' => true,
			));
		}
		/**
		 * @description prevent the Theme Preview from being loaded on the old version WordPress
		 */
		function preview(){
			if(isset($_GET['preview'])){
				wp_die($this->message);
			}
		}
	}
}