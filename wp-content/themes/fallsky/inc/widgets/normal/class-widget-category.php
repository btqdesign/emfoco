<?php
/**
* Theme Custom Widget Category
*/

if(!class_exists('Fallsky_Widget_Category')){
	class Fallsky_Widget_Category extends Fallsky_Widget{
		public function __construct(){
			$widget_ops = array(
				'classname' 					=> 'fallsky-widget_cat',
				'description' 					=> esc_html__('Display your selected categories with background image', 'fallsky'),
				'customize_selective_refresh' 	=> true,
			);
			parent::__construct('fallsky-widget_cat', esc_html__('Fallsky Category', 'fallsky'), $widget_ops);
		}
		/**
		* Print the widget content
		*/
		function widget($args, $instance){
			$this->instance = $instance;

			$title = $this->get_value('title');
			/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
			$title = apply_filters('widget_title', $title, $instance, $this->id_base);

			echo str_replace($this->custom_class, sprintf(' %s %s', $this->get_value('style'), $this->get_value('layout')), $args['before_widget']);
			echo empty($title) ? '' : $args['before_title'] . $title . $args['after_title'];
			print($this->get_content());
			print($args['after_widget']);
		}		
		/**
		 * Generate the main content
		 * @return html string
		 */
		public function get_content(){
			$cids 		= fallsky_convert_tax_slug2id($this->get_value('categories'));
			$content 	= '';
			if(!empty($cids) && is_array($cids)){
				$is_preivew 	= isset($this->instance['is_preview']) ? $this->instance['is_preview'] : false;
				$show_count 	= ('on' == $this->get_value('show-count'));

				if( empty( $this->instance['from_archive'] ) ) {
					do_action( 'fallsky_set_frontend_options', 'sidebar_category' );
				}
				$categories = get_terms(array('include' => $cids, 'taxonomy' => 'category'));
				foreach($categories as $cat){
					$cat_url 	= get_term_link($cat, 'category');
					$img_html	= apply_filters(
						'loftocean_get_taxonomy_image_bg', 
						false, 
						$cat, 
						apply_filters( 'fallsky_image_sizes', false ), 
						array( 'class' => 'cat-bg' )
					);
					$cat_name 	= $cat->name;

					$content .= sprintf(
						'<div class="cat"><a href="%s">%s<div class="cat-meta"><div class="cat-meta-wrapper">%s%s</div></div></a></div>',
						$cat_url,
						$img_html,
						sprintf('<span class="category-name">%s</span>', $cat_name),
						$show_count || $is_preivew ? sprintf(
							'<span class="counts%s">%s</span>', 
							$show_count ? '' : ' hide',
							fallsky_get_category_post_count($cat)
						) : ''
					);
				}
				if(empty($this->instance['from_archive'])){
					do_action('fallsky_reset_frontend_options');
				}
				return sprintf('<div class="cat-list">%s</div>', $content);
			}
			else{
				return sprintf('<p class="error-message nothing-found category">%s</p>', esc_html__('No category selected.', 'fallsky'));
			}
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
				'title'			=> esc_html__('Title', 'fallsky'),
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_text'
			));
			$this->add_control(array(
				'id' 			=> 'categories',
				'type'			=> 'select',
				'default'		=> array(),
				'input_attr'	=> array('multiple' => 'multiple'),
				'title'			=> esc_html__('Choose Categories', 'fallsky'),
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_choices',
				'choices'		=> fallsky_get_terms('category', false)
			));
			$this->add_control(array(
				'id' 			=> 'style',
				'type'			=> 'select',
				'default'		=> 'style-rectangle',
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_choice',
				'title'			=> esc_html__('Category Style', 'fallsky'),
				'choices'		=> array(
					'style-rectangle' 	=> esc_html__('Rectangle', 'fallsky'),
					'style-circle'		=> esc_html__('Circle', 'fallsky'),
					'style-stripe' 		=> esc_html__('Stripe', 'fallsky')
				)
			));
			$this->add_control(array(
				'id' 			=> 'layout',
				'type'			=> 'select',
				'default'		=> 'column-2',
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_choice',
				'dependency'	=> array('style' => array('value' => array('circle'))),
				'title'			=> esc_html__('Category Layout', 'fallsky'),
				'choices'		=> array(
					'column-2' 	=> esc_html__('2 Columns', 'fallsky'),
					'column-3'	=> esc_html__('3 Columns', 'fallsky'),
					'column-4' 	=> esc_html__('4 Columns', 'fallsky'),
					'column-5'	=> esc_html__('5 Columns', 'fallsky')
				)
			));
			$this->add_control(array(
				'id' 			=> 'show-count',
				'type'			=> 'checkbox',
				'default'		=> 'on',
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_checkbox',
				'title'			=> esc_html__('Show Post Counts', 'fallsky')
			));
		}
	}

	add_action('widgets_init', function(){ register_widget('Fallsky_Widget_Category'); });
}

