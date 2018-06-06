<?php
/**
* Shortcode Row
*/

if(!class_exists('LoftOcean_Shortcode_Row')) {
	class LoftOcean_Shortcode_Row {
		private $class = '';
		private $id = 'loftocean-shortcode-row';
		private $name = 'lo_row';
		private $type = 'complex';
		private $html = '<i class=""></i><span class="title">%s</span>';
		public function __construct() {
			add_action('admin_init', array($this, 'init'));
			add_shortcode($this->name, array($this, 'parse'));
		}
		/**
		* @description init shortcode row
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
			$html = sprintf($this->html, esc_html__('Row', 'loftocean'));
			printf($tmpl, $this->class, $this->id, $this->name, $this->type, $html);
		}
		/**
		* @description shortcode setting tmpl
		*/
		public function shortcode_tmpl() { ?>
			<script type="text/html" id="tmpl-loftocean-shortcode-<?php print($this->name); ?>">
				<div class="shortcode-description">
					<p class="lo-column-description"><?php esc_html_e('You can insert the shortcode into content, then use [column] shortcode to add another column into the group.', 'loftocean'); ?></p>
				</div>
				<div class="control" id="column1-content">
					<span class="control-title"><?php esc_html_e('Column 1 Content', 'loftocean'); ?></span> <br>
					<textarea name="column1-content"><?php esc_html_e('Column 1 Content', 'loftocean'); ?></textarea>
				</div>
				<div class="control" id="column2-content">
					<span class="control-title"><?php esc_html_e('Column 2 Content', 'loftocean'); ?></span> <br>
					<textarea name="column2-content"><?php esc_html_e('Column 2 Content', 'loftocean'); ?></textarea>
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
			$wrap = '<div class="lo-row">%s</div>';
			return sprintf($wrap, do_shortcode($content));
		}
	}
	new LoftOcean_Shortcode_Row();
}