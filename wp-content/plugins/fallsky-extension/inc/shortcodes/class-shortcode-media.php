<?php
if(!class_exists('LoftOcean_Media')){
	class LoftOcean_Media {
		public $gallery_types 			= array();
		private static $gallery_index 	= 0;
		private $popup_sliders 			= array();
		function __construct() {
			add_action('admin_init', 				array($this, 'admin_init'));
			add_action('wp_footer', 				array($this, 'popup_sliders_in_site_footer'));
			add_filter('post_gallery', 				array($this, 'generate_gallery'), 10, 2);
			add_filter('media_send_to_editor', 		array($this, 'media_send_to_editor'), 9999, 3);

			add_shortcode('lo_wide_image', 			array($this, 'parse_shortcode_wide_image'));
		}
		public function admin_init() { 
			$this->gallery_types = apply_filters('loftocean_gallery_types', array(
				'default' 	=> esc_html__('Grid', 'loftocean'),
				'slider' 	=> esc_html__('Slider', 'loftocean'),
				'justified'	=> esc_html__('Justified Grid', 'loftocean')
			));

			// Enqueue the media UI only if needed.
			if(count($this->gallery_types) > 1){
				add_action('wp_enqueue_media', array($this, 'wp_enqueue_media'));
				add_action('print_media_templates', array($this, 'print_media_templates'));
			}
		}
		/**
		* Add wide image shortcode if needed
		*/
		public function media_send_to_editor($html, $id, $attachment){
			if(!empty($attachment['image_container']) && ('fullwidth' == $attachment['image_container'])){
				$html = sprintf('[lo_wide_image]%s[/lo_wide_image]', $html);
			}

			return $html;
		}
		/**
		* Parse shortcode wide_image
		*/
		public function parse_shortcode_wide_image($atts, $content = ''){
			$wrap = '<div class="wide-image">%s</div>';
			$content = trim($content);
			if(!empty($content)){
				$content = do_shortcode($content);
				$content = preg_replace('/^(<p>|<\/p>|\n|\r|<br>|<br \/>)*/i', '', $content);
				$content = preg_replace('/(<p>|<\/p>|\n|\r|<br>|<br \/>)*$/i', '', $content);
			}
			return sprintf($wrap, $content);
		}
		/**
		 * Registers/enqueues the gallery settings admin js.
		 */
		function wp_enqueue_media() { 
			global $post;  
			if(!empty($post)){
				wp_enqueue_script('loftocean-media-settings', FALLSKY_PLUGIN_URI . 'assets/js/media-settings.min.js', array('media-views', 'media-models'), FALLSKY_PLUGIN_ASSETS_VERSION, true);
			}
		}

		/**
		 * Outputs a view template which can be used with wp.media.template
		 */
		function print_media_templates() {
			global $post;  
			if(!empty($post)){
				$default_gallery_type = apply_filters('loftocean_default_gallery_type', 'default'); ?>
				<script type="text/html" id="tmpl-loftocean-gallery-settings"> 
					<label class="setting">
						<span><?php esc_html_e('Type', 'loftocean'); ?></span>
						<select class="type" name="type" data-setting="type">
							<?php foreach($this->gallery_types as $value => $caption) : ?>
								<option value="<?php echo esc_attr($value); ?>" <?php selected($value, $default_gallery_type); ?>><?php echo esc_html($caption); ?></option>
							<?php endforeach; ?>
						</select>
					</label> 
					<label class="setting orientation gallery-type-slider" style="display: none;">
						<span><?php esc_html_e('Ratio', 'loftocean'); ?></span>
						<select name="slider_ratio" data-setting="slider_ratio">
							<option value="ratio-3-2" selected><?php esc_html_e('3:2', 'loftocean'); ?></option>
							<option value="ratio-1-1"><?php esc_html_e('1:1', 'loftocean'); ?></option>
							<option value="ratio-4-5"><?php esc_html_e('4:5', 'loftocean'); ?></option>
						</select>
					</label>
					<label class="setting row-height gallery-type-justified" style="display: none;">
						<span><?php esc_html_e('Row Height', 'loftocean'); ?></span>
						<input name="justified_row_height" data-setting="justified_row_height" type="number" min="1" value="120" style="float: none;" />
					</label>
					<label class="setting last-row-style gallery-type-justified" style="display: none;">
						<span><?php esc_html_e('Last Row', 'loftocean'); ?></span>
						<select name="justified_last_row_style" data-setting="justified_last_row_style">
							<option value="nojustify" selected><?php esc_html_e('No Justify', 'loftocean'); ?></option>
							<option value="justify"><?php esc_html_e('Justify', 'loftocean'); ?></option>
							<option value="hide"><?php esc_html_e('Hide', 'loftocean'); ?></option>
						</select>
					</label>
					<label class="setting margin gallery-type-justified" style="display: none;">
						<span><?php esc_html_e('Margin', 'loftocean'); ?></span>
						<input name="justified_margin" data-setting="justified_margin" value="1" type="number" min="0" style="float: none;" />
					</label>
				</script> 

				<script type="text/html" id="tmpl-loftocean-display-settings">
					<label class="setting attachment-image-container">
						<span><?php esc_html_e('Image Container', 'LoftOcean_Media'); ?></span>
						<select name="image_container" data-setting="image_container">
							<option value="original" selected><?php esc_html_e('Within content', 'loftocean'); ?></option>
							<option value="fullwidth"><?php esc_html_e('Stretch to fullwidth', 'loftocean'); ?></option>
						</select>
					</label>
				</script> <?php
			}
		}
		/**
		* Parse shortcode gallery
		*/
		public function generate_gallery($output, $attr){
			// Return if not for custom type gallery
			if(!is_singular() || empty($attr['type']) || !in_array($attr['type'], array('slider', 'justified'))) return;

			$post = get_post();
			$atts = shortcode_atts(array(
				'order'   					=> 'ASC',
				'orderby' 					=> 'menu_order ID',
				'id'      					=> $post ? $post->ID : 0,
				'include' 					=> '',
				'exclude' 					=> '',
				'type'						=> 'slider',
				'slider_ratio' 				=> 'ratio-3-2',
				'justified_row_height' 		=> 120,
				'justified_last_row_style'	=> 'nojustify',
				'justified_margin'			=> 1
			), $attr, 'gallery');

			if(!in_array($atts['slider_ratio'], array('ratio-3-2', 'ratio-1-1', 'ratio-4-5'))){
				$atts['slider_ratio'] = 'ratio-3-2';
			}
			$id = intval($atts['id']);

			if(!empty($atts['include'])){
				$_attachments = get_posts(array(
					'include' 			=> $atts['include'], 
					'post_status' 		=> 'inherit', 
					'post_type' 		=> 'attachment', 
					'post_mime_type' 	=> 'image', 
					'order' 			=> $atts['order'], 
					'orderby' 			=> $atts['orderby']
				));

				$attachments = array();
				foreach($_attachments as $key => $val){
					$attachments[$val->ID] = $_attachments[$key];
				}
			}
			else if(!empty($atts['exclude'])){
				$attachments = get_children(array(
					'post_parent' 		=> $id, 
					'exclude' 			=> $atts['exclude'], 
					'post_status' 		=> 'inherit', 
					'post_type' 		=> 'attachment', 
					'post_mime_type' 	=> 'image', 
					'order' 			=> $atts['order'], 
					'orderby'	 		=> $atts['orderby']
				));
			}
			else{
				$attachments = get_children(array(
					'post_parent' 		=> $id, 
					'post_status' 		=> 'inherit', 
					'post_type' 		=> 'attachment', 
					'post_mime_type' 	=> 'image', 
					'order' 			=> $atts['order'], 
					'orderby' 			=> $atts['orderby']
				));
			}
			// Return if no attachment found
			if(empty($attachments)) return '';

			$is_slider 	= 'slider' == $atts['type'];
			$items 		= array();
			$tmpl 		= '<div class="gallery-item%s">%s</div>';
			$wrap 		= sprintf(
				'<div class="post-content-gallery %s" data-gallery-id="loftocean-gallery-%s">%s</div>',
				$is_slider ? sprintf('gallery-slider %s', $atts['slider_ratio']) : sprintf(
					'gallery-justified" data-row-height="%s" data-last-row="%s" data-margin="%s', 
					$atts['justified_row_height'],
					$atts['justified_last_row_style'],
					$atts['justified_margin']
				),
				++self::$gallery_index,
				'%s'
			);
			if(!$is_slider){
				$this->popup_sliders[self::$gallery_index] = array_keys($attachments);
			}
			$index 	= 0;
			$image_sizes = apply_filters( 'loftocean_gallery_image_sizes', array( 'full', 'full' ) );
			foreach($attachments as $id => $attachment){
				$custom 	= '';
				$caption 	= wp_get_attachment_caption($id);
				if($is_slider){
					$caption 	= empty( $caption ) ? '' : sprintf('<span class="wp-caption-text">%s</span>', wp_kses_post($caption));
					$custom		= ( 0 === $index++ ) ? ' first' : '" style="display: none;';
					$image 		= apply_filters( 'loftocean_get_preload_bg', '', $id, $image_sizes, array( 'html' => $caption ) );
				}
				else{
					$args = empty($caption) ? array() : array('attrs' => array('title' => $caption));
					$image = apply_filters( 'loftocean_get_responsive_image', '', $id, $image_sizes[0], $args );
				}
				$items[] = sprintf( $tmpl, $custom, $image );
			}
			return  sprintf(
				$wrap, 
				sprintf(
					'<div class="image-gallery">%s</div>%s', 
					implode('', $items),
					$is_slider ? '<div class="loftocean-gallery-zoom zoom"></div>' : ''
				)
			);
		}
		/**
		* Generate popup sliders in site footer
		*/
		public function popup_sliders_in_site_footer() {
			if( !empty( $this->popup_sliders ) && is_array( $this->popup_sliders ) ) {
				$html = array();
				$wrap = '<div class="popup-slider gallery-slider loftocean-gallery-%s hide">%s</div>';
				$item = '<div class="gallery-item%s">%s</div>';
				$zoom = '<div class="loftocean-popup-gallery-close"></div>';
				$image_sizes = apply_filters( 'loftocean_popup_gallery_image_sizes', array( 'full', 'full' ) );
				foreach( $this->popup_sliders as $gid => $slider ) {
					$items = array();
					foreach( $slider as $index => $attach_id ) {
						if( false !== get_post_status( $attach_id ) ) {
							$custom 	= '';
							$caption 	= wp_get_attachment_caption( $attach_id );
							$caption 	= empty( $caption ) ? '' : sprintf( '<span class="wp-caption-text">%s</span>', wp_kses_post( $caption ) );
							$custom		= (0 === $index) ? ' first' : '" style="display: none;';
							$image 		= apply_filters( 'loftocean_get_preload_bg', '', $attach_id, $image_sizes, array( 'html' => $caption ) );
							$items[] 	= sprintf( $item, $custom, $image );
						}
					}
					if( !empty( $items ) ) {
						$html[] = sprintf( $wrap, $gid, sprintf( '<div class="image-gallery">%s</div>%s', implode( '', $items), $zoom ) );
					}
				}
				if( !empty( $html ) ) {
					printf(
						'<div class="loftocean-popup-sliders">%s</div>', 
						implode( '', $html )
					);
				}
			}
		}
	}
	new LoftOcean_Media();
}
