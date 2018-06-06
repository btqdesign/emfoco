<?php
/**
* Shortcode Divider
*/

if(!class_exists('LoftOcean_Shortcode_Divider')) {
	class LoftOcean_Shortcode_Divider {
		private $class = '';
		private $id = 'loftocean-shortcode-divider';
		private $name = 'lo_divider';
		private $type = 'complex';
		private $html = '<i class=""></i><span class="title">%s</span>';
		public function __construct() {
			add_action('admin_init', array($this, 'init'));
			add_shortcode($this->name, array($this, 'parse'));
		}
		/**
		* @description init shortcode divider
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
			$html = sprintf($this->html, esc_html__('Divider', 'loftocean'));
			printf($tmpl, $this->class, $this->id, $this->name, $this->type, $html);
		}
		/**
		* @description shortcode setting tmpl
		*/
		public function shortcode_tmpl() { ?>
			<script type="text/html" id="tmpl-loftocean-shortcode-<?php print($this->name); ?>">
				<div class="control" id="divider-style">
					<span class="control-title"><?php esc_html_e('Style', 'loftocean'); ?></span>
					<label for="style-solid">
						<input type="radio" name="divider-style" id="style-solid" value="solid" checked /> <?php esc_html_e('Solid', 'loftocean'); ?>
					</label>
					<label for="style-dashed">
						<input type="radio" name="divider-style" id="style-dashed" value="dashed" /> <?php esc_html_e('Dashed', 'loftocean'); ?>
					</label>
					<label for="style-dotted">
						<input type="radio" name="divider-style" id="style-dotted" value="dotted" /> <?php esc_html_e('Dotted', 'loftocean'); ?>
					</label>
				</div>
				<div class="control dropdown-controls" id="divider-size">
					<span class="control-title"><?php esc_html_e('Size', 'loftocean'); ?></span>
					<select name="divider-size">
					<?php for($i = 1; $i < 11; $i++) printf('<option value="%1$s">%1$s</option>', ($i . 'px')); ?>
					</select>
				</div>
				<div class="control color-picker-wrapper" id="divider-color">
					<span class="control-title"><?php esc_html_e('Color', 'loftocean'); ?></span>
					<input type="text" name="divider-color" value="#ddd" class="loftocean-color-picker" />
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
				'style'  => 'solid',
				'size' => '1px',
				'color' => '#ddd'
			), $atts, $this->name);

			$styles = sprintf('style="border-top: %s %s %s;"', $atts['style'], $atts['size'], $atts['color']);

			return sprintf('<hr class="lo-divider" %s />', $styles);
		}
	}
	new LoftOcean_Shortcode_Divider();
}