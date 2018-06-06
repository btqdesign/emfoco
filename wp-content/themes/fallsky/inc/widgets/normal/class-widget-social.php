<?php
/**
* Theme Custom Widget Socail
*/

if(!class_exists('Fallsky_Widget_Social')){
	class Fallsky_Widget_Social extends Fallsky_Widget{
		public function __construct(){
			$widget_ops = array(
				'classname' 					=> 'fallsky-widget_social',
				'description' 					=> esc_html__('Display your social menu.', 'fallsky'),
				'customize_selective_refresh' 	=> true,
			);
			parent::__construct('fallsky-widget_social', esc_html__('Fallsky Social', 'fallsky'), $widget_ops);
		}
		/**
		* Print the widget content
		*/
		function widget($args, $instance){ 
			$this->instance = $instance;

			$title = $this->get_value('title');
			/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
			$title = apply_filters('widget_title', $title, $instance, $this->id_base);


			if(has_nav_menu('social')){
				echo str_replace($this->custom_class, '', $args['before_widget']);
				echo empty($title) ? '' : $args['before_title'] . $title . $args['after_title']; 

				wp_nav_menu(array(
					'theme_location' 	=> 'social',
					'depth' 			=> 1,
					'container' 		=> 'div',
					'container_class' 	=> 'socialwidget',
					'menu_class' 		=> 'social-nav menu',
					'menu_id' 			=> empty($args['widget_id']) ? 'footer-socail-icons' : sprintf('social-menu-%s', $args['widget_id'])
				));
				print($args['after_widget']);
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
				'title'			=> esc_html__('Title:', 'fallsky'),
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_text'
			));
		}
	}

	add_action('widgets_init', function(){ register_widget('Fallsky_Widget_Social'); });
}

