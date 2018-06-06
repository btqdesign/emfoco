<?php
/**
* Theme Custom Widget for Homepage only 
*/

if(!class_exists('Fallsky_Homepage_Widget_MC4WP_Signup') && function_exists('mc4wp_show_form')){
	class Fallsky_Homepage_Widget_MC4WP_Signup extends Fallsky_Widget {
		public function __construct(){
			$widget_ops = array(
				'classname' 					=> 'signup-form',
				'description' 					=> esc_html__('Add MailChimp for WordPress Singup form to your homepage.', 'fallsky'),
				'customize_selective_refresh' 	=> false
			);
			parent::__construct('fallsky-homepage-widget-mc4wp-singup', esc_html__('MailChimp Signup Form', 'fallsky'), $widget_ops);
		}
		/**
		 * Generate the main content
		 * @return html string
		 */
		public function get_content(){
			$ids 		= fallsky_mc4w_forms();
			$form_id 	= $this->get_value('form-id');
			if(!empty($form_id) && !empty($ids)){
				$ids = array_keys($ids);
				if(in_array($form_id, $ids)){
					$form 		= get_post($form_id);
					$shortcode 	= sprintf('[mc4wp_form id="%d"]', $form_id);
					$form_title = apply_filters('the_title', $form->post_title);
					return sprintf(
						'<div class="widget widget_mc4wp_form_widget">%s%s</div>',
						empty($form_title) ? '' : sprintf('<h5 class="widget-title">%s</h5>', $form_title),
						do_shortcode($shortcode)
					);
				};
			}
			return '';
		}
		/**
		* Helper function to get html before widget
		* @param array sidebar settings
		* @return html string
		*/
		protected function get_before_widget($args){
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
					}
					do_action('fallsky_reset_frontend_options');
				}
			}
			$class = $this->get_section_class($class);

			return $this->replace_before_widget($class, $styles, $args['before_widget']);
		}
		/**
		* Overwrite parent class to get more class name for section wrapper
		* @param array class
		* @return array class
		*/
		protected function get_section_class($class){
			$align = ('on' == $this->get_value('align'));
			$align ? array_push($class, 'align-center') : '';
			return $class;
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
				'id' 			=> 'title-align',
				'type'			=> 'checkbox',
				'default'		=> '',
				'sanitize_cb'	=> 'fallsky_widget_sanitize_checkbox',
				'title'			=> esc_html__('Center Section Title', 'fallsky')
			));
			$this->add_control(array(
				'id' 			=> 'color',
				'type'			=> 'select',
				'default'		=> 'default',
				'title'			=> esc_html__('Color', 'fallsky'),
				'sanitize_cb'	=> 'fallsky_widget_sanitize_choice',
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
				'sanitize_cb'	=> 'fallsky_widget_sanitize_choice',
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
				'sanitize_cb'	=> 'fallsky_widget_sanitize_color',
				'title'			=> esc_html__('Custom background color', 'fallsky')
			));
			$this->add_control(array(
				'id' 			=> 'custom-bg-image',
				'type'			=> 'image',
				'default'		=> '',
				'dependency'	=> array('color' => array('value' => array('custom'))),
				'sanitize_cb'	=> 'fallsky_widget_sanitize_number',
				'title'			=> esc_html__('Background Image', 'fallsky')
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
				'id' 			=> 'form-id',
				'type'			=> 'select',
				'default'		=> fallsky_get_default_mc4wp_form_id(),
				'title'			=> esc_html__('Choose a form', 'fallsky'),
				'sanitize_cb'	=> 'fallsky_widget_sanitize_number',
				'choices'		=> fallsky_mc4w_forms()
			));
			$this->add_control(array(
				'id' 			=> 'align',
				'type'			=> 'checkbox',
				'default'		=> 'on',
				'sanitize_cb'	=> 'fallsky_widget_sanitize_checkbox',
				'title'			=> esc_html__('Center the form', 'fallsky')
			));
		}
	}
}
