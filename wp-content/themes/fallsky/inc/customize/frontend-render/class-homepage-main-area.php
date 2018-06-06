<?php
/**
* Customize section homepage related frontend render class.
*/

if(!class_exists('Fallsky_Customize_Homepage_Main_Area_Frontend_Render')){
	class Fallsky_Customize_Homepage_Main_Area_Frontend_Render{
		private $sidebars = array();
		private $sidebar_args = array();
		public function __construct(){
			global $pagenow; 
			$this->sidebar_args = array(
				'class' 		=> '',
				'before_widget' => '<div id="%1$s" class="home-widget %2$s">',
				'after_widget'	=> '</div>',
				'before_title'	=> '<div class="section-header%1$s"><h5 class="section-title">',
				'after_title'	=> '</h5></div>'
			);

			if(empty($pagenow) || ('widgets.php' != $pagenow)){ // Not show this sidebar page in /wp-admin/widgets.php
				add_action('widgets_init', 	array($this, 'register_widgets'));
			}
			add_action('pre_get_posts', array($this, 'set_frontpage'), 1);
		}
		public function set_frontpage($query){
			if($query->is_main_query()){
				if(fallsky_is_customize_front_page($query)){
					define('FALLSKY_IS_FRONT_PAGE', true);
					$ppp = $this->get_ppp_from_latest_post_widget();
					if($ppp){
						$query->set('posts_per_page', $ppp);
					}
				}
				if(fallsky_is_static_front_page_from_customize($query)){
					$page = get_post($query->get('page_id'));
					$query->set('post_type', 'post');
					$query->set('page_id', '');

					if(isset($query->query['paged'])){
						$query->set('paged', $query->query['paged']);
					}
					// Get the actual WP page to avoid errors and let us use is_front_page()
					// This is hacky but works. Awaiting https://core.trac.wordpress.org/ticket/21096
					global $wp_post_types;
					$wp_post_types['post']->ID 			= $page->ID;
					$wp_post_types['post']->post_title	= $page->post_title;
					$wp_post_types['post']->post_name	= $page->post_name;
					$wp_post_types['post']->post_type 	= $page->post_type;
					$wp_post_types['post']->ancestors 	= get_ancestors($page->ID, $page->post_type);

					$query->is_singular 			= false;
					$query->is_post_type_archive 	= true;
					$query->is_archive				= true;
					$query->is_page 				= true;

					// Fix menu item for static homepage
					add_filter('wp_nav_menu_objects', array($this, 'homepage_menu_item_classes'), 99);
				}
				remove_action('pre_get_posts', array($this, 'set_frontpage'), 999);
			}
		}
		private function get_ppp_from_latest_post_widget(){
			$widgets 	= fallsky_get_theme_mod('fallsky_homepage_main_area');
			$ppp 		= false;
			if(!empty($widgets) && is_array($widgets)){
				$widgets_post 	= get_option('widget_fallsky-homepage-widget-posts');
				$widget_base_id = 'fallsky-homepage-widget-posts';
				$posts_per_page = get_option('posts_per_page', 10);
				foreach($widgets as $widget_id){
					if(0 === strpos($widget_id, $widget_base_id)){
						$wid 		= str_replace(sprintf('%s-', $widget_base_id), '', $widget_id);
						$settings 	= isset($widgets_post[$wid]) ? $widgets_post[$wid] : false;
						if(!empty($settings) && ('latest' == $settings['filter-by']) && isset($settings['number'])){
							$number = $settings['number'];
							if(($number > 0) && ($number < $posts_per_page)){
								$posts_per_page = $number;
								$ppp 			= $number;
							}
						}
					}
				}
			}
			return $ppp;
		}
		function homepage_menu_item_classes($menu_items){
			$homepage_id = get_option('page_on_front', false);
			if(!empty($homepage_id) && !empty($menu_items) && is_array($menu_items)){
				foreach($menu_items as $key => $menu_item){
					$classes = (array) $menu_item->classes;
					$menu_id = (int) $menu_item->object_id;
					if($homepage_id == $menu_id){
						$menu_items[$key]->current = true;
						$classes[] = 'current-menu-item';
						$classes[] = 'current_page_item';
					}
					$menu_items[$key]->classes = array_unique( $classes );
				}
			}
			return $menu_items;
		}
		public function register_widgets(){
			$this->includes();

			register_widget('Fallsky_Homepage_Widget_Posts');
			register_widget('Fallsky_Homepage_Widget_Banner');
			register_widget('Fallsky_Homepage_Widget_Featured_Category');
			register_widget('Fallsky_Homepage_Widget_Call_Action');
			register_widget('Fallsky_Homepage_Widget_Custom_Content');
			if(class_exists('Fallsky_Homepage_Widget_MC4WP_Signup')){
				register_widget('Fallsky_Homepage_Widget_MC4WP_Signup');
			}
			if(class_exists('WooCommerce')){
				register_widget('Fallsky_Homepage_Widget_Products');
				register_widget('Fallsky_Homepage_Widget_Product_Category');
			}
		}
		private function includes(){
			$dir = FALLSKY_THEME_INC . 'widgets/homepage/';

			require_once $dir . 'class-widget-posts.php';
			require_once $dir . 'class-widget-mc4wp-signup.php';
			require_once $dir . 'class-widget-banner.php';
			require_once $dir . 'class-widget-featured-category.php';
			require_once $dir . 'class-widget-call-to-action.php';
			require_once $dir . 'class-widget-products.php';
			require_once $dir . 'class-widget-product-category.php';
			require_once $dir . 'class-widget-custom-content.php';
		}
		public function show_content(){
			if($this->is_active_homepage_area('fallsky_homepage_main_area')){
				$this->dynamic_hoomepage_area('fallsky_homepage_main_area');
			}
		}
		public function customizer_selective_refresh_show_content(){
			echo '<div id="primary" class="content-area">'; 
			do_action('fallsky_main_content');
			echo '</div>';
		}
		public function frontend_js_vars($vars = array()){
			global $fallsky_is_preview;

			return $vars;
		}
		public function page_layout($layout){
			return fallsky_get_theme_mod('fallsky_home_sidebar');
		}
		private function is_active_homepage_area($key){
			if(!empty($key)){
				$homepage_area = fallsky_get_theme_mod($key);
				return !empty($homepage_area);
			}
			return false;
		}
		private function dynamic_hoomepage_area($key){
			global $wp_registered_widgets; 
			$homepage_area = fallsky_get_theme_mod($key);
			if(empty($homepage_area)){
				return false;
			}

			foreach($homepage_area as $id){
				if(!isset($wp_registered_widgets[$id])){
					continue;
				}

				$params = array_merge(
					array(array_merge($this->sidebar_args, array('widget_id' => $id, 'widget_name' => $wp_registered_widgets[$id]['name']))),
					(array)$wp_registered_widgets[$id]['params']
				);

				// Substitute HTML id and class attributes into before_widget
				$classname_ = '';
				foreach((array) $wp_registered_widgets[$id]['classname'] as $cn){
					if(is_string($cn)){
						$classname_ .= '_' . $cn;
					}
					else if(is_object($cn)){
						$classname_ .= '_' . get_class($cn);
					}
				}
				$classname_ = ltrim($classname_, '_');
				$params[0]['before_widget'] = sprintf($params[0]['before_widget'], $id, $classname_);
				$callback = $wp_registered_widgets[$id]['callback'];
				if(is_callable($callback)){
					call_user_func_array($callback, $params);
					$did_one = true;
				}
			}
		}
	}
}