<?php
// Instagram feed widget
class LoftOcean_Widget_Instagram extends WP_Widget {
	public function __construct() {
		$class = apply_filters('loftocean_instagram_widget_class', 	'loftocean-widget_instagram');
		$title = apply_filters('loftocean_instagram_widget_name', 	esc_html__('LoftOcean Instagram', 'loftocean'));
		$widget_ops = array(
			'classname' 					=> $class,
			'description' 					=> esc_html__('Show your Instagram images.', 'loftocean'),
			'customize_selective_refresh' 	=> true,
		);
		parent::__construct('loftocean-instagram', $title, $widget_ops);
		$this->alt_option_name = 'loftocean-widget_instagram';
	}
	/**
	 * Outputs the content for the current Recent Posts widget instance.
	 */
	public function widget( $args, $instance ) {
		if (!isset( $args['widget_id'])){
			$args['widget_id'] = $this->id;
		}

		$username 	= !empty($instance['username']) ? $instance['username'] : '';
		$title 		= !empty($instance['title']) ? esc_html($instance['title']) : '';

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters('widget_title', $title, $instance, $this->id_base);

		$number = (!empty($instance['number'])) ? absint($instance['number']) : 5;
		if(!$number)
			$number = 5;

		$instagram = apply_filters('loftocean_instagram_html', '', $username, $number);

		if(!empty($instagram)){
			print($args['before_widget']);
			if(!empty($title)){
				printf('%s%s%s', $args['before_title'], $title, $args['after_title']);
			}
			print($instagram);
			print($args['after_widget']);
		}
	}
	/**
	 * Handles updating the settings for the current Recent Posts widget instance.
	 */
	public function update($new_instance, $old_instance){
		$instance 				= $old_instance;
		$instance['username'] 	= sanitize_text_field($new_instance['username']);
		$instance['title'] 		= sanitize_text_field($new_instance['title']);
		$instance['number'] 	= (int) $new_instance['number'];
		return $instance;
	}
	/**
	 * Outputs the settings form for the Recent Posts widget.
	 */
	public function form( $instance ) {
		$username  = isset($instance['username']) 	? esc_attr($instance['username']) : '';
		$title     = isset($instance['title']) 		? esc_attr($instance['title']) : '';
		$number    = isset($instance['number']) 	? absint($instance['number']) : 5;
?>
		<p><label for="<?php print($this->get_field_id('username')); ?>"><?php esc_html_e('Instagram Username:', 'loftocean'); ?></label>
		<input class="widefat" id="<?php print($this->get_field_id('username')); ?>" name="<?php print($this->get_field_name('username')); ?>" type="text" value="<?php print($username); ?>" /></p>

		<p><label for="<?php print($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'loftocean'); ?></label>
		<input class="widefat" id="<?php print($this->get_field_id('title')); ?>" name="<?php print($this->get_field_name('title')); ?>" type="text" value="<?php print($title); ?>" /></p>

		<p><label for="<?php print($this->get_field_id( 'number' )); ?>"><?php esc_html_e('Number of photos to show:', 'loftocean'); ?></label>
		<input class="tiny-text" id="<?php print($this->get_field_id('number')); ?>" name="<?php print($this->get_field_name('number')); ?>" type="number" step="1" min="1" value="<?php print($number); ?>" size="3" /></p>

<?php
	}
}

if(!class_exists('LoftOcean_Instagram')){
	class LoftOcean_Instagram {
		private $access_token 	= false;
		private $user_id 		= false;
		private $user_name 		= false;
		private $privi 			= 'manage_options';
		private $slug 			= 'loftocean-instagram';
		private $parent 		= false;
		private $ajax_action 	= 'loftocean_remove_instagram_token';
		public function __construct(){
			add_action('wp', 							array($this, 'wp'));
//			add_action('admin_menu', 					array($this, 'submenu'), 30);
			add_action('wp_ajax_' . $this->ajax_action, array($this, 'ajax_remove_tokens'));

			add_filter('loftocean_instagram_valid', 	array($this, 'validate_instagram_token'));
			add_filter('loftocean_instagram_page',		array($this, 'instagram_setting_uri'));
		}
		/**
		* Add sub menu for instagram settings
		*/
		public function submenu(){
			$title = esc_html__('Instagram API', 'loftocean');
			$this->parent = $parent_slug = apply_filters('loftocean_theme_menu_slug', 'options-general.php');
			add_submenu_page($parent_slug, $title, $title, $this->privi, $this->slug, array($this, 'render_settings'));
		}
		/**
		* Ajax action remove instagram tokens
		*/
		public function ajax_remove_tokens(){ 
			if(wp_verify_nonce($_POST['nonce'], $this->ajax_action)){
				delete_option('loftocean_instagram_user_token');
				wp_send_json_success('done');
			}
			else{
				wp_send_json_error('failed');
			}
		}
		/**
		* Render the instagram setting page
		*/
		public function render_settings(){
			$message = $tokens = $valid = false;
			if(!empty($_GET['access_token'])){
				$user = $this->curl('https://api.instagram.com/v1/users/self?access_token=' . $_GET['access_token']);
				if(!empty($user) && !empty($user->data) && !empty($user->data->id)){
					$tokens = array('token' => $_GET['access_token'], 'user_id' => $user->data->id, 'user_name' => $user->data->username);
					$valid 	= true;
					update_option('loftocean_instagram_user_token', $tokens);
					wp_redirect(apply_filters('loftocean_instagram_page', ''));
				}
				else{
					$message = sprintf('The Instagram Access Token is not valid, please try again!', 'loftocean');
				}
			}
			else{
				$tokens = get_option('loftocean_instagram_user_token', false);
				if($tokens) $valid = true;
			}
			$url = esc_url(admin_url(sprintf(
				'%s?page=%s&response_type=token',
				'options-general.php' == $this->parent ? 'options-general.php' : 'admin.php',
				$this->slug
			)));
			$get_token = sprintf(
				'<a href="%s" class="button button-primary" style="margin-right: 20px;">%s</a>', 
				esc_url_raw(sprintf(
					'https://api.instagram.com/oauth/authorize/?client_id=%s&scope=basic+public_content&response_type=token&redirect_uri=%s',
					'18160d935d7e4dd8b18b75ab0de0e2d5', //54da896cf80343ecb0e356ac5479d9ec',
					sprintf(
						'http://instagram.loftocean.com/?return_url=%s', 
						esc_url(admin_url(sprintf(
							'%s?page=%s&response_type=token',
							'options-general.php' == $this->parent ? 'options-general.php' : 'admin.php',
							$this->slug
						)))
					) //sprintf('http://api.web-dorado.com/instagram/?return_url=%s', $url)
				)),
				esc_html__('Sign in with Instagram.', 'loftocean')
			);
			$clear_btn = sprintf('<a href="#" class="instagram-clear-btn button button-primary">%s</a>', esc_html__('Remove Token', 'loftocean'));
			$fields = array(
				'token' 	=> esc_html__('Access Token', 'loftocean'),
				'user_id' 	=> esc_html__('User ID', 'loftocean'),
				'user_name' => esc_html__('Username', 'loftocean')
			); ?>
			<div class="wrap loftocean-instagram">
				<h2><?php esc_html_e('Instagram Access Token', 'loftocean'); ?></h2>
				<?php if(!empty($message)) : ?>
				<div class="notice notice-error">
					<p><?php print($message); ?></p>
				</div>
				<?php endif; ?>
				<form name="form">
					<p><?php esc_html_e('You need Access Token for using Instagram API. Click Sign in with Instagram button above to get yours.', 'loftocean'); ?></p>
					<?php if($valid) : ?>
					<table class="form-table"><tbody>
						<?php foreach($fields as $id => $title) : ?>
						<tr>
							<th><?php print($title); ?></th>
							<td><input type="text" readonly value="<?php print($tokens[$id]); ?>" size="60" ></td>
						</tr>
						<?php endforeach; ?>
					</tbody></table>
					<?php endif; ?>
					<p><?php printf('%s%s', $get_token, ($valid ? $clear_btn : '')); ?></p>
				</form>
			</div> <?php

			add_action('admin_print_footer_scripts', array($this, 'print_script'), 999);
		}
		public function print_script(){ ?>
			<script type="text/javascript">
				jQuery(document).ready(function($){
					$('.instagram-clear-btn.button').on('click', function(e){
						e.preventDefault();
						var $button = $(this),
							$error 	= $('<div>', {'class': 'notice notice-error'}).append($('<p>', {'text': "<?php esc_html_e('Failed to remove Instagram Access Token, please try again late.', 'loftocean'); ?>"}));
							OK		= window.confirm("<?php esc_html_e('Are you sure to remove your Instagram Tokens?', 'loftocean'); ?>");
						if(OK){ 
							jQuery.post(wp.ajax.settings.url, {'nonce': '<?php echo wp_create_nonce($this->ajax_action); ?>', 'action': '<?php print($this->ajax_action); ?>'})
								.done(function(){
									$button.closest('form').find('input[type=text]').val('');
									$button.remove();
								})
								.fail(function(){
									$button.closest('.loftocean-instagram').find('h2').after($error);
								});
						}
					});
				});
			</script><?php
		}
		/**
		* Test function to check current information is valid
		* @return boolean
		*/
		public function validate_instagram_token($valid = false){
			$tokens = get_option('loftocean_instagram_user_token', false);
			return !empty($tokens) && !empty($tokens['token']) && !empty($tokens['user_id']);
		}
		/**
		* Get instagram setting page url
		* @return url
		*/
		public function instagram_setting_uri($uri){
			return esc_url(admin_url(sprintf(
				'%s?page=%s', 
				'options-general.php' == $this->parent ? 'options-general.php' : 'admin.php',
				$this->slug
			)));
		}
		/**
		* Filters for frontend calling
		*/
		public function wp(){
			// $tokens = get_option('loftocean_instagram_user_token', false);
			// if($tokens){
			// 	$this->access_token = $tokens['token'];
			// 	$this->user_id 		= $tokens['user_id'];
			// 	$this->user_name 	= $tokens['user_name'];

				add_filter('loftocean_instagram_feed', 	array($this, 'get_feeds'), 10, 3);
				add_filter('loftocean_instagram_html', 	array($this, 'get_html'), 10, 3);
			// }
		}
		/**
		* @description helper function to actually send the request to instangram api server
		* @param url string api url
		* @return json object if any data returned
		**/
		private function curl($url){
			$curl = curl_init(); // initializing
			curl_setopt($curl, CURLOPT_URL, $url);
			// Set to return the result but not print
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			// Set timeout value
			curl_setopt($curl, CURLOPT_TIMEOUT, 20);
			// Get the query results
			$json = curl_exec($curl);
			curl_close($curl);

			return json_decode($json); // decode and return
		}
		/**
		* @description helper function to get the user id 
		* @param string instagram user name
		* @return string user id of given user name from instagram
		*/
		private function get_user_id($username){
			if(!empty($this->user_id[$username])){
				return $this->user_id[$username];
			}
			else{
				$access_token 	= $this->access_token;
				$user_id 		= get_option('loftocean_instagram_user_id_' . $username);
				if(empty($user_id)){
					$user_search = $this->curl("https://api.instagram.com/v1/users/search?q=" . $username . "&access_token=" . $access_token);
					if(isset($user_search->data)){
						foreach($user_search->data as $user){
							if(trim($user->username) == $username){
								$user_id = $user->id;
								break;
							}
						}
					}
					empty($user_id) ? '' : update_option('loftocean_instagram_user_id_' . $username, $user_id);
				}
				if(!empty($user_id)){
					$this->user_id[$username] = $user_id;
				}
				return $user_id;
			}
		}
		/**
		* @description get instagram feed from transient or from instagram site
		* @param string username
		* @param int number of feeds to get
		* @return mix if feeds exists, return array of feeds otherwise return boolean false
		*/
		public function get_feeds($instagram, $username = '', $limit = 24){
			if(!empty($username)){
				$username 	= $username;
				$db_user 	= sanitize_title_with_dashes($username);
				$feeds 		= get_transient('loftocean_instagram-' . $db_user);
				if(false === $feeds){
					$remote = wp_remote_get('https://www.instagram.com/' . trim($username));
					if(is_wp_error($remote)) return $instagram; 
					if(200 != wp_remote_retrieve_response_code($remote)) return $instagram;

					$shards 		= explode('window._sharedData = ', $remote['body']);
					$insta_json 	= explode(';</script>', $shards[1]);
					$insta_array 	= json_decode($insta_json[0], TRUE);

					if(!$insta_array) return $instagram;

					if(isset($insta_array['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'])){
						$images = $insta_array['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'];
						if(!is_array($images)) return $instagram;
					}
					else{
						return $instagram;
					}

					$instagram = array();
					foreach($images as $image){
						$image = $image['node'];
						$type = ($image['is_video'] == true) ? 'video' : 'image';
						$caption = empty($image['caption']) ? esc_attr__( 'Instagram Image', 'loftocean') : $image['caption'];
						if($type === 'image'){
							$instagram[] = array(
								'description'   => $caption,
								'link'		  	=> esc_url_raw('https://instagram.com/p/' . $image['shortcode']),
								'thumbnail'	 	=> $image['thumbnail_src'],
								'small'			=> $image['thumbnail_src'],
								'large'			=> $image['thumbnail_src'],
								'original'		=> $image['display_url'],
								'type'		  	=> $type
							);
						}
					}
					$feeds = $instagram;
					// do not set an empty transient - should help catch private or empty accounts
					if(!empty($instagram)){
						$instagram = maybe_serialize($instagram);
						set_transient('loftocean_instagram-' . $db_user, $instagram, apply_filters('loftocean_instagram_cache_time', HOUR_IN_SECONDS * 2));
					}
				}
				return maybe_unserialize($feeds);
			}
			return $instagram;
	
			// if(!empty($username)){
			// 	$username 	= $this->user_name; //trim($username);
			// 	$db_user 	= sanitize_title_with_dashes($username);
			// 	$feeds 		= get_transient('loftocean_instagram-' . $db_user);
			// 	if(empty($feeds)){
			// 		$feeds 	= array();
			// 		$go 	= intval($limit) ? intval($limit) : 24;
			// 		$uid 	= $this->user_id; //$this->get_user_id($username);
			// 		if(!empty($uid)){
			// 			$data = $this->curl(sprintf(
			// 				'https://api.instagram.com/v1/users/%s/media/recent?count=33&access_token=%s',
			// 				$uid,
			// 				$this->access_token
			// 			));
			// 			while($go && $data && isset($data->data)){
			// 				foreach($data->data as $feed){
			// 					$feeds[] = array(
			// 						'link'			=> $feed->link,
			// 						'thumbnail'	 	=> $feed->images->thumbnail->url,
			// 						'small'			=> $feed->images->low_resolution->url,
			// 						'large'			=> $feed->images->standard_resolution->url
			// 					);
			// 					$go --;
			// 					if($go < 1) break; 
			// 				}
			// 				$data = $go && !empty($data->pagination->next_url) ? $this->curl($data->pagination->next_url) : false;
			// 			}
			// 			empty($feeds) ? '' : set_transient(
			// 				sprintf('loftocean_instagram-%s', $db_user), 
			// 				maybe_serialize($feeds), 
			// 				apply_filters('loftocean_instagram_cache_time', HOUR_IN_SECONDS * 4)
			// 			);
			// 		}
			// 	}
			// 	else{
			// 		$feeds = maybe_unserialize($feeds); 
			// 	}
			// 	return empty($feeds) ? $instagram : $feeds; 
			// }
			// return $instagram;
		}
		/**
		* @description callback function of filter loftocean_instagram_html, to get instagram feeds html
		* @param string default html string
		* @param string username
		* @param int number of feeds to return
		* @return string feed list html
		*/
		public function get_html($html, $username, $limit = 0){
			$feeds = apply_filters('loftocean_instagram_feed', false, $username);
			if(!empty($feeds) && is_array($feeds)){
//				$feeds = maybe_unserialize($feeds);
				if(is_array($feeds) && (count($feeds) > 0)){
					if($limit > 0){
						$feeds = array_slice($feeds, 0, $limit);
					}
					$html = '<ul>';
					foreach($feeds as $ins){
						$html .= '<li>';
						$html .= '<a href="' . esc_url($ins['link']) . '">'; 
						$html .= '<div class="feed-bg" style="background-image: url(' . esc_url($ins['small']) . ');"></div>';
						$html .= '</a>';
						$html .= '</li>';
					}
					$html .= '</ul>';
					return $html;
				}
			}
			return false;
		}
	}
	new LoftOcean_Instagram();
}