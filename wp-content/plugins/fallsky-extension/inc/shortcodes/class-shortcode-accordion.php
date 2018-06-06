<?php
/**
* Shortcode Accordion
*/

if(!class_exists('LoftOcean_Shortcode_Accordion')) {
	class LoftOcean_Shortcode_Accordion {
		private $class = '';
		private $id = 'loftocean-shortcode-accordion';
		private $name = 'lo_accordion';
		private $type = 'complex';
		private $html = '<i class=""></i><span class="title">%s</span>';
		public function __construct() {
			add_action('admin_init', array($this, 'init'));
			add_shortcode($this->name, array($this, 'parse'));
		}
		/**
		* @description init shortcode accordion
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
			$html = sprintf($this->html, esc_html__('Accordion', 'loftocean'));
			printf($tmpl, $this->class, $this->id, $this->name, $this->type, $html);
		}
		/**
		* @description shortcode setting tmpl
		*/
		public function shortcode_tmpl() { ?>
			<script type="text/html" id="tmpl-loftocean-shortcode-<?php print($this->name); ?>">
				<div class="shortcode-description">
					<p class="lo-column-description"><?php esc_html_e('Please wrap tab shortcode with [lo_accordions][/lo_accordions] shortcode.', 'loftocean'); ?></p>
				</div>
				<div class="control" id="accordion-default-open">
					<label for="accordion-open">
						<input type="checkbox" name="accordion-open" id="accordion-open" value="on" /> <?php esc_html_e('Open this accordion item by default', 'loftocean'); ?>
					</label>
				</div>
				<div class="control" id="accordion-title">
					<span class="control-title"><?php esc_html_e('Accordion Title', 'loftocean'); ?></span>
					<input type="text" name="accordion-title" value="<?php esc_html_e('Accordion Title', 'loftocean'); ?>" />
				</div>
				<div class="control" id="accordion-content">
					<span class="control-title"><?php esc_html_e('Accordion Content', 'loftocean'); ?></span> <br>
					<textarea name="accordion-content"><?php esc_html_e('Accordion Content', 'loftocean'); ?></textarea>
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
				'open' => '',
				'title' => ''
			), $atts, $this->name);

			$class = empty($atts['open']) ? '' : ' open';

			$wrap = '<div class="accordion-item%s"><div class="accordion-title"><span class="accordion-icon"></span><span class="title">%s</span></div><div class="accordion-content">%s</div></div>';

			return sprintf($wrap, $class, esc_html($atts['title']), do_shortcode($content));
		}
	}
	new LoftOcean_Shortcode_Accordion();
}