<?php
/**
* Shortcode Drop Caps
*/

if(!class_exists('LoftOcean_Shortcode_Drop_Caps')) {
	class LoftOcean_Shortcode_Drop_Caps {
		private $class = '';
		private $id = 'loftocean-shortcode-drop_caps';
		private $name = 'lo_drop_caps';
		private $type = 'complex';
		private $html = '<i class=""></i><span class="title">%s</span>';
		public function __construct() {
			add_action('admin_init', array($this, 'init'));
			add_shortcode($this->name, array($this, 'parse'));
		}
		/**
		* @description init shortcode author
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
			$html = sprintf($this->html, esc_html__('Drop Caps', 'loftocean'));
			printf($tmpl, $this->class, $this->id, $this->name, $this->type, $html);
		}
		/**
		* @description shortcode setting tmpl
		*/
		public function shortcode_tmpl() { ?>
			<script type="text/html" id="tmpl-loftocean-shortcode-<?php print($this->name); ?>">
				<div class="control" id="drop-caps-style">
					<span class="control-title"><?php esc_html_e('Style', 'loftocean'); ?></span>
					<label for="drop-caps-style-normal">
						<input type="radio" name="drop-caps-style" id="drop-caps-style-normal" value="normal" checked /> <?php esc_html_e('Normal', 'loftocean'); ?>
					</label>
					<label for="drop-caps-style-light">
						<input type="radio" name="drop-caps-style" id="drop-caps-style-light" value="light" /> <?php esc_html_e('Light', 'loftocean'); ?>
					</label>
					<label for="drop-caps-style-square">
						<input type="radio" name="drop-caps-style" id="drop-caps-style-square" value="square" /> <?php esc_html_e('Square', 'loftocean'); ?>
					</label>
					<label for="drop-caps-style-square-light">
						<input type="radio" name="drop-caps-style" id="drop-caps-style-square-light" value="square-light" /> <?php esc_html_e('Square Light Background', 'loftocean'); ?>
					</label>
					<label for="drop-caps-style-square-dark">
						<input type="radio" name="drop-caps-style" id="drop-caps-style-square-dark" value="square-dark" /> <?php esc_html_e('Square Dark Background', 'loftocean'); ?>
					</label>
				</div>
				<div class="control" id="drop-caps-wrap">
					<span class="control-title"><?php esc_html_e('Wrap Tag', 'loftocean'); ?></span>
					<select name="drop-caps-wrap">
						<option value="p">P</option>
						<option value="h1">H1</option>
						<option value="h2">H2</option>
						<option value="h3">H3</option>
						<option value="h4">H4</option>
						<option value="h5">H5</option>
						<option value="h6">H6</option>
					</select>
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
			$styles = array(
				'normal'       => '',
				'square-dark'  => ' square dark-bg', 
				'square'       => ' square',
				'square-light' => ' dropcap square light-bg',
				'light'        => ' light'
			);
			$tags = array('p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6');
			if(!empty($content)){
				$atts  = shortcode_atts(array('style' => 'normal', 'tag' => 'p'), $atts, $this->name);
				$class = in_array($atts['style'], array_keys($styles)) ? $styles[$atts['style']] : '';
				$tag   = !empty($atts['tag']) ? strtolower($atts['tag']) : 'p';
				$tag   = in_array($tag, $tags) ? $tag : 'p';

				return sprintf('<%1$s class="dropcap%2$s">%3$s</%1$s>', $tag, $class, do_shortcode($content));
			}
			return '';
		}
	}
	new LoftOcean_Shortcode_Drop_Caps();
}