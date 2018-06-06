<?php
/**
* Theme Custom Widget Post
*/

if(!class_exists('Fallsky_Widget_Posts')){
	class Fallsky_Widget_Posts extends Fallsky_Widget{
		public function __construct(){
			$widget_ops = array(
				'classname' 					=> 'fallsky-widget_posts',
				'description' 					=> esc_html__('Display your posts.', 'fallsky'),
				'customize_selective_refresh' 	=> true,
			);
			parent::__construct('fallsky-widget_posts', esc_html__('Fallsky Posts', 'fallsky'), $widget_ops);
		}
		/**
		* Print the widget content
		*/
		function widget($args, $instance){ 
			$this->instance = $instance;

			$title = $this->get_value('title');
			/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
			$title = apply_filters('widget_title', $title, $instance, $this->id_base);

			echo str_replace($this->custom_class, $this->get_class(), $args['before_widget']);
			echo empty($title) ? '' : $args['before_title'] . $title . $args['after_title']; 
			print($this->get_content());
			print($args['after_widget']);
		}
		/**
		 * Generate the main content
		 * @return html string
		 */
		public function get_content(){
			$posts 		= $this->get_posts();
			$wrap 		= '<ul>%s</ul>';
			$items 		= array();
			$tmpl 		= '<li><a href="%s">%s<div class="post-content"><h4 class="post-title">%s</h4>%s</div></a></li>';
			$size 		= ('small-thumbnail' == $this->get_value('style')) ? 'thumbnail' : 'medium';
			$meta 		= $this->get_meta_show();
			$has_meta 	= !empty($meta);
		
			if($posts->have_posts()){
				while($posts->have_posts()){
					$posts->the_post();
					$pid 		= get_the_ID();
					$post_meta 	= '';
					$thumnail 	= has_post_thumbnail() ? sprintf(
						'<div class="thumbnail"%s>%s</div>',
						'thumbnail' == $size ? '' 
							: sprintf(' style="background-image: url(%s);"', esc_url( fallsky_get_image_src( get_post_thumbnail_id( $pid ), 'fallsky_small_medium', false ) ) ),
						get_the_post_thumbnail($pid, $size)
					) : '';
					if($has_meta){
						if(in_array('category', $meta)){
							$categories = get_the_category();
							$cat_names 	= array();
							foreach($categories as $cat){ 
								array_push($cat_names, $cat->name); 
							}
							$post_meta .= sprintf('<span class="meta-item">%s</span>', implode(', ', $cat_names));
						}
						if(in_array('date', $meta)){
							$post_meta .= sprintf(
								'<time class="published meta-item" datetime="%s">%s</time>',
								esc_attr(get_the_date('c')),
								get_the_date()
							);
						}
						if(in_array('author', $meta)){
							$post_meta .= sprintf(
								'<span class="meta-item">%s</span>',
								esc_attr(get_the_author())
							);
						}
						if(in_array('view', $meta)){
							$view = apply_filters('loftocean_view_count_number', 0);
							if(!empty($view)){
								$post_meta .= sprintf(
									'<span class="meta-item">%s</span>',
									sprintf(esc_html(_n('%s View', '%s Views', $view, 'fallsky')), $view)
								); 
							}
						}
						if(in_array('like', $meta)){
							$like = apply_filters('loftocean_like_count_number', 0);
							if(!empty($like)){
								$post_meta .= sprintf(
									'<span class="meta-item">%s</span>',
									sprintf(esc_html(_n('%s Like', '%s Likes', $like, 'fallsky')), $like)
								); 
							}
						}
						if(in_array('comment', 	$meta)){ 
							$num = get_comments_number();
							if(comments_open() && !empty($num)){
								$post_meta .= sprintf(
									'<span class="meta-item">%s</span>',
									sprintf(esc_html(_n('%s Comment', '%s Comments', $num, 'fallsky')), $num)
								);
							}
						}
						$post_meta = sprintf('<div class="post-meta">%s</div>', $post_meta);
					}

					$items[] 	= sprintf(
						$tmpl,
						get_permalink(),
						$thumnail,
						get_the_title(),
						$post_meta
					);

					if('mixed-thumbnail' == $this->get_value('style')){
						$size = 'thumbnail';
					}
				}
				wp_reset_postdata();
				return sprintf($wrap, implode('', $items));
			}
			else{
				return sprintf(
					'<div class="post-content"><h4 class="post-title">%s</h4></div>',
					esc_html__('Nothing Found', 'fallsky')
				);
			}
		}
		/**
		* Get widget custom classes
		* @return string class
		*/
		private function get_class(){
			$class = array($this->get_value('style'));
			if($this->is_checked('show-list-number')){
				array_push($class, 'with-post-number');
			}
			return sprintf(' %s', implode(' ', $class));
		}
		/**
		* Get posts by current widget settings
		* @return object WP_Query object
		*/
		private function get_posts(){
			if(!isset($this->posts[$this->id])){
				$ppp 	= $this->get_value('number');
				$args 	= array('posts_per_page' => $ppp, 'ignore_sticky_posts' => true, 'paged' => 1, 'post_type' => 'post'); 
				$this->posts[$this->id] = new WP_Query(apply_filters(
					'fallsky_widget_posts_list_args', 
					$args, 
					array_merge($this->defaults, $this->instance)
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
				'category' 	=> 'post-meta-category',
				'author' 	=> 'post-meta-author',
				'date' 		=> 'post-meta-date',
				'like' 		=> 'post-meta-like',
				'view' 		=> 'post-meta-view',
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
				'title'			=> esc_html__('Title:', 'fallsky')
			));
			$this->add_control(array(
				'id' 			=> 'filter-by',
				'type'			=> 'select',
				'default'		=> 'latest',
				'title'			=> esc_html__('Choose Posts:', 'fallsky'),
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
				'id' 			=> 'style',
				'type'			=> 'select',
				'default'		=> 'small-thumbnail',
				'title'			=> esc_html__('Style:', 'fallsky'),
				'sanitize_cb'	=> 'fallsky_widget_sanitize_choice',
				'choices'		=> array(
					'small-thumbnail' 	=> esc_html__('Small Thumbnail', 'fallsky'),
					'large-thumbnail'	=> esc_html__('Large Thumbnail', 'fallsky'),
					'mixed-thumbnail' 	=> esc_html__('Large First + Small Thumbnail', 'fallsky')
				)
			));
			$this->add_control(array(
				'id' 			=> 'show-list-number',
				'type'			=> 'checkbox',
				'default'		=> 'on',
				'sanitize_cb'	=> 'fallsky_widget_sanitize_checkbox',
				'title'			=> esc_html__('Show list number', 'fallsky')
			));
			$this->add_control(array(
				'id' 			=> 'number',
				'type'			=> 'number',
				'default'		=> 3,
				'sanitize_cb'	=> 'fallsky_widget_sanitize_number',
				'title'			=> esc_html__('Number of posts to show', 'fallsky'),
				'input_attr'	=> array( 'min' => 1, 'max' => 10 )
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
				'default'		=> '',
				'sanitize_cb'	=> 'fallsky_widget_sanitize_checkbox',
				'title'			=> esc_html__('Category', 'fallsky')
			));
			$this->add_control(array(
				'id' 			=> 'post-meta-date',
				'type'			=> 'checkbox',
				'default'		=> '',
				'sanitize_cb'	=> 'fallsky_widget_sanitize_checkbox',
				'title'			=> esc_html__('Publish Date', 'fallsky')
			));
			$this->add_control(array(
				'id' 			=> 'post-meta-author',
				'type'			=> 'checkbox',
				'default'		=> '',
				'sanitize_cb'	=> 'fallsky_widget_sanitize_checkbox',
				'title'			=> esc_html__('Author', 'fallsky')
			));
			$this->add_control(array(
				'id' 			=> 'post-meta-view',
				'type'			=> 'checkbox',
				'default'		=> '',
				'sanitize_cb'	=> 'fallsky_widget_sanitize_checkbox',
				'title'			=> esc_html__('View Counts', 'fallsky')
			));
			$this->add_control(array(
				'id' 			=> 'post-meta-like',
				'type'			=> 'checkbox',
				'default'		=> '',
				'sanitize_cb'	=> 'fallsky_widget_sanitize_checkbox',
				'title'			=> esc_html__('Like Counts', 'fallsky')
			));
			$this->add_control(array(
				'id' 			=> 'post-meta-comment',
				'type'			=> 'checkbox',
				'default'		=> '',
				'sanitize_cb'	=> 'fallsky_widget_sanitize_checkbox',
				'title'			=> esc_html__('Comment Counts', 'fallsky')
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
	}

	add_action('widgets_init', function(){ register_widget('Fallsky_Widget_Posts'); });
}
