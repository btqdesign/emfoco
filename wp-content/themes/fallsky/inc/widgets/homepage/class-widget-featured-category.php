<?php
/**
* Theme Custom Widget for Homepage only 
*/

if(!class_exists('Fallsky_Homepage_Widget_Featured_Category')){
	class Fallsky_Homepage_Widget_Featured_Category extends Fallsky_Widget{
		public function __construct(){
			$widget_ops = array(
				'classname' 					=> 'featured-categories',
				'description' 					=> esc_html__('Add Featured Category to your homepage.', 'fallsky'),
				'customize_selective_refresh' 	=> false
			);
			parent::__construct('fallsky-homepage-widget-featured-category', esc_html__('Featured Category', 'fallsky'), $widget_ops);
		}
		/**
		 * Generate the main content
		 * @return html string
		 */
		public function get_content(){
			$cids 		= fallsky_convert_tax_slug2id((array)$this->get_value('categories'));
			$content 	= '';
			if(!empty($cids) && is_array($cids)){
				$classes	= array('widget', 'fallsky-widget_cat', $this->get_value('style'), $this->get_value('layout'));
				$show_count = ('on' == $this->get_value('show-count'));

				do_action( 'fallsky_set_frontend_options', 'widget_category', array( 'column' => $this->get_value( 'layout' ) ) );
				$categories = get_terms(array('include' => $cids, 'taxonomy' => 'category'));
				foreach($categories as $cat){
					$cat_url 	= get_term_link($cat, 'category');
					$cat_name 	= $cat->name;
					$img_html	= apply_filters(
						'loftocean_get_taxonomy_image_bg', 
						false, 
						$cat, 
						apply_filters( 'fallsky_image_sizes', false ), 
						array('class' => 'cat-bg')
					);

					$content .= sprintf(
						'<div class="cat"><a href="%s">%s<div class="cat-meta"><div class="cat-meta-wrapper">%s%s</div></div></a></div>',
						$cat_url,
						$img_html,
						sprintf('<span class="category-name">%s</span>', $cat_name),
						$show_count ? sprintf('<span class="counts">%s</span>', $cat->count) : ''
					);
				}
				do_action('fallsky_reset_frontend_options');
				return sprintf('<div class="%s">%s</div>', implode(' ', $classes), $content);
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
				'title'			=> esc_html__('Section Title', 'fallsky'),
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_text'
			 ));
			$this->add_control(array(
				'id' 			=> 'title-align',
				'type'			=> 'checkbox',
				'default'		=> '',
				'title'			=> esc_html__('Center Section Title', 'fallsky'),
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_checkbox'
			));
			$this->add_control(array(
				'id' 			=> 'color',
				'type'			=> 'select',
				'default'		=> 'default',
				'title'			=> esc_html__('Color', 'fallsky'),
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_choice',
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
				'dependency'	=> array('color' => array('value' => array('custom'))),
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_choice',
				'choices'		=> array(
					'light-color' 	=> array('label' => esc_html__('Light', 'fallsky')),
					'dark-color'	=> array('label' => esc_html__('Dark', 'fallsky'))
				)
			));
			$this->add_control(array(
				'id' 			=> 'custom-bg-color',
				'type'			=> 'color-picker',
				'default'		=> '',
				'dependency'	=> array('color' => array('value' => array('custom'))),
				'title'			=> esc_html__('Custom background color', 'fallsky'),
				'sanitize_cb'	=> 'fallsky_widget_sanitize_color'
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
					'style-circle'		=> esc_html__('Circle', 'fallsky')
				)
			));
			$this->add_control(array(
				'id' 			=> 'layout',
				'type'			=> 'select',
				'default'		=> 'column-2',
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_choice',
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
}
