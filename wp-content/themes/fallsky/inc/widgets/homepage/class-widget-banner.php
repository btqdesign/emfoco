<?php
/**
* Theme Custom Widget for Homepage only 
*/

if(!class_exists('Fallsky_Homepage_Widget_Banner')){
	class Fallsky_Homepage_Widget_Banner extends Fallsky_Widget{
		private $is_no_sidebar 				= false;
		private $banner_custom_height 		= false;
		private $is_fullwidth_with_effect 	= false;
		private $banner_custom_image		= false;
		private $render_banner 				= false;
		private static $filter_added 		= false;
		public function __construct(){
			$widget_ops = array(
				'classname' 					=> 'ad-banner',
				'description' 					=> esc_html__('Add Ad banner to your homepage.', 'fallsky'),
				'customize_selective_refresh' 	=> false
			);
			parent::__construct('fallsky-homepage-widget-banner', esc_html__('Ad Banner', 'fallsky'), $widget_ops);
		}
		public function widget($args, $instance){
			$this->instance 		= $instance;
			$page_layout 			= apply_filters('fallsky_page_layout', '');
			$this->is_no_sidebar 	= empty($page_layout);

			$title 			= $this->get_title($args);
			$before_widget	= $this->get_before_widget($args);
			$content 		= $this->get_content();

			printf(
				'%s<div class="container">%s<div class="section-content"%s%s>%s</div></div>%s',
				$before_widget,
				$title,
				$this->is_fullwidth_with_effect && !empty($this->banner_custom_image) ? sprintf(
					' data-bg-image="url(%s)"', 
					esc_url($this->banner_custom_image)
				) : '',
				empty($this->banner_custom_height) ? '' : sprintf(' data-custom-height="%s"', $this->banner_custom_height),
				$content,
				$args['after_widget']
			);
		}
		/**
		 * Generate the main content
		 * @return html string
		 */
		public function get_content() {
			$source	= $this->get_value( 'source' );		

			if( 'custom' == $source ) {
				$url 		= esc_url( $this->get_value( 'custom-url' ) );
				$image 		= intval( $this->get_value( 'custom-image' ) );
				$width 		= intval( $this->get_value( 'custom-width' ) );
				$new_tab 	= ( 'on' == $this->get_value( 'custom-target' ) );

				if( !empty( $image ) && ( false !== get_post_status( $image ) ) && !empty( $width ) ) {
					$this->add_frontend_filters();
					$this->render_banner = true;
					$this->banner_custom_image = fallsky_get_image_src( $image, 'full', false );
					$banner_image = wp_get_attachment_image($image, 'full', false, array('class' => '', 'alt' => fallsky_get_image_alt($image)));
					$this->render_banner = false;
					$wrap = empty( $url ) ? '%s' : sprintf( '<a href="%s"%s>%s</a>', $url, ( $new_tab ? ' target="_blank"' : '' ), '%s' );
					return sprintf( $wrap, $banner_image );
				}
			}
			else{
				$content = $this->get_value( 'embed-code' );
				return $content;
			}
			
			return '';
		}
		/**
		* Add filter for frontend rendering
		*/
		private function add_frontend_filters(){
			if(!self::$filter_added){
				add_filter('wp_get_attachment_image_src', array($this, 'customize_image_size'), 999, 4);
				self::$filter_added = true;
			}
		}
		/**
		* Calculate banner image size
		* @return array (src, width, height)
		*/
		public function customize_image_size($image, $id, $size, $icon){
			if($this->render_banner){ 
				$width = absint($this->get_value('custom-width'));
				if((intval($width) > 0) && !empty($image[1])){
					$image[2] = $width / $image[1] * $image[2];
					$image[1] = $width;
				}
			}
			return $image;
		}
		/**
		* Helper function to generate any extra section classes
		*  This function can be overwritten by child class if needed
		* @param array classes
		* @return array classes
		*/
		protected function get_section_class($class){
			global $fallsky_is_preview;
			if(('custom' == $this->get_value('source')) && ($this->is_no_sidebar || $fallsky_is_preview)){
				$image = intval($this->get_value('custom-image'));
				if(!empty($image) && (false !== get_post_status($image))){
					if($this->is_checked('fullwidth')){
						array_push($class, 'fullwidth');
						if($this->is_checked('large-banner-effect')){
							array_push($class, 'large-banner-special');
							$this->is_fullwidth_with_effect	= true;
							$this->banner_custom_height		= intval($this->get_value('banner-custom-height'));
						}
					}
				}
			}
			return $class;
		}
		/**
		* Test option is check
		* @param option name
		* @return boolean true if check, otherwise false
		*/
		private function is_checked($option){
			return ('on' == $this->get_value($option));
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
				'id' 			=> 'source',
				'type'			=> 'select',
				'default'		=> 'custom',
				'title'			=> esc_html__('Ad Source', 'fallsky'),
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_choice',
				'choices'		=> array(
					'custom'	=> array('label' => esc_html__('Custom Banner', 'fallsky')),
					'embed'		=> array('label' => esc_html__('Embed Code', 'fallsky'))
				)
			));
			$this->add_control(array(
				'id' 			=> 'custom-url',
				'type'			=> 'text',
				'default'		=> '#',
				'dependency'	=> array('source' => array('value' => array('custom'))),
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_url',
				'title'			=> esc_html__('URL', 'fallsky')
			));
			$this->add_control(array(
				'id' 			=> 'custom-image',
				'type'			=> 'image',
				'default'		=> '',
				'dependency'	=> array('source' => array('value' => array('custom'))),
				'title'			=> esc_html__('Ad Image', 'fallsky'),
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_number'
			));
			$this->add_control(array(
				'id' 			=> 'custom-width',
				'type'			=> 'number',
				'default'		=> '1200',
				'widefat' 		=> false,
				'input_attr'	=> array('style' => 'width: 80px; margin-right: 5px;'),
				'title'			=> esc_html__('Image Max Width', 'fallsky'),
				'dependency'	=> array(
					'source' 		=> array('value' => array('custom')),
					'custom-image' 	=> array('value' => array('', '0', 0), 'operator' => 'not in')
				),
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_number',
				'text_after'	=> 'px'
			));
			$this->add_control(array(
				'id' 			=> 'custom-target',
				'type'			=> 'checkbox',
				'default'		=> 'on',
				'dependency'	=> array('source' => array('value' => array('custom'))),
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_checkbox',
				'title'			=> esc_html__('Open link in a new tab', 'fallsky')
			));
			$this->add_control(array(
				'id' 			=> 'fullwidth',
				'type'			=> 'checkbox',
				'default'		=> '',
				'dependency'	=> array(
					'source' 		=> array('value' => array('custom')),
					'custom-image' 	=> array('value' => array('', '0', 0), 'operator' => 'not in')
				),
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_checkbox',
				'title'			=> esc_html__('Make the container to be fullwidth when homepage has no sidebar', 'fallsky')
			));
			$this->add_control(array(
				'id' 			=> 'large-banner-effect',
				'type'			=> 'checkbox',
				'default'		=> '',
				'dependency'	=> array(
					'source' 		=> array('value' => array('custom')),
					'custom-image' 	=> array('value' => array('', '0', 0), 'operator' => 'not in'),
					'fullwidth'		=> array('value' => array('on'))
				),
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_checkbox',
				'title'			=> esc_html__('Enable large banner special effect', 'fallsky'),
				'description' 	=> esc_html__('Only works when screen width is larger than 1024px', 'fallsky')
			));
			$this->add_control(array(
				'id' 			=> 'banner-custom-height',
				'type'			=> 'number',
				'default'		=> '400',
				'widefat' 		=> false,
				'input_attr'	=> array('style' => 'width: 80px; margin-right: 5px;'),
				'title'			=> esc_html__('Banner Visible Height', 'fallsky'),
				'dependency'	=> array(
					'source'		 		=> array('value' => array('custom')),
					'custom-image' 			=> array('value' => array('', '0', 0), 'operator' => 'not in'),
					'fullwidth'				=> array('value' => array('on')),
					'large-banner-effect'	=> array('value' => array('on'))
				),
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_number',
				'text_after'	=> 'px'
			));
			$this->add_control(array(
				'id' 			=> 'embed-code',
				'type'			=> 'textarea',
				'default'		=> '',
				'dependency'	=> array('source' => array('value' => array('embed'))),
				'sanitize_cb' 	=> 'fallsky_widget_sanitize_html',
				'title'			=> esc_html__('Embed Code', 'fallsky')
			));
		}
	}
}
