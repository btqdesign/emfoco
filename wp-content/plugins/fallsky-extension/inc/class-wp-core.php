<?php
/* 
 *************************************************************************************
 * Initial verison
 *		1. Remove core attributes for w3c html validation
 *		2. $image_size tell the image size for featured background image
 *		3. Add background image preloader to improve user experience
 *************************************************************************************
 */

if(!class_exists('LoftOcean_Core')){
	class LoftOcean_Core {
		private $image_size_filter = '';
		function __construct(){
			add_filter( 'user_contactmethods', 				array( $this, 'user_socials' ) );
			add_filter( 'get_custom_logo', 					array( $this, 'custom_logo' ), 10, 2 );
			add_filter( 'loftocean_get_responsive_image', 	array( $this, 'get_responsive_image' ), 10, 4 );
			add_filter( 'loftocean_get_preload_bg', 		array( $this, 'get_preload_bg' ), 10, 4 );
			add_filter( 'loftocean_get_preload_bg_attrs', 	array( $this, 'get_preload_bg_attrs' ), 10, 3 );
			add_filter( 'loftocean_posts_args', 			array( $this, 'list_post_args' ), 10, 2 );
			remove_filter( 'get_the_excerpt', 				'wpautop' );

			add_action( 'wp_enqueue_scripts', 	array( $this, 'enqueue_scripts' ) );
			add_action( 'loftocean_user_social', array( $this, 'get_user_socials' ) );
		}
		/**
		* @desciption get user social links html
		*/
		public function get_user_socials(){
			$items		= array();
			$user_id 	= get_the_author_meta('ID'); 
			$socials 	= array(
				'website' 	=> array(
					'url' 	=> get_the_author_meta('url'), 	
					'title' => __('Website', 'loftocean')
				),
				'gplus' 	=> array(
					'url' 	=> get_the_author_meta('googleplus'), 
					'title' => __('Google Plus', 'loftocean')
				),
				'twitter' 	=> array(
					'url' 	=> get_user_meta($user_id, 'twitter', true), 
					'title' => __('Twitter', 'loftocean')
				),
				'facebook' 	=> array(
					'url' 	=> get_user_meta($user_id, 'facebook', true), 
					'title' => __('Facebook', 'loftocean')
				),
				'instagram' => array(
					'url' 	=> get_user_meta($user_id, 'instagram', true), 
					'title' => __('Instagram', 'loftocean')
				),
				'pinterest' => array(
					'url' 	=> get_user_meta($user_id, 'pinterest', true), 
					'title' => __('Pinterest', 'loftocean')
				)
			);
			foreach($socials as $id => $attr){
				if(!empty($attr['url'])) array_push($items, sprintf(
					'<li><a href="%s" title="%s">%s</a></li>',
					$attr['url'],
					esc_attr($attr['title']),
					esc_html($attr['title'])
				));
			}
			if(!empty($items)){
				printf(
					'<div class="author-social"><ul class="social-nav">%s</ul></div>',
					implode('', $items)
				);
			}
		}
		/**
		* @description add social links UI in user profile page
		* @return array
		*/
		public function user_socials($profile_fields){
			// Add new fields
			$profile_fields['googleplus'] 	= esc_html__('Google Plus URL', 'loftocean');
			$profile_fields['facebook'] 	= esc_html__('Facebook URL', 	'loftocean');
			$profile_fields['twitter'] 		= esc_html__('Twitter URL', 	'loftocean');
			$profile_fields['instagram'] 	= esc_html__('Instagram URL', 	'loftocean');
			$profile_fields['pinterest'] 	= esc_html__('Pinterest URL', 	'loftocean');

			return $profile_fields;
		}
		// Remove the itemprop attribute
		public function custom_logo($html, $blog_id){
			$html = str_replace(array(' itemprop="url"', ' itemprop="logo"'), '', $html);
			return $html;
		}
		/**
		* Get preload background html
		* @param string previous attribute string
		* @param int image id
		* @param array image sizes ['normal-size', 'retina-size']
		* @return string changed attribute string
		*/
		public function get_preload_bg_attrs( $attrs, $id, $sizes = array( 'full', 'full' ) ) {
			$srcs = $this->get_image_srcs( $id, $sizes );
			if( !empty( $srcs ) ) {
				$attrs = '';
				if( ( $srcs['normal_image_src'] == $srcs['preload_image_src'] ) && ( $srcs['retina_image_src'] == $srcs['preload_image_src'] ) ) {
					$attrs = sprintf( 'background-image: url(%s);', esc_url( $srcs['preload_image_src'] ) );
				}
				else {
					$attrs = sprintf(
						'background-image: url(%s); filter: blur(5px);" data-loftocean-image="1" data-loftocean-normal-image="%s" data-loftocean-retina-image="%s',
						esc_url( $srcs['preload_image_src'] ),
						esc_url( $srcs['normal_image_src'] ),
						esc_url( $srcs['retina_image_src'] )
					);
				}
			}
			return $attrs;
		}
		/**
		* Get preload background html
		* @param int image id
		* @param string image size
		* @param array options
		* @return string html
		*/
		public function get_preload_bg( $html, $id, $sizes = array( 'full', 'full' ), $args = array() ) {
			$preload_attrs = $this->get_preload_bg_attrs( '', $id, $sizes );
			if( !empty( $preload_attrs ) ) {
				$attrs = empty( $args['attrs'] ) ? array() : $args['attrs'];
				if( !empty( $args['class'] ) ) $attrs['class'] = $args['class'];
				$attrs['style'] = sprintf(
					'%s%s', 
					empty( $attrs['style'] ) ? '' : sprintf( '%s ', $attrs['style'] ), 
					$preload_attrs
				);
				return sprintf(
					'<%1$s %2$s>%3$s</%1$s>', 
					empty($args['tag']) ? 'div' : $args['tag'],
					$this->get_attributes($attrs),
					empty($args['html']) ? '' : $args['html']
				);
			}
			return '';
		}
		/**
		* Get preload image html
		* @param int image id
		* @param string image size
		* @param array options
		* @return string html
		*/
		public function get_responsive_image( $html, $id, $size = 'full', $args = array() ) {
			$image_src = $this->get_image_src( $id, $size );
			if( !empty( $image_src ) ) {
				$attrs = empty( $args['attrs'] ) ? array() : $args['attrs'];
				$attrs['alt'] 	= isset( $attrs['alt'] ) ? $attrs['alt'] : $this->get_image_alt( $id );
				return wp_image_add_srcset_and_sizes( sprintf(
					'<img src="%s" %s>', 
					$image_src, 
					$this->get_attributes( $attrs )
				), wp_get_attachment_metadata( $id ), $id );
			}
			return '';
		}
		/**
		* Parse WP_Query arguments for post list
		* @param array 
		* @param string filter
		* @return array
		*/
		public function list_post_args($args, $filter){
			if(!empty($filter)){
				switch($filter){
					case 'featured':
						$args = array_merge($args, array(
							'ignore_sticky_posts' 	=> true,
							'meta_key' 				=> 'loftocean-featured-post',
							'meta_value'			=> 'on'
						));
						break;
					case 'views':
						$args = array_merge($args, array(
							'ignore_sticky_posts' 	=> true,
							'orderby'  				=> 'meta_value_num',
							'meta_key'  			=> 'loftocean-view-count',
							'order' 				=> 'DESC'
						));
						break;
					case 'likes':
						$args = array_merge($args, array(
							'ignore_sticky_posts' 	=> true,
							'orderby'   			=> 'meta_value_num',
							'meta_key'  			=> 'loftocean-like-count',
							'order' 				=> 'DESC'
						));
						break;
				}
			}
			return $args;
		}
		/**
		* Helpre function to get the image srcs
		* @param int image id
		* @param array image sizes ['normal-size', 'retina-size']
		* @return mix array if exists, otherwise boolean false
		*/
		private function get_image_srcs( $id, $sizes = array( 'full', 'full' ) ) {
			return get_post($id) ? array(
				'preload_image_src' => $this->get_image_src( $id, 'medium' ),
				'normal_image_src' 	=> $this->get_image_src( $id, $sizes[0] ),
				'retina_image_src'	=> $this->get_image_src( $id, $sizes[1] )
			) : false;
		}
		/**
		* Helpre function to get the image src
		* @param int image id
		* @param array image size
		* @return mix string if exists, otherwise boolean false
		*/
		private function get_image_src( $id, $size = 'full' ) {
			if( get_post( $id ) ) {
				$image = wp_get_attachment_image_src( $id, $size );
				return $image[0];
			} 
			return false;
		}
		/**
		* Helper function to get the html attributes
		* @param array attributes
		* @return string html attributes
		*/
		private function get_attributes( $attrs ) {
			if( !empty( $attrs ) && is_array( $attrs ) ) {
				$items = array();
				foreach( $attrs as $key => $item ) { 
					if( !empty( $key ) ) { 
						$item = ( 'style' == $key ) ? $item : esc_attr( $item );
						$items[] = sprintf( '%s="%s"', esc_attr( $key ),  $item );
					}
				}
				return implode( ' ', $items );
			}
			return is_string( $attrs ) ? $attrs : '';
		}
		/**
		* Get image alt text
		* @param int image id
		* @return string image alt text
		*/
		function get_image_alt( $image_id ) {
			if( !empty( $image_id ) && ( false !== get_post_status( $image_id ) ) ) {
				$alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
				if( empty( $alt ) ) {
					$attachment = get_post( $image_id );
					return empty( $attachment->post_title ) ? esc_attr( $attachment->post_name ) : esc_attr( $attachment->post_title );
				}
				else{
					return esc_attr( $alt );
				}
			}

			return '';
		}
		// Enqueue script for background image preloader
		public function enqueue_scripts() {
			wp_enqueue_script( 
				'loftocean-image-preloader', 
				FALLSKY_PLUGIN_URI . 'assets/js/image-preloader.min.js', 
				array('jquery'), 
				FALLSKY_PLUGIN_ASSETS_VERSION, 
				true 
			);
		}
	}
	add_action( 'after_setup_theme', function() { new LoftOcean_Core(); } );
}