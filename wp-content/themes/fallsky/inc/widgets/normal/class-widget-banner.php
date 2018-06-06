<?php
/**
* Theme Custom Widget Banner
*/

if(!class_exists('Fallsky_Widget_Banner')){
	class Fallsky_Widget_Banner extends Fallsky_Widget{
		public function __construct(){
			$widget_ops = array(
				'classname' 					=> 'fallsky-widget_ad',
				'description' 					=> esc_html__('Add an ad banner.', 'fallsky'),
				'customize_selective_refresh' 	=> true,
			);
			parent::__construct('fallsky-widget_ad', esc_html__('Fallsky Ad', 'fallsky'), $widget_ops);
			$this->force_add_widget = true;
		}
		/**
		* Print the widget content
		*/
		function widget($args, $instance){ 
			$this->instance = $instance;

			$title = $this->get_value('title');
			/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
			$title = apply_filters('widget_title', $title, $instance, $this->id_base);

			$content = $this->get_content();
			if(!empty($content)){
				printf(
					'%s%s%s%s',
					str_replace($this->custom_class, '', $args['before_widget']),
					empty($title) ? '' : $args['before_title'] . $title . $args['after_title'],
					$content,
					$args['after_widget']
				);
			}
		}		
		/**
		 * Generate the main content
		 * @return html string
		 */
		public function get_content(){
			$new_tab 	= $this->get_value( 'target' );
			$url 		= $this->get_value( 'url' );
			do_action( 'fallsky_set_frontend_options', 'widget-banner' );
			$image_id 	= intval( $this->get_value( 'image' ) );
			$image 		= empty( $image_id ) ? false : fallsky_get_responsive_image( array( 'id' => $image_id ) );
			if( !empty( $image ) ) {
				$wrap = empty( $url ) ? '%s' : sprintf( '<a href="%s"%s>%s</a>', esc_url( $url ), ( empty( $new_tab ) ? '' : ' target="_blank"' ), '%s' );
				return sprintf( $wrap, $image );
			}
			do_action( 'fallsky_reset_frontend_options' );
			return false;
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
				'id' 			=> 'url',
				'type'			=> 'text',
				'default'		=> '',
				'title'			=> esc_html__('URL:', 'fallsky'),
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_url'
			));
			$this->add_control(array(
				'id' 			=> 'image',
				'type'			=> 'image',
				'default'		=> '',
				'title'			=> esc_html__('Ad Image:', 'fallsky'),
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_number'
			));		
			$this->add_control(array(
				'id' 			=> 'target',
				'type'			=> 'checkbox',
				'default'		=> '',
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_checkbox',
				'title'			=> esc_html__('Open link in a new tab', 'fallsky')
			));
		}
	}

	add_action('widgets_init', function(){ register_widget('Fallsky_Widget_Banner'); });
}

