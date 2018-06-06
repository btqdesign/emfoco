<?php
/**
* Customize section homepage related frontend render class.
*/

if(!class_exists('Fallsky_Category_Index_Page_Render')){
	class Fallsky_Category_Index_Page_Render{
		private $page_id = '';
		private $is_transparent_header  = false;
		public function __construct(){
			add_action('wp', array($this, 'wp'));
		}
		public function wp(){
			global $fallsky_is_preview;
			$this->page_id = fallsky_get_category_index_page_id();
			$this->is_transparent_header = apply_filters( 'fallsky_transparent_archive_site_header', false );

			if(!empty($this->page_id) && is_page($this->page_id)){ 
				add_filter( 'fallsky_site_header_class', 			array( $this, 'site_header_class' ) );
				add_filter( 'body_class',							array( $this, 'body_class' ), 999 );
				add_filter( 'fallsky_is_page_header_with_bg', 		array( $this, 'is_page_header_with_bg' ) );
				add_filter( 'fallsky_is_transparent_site_header', 	array( $this, 'is_transparent_site_header' ) );
				add_filter( 'fallsky_page_layout_id',				array( $this, 'page_layout_id' ) );
				add_action( 'fallsky_before_main_content', 			array( $this, 'page_header' ) );
				add_action( 'fallsky_main_content', 				array( $this, 'main_content' ) );
			}	
		}
		/**
		* Change the class name for <body>
		* 	1. Remove the page related class name
		*	2. Add category index related class name
		*/
		public function body_class($class){
			$remove = array();
			foreach($class as $c){
				if(('page-header-with-bg' != $c) && (strpos($c, 'page') === 0)){
					array_push($remove, $c);
				}
			}
			$class = array_diff($class, $remove);
			$class = array_merge($class, array('category-index-page', 'archive'));

			return $class;
		}
		/**
		* The filter callback function fallsky_is_page_header_with_bg
		* @param boolean default 
		* @return boolean changed value
		*/
		public function is_page_header_with_bg($has){
			return has_post_thumbnail();
		}
		/*
		* Test if cucrent page is transparent site header
		* @param array of boolean, ['show on frontend', 'not show on frontend, but show on customize preview page']
		* @return array of boolean
		*/
		public function is_transparent_site_header($is = array(false, false)){
			return has_post_thumbnail() && $this->is_transparent_header ? array(true, false) : $is;
		}
		/**
		* Add transparent class to site head if needed
		* @param array class name list
		* @return array
		*/
		public function site_header_class( $class ){
			if( has_post_thumbnail() && $this->is_transparent_header ) {
				array_push( $class, 'transparent' );
			}
			return $class;
		}
		/*
		* Get the theme mod name of page sidebar layout for category index page
		* @param string default value
		* @return string changed value
		*/
		public function page_layout_id( $layout_id ) {
			return 'fallsky_category_index_sidebar';
		}
		/**
		* Output the category index page header
		*/
		public function page_header() {
			$header_media = ''; 
			$page_content = get_the_content();	
			if( has_post_thumbnail() ) {
				do_action( 'fallsky_set_frontend_options', 'page_header' );
				$header_media = sprintf( '<div class="featured-media-section">%s</div>', fallsky_get_preload_bg( array( 'class' => 'header-img' ) ) );
				do_action( 'fallsky_reset_frontend_options' );
			}
			printf(
				'<header class="page-header">%s<div class="page-header-text"><h1 class="page-title">%s</h1></div></header>',
				$header_media,
				get_the_title()
			);
		}
		/**
		* Category index page main content
		*	If have any categories, print the output by using widget category
		*/
		public function main_content() {
			$args = array( 'fields' => 'id=>slug' );
			if( 'top' == fallsky_get_theme_mod( 'fallsky_category_index_categories' ) ) {
				$args['parent'] = 0;
			}
			$categories = get_categories( $args );
			if( !empty( $categories ) ) {
				global $fallsky_is_preview;
				$values = array(
					'title' 		=> '', 
					'categories' 	=> array_values($categories), 
					'style' 		=> fallsky_get_theme_mod( 'fallsky_category_index_style' ),
					'layout'		=> fallsky_get_theme_mod( 'fallsky_category_index_layout' ),
					'show-count' 	=> fallsky_get_theme_mod( 'fallsky_category_index_show_post_count' ),
					'is_preview'	=> $fallsky_is_preview
				);
				the_widget( 'Fallsky_Widget_Category', $values );
			}
		}
	}
}