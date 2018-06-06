<?php
/**
* Page related frontend render class.
*/

if(!class_exists('Fallsky_Page_Render')){
	class Fallsky_Page_Render {
		private $page_header_layout 	= '';
		private $is_transparent_header 	= false;
		private $is_header_layout1 		= false;
		private $blank_page 			= false;
		private $is_static_front_page 	= false;
		private $static_page_ids 		= array();
		public function __construct(){
			// The class will be initialized in action wp, so it's saft to call all functions
			$queried_object 			= get_queried_object();
			$this->static_page_ids 		= fallsky_get_static_pages(false);
			$this->is_static_front_page = fallsky_is_static_front_page_from_page();
			if((is_page() && !in_array($queried_object->ID, $this->static_page_ids)) || $this->is_static_front_page){
				$this->set_globals();
				$this->page_header_layout 		= apply_filters( 'fallsky_get_page_header_layout', '' );
				$this->is_transparent_header 	= fallsky_module_enabled( 'fallsky_single_page_layout2_transparent_site_header' );
				$this->is_header_layout1		= ( 'page-header-layout-1' == $this->page_header_layout );
				$this->blank_page 				= defined( 'FALLSKY_BLANK_PAGE' ) && FALLSKY_BLANK_PAGE;

				add_filter( 'fallsky_is_page_header_with_bg', 		array( $this, 'is_page_header_with_bg' ) );
				add_filter(	'fallsky_page_layout', 					array( $this, 'page_layout' ), 999 );
				add_filter( 'fallsky_site_header_class', 			array( $this, 'site_header_class' ) );
				add_filter( 'body_class',							array( $this, 'body_class' ), 999 );
				add_filter( 'fallsky_is_transparent_site_header', 	array( $this, 'is_transparent_site_header' ) );
				add_action( 'fallsky_before_main_content', 			array( $this, 'page_header' ) );
				add_action( 'fallsky_main_content', 				array( $this, 'main_content' ) );
			}
		}
		/**
		* If current page header with bg
		* 	1. Always return false for page header layout 1
		*	2. Return true if has post thumbnail for page header layout 2/3
		*	3. Otherwise return false
		* @param boolean default value
		* @return boolean
		*/
		public function is_page_header_with_bg($has){
			return $this->blank_page ? false 
				: ($this->is_header_layout1 ? false : has_post_thumbnail());
		}
		/**
		* Add transparent class to site head if needed
		* @param array class name list
		* @return array
		*/
		public function site_header_class( $class ){
			if( ( 'page-header-layout-2' == $this->page_header_layout ) && $this->is_transparent_header && has_post_thumbnail() ){
				array_push( $class, 'transparent' );
			}
			return $class;
		}
		/*
		* Get current page layout for pages
		* @param string default layout
		* @return string layout
		*/
		public function page_layout($layout){
			if($this->blank_page){
				return '';
			}

			global $post;
			switch(get_page_template_slug($post->ID)){
				case 'template-left-sidebar.php':
					return 'with-sidebar-left';
				case 'template-no-sidebar.php':
					return '';
				default:
					return 'with-sidebar-right';
			}
		}
		/*
		* Test if cucrent page is transparent site header
		* @param array of boolean, ['show on frontend', 'not show on frontend, but show on customize preview page']
		* @return array of boolean
		*/
		public function is_transparent_site_header($is = array(false, false)){
			return ( 'page-header-layout-2' == $this->page_header_layout ) && $this->is_transparent_header && has_post_thumbnail() ? array(true, false) : $is;
		}
		/**
		* Change the class name for <body>
		* 	1. Remove the page related class name
		*	2. Add category index related class name
		*/
		public function body_class($class){
			if(!$this->blank_page){
				array_push($class, $this->page_header_layout);
			}
			return $class;
		}
		/**
		* Output the category index page header
		*/
		public function page_header(){
			$has_thumbnail 	= has_post_thumbnail();
			$image_caption 	= '';
			$media_section 	= '';
			if($has_thumbnail){
				$image_caption = fallsky_get_featured_image_caption_html();
				do_action( 'fallsky_set_frontend_options', 'page_header' );
				$media_section = sprintf(
					'<div class="featured-media-section">%s%s</div>', 
					fallsky_get_preload_bg( array( 'class' => 'header-img' ) ),
					in_array( $this->page_header_layout, array( 'page-header-layout-1', 'page-header-layout-3' ) ) ? $image_caption : ''
				);
				do_action( 'fallsky_reset_frontend_options' );
			}
			if($this->is_header_layout1){
				print($media_section);
			}
			else{
				$is_layout2 = ('page-header-layout-2' == $this->page_header_layout);
				$text_wrap  = $is_layout2 ? '<div class="page-header-text">%s</div>' : '%s';

				printf(
					'<header class="page-header">%s%s%s</header>',
					$media_section,
					$is_layout2 ? $image_caption : '',
					sprintf($text_wrap, sprintf('<h1 class="page-title">%s</h1>', get_the_title()))
				);
			}
		}
		/**
		* Category index page main content
		*	If have any categories, print the output by using widget category
		*/
		public function main_content(){ 
			while(have_posts()){
				the_post(); 
				$pid = get_the_ID();
				$hide_before_ad = get_post_meta($pid, 'fallsky_hide_before_page_content_ad', true);
				$hide_after_ad  = get_post_meta($pid, 'fallsky_hide_after_page_content_ad', true); 
				$hide_before_ad = ('on' == $hide_before_ad);
				$hide_after_ad  = ('on' == $hide_after_ad); ?>
				<article <?php post_class(); ?>> <?php
					if(!$hide_before_ad && !$this->is_header_layout1){
						do_action('fallsky_ads', 'before_single_page_content');
					}
					if($this->is_header_layout1) : ?>
						<header class="page-header">
							<h1 class="page-title"><?php the_title(); ?></h1>
						</header> <?php
						if(!$hide_before_ad){
							do_action('fallsky_ads', 'before_single_page_content');
						}
					endif; ?>
					<div class="post-entry"><?php the_content(); ?></div><!-- end of post-entry  -->
					<?php 
						wp_link_pages( array(
							'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__('Pages:', 'fallsky') . '</span>',
							'after'       => '</div>',
							'link_before' => '<span>',
							'link_after'  => '</span>'
						));
						if(current_user_can('edit_post', $pid)){
							fallsky_page_edit_link();
						}
						if(!$hide_after_ad){ 
							do_action('fallsky_ads', 'after_single_page_content'); 
						} 
					?>
				</article> <?php

				if(comments_open() || get_comments_number()){
					comments_template();
				}
			}
			wp_reset_postdata();
		}
		/**
		* Change global settings
		*/
		private function set_globals(){
			$page_layout 				= $this->page_layout('');
			$GLOBALS['content_width'] 	= empty($page_layout) ? 1000 : 790;
		}
	}
	new Fallsky_Page_Render();
}