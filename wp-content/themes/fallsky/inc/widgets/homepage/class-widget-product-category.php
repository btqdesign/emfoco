<?php
/**
* Theme Custom Widget for Homepage only 
*/

if(!class_exists('Fallsky_Homepage_Widget_Product_Category') && class_exists('WooCommerce')){
	class Fallsky_Homepage_Widget_Product_Category extends Fallsky_Widget {
		public function __construct(){
			$widget_ops = array(
				'classname' 					=> 'woocommerce product-categories',
				'description' 					=> esc_html__('Add Product Categories to your homepage.', 'fallsky'),
				'customize_selective_refresh' 	=> false
			);
			parent::__construct('fallsky-homepage-widget-product-categories', esc_html__('Product Categories', 'fallsky'), $widget_ops);
		}
		/**
		 * Generate the main content
		 * @return html string
		 */
		public function get_content(){
			$cids 		= (array)$this->get_value('categories');
			$cids 		= fallsky_convert_tax_slug2id($cids, 'product_cat');
			$content 	= '';
			if(!empty($cids) && is_array($cids)){
				$index 	= 0;
				$layout = $this->get_value('layout');
				$cols 	= array('cols-2' => 2, 'cols-3' => 3, 'cols-4' => 4, 'cols-5' => 5);
				$mod 	= isset($cols[$layout]) ? $cols[$layout] : 3;
				$class 	= array('products', 'layout-grid', $layout); 
				$cates 	= get_terms(array('include' => $cids, 'taxonomy' => 'product_cat'));
				foreach($cates as $cat){
					$cat_url 	= get_term_link($cat, 'product_cat');
					$image_id 	= get_term_meta($cat->term_id, 'thumbnail_id', true);
					$image_src	= fallsky_get_image_src( $image_id, 'full', false );
					$cat_name 	= $cat->name;
					$cat_count 	= $cat->count;
					ob_start();
					woocommerce_subcategory_thumbnail($cat);
					$image 		= ob_get_clean();

					$content .= sprintf(
						'<li class="product-category product%s"><a href="%s">%s%s</a></li>',
						(0 == ($index % $mod)) ? ' first' : (($mod - 1) == ($index % $mod) ? ' last' : ''),
						$cat_url,
						$image_src ? $image : '',
						sprintf(
							'<h2 class="woocommerce-loop-category__title">%s<mark class="count">(%d)</mark></h2>',
							$cat_name,
							$cat_count
						)
					);
					$index ++;
				}
				return sprintf('<ul class="%s">%s</ul>', implode(' ', $class), $content);
			}
			else{
				return sprintf('<p class="error-message nothing-found product-category">%s</p>', esc_html__('No product category selected.', 'fallsky'));
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
					'light-color'	=> array('label' => esc_html__('Light', 'fallsky')),
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
				'default'		=> 'latest',
				'input_attr'	=> array('multiple' => 'multiple'),
				'title'			=> esc_html__('Choose Categories', 'fallsky'),
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_choices',
				'choices'		=> array()
			));
			$this->add_control(array(
				'id' 			=> 'layout',
				'type'			=> 'select',
				'default'		=> 'cols-2',
				'title'			=> esc_html__('Category Layout', 'fallsky'),
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_choice',
				'choices'		=> array(
					'cols-2' => esc_html__('2 Columns', 'fallsky'),
					'cols-3' => esc_html__('3 Columns', 'fallsky'),
					'cols-4' => esc_html__('4 Columns', 'fallsky'),
					'cols-5' => esc_html__('5 Columns', 'fallsky')
				)
			));
		}
		public function form($instance){
			$this->update_control_category();
			parent::form($instance);
		}
		private function update_control_category(){
			$this->controls['categories']['choices'] = fallsky_get_terms('product_cat', false);
		}
	}
}
