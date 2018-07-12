<?php
/**
* Theme Custom Widget for Homepage only 
*/

if(!class_exists('Fallsky_Homepage_Widget_Call_Action')){
	class Fallsky_Homepage_Widget_Call_Action extends Fallsky_Widget{
		public function __construct(){
			$widget_ops = array(
				'classname' 					=> 'call-to-action',
				'description' 					=> esc_html__('Add Call To Action to your homepage.', 'fallsky'),
				'customize_selective_refresh' 	=> false
			);
			parent::__construct('fallsky-homepage-widget-call-action', esc_html__('Call To Action', 'fallsky'), $widget_ops);
		}
		/**
		 * Generate the main content
		 * @return html string
		 */
		public function get_content(){
			$layout 	 	= $this->get_value('layout');
			$heading 		= $this->get_value('heading');
			$description 	= $this->get_value('description');
			$button_text 	= $this->get_value('button-text');
			$button_url		= $this->get_value('button-url');
			$target 		= ('on' == $this->get_value('target'));

			$media_html 	= '';
			$media			= $this->get_value('media');
			$media_id		= $media['id'];
			$media_type 	= $media['type'];
			$is_image 		= empty($media_type) || ('image' == $media_type);
			if((1 != $layout) && !empty($media_id)){
				do_action('fallsky_set_frontend_options', 'homepage_widgets');
				$media_html = sprintf(
					'<figure class="cta-img">%s</figure>',
					$is_image ? fallsky_get_responsive_image( array( 'id' => $media_id ) ) 
						: sprintf( '<video controls src="%s"></video>', wp_get_attachment_url( $media_id ) )
				);
				do_action('fallsky_reset_frontend_options');
			}

			return sprintf(
				'%s<div class="cta-text">%s%s%s</div>',
				$media_html,
				empty($heading) 	? '' : sprintf( '<h2>%s</h2>', apply_filters( 'fallsky_multilingual_text', $heading ) ),
				empty($description) ? '' : apply_filters('widget_text_content', $description),
				empty($button_text) || empty($button_url) ? '' : sprintf(
					'<a href="%s"%s class="button">%s</a>', 
					$button_url, 
					$target ? ' target="_blank"' : '',
					apply_filters( 'fallsky_multilingual_text', $button_text )
				)
			);
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
					$image_src 	= fallsky_get_image_src( $bg_image, 'fallsky_large', 'false' );
					if( !empty($image_src ) ) {
						$styles	.= sprintf( 'background-image: url(%s);', $image_src );
						$styles .= sprintf( ' background-position: %s;', $this->get_value( 'custom-bg-image-position' ) );
						$styles .= sprintf( ' background-size: %s;', $this->get_value( 'custom-bg-image-size' ) );
						$styles .= sprintf( ' background-repeat: %s;', ( 'on' == $this->get_value( 'custom-bg-image-repeat' ) ? 'repeat' : 'no-repeat' ) );
						$styles .= sprintf( ' background-attachment: %s;', ( 'on' == $this->get_value( 'custom-bg-image-scroll') ? 'scroll' : 'fixed' ) );
					}
					do_action( 'fallsky_reset_frontend_options' );
				}
			}
			$class = $this->get_section_class($class);

			return $this->replace_before_widget($class, $styles, $args['before_widget']);
		}
		/**
		* Helper function to generate any extra section classes
		* @param array classes
		* @return array classes
		*/
		protected function get_section_class($class){
			$layout = $this->get_value('layout');
			$align 	= $this->get_value('text-align');
			in_array($layout, array('3', '5')) 		? array_push($class, 'reverse') : '';
			in_array($layout, array('1', '2', '3')) ? array_push($class, 'column-1') : array_push($class, 'column-2');
			array_push($class, $align);
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
				'id' 			=> 'layout',
				'type'			=> 'radio-with-thumbnail',
				'default'		=> '1',
				'title'			=> esc_html__('Layout', 'fallsky'),
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_choice',
				'choices'		=> array(
					'1'	=> esc_html__('1 column, no media', 'fallsky'),
					'2'	=> esc_html__('1 column, media top, text bottom', 'fallsky'),
					'3'	=> esc_html__('1 column, media bottom, text top', 'fallsky'),
					'4'	=> esc_html__('2 column, media left, text right', 'fallsky'),
					'5'	=> esc_html__('2 column, text left, media right', 'fallsky')
				)
			));
			$this->add_control(array(
				'id' 			=> 'heading',
				'type'			=> 'text',
				'default'		=> '',
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_text',
				'title'			=> esc_html__('Heading', 'fallsky')
			));
			$this->add_control(array(
				'id' 			=> 'description',
				'type'			=> 'textarea',
				'default'		=> '',
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_text',
				'title'			=> esc_html__('Description', 'fallsky')
			));
			$this->add_control(array(
				'id' 			=> 'button-text',
				'type'			=> 'text',
				'default'		=> '',
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_html',
				'title'			=> esc_html__('Button Text', 'fallsky')
			));
			$this->add_control(array(
				'id' 			=> 'button-url',
				'type'			=> 'url',
				'default'		=> '#',
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_url',
				'title'			=> esc_html__('Button Link', 'fallsky')
			));
			$this->add_control(array(
				'id' 			=> 'target',
				'type'			=> 'checkbox',
				'default'		=> 'on',
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_checkbox',
				'title'			=> esc_html__('Open link in a new tab', 'fallsky')
			));
			$this->add_control(array(
				'id' 			=> 'text-align',
				'type'			=> 'select',
				'default'		=> 'align-left',
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_choice',
				'title'			=> esc_html__('Text Alignment', 'fallsky'),
				'choices'		=> array(
					'align-left'	=> esc_html__('Left', 'fallsky'),
					'align-center'	=> esc_html__('Center', 'fallsky'),
					'align-right'	=> esc_html__('Right', 'fallsky')
				)
			));
			$this->add_control(array(
				'id' 			=> 'media',
				'type'			=> 'image',
				'default'		=> array('id' => '', 'type' => 'media'),
				'media_types'	=> 'image-video',
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_media',
				'title'			=> esc_html__('Media', 'fallsky'),
				'dependency'	=> array(
					'layout' => array('value' => array('1'), 'operator' => 'not in')
				)
			));
		}
	}
}
