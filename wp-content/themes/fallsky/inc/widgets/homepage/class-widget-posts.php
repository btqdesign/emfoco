<?php
/**
* Theme Custom Widget for Homepage only 
*/

if(!class_exists('Fallsky_Homepage_Widget_Posts')){
	class Fallsky_Homepage_Widget_Posts extends Fallsky_Widget{
		private $is_no_sidebar 			= false;
		private $posts 					= array();
		private $latest 				= false;
		private $render_widget_post 	= false;
		static private $filter_added 	= false;
		public function __construct(){
			$widget_ops = array(
				'classname' 					=> '',
				'description' 					=> esc_html__('Add posts to your homepage.', 'fallsky'),
				'customize_selective_refresh' 	=> false
			);
			parent::__construct('fallsky-homepage-widget-posts', esc_html__('Posts', 'fallsky'), $widget_ops);
		}
		/**
		* Add filter for frontend rendering
		*/
		private function frontend_filters(){
			if(!self::$filter_added){
				add_filter('post_class', array($this, 'post_class'), 999);
				self::$filter_added = true;
			}
		}
		/**
		 * Generate the main content
		 * @return html string
		 */
		public function get_content(){
			global $paged;
			$is_latest 		= ('latest' == $this->get_value('filter-by'));
			$this->latest 	= $is_latest;
			// Get the actual paged number
			$current_page	= fallsky_is_ajax_on_frontpage() && isset($_REQUEST['page']) ? $_REQUEST['page'] : ($is_latest ? max($paged, 1) : 1);
			$page 			= $current_page; // $page maybe changed after function get_posts() called
			$layout 		= $this->get_value('layout');
			$has_cols 		= array('list', 'masonry', 'card', 'grid', 'overlay', 'overlay-mix');
			$posts 			= $this->get_posts($page, $is_latest);
			$list_args 		= apply_filters('fallsky_homepage_widget_post_args', array(
				'posts' 				=> $posts,
				'layout'				=> $layout,
				'columns' 				=> in_array( $layout, $has_cols ) ? $this->get_value( $layout . '-column' ) : false,
				'post_meta'				=> $this->get_meta_show(),
				'show_read_more_btn' 	=> $this->is_checked( 'show-read-more' ),
				'center_text'			=> $this->is_checked( 'center-text' ),
				'image_orientation'		=> $this->get_value( 'featured-image-orientation' ),
				'card_color'			=> $this->get_value( 'card-color' ),
				'is_latest' 			=> $is_latest
			));
		
			ob_start();
			
			$this->frontend_filters();
			$this->render_widget_post = true;
			do_action('fallsky_posts_list', $list_args);
			$this->render_widget_post = false;
			if($is_latest && $this->is_checked('pagination')){
				do_action('fallsky_posts_pagination', array('posts' => $posts, 'page' => $page, 'data' => array('next-page' => ($current_page + 1), 'type' => 'widget')));
			}
			return ob_get_clean();
		}
		/**
		* Add class name sticky to post class
		*/
		public function post_class($class){
			if($this->render_widget_post){
				global $post; 
				if($this->latest){
					if(is_sticky($post->ID) && !is_paged() && !wp_doing_ajax()){
						array_push($class, 'sticky');
					}
				}
				else{
					$class = array_diff($class, array('sticky'));
				}
			}
			return $class;
		}
		/**
		* Handles widget output
		* @param array widget arguments
		* @param array current widget setting values
		*/
		public function widget($args, $instance){
			$this->instance 		= $instance;

			$page_layout 			= apply_filters('fallsky_page_layout', '');
			$this->is_no_sidebar 	= empty($page_layout);

			printf(
				'%s<div class="container">%s%s</div>%s',
				$this->get_before_widget($args),
				$this->get_title($args),
				$this->get_content(),
				$args['after_widget']
			);
		}
		/**
		* Helper function to get widget title
		* @param array sidebar settings
		* @return html string
		*/
		protected function get_title($args){
			$filter = $this->get_value('filter-by');
			if('category' == $filter){
				$title 	 		= $this->get_value('title');
				$title_align 	= $this->get_value('title-align');
				if(!empty($title)){
					$title_class 	= empty($title_align) ? '' : ' align-center';
					$category 		= fallsky_convert_tax_slug2id($this->get_value('category'));
					$category_link 	= empty($category) ? '' : get_term_link(intval($category), 'category');
					$title = sprintf(
						'%s%s%s',
						sprintf($args['before_title'], $title_class),
						empty($category_link) ? $title : sprintf( '<a href="%s">%s</a>', $category_link, apply_filters( 'widget_title', $title ) ),
						$args['after_title']
					);
				}
				return $title;
			}
			else{
				return parent::get_title($args);
			}
		}
		/**
		* Get posts by current widget settings
		* @return object WP_Query object
		*/
		private function get_posts(&$page, $is_latest){
			global $paged;
			if(!isset($this->posts[$this->id])){
				$args = array('paged' => $page);
				if($is_latest && ($paged > 1) && fallsky_is_ajax_pagination()){
					$ppp 	= $this->get_value('number');
					$args 	= array('posts_per_page' => $ppp * $page, 'paged' => 1);
					$page 	= 1;
				}
				$args['ignore_sticky_posts'] = $is_latest ? false : true;
				$this->posts[$this->id] = new WP_Query(apply_filters(
					'fallsky_widget_posts_list_args', 
					$args, 
					array_merge($this->defaults, $this->instance, array('homewidget' => true))
				));
			}
			return $this->posts[$this->id];
		}
		/**
		* Get meta list checked to show
		* @return array 
		*/
		private function get_meta_show(){
			$meta = array();
			$sets = array(
				'excerpt'	=> 'show-post-excerpt',
				'category' 	=> 'post-meta-category',
				'author' 	=> 'post-meta-author',
				'date' 		=> 'post-meta-date',
				'view' 		=> 'post-meta-view',
				'like' 		=> 'post-meta-like',
				'comment' 	=> 'post-meta-comment'
			);
			foreach($sets as $id => $name){
				if($this->is_checked($name)){
					array_push($meta, $id);
				}
			}
			return $meta;
		}
		/**
		* Helper function to generate any extra section classes
		*  This function can be overwritten by child class if needed
		* @param array classes
		* @return array classes
		*/
		protected function get_section_class($class){
			global $fallsky_is_preview;
			if($this->is_checked('fullwidth') && ($this->is_no_sidebar || $fallsky_is_preview)){
				array_push($class, 'fullwidth');
			}
			return $class;
		}
		/**
		 * Register all the form elements for showing
		 * 	Each control has at least id, type and default value
		 * 	For control with type select, should has a list of choices
		 * 	For each control can has attributes to the form elements
		 */
		public function register_controls(){
			$this->add_control(array(
				'id' 			=> 'title',
				'type'			=> 'text',
				'default'		=> '',
				'sanitize_cb'	=> 'fallsky_widget_sanitize_text',
				'title'			=> esc_html__('Section Title', 'fallsky')
			));
			$this->add_control(array(
				'id' 			=> 'title-align',
				'type'			=> 'checkbox',
				'default'		=> 'on',
				'sanitize_cb'	=> 'fallsky_widget_sanitize_checkbox',
				'title'			=> esc_html__('Center Section Title', 'fallsky')
			));
			$this->add_control(array(
				'id' 			=> 'color',
				'type'			=> 'select',
				'default'		=> 'default',
				'title'			=> esc_html__('Color', 'fallsky'),
				'sanitize_cb'	=> 'fallsky_widget_sanitize_choice',
				'choices'		=> array(
					'default' 	=> array('label' => esc_html__('Default', 'fallsky')),
					'custom'	=> array('label' => esc_html__('Custom', 'fallsky'))
				)
			));
			$this->add_control(array(
				'id' 			=> 'custom-color-scheme',
				'type'			=> 'select',
				'default'		=> 'light-color',
				'title'			=> esc_html__('Color Scheme', 'fallsky'),
				'sanitize_cb'	=> 'fallsky_widget_sanitize_choice',
				'dependency'	=> array('color' => array('value' => array('custom'))),
				'choices'		=> array(
					'light-color' 	=> array('label' => esc_html__('Light', 'fallsky')),
					'dark-color'	=> array('label' => esc_html__('Dark', 'fallsky'))
				)
			));
			$this->add_control(array(
				'id' 			=> 'custom-bg-color',
				'type'			=> 'color-picker',
				'default'		=> '',
				'sanitize_cb'	=> 'fallsky_widget_sanitize_color',
				'dependency'	=> array('color' => array('value' => array('custom'))),
				'title'			=> esc_html__('Custom background color', 'fallsky')
			));
			$this->add_control(array(
				'id' 			=> 'padding-top',
				'type'			=> 'number',
				'default'		=> '50',
				'title'			=> esc_html__('Padding Top', 'fallsky'),
				'sanitize_cb'	=> 'fallsky_widget_sanitize_number',
				'input_attr'	=> array('min' => 0, 'style' => 'width: 80px; margin-right: 5px;'),
				'text_after'	=> 'px'
			));
			$this->add_control(array(
				'id' 			=> 'padding-bottom',
				'type'			=> 'number',
				'default'		=> '50',
				'title'			=> esc_html__('Padding Bottom', 'fallsky'),
				'sanitize_cb'	=> 'fallsky_widget_sanitize_number',
				'input_attr'	=> array('min' => 0, 'style' => 'width: 80px; margin-right: 5px;'),
				'text_after'	=> 'px'
			));
			$this->add_control(array(
				'id' 			=> 'filter-by',
				'type'			=> 'select',
				'default'		=> 'latest',
				'title'			=> esc_html__('Choose Posts', 'fallsky'),
				'sanitize_cb'	=> 'fallsky_widget_sanitize_choice',
				'choices'		=> array(
					'latest' 	=> array('label' => esc_html__('Latest', 'fallsky')),
					'category'	=> array('label' => esc_html__('From a selected category', 'fallsky')),
					'featured' 	=> array('label' => esc_html__('Featured posts', 'fallsky')),
					'views'		=> array('label' => esc_html__('Most viewed', 'fallsky')),
					'likes' 	=> array('label' => esc_html__('Most liked', 'fallsky')),
					'comments'	=> array('label' => esc_html__('Most commented', 'fallsky')),
					'format'	=> array('label' => esc_html__('From a selected format', 'fallsky'))
				)
			));
			$this->add_control(array(
				'id' 			=> 'category',
				'type'			=> 'select',
				'default'		=> '',
				'title'			=> '',
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_choice',
				'dependency'	=> array('filter-by' => array('value' => array('category'))),
				'choices'		=> fallsky_get_terms('category', true, esc_html__('Choose a category', 'fallsky'))
			));
			$this->add_control(array(
				'id' 			=> 'post-format',
				'type'			=> 'select',
				'default'		=> 'standard',
				'title'			=> '',
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_choice',
				'dependency'	=> array('filter-by' => array('value' => array('format'))),
				'choices'		=> $this->get_post_formats()
			));
			$this->add_control(array(
				'id' 			=> 'layout',
				'type'			=> 'select',
				'default'		=> 'masonry',
				'title'			=> esc_html__('Posts Layout', 'fallsky'),
				'sanitize_cb'	=> 'fallsky_widget_sanitize_choice',
				'choices'		=> array(
					'masonry' 		=> array('label' => esc_html__('Masonry', 'fallsky')),
					'list'			=> array('label' => esc_html__('List', 'fallsky')),
					'zigzag'		=> array('label' => esc_html__('ZigZag', 'fallsky')),
					'grid' 			=> array('label' => esc_html__('Grid', 'fallsky')),
					'card'			=> array('label' => esc_html__('Card', 'fallsky')),
					'overlay'		=> array('label' => esc_html__('Overlay', 'fallsky')),
					'overlay-mix' 	=> array('label' => esc_html__('Overlay Mix', 'fallsky'))
				)
			));
			$this->add_control(array(
				'id' 			=> 'list-column',
				'type'			=> 'select',
				'default'		=> '1',
				'title'			=> esc_html__('Columns', 'fallsky'),
				'dependency'	=> array('layout' => array('value' => array('list'))),
				'sanitize_cb'	=> 'fallsky_widget_sanitize_choice',
				'choices'		=> array(
					'1' => array('label' => esc_html__('1 Column', 'fallsky')),
					'2'	=> array('label' => esc_html__('2 Columns', 'fallsky'))
				)
			));
			$this->add_control(array(
				'id' 			=> 'masonry-column',
				'type'			=> 'select',
				'default'		=> '2',
				'title'			=> esc_html__('Columns', 'fallsky'),
				'sanitize_cb'	=> 'fallsky_widget_sanitize_choice',
				'dependency'	=> array('layout' => array('value' => array('masonry'))),
				'choices'		=> array(
					'2' => array('label' => esc_html__('2 Column', 'fallsky')),
					'3'	=> array('label' => esc_html__('3 Columns', 'fallsky'))
				)
			));
			$this->add_control(array(
				'id' 			=> 'card-column',
				'type'			=> 'select',
				'default'		=> '1',
				'title'			=> esc_html__('Columns', 'fallsky'),
				'sanitize_cb'	=> 'fallsky_widget_sanitize_choice',
				'dependency'	=> array('layout' => array('value' => array('card'))),
				'choices'		=> array(
					'1' => array('label' => esc_html__('1 Column', 'fallsky')),
					'2'	=> array('label' => esc_html__('2 Columns', 'fallsky')),
					'3' => array('label' => esc_html__('3 Columns', 'fallsky')),
					'4'	=> array('label' => esc_html__('4 Columns', 'fallsky'))
				)
			));
			$this->add_control(array(
				'id' 			=> 'card-color',
				'type'			=> 'select',
				'default'		=> 'light-card',
				'title'			=> esc_html__('Card Color', 'fallsky'),
				'sanitize_cb'	=> 'fallsky_widget_sanitize_choice',
				'dependency'	=> array('layout' => array('value' => array('card'))),
				'choices'		=> array(
					'light-card' 	=> array('label' => esc_html__('Light', 'fallsky')),
					'dark-card'		=> array('label' => esc_html__('Dark', 'fallsky'))
				)
			));
			$this->add_control(array(
				'id' 			=> 'grid-column',
				'type'			=> 'select',
				'default'		=> '2',
				'sanitize_cb'	=> 'fallsky_widget_sanitize_choice',
				'title'			=> esc_html__('Columns', 'fallsky'),
				'dependency'	=> array('layout' => array('value' => array('grid'))),
				'choices'		=> array(
					'2'	=> array('label' => esc_html__('2 Columns', 'fallsky')),
					'3' => array('label' => esc_html__('3 Columns', 'fallsky')),
					'4'	=> array('label' => esc_html__('4 Columns', 'fallsky'))
				)
			));
			$this->add_control(array(
				'id' 			=> 'overlay-column',
				'type'			=> 'select',
				'default'		=> '1',
				'sanitize_cb'	=> 'fallsky_widget_sanitize_choice',
				'title'			=> esc_html__('Columns', 'fallsky'),
				'dependency'	=> array('layout' => array('value' => array('overlay'))),
				'choices'		=> array(
					'1' => array('label' => esc_html__('1 Column', 'fallsky')),
					'2'	=> array('label' => esc_html__('2 Columns', 'fallsky')),
					'3' => array('label' => esc_html__('3 Columns', 'fallsky')),
					'4'	=> array('label' => esc_html__('4 Columns', 'fallsky'))
				)
			));
			$this->add_control(array(
				'id' 			=> 'overlay-mix-column',
				'type'			=> 'select',
				'default'		=> '1',
				'sanitize_cb'	=> 'fallsky_widget_sanitize_choice',
				'title'			=> esc_html__('Columns', 'fallsky'),
				'dependency'	=> array('layout' => array('value' => array('overlay-mix'))),
				'choices'		=> array(
					'1-2-mix' 	=> array('label' => esc_html__('1+2 Columns', 'fallsky')),
					'1-4-mix'	=> array('label' => esc_html__('1+4 Columns', 'fallsky')),
					'2-3-mix' 	=> array('label' => esc_html__('2+3 Columns', 'fallsky')),
					'1-2-2-mix'	=> array('label' => esc_html__('1+2+2 Columns', 'fallsky'))
				)
			));
			$this->add_control( array(
				'id' 			=> 'featured-image-orientation',
				'type'			=> 'select',
				'default'		=> '',
				'sanitize_cb'	=> 'fallsky_widget_sanitize_choice',
				'title'			=> esc_html__( 'Featured Image Orientation', 'fallsky' ),
				'dependency'	=> array( 'layout' => array( 'value' => array( 'grid' ) ) ),
				'choices'		=> array(
					'' 				=> array( 'label' => esc_html__( 'Landscape', 'fallsky' ) ),
					'img-square' 	=> array( 'label' => esc_html__( 'Square', 'fallsky' ) ),
					'img-portrait'	=> array( 'label' => esc_html__( 'Portrait', 'fallsky' ) )
				)
			) );
			$this->add_control(array(
				'id' 			=> 'show-post-excerpt',
				'type'			=> 'checkbox',
				'default'		=> 'on',
				'dependency'	=> array('layout' => array('value' => array('masonry', 'list', 'zigzag', 'grid', 'card'))),
				'sanitize_cb'	=> 'fallsky_widget_sanitize_checkbox',
				'title'			=> esc_html__('Show post excerpt', 'fallsky')
			));
			$this->add_control(array(
				'id' 			=> 'show-read-more',
				'type'			=> 'checkbox',
				'default'		=> 'on',
				'dependency'	=> array('layout' => array('value' => array('masonry', 'zigzag', 'card'))),
				'sanitize_cb'	=> 'fallsky_widget_sanitize_checkbox',
				'title'			=> esc_html__('Show Read More button', 'fallsky')
			));
			$this->add_control(array(
				'id' 			=> 'center-text',
				'type'			=> 'checkbox',
				'default'		=> '',
				'dependency'	=> array('layout' => array('value' => array( 'masonry', 'grid' ))),
				'sanitize_cb'	=> 'fallsky_widget_sanitize_checkbox',
				'title'			=> esc_html__('Center text', 'fallsky')
			));
			$this->add_control(array(
				'id' 		=> 'post-meta-title',
				'type'		=> 'title',
				'default'	=> '',
				'title'		=> esc_html__('Display selected post meta', 'fallsky'),
			));
			$this->add_control(array(
				'id' 			=> 'post-meta-category',
				'type'			=> 'checkbox',
				'default'		=> 'on',
				'sanitize_cb'	=> 'fallsky_widget_sanitize_checkbox',
				'title'			=> esc_html__('Category', 'fallsky')
			));
			$this->add_control(array(
				'id' 			=> 'post-meta-author',
				'type'			=> 'checkbox',
				'default'		=> '',
				'sanitize_cb'	=> 'fallsky_widget_sanitize_checkbox',
				'title'			=> esc_html__('Author', 'fallsky')
			));
			$this->add_control(array(
				'id' 			=> 'post-meta-date',
				'type'			=> 'checkbox',
				'default'		=> '',
				'sanitize_cb'	=> 'fallsky_widget_sanitize_checkbox',
				'title'			=> esc_html__('Publish Date', 'fallsky')
			));
			$this->add_control(array(
				'id' 			=> 'post-meta-view',
				'type'			=> 'checkbox',
				'default'		=> '',
				'sanitize_cb'	=> 'fallsky_widget_sanitize_checkbox',
				'title'			=> esc_html__('View Count', 'fallsky')
			));
			$this->add_control(array(
				'id' 			=> 'post-meta-like',
				'type'			=> 'checkbox',
				'default'		=> '',
				'sanitize_cb'	=> 'fallsky_widget_sanitize_checkbox',
				'title'			=> esc_html__('Like Count', 'fallsky')
			));
			$this->add_control(array(
				'id' 			=> 'post-meta-comment',
				'type'			=> 'checkbox',
				'default'		=> '',
				'sanitize_cb'	=> 'fallsky_widget_sanitize_checkbox',
				'title'			=> esc_html__('Comment Count', 'fallsky')
			));
			$this->add_control(array(
				'id' 			=> 'number',
				'type'			=> 'number',
				'default'		=> get_option('posts_per_page', 10),
				'sanitize_cb'	=> 'fallsky_widget_sanitize_number',
				'title'			=> esc_html__('Show at most x posts per page', 'fallsky'),
				'input_attr'	=> array( 'min' => 1 )
			));
			$this->add_control(array(
				'id' 			=> 'pagination',
				'type'			=> 'checkbox',
				'default'		=> 'on',
				'dependency'	=> array('filter-by' => array('value' => array('latest'))),
				'sanitize_cb'	=> 'fallsky_widget_sanitize_checkbox',
				'title'			=> esc_html__('Display Pagination', 'fallsky')
			));
			$this->add_control(array(
				'id' 			=> 'fullwidth',
				'type'			=> 'checkbox',
				'default'		=> '',
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_checkbox',
				'title'			=> esc_html__('Make the container to be fullwidth when homepage has no sidebar', 'fallsky')
			));
		}
		/**
		* Get post format list
		* @return array post format list
		*/
		private function get_post_formats(){
			$formats = apply_filters('fallsky_post_formats', array());
			$options = array();
			foreach($formats as $f){
				$options[$f] = ucfirst($f);
			}
			return $options;
		}
		/**
		* Test option is check
		* @param option name
		* @return boolean true if check, otherwise false
		*/
		private function is_checked($option){
			return ('on' == $this->get_value($option));
		}
		/**
		* Add frontend javascript variables
		* @param array 
		* @return array
		*/
		public static function frontend_js_vars($vars = array()){
			$widgets = array(
				'fallsky-homepage-widget-posts'	=> 'Fallsky_Homepage_Widget_Posts'
			);
			if(isset($vars['widgets'])){
				$widgets = array_merge($vars['widgets'], $widgets);
			}
			$vars['widgets'] = $widgets;

			return $vars;
		}
	}

	add_filter('fallsky_frontend_js_vars', array('Fallsky_Homepage_Widget_Posts', 'frontend_js_vars'));
}
