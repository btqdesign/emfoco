<?php
/**
* Theme Custom Widget Profile
*/

if(!class_exists('Fallsky_Widget_Profile')){
	class Fallsky_Widget_Profile extends Fallsky_Widget{
		public function __construct(){
			$widget_ops = array(
				'classname' 					=> 'fallsky-widget_aboutme',
				'description' 					=> esc_html__('Add brief information about you and your site.', 'fallsky'),
				'customize_selective_refresh' 	=> true,
			);
			parent::__construct('fallsky-widget_aboutme', esc_html__('Fallsky Profile', 'fallsky'), $widget_ops);
		}
		/**
		* Print the widget content
		*/
		function widget($args, $instance){ 
			$this->instance = $instance;

			$title = $this->get_value('title');
			/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
			$title = apply_filters('widget_title', $title, $instance, $this->id_base);

			echo str_replace($this->custom_class, '', $args['before_widget']);
			echo empty($title) ? '' : $args['before_title'] . $title . $args['after_title']; 
			print($this->get_content());
			print($args['after_widget']);
		}		
		/**
		 * Generate the main content
		 * @return html string
		 */
		public function get_content(){
			$photo 		= $signature = '';
			$subtitle 	= esc_html($this->get_value( 'subtitle' ) );
			$photo_id 	= intval($this->get_value( 'photo' ) );
			$photo_src	= fallsky_get_image_src( $photo_id, 'fallsky_small', false );
			if( !empty( $photo_src ) ) {
				$photo = wp_make_content_images_responsive(
					sprintf(
						'<div class="profile"><img class="profile-img wp-image-%d" alt="%s" src="%s"></div>',
						$photo_id,
						fallsky_get_image_alt( $photo_id ),
						esc_url( $photo_src )
					)
				);
			}

			$signature_id 		= intval($this->get_value( 'signature' ) );
			$signature_src		= fallsky_get_image_src( $signature_id, 'fallsky_small', false );
			$signature_width 	= intval( $this->get_value( 'signature-width' ) );
			if( !empty( $signature_src ) ) {
				$signature = wp_make_content_images_responsive(
					sprintf(
						'<div class="signature-img"><img%s class="wp-image-%d" alt="%s" src="%s"></div>',
						empty( $signature_width ) ? '' : ' width=' . $signature_width,
						$signature_id,
						fallsky_get_image_alt( $signature_id ),
						esc_url( $signature_src )
					)
				);
			}

			$link_text		= $this->get_value('link-text');
			$link_url 		= $this->get_value('link-url');
			$description 	= $this->get_value('description');

			return sprintf(
				'%s<div class="textwidget">%s%s%s%s</div>',
				$photo,
				empty($subtitle) ? '' : sprintf('<h6 class="subheading">' . $subtitle . '</h6>'),
				empty($description) ? '' : sprintf('<p>%s</p>', do_shortcode(wp_kses_post($description))),
				$signature,
				empty($link_text) || empty($link_url) ? '' : sprintf('<a href="%s" class="button">%s</a>', esc_url($link_url), esc_html($link_text))
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
				'title'			=> esc_html__('Title:', 'fallsky'),
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_text'
			));
			$this->add_control(array(
				'id' 			=> 'photo',
				'type'			=> 'image',
				'default'		=> '',
				'title'			=> esc_html__('Photo:', 'fallsky'),
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_number'
			));
			$this->add_control(array(
				'id' 			=> 'subtitle',
				'type'			=> 'text',
				'default'		=> '',
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_text',
				'title'			=> esc_html__('Sub Title (optional):', 'fallsky')
			));
			$this->add_control(array(
				'id' 			=> 'description',
				'type'			=> 'textarea',
				'default'		=> '',
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_html',
				'title'			=> esc_html__('Description:', 'fallsky')
			));
			$this->add_control(array(
				'id' 			=> 'signature',
				'type'			=> 'image',
				'default'		=> '',
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_number',
				'title'			=> esc_html__('Signature Image (optional):', 'fallsky')
			));
			$this->add_control(array(
				'id' 			=> 'signature-width',
				'type'			=> 'number',
				'default'		=> 175,
				'input_attr'	=> array('min' => 90, 'max' => 240, 'style' => 'width: 50px;'),
				'widefat'		=> false,
				'text_after' 	=> 'px',
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_number',
				'title'			=> esc_html__('Signature Image Max Width:', 'fallsky'),
				'dependency' 	=> array(
					'signature' => array('value' => array('', 0, '0'), 'operator' => 'not in')
				)
			));
			$this->add_control(array(
				'id' 			=> 'link-text',
				'type'			=> 'text',
				'default'		=> '',
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_text',
				'title'			=> esc_html__('Button Text (optional):', 'fallsky')
			));
			$this->add_control(array(
				'id' 			=> 'link-url',
				'type'			=> 'text',
				'default'		=> '#',
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_url',
				'title'			=> esc_html__('Button URL (optional):', 'fallsky')
			));
		}
	}

	add_action('widgets_init', function(){ register_widget('Fallsky_Widget_Profile'); });
}

