<?php
/**
* Shortcode generator
*/
if(!class_exists('LoftOcean_Shortcodes')) {
	class LoftOceam_Shortcodes {
		public $keep_shortcodes = array('lo_tweet', 'lo_highlight', 'lo_drop_caps', 'lo_divider', 'lo_button');
		public function __construct() {
			$this->includes();
			add_action('admin_init', 						array($this, 'init_generator'));
			add_filter('the_content', 						array($this, 'remove_p'));
			add_filter('the_content', 						array($this, 'breakline'), 999999);
			add_filter('loftocean_excerpt_strip_shortcode', array($this, 'strip_shortcodes'));
			add_filter('strip_shortcodes_tagnames', 		array($this, 'keep_shortcode'));

			remove_filter('get_the_excerpt', 				'wp_trim_excerpt');

			add_filter('get_the_excerpt', 					array($this, 'wp_trim_excerpt'));
			add_action('wp_enqueue_scripts', 				array($this, 'enqueue_scripts'));
		}
		public function keep_shortcode($tags){
			return array_diff($tags, $this->keep_shortcodes);
		}
		public function wp_trim_excerpt( $text = '' ) {
			$raw_excerpt = $text;
			if ( '' == $text ) {
				$text = get_the_content('');

				$text = apply_filters('loftocean_excerpt_strip_shortcode', $text); 

				/** This filter is documented in wp-includes/post-template.php */
				$text = apply_filters('the_content', $text);
				$text = str_replace(']]>', ']]&gt;', $text); 

				/**
				 * Filters the number of words in an excerpt.
				 *
				 * @since 2.7.0
				 *
				 * @param int $number The number of words. Default 55.
				 */
				$excerpt_length = apply_filters( 'excerpt_length', 55 );
				/**
				 * Filters the string in the "more" link displayed after a trimmed excerpt.
				 *
				 * @since 2.9.0
				 *
				 * @param string $more_string The string shown within the more link.
				 */
				$excerpt_more = apply_filters( 'excerpt_more', ' ' . '[&hellip;]' );
				$text = wp_trim_words( $text, $excerpt_length, $excerpt_more ); 
			}
			/**
			 * Filters the trimmed excerpt string.
			 *
			 * @since 2.8.0
			 *
			 * @param string $text        The trimmed text.
			 * @param string $raw_excerpt The text prior to trimming.
			 */
			return apply_filters( 'wp_trim_excerpt', $text, $raw_excerpt );
		}
		public function strip_shortcodes($content){
			global $shortcode_tags;

			if(false === strpos($content, '[')){
				return $content;
			}

			if(empty($shortcode_tags) || !is_array($shortcode_tags))
				return $content;

			// Find all registered tag names in $content.
			preg_match_all('@\[([^<>&/\[\]\x00-\x20=]++)@', $content, $matches);

			$tags_to_remove = array_keys($shortcode_tags);

			/**
			 * Filters the list of shortcode tags to remove from the content.
			 *
			 * @since 4.7.0
			 *
			 * @param array  $tag_array Array of shortcode tags to remove.
			 * @param string $content   Content shortcodes are being removed from.
			 */
			$tags_to_remove = apply_filters('strip_shortcodes_tagnames', $tags_to_remove, $content);

			$tagnames = array_intersect($tags_to_remove, $matches[1]);

			if(empty($tagnames)){
				return $this->remove_tags($content);
			}

			$content = do_shortcodes_in_html_tags( $content, true, $tagnames ); 

			$pattern = get_shortcode_regex( $tagnames ); 
			$content = preg_replace_callback( "/$pattern/", 'strip_shortcode_tag', $content ); 

			$content = $this->remove_tags($content);

			// Always restore square braces so we don't break things like <!--[if IE ]>
			$content = unescape_invalid_shortcodes( $content );

			return $content;
		}
		private function remove_tags($content){
			foreach($this->keep_shortcodes as $t){
				$pattern = '/' . get_shortcode_regex(array($t)) . '/';
				$content = preg_replace($pattern, "$5", $content);
			}
			return $content;
		}
		public function breakline($content){
			$content = preg_replace('|<p>\s*</p>|', '', $content);
			$content = preg_replace('|(?<!<br />)\s*\n<!--p-->(.*)<!--ep-->|', "\n<p>$1</p>", $content); 
			return str_replace(array('<!--p-->', '<!--ep-->'), '', $content); 
		}
		public function remove_p($content) {
			$block = implode('|', array('lo_button', 'lo_divider', 'lo_highlight', 'lo_tweet', 'lo_drop_caps', 'lo_row', 'lo_column', 'lo_tabs', 'lo_tab', 'lo_accordions', 'lo_accordion', 'lo_wide_image', 'gallery'));
			// opening tag
			$rep = preg_replace("/(<p>)?\[($block)(\s[^\]]+)?\](<\/p>|<br \/>)?/", "[$2$3]", $content);
				
			// closing tag
			$rep = preg_replace("/(<p>)?\[\/($block)](<\/p>|<br \/>)?/", "[/$2]", $rep);
			return $rep;
		}
		private function includes(){
			$dir = FALLSKY_PLUGIN_DIR . 'inc/shortcodes/';
			require_once $dir . 'class-shortcode-media.php';

			require_once $dir . 'class-shortcode-author.php';
			require_once $dir . 'class-shortcode-drop-caps.php';
			require_once $dir . 'class-shortcode-highlight.php';
			require_once $dir . 'class-shortcode-tweet.php';
			require_once $dir . 'class-shortcode-row.php';
			require_once $dir . 'class-shortcode-column.php';
			require_once $dir . 'class-shortcode-button.php';
			require_once $dir . 'class-shortcode-divider.php';
			require_once $dir . 'class-shortcode-tabs.php';
			require_once $dir . 'class-shortcode-tab.php';
			require_once $dir . 'class-shortcode-accordions.php';
			require_once $dir . 'class-shortcode-accordion.php';
		}
		public function init_generator() {
			add_action('media_buttons', 							array($this, 'media_buttons'));
			add_action('admin_footer-post-new.php', 				array($this, 'shortcode_generator_html'));
			add_action('admin_footer-post.php', 					array($this, 'shortcode_generator_html'));
			add_action('customize_controls_print_footer_scripts', 	array($this, 'shortcode_generator_html'));
			add_action('admin_enqueue_scripts', 					array($this, 'admin_enqueue_script'));
		}
		/*
		* @description add button to media buttons
		* @param string, the editor unique id
		*/
		public function media_buttons($editor_id){ ?>
			<button type="button" id="insert-loftocean-shortcode-button" class="button insert-loftocean-shortcode" data-editor="<?php print($editor_id); ?>">
				<?php esc_html_e('Add Shortcode', 'loftocean'); ?>
			</button> <?php
		}
		/**
		* @descriptioin enqueue script for admin screen
		*/
		public function admin_enqueue_script(){
			global $parent_file, $pagenow;
			if((!empty($parent_file) && (strpos($parent_file, 'edit.php') !== false)) || ('customize.php' == $pagenow)){
				wp_enqueue_script('loftocean_sc_generator_script', FALLSKY_PLUGIN_URI . 'assets/js/shortcode-generator.min.js', array('jquery', 'wp-color-picker'), FALLSKY_PLUGIN_ASSETS_VERSION, true);
				wp_enqueue_style('loftocean_sc_generator_style', FALLSKY_PLUGIN_URI . 'assets/css/shortcode-generator.min.css', array(), FALLSKY_PLUGIN_ASSETS_VERSION);
				wp_enqueue_style('wp-color-picker');
			}
		}
		/**
		* @description enqueue scripts for front end
		*/	
		public function enqueue_scripts(){
			$deps = array('jquery', 'jquery-ui-accordion');
			wp_enqueue_script('loftocean_shortcodes_script', FALLSKY_PLUGIN_URI . 'assets/js/shortcodes.min.js', $deps, FALLSKY_PLUGIN_ASSETS_VERSION, true);	
		}
		/**
		* @description shortcode generator panel html
		*/
		public function shortcode_generator_html(){
			global $parent_file, $pagenow;
			if((!empty($parent_file) && (strpos($parent_file, 'edit.php') !== false)) || ('customize.php' == $pagenow)) :
				$tmpl = apply_filters('loftocean_shortcode_btn_tmpl', '<span class="shortcode-btn %s" id="%s" data-shortcode="%s" data-type="%s">%s</span>'); ?>
				<div class="loftocean-shortcode-generator-wrap">
					<div class="loftocean-shortcode-generator-panel">
						<div class="loftocean-shortcode-generator-head">
							<div class="title">
								<h1><?php echo apply_filters('loftocean_shortcode_generator_title', esc_html__('Shortcodes', 'loftocean')); ?></h1>
							</div>
						</div>
						<div class="loftocean-shortcode-generator-body">
							<div class="shortcode-list">
								<?php do_action('loftocean_shortcodes', $tmpl); ?>
							</div>
							<div class="shortcode-settings"><!-- content will added dynamically by javascript --></div>
						</div>
						<button title="<?php esc_html_e('Close (Esc)', 'loftocean'); ?>" type="button" class="close">×</button>
					</div>
				</div>
				<div class="loftocean-shortcode-color-picker" style="display: none;"><input name="loftocean-shortcode-color" type="text" /></div>
				<script type="text/html" id="tmpl-loftocean-shortcode-header">
					<div class="setting-header">
						<a href="#" class="shortcode-home" title="<?php esc_html_e('Click to return to the shortcodes list', 'loftocean'); ?>"><?php esc_html_e('All shortcodes', 'loftocean'); ?></a>
					</div>
				</script>
				<script type="text/html" id="tmpl-loftocean-shortcode-footer">
					<div class="setting-footer">
						<input type="hidden" name="loftocean-shortcode-name" value="" />
						<a href="#" class="button button-primary button-large insert">
							<i class="fa fa-check"></i><?php esc_html_e('Insert shortcode', 'loftocean'); ?>
						</a>
					</div>
				</script> <?php
				do_action('loftocean_shortcodes_setting_tmpl');
			endif;
		}
	}
	// Init shortcodes
	new LoftOceam_Shortcodes();
}