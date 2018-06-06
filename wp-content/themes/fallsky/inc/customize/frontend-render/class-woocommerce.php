<?php
/**
* Woocommerce related frontend render class.
*/

if(!class_exists('Fallsky_Woocommerce_Render')){
	class Fallsky_Woocommerce_Render {
		private $wc_pages 				= array('myaccount', 'cart', 'checkout');
		private $header_bg 				= false;
		private $is_archive 			= false;
		private $is_static_pages 		= false;
		private $is_transparent_header  = false;
		private $name 					= '';
		private $description 			= '';
		private $show_items  			= array();
		public function __construct(){
			$this->load_support();

 			add_action('widgets_init', 					array($this, 'register_sidebar'));
			add_action('wp', 							array($this, 'load_frontend_actions'), 999);
			add_action('pre_get_posts', 				array($this, 'change_posts_per_page'));
			add_action('fallsky_woocommerce_label', 	array($this, 'loop_out_of_stock'), 5);

 			add_filter('theme_page_templates', 			array($this, 'remove_page_template'), 99, 3);
 			add_filter('fallsky_static_pages',			array($this, 'static_pages'));
		}
		/**
		* Initilize to support woocommerce features
		*/
		public function load_support(){
			add_theme_support('woocommerce');
			add_theme_support('wc-product-gallery-zoom');
			add_theme_support('wc-product-gallery-lightbox');
			add_theme_support('wc-product-gallery-slider');
		}
		/**
		* Register sidebar for woocommerce archive/product pages
		*/
		public function register_sidebar(){
			register_sidebar(array(
				'name'          => esc_html__('Shop Sidebar', 'fallsky'),
				'id'            => 'shop-sidebar',
				'description'   => esc_html__('Add widgets here to appear in your shop sidebar.', 'fallsky'),
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h5 class="widget-title">',
				'after_title'   => '</h5>'
			));
		}
		/**
		* Change product archive page posts number
		*/
		public function change_posts_per_page($query){
			if($query->is_main_query() && !is_admin()){
				if(($query->is_post_type_archive('product') || is_product_taxonomy())){
					$ppp = fallsky_get_theme_mod('fallsky_woocommerce_products_per_page');
					$ppp = absint($ppp);
					if($ppp > 0){
						$query->set('posts_per_page', $ppp);
					}
				}
			}
		}
		/**
		* Load frontend related actions
		*/
		public function load_frontend_actions(){
			// The class will be initialized in action wp, so it's saft to call all functions
			add_action( 'fallsky_cart_in_site_header', 	array($this, 'cart_icon'));
			add_action( 'wp_enqueue_scripts', 			array($this, 'enqueue_script'), 5);
			add_filter( 'fallsky_fallback_styles', 		array( $this, 'output_fallback_style' ) );
			add_filter( 'fallsky_inline_style_handler', array($this, 'theme_inline_style_handler'));

 			add_filter('fallsky_theme_css_deps',		array($this, 'theme_css_deps'));

			$wc_static_page_ids = $this->get_woocommerce_pages($this->wc_pages);
			$this->is_static_pages = !empty($wc_static_page_ids) && is_page($wc_static_page_ids);

			if(!is_admin() && ($this->is_shop() || is_product_taxonomy() || is_singular('product') || $this->is_static_pages)){ 
				$this->set_globals(); 

				add_filter('body_class', 									array($this, 'body_class'));
				add_filter( 'fallsky_site_header_class', 					array( $this, 'site_header_class' ) );
				add_filter('fallsky_is_page_header_with_bg', 				array($this, 'is_page_header_with_bg'));
				add_filter('fallsky_is_transparent_site_header', 			array($this, 'is_transparent_site_header'));
				add_filter('fallsky_page_layout', 							array($this, 'page_layout'), 999);
				add_filter('fallsky_get_sidebar_id', 						array($this, 'get_sidebar_id'), 999);
				add_filter('loop_shop_columns', 							array($this, 'loop_columns'), 999);
				add_filter('woocommerce_output_related_products_args', 		array($this, 'related_products_args'), 99);
				add_filter('woocommerce_upsell_display_args', 				array($this, 'upsell_products_args'), 99);
				add_filter('woocommerce_product_review_comment_form_args',	array($this, 'add_comment_form_fields'));

				add_action('fallsky_before_main_content', 				array($this, 'page_header'));
				add_action('woocommerce_before_main_content', 			array($this, 'before_main_content'), 0);
				add_action('woocommerce_after_main_content', 			array($this, 'after_main_content'), 999);
				add_action('woocommerce_sidebar', 						array($this, 'after_sidebar'), 999);
				add_action('woocommerce_single_product_summary', 		array($this, 'social_sharing'), 99);
				add_action('woocommerce_before_shop_loop_item_title', 	array($this, 'loop_out_of_stock'), 5);

				remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
				remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
				remove_action('woocommerce_after_main_content',  'woocommerce_output_content_wrapper_end', 10);
				remove_action('woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10);
			 	remove_action('woocommerce_archive_description', 'woocommerce_product_archive_description', 10);

			 	if(!in_array('sale', $this->show_items)){
			 		remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);
			 	}
			 	if('overlay' == esc_attr(fallsky_get_theme_mod('fallsky_woocommerce_archive_style'))){
					add_action('woocommerce_before_shop_loop_item_title', 	array($this, 'overlay_open_wrap'), 20);
					add_action('woocommerce_after_shop_loop_item_title', 	array($this, 'overlay_close_wrap'), 50);
				 	remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);
				 	if(in_array('rating', $this->show_items)){
						add_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 25);
					}
					if(!in_array('title', $this->show_items)){
						remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10);
					}
					if(!in_array('price', $this->show_items)){
						remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);
					}
				}
				else{
					add_action('woocommerce_before_shop_loop_item', array($this, 'normal_open_wrap'), 1);
					add_action('woocommerce_after_shop_loop_item', 	array($this, 'normal_close_wrap'), 20);

					remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10);
					remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);
					remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);

					add_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_open', 25);
					if(in_array('title', $this->show_items)){
						add_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_title', 30);
					}
					if(in_array('price', $this->show_items)){
						add_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 30);
					}
					if(in_array('rating', $this->show_items)){
						add_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 30);
					}
					add_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 35);
				}

				add_filter('woocommerce_show_page_title', '__return_false', 99);
			}
		}
		/**
		* Remove body class for woocommerce static pages
		* @param array class name list
		* @return array
		*/
		public function body_class($class){
			if($this->is_static_pages){
				$class = array_diff($class, array('page-template-default'));
				array_push($class, 'page-header-layout-2');
			}
			return $class;
		}
		/**
		* Remove page template option for woocommerce pages
		* @param array template list
		* @param object WP_Theme class object
		* @param object WP_Post class object
		*/
		public function remove_page_template($page_templates, $theme, $post){
			$pages = $this->get_woocommerce_pages($this->wc_pages);
			if($post && (count($pages) > 0) && in_array(absint($post->ID), $pages)){
				$page_templates = array();
			}
			return $page_templates;
		}
		/**
		* Get the woocommerce static pages
		*/
		public function static_pages($pages){
			$wc_pages = $this->wc_pages;
			array_push($wc_pages, 'shop');  
			$wc_page_ids = $this->get_woocommerce_pages($wc_pages);
			return is_array($wc_page_ids) ? array_merge($pages, $wc_page_ids) : $pages;
		}
		/**
		* Add shop cart icon to site header
		*/
		public function cart_icon(){
			global $fallsky_is_preview;

			$cart_enabled 	= fallsky_module_enabled('fallsky_woocommerce_show_cart');
			$cart_url 		= function_exists('wc_get_cart_url') ? wc_get_cart_url() : WC()->cart->get_cart_url();
			$cart_class 	= array('site-header-cart', 'menu');
			$fallsky_is_preview && !$cart_enabled ? array_push($cart_class, 'hide') : '';
			array_push($cart_class, esc_attr(fallsky_get_theme_mod('fallsky_woocommerce_cart_button_style'))); 

			if($cart_enabled || $fallsky_is_preview) : ?>
				<div id="site-header-cart" class="<?php echo implode(' ', array_filter($cart_class)); ?>">
					<a class="cart-contents" href="<?php echo esc_url($cart_url); ?>" title="<?php esc_attr_e('View your shopping cart', 'fallsky'); ?>">
						<span class="cart-icon"><span><?php esc_html_e('Cart', 'fallsky'); ?></span></span>
					</a>
					
					<div class="widget woocommerce widget_shopping_cart">
						<div class="widget_shopping_cart_content">
							<?php woocommerce_mini_cart(); ?>
						</div>
					</div>
				</div> <?php
			endif; 
		}
		/**
		* Enqueue woocommerce related style file
		*/
		public function enqueue_script(){
			$asset_uri 		= FALLSKY_ASSETS_URI;
			$asset_version 	= FALLSKY_ASSETS_VERSION;
			wp_enqueue_style('fallsky-theme-woocommerce', $asset_uri . 'css/frontend/fallsky-woocommerce.css', array('fallsky-theme-style'), $asset_version);
		}
		public function output_fallback_style() {
			$asset_uri 		= FALLSKY_ASSETS_URI;
			$asset_version 	= FALLSKY_ASSETS_VERSION;
			return sprintf(
				'<link rel="stylesheet" id="fallsky-woocommerce-fallback-style" href="%s?ver=%s" type="text/css" media="all">',
				$asset_uri . 'css/frontend/fallsky-woocommerce-fallback.css', 
				$asset_version
			);
		}
		/**
		* Add woocommerce style to theme main css dependency list
		*/
		public function theme_css_deps($deps){
			return array_merge($deps, array('woocommerce-general', 'woocommerce-layout', 'woocommerce-smallscreen'));
		}
		/**
		* Theme inline style handler
		*/
		public function theme_inline_style_handler($handler){
			return 'fallsky-theme-woocommerce';
		}
		/**
		* If current page header with bg
		* 	1. Always return false for page header layout 1
		*	2. Return true if has post thumbnail for page header layout 2/3
		*	3. Otherwise return false
		* @param boolean default value
		* @return boolean
		*/
		public function is_page_header_with_bg($has){
			if($this->is_archive || $this->is_static_pages){
				return !empty($this->header_bg);
			}
			return $has;
		}
		/*
		* Test if cucrent page is transparent site header
		* @param array of boolean, ['show on frontend', 'not show on frontend, but show on customize preview page']
		* @return array of boolean
		*/
		public function is_transparent_site_header( $is = array(false, false ) ) {
			return ( $this->is_archive || $this->is_static_pages ) && !empty( $this->header_bg ) && $this->is_transparent_header ? array(true, false) : $is;
		}
		/**
		* Add transparent class to site head if needed
		* @param array class name list
		* @return array
		*/
		public function site_header_class( $class ){
			if( ( $this->is_archive || $this->is_static_pages ) && !empty( $this->header_bg ) && $this->is_transparent_header ) {
				array_push( $class, 'transparent' );
			}
			return $class;
		}
		/*
		* Get current page layout for pages
		* @param string default layout
		* @return string layout
		*/
		public function page_layout($layout){
			return $this->is_static_pages ? '' : (is_singular('product') 
				? esc_attr(fallsky_get_theme_mod('fallsky_woocommerce_sidebar_single')) 
					: esc_attr(fallsky_get_theme_mod('fallsky_woocommerce_sidebar_general')));
		}
		/**
		* Get sidebar id for woocommerce pages
		*/
		public function get_sidebar_id($id){
			return fallsky_get_theme_mod('fallsky_woocommerce_sidebar_content');
		}
		/**
		* Change product list column
		*/
		public function loop_columns($col){ 
			$custom = fallsky_get_theme_mod('fallsky_woocommerce_archive_layout');
			$custom = intval($custom);
			return ($custom > 0) ? $custom : $col;
		}
		/**
		* Change related product query args
		*/
		public function related_products_args($args){
			$args['posts_per_page'] = 3; // 3 related products
			$args['columns'] = 3; // arranged in 3 columns
			return $args;
		}
		/**
		* Change upsell product query args
		*/
		public function upsell_products_args($args){
			$args['posts_per_page'] = 3; // 3 related products
			$args['columns'] = 3; // arranged in 3 columns
			return $args;
		}
		/**
		* Add url field for single product comment form
		*/
		public function add_comment_form_fields($comment_form){
			$fields = isset($comment_form['fields']) ? $comment_form['fields'] : array();
			if(empty($fields['url'])){
				$commenter = wp_get_current_commenter();
				$fields['url'] = sprintf(
					'<p class="comment-form-url"><label for="url">%s</label>%s</p>',
					esc_html__('Website', 'fallsky'),
					sprintf(
						'<input id="url" name="url" type="url" value="%s" size="30" maxlength="200" />',
						esc_attr($commenter['comment_author_url'])
					)
				);
			}
			$comment_form['fields'] = $fields;

			return $comment_form;
		}
		/**
		* Output the category index page header
		*/
		public function page_header(){
			if(empty($this->name)){
				return;
			}

			printf(
				'<header class="page-header">%s%s</header>',
				$this->header_bg,
				sprintf(
					'<div class="page-header-text"><h1 class="page-title">%s</h1>%s</div>',
					esc_html($this->name),
					empty($this->description) ? '' : sprintf('<div class="category-description">%s</div>', $this->description)
				)
			);	
		}
		public function loop_out_of_stock(){
			global $product;
			if(!$product->managing_stock() && !$product->is_in_stock()){
				printf(
					'<span class="stock out-of-stock">%s</span>',
					esc_html__('Out of Stock', 'fallsky')
				);
			}
		}
		/**
		* Add open wrap div before title
		*/
		public function overlay_open_wrap(){
			echo '<div class="product-info">';
		}
		/**
		* Add close wrap div after rating
		*/
		public function overlay_close_wrap(){
			echo '</div>';
		}
		/**
		* Add open wrap div open before product link 
		*/
		public function normal_open_wrap(){
			echo '<div class="product-image">';
		}
		/**
		* Add close wrap div after add to cart link
		*/
		public function normal_close_wrap(){
			echo '</div>';
		}
		/**
		* Add open wrap divs before main content
		*/
		public function before_main_content(){ ?>
			<div class="main">
				<div class="container">
					<div id="primary" class="content-area"> <?php
		}
		/*
		* Add close wrap div after main content
		*/
		public function after_main_content(){ ?>
			</div> <?php
		}
		/**
		* Add close wrap divs after sidebar
		*/
		public function after_sidebar(){ ?>
			</div> </div> <?php
		}
		/**
		* Add Social sharing buttons after product description
		*/
		public function social_sharing(){
			do_action('loftocean_post_meta_sharing', array(
				'facebook',
				'twitter',
				'pinterest',
				'google_plus'
			), 'social-share-icons');
		}
		/**
		* Change global settings
		*/
		private function set_globals(){
			$items = array('title', 'price', 'rating', 'sale');
			foreach($items as $i){
				if(fallsky_module_enabled('fallsky_woocommerce_archive_show_' . $i)){
					array_push($this->show_items, $i);
				}
			}
			// Return if in search result page
			if(is_search()){
				return;
			}

			// Generate the page header information
			$header_image_id = false;
			if(is_singular('product')){
				$page_layout = $this->page_layout('');
				$GLOBALS['content_width'] = empty($page_layout) ? 1000 : 790;
			}

			if( $this->is_shop() || is_product_taxonomy()){ 
				$this->is_archive = true;
				if( $this->is_shop() ){
					$shop_page			= get_post(wc_get_page_id('shop'));
					$header_image_id 	= get_post_thumbnail_id($shop_page->ID);
					$this->name 		= $shop_page->post_title;
					$this->description 	= strip_tags($shop_page->post_content);
				}
				else{
					$queried 			= get_queried_object();
					$header_image_id 	= get_woocommerce_term_meta($queried->term_id, 'thumbnail_id', true);
					$this->name 		= $queried->name;
					$this->description 	= strip_tags($queried->description);
				}
			}
			else if($this->is_static_pages){
				global $post;
				$header_image_id 	= get_post_thumbnail_id($post->ID);
				$this->name 		= $post->post_title;
			}

			$this->header_bg = empty( $header_image_id ) ? '' : sprintf(
				'<div class="featured-media-section">%s</div>',
				fallsky_get_preload_bg( array( 'id' => $header_image_id, 'class' => 'header-img' ) )
			);

			$this->is_transparent_header = apply_filters( 'fallsky_transparent_archive_site_header', false );
		}
		/**
		* Get woocommerce static page ids
		* @param mix pages
		* @return mix return boolean false if no page passed, 
		*	if page exists and only one request one page, return the id, 
		*		otherwise return array of ids.
		*/
		private function get_woocommerce_pages($pages){
			$ids = false;
			if(!empty($pages)){
				if(is_array($pages)){
					$ids = array();
					foreach($pages as $p){
						$id = wc_get_page_id($p);
						(empty($id) || ($id === -1)) ? '' : array_push($ids, $id);
					}
				}
				else{
					$ids = wc_get_page_id($pages);
				}
			}
			return $ids;
		}
		/**
		* Test if is in shop page
		*/
		private function is_shop(){
			$page_id = wc_get_page_id( 'shop' );
			return !empty( $page_id ) && ( $page_id !== -1 ) && is_shop();
		}
	}
	new Fallsky_Woocommerce_Render();
}