<?php
/**
* Shortcode Button
*/

if(!class_exists('LoftOcean_Shortcode_Button')) {
	class LoftOcean_Shortcode_Button {
		private $class = '';
		private $id = 'loftocean-shortcode-button';
		private $name = 'lo_button';
		private $type = 'complex';
		private $html = '<i class=""></i><span class="title">%s</span>';
		public function __construct() {
			add_action('admin_init', array($this, 'init'));
			add_shortcode($this->name, array($this, 'parse'));
		}
		/**
		* @description init shortcode button
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
			$html = sprintf($this->html, esc_html__('Button', 'loftocean'));
			printf($tmpl, $this->class, $this->id, $this->name, $this->type, $html);
		}
		/**
		* @description shortcode setting tmpl
		*/
		public function shortcode_tmpl() { ?>
			<script type="text/html" id="tmpl-loftocean-shortcode-<?php print($this->name); ?>">		
				<div class="control" id="button-text">
					<span class="control-title"><?php esc_html_e('Button Text', 'loftocean'); ?></span>
					<input type="text" name="button-text" value="<?php esc_html_e('Button Text', 'loftocean'); ?>" />
				</div>		
				<div class="control" id="button-url">
					<span class="control-title"><?php esc_html_e('URL', 'loftocean'); ?></span>
					<input type="url" name="button-url" value="" placeholder="http://..." />
				</div>
				<div class="control switcher" id="button-target">
					<label for="button-target-new">
						<input type="checkbox" name="button-target" id="button-target-new" value="on" /> <?php esc_html_e('Open in a new tab', 'loftocean'); ?>
					</label>
				</div>
				<div class="control dropdown-controls" id="button-size">
					<span class="control-title"><?php esc_html_e('Size', 'loftocean'); ?></span>
					<label for="button-size-small">
						<input type="radio" name="button-size" id="button-size-small" value="small" /> <?php esc_html_e('Small', 'loftocean'); ?>
					</label>
					<label for="button-size-medium">
						<input type="radio" name="button-size" id="button-size-medium" value="medium" checked /> <?php esc_html_e('Medium', 'loftocean'); ?>
					</label>
					<label for="button-size-large">
						<input type="radio" name="button-size" id="button-size-large" value="large" /> <?php esc_html_e('Large', 'loftocean'); ?>
					</label>
					<label for="button-size-extra-large">
						<input type="radio" name="button-size" id="button-size-extra-large" value="extra-large" /> <?php esc_html_e('Extra Large', 'loftocean'); ?>
					</label>
				</div>
				<div class="control" id="button-background">
					<span class="control-title"><?php esc_html_e('Background Color', 'loftocean'); ?></span>
					<label for="button-bg-color-default">
						<input type="radio" name="button-bg-color" id="button-bg-color-default" value="default" checked /> <?php esc_html_e('Default', 'loftocean'); ?>
					</label>
					<label for="button-bg-color-custom">
						<input type="radio" name="button-bg-color" id="button-bg-color-custom" value="custom" /> <?php esc_html_e('Custom', 'loftocean'); ?>
					</label>
					<div class="button-bg-custom-color color-picker-wrapper" style="display: none;">
						<input type="text" name="button-bg-custom-color" value="#000" class="loftocean-color-picker" />
					</div>
				</div>
				<div class="control" id="button-color">
					<span class="control-title"><?php esc_html_e('Text Color', 'loftocean'); ?></span>
					<label for="button-text-color-default">
						<input type="radio" name="button-text-color" id="button-text-color-default" value="default" checked /> <?php esc_html_e('Default', 'loftocean'); ?>
					</label>
					<label for="button-text-color-custom">
						<input type="radio" name="button-text-color" id="button-text-color-custom" value="custom" /> <?php esc_html_e('Custom', 'loftocean'); ?>
					</label>
					<div class="button-text-custom-color color-picker-wrapper" style="display: none;">
						<input type="text" name="button-text-custom-color" value="#fff" class="loftocean-color-picker" />
					</div>
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
			$sizes = array('small', 'medium', 'large', 'extra-large');
			$atts = shortcode_atts(array(
				'url'  => '#',
				'target' => '',
				'size' => 'medium',
				'background' => '',
				'color' => ''
			), $atts, $this->name);

			return sprintf(
				'<a href="%s" class="button lo-button %s"%s%s><span%s>%s</span></a>',
				$atts['url'],
				in_array($atts['size'], $sizes) ? $atts['size'] : 'medium',
				empty($atts['target']) ? '' : ' target="_blank"',
				$this->validate_color($atts['background']) ? sprintf(' style="color: %s;"', $atts['background']) : '',
				$this->validate_color($atts['color']) ? sprintf(' style="color: %s;"', $atts['color']) : '',
				$content
			);
		}
		/**
		* @description helper function to validate color format
		*/
		private function validate_color($hex){
			return !empty($hex) && preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $hex);
		}
	}
	new LoftOcean_Shortcode_Button();
}