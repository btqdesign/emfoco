<?php
// Post meta related functions
if(!class_exists('LoftOcean_Post_Metas')){
	class LoftOcean_Post_Metas {
		private $like_single_text 	= '';
		private $like_plural_text 	= '';
		private $view_single_text 	= '';
		private $view_plural_text 	= '';
		private $likes 				= array();
		private $views 				= array();
		function __construct(){
			add_action('wp_enqueue_scripts', 					array($this, 'enqueue_scripts'));
			add_action('wp_ajax_loftocean_post_like', 			array($this, 'update_post_like'));
			add_action('wp_ajax_nopriv_loftocean_post_like', 	array($this, 'update_post_like'));
			add_action('wp', 									array($this, 'update_post_view'));
			add_action('loftocean_post_meta_sharing', 			array($this, 'post_meta_sharing'), 20, 2);

			add_filter('loftocean_like_count_number', 			array($this, 'get_like_count'));
			add_filter('loftocean_view_count_number', 			array($this, 'get_view_count'));
			add_filter('loftocean_clickable_like', 				array($this, 'clickable_like'));

			add_action('loftocean_post_metabox_after_html', 	array($this, 'view_like_count_options'));
			add_action('loftocean_save_post', 					array($this, 'save_view_like_count'));
		}
		/**
		* @description register fav like related javscript
		*/
		public function enqueue_scripts(){
			wp_register_script('loftocean-post-metas', FALLSKY_PLUGIN_URI . 'assets/js/post-metas.min.js', array('jquery'), FALLSKY_PLUGIN_ASSETS_VERSION, true);
			wp_localize_script('loftocean-post-metas', 'loftocean_ajax', array(
				'url' => esc_url(admin_url('admin-ajax.php')),
				'like' => array( 'action' => 'loftocean_post_like' )
			));
			wp_enqueue_script('loftocean-post-metas');
		}
		/**
		* @description favour like ajax handler
		*/
		public function update_post_like(){
			if(isset($_POST['post_id'])){
				if(get_post_status($_POST['post_id']) !== false){
					$pid = $_POST['post_id'];
					$likes = get_post_meta($pid, 'loftocean-like-count', true);
					update_post_meta($pid, 'loftocean-like-count', (intval($likes) + 1));
				}
			}
			wp_send_json_success();
		}
		/**
		* @description visits ajax handler
		*/
		public function update_post_view(){
			if(is_singular('post')){
				global $post;
				$pid = $post->ID;
				$view = get_post_meta($pid, 'loftocean-view-count', true);
				update_post_meta($pid, 'loftocean-view-count', (intval($view) + 1));
			}
		}
		/**
		* @description get post like count
		*/
		public function get_like_count(){
			$pid = get_the_ID();
			if(!isset($this->likes[$pid])){
				$like = get_post_meta($pid, 'loftocean-like-count', true);
				$this->likes[$pid] = $this->convert_number($like);
			}
			return $this->likes[$pid];
		}
		/**
		* @description get post like count
		*/
		public function get_view_count(){
			$pid = get_the_ID();
			if(!isset($this->views[$pid])){
				$view = get_post_meta($pid, 'loftocean-view-count', true);
				$this->views[$pid] = $this->convert_number($view);
			}
			return $this->views[$pid];
		}
		/**
		* Output clickable like button with number
		*/
		public function clickable_like(){
			$like_number = apply_filters('loftocean_like_count_number', 0);
			$cookie_name = 'loftocean_post_likes_post-' . get_the_ID();
			return sprintf(
				'<div class="article-like%s" data-post-id="%s"><span class="icon_heart_alt"></span><span class="counts">%s</span></div>',
				isset($_COOKIE[$cookie_name]) && ('done' == $_COOKIE[$cookie_name]) ? ' liked' : '',
				get_the_ID(),
				$like_number
			);
		}
		/**
		* @description display content sharing html
		*/
		public function post_meta_sharing($enabled, $class = 'side-share-icons'){
			$enabled = (array)$enabled;
			$url= get_permalink();
			$title = get_the_title();
			$excerpt = get_the_excerpt();
			$media = has_post_thumbnail() ? '&media=' . wp_get_attachment_url(get_post_thumbnail_id()) : ''; ?>

			<div class="<?php print($class); ?>">
				<span class="share-title"><?php esc_html_e('Share the article:', 'loftocean'); ?></span>
				<?php if(in_array('facebook', $enabled)) : ?>
					<?php printf(
						'<a target="_blank" title="%s" href="%s"><i class="social_facebook"></i></a>',
						esc_html__('Facebook', 'loftocean'),
						esc_url('http://www.facebook.com/sharer.php?u=' . $url . '&t=' . $title)
					); ?>
				<?php endif; ?>

				<?php if(in_array('twitter', $enabled)) : ?>
					<?php printf(
						'<a target="_blank" title="%s" href="%s"><i class="social_twitter"></i></a>', 
						esc_html__('Twitter', 'loftocean'), 
						esc_url('http://twitter.com/share?text=' . $title . '&url=' . $url)
					); ?>
				<?php endif; ?>

				<?php if(in_array('pinterest', $enabled)) : ?>
					<?php printf(
						'<a target="_blank" title="%s" href="%s" data-props="width=757,height=728"><i class="social_pinterest"></i></a>', 
						esc_html__('Pinterest', 'loftocean'), 
						esc_url('http://pinterest.com/pin/create/button/?url=' . $url . '&description=' . $title . $media)
					); ?>
				<?php endif; ?>

				<?php if(in_array('google_plus', $enabled)) : ?>
					<?php printf(
						'<a target="_blank" title="%s" href="%s" data-props="width=757,height=728"><i class="social_googleplus"></i></a>', 
						esc_html__('Google plus', 'loftocean'), 
						esc_url('https://plus.google.com/share?url=' . $url)
					); ?>
				<?php endif; ?>
			</div> <?php
		}
		/**
		* @description add settings to theme option panel for post
		*/
		public function view_like_count_options(){
			global $post;
			$pid 	= $post->ID;
			$items 	= array(
				'like' => array('title' => esc_html__('Like Counts: ', 'loftocean'), 'count' => intval(get_post_meta($pid, 'loftocean-like-count', true))),
				'view' => array('title' => esc_html__('View Counts: ', 'loftocean'), 'count' => intval(get_post_meta($pid, 'loftocean-view-count', true)))
			);

			foreach($items as $id => $attrs){
				printf(
					'<p class="loftocean-post-counter-wrap">%s%s%s%s%s</p>',
					sprintf('<label>%s</label>', $attrs['title']),
					sprintf(
						'<input type="number" min="0" name="loftocean-post-%s-count" value="%s" readonly style="width: 90px;" />',
						$id,
						$attrs['count']
					),
					sprintf('<a href="#" class="edit">%s</a>', esc_html__('Edit', 'loftocean')),
					sprintf('<a href="#" class="cancel" style="display: none;">%s</a> ', esc_html__('Cancel', 'loftocean')),
					sprintf('<a href="#" class="save" style="display: none;">%s</a>', esc_html__('Done', 'loftocean'))
				);
			}
		}
		/**
		* @description save like view count
		*/
		public function save_view_like_count($pid){
			$like = empty($_REQUEST['loftocean-post-like-count']) ? 0 : intval($_REQUEST['loftocean-post-like-count']);
			$view = empty($_REQUEST['loftocean-post-view-count']) ? 0 : intval($_REQUEST['loftocean-post-view-count']);
			update_post_meta($pid, 'loftocean-like-count', $like);
			update_post_meta($pid, 'loftocean-view-count', $view);
		}
		/**
		* @description helper function to convert numbers
		*/
		private function convert_number($num){
			$num = intval($num);
			if(empty($num)){
				return 0;
			}
			else if($num >= 1000000){
				$num = floor($num / 100000) / 10;
				return $num . 'M';
			}
			else if($num >= 1000){
				$num = floor($num / 100) / 10;
				return $num . 'K';
			}
			else{
				return $num;
			}
		}
	}
	new LoftOcean_Post_Metas();
}