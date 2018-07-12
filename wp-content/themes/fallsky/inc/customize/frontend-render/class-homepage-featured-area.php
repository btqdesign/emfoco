<?php
/**
* Customize section homepage related frontend render class.
*/

if(!class_exists('Fallsky_Customize_Homepage_Featured_Frontend_Render')){
	class Fallsky_Customize_Homepage_Featured_Frontend_Render{
		private $category_args = array();
		public function __construct(){
			$this->category_args = array('seperator' => ', ', 'wrap_class' => 'cat-links',);
			add_action('pre_get_posts', 									array($this, 'exclude_featured_area_posts'), 5);
			add_filter('fallsky_frontpage_widget_exclude_featured_posts',	array($this, 'widget_exclude_featured_posts'));
			add_filter('customize_render_partials_response', 				array($this, 'export_custom_content_bg_video_settings'), 10, 3);
		}
		public function widget_exclude_featured_posts($query){
			if(fallsky_is_front_page() || fallsky_is_ajax_on_frontpage()){
				$show = fallsky_module_enabled('fallsky_home_show_fullwidth_featured_area');
				$type = fallsky_get_theme_mod('fallsky_home_fullwidth_featured_area_type');
				$hide = fallsky_module_enabled('fallsky_home_exclude_posts_from_main_content');
				if($show && $hide && in_array($type, array('posts-slider', 'posts-blocks'))){
					$exclude_posts = $this->get_posts(array('fields' => 'ids'));
					if($exclude_posts->have_posts()){
						$query['post__not_in'] = array_unique($exclude_posts->posts);
					}
				}
			}
			return $query;
		}
		public function exclude_featured_area_posts($query){
			if($query->is_main_query() && fallsky_is_front_page()){
				$show = fallsky_module_enabled('fallsky_home_show_fullwidth_featured_area');
				$type = fallsky_get_theme_mod('fallsky_home_fullwidth_featured_area_type');
				$hide = fallsky_module_enabled('fallsky_home_exclude_posts_from_main_content');
				if($show && $hide && in_array($type, array('posts-slider', 'posts-blocks'))){
					$exclude_posts = $this->get_posts(array('fields' => 'ids'));
					$exclude_posts->have_posts() ? $query->set('post__not_in', array_unique($exclude_posts->posts)) : '';
				}
			}
		}
		private function get_posts($args = array()){
			$ppp = 3;
			if('posts-slider' == fallsky_get_theme_mod('fallsky_home_fullwidth_featured_area_type')){
				$ppp = absint(fallsky_get_theme_mod('fallsky_home_posts_slider_post_number'));
				$ppp = (empty($ppp) || ($ppp < 0) || ($ppp > 8)) ? 3 : $ppp;
			}
			$args = array_merge(array(
				'posts_per_page'		=> $ppp, 
				'offset' 				=> 0, 
				'paged'					=> 1,
				'ignore_sticky_posts' 	=> true, 
				'post_type' 			=> 'post'
			), $args);

			$filter = fallsky_get_theme_mod('fallsky_home_posts_by');
			switch($filter){
				case 'category':
					$category_ids = fallsky_convert_tax_slug2id((array)fallsky_get_theme_mod('fallsky_home_categories'));
					if($category_ids){
						$args['category__in'] = $category_ids;
					}
					break;
				case 'comments':
					$args = array_merge($args, array('orderby' => 'comment_count', 'order' => 'DESC'));
					break;
				default:
					$args = apply_filters('loftocean_posts_args', $args, $filter);	
			}
			return new WP_Query($args);

		}
		private function get_slider_content(){
			global $fallsky_is_preview;

			$posts = $this->get_posts();
			if($posts->have_posts()){
				$style 			= fallsky_get_theme_mod('fallsky_home_posts_slider_style');
				$hide_excerpt 	= fallsky_module_enabled('fallsky_home_posts_hide_excerpt');
				$hide_category 	= fallsky_module_enabled('fallsky_home_posts_slider_hide_category');
				$category_args 	= $this->category_args;
				$content_class 	= 'post-content';
				$arrows 		= $style == 'style-slider-3' ? '' : '<div class="slider-arrows"></div>';
				$item_list 		= array();
				$main_tmpl 		=  '<div class="top-slider %s"%s data-slider-number="%s"><div class="slider-wrapper">%s</div>%s</div>';
				$bg_tmpl  		= '<div class="post-bg"><a class="post-link" href="%s">%s</a></div>';
				$item_tmpl 		= '<article class="post"%s>%s<div class="%s"><h2 class="post-title"><a href="%s">%s</a></h2>%s%s</div></article>';

				if($hide_category && $fallsky_is_preview){
					$category_args['wrap_class'] = sprintf('%s hide', $category_args['wrap_class']);
				}
				while( $posts->have_posts() ) {
					$posts->the_post();
					$post_title 	= apply_filters('the_title', get_the_title());
					$post_url 		= get_permalink();
					$excerpt 		= get_the_excerpt();
					$categories 	= fallsky_get_post_categories($category_args, false);
					$bg_image 		= has_post_thumbnail() ? sprintf(
						$bg_tmpl, 
						$post_url, 
						fallsky_get_preload_bg( array( 'id' => get_post_thumbnail_id(), 'class' => 'post-bg-img' ) )
					) : '';
					$item_list[] 	= sprintf(
						$item_tmpl,
						$posts->current_post > 0 ? ' style="display: none;"' : '',
						$bg_image,
						$content_class,
						$post_url,
						$post_title,
						$hide_category && !$fallsky_is_preview ? '' : $categories,
						( $hide_excerpt && !$fallsky_is_preview ) || empty( $excerpt ) ? '' : sprintf(
							'<div class="post-excerpt%s">%s</div>', 
							$fallsky_is_preview && $hide_excerpt ? ' hide' : '',
							$excerpt
						)
					);
				}
				wp_reset_postdata();
				return sprintf(
					$main_tmpl,
					$style,
					$fallsky_is_preview ? sprintf(' data-style="%s"', $style) : '',
					$posts->post_count,
					implode('', $item_list),
					$arrows
				);
			}
		}
		private function get_block_content(){
			global $fallsky_is_preview;

			$posts = $this->get_posts();
			if($posts->have_posts()){
				$post_list  	= array();
				$bg_list 		= array();
				$type 			= fallsky_get_theme_mod('fallsky_home_posts_blocks_style');
				$tmpl 			= '<div class="top-blocks %s">%s<div class="blocks-wrapper">%s</div></div>';
				$tmpl_bg 		= '<div class="blocks-3-bg">%s</div>';
				$is_type1 		= $type == 'style-blocks-1';
				$is_type3 		= $type == 'style-blocks-3';
				$hide_category 	= fallsky_module_enabled('fallsky_home_posts_block_hide_category');
				$category_args 	= $this->category_args;
				if($hide_category && $fallsky_is_preview){
					$category_args['wrap_class'] = sprintf('%s hide', $category_args['wrap_class']);
				}
				if($is_type3){
					$bg_wrap 	= '<div class="post-bg%s" id="featured-post-id-%s">%s</div>';
					$item_wrap 	= '<article class="post%5$s" data-post-id="%4$s"><div class="post-content"><h2 class="post-title"><a href="%2$s">%1$s</a></h2>%3$s</div><a class="post-link" href="%2$s"></a></article>';
				}
				else{
					$bg_wrap 	= '<div class="post-bg"><a class="post-link" href="%s">%s</a></div>';
					$item_wrap	= '<article class="post%5$s">%4$s<div class="post-content"><h2 class="post-title"><a href="%2$s">%1$s</a></h2>%3$s</div></article>';
				}
				while($posts->have_posts()){
					$posts->the_post(); 
					if($is_type1 && (1 === $posts->current_post)){
						do_action('fallsky_update_frontend_options', 'homepage_blocks');
					}
					$post_id 	= get_the_ID();
					$post_title = apply_filters('post_title', get_the_title());
					$post_url 	= get_permalink();
					$has_thumb 	= has_post_thumbnail();
					$image_bg 	= $has_thumb ? fallsky_get_preload_bg( array( 'id' => get_post_thumbnail_id(), 'class' => 'post-bg-img' ) ) : false;

					$post_list[] = sprintf(
						$item_wrap,
						$post_title,
						$post_url,
						$is_type3 ? '' : ($hide_category && !$fallsky_is_preview ? '' : fallsky_get_post_categories($category_args, false)),
						$is_type3 ? $post_id : sprintf($bg_wrap, $post_url, $image_bg),
						$is_type3 ? '' : ($has_thumb ? '' : ' without-featured-img')
					);
					if($is_type3 && $image_bg){
						$bg_list[] = sprintf(
							$bg_wrap,
							$posts->current_post === 0 ? ' active' : '',
							$post_id, 
							$image_bg
						);
					}
				}
				wp_reset_postdata();
				return sprintf(
					$tmpl,
					$type,
					$is_type3 && !empty($bg_list) ? sprintf($tmpl_bg, implode('', $bg_list)) : '',
					implode('', $post_list)
				);
			}
		}
		private function get_custom_content(){
			global $fallsky_is_preview;
			$content 	= fallsky_get_theme_mod('fallsky_home_custom_content_editor'); 
			$video 		= fallsky_get_theme_mod('fallsky_home_custom_content_bg_video'); 
			$main_tmpl 	= '<div class="custom-content">%s<div class="container"><div class="content">%s</div></div></div>';

			if($this->get_video_url() || $fallsky_is_preview){
				wp_enqueue_script('fallsky-bg-video');
				wp_localize_script('fallsky-bg-video', 'fallsky_featured_custom_content_bg_video_settings', $this->get_bg_video_settings());
			}
			return sprintf(
				$main_tmpl,
				$this->get_custom_content_bg_html(),
				apply_filters('widget_text_content', wp_make_content_images_responsive($content))
			);
		}
		private function get_content($type){
			switch($type){
				case 'custom':
					do_action('fallsky_set_frontend_options', 'homepage_custom');
					$html = $this->get_custom_content();
					break;
				case 'posts-blocks':
					do_action('fallsky_set_frontend_options', 'homepage_blocks');
					$html = $this->get_block_content();
					break;
				default:
					do_action('fallsky_set_frontend_options', 'homepage_slider');
					$html = $this->get_slider_content();
					break;
			}
			do_action('fallsky_reset_frontend_options');
			return $html;
		}
		public function show_content(){
			$show 	= fallsky_module_enabled('fallsky_home_show_fullwidth_featured_area');
			$type 	= fallsky_get_theme_mod('fallsky_home_fullwidth_featured_area_type');
			$class 	= array(
				'posts-slider' 	=> 'style-slider',
				'posts-blocks' 	=> 'style-blocks',
				'custom'		=> 'style-custom'
			);
			if($show){
				printf(
					'<div class="%s">%s</div>',
					sprintf(
						'featured-section %s', 
						(isset($class[$type]) ? $class[$type] : '')
					),
					$this->get_content($type)
				);
			}
		}
		public function customizer_selective_refresh_show_content(){
			$type 		= fallsky_get_theme_mod('fallsky_home_fullwidth_featured_area_type');
			$excluded 	= fallsky_module_enabled('fallsky_home_exclude_posts_from_main_content');
			if(in_array($type, array('posts-slider', 'posts-blocks')) && $excluded){
				return false;
			}
			else{
				$this->show_content();
			}
		}
		private function get_video_url(){
			$id 	= absint(fallsky_get_theme_mod('fallsky_home_custom_content_bg_video'));
			$url 	= esc_url(fallsky_get_theme_mod('fallsky_home_custom_content_external_bg_video'));
			if($id){
				$url = wp_get_attachment_url($id);
			}
			return empty($url) ? false : esc_url_raw(set_url_scheme($url));
		}
		private function get_bg_video_settings(){
			$height   	= absint(fallsky_get_theme_mod('fallsky_home_custom_content_height'));
			$height 	= max($height, 600);
			$video_url  = $this->get_video_url();
			$video_type = wp_check_filetype($video_url, wp_get_mime_types());

			$settings = array(
				'mimeType'  => '',
				'videoUrl'  => $video_url,
				'width'     => 2000,
				'height'    => $height,
				'minWidth'  => 900,
				'minHeight' => 500
			);

			if(preg_match('#^https?://(?:www\.)?(?:youtube\.com/watch|youtu\.be/)#', $video_url)){
				$settings['mimeType'] = 'video/x-youtube';
			} 
			elseif(!empty($video_type['type'])){
				$settings['mimeType'] = $video_type['type'];
			}

			return $settings;
		}
		public function bg_video_settings(){
			print($this->get_custom_content_bg_html());
		}
		public function export_custom_content_bg_video_settings($response, $selective_refresh, $partials){
			if(isset($partials['fallsky_home_custom_content_bg_video'])){
				$response['fallsky_featured_custom_content_bg_video_settings'] = $this->get_bg_video_settings();
			}

			return $response;
		}
		private function get_custom_content_bg_html() {
			$image_id	= fallsky_get_theme_mod( 'fallsky_home_custom_content_bg_image' );
			$image_src 	= fallsky_get_image_src( $image_id, 'fallsky_large', false );
			$bg_color 	= fallsky_get_theme_mod( 'fallsky_home_custom_content_bg_color' );
			$bg_tmpl 	= '<div class="section-bg">%s</div>';
			$bg_image 	= empty( $image_src ) ? '' : sprintf( '<div class="section-bg-img" style="background-image: url(%s);"></div>', $image_src );

			return empty( $image_src ) && empty( $bg_color ) ? '' : sprintf( $bg_tmpl, $bg_image );
		}
		public function frontend_js_vars($vars = array()){
			global $fallsky_is_preview;
			$show 	= fallsky_module_enabled('fallsky_home_show_fullwidth_featured_area');
			$type 	= fallsky_get_theme_mod('fallsky_home_fullwidth_featured_area_type');
			$style 	= fallsky_get_theme_mod('fallsky_home_posts_slider_style');
			$pause 	= intval(fallsky_get_theme_mod('fallsky_home_posts_slider_auto_play_pause_duration'));
			$args 	= array(
				'autoplay' 		=> fallsky_module_enabled('fallsky_home_posts_slider_auto_play'),
				'autoplaySpeed' => empty($pause) ? 5000 : $pause * 1000,
				'pauseOnHover'	=> fallsky_module_enabled( 'fallsky_home_posts_slider_pause_on_hover' ),
				'rtl' 			=> is_rtl()
			);
			if($fallsky_is_preview){
				$vars['featured_slider_customize'] = array(				
					'style-slider-1' => array_merge($args, array(
						'dots' 				=> true,
						'infinite' 			=> true,
						'speed' 			=> 500,
						'fade' 				=> true,
						'cssEase' 			=> 'linear',
						'appendArrows' 		=> '.slider-arrows',
						'adaptiveHeight' 	=>  true
					)),
					'style-slider-2' => array_merge($args, array(
						'dots' 				=> false,
						'infinite' 			=> true,
						'speed' 			=> 500,
						'fade' 				=> true,
						'cssEase' 			=> 'linear',
						'appendArrows' 		=> '.slider-arrows'
					)),
					'style-slider-3' => array_merge($args, array(
						'dots' 				=> true,
						'arrows' 			=> false,
						'infinite' 			=> true,
						'speed' 			=> 500,
						'fade' 				=> true,
						'cssEase' 			=> 'linear'
					))
				);
			}
			if(($show && in_array($type, array('posts-slider'))) || $fallsky_is_preview){
				$args = array_merge($args, array(
					'dots' 		=> in_array( $style, array( 'style-slider-1', 'style-slider-3' ) ),
					'infinite' 	=> true,
					'speed' 	=> 500,
					'fade' 		=> true,
					'cssEase'	=> 'linear'
				));
				if(in_array($style, array('style-slider-1', 'style-slider-2'))){
					$args['appendArrows'] = '.slider-arrows';
				}
				if(in_array($style, array('style-slider-1'))){
					$args['adaptiveHeight'] = true;
				}
				if(in_array($style, array('style-slider-3'))){
					$args['arrows'] = false;
				}

				$vars['featured_slider'] = $args;
			}

			return $vars;
		}
	}
}