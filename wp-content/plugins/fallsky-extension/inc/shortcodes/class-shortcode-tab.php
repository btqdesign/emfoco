<?php
/**
* Shortcode Tab
*/

if(!class_exists('LoftOcean_Shortcode_Tab')) {
	class LoftOcean_Shortcode_Tab {
		private $class = '';
		private $id = 'loftocean-shortcode-tab';
		private $name = 'lo_tab';
		private $type = 'complex';
		private $html = '<i class=""></i><span class="title">%s</span>';
		public function __construct() {
			add_action('admin_init', array($this, 'init'));
			add_shortcode($this->name, array($this, 'parse'));
		}
		/**
		* @description init shortcode tab
		*/
		public function init() {
			add_action('loftocean_shortcodes', array($this, 'shortcode_btn'));
			add_action('loftocean_shortcodes_setting_tmpl', array($this, 'shortcode_tmpl'));
		}
		/**
		* @description show the button html
		* @param string with format 
		*   %1$s class name
		*   %2$s id 
		*   %3$s shortcode name
		*   %4$s shortcode type, with or without settings
		*   %5$s shortcode title and any icons
		*/
		public function shortcode_btn($tmpl) {
			$html = sprintf($this->html, esc_html__('Tab', 'loftocean'));
			printf($tmpl, $this->class, $this->id, $this->name, $this->type, $html);
		}
		/**
		* @description shortcode setting tmpl
		*/
		public function shortcode_tmpl() { ?>
			<script type="text/html" id="tmpl-loftocean-shortcode-<?php print($this->name); ?>">
				<div class="shortcode-description">
					<p class="lo-column-description"><?php esc_html_e('Please wrap tab shortcode with [lo_tabs][/lo_tabs] shortcode.', 'loftocean'); ?></p>
				</div>
				<div class="control" id="tab-title">
					<span class="control-title"><?php esc_html_e('Tab Title', 'loftocean'); ?></span>
					<input type="text" name="tab-title" value="<?php esc_html_e('Tab Title', 'loftocean'); ?>" />
				</div>
				<div class="control" id="tab-content">
					<span class="control-title"><?php esc_html_e('Tab Content', 'loftocean'); ?></span> <br>
					<textarea name="tab-content"><?php esc_html_e('Tab Content', 'loftocean'); ?></textarea>
				</div>
			</script> <?php
		}
		/**
		* @description parse shortcode
		* @param array, shortcode attributes
		* @param string, content
		* @return string
		*/
		public function parse($atts, $content = ''){
			$atts = shortcode_atts(array(
				'title' => ''
			), $atts, $this->name);

			$wrap = '[[tab-title]]<a href="#">%s</a>[[/tab-title]]<div class="lo-tab-content">%s</div>';

			return sprintf($wrap, esc_html($atts['title']), do_shortcode($content));
		}
	}
	new LoftOcean_Shortcode_Tab();
}