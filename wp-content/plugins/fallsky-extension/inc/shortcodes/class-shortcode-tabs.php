<?php
/**
* Shortcode Tabs
*/

if(!class_exists('LoftOcean_Shortcode_Tabs')) {
	class LoftOcean_Shortcode_Tabs {
		private $class = '';
		private $id = 'loftocean-shortcode-tabs';
		private $name = 'lo_tabs';
		private $type = 'complex';
		private $html = '<i class=""></i><span class="title">%s</span>';
		public function __construct() {
			add_action('admin_init', array($this, 'init'));
			add_shortcode($this->name, array($this, 'parse'));
		}
		/**
		* @description init shortcode tabs
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
			$html = sprintf($this->html, esc_html__('Tabs', 'loftocean'));
			printf($tmpl, $this->class, $this->id, $this->name, $this->type, $html);
		}
		/**
		* @description shortcode setting tmpl
		*/
		public function shortcode_tmpl() { ?>
			<script type="text/html" id="tmpl-loftocean-shortcode-<?php print($this->name); ?>">
				<div class="shortcode-description">
					<p class="lo-column-description"><?php esc_html_e('You can insert the shortcode into content, then use [tab] shortcode to add another tab into the group.', 'loftocean'); ?></p>
				</div>
				<div class="control" id="column-sizes">
					<span class="control-title"><?php esc_html_e('', 'loftocean'); ?></span>
					<label for="tabs-direction-horizontal">
						<input type="radio" name="tabs-direction" id="tabs-direction-horizontal" value="horinzontal" checked /> <?php esc_html_e('Horizontal Tabs', 'loftocean'); ?>
					</label>
					<label for="tabs-direction-vertical">
						<input type="radio" name="tabs-direction" id="tabs-direction-vertical" value="vertical" /> <?php esc_html_e('Vertical Tabs', 'loftocean'); ?>
					</label>
				</div>
				<div class="control" id="tab1-title">
					<span class="control-title"><?php esc_html_e('Tab 1 Title', 'loftocean'); ?></span>
					<input type="text" name="tab1-title" value="<?php esc_html_e('Tab 1', 'loftocean'); ?>" />
				</div>
				<div class="control" id="tab1-content">
					<span class="control-title"><?php esc_html_e('Tab 1 Content', 'loftocean'); ?></span> <br>
					<textarea name="tab1-content"><?php esc_html_e('Tab 1 Content', 'loftocean'); ?></textarea>
				</div>
				<div class="control" id="tab2-title">
					<span class="control-title"><?php esc_html_e('Tab 2 Title', 'loftocean'); ?></span>
					<input type="text" name="tab2-title" value="<?php esc_html_e('Tab 2', 'loftocean'); ?>" />
				</div>
				<div class="control" id="tab2-content">
					<span class="control-title"><?php esc_html_e('Tab 2 Content', 'loftocean'); ?></span> <br>
					<textarea name="tab2-content"><?php esc_html_e('Tab 2 Content', 'loftocean'); ?></textarea>
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
			$directions = array(
				'horizontal' => '',
				'vertical' => 'vertical-tab'
			);
			$atts = shortcode_atts(array(
				'direction' => 'horizontal'
			), $atts, $this->name);

			$wrap = '<div class="lo-tabs%s" style="display: none;">%s</div>';
			$class = in_array($atts['direction'], array_keys($directions)) ? $directions[$atts['direction']] : '';
			$content = do_shortcode($content);
			$content = preg_replace('~<br.*?>~', '', $content); // Remove all the <br> tag
			if(preg_match_all('/\[\[tab-title\]\](.*)\[\[\/tab-title\]\]/Ui', $content, $match)){
				$titles = sprintf('<div class="lo-tabs-titles">%s</div>', implode('', $match[1]));
				$content = $titles . sprintf(
					'<div class="lo-tabs-content">%s</div>', 
					preg_replace('/\[\[tab-title\]\](.*)\[\[\/tab-title\]\]/Ui', '', $content)
				);
			}

			return sprintf(
				$wrap, 
				empty($class) ? '' : (' ' . $class), 
				$content
			);
		}
	}
	new LoftOcean_Shortcode_Tabs();
}