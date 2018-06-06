<?php
/**
* Theme Custom Widget for Homepage only 
*/

if(!class_exists('Fallsky_Homepage_Widget_Custom_Content')){
	class Fallsky_Homepage_Widget_Custom_Content extends Fallsky_Widget {
		public function __construct(){
			$widget_ops = array(
				'classname' 					=> 'custom-content',
				'description' 					=> esc_html__('Add Custom Content to your homepage.', 'fallsky'),
				'customize_selective_refresh' 	=> false
			);
			parent::__construct('fallsky-homepage-widget-custom-content', esc_html__('Custom Content', 'fallsky'), $widget_ops);
		}
		/**
		 * Generate main content
		 * @return html string
		 */
		public function get_content(){
			$content = $this->get_value('content'); 
			return apply_filters('widget_text_content', wp_make_content_images_responsive($content));
		}
		/**
		* Helper function to get html before widget
		* @param array sidebar settings
		* @return html string
		*/
		protected function get_before_widget($args){
			$overlay_html = '';
			$class 	= array();
			$styles = '';
			$color_type = $this->get_value('color');
			if('default' == $color_type){
				array_push($class, 'default-color');
			}
			else{
				$cids 	= array_keys($this->controls);
				array_push($class, $this->get_value('custom-color-scheme'));
				if(in_array('custom-bg-color', $cids)){
					$bg_color = $this->get_value('custom-bg-color');
					$styles  .= empty($bg_color) ? '' : sprintf('background-color: %s;', $bg_color);
				}
				if(in_array('custom-bg-image', $cids)){
					do_action('fallsky_set_frontend_options', 'homepage_widgets');
					$bg_image 	= $this->get_value('custom-bg-image');
					$image_src 	= fallsky_get_image_src( $bg_image, 'fallsky_large', false );
					if(!empty($image_src)){
						$styles	.= sprintf('background-image: url(%s);', $image_src);
						$styles .= sprintf(' background-position: %s;', $this->get_value('custom-bg-image-position'));
						$styles .= sprintf(' background-size: %s;', $this->get_value('custom-bg-image-size'));
						$styles .= sprintf(' background-repeat: %s;', ('on' == $this->get_value('custom-bg-image-repeat') ? 'repeat' : 'no-repeat'));
						$styles .= sprintf(' background-attachment: %s;', ('on' == $this->get_value('custom-bg-image-scroll') ? 'scroll' : 'fixed'));

						if( 'on' == $this->get_value( 'custom-bg-enable_overlay' ) ) {
							$overlay_styles = array();
							$overlay_color = $this->get_value( 'custom-bg-overlay-color' );
							$overlay_opacity = $this->get_value( 'custom-bg-overlay-opacity' );
							empty( $overlay_color ) ? '' : array_push( $overlay_styles, sprintf( 'background: %s;', $overlay_color ) );
							empty( $overlay_opacity ) ? '' : array_push( $overlay_styles, sprintf( 'opacity: %s;', ( $overlay_opacity / 100 ) ) );
							if( !empty( $overlay_styles ) ) {
								$overlay_html = sprintf(
									'<div class="bg-overlay" style="%s"></div>',
									implode( ' ', $overlay_styles )
								);
								}
						}
					}
					do_action('fallsky_reset_frontend_options');
				}
			}
			$class = $this->get_section_class($class);

			return sprintf(
				'%s%s', 
				$this->replace_before_widget($class, $styles, $args['before_widget']),
				$overlay_html
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
				'id' 			=> 'custom-bg-image',
				'type'			=> 'image',
				'default'		=> '',
				'dependency'	=> array('color' => array('value' => array('custom'))),
				'title'			=> esc_html__('Background Image', 'fallsky'),
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_number',
				'choices'		=> array()
			));
			$this->add_control(array(
				'id' 			=> 'custom-bg-image-position',
				'type'			=> 'select',
				'default'		=> 'center center',
				'sanitize_cb'	=> 'fallsky_widget_sanitize_choice',
				'title'			=> esc_html__('Background Image Position', 'fallsky'),
				'dependency'	=> array(
					'color' 			=> array('value' => array('custom')),
					'custom-bg-image' 	=> array('value' => array('', 0, '0'), 'operator' => 'not in')
				),
				'choices'		=> array(
					'left top' 		=> esc_html__('Left Top', 'fallsky'),
					'left center' 	=> esc_html__('Left Center', 'fallsky'),
					'left bottom' 	=> esc_html__('Left Bottom', 'fallsky'),
					'center top' 	=> esc_html__('Center Top', 'fallsky'),
					'center center' => esc_html__('Center Center', 'fallsky'),
					'center bottom' => esc_html__('Center Bottom', 'fallsky'),
					'right top' 	=> esc_html__('Right Top', 'fallsky'),
					'right center' 	=> esc_html__('Right Center', 'fallsky'),
					'right bottom' 	=> esc_html__('Right Bottom', 'fallsky')
				)
			));
			$this->add_control(array(
				'id' 			=> 'custom-bg-image-size',
				'type'			=> 'select',
				'default'		=> 'auto',
				'sanitize_cb'	=> 'fallsky_widget_sanitize_choice',
				'title'			=> esc_html__('Background Image Size', 'fallsky'),
				'dependency'	=> array(
					'color' 			=> array('value' => array('custom')),
					'custom-bg-image' 	=> array('value' => array('', 0, '0'), 'operator' => 'not in')
				),
				'choices'		=> array(
					'auto' 		=> esc_html__('Original', 'fallsky'),
					'contain' 	=> esc_html__('Fit to Screen', 'fallsky'),
					'cover' 	=> esc_html__('Fill Screen', 'fallsky')
				)
			));
			$this->add_control(array(
				'id' 			=> 'custom-bg-image-repeat',
				'type'			=> 'checkbox',
				'default'		=> '',
				'sanitize_cb'	=> 'fallsky_widget_sanitize_checkbox',
				'title'			=> esc_html__('Repeat Background Image', 'fallsky'),
				'dependency'	=> array(
					'color' 			=> array('value' => array('custom')),
					'custom-bg-image' 	=> array('value' => array('', 0, '0'), 'operator' => 'not in')
				)
			));
			$this->add_control(array(
				'id' 			=> 'custom-bg-image-scroll',
				'type'			=> 'checkbox',
				'default'		=> '',
				'sanitize_cb'	=> 'fallsky_widget_sanitize_checkbox',
				'title'			=> esc_html__('Scroll with Page', 'fallsky'),
				'dependency'	=> array(
					'color' 			=> array('value' => array('custom')),
					'custom-bg-image' 	=> array('value' => array('', 0, '0'), 'operator' => 'not in')
				)
			));
			$this->add_control(array(
				'id' 			=> 'custom-bg-enable_overlay',
				'type'			=> 'checkbox',
				'default'		=> '',
				'sanitize_cb'	=> 'fallsky_widget_sanitize_checkbox',
				'title'			=> esc_html__('Add an Overlay', 'fallsky'),
				'dependency'	=> array(
					'color' 			=> array( 'value' => array( 'custom' ) ),
					'custom-bg-image' 	=> array( 'value' => array( '', 0, '0' ), 'operator' => 'not in' )
				)
			));
			$this->add_control(array(
				'id' 			=> 'custom-bg-overlay-color',
				'type'			=> 'color-picker',
				'default'		=> '#000000',
				'sanitize_cb'	=> 'fallsky_widget_sanitize_color',
				'title'			=> esc_html__('Overlay Color', 'fallsky'),
				'dependency'	=> array(
					'color' 					=> array( 'value' => array( 'custom' ) ),
					'custom-bg-image' 			=> array( 'value' => array( '', 0, '0' ), 'operator' => 'not in' ),
					'custom-bg-enable_overlay' 	=> array( 'value' => array( 'on' ) )
				)
			));
			$this->add_control(array(
				'id' 			=> 'custom-bg-overlay-opacity',
				'type'			=> 'slider',
				'default'		=> '40',
				'sanitize_cb'	=> 'fallsky_widget_sanitize_number',
				'title'			=> esc_html__('Overlay Opacity', 'fallsky'),
				'after_text'	=> esc_html__('%', 'fallsky'),
				'dependency'	=> array(
					'color' 					=> array( 'value' => array( 'custom' ) ),
					'custom-bg-image' 			=> array( 'value' => array( '', 0, '0' ), 'operator' => 'not in' ),
					'custom-bg-enable_overlay' 	=> array( 'value' => array( 'on' ) )
				),
				'slider_attr'	=> array(
					'data-min'	=> '0',
					'data-max'	=> '100',
					'data-step'	=> '1'
				)
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
				'id' 			=> 'content',
				'type'			=> 'editor',
				'default'		=> '',
				'title'			=> esc_html__('Add content', 'fallsky'),
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_html'
			));
		}
	}
}
