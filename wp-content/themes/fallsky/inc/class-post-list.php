<?php
/**
* Theme Post List class
*/

if(!class_exists('Fallsky_Post_List')){
	class Fallsky_Post_List {
		private $is_prev_next_nav = false;
		public function __construct(){
			add_action('fallsky_posts_list', 				array($this, 'post_list'));
			add_action('fallsky_posts_pagination',			array($this, 'post_pagination'), 10, 2);
			add_action('wp_ajax_fallsky_posts_list', 		array($this, 'ajax_post_list'));
			add_action('wp_ajax_nopriv_fallsky_posts_list', array($this, 'ajax_post_list'));

			add_filter('fallsky_widget_posts_list_args', 	array($this, 'widget_posts_args'), 10, 2);
			add_filter('fallsky_frontend_js_vars', 			array($this, 'frontend_js_vars'));
			add_filter('previous_posts_link_attributes', 	array($this, 'previous_posts_link_attrs'));
			add_filter('next_posts_link_attributes', 		array($this, 'next_posts_link_attrs'));
		}
		/**
		* @description display post list html
		* @param array settings
		* 	1. posts 	WP_Query object
		* 	2. layout
		* 	3. columns
		* 	4. post meta to show
		*/
		public function post_list($args){ 
			global $wp_query, $fallsky_list_args;
			$posts = isset($args['posts']) ? $args['posts'] : $wp_query; 
			if($posts->have_posts()){
				$layout 			= isset($args['layout']) ? ('overlay-mix' == $args['layout'] ? 'overlay' : $args['layout']) : 'standard';
				$fallsky_list_args 	= array(
					'show_read_more_btn' 	=> isset($args['show_read_more_btn']) && $args['show_read_more_btn'],
					'card_color'			=> isset($args['card_color']) ? $args['card_color'] : '',
					'read_more'				=> fallsky_get_theme_mod('fallsky_read_more_text'),
					'column'				=> $args['columns'],
					'post_meta'				=> isset($args['post_meta']) ? $args['post_meta'] : array('category', 'author', 'date', 'view' , 'like', 'comment')
				);
				do_action('fallsky_set_frontend_options', 'post_list', array('layout' => $layout, 'column' => $args['columns']));
				printf('<div%s><div class="posts-wrapper">', $this->get_post_list_class($args));
				($layout == 'masonry') ? $this->masonry_post($posts, $args['columns']) : $this->post($posts, $layout);
				print('</div></div>');
				do_action('fallsky_reset_frontend_options');
			}
			else{
				print('<div class="posts no-post-found">');
				get_template_part('template-parts/loop/none');
				print('</div>');
			}
		}
		/**
		* Get post html for other layout styles
		* @param object wp_query object
		* @param string columns
		*/
		private function masonry_post($posts, $cols){
			global $fallsky_list_args;
			$column 	= empty($cols) ? 2 : $cols;
			$items 		= array();
			$columns 	= array();
			while($posts->have_posts()){
				$posts->the_post();
				$post_class = get_post_class();
				$fallsky_list_args['media'] = in_array('format-gallery', $post_class) ? apply_filters('loftocean_get_post_format_media', false) : false;

				ob_start();
				get_template_part('template-parts/loop/post', 'masonry');
				array_push($items, ob_get_clean());
			}
			wp_reset_postdata();
			if(wp_doing_ajax()){
				echo implode('', $items);
			}
			else{
				for($i = $column; $i > 0; $i--){
					$html 	= '';
					$length = floor(count($items) / $i);
					if($length > 0){
						$html 	= implode('', array_slice($items, -$length));	
						$items 	= array_slice($items, 0, -$length);
					}
					array_unshift($columns, sprintf('<div class="masonry-column">%s</div>', $html));
				}
				echo implode('', $columns);
			}
		}
		/**
		* Get post html for other layout styles
		* @param object wp_query object
		* @param string layout style
		*/
		private function post($posts, $layout){
			global $fallsky_list_args;
			while($posts->have_posts()){
				$posts->the_post();
				$post_class = get_post_class();
				$fallsky_list_args['media'] = in_array('format-gallery', $post_class) 
					? apply_filters('loftocean_get_post_format_media', false) : false;

				get_template_part('template-parts/loop/post', $layout);
			}
			wp_reset_postdata();
		}
		/**
		* Show the pagination for post list
		* @param array list arguments
		*/
		public function post_pagination($args){
			global $wp_query;
			$posts 		= isset($args['posts']) ? $args['posts'] : $wp_query;
			$page 		= empty($args['page']) 	? 1 : $args['page'];
			$style 		= fallsky_get_theme_mod('fallsky_pagination_style');
			$data 		= empty($args['data']) ? '' : $this->get_data($args['data']);
			$args 		= array_merge($args, array('max_page_num' => $posts->max_num_pages, 'data' => $data, 'page' => $page));
	
			if(in_array($style, array('next-prev', 'page-number'))){
				global $paged;
				$original 	= array('query' => $wp_query, 'paged' => $paged);
				$wp_query 	= $posts;
				$paged		= min($page, $args['max_page_num']); 
				$this->pagination_next_prev($style);
				$wp_query 	= $original['query'];
				$paged		= $original['paged'];
			}
			else if(in_array($style, array('ajax-more', 'ajax-infinite'))){
				$this->ajax_pagination($args, $style);
			}
		}
		/**
		* For ajax based pagination
		* @param array arguments max page num and page
		* @param string pagination style
		*/
		private function ajax_pagination($args, $style){
			if($args['max_page_num'] > $args['page']){
				$styles = $loading = '';
				$class 	= 'style-load-more';
				if('ajax-infinite' == $style){
					$class 		= 'style-infinite';
					$styles 	= ' style="opacity: 0;"';
					$loading	= ' loading';
				}
				printf(
					'<nav class="navigation pagination ajax %s"%s%s%s><div class="pagination-container load-more%s">%s%s</div></nav>',
					$class,
					empty($args['data']) ? '' : sprintf(' %s', $args['data']),
					$styles,
					$this->attrs_data(),
					$loading,
					sprintf('<h2 class="screen-reader-text">%s</h2>', esc_html__('Posts Navigation', 'fallsky')),
					sprintf(
						'<a href="#" data-no-post-text="%s" class="load-more-btn">%s</a>',
						esc_html__('No More Posts', 'fallsky'),
						esc_html__('Load More', 'fallsky')
					)
				);	
			}
		}
		/**
		* Pagination for type next_prev
		* @param array arguments max page num and page
		*/
		private function pagination_next_prev($style){
			$this->is_prev_next_nav = true;
			$prev_label = sprintf('<span class="arrow_carrot-left"></span>%s ', 	esc_html__('Prev', 'fallsky'));
			$next_label = sprintf('%s <span class="arrow_carrot-right"></span>', 	esc_html__('Next', 'fallsky'));
			$prev_link 	= get_previous_posts_link($prev_label);
			$next_link 	= get_next_posts_link($next_label);

			if(!empty($prev_link) || !empty($next_link)){
				printf(
					'<nav class="navigation pagination style-links"><div class="pagination-container">%s%s%s%s</div></nav>',
					sprintf('<h2 class="screen-reader-text">%s</h2>', esc_html__('Posts navigation', 'fallsky')),
					empty($prev_link) ? sprintf('<span class="prev page-numbers">%s</span>', $prev_label) : $prev_link,
					'page-number' == $style ? paginate_links(array('prev_next' => false, 'type' => 'plain')) : '',
					empty($next_link) ? sprintf('<span class="next page-numbers">%s</span>', $next_label) : $next_link
				);
			}
			$this->is_prev_next_nav = false;
		}
		/**
		* Ajax handler for widget posts pagination
		*/
		public function ajax_post_list(){
			$types = array('widget', 'category', 'tag', 'author', 'search', 'date', 'post_format', 'blog'); 
			if(!isset($_REQUEST['type']) || !in_array($_REQUEST['type'], $types) || empty($_REQUEST['data'])){
				wp_send_json_error(esc_html__('Request not allowed', 'fallsky'));
			}

			$data = $_REQUEST['data']; 
			ob_start();
			switch($_REQUEST['type']){
				case 'widget':
					$widgets_post 	= get_option('widget_fallsky-homepage-widget-posts');
					if(isset($_REQUEST['data'])){
						$widget_class 	= $data['widget'];
						$widget_id 		= $data['widgetID'];
						if(!empty($widgets_post) && isset($widgets_post[$widget_id])){
							the_widget($widget_class, $widgets_post[$widget_id]);
						}
					}
					break;
				case 'category':
				case 'tag':
				case 'author':
				case 'search': 
				case 'date':
				case 'post_format':
				case 'blog':
					do_action('fallsky_archive_ajax');
					break;
			}
			wp_send_json_success(ob_get_clean());
		}
		/**
		* Filter to generate WP_Query arguments for widget posts
		* @param array original args
		* @param widget settings
		* @return array args
		*/
		public function widget_posts_args($args, $sets){
			if(!empty($sets)){
				$filter = $sets['filter-by'];
				$number = intval($sets['number']);
				$args	= array_merge(array(
					'posts_per_page'		=> empty($number) ? get_option('posts_per_page', 10) : $number, 
					'paged' 				=> 1, 
					'ignore_sticky_posts' 	=> true, 
					'post_type' 			=> 'post',
					'post_status'			=> 'publish'
				), $args);

				switch($filter){
					case 'latest':
						break;
					case 'category':
						$category = fallsky_convert_tax_slug2id($sets['category']);
						$category = intval($category);
						if(!empty($category)){
							$args['tax_query'] = array(
								array(
									'taxonomy' => 'category',
									'field'    => 'term_id',
									'terms'    => $category
								)
							);
						}
						break;
					case 'comments':
						$args = array_merge($args, array('orderby' => 'comment_count', 'order' => 'DESC'));
						break;
					case 'format': 
						$format = $sets['post-format'];
						if('standard' == $format){
							$args['tax_query'] = array(
								array(
									'taxonomy' => 'post_format', 
									'operator' => 'NOT EXISTS'
								)
							);
						}
						else{
							$args['tax_query'] =  array(
								array(
									'taxonomy' => 'post_format',
									'field'    => 'slug',
									'terms'    => array(sprintf('post-format-%s', $format))
								)
							);
						}
						break;
					default:
						$args = apply_filters('loftocean_posts_args', $args, $filter);
				}
				if(('latest' == $filter) && !empty($sets['homewidget'])){
					$args = apply_filters('fallsky_frontpage_widget_exclude_featured_posts', $args);
				}
			}

			return $args;
		}
		/**
		* Helper function to generate post list class
		* @param array arguments, including the layout and columns settings
		* @return string html class attributes
		*/
		private function get_post_list_class( $args ) {
			$class 	= array( 'posts' );
			$layout = empty( $args['layout'] ) ? 'standard' : $args['layout'];
			$layout = ( 'overlay-mix' == $layout ) ? 'overlay' : $layout;
			array_push( $class, sprintf( 'layout-%s', $layout ) );
			empty( $args['columns'] ) ? '' : array_push( $class, sprintf( 'column-%s', $args['columns'] ) );
			( 'card' == $layout ) && isset( $args['card_color'] ) ? array_push( $class, $args['card_color'] ) : '';
			in_array( $layout, array( 'masonry', 'grid' ) ) && !empty( $args['center_text'] ) ? array_push( $class, 'text-centered' ) : '';
			in_array( $layout, array( 'grid' ) ) && !empty( $args['image_orientation'] ) ? array_push( $class, $args['image_orientation'] ) : '';

			return sprintf( ' class="%s"', implode( ' ', $class ) );
		}
		/**
		* Get html attributes from data
		* @param array datas
		* @return html attributes
		*/
		private function get_data($data){
			$attrs = array();
			foreach($data as $name => $value){
				if(!empty($value) && !empty($name)){
					if($name == 'next-page'){
						$name = 'page';
					}
					array_push($attrs, sprintf('data-%s="%s"', $name, $value));
				}
			}
			return empty($attrs) ? '' : implode(' ', $attrs);
		}
		/**
		* Add class to previous post link
		*/
		public function previous_posts_link_attrs($attr){
			return $this->is_prev_next_nav ? 'class="prev page-numbers"' : $attr;
		}
		/**
		* Add class to next post link
		*/
		public function next_posts_link_attrs($attr){
			return $this->is_prev_next_nav ? 'class="next page-numbers"' : $attr;
		}
		/**
		* Output the javascript variables for pagination
		* @param array javascript vars
		* @return array
		*/
		public function frontend_js_vars($vars = array()){
			$vars['ajax_pagination'] = array(
				'url' 			=> admin_url('admin-ajax.php'),
				'action' 		=> 'fallsky_posts_list',
				'is_front_page'	=> fallsky_is_front_page()
			);
			return $vars;
		}
		/**
		* Data attributes for ajax based pagination
		*/
		private function attrs_data(){
			$attrs = apply_filters('fallsky_ajax_pagination_attrs', array());
			if(!empty($attrs) && is_array($attrs)){
				return sprintf(" data-attrs='%s'", esc_js(json_encode($attrs))); 
			}
		}
	}
	new Fallsky_Post_List();
}