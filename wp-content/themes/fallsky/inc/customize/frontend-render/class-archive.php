<?php
/**
* Archive(category/tag/author/date archive/search) page frontend render class.
*/

if(!class_exists('Fallsky_Archive_Render')){
	class Fallsky_Archive_Render{
		private $archive_type 			= false;
		private $list_args				= array();
		private $nav_args 				= array();
		private $queried_object 		= false;
		private $image_html				= false;
		private $media_bg_hide 			= false;
		private $query_vars 			= array('filter');
		private $filtered_cat 			= false;
		private $is_transparent_header 	= false;
		public function __construct(){
			add_action('wp', array($this, 'wp'), 999);

			add_filter('query_vars', 			array($this, 'add_query_vars'));
			add_action('parse_request', 		array($this, 'parse_request'));
			add_action('pre_get_posts', 		array($this, 'pre_get_posts'));
			add_action('fallsky_archive_ajax', 	array($this, 'archive_ajax'));
		}
		public function wp(){
			global $fallsky_archive_type;
			if(!empty($fallsky_archive_type) && ($fallsky_archive_type != 'home')){
				$this->archive_type = $fallsky_archive_type;
				$this->is_transparent_header = apply_filters( 'fallsky_transparent_archive_site_header', false );
				$this->init_args();
				$this->get_properties();

				add_filter( 'fallsky_is_page_header_with_bg', 		array( $this, 'is_page_header_with_bg' ) );
				add_filter( 'fallsky_is_transparent_site_header', 	array( $this, 'is_transparent_site_header' ) );
				add_filter( 'fallsky_site_header_class', 			array( $this, 'site_header_class' ) );
				add_filter(	'body_class',							array($this, 'body_class' ) );
				add_action(	'fallsky_before_main_content', 			array($this, 'page_header' ) );
				add_action(	'fallsky_main_content', 				array($this, 'main_content' ) );
			}
		}
		public function add_query_vars($vars){
			foreach($this->query_vars as $var){
				array_push($vars, $var);
			}
			return $vars;
		}
		public function parse_request(){
			global $wp;
			// Map query vars to their keys, or get them if endpoints are not supported
			foreach($this->query_vars as $var){
				if(isset($_GET[$var])){
					$wp->query_vars[$var] = $_GET[$var];
				}
			}
		}
		public function pre_get_posts($query){
			if($query->is_main_query() && !is_admin()){
				global $fallsky_archive_type;
				// If global $fallsky_archive_type not initilized, call the action
				do_action('fallsky_global');

				if(!empty($fallsky_archive_type) && ('home' != $fallsky_archive_type)){
					$paged = $query->get('paged');
					if(fallsky_is_ajax_pagination() && ($paged > 1)){
						if('search' == $fallsky_archive_type){
							global $fallsky_actual_paged;
							$fallsky_actual_paged = $query->get('paged');
							$query->set('posts_per_page', $this->get_posts_per_page($fallsky_archive_type) * $fallsky_actual_paged);
						}
						$query->set('paged', 1); 
					}
					else{
						$query->set('posts_per_page', $this->get_posts_per_page($fallsky_archive_type));
						if(('category' == $fallsky_archive_type) && get_query_var('filter', false)){
							$filter = get_category_by_slug(get_query_var('filter'));
							if(!empty($filter)){
								$this->filtered_cat = $filter;
								$query->set('cat', $filter->term_id);
							}
						}
					}
					if('search' == $fallsky_archive_type){
						$post_type = $query->get('post_type');
						empty($post_type) ? $query->set('post_type', 'post') : '';
					}
				}
			}
		}
		public function body_class($class){
			if('blog' == $this->archive_type){
				array_push($class, 'archive');
			}
			return $class;
		}
		/**
		* Handler the ajax request
		*/
		public function archive_ajax(){
			global $wp_query, $paged, $fallsky_archive_type;
			$this->archive_type = $fallsky_archive_type = $_REQUEST['type'];
			$data 	= $_REQUEST['data'];
			$paged 	= $_REQUEST['page'];
			$args 	= array(
				'post_type' 		=> 'post', 
				'post_status' 		=> 'publish', 
				'paged'				=> $paged, 
				'posts_per_page' 	=> $this->get_posts_per_page($this->archive_type)
			);
			switch($this->archive_type){
				case 'category':
					$category = get_category_by_slug($data['category']);
					if(false === $category){
						return false;
					}
					$this->queried_object 	= $category;
					$filtered_cat 			= empty($data['filter']) ? false : get_category_by_slug($data['filter']); 
					$args['cat'] 			= (false === $filtered_cat) ? $category->term_id : $filtered_cat->term_id;
					break;
				case 'tag':
					$tag = get_term_by('slug', $data['tag'], 'post_tag');
					if(false === $tag){
						return false;
					}
					$this->queried_object 	= $tag;
					$args['tag_id'] = $tag->term_id;
					break;
				case 'author':
					$author = get_user_by('login', $data['author']);
					if(false === $author){
						return false;
					}
					$this->queried_object 	= $author;
					$args['author'] = intval($author->ID);
					break;
				case 'search':
					$args['s'] = $data['search'];
					break;
				case 'date':
					if(isset($data['year']) && ($data['year'] > 0)){
						$args['year']	 	= $data['year'];
					}
					if(isset($data['month']) && ($data['month'] > 0)){
						$args['monthnum']	= $data['month'];
					}
					if(isset($data['day']) && ($data['day'] > 0)){
						$args['day'] 		= $data['day'];
					}
					break;
				case 'post_format':
					$args['tax_query'] =  array(
						array(
							'taxonomy' => 'post_format',
							'field'    => 'slug',
							'terms'    => array($data['format'])
						)
					);
					break;
				case 'blog':
					break;
				default:
					return false;
			}

			$wp_query = new WP_Query($args);
			$this->init_args();
			$this->parse_args();
			do_action('fallsky_posts_list', $this->list_args);
			do_action('fallsky_posts_pagination', $this->nav_args);
		}
		/**
		* Add needed class name to body
		*/
		public function is_page_header_with_bg($flag){
			$archive_type = $this->archive_type;
			if(in_array($archive_type, array('category', 'tag', 'blog')) && !empty($this->image_html) && !$this->media_bg_hide){
				return true;
			}
			return $flag;
		}
		/*
		* Test if cucrent page is transparent site header
		* @param array of boolean, ['show on frontend', 'not show on frontend, but show on customize preview page']
		* @return array of boolean
		*/
		public function is_transparent_site_header( $is = array(false, false) ) {
			global $fallsky_archive_type;
			if( !empty( $fallsky_archive_type ) && in_array( $fallsky_archive_type, array( 'category', 'tag', 'blog' ) ) && $this->is_transparent_header && !empty( $this->image_html ) ) {
				return $this->media_bg_hide ? array( false, true ) : array( true, false );
			}
			return $is;
		}
		/**
		* Add transparent class to site head if needed
		* @param array class name list
		* @return array
		*/
		public function site_header_class( $class ){
			global $fallsky_archive_type;
			if( !empty( $fallsky_archive_type ) && in_array( $fallsky_archive_type, array( 'category', 'tag', 'blog' ) ) && $this->is_transparent_header && !empty( $this->image_html ) ) {
				array_push( $class, 'transparent' );
			}
			return $class;
		}
		/**
		* Archive page header
		*/
		public function page_header(){
			$archive_type = $this->archive_type;
			$header_text 	= '';
			$header_media 	= '';

			// If not show the page header enabled, return
			if(in_array($archive_type, array('category', 'tag')) && !fallsky_module_enabled('fallsky_' . $archive_type . '_show_page_header')){
				return '';
			}

			if(in_array($archive_type, array('category', 'tag', 'blog'))){
				$queried 		= $this->queried_object;
				$is_blog 		= ('blog' == $archive_type);
				$title 			= $is_blog ? $queried->post_title : $queried->name;
				$description 	= $is_blog ? '' : apply_filters('widget_text_content', $queried->description);
				$image_html		= $this->image_html;
				$header_media	= empty($image_html) ? '' : sprintf(
					'<div class="featured-media-section%s">%s</div>',
					$this->media_bg_hide ? ' hide' : '',
					$image_html
				);
				$header_text 	= sprintf(
					'<h1 class="page-title">%s</h1>%s',
					apply_filters('the_title', $title),
					empty($description) ? '' : sprintf('<div class="description %s-description">%s</div>', $archive_type, $description)
				);		
			}
			else if('author' == $archive_type){
				$avatar 		= get_avatar(get_the_author_meta('user_email'), 150);
				$description 	= get_the_author_meta('description');
				$socials 		= fallsky_author_socials(false);
				$header_text = sprintf(
					'<div class="author-bio"><div class="author-bio-top">%s<div class="author-info"><h1 class="page-title author-name">%s</h1>%s</div></div>%s</div>',
					empty($avatar) ? '' : sprintf('<div class="author-photo">%s</div>', $avatar),
					$this->queried_object->display_name,
					empty($socials) ? '' : $socials,
					empty($description) ? '' : sprintf('<div class="author-bio-text">%s</div>', apply_filters('widget_text_content', $description))
				);
			}
			else if('search' == $archive_type){
				global $wp_query;
				$found = $wp_query->found_posts;
				if($found > 0){
					$header_text = sprintf(
						'<span>%s</span><h1 class="page-title">%s</h1>',
						sprintf(esc_html(_n('Found %s Result for', 'Found %s Results for', $found, 'fallsky')), number_format_i18n($found)),
						esc_html(get_search_query())
					);
				}
			}
			else if('date' == $archive_type){
				if(is_year()){
					$header_text = sprintf('<span>%s</span><h1 class="page-title">%s</h1>',
						esc_html_x('Yearly Archive', 'yearly archives', 'fallsky'), 
						get_the_date(_x('Y', 'yearly archives date format', 'fallsky'))
					);
				} 
				else if(is_month()){
					$header_text = sprintf('<span>%s</span><h1 class="page-title">%s</h1>',
						esc_html_x('Monthly Archive', 'monthly archives', 'fallsky'),
						get_the_date(_x('F Y', 'monthly archives date format', 'fallsky'))
					);
				}
				else{
					$header_text = sprintf('<span>%s</span><h1 class="page-title">%s</h1>',
						esc_html_x('Daily Archive', 'daily archives', 'fallsky'),
						get_the_date(_x('F j, Y', 'daily archives date format', 'fallsky'))
					);
				}
			}
			else if('post_format' == $archive_type){
				$queried 		= $this->queried_object;
				$title 			= $queried->name;
				$header_text 	= sprintf(
					'<h1 class="page-title">%s</h1>',
					apply_filters('the_title', $title)
				);
			}

			printf(
				'<header class="page-header">%s<div class="page-header-text">%s</div></header>',
				$header_media,
				$header_text
			);
		}
		/**
		* Archive page main content
		*/
		public function main_content(){
			$show_list = true;
			if('category' == $this->archive_type){
				$show_list = $this->main_content_category();
			}
			if($show_list){
				$this->parse_args();
				do_action('fallsky_posts_list', $this->list_args);
				do_action('fallsky_posts_pagination', $this->nav_args);
			}
		}
		/*
		* Category archive page main content
		* @return boolean return true if still need to show the post list, otherwise return false
		*/
		private function main_content_category(){
			$show_list = true;
			// Show category filter
			$this->category_filter();
			// Show sub category list if needed
			if('subcategory' == fallsky_get_theme_mod('fallsky_category_content')){
				$show_list = !$this->sub_categories(); 
			}

			return $show_list;
		}
		/**
		* Category filter if have
		*/
		private function category_filter(){
			global $fallsky_is_preview;
			$current_id 	= $this->queried_object->term_id;
			$current_count 	= fallsky_get_category_post_count($this->queried_object);
			$current_link	= get_category_link($current_id);
			$children 		= get_categories(array('parent' => $this->queried_object->term_id));
			$filter  		= get_query_var('filter', $this->queried_object->slug);
			$enabled 		= fallsky_module_enabled('fallsky_category_show_subcategory_filter');
			if(!empty($children) && ($enabled || $fallsky_is_preview)){
				$item_tmpl 	= '<li%s><a href="%s" title="%s"><span class="category-name">%s</span> <span class="counts">%s</span></a></li>';
				$wrap 		= '<div class="cat-filter%s"><ul>%s%s</ul></div>';
				$items 		= array();
				$actived 	= false;
				foreach($children as $child){
					$name 	= $child->name;
					$class 	= '';
					if($child->slug == $filter){
						$actived 	= true;
						$class 		= ' class="active"';
					}
					$items[] = sprintf( $item_tmpl, $class, add_query_arg('filter', $child->slug, $current_link), esc_attr($name), esc_html($name), fallsky_get_category_post_count($child) );
				}
				printf(
					$wrap,
					!$enabled && $fallsky_is_preview ? ' hide' : '',
					sprintf( $item_tmpl, ($actived ? '' : ' class="active"'), $current_link, esc_attr__('All', 'fallsky'), esc_html__('All', 'fallsky'), $current_count ),
					implode('', $items)
				);
			}
		}
		/**
		* Show sub categories if have any
		* @return boolean return true if have child category, otherwise false
		*/
		private function sub_categories(){
			global $fallsky_is_preview;
			$category = $this->filtered_cat ? $this->filtered_cat : $this->queried_object;
			$children = get_categories(array('parent' => $category->term_id, 'fields' => 'id=>slug'));
			if(!empty($children)){
				$layout = fallsky_get_theme_mod('fallsky_category_subcategory_layout');
				do_action('fallsky_set_frontend_options', 'widget_category', array('column' => $layout));
				$values = array(
					'title' 		=> '', 
					'categories' 	=> array_values($children), 
					'style' 		=> fallsky_get_theme_mod('fallsky_category_subcategory_style'),
					'layout'		=> $layout,
					'show-count' 	=> fallsky_get_theme_mod('fallsky_category_subcategory_show_post_count'),
					'is_preview'	=> $fallsky_is_preview,
					'from_archive' 	=> true
				);
				the_widget('Fallsky_Widget_Category', $values);
				do_action('fallsky_reset_frontend_options');
				return true;
			}
			return false;
		}
		/**
		* Set the default values to list arguments
		*/
		private function init_args(){
			global $wp_query, $paged;
			$paged = max($paged, 1);
			$this->list_args = array(
				'posts' 				=> $wp_query,
				'layout'				=> 'standard',
				'columns' 				=> false,
				'show_read_more_btn'	=> true,
				'center_text'			=> false,
				'image_orientation'		=> '',
				'card_color'			=> '',
				'post_meta'				=> array()
			);
			$this->nav_args = array(
				'posts'	=> $wp_query,
				'page'  => $paged,
				'data' 	=> array(
					'type'		=> 'blog',
					'next-page' => $paged + 1
				)
			);
		}
		/**
		* Get properties for current archive page
		*/
		private function get_properties(){
			$queried_object 		= get_queried_object();
			$archive_type 			= $this->archive_type;
			$this->queried_object 	= $queried_object; 
			do_action('fallsky_set_frontend_options', 'page_header');
			switch($archive_type){
				case 'category':
				case 'tag':
					if(fallsky_module_enabled('fallsky_' . $archive_type . '_show_page_header')){
						$image_html = apply_filters(
							'loftocean_get_taxonomy_image_bg', 
							'', 
							$queried_object, 
							apply_filters( 'fallsky_image_sizes', false ), 
							array('class' => 'header-img')
						);
						
						if(!fallsky_module_enabled(sprintf('fallsky_%s_show_image', $archive_type)) && !empty($image_html)){
							global $fallsky_is_preview;
							$fallsky_is_preview ? ($this->media_bg_hide = true) : ($image_html = false);
						}
						$this->image_html = $image_html;
					}
					break;
				case 'blog':
					$image_id = get_post_thumbnail_id($queried_object->ID);
					$this->image_html = empty($image_id) ? '' 
						: fallsky_get_preload_bg( array( 'id' => get_post_thumbnail_id( $queried_object->ID ), 'class' => 'header-img' ) );
					break;
			}
			do_action('fallsky_reset_frontend_options');
		}
		/**
		* Parse arguments for post list and navigation
		*/
		private function parse_args(){
			$archive_type = $this->archive_type;
			// List args
			$layout 			= fallsky_get_theme_mod(sprintf('fallsky_%s_posts_layout', $archive_type));
			$has_cols 			= array('list', 'masonry', 'card', 'grid', 'overlay', 'overlay-mix');
			$this->list_args 	= wp_parse_args(array(
				'layout' 				=> $layout,
				'columns'				=> in_array($layout, $has_cols) ? fallsky_get_theme_mod( sprintf( 'fallsky_%s_column_%s', $archive_type, str_replace( '-', '_', $layout ) ) ) : false,
				'image_orientation' 	=> fallsky_get_theme_mod( sprintf( 'fallsky_%s_image_orientation', $archive_type ) ),
				'post_meta' 			=> $this->get_meta(),
				'show_read_more_btn' 	=> fallsky_module_enabled(sprintf('fallsky_%s_show_read_more_btn', $archive_type)),
				'center_text'			=> fallsky_module_enabled(sprintf('fallsky_%s_masonry_center_text', $archive_type)),
				'card_color'			=> fallsky_get_theme_mod(sprintf('fallsky_%s_card_color', $archive_type))
			), $this->list_args);

			// Nav args
			switch($archive_type){
				case 'category':
					$this->nav_args['data'] = array_merge($this->nav_args['data'], array(
						'type' 		=> $archive_type,
						'filter'	=> $this->filtered_cat ? $this->filtered_cat->slug : '',
						'category' 	=> $this->queried_object->slug
	 				));
	 				break;
				case 'tag':
					$this->nav_args['data'] = array_merge($this->nav_args['data'], array(
						'type' 	=> $archive_type,
						'tag' 	=> $this->queried_object->slug
	 				));
	 				break;
				case 'author':
					$this->nav_args['data'] = array_merge($this->nav_args['data'], array(
						'type' 		=> $archive_type,
						'author' 	=> $this->queried_object->user_login
	 				));
	 				break;
	 			case 'search':
	 				global $paged, $fallsky_actual_paged;
					$this->nav_args['data'] = array_merge($this->nav_args['data'], array(
						'type' 		=> $archive_type,
						'next-page' => empty($fallsky_actual_paged) ? ($paged + 1) : ($fallsky_actual_paged + 1),
						'search' 	=> get_search_query()
	 				));
	 				break;
	 			case 'date':
	 				global $wp_query;
	 				$data = array();
					$this->nav_args['data'] = array_merge($this->nav_args['data'], array(
						'type' 	=> $archive_type,
						'year'	=> $wp_query->get('year'),
						'month'	=> $wp_query->get('monthnum'),
						'day'	=> $wp_query->get('day')
	 				));
	 				break;
	 			case 'post_format':
	 				$this->nav_args['data'] = array_merge($this->nav_args['data'], array(
						'type' 		=> $archive_type,
						'format' 	=> $this->queried_object->slug
	 				));
	 				break;
			}
		}
		/**
		* Get post meta settings
		*/
		private function get_meta(){
			$archive_type = $this->archive_type;
			$meta = array();
			$sets = array(
				'excerpt' 	=> sprintf('fallsky_%s_show_post_meta_excerpt', 	$archive_type),
				'category' 	=> sprintf('fallsky_%s_show_post_meta_category', 	$archive_type),
				'author' 	=> sprintf('fallsky_%s_show_post_meta_author', 		$archive_type),
				'date' 		=> sprintf('fallsky_%s_show_post_meta_date', 		$archive_type),
				'view' 		=> sprintf('fallsky_%s_show_post_meta_view', 		$archive_type),
				'like' 		=> sprintf('fallsky_%s_show_post_meta_like', 		$archive_type),
				'comment' 	=> sprintf('fallsky_%s_show_post_meta_comment', 	$archive_type)
			);
			foreach($sets as $id => $name){
				if(fallsky_module_enabled($name)) array_push($meta, $id);
			}
			return $meta;
		}
		/**
		* Get posts_per_page number for current archive page
		* @param string archive type
		* @return int
		*/
		private function get_posts_per_page($type){
			return fallsky_get_theme_mod(sprintf('fallsky_%s_posts_per_page', $type));
		}
	}
	new Fallsky_Archive_Render();
}