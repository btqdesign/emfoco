<?php
/**
* Shortcode Tweet
*/

if(!class_exists('LoftOcean_Shortcode_Tweet')) {
	class LoftOcean_Shortcode_Tweet {
		private $class = '';
		private $id = 'loftocean-shortcode-tweet';
		private $name = 'lo_tweet';
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
		public function shortcode_btn($tmpl) {
			$html = sprintf($this->html, esc_html__('Tweet It', 'loftocean'));
			printf($tmpl, $this->class, $this->id, $this->name, $this->type, $html);
		}
		/**
		* @description shortcode setting tmpl
		*/
		public function shortcode_tmpl() { 
			$user_id = get_current_user_id();
			$twitter = get_user_meta($user_id, 'twitter', true);
			$twitter = untrailingslashit($twitter);
			$username = empty($twitter) ? false : explode('/', $twitter); ?>
			<script type="text/html" id="tmpl-loftocean-shortcode-<?php print($this->name); ?>">
				<div class="control" id="tweet-it-at">
					<span class="control-title"><?php esc_html_e('Twitter Username (optional)', 'loftocean'); ?></span>
					<input type="text" name="tweet-account" value="<?php echo is_array($username) ? array_pop($username) : ''; ?>" placeholder="<?php esc_html_e('Twitter Username', 'loftocean'); ?>" />
				</div>
				<div class="control" id="tweet-it-style">
					<span class="control-title"><?php esc_html_e('Style', 'loftocean'); ?></span>
					<label for="tweet-style-inline">
						<input type="radio" name="tweet-style" id="tweet-style-inline" value="inline" checked /> <?php esc_html_e('Inline', 'loftocean'); ?>
					</label>
					<label for="tweet-style-paragraph">
						<input type="radio" name="tweet-style" id="tweet-style-paragraph" value="paragraph" /> <?php esc_html_e('Paragraph', 'loftocean'); ?>
					</label>
				</div>
				<div class="control" id="tweet-it-wrap-tag" style="display: none;">
					<span class="control-title"><?php esc_html_e('Wrap Tag', 'loftocean'); ?></span>
					<select name="tweet-wrap">
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
			$atts = shortcode_atts(array('style' => 'inline', 'tag' => 'p', 'via' => ''), $atts, $this->name);
			$tags = array('p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6');
			if(!empty($content)){
				$content = force_balance_tags($content);
				$tag   = !empty($atts['tag']) ? strtolower($atts['tag']) : 'p';
				$tag   = in_array($tag, $tags) ? $tag : 'p';
				$at    = empty($atts['via']) ? '' : $atts['via'];

				$text  = '"' . strip_tags($content) . '"';
				$text  = urlencode($text);

				$args = array('text' => $text, 'url'  => get_permalink());
				if(!empty($at)){
					$args['via'] = esc_attr($at);
				}

				$url = add_query_arg($args, 'http://twitter.com/intent/tweet');
				return $atts['style'] == 'inline' ? sprintf('<a class="tweet-it" href="%s">%s</a>', $url, $content) 
					: sprintf('<div class="tweet-it-paragraph"><%1$s><a class="tweet-it" href="%2$s">%3$s</a></%1$s></div>', $tag, $url, $content);

			}
			return $content;
		}
	}
	new LoftOcean_Shortcode_Tweet();
}

