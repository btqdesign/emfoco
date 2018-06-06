<?php
if(!class_exists('LoftOcean_Meta_Box')){
	// Add meta box/column for posts
	class LoftOcean_Meta_Box {
		private $format_media 		= false;
		private $format_meta_name 	= 'loftocean-format-media';
		private $background_video 	= array();
		private $posts_media 		= array();
		private $post_authors 		= array();
		public function __construct(){
			add_action('add_meta_boxes', 					array($this, 'register_meta_boxes'));
			add_action('save_post', 						array($this, 'save_meta'), 10, 3);

			add_action('manage_post_posts_custom_column' , 	array($this, 'display_column_featured'), 10, 2);
			add_action('wp_ajax_loftocean_featured_post', 	array($this, 'ajax_featured_post'));
			add_action('admin_footer-post-new.php', 		array($this, 'format_meta_box'));
			add_action('admin_footer-post.php', 			array($this, 'format_meta_box'));
			add_action('admin_enqueue_scripts', 			array($this, 'admin_enqueue_script'));
			add_action('loftocean_post_metabox_after_html', array($this, 'post_co_authors'), 999);

			add_filter('loftocean_background_video', 		array($this, 'background_video'), 10, 2);
			add_filter('loftocean_has_background_video', 	array($this, 'has_background_video'), 10, 2);
			add_filter('loftocean_has_vimeo_bg_video', 		array($this, 'has_vimeo_background_video'), 10, 2);
			add_filter('manage_post_posts_columns' , 		array($this, 'add_column_featured'));
			add_filter('loftocean_meta_box_js_vars', 		array($this, 'meta_box_js_vars'));
			add_filter('loftocean_get_post_format_media', 	array($this, 'get_post_format_media'));
			add_filter('loftocean_post_authors', 			array($this, 'get_post_authors'));
		}
		// Get all authors for current post
		public function get_post_authors($author){
			global $post;
			$pid = $post->ID;
			if(!isset($this->post_author[$pid])){
				$authors 	= array($post->post_author); 
				$co_authors = get_post_meta($post->ID, 'loftocean-post-co-authors', true); 
				if(!empty($co_authors) && is_array($co_authors)){
					foreach($co_authors as $ca){
						if(!in_array($ca, $authors)) array_push($authors, $ca);
					}
				}

				$this->post_authors[$pid] = array_filter($authors);
			}
			return $this->post_authors[$pid];
		}
		// Add js vars for column featured ajax calling
		public function meta_box_js_vars($vars){
			return array_merge((array)$vars, array(
				'featured_post' => array(
					'url' => esc_url(admin_url('admin-ajax.php')),
					'action' => 'loftocean_featured_post',
					'nonce' => wp_create_nonce('loftocean_featured_post')
				)
			));
		}
		// Enqueue script for wp admin
		public function admin_enqueue_script(){
			wp_enqueue_media();
			wp_register_script('loftocean_meta_box', FALLSKY_PLUGIN_URI . 'assets/js/meta-box.min.js', array('jquery'), FALLSKY_PLUGIN_ASSETS_VERSION, true);
			wp_localize_script('loftocean_meta_box', 'loftocean_meta', apply_filters('loftocean_meta_box_js_vars', array()));
			wp_enqueue_script('loftocean_meta_box');
		}
		// Save featured post status via ajax
		public function ajax_featured_post(){
			if(isset($_POST['post_id']) && isset($_POST['loftocean_featured_post'])){
				check_ajax_referer('loftocean_featured_post', 'nonce');
				if(get_post_status($_POST['post_id']) !== false){
					$pid = $_POST['post_id'];
					$staus = ($_POST['loftocean_featured_post'] == 'on') ? 'on' : 'off'; 
					update_post_meta($pid, 'loftocean-featured-post', $staus);
					wp_send_json_success();
				}
			}
			wp_send_json_error();
		}
		// Add new column featured post
		public function add_column_featured($columns){
			return array_merge($columns, array('loftocean-featured' => esc_html__('Featured', 'loftocean')));
		}
		// Display featured post column html
		function display_column_featured($column, $post_id){
			if($column == 'loftocean-featured'){
				$disabled = current_user_can('edit_post', $post_id) ? '' : ' disabled="disabled"';
				$featured = esc_attr(get_post_meta($post_id, 'loftocean-featured-post', true));
				echo '<input data-id="' . $post_id . '" type="checkbox" name="loftocean-featured-post-inline-edit" value="on" ' . checked('on', $featured, false) . $disabled . ' />';
			}
		}
		/*
		* @description show background video
		* @param string video html string
		* @param int post id
		* @return string video html string or empty
		*/
		public function background_video($video, $pid){
			$key = 'post-' . $pid;
			if(array_key_exists($key, $this->background_video)){
				return $this->background_video[$key];
			}

			if(!empty($pid) && (false !== get_post_status($pid))){
				$p = get_post($pid);
				$p_type = $p->post_type;
				$video = '';
				if(('post' == $p_type) && ('video' == get_post_format($pid))){
					$media = get_post_meta($pid, 'loftocean-format-media', true);
					$video = (is_array($media) && isset($media['video-code'])) ? $media['video-code'] : '';
				}
				else if('page' == $p_type){
					$media = get_post_meta($pid, 'loftocean-page-background-video', true);
					$video = (is_array($media) && isset($media['code'])) ? $media['code'] : '';
				}
				$this->background_video[$key] = empty($video) ? '' : $this->get_video($video);
				return $this->background_video[$key];
			}
			return '';
		}
		/*
		* @description test if has background video
		* @param boolean current result
		* @param int post id
		* @return boolean true if background video exists
		*/
		public function has_background_video($has, $pid){
			$video = $this->background_video('', $pid);
			return !empty($video);
		}
		/*
		* @description test if has vimeo background video
		* @param boolean current result
		* @param int post id
		* @return boolean true if vimeo background video exists
		*/
		public function has_vimeo_background_video($has, $pid){
			$regex = '/https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)/';
			$video = $this->background_video('', $pid);
			return !empty($video) && preg_match($regex, $video);
		}
		// Register loftloader shortcode meta box
		public function register_meta_boxes(){
		    add_meta_box('loftocean_post_meta_box', apply_filters('loftocean_post_metabox_title', esc_html__('Theme Options', 'loftocean')), array($this, 'post_metabox'), 'post', 'advanced');
		}
		// Show meta box html
		public function post_metabox($post){
			$featured = esc_attr(get_post_meta($post->ID, 'loftocean-featured-post', true)); 
			do_action('loftocean_post_metabox_before_html', $post); ?>
			<p>
				<label><?php esc_html_e('Featured Post:', 'loftocean'); ?></label>
				<input type="checkbox" id="loftocean-enable-featured-post" name="loftocean-featured-post" value="on" <?php checked('on', $featured); ?> />
				<label class="checkbox-label" for="loftocean-enable-featured-post"><?php esc_html_e('Check to mark this post as featured post', 'loftocean'); ?></label>
			</p>
			<input type="hidden" name="loftocean_nonce" value="<?php echo wp_create_nonce('loftocean_nonce'); ?>" /> <?php
			do_action('loftocean_post_metabox_after_html', $post);
		}
		public function post_co_authors( $post ) {
			if( is_multi_author() ) {
				$current_author = $post->post_author;
				$users 			= get_users(array('fields' => array('ID', 'display_name'))); 
				$authors 		= apply_filters('loftocean_post_authors', array());
				$options 		= array(); 
				foreach($users as $user){ 
					$uid 		= $user->ID;
					$is_current = ($uid == $current_author);
					$options[] = sprintf('<option value="%s" %s%s>%s</option>', 
						$uid, 
						(in_array($uid, $authors) && !$is_current ? ' selected' : ''), 
						$is_current ? ' style="display: none;"' : '',
						esc_html($user->display_name)
					); 
				} ?>
				<p>
					<label style="display: block;"><?php esc_html_e('Add Co-Author(s):', 'loftocean'); ?></label>
					<select name="loftocean_single_post_co_authors[]" id="loftocean-single-post-co-authors" multiple style="width: 100%;"><?php echo implode('', $options); ?></select>
				</p> <?php
			}
		}
		// Save loftloader shortcode meta
		public function save_meta($post_id, $post, $update){
			if(empty($update) || !in_array($post->post_type, array('post'))) return;

			if(wp_verify_nonce($_REQUEST['loftocean_nonce'], 'loftocean_nonce') !== false){
				$featured = empty($_REQUEST['loftocean-featured-post']) ? '' : 'on';
				update_post_meta($post_id, 'loftocean-featured-post', $featured);

				$format_media = empty($_REQUEST[$this->format_meta_name]) ? '' : $_REQUEST[$this->format_meta_name];
				$format_media['gallery-code'] = $this->sanitize_textarea($format_media, 'gallery-code');
				$format_media['gallery-id'] = $this->sanitize_textarea($format_media, 'gallery-id');
				$format_media['audio-code'] = $this->sanitize_html($format_media, 'audio-code');
				$format_media['audio-id'] = $this->sanitize_num($format_media, 'audio-id');
				$format_media['video-code'] = $this->sanitize_html($format_media, 'video-code');
				$format_media['video-id'] = $this->sanitize_num($format_media, 'video-id');

				update_post_meta($post_id, 'loftocean-format-media', $format_media);

				if(isset($_REQUEST['loftocean_single_post_co_authors'])){
					update_post_meta($post_id, 'loftocean-post-co-authors', $_REQUEST['loftocean_single_post_co_authors']);
				}

				// Save theme custom options
				do_action('loftocean_save_post', $post_id);
			}
		}
		// Add js template for format extra info
		public function format_meta_box(){
			global $post; 
			if(($post->post_type == 'post') && current_theme_supports('post-formats') && post_type_supports($post->post_type, 'post-formats')) :
				$pid = $post->ID;
				$format = get_post_format($pid);  ?>
				<script type="text/html" id="loftocean-tmpl-format-meta-box" data-format="<?php print($format); ?>">
					<div id="loftocean-format-media" style="padding-top:10px;">
						<h4 style="margin: 0;"><?php esc_html_e('Post Format Content', 'loftocean'); ?> </h4>
						<div class="format gallery">
							<p><a href="#" class="format-media gallery"><?php esc_html_e('Choose Gallery', 'loftocean'); ?></a></p>
							<label>
								<?php esc_html_e('Or type manually:', 'loftocean'); ?>
								<textarea <?php $this->get_format_name('gallery-code'); ?> style="width: 98%; height: 70px;" class="gallery-code"><?php $this->get_format_content('gallery-code'); ?></textarea>
								<input <?php $this->get_format_name('gallery-id'); ?> type="hidden" value="<?php $this->get_format_content('gallery-id'); ?>" class="gallery-id" >
							</label>
							<span class="description"><?php esc_html_e('(gallery shortcode allowed only)', 'loftocean'); ?></span>
						</div>
						<div class="format audio">
							<p><a href="#" class="format-media audio"><?php esc_html_e('Choose Audio', 'loftocean'); ?></a></p>
							<label>
								<?php esc_html__('Or type manually:', 'loftocean'); ?>
								<textarea class="audio-code" readonly <?php $this->get_format_name('audio-code'); ?> style="width: 98%; height: 115px;"><?php $this->get_format_content('audio-code'); ?></textarea>
								<input class="audio-id" type="hidden" <?php $this->get_format_name('audio-id'); ?> value="<?php $this->get_format_content('audio-id'); ?>" />
							</label>
						</div>
						<div class="format video">
							<p><a href="#" class="format-media video"><?php esc_html_e('Choose Video', 'loftocean'); ?></a></p>
							<label>
								<?php esc_html_e('Or type manually:', 'loftocean'); ?>
								<textarea class="video-code" <?php $this->get_format_name('video-code'); ?> style="width: 98%; height: 115px;"><?php $this->get_format_content('video-code'); ?></textarea>
								<input class="video-id" type="hidden" <?php $this->get_format_name('video-id'); ?> value="<?php $this->get_format_content('video-id'); ?>" />
							</label>
							<span style="font-size: 11px;">
								<?php printf(esc_html__('%1$sNote:%2$s support %1$sYoutube/Vimeo Embed <iframe>%2$s or %1$sHTML5 <video>%2$s only.', 'loftocean'), '<b>', '</b>'); ?>
							</span>
							<?php do_action('loftocean_post_metabox_format_video', $post); ?>
						</div>
					</div>
				</script> <?php
			endif;
		}
		// Post Format Media
		public function get_post_format_media($media){
			global $post, $content_width;
			$pid = $post->ID;
			if(!isset($this->posts_media[$pid])){
				$format_media = '';
				$format_content = get_post_meta($pid, 'loftocean-format-media', true);
				if(!empty($format_content) && is_array($format_content)){
					switch(get_post_format()){
						case 'gallery':
							$format_media = empty($format_content['gallery-code']) ? '' : $format_content['gallery-code'];
							break;
						case 'video':
							$width 			= $content_width;
							$content_width 	= 600; // Make the initial width to 600.
							$format_media 	= empty($format_content['video-code']) ? '' : $this->get_video($format_content['video-code']);
							$content_width 	= $width; // Reset the default content width.
							break;
						case 'audio':
							$format_media = empty($format_content['audio-code']) || !has_shortcode($format_content['audio-code'], 'audio') 
								? '' : do_shortcode($format_content['audio-code']);
							break;
						default: 
							$format_media = false;
					}
				}
				$this->posts_media[$pid] = $format_media;
			}
			return $this->posts_media[$pid];
		}
		/**
		* @description helper function to get format media content for post format meta box
		* @param string post format type name
		* @return string post format media if exists
		*/
		private function get_format_content($name){
			global $post;
			$media = $this->format_media ? $this->format_media : get_post_meta($post->ID, 'loftocean-format-media', true);

			echo !empty($media) && isset($media[$name]) ? esc_attr($media[$name]) : '';
		}
		// Helper function to get format media name
		private function get_format_name($name){
			if(!empty($name)){
				printf('name="%s[%s]"', $this->format_meta_name, $name);
			}
		}
		// Helper function santize textarea
		private function sanitize_textarea($values, $name){
			return empty($values[$name]) ? '' : sanitize_text_field($values[$name]);
		}
		// Helper function sanitize html string
		private function sanitize_html($values, $name){
			if(current_user_can( 'unfiltered_html')){
				return $values[$name];
			}
			else{
				global $allowedposttags;
				return empty($values[$name]) ? '' : wp_kses($values[$name], array_merge($allowedposttags, array(
					'iframe' => array(
						'width' => true,
						'height' => true,
						'src' => true,
						'frameborder' => true,
						'allowfullscreen' => true
					))));
			}
		}
		// Helper function sanitize number
		private function sanitize_num($values, $name){
			return empty($values[$name]) ? '' : absint($values[$name]);
		}
		// Helper function sanitize url
		private function sanitize_url($values, $name){
			return empty($values[$name]) ? '' : esc_url_raw($values[$name]);
		}
		/**
		* Helper function to test current video is valid
		*/
		private function get_video($video){
			if(!empty($video)){
				$regex_video 	= '/<video[^>]*>.*<\/video>/';
				$regex_youtube	= '/^.*(?:(?:youtu\.be\/|v\/|vi\/|u\/\w\/|embed\/)|(?:(?:watch)?\?v(?:i)?=|\&v(?:i)?=))([^#\&\?]*).*/';
				$regex_vimeo 	= '/https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)/';
				if(has_shortcode($video, 'video')){
					return do_shortcode($video);
				}
				else if(preg_match($regex_video, $video) || preg_match($regex_youtube, $video) || preg_match($regex_vimeo, $video)){
					return $video;
				}
			}
			return false;
		}
	}
	new LoftOcean_Meta_Box();
}