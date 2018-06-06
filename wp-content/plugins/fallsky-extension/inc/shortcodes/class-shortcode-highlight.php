<?php
/**
* Shortcode Highlight
*/


if(!class_exists('LoftOcean_Shortcode_Highlight')) {
	class LoftOcean_Shortcode_Highlight {
		private $class = '';
		private $id = 'loftocean-shortcode-highlight';
		private $name = 'lo_highlight';
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
			$html = sprintf($this->html, esc_html__('Highlights', 'loftocean'));
			printf($tmpl, $this->class, $this->id, $this->name, $this->type, $html);
		}
		/**
		* @description shortcode setting tmpl
		*/
		public function shortcode_tmpl() { ?>
			<script type="text/html" id="tmpl-loftocean-shortcode-<?php print($this->name); ?>">
				<div class="control" id="highlight-style">
					<span class="control-title"><?php esc_html_e('Style', 'loftocean'); ?></span>
					<label for="highlight-style-normal">
						<input type="radio" name="highlight-style" id="highlight-style-normal" value="normal" checked /> <?php esc_html_e('Normal', 'loftocean'); ?>
					</label>
					<label for="highlight-style-bottomline">
						<input type="radio" name="highlight-style" id="highlight-style-bottomline" value="bottomline" /> <?php esc_html_e('Bottom Line', 'loftocean'); ?>
					</label>
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
				'normal'     => '',
				'bottomline' => ' bottomline'
			);
			if(!empty($content)){
				$atts  = shortcode_atts(array('style' => 'normal'), $atts, $this->name);
				$class = in_array($atts['style'], array_keys($styles)) ? $styles[$atts['style']] : '';

				return sprintf('<!--p--><span class="highlight%s">%s</span><!--ep-->', $class, do_shortcode($content));
			}
			return '';
		}
	}
	new LoftOcean_Shortcode_Highlight();
}
