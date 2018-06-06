<?php
/**
* Theme Custom Widget for Homepage only 
*/

if(!class_exists('Fallsky_Homepage_Widget_Products') && class_exists('WooCommerce')){
	class Fallsky_Homepage_Widget_Products extends Fallsky_Widget{
		private $overlay = false;
		private $options = array();
		public function __construct(){
			$widget_ops = array(
				'classname' 					=> 'woocommerce products',
				'description' 					=> esc_html__('Add Products to your homepage.', 'fallsky'),
				'customize_selective_refresh' 	=> false
			);
			parent::__construct('fallsky-homepage-widget-products', esc_html__('Products', 'fallsky'), $widget_ops);
		}
		/**
		 * Generate the main content
		 * @return html string
		 */
		public function get_content(){
			$products 		= $this->get_products();
			$this->overlay	= ($this->get_value('style') == 'overlay');
			$this->options	= array(
				'show_title' 	=> ('on' == $this->get_value('show-title')),
				'show_price' 	=> ('on' == $this->get_value('show-price')),
				'show_rating'	=> ('on' == $this->get_value('show-rating')),
				'show_sale'		=> ('on' == $this->get_value('show-sale-label'))
			);

			return $products->have_posts() ? $this->get_list_html($products) : sprintf(
				'<p class="woocommerce-info">%s</p>', 
				esc_html__('No products were found matching your selection.', 'fallsky')
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
					$category 		= fallsky_convert_tax_slug2id($this->get_value('category'), 'product_cat');
					$category 		= intval($category);
					$category_link 	= empty($category) ? '' : get_term_link($category, 'product_cat');
					$title = sprintf(
						'%s%s%s',
						sprintf($args['before_title'], $title_class),
						empty($category_link) ? $title : sprintf('<a href="%s">%s</a>', $category_link, $title),
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
		* Get product items based on current settings
		* @return object WP_Query object
		*/
		private function get_products(){
			$filter = $this->get_value('filter-by');
			$number = intval($this->get_value('number'));
			$number = empty($number) ? 12 : $number;
			$args	= array(
				'posts_per_page'		=> $number, 
				'offset' 				=> 0, 
				'ignore_sticky_posts' 	=> true, 
				'post_type' 			=> 'product'
			);

			switch($filter){
				case 'category':
					$category = fallsky_convert_tax_slug2id($this->get_value('category'), 'product_cat');
					$category = intval($category);
					if(!empty($category)){
						$args['tax_query'] = array(
							array(
								'taxonomy' => 'product_cat',
								'field'    => 'term_id',
								'terms'    => $category
							)
						);
					}
					break;
				case 'featured':
					$product_visibility_term_ids = wc_get_product_visibility_term_ids();
					$args['tax_query']= array(
						array(
							'taxonomy' => 'product_visibility',
							'field'    => 'term_taxonomy_id',
							'terms'    => $product_visibility_term_ids['featured'],
						)
					);
					break;
			}
			return new WP_Query($args);
		}
		/**
		* Get overlay classes
		* @param array class list
		* @return string class string
		*/
		private function list_classes($class = array()){
			if($this->overlay){
				array_push($class, 'style-overlay');
				array_push($class, $this->get_value('overlay-color-scheme'));
			}
			return empty($class) ? '' : sprintf(' class="%s"', implode(' ', $class));
		}
		/**
		* Print item html with overlay
		*/
		private function overlay_item(){
			$options = $this->options;
			$product_url = esc_url(get_permalink()); ?>
			<a href="<?php echo esc_url(get_permalink()); ?>" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">
				<?php $options['show_sale'] ? woocommerce_show_product_loop_sale_flash() : ''; ?>
				<?php woocommerce_template_loop_product_thumbnail(); ?>
				<div class="product-info">
					<?php $options['show_title'] 	? printf('<h2 class="woocommerce-loop-product__title">%s</h2>', get_the_title()) : ''; ?>
					<?php $options['show_price'] 	? woocommerce_template_loop_price() : ''; ?>
					<?php $options['show_rating'] 	? woocommerce_template_loop_rating() : ''; ?>
				</div>
			</a> <?php
			woocommerce_template_loop_add_to_cart();
		}
		/**
		* Print item html without overlay
		*/
		private function normal_item(){
			$options = $this->options;
			$product_url = esc_url(get_permalink());
			if(has_post_thumbnail()) : ?>
				<div class="product-image">
					<a href="<?php print($product_url); ?>" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">
						<?php $options['show_sale'] ? woocommerce_show_product_loop_sale_flash() : ''; ?>
						<?php do_action('fallsky_woocommerce_label'); ?>
						<?php woocommerce_template_loop_product_thumbnail(); ?>
					</a>
					<?php woocommerce_template_loop_add_to_cart(); ?>
				</div> <?php
			endif;
			if($options['show_title'] || $options['show_price'] || $options['show_rating']) : ?>
				<a href="<?php print($product_url); ?>" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">
					<?php $options['show_title'] 	? printf('<h2 class="woocommerce-loop-product__title">%s</h2>', get_the_title()) : ''; ?>
					<?php $options['show_price'] 	? woocommerce_template_loop_price() : ''; ?>
					<?php $options['show_rating'] 	? woocommerce_template_loop_rating() : ''; ?>
				</a> <?php
			endif;
		}
		/**
		* Get product list html
		* @param object WP_Query object
		* @return html string
		*/
		private function get_list_html($products){
			$mod 		= false;
			$method 	= array();
			$start 		= $end = $wrap = '';
			$class 		= array('products');
			$is_grid 	= ('grid' == $this->get_value('layout'));
			$cols 		= array('cols-2' => 2, 'cols-3' => 3, 'cols-4' => 4, 'cols-5' => 5, 'cols-6' => 6, 'cols-7' => 7, 'cols-8' => 8);
			if($is_grid){
				$col 	= $this->get_value('grid-column');
				$class 	= array_merge($class, array('layout-grid', $col));
				$wrap 	= '<ul%s>%s</ul>';
				$start 	= '<li class="%s">';
				$end 	= '</li>';
				$mod 	= isset($cols[$col]) ? $cols[$col] : 3;
			}
			else{
				$col 	= $this->get_value('carousel-column');
				$class 	= array_merge($class, array('layout-carousel', $col));
				$wrap 	= sprintf('<div%s data-slides-to-show=%d>%s</div>', '%s', (isset($cols[$col]) ? $cols[$col] : 3), '%s');
				$start 	= '<div class="%s">';
				$end 	= '</div>';
			}

			ob_start();
			while($products->have_posts()){
				$products->the_post();
				do_action('woocommerce_shop_loop'); 
				$pclass = array_diff(get_post_class(), array('first', 'last'));
				if($is_grid){
					$pindex = $products->current_post;
					(($pindex % $mod) == 0) ? array_push($pclass, 'first') : ((($mod - 1) == ($pindex % $mod)) ? array_push($pclass,  'last') : '');
				}
				
				printf($start, implode(' ', $pclass));
				$this->overlay ? $this->overlay_item() : $this->normal_item();
				print($end);
			}
			wp_reset_postdata();

			return sprintf(
				$wrap,
				$this->list_classes($class),
				ob_get_clean()
			);
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
				'id' 			=> 'filter-by',
				'type'			=> 'select',
				'default'		=> 'latest',
				'title'			=> esc_html__('Choose products', 'fallsky'),
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_choice',
				'choices'		=> array(
					'latest'	=> esc_html__('Latest', 'fallsky'),
					'category'	=> esc_html__('From a selected category', 'fallsky'),
					'featured'	=> esc_html__('Featured', 'fallsky')
				)
			));
			$this->add_control(array(
				'id' 			=> 'category',
				'type'			=> 'select',
				'default'		=> '',
				'title'			=> '',
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_text',
				'dependency'	=> array('filter-by' => array('value' => array('category'))),
				'choices'		=> array()
			));
			$this->add_control(array(
				'id' 			=> 'layout',
				'type'			=> 'select',
				'default'		=> 'grid',
				'title'			=> esc_html__('Products Layout', 'fallsky'),
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_choice',
				'choices'		=> array(
					'grid'		=> esc_html__('Grid', 'fallsky'),
					'carousel'	=> esc_html__('Carousel', 'fallsky')
				)
			));
			$this->add_control(array(
				'id' 			=> 'grid-column',
				'type'			=> 'select',
				'default'		=> 'cols-3',
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_choice',
				'dependency'	=> array('layout' => array('value' => array('grid'))),
				'title'			=> esc_html__('Columns', 'fallsky'),
				'choices'		=> array(
					'cols-2' => esc_html__('2 Columns', 'fallsky'),
					'cols-3' => esc_html__('3 Columns', 'fallsky'),
					'cols-4' => esc_html__('4 Columns', 'fallsky')
				)
			));
			$this->add_control(array(
				'id' 			=> 'carousel-column',
				'type'			=> 'select',
				'default'		=> 'cols-3',
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_choice',
				'dependency'	=> array('layout' => array('value' => array('carousel'))),
				'title'			=> esc_html__('Columns', 'fallsky'),
				'choices'		=> array(
					'cols-3' => esc_html__('3 Columns', 'fallsky'),
					'cols-4' => esc_html__('4 Columns', 'fallsky'),
					'cols-5' => esc_html__('5 Columns', 'fallsky'),
					'cols-6' => esc_html__('6 Columns', 'fallsky'),
					'cols-7' => esc_html__('7 Columns', 'fallsky'),
					'cols-8' => esc_html__('8 Columns', 'fallsky')
				)
			));
			$this->add_control(array(
				'id' 			=> 'style',
				'type'			=> 'select',
				'default'		=> 'normal',
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_choice',
				'title'			=> esc_html__('Products Style', 'fallsky'),
				'choices'		=> array(
					'normal' 	=> esc_html__('Normal', 'fallsky'),
					'overlay' 	=> esc_html__('Overlay', 'fallsky')
				)
			));
			$this->add_control(array(
				'id' 			=> 'overlay-color-scheme',
				'type'			=> 'radio',
				'default'		=> 'overlay-light-color',
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_choice',
				'title'			=> esc_html__('Overlay Color Scheme', 'fallsky'),
				'dependency'	=> array('style' => array('value' => array('overlay'))),
				'choices'		=> array(
					'overlay-light-color'	=> esc_html__('Light', 'fallsky'),
					'overlay-dark-color' 	=> esc_html__('Dark', 'fallsky')
				)
			));
			$this->add_control(array(
				'id' 			=> 'number',
				'type'			=> 'number',
				'default'		=> '12',
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_number',
				'title'			=> esc_html__('Show at most x products in the section', 'fallsky')
			));
			$this->add_control(array(
				'id' 			=> 'show-title',
				'type'			=> 'checkbox',
				'default'		=> 'on',
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_checkbox',
				'title'			=> esc_html__('Show Title', 'fallsky')
			));
			$this->add_control(array(
				'id' 			=> 'show-price',
				'type'			=> 'checkbox',
				'default'		=> 'on',
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_checkbox',
				'title'			=> esc_html__('Show Price', 'fallsky')
			));
			$this->add_control(array(
				'id' 			=> 'show-rating',
				'type'			=> 'checkbox',
				'default'		=> 'on',
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_checkbox',
				'title'			=> esc_html__('Show Rating', 'fallsky')
			));
			$this->add_control(array(
				'id' 			=> 'show-sale-label',
				'type'			=> 'checkbox',
				'default'		=> 'on',
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_checkbox',
				'title'			=> esc_html__('Show On Sale Label', 'fallsky')
			));
			$this->add_control(array(
				'id' 			=> 'widget-description',
				'type'			=> 'description',
				'default'		=> '',
				'description'	=> esc_html__('Please note: For column 6/7/8, products text and labels will be hiddeen when home page has sidebar.', 'fallsky')
			));
		}
		public function form($instance){
			$this->update_control_category();
			parent::form($instance);
		}
		private function update_control_category(){
			$this->controls['category']['choices'] = fallsky_get_terms('product_cat', true, esc_html__('Choose a category', 'fallsky'));
		}
	}
}
