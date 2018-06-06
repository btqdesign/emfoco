<?php
/**
* Options for different scenario for frontend 
*/

if(!class_exists('Fallsky_Frontend_Options')){
	class Fallsky_Frontend_Options {
		private $default_options 	= array();
		private $option_keys 		= array();
		private $customized_options = array();
		public function __construct(){ 
			$this->set_variables();

			add_filter( 'excerpt_more', 						array( $this, 'excerpt_more' ), 999 );
			add_filter( 'excerpt_length', 						array( $this, 'excerpt_length' ) );
			add_filter( 'get_the_excerpt', 						array( $this, 'adjust_excerpt' ), 999999 );
			add_filter( 'fallsky_image_sizes',					array( $this, 'get_image_sizes' ) );
			add_filter( 'fallsky_image_size', 					array( $this, 'get_normal_image_size' ) );
			add_filter( 'loftocean_gallery_image_sizes',	 	array( $this, 'gallery_image_sizes' ) );
			add_filter( 'loftocean_popup_gallery_image_sizes', 	array( $this, 'popup_gallery_image_sizes' ) );

			add_action( 'fallsky_set_frontend_options',			array( $this, 'set_frontend_options' ), 10, 2 );
			add_action( 'fallsky_update_frontend_options', 		array( $this, 'update_frontend_options' ), 10, 2 );
			add_action( 'fallsky_reset_frontend_options', 		array( $this, 'reset_frontend_options' ) );
		}
		/**
		* Set frontend options
		*/
		private function set_variables() {
			$this->default_options  = $this->customized_options = array(
				'excerpt_length' 	=> 30,
				'image_sizes'		=> ''
			);
			$this->option_keys = array_keys( $this->default_options );
		}
		/**
		* Action callback function to change the frontend options
		* @param string id to identify current scenario
		*/
		public function set_frontend_options( $id, $options = array() ){
			if( !empty( $id ) ) {
				$id = sprintf( 'get_%s_options', str_replace( '-', '_', sanitize_title( $id ) ) );
				$this->customized_options = $this->default_options;
				$args = is_callable( array( $this, $id ) ) ? call_user_func( array( $this, $id ), $options ) : array();
				if( !empty( $args ) && is_array( $args ) ) {
					foreach( $args as $key => $value ) {
						if( in_array( $key, $this->option_keys ) ) {
							$this->customized_options[$key] = $value;
						}
					}
				}
			}
		}
		/**
		* Action callback function to change the frontend options
		*/
		public function reset_frontend_options(){
			$this->customized_options = $this->default_options;
		}
		/**
		* Action callback function to update the frontend options based on the previous settings
		* @param string id to identify current scenario
		*/
		public function update_frontend_options( $id, $options = array() ) {
			if( !empty($id ) ) {
				$id 	= sprintf( 'get_%s_update_options', str_replace( '-', '_', sanitize_title( $id ) ) );
				$args 	= is_callable( array( $this, $id ) ) ? call_user_func( array( $this, $id ), $options ) : array();
				if( !empty( $args ) && is_array( $args ) ) {
					foreach( $args as $key => $value ) {
						if( in_array( $key, $this->option_keys ) ) {
							$this->customized_options[$key] = $value;
						}
					}
				}
			}
		}
		/**
		* @description change the default read more text
		* @param string default read more text
		* @return string read more text after changed
		*/
		public function excerpt_more( $text ) {
			return '...';
		}
		/**
		* @description change the default post excerpt length
		* @param int default excerpt length
		* @return int excerpt length after changed
		*/
		public function excerpt_length( $length ) {
			return $this->customized_options['excerpt_length'];
		}
		/*
		* Change the default expert content if need
		* 	1. If the post does not have manual post excerpt but has more tag inside post content,
		* 		Will return the content before more tag as excerpt, no matter what the excerpt length set
		*/
		public function adjust_excerpt( $excerpt ) {
			global $post;
			$content = explode( '<!--more-->', $post->post_content, 2 );

			if( has_excerpt() ) { 
				return wp_kses_post( trim( $excerpt ) );
			}
			else if( count( $content ) > 1 ) {
				$excerpt = strip_tags( trim( $content[0] ) );
				return apply_filters( 'loftocean_excerpt_strip_shortcode', $excerpt );
			}
			else {
				return $excerpt;
			}
		}
		/**
		* Get the image size for post/page custom gallery
		*/
		public function gallery_image_sizes( $size ) {
			return array( 'fallsky_medium', 'fallsky_medium_large' );
		}
		/**
		* Get the image size for post/page custom gallery
		*/
		public function popup_gallery_image_sizes($size){
			return array( 'fallsky_large', 'fallsky_large' );
		}
		/**
		* Get image size set currently
		*/
		public function get_image_sizes( $sizes ){
			$image_sizes = $this->customized_options['image_sizes'];
			return empty( $image_sizes ) ? $sizes : $image_sizes;
		}
		public function get_normal_image_size( $size ) {
			$sizes = apply_filters( 'fallsky_image_sizes', '' );
			return empty( $sizes ) ? $size : $sizes[0];
		}
		// For homepage slider
		private function get_homepage_slider_options( $args = array() ) { 
			$options = array( 'excerpt_length' => 17 );
			$options['image_sizes'] = array( 'fallsky_medium_large', 'fallsky_large' );
			return $options;
		}
		// For homepage blocks
		private function get_homepage_blocks_options( $args = array() ) {
			switch( fallsky_get_theme_mod( 'fallsky_home_posts_blocks_style' ) ) {
				case 'style-blocks-1':
				case 'style-blocks-3':
					return array( 'image_sizes' => array( 'fallsky_medium_large', 'fallsky_large' ) );
				case 'style-blocks-2':
					return array( 'image_sizes' => array( 'fallsky_small_medium', 'fallsky_medium' ) );
			}
		}
		// For homepage block style 1 update options
		private function get_homepage_blocks_update_options( $args = array() ) {
			return array( 'image_sizes' => array( 'fallsky_small_medium', 'fallsky_medium' ) );
		}
		// For homepage custom content
		private function get_homepage_custom_options( $args = array() ) { 
			return array( 'image_sizes' => array( 'fallsky_large', 'fallsky_large' ) );
		}
		// For homepage widgets
		private function get_homepage_widgets_options( $args = array() ) { 
			return array( 'image_sizes' => array( 'fallsky_large', 'fallsky_large' ) );
		}
		// For page header background image
		private function get_page_header_options( $args = array() ) { 
			return array( 'image_sizes' => array( 'fallsky_large', 'fallsky_large' ) );
		}
		// For post list
		private function get_post_list_options( $args = array() ) {
			$options = array('image_sizes' => array( 'fallsky_medium_large', 'fallsky_large' ) );
			if( !empty( $args ) && is_array( $args ) && !empty( $args['layout'] ) ) {
				if( in_array( $args['layout'], array( 'masonry', 'list', 'zigzag', 'grid', 'card' ) ) ) {
					$excerpt_length_id = sprintf(
						'fallsky_post_excerpt_length_for_layout_%s%s', 
						$args['layout'],
						( 'list' == $args['layout'] ) && !empty( $args['column'] ) ? sprintf( '_%scol', $args['column'] ) : ''
					);
					$options['excerpt_length'] = intval( fallsky_get_theme_mod( $excerpt_length_id ) );
				}
				switch( $args['layout'] ) {
					case 'standard':
						break;
					case 'masonry':
						$options['image_sizes'] = array( 'fallsky_small', 'fallsky_small' );
						break;
					case 'card':
						if( !empty( $args['column'] ) ) {
							switch( $args['column'] ) {
								case '1':
									break;
								default:
									$options['image_sizes'] = array( 'fallsky_small_medium', 'fallsky_medium' );
							}
						}
						break;
					case 'list':
						if( !empty( $args['column'] ) && ( '2' == $args['column'] ) ) {
							$options['image_sizes'] = array( 'fallsky_small_medium', 'fallsky_small_medium' );
						}
						else{
							$options['image_sizes'] = array( 'fallsky_small_medium', 'fallsky_medium' );
						}
						break;
					case 'grid':
						if( !empty( $args['column'] ) ){
							switch( $args['column'] ) {
								case 2:
									$options['image_sizes'] = array( 'fallsky_small_medium', 'fallsky_medium_large' );
									break;
								default:
									$options['image_sizes'] = array( 'fallsky_small_medium', 'fallsky_medium' );
							}
						}
						break;
					case 'zigzag':
						$options['image_sizes'] = array( 'fallsky_medium', 'fallsky_medium' );
						break;
					case 'overlay':
						if( !empty( $args['column'] ) ) {
							switch( $args['column'] ) {
								case 1:
									break;
								default:
									$options['image_sizes'] = array( 'fallsky_small_medium', 'fallsky_medium' );
							}
						}
						break;
					case 'overlay-mix':
						if( !empty( $args['column'] ) ) {
							switch( $args['column'] ) {
								case '1-2-2-mix':
									break;
								default:
									$options['image_sizes'] = array( 'fallsky_small_medium', 'fallsky_medium' );
							}
						}
						break;
					default:
				}
			}
			return $options;
		}
		// For post list
		private function get_post_list_update_options( $args = array() ) { 
			return array( 'image_sizes' => array( 'fallsky_large', 'fallsky_large' ) );
		}
		// For post header
		private function get_post_header_options( $args = array() ) {
			if( $args['template'] ) {
				switch($args['template']){
					case 'post-template-1':
					case 'post-template-2':
					case 'post-template-3':
					case 'post-template-7':
						return array( 'image_sizes' => array( 'fallsky_large', 'fallsky_large' ) );
					case 'post-template-6':
						return array( 'image_sizes' => array( 'fallsky_medium', 'fallsky_medium_large' ) );
				}
			}
			return array();
		}
		// For homepage and archive widget category
		private function get_widget_category_options( $args ) {
			if( $args['column'] ) {
				switch( $args['column'] ) {
					case 'column-2':
						return array( 'image_sizes' => array( 'fallsky_small_medium', 'fallsky_medium_large' ) );
					case 'column-3':
						return array( 'image_sizes' => array( 'fallsky_small', 'fallsky_small_medium' ) );
					default:
						return array( 'image_sizes' => array( 'fallsky_small', 'fallsky_small' ) );
				}
			}
			return array();
		}
		// For sidebar widget category
		private function get_sidebar_category_options( $args ) {
			return array( 'image_sizes' => array( 'medium', 'fallsky_small' ) );
		}
		// For single post pagination
		private function get_single_post_nav_options( $args ) {
			return array( 'image_sizes' => array( 'fallsky_small_medium', 'fallsky_medium_large' ) );
		}
		// For related post background image
		private function get_related_posts_options( $args ) {
			return array( 'image_sizes' => array( 'fallsky_small', 'fallsky_small' ) );
		}
		// For ajax search result
		private function get_ajax_search_result_options( $args ) {
			return array( 'image_sizes' => array( 'fallsky_small', 'fallsky_small' ) );
		}
		// For widget banner
		private function get_widget_banner_options( $args ) {
			return array( 'image_sizes' => array( 'medium', 'fallsky_small' ) );
		}
		// For mega menu category
		private function get_mega_menu_post_options( $args ) {
			return array( 'image_sizes' => array( 'fallsky_small', 'fallsky_small' ) );
		}
	}
	add_action( 'after_setup_theme', function() { new Fallsky_Frontend_Options(); }, 99 );
}
