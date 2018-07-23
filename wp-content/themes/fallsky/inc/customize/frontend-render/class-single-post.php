<?php
/**
* Single post related frontend render class.
*/

if ( ! class_exists( 'Fallsky_Single_Post_Render' ) ) {
	class Fallsky_Single_Post_Render {
		private $template 			= '';
		private $transparent_header = false;
		private $comment_location 	= '';
		private $featured_media 	= '';
		private $image_caption 		= '';
		private $post_format 		= '';
		private $format_media 		= '';
		private $play_button		= '';
		private $btn_in_text 		= false;
		private $outer_header 		= '';
		private $inner_header 		= '';
		private $post_nav 			= false;
		private $side_share			= false;
		private $icons_html 		= NULL;
		public function __construct() {
			global $fallsky_list_args;
			// The class will be intialized in action wp, so it's safe to call all functions
			$this->template					= apply_filters( 'fallsky_get_post_template', '' );
			$this->is_transparent_header 	= fallsky_module_enabled( 'fallsky_single_post_template1_transparent_site_header' );
			$this->post_format 				= get_post_format();
			$this->comment_location 		= fallsky_get_theme_mod( 'fallsky_comment_location' );
			if ( empty( $this->template ) ) {
				$this->template = fallsky_get_theme_mod( 'fallsky_single_post_default_template' );
			}

			$this->set_globals();
			$this->set_format_media();
			$this->featured_media_section();
			$this->generate_page_headers();

			$this->post_nav 	= fallsky_module_enabled( 'fallsky_sticky_post_nav' );
			$this->side_share 	= fallsky_module_enabled( 'fallsky_single_post_show_sharing_buttons' );

			add_filter( 'fallsky_is_page_header_with_bg', 		array( $this, 'is_page_header_with_bg' ) );
			add_filter( 'fallsky_is_transparent_site_header', 	array( $this, 'is_transparent_site_header' ) );
			add_filter( 'fallsky_site_header_class', 			array( $this, 'site_header_class' ) );
			add_filter( 'fallsky_page_layout', 					array( $this, 'page_layout' ), 999 );
			add_filter( 'body_class',							array( $this, 'body_class' ), 999 );
			add_action( 'fallsky_post_nav',						array( $this, 'post_navigation' ) );
			add_action( 'fallsky_before_main_content', 			array( $this, 'before_main_content' ) );
			add_action( 'fallsky_single_post_main_content', 	array( $this, 'main_content' ) );
			add_action( 'fallsky_after_main_content', 			array( $this, 'after_main_content' ), 5 );
			add_action( 'wp_footer',							array( $this, 'add_media' ) );
		}
		/**
		* Add transparent class to site head if needed
		* @param array class name list
		* @return array
		*/
		public function site_header_class($class){
			if ( ( 'post-template-1' == $this->template ) && $this->is_transparent_header && !empty( $this->featured_media ) ){
				array_push( $class, 'transparent' );
			}

			return $class;
		}
		/**
		* If current page header with bg
		* 	1. Return true if has post thumbnail for page header layout 2/3
		*	2. Otherwise return false
		* @param boolean default value
		* @return boolean
		*/
		public function is_page_header_with_bg( $has ) {
			return ! empty( $this->featured_media );
		}
		/*
		* Test if cucrent page is transparent site header
		* @param array of boolean, ['show on frontend', 'not show on frontend, but show on customize preview page']
		* @return array of boolean
		*/
		public function is_transparent_site_header( $is = array( false, false ) ) {
			return ( 'post-template-1' == $this->template ) && $this->is_transparent_header && ! empty( $this->featured_media ) ? array( true, false ) : $is;
		}
		/*
		* Get current page layout for pages
		* @param string default layout
		* @return string layout
		*/
		public function page_layout( $layout ) {
			$layout = apply_filters( 'fallsky_get_post_sidebar_layout', '' );
			switch ( $layout ) {
				case 'with-sidebar-left':
				case 'with-sidebar-right':
					return $layout;
				case 'no-sidebar':
					return '';
				default:
					return fallsky_get_theme_mod('fallsky_single_post_default_sidebar');
			}
		}
		/**
		* Change the class name for <body>
		* 	1. Remove the page related class name
		*	2. Add category index related class name
		*/
		public function body_class( $class ) {
			array_push( $class, $this->template );
			if ( $this->side_share ) {
				array_push( $class, 'side-share-enabled' );
			}

			return $class;
		}
		/**
		* Output the post sticky navigation
		*/
		public function post_navigation() {
			global $fallsky_is_preview;
			if ( $this->post_nav || $fallsky_is_preview ) { 
				$class = array( 'post-nav', fallsky_get_theme_mod( 'fallsky_post_nav_color_scheme' ) );
				if ( $fallsky_is_preview && ! $this->post_nav ) {
					array_push( $class, 'hide' ); 
				} ?>

				<div class="<?php echo implode( ' ', $class ); ?>">
					<div class="container">
						<?php $this->breadcrumb(); ?>
						<?php $this->icons(); ?>
						<div id="post-nav-site-search">
							<span class="search-button"><span class="screen-reader-text"><?php esc_html_e( 'Search', 'fallsky' ); ?></span></span>
						</div>
					</div>
				</div> <?php
			}
		}
		/**
		* Output the outer page header
		*/
		public function before_main_content() {
			print( $this->outer_header );
		}
		/**
		* Output the main content
		*/
		public function main_content() { 
			$this->side_modules(); ?>
			<div class="container">
				<div id="primary" class="content-area"><?php $this->post_content(); $this->after_modules(); ?></div> 
			</div> <?php
		}
		/**
		* Output the content after main content
		*/
		public function after_main_content() {
			ob_start();
			if ( fallsky_module_enabled( 'fallsky_single_post_show_pagination' ) ) {
				$this->post_pagination();
			}
			if ( fallsky_module_enabled( 'fallsky_single_post_show_related_posts' ) ) {
				$this->related_posts();
			}
			// Comments
			if ( 'after_main_content' == $this->comment_location ) {
				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
			}
			$content = ob_get_clean();
			if ( ! empty( $content ) ) {
				printf( '<div class="content-after-post">%s</div>', $content );
			}
		}
		/**
		* Output the audio html
		*/
		public function add_media() {
			if ( in_array( $this->post_format, array( 'audio', 'video' ) ) && ! empty( $this->format_media ) ) {
				printf( '<script type="text/html" id="fallsky-single-post-media">%s</script>', $this->format_media );
			}
		}
		/**
		* Change global settings
		*/
		private function set_globals() {
			$page_layout = $this->page_layout( '' );
			$GLOBALS['content_width'] = empty( $page_layout ) ? 1000 : 790;
		}
		/**
		* Set featured media section
		* 	Using class property $featured_media
		*/
		private function featured_media_section() {
			do_action( 'fallsky_set_frontend_options', 'post-header', array( 'template' => $this->template ) );
			$has_thumbnail 	= has_post_thumbnail();
			$gallery 		= fallsky_get_post_gallery();
			$is_gallery 	= ( 'gallery' == $this->post_format ) && ! empty( $gallery );
			$has_play_btn 	= ! empty( $this->play_button ) && !$this->btn_in_text;
			$is_image 		= in_array( $this->template, array( 'post-template-4', 'post-template-5' ) );
			$image_caption 	= ''; 
			if ( ! $is_gallery && $has_thumbnail ) {
				if ( in_array( $this->template, array( 'post-template-1', 'post-template-6' ) ) ) {
					$this->image_caption = fallsky_get_featured_image_caption_html();
				} else {
					$image_caption = fallsky_get_featured_image_caption_html();
				}
			}
			if ( $is_gallery || $has_thumbnail || $has_play_btn ) {
				$this->featured_media = sprintf(
					'<div class="featured-media-section">%s%s%s</div>',
					$is_gallery ? $gallery : ( $has_thumbnail ? ( $is_image ? $this->get_featured_image() : $this->get_featured_bg_image() ) : '' ),
					$has_play_btn ? $this->play_button : '',
					$image_caption
				);
			}
			do_action( 'fallsky_reset_frontend_options' );
		}
		/**
		* Generate the page header for single post
		*  Using class property $outer_header/$inner_header
		*/
		private function generate_page_headers() {
			$template 		= $this->template;
			$header_class 	= array( 'post-header' );
			$media_section 	= $this->featured_media;
			$header_title 	= the_title( '<h1 class="post-title">', '</h1>', false );
			$post_category 	= fallsky_module_enabled( 'fallsky_single_post_show_category' ) ? fallsky_get_post_categories( array(), false ) : '';
			if ( ! empty( $media_section ) ) {
				array_push( $header_class, 'has-post-thumbnail' );
			}
			$header_class 	= implode( ' ', $header_class );
			switch ( $template ) {
				case 'post-template-1':
				case 'post-template-7':
					$this->outer_header = sprintf(
						sprintf(
							'<header class="%s">%s%s</header>',
							$header_class,
							( 'post-template-7' == $template ) ? '%2$s%1$s' : '%1$s%2$s',
							$this->image_caption
						),
						$media_section,
						sprintf(
							'<div class="post-header-text">%s%s%s</div>',
							$this->btn_in_text ? $this->play_button : '',
							$header_title,
							$post_category
						)
					);
					break;
				case 'post-template-2':
				case 'post-template-3':
					$this->outer_header = $media_section;
					$this->inner_header = sprintf(
						'<header class="%s">%s<div class="post-header-text">%s%s</div></header>',
						$header_class,
						$this->image_caption,
						$header_title,
						$post_category
					);
					break;
				case 'post-template-4':
				case 'post-template-5':
				case 'post-template-6':
					$this->inner_header = sprintf(
						sprintf(
							'<header class="%s">%s%s</header>',
							$header_class,
							('post-template-5' == $template) ? '%2$s%1$s' : '%1$s%2$s',
							$this->image_caption
						),
						$media_section,
						sprintf(
							'<div class="post-header-text">%s%s%s</div>',
							$this->btn_in_text ? $this->play_button : '',
							$header_title,
							$post_category
						)
					);
					break;
			}
		}
		/**
		* Print post main content
		*/
		private function post_content() {
			$show_author 	= fallsky_module_enabled( 'fallsky_single_post_show_author' );
			$show_date  	= fallsky_module_enabled( 'fallsky_single_post_show_date' );
			$show_view	 	= fallsky_module_enabled( 'fallsky_single_post_show_view' );
			$show_like 		= fallsky_module_enabled( 'fallsky_single_post_show_like' );
			$show_comment 	= fallsky_module_enabled( 'fallsky_single_post_show_comment_count' );
			$show_tags 	 	= fallsky_module_enabled( 'fallsky_single_post_show_tags' ); 
			while ( have_posts() ) {
				the_post(); ?>
				<article <?php post_class(); ?>>
					<?php print( $this->inner_header ); ?>
					<?php do_action( 'fallsky_ads', 'before_single_post_content' ); ?>
					<div class="post-entry"><?php the_content(); ?></div><!-- end of post-entry  -->
					<?php
						wp_link_pages( array(
							'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__('Pages:', 'fallsky') . '</span>',
							'after'       => '</div>',
							'link_before' => '<span>',
							'link_after'  => '</span>'
						) );
					?>
					<?php do_action( 'fallsky_ads', 'after_single_post_content' ); ?>
					<?php if ( $show_tags ){ fallsky_post_tags(); } ?>
					<?php if ( $show_author || $show_date || $show_view || current_user_can( 'edit_post', get_the_ID() ) ) : ?>
					<footer class="post-meta">
						<?php if ( $show_author ) { fallsky_meta_author(); } ?>
						<?php if ( $show_date ) { fallsky_meta_date(); } ?>
						<?php if ( $show_view ) { fallsky_meta_view( 'view' ); } ?>
						<?php fallsky_meta_edit_link(); ?>
					</footer>
					<?php endif; ?>
					<?php $this->icons( $show_like, $show_comment ); ?>
				</article><?php
			}
			wp_reset_postdata();
		}
		/**
		* Set the format media and media play button for audio/video
		* 	Using class property $format_media and $play_button 
		*/
		private function set_format_media() {
			$format = $this->post_format;
			if ( in_array( $format, array( 'audio', 'video' ) ) ) {
				$media = apply_filters( 'loftocean_get_post_format_media', false );
				if ( ! empty( $media ) ) {
					$this->format_media = $media;
					$this->btn_in_text 	= in_array( $this->template, array( 'post-template-1', 'post-template-6' ) );
					$this->play_button	= sprintf(
						'<div class="%s"></div>', 
						( 'audio' == $format ) ? 'play-audio-btn' : 'play-video-btn'
					);
				}
			}
		}
		/**
		* Print the side modules for post
		*/
		private function side_modules() {
			if ( $this->side_share ) {
				fallsky_share_buttons(); 
			}
			do_action( 'fallsky_single_post_side_modules' );
		}
		/**
		* Print the modules after main post content
		*/
		private function after_modules() {
			// Author info box
			if ( fallsky_module_enabled( 'fallsky_single_post_show_author_info_box' ) ){
				$this->list_author_info();
			}
			// Signup form
			$mc4wp_form = fallsky_get_theme_mod( 'fallsky_single_post_signup_form_id' );
			$has_mc4wp 	= function_exists( 'mc4wp' ) && fallsky_module_enabled( 'fallsky_single_post_show_signup_form' ) && ! empty( $mc4wp_form );
			if ( $has_mc4wp ) {
				$this->signup_form( $mc4wp_form );
			}
			// Comments
			if ( 'after_post_content' == $this->comment_location ) {
				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
			}

			do_action( 'fallsky_single_post_after_content_modules' );
		}
		/**
		* Helper function to get the breadcrumb for post nav
		*/
		private function breadcrumb() {
			$categories = get_the_category();
			$first_cat 	= $categories[0];
			printf(
				'<div class="breadcrumb">%s%s<span class="current-position">%s</span></div>',
				sprintf( '<a href="%s">%s</a> &gt; ', esc_url( home_url( '/' ) ), esc_html__( 'Home', 'fallsky' ) ),
				sprintf( '<a href="%s">%s</a> &gt; ', get_category_link( $first_cat), esc_html( $first_cat->name ) ),
				get_the_title()
			);
		}
		/**
		* Show comment/like icons
		* @param boolean whether to show like icon
		* @param boolean whether to show comment icon
		*/
		private function icons( $show_like = true, $show_comment = true ) {
			$comment_number 	= get_comments_number();
			$has_comment 		= $show_comment && ( comments_open() || ! empty( $comment_number ) );
			$has_like_count 	= class_exists( 'Fallsky_Extension' ) && $show_like;
			$like_number 		= apply_filters( 'loftocean_like_count_number', 0 ); 
			$this->icons_html 	= '';
			if ( $has_comment || $has_like_count ) {
				printf(
					'<aside class="article-like-comment">%s%s</aside>',
					$has_comment && ! post_password_required() ? sprintf(
						'<div class="article-comment"><a href="#comments"><span class="icon_comment_alt"></span><span class="counts">%s</span></a></div>',
						$comment_number
					) : '',
					$has_like_count ? apply_filters( 'loftocean_clickable_like', '' ) : ''
				);	
			}
		}
		/**
		* Helper function to list all authors for current post
		*/
		private function list_author_info() {
			global $authordata;
			$original_user 	= $authordata;

			$authors 	= fallsky_get_post_authors();
			$author_bio	= array();
			foreach ( $authors as $author ) { 
				$author_id 		= $author->ID;
				$authordata 	= get_userdata( $author_id );
				$name 			= $author->display_name;
				$url 			= get_author_posts_url( $author_id );
				$avatar 		= get_avatar( $author->user_email, 150 );
				$description 	= get_the_author_meta( 'description' );
				$socials 		= fallsky_author_socials( false );
				if ( ! empty( $description ) ) {
					$author_bio[] 	= sprintf(
						'<div class="author-bio"><div class="author-bio-top">%s<div class="author-info"><h4 class="author-name"><a href="%s">%s</a></h4>%s</div></div>%s</div>',
						empty( $avatar ) ? '' : sprintf( '<div class="author-photo">%s</div>', $avatar ),
						esc_url( $url ),
						esc_html( $name ),
						empty( $socials ) ? '' : $socials,
						sprintf( '<div class="author-bio-text">%s</div>', apply_filters( 'widget_text_content', $description ) )
					);
				}
			}
			$authordata = $original_user;
			if ( ! empty( $author_bio ) ) {
				printf( '<aside class="author-info-box">%s</aside>', implode( '', $author_bio ) );
			}
		}
		/**
		* Get signup form
		* @param string mc4wp form id
		*/
		private function signup_form( $mc4wp_form ) {
			$form = get_post( $mc4wp_form );
			echo '<aside class="signup-form">';
			the_widget( 'MC4WP_Form_Widget', array( 'title' => apply_filters( 'the_title', $form->post_title ), 'form-id' => $mc4wp_form ) );
			echo '</aside>';
		}
		/*
		* Get post pagination
		*/
		private function post_pagination() {
			$nav = get_the_post_navigation( array(
				'next_text' => '[[FALLSKY_POST_NAV_NEXT_BG]]<span class="meta-nav" aria-hidden="true">' . esc_html__( 'Next Article', 'fallsky' ) . '</span> ' .
					'<span class="screen-reader-text">' . esc_html__( 'Next post:', 'fallsky' ) . '</span> ' .
					'<span class="post-title">%title</span>',
				'prev_text' => '[[FALLSKY_POST_NAV_PREV_BG]]<span class="meta-nav" aria-hidden="true">' . esc_html__( 'Previous Article', 'fallsky' ) . '</span> ' .
					'<span class="screen-reader-text">' . esc_html__( 'Previous post:', 'fallsky' ) . '</span> ' .
					'<span class="post-title">%title</span>',
			) );

			$prev = get_adjacent_post( false, '', true, 'category' );
			$next = get_adjacent_post( false, '', false, 'category' );

			do_action( 'fallsky_set_frontend_options', 'single_post_nav' );
			$prev = ( $prev && get_post_thumbnail_id( $prev->ID ) ) ? fallsky_get_preload_bg( array( 'class' => 'post-img', 'id' => get_post_thumbnail_id( $prev->ID ) ) ) : '';
			$next = ( $next && get_post_thumbnail_id( $next->ID ) ) ? fallsky_get_preload_bg( array( 'class' => 'post-img', 'id' => get_post_thumbnail_id( $next->ID ) ) ) : '';
			do_action( 'fallsky_reset_frontend_options' );

			echo str_replace( array( '[[FALLSKY_POST_NAV_PREV_BG]]', '[[FALLSKY_POST_NAV_NEXT_BG]]' ), array( $prev, $next ), $nav );
		}
		/**
		* Get related posts
		*/
		private function related_posts() {
			$number 	= intval( fallsky_get_theme_mod( 'fallsky_single_post_related_post_number' ) );
			$number 	= in_array( $number, array( 3, 6 ) ) ? $number : 6;
			$posts 		= apply_filters( 'loftocean_related_posts', '', esc_attr(fallsky_get_theme_mod( 'fallsky_single_post_related_posts_by' ) ), $number );
			$wrap 		= '<div class="related-posts">%s<div class="related-posts-container">%s</div></div>';
			$tmpl 		= '<article class="related-post%s">%s%s</article>';
			$media 		= '<div class="featured-img">%s</div>';
			$content 	= '<div class="post-content"><header class="post-header"><h4 class="post-title"><a href="%s">%s</a></h4>%s</header></div>';
			$items 		= array();
			if ( is_object( $posts ) && ( get_class( $posts ) == 'WP_Query') && $posts->have_posts() ) {
				$title = esc_html( fallsky_get_theme_mod( 'fallsky_single_post_related_posts_title' ) );
				do_action( 'fallsky_set_frontend_options', 'related_posts' );
				while ( $posts->have_posts() ) {
					$posts->the_post();
					$post_url 		= get_permalink();
					$has_thumbnail 	= has_post_thumbnail();
					$items[]		= sprintf(
						$tmpl,
						$has_thumbnail ? ' has-post-thumbnail' : '',
						$has_thumbnail ? sprintf( $media, fallsky_get_preload_bg( array(
							'class' => '', 
							'html' 	=> fallsky_get_responsive_image(), 
							'tag' 	=> 'a', 
							'attrs' => array( 'href' => $post_url )
						) ) ) : '',
						sprintf(
							$content, 
							$post_url, 
							get_the_title(),
							fallsky_meta_date( false )
						)
					);
				}
				wp_reset_postdata();
				do_action( 'fallsky_reset_frontend_options' );	
				printf(
					'<div class="related-posts">%s<div class="related-posts-container column-%s">%s</div></div>',
					!empty( $title ) ? sprintf( '<h4 class="related-posts-title">%s</h4>', $title ) : '',
					$number,
					implode( '', $items )
				);
			}
		}
		/**
		* Helper function to get featured image as background
		*/
		private function get_featured_bg_image() {
			return fallsky_get_preload_bg( array( 'class' => 'header-img' ) );
		}
		/**
		* Helper function to get featured image as image
		*/
		private function get_featured_image() {
			return sprintf( '<figure class="header-img">%s</figure>', fallsky_get_responsive_image() );
		}
	}
	new Fallsky_Single_Post_Render();
}