<?php
/**
* Shortcode Column
*/

if(!class_exists('LoftOcean_Shortcode_Column')) {
	class LoftOcean_Shortcode_Column {
		private $class = '';
		private $id = 'loftocean-shortcode-column';
		private $name = 'lo_column';
		private $type = 'complex';
		private $html = '<i class=""></i><span class="title">%s</span>';
		public function __construct() {
			add_action('admin_init', array($this, 'init'));
			add_shortcode($this->name, array($this, 'parse'));
		}
		/**
		* @description init shortcode column
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
			$html = sprintf($this->html, esc_html__('Column', 'loftocean'));
			printf($tmpl, $this->class, $this->id, $this->name, $this->type, $html);
		}
		/**
		* @description shortcode setting tmpl
		*/
		public function shortcode_tmpl() { ?>
			<script type="text/html" id="tmpl-loftocean-shortcode-<?php print($this->name); ?>">
				<div class="shortcode-description">
					<p class="lo-column-description"><?php esc_html_e('Please wrap column shortcode with [lo_row][/lo_row] shortcode.', 'loftocean'); ?></p>
				</div>
				<div class="control" id="column-sizes">
					<span class="control-title"><?php esc_html_e('Size', 'loftocean'); ?></span>
					<label for="size-half">
						<input type="radio" name="column-sizes" id="size-half" value="1/2" checked /> <?php esc_html_e('1/2', 'loftocean'); ?>
					</label>
					<label for="size-one-third">
						<input type="radio" name="column-sizes" id="size-one-third" value="1/3" /> <?php esc_html_e('1/3', 'loftocean'); ?>
					</label>
					<label for="size-two-thirds">
						<input type="radio" name="column-sizes" id="size-two-thirds" value="2/3" /> <?php esc_html_e('2/3', 'loftocean'); ?>
					</label>
					<label for="size-one-fourth">
						<input type="radio" name="column-sizes" id="size-one-fourth" value="1/4" /> <?php esc_html_e('1/4', 'loftocean'); ?>
					</label>
					<label for="size-three-fourths">
						<input type="radio" name="column-sizes" id="size-three-fourths" value="3/4" /> <?php esc_html_e('3/4', 'loftocean'); ?>
					</label>
					<label for="size-one-fifth">
						<input type="radio" name="column-sizes" id="size-one-fifth" value="1/5" /> <?php esc_html_e('1/5', 'loftocean'); ?>
					</label>
					<label for="size-two-fifths">
						<input type="radio" name="column-sizes" id="size-two-fifths" value="2/5" /> <?php esc_html_e('2/5', 'loftocean'); ?>
					</label>
					<label for="size-three-fifths">
						<input type="radio" name="column-sizes" id="three-fifths" value="3/5" /> <?php esc_html_e('3/5', 'loftocean'); ?>
					</label>
					<label for="size-four-fifths">
						<input type="radio" name="column-sizes" id="size-four-fifths" value="4/5" /> <?php esc_html_e('4/5', 'loftocean'); ?>
					</label>
				</div>
				<div class="control" id="column-content">
					<span class="control-title"><?php esc_html_e('Column Content', 'loftocean'); ?></span> <br>
					<textarea name="column-content"><?php esc_html_e('Column Content', 'loftocean'); ?></textarea>
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
			$sizes = array(
				'1/2' => 'one-half',
				'1/3' => 'one-third', 
				'2/3' => 'two-thirds',
				'1/4' => 'one-fourth',
				'3/4' => 'three-fourths',
				'1/5' => 'one-fifth',
				'2/5' => 'two-fifths', 
				'3/5' => 'three-fifths',
				'4/5' => 'four-fifths'
			);
			$atts = shortcode_atts(array(
				'size' => '1/2'
			), $atts, $this->name);

			$wrap = '<div class="lo-column %s">%s</div>';
			$class = in_array($atts['size'], array_keys($sizes)) ? $sizes[$atts['size']] : 'one-half';

			return sprintf($wrap, $class, do_shortcode($content));
		}
	}
	new LoftOcean_Shortcode_Column();
}