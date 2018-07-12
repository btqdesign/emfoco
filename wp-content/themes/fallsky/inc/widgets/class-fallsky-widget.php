<?php
/**
* Theme Custom Widget base class 
*/

if(!class_exists('Fallsky_Widget')){
	class Fallsky_Widget extends WP_Widget{
		// Widget control elements
		public $controls 		= array();
		// Widget control element dependency
		public $dependency 		= array();
		// Widget control elements json
		public $json			= array();
		// Default values for each element
		public $defaults		= array();
		// Current setting values
		public $instance		= array();
		// Flag to control add the json and dependency js vars only once
		protected $registered 	= false;
		// Custom class name
		protected $custom_class = '[[fallsky-custom-class]]';
		protected $force_add_widget 		= false;
		// Flag to identify editor template has been output
		protected static $done_editor_tmpl 	= false;
		// Flag to identify currently printing editor template for backend
		private static $render_editor_tmpl 	= false;
		public function __construct($id, $title, $options = array()){
			$custom_class 			= $this->custom_class; // Add the holder place for custom class if needed depends on the settings
			$options['classname'] 	= empty($options['classname']) ? $custom_class : sprintf('%s%s', $options['classname'], $custom_class);
			parent::__construct($id, $title, $options);
			$this->register_controls();
		}
		/**
		* Register the widget control elements 
		* 	Must be overwrite in child class
		*/
		public function register_controls(){ }
		/**
		* To add the json and dependency js vars once per widgets
		* @param int widget instance number
		*/
		public function _register_one($number = -1){
			parent::_register_one($number);
			if(!empty($this->registered)){
				return;
			}

			$this->registered = true;

			// Output the javascript variables for theme widget
			add_filter('fallsky_widget_js_vars', array($this, 'widget_js_vars'));

			global $pagenow;
			// Get the editor html template and settings
			if(!self::$done_editor_tmpl && !empty($pagenow) && in_array($pagenow, array('customize.php'))){
				self::$done_editor_tmpl = true;

				self::$render_editor_tmpl = true;
				add_filter('tiny_mce_before_init', array($this, 'mce_editor_settings'), 999, 2);
				if(!class_exists('_WP_Editors', false)){
					require( ABSPATH . WPINC . '/class-wp-editor.php');
				}
				$id = 'fallsky-widget-editor-id';
				$settings = _WP_Editors::parse_settings($id, array());
				_WP_Editors::editor_settings($id, $settings);
				self::$render_editor_tmpl = false;
			}
		}
		/**
		* Add fitler for mce editor settings
		*/
		public function mce_editor_settings($mceInit, $editor_id){
			if(self::$render_editor_tmpl && ($editor_id == 'fallsky-widget-editor-id')){
				$mceInit['wp_skip_init'] = true;
			}
			return $mceInit;
		}
		/***
		* The actual function to output the json and dependency js vars
		* @param array values before changed
		* @return array values after changed
		*/
		public function widget_js_vars($vars = array()){
			if(!empty($this->json) && !empty($this->dependency)){
				$widget_id = $this->id_base;
				if(empty($vars['widgets'])){
					$vars['widgets'] = array();
				}
				if(empty($vars['widget_json'])){
					$vars['widget_json'] = array();
				}
				if(empty($vars['widget_dependency'])){
					$vars['widget_dependency'] = array();
				}

				array_push($vars['widgets'], $widget_id);
				$vars['widget_json'][$widget_id] 		= $this->json;
				$vars['widget_dependency'][$widget_id] 	= $this->dependency;
			}
			else if($this->force_add_widget){
				$widget_id = $this->id_base;
				if(empty($vars['widgets'])){
					$vars['widgets'] = array();
				}
				array_push($vars['widgets'], $widget_id);
			}
			return $vars;
		}
		/**
		* Get setting value by its id, return the default value if not set
		* @param string widget control element id
		* @return mix current setting value
		*/
		public function get_value($id){
			return isset($this->instance[$id]) ? $this->instance[$id] : $this->defaults[$id];
		}
		/**
		* Add the default input attributes to widget control
		* @param array widget control with id, type, default value....
		* @return array widget control 
		*/
		private function attributes($control){
			$item_id 	= array('data-fallsky-widget-item-id' => $control['id']);
			$item_class = sprintf(
				'fallsky-widget-item%s%s', 
				($control['type'] == 'color-picker' ? ' fallsky-color-picker' : ($control['widefat'] ? ' widefat' : '')),
				empty($control['input_attr']['class']) ? '' : sprintf(' %s', $control['input_attr']['class'])
			);
			
			$control['input_attr'] = empty($control['input_attr']) ? $item_id : array_merge($control['input_attr'], $item_id);
			$control['input_attr']['class'] = $item_class;
			return $control;
		}
		/**
		* Dynamically add the widget control
		* @param array control, control need to be added
		*/
		public function add_control($control){
			$control = array_merge(array('id' => '', 'type' => '', 'default' => 'not set', 'widefat' => true), $control);
			if(!empty($control['id']) && !empty($control['type']) && ($control['default'] !== 'not set')){
				$control 	= $this->attributes($control);
				$cid 		= $control['id'];
				$this->controls[$cid] = $control;
				$this->defaults[$cid] = $control['default'];
				if(!empty($control['dependency'])){
					foreach($control['dependency'] as $id => $dep){
						if(empty($this->dependency[$id])){
							$this->dependency[$id] = array();
						}
						$this->dependency[$id][] = $cid;
					}
					$this->json[$cid] = $control['dependency'];
				}
			}
		}
		/**
		* Output the html for each type of controls
		* @param array control
		* @return html 
		*/
		public function render_control($control){
			$html 		= '';
			$cid 		= $control['id'];
			$value 		= $this->get_value($cid);
			$field_id 	= $this->get_field_id($cid);
			$field_name = $this->get_field_name($cid);
			$field_attr = $this->get_attrs($control['input_attr']);
			switch($control['type']){
				case 'radio':
					if(!empty($control['choices'])){
						$html .= empty($control['title']) ? '' : sprintf('<label class="title">%s</label>', $control['title']);
						foreach($control['choices'] as $val => $attr){
							$html .= sprintf(
								'<label class="radio-wrapper" for="%1$s"><input type="radio" id="%1$s" name="%2$s" value="%3$s"%4$s %5$s >%6$s</label>', 
								sprintf('%s-%s', $field_id, $val), 
								$field_name,
								$val,
								checked($val, $value, false),
								$field_attr,
								$attr
							);
						}
					}
					break;
				case 'radio-with-thumbnail':
					if(!empty($control['choices'])){
						$html .= empty($control['title']) ? '' : sprintf('<label class="title">%s</label>', $control['title']);
						$html .= sprintf('<div class="%s-%s-radio-btn-wrap">', $this->id_base, $cid);
						foreach($control['choices'] as $val => $attr){
							$html .= sprintf(
								'<label class="radio-wrapper" for="%1$s"><input type="radio" id="%1$s" name="%2$s" value="%3$s"%4$s %5$s ><span class="thumbnail"></span><span class="thumbnail-title">%6$s</span></label>', 
								sprintf('%s-%s', $field_id, $val), 
								$field_name,
								$val,
								checked($val, $value, false),
								$field_attr,
								$attr
							);
						}
						$html .= '</div>';
					}
					break;
				case 'select':
					if(!empty($control['choices'])){
						$value = is_array($value) ? $value : array($value);
						$html .= empty($control['title']) ? '' : sprintf('<label class="title" for="%s">%s</label>', $field_id, $control['title']);
						$html .= sprintf(
							'<select id="%s" name="%s%s" %s>', 
							$field_id, 
							$field_name,
							in_array('multiple', $control['input_attr']) ? '[]' : '',
							$field_attr
						);
						foreach($control['choices'] as $val => $attr){
							$html .= sprintf(
								'<option value="%s"%s>%s</option>',
								$val,
								in_array($val, $value) ? ' selected' : '',
								is_array($attr) ? $attr['label'] : $attr
							);
						}
						$html .= '</select>';
					}
					break;
				case 'color-picker':
					$default_color = $this->defaults[$cid];
					$html .= empty($control['title']) ? '' : sprintf('<label class="title" for="%s">%s</label>', $field_id, $control['title']);
					$html .= sprintf(
						'<input id="%s" name="%s" type="text" value="%s" placeholder="%s" %s/>', 
						$field_id, 
						$field_name, 
						$value, 
						empty($default_color) ? '#RRGGBB' : $default_color,
						$field_attr
					);
					break;
				case 'checkbox':
					$html .= sprintf(
						'<input type="checkbox" id="%s" name="%s" value="on" %s %s>', 
						$field_id, 
						$field_name, 
						checked($value, 'on', false), 
						$field_attr
					);
					$html .= empty($control['title']) ? '' : sprintf(
						'<label for="%s">%s</label>', 
						$field_id, $control['title']
					);
					$html .= empty($control['description']) ? '' : sprintf(
						'<span class="homepage-widget-control-description description" style="display: block;">%s</span>', 
						$control['description']
					);
					break;
				case 'editor':
					$html .= empty($control['title']) ? '' : sprintf('<label class="title">%s</label>', $control['title']);
					$html .= sprintf(
						'<div class="hide editor-textarea-wrap"><textarea data-id="%s" name="%s" %s>%s</textarea></div>', 
						$field_id, 
						$field_name, 
						$field_attr, 
						$value
					);
					break;
				case 'image':
					$input_html = '';
					$media_html = false;
					$media_src 	= false;
					$mid 		= false;
					$image_only = empty($control['media_types']) || ('image' == $control['media_types']);
					$text 		= array(
						'preview'	=> $image_only ? esc_html__('No image selected', 'fallsky') : esc_html__('No media selected', 'fallsky'),
						'choose' 	=> $image_only ? esc_html__('Choose Image', 'fallsky') : esc_html__('Choose Media', 'fallsky'),
						'remove' 	=> $image_only ? esc_html__('Remove Image', 'fallsky') : esc_html__('Remove Media', 'fallsky')
					);

					if($image_only){
						$mid = isset($value) ? absint($value) : '';
						if(!empty($mid)){
							$media_src = fallsky_get_image_src( $mid, 'medium' );
						}

						$input_html = sprintf( '<input name="%s" type="hidden" value="%s" %s />', $field_name, $value, $field_attr );
						$media_html = $media_src ? sprintf('<img class="attachment-thumb" src="%s" alt="%s">', $media_src, fallsky_get_image_alt($mid)) : '';
					}
					else{
						$value 		= empty($value) ? array('type' => '', 'id' => '') : $value; 
						$mid 		= absint($value['id']);
						$media_alt 	= '';
						if(!empty($mid)){
							$media_src 	= wp_get_attachment_url($mid); 
							if('image' == $value['type']){
								$media_alt	= fallsky_get_image_alt($mid);
							}
						}

						$html_tmpl 	= ('image' == $value['type']) ? '<img class="attachment-thumb" src="%s" alt="%s">' : '<video controls class="attachment-thumb" src="%s">%s</video>';
						$media_html = $media_src ? sprintf($html_tmpl, $media_src, $media_alt) : '';
						$input_html = sprintf(
							'%s%s',
							sprintf('<input name="%s[id]" type="hidden" value="%s" %s />', 	$field_name, $value['id'], $field_attr),
							sprintf('<input name="%s[type]" type="hidden" value="%s" />', 	$field_name, $value['type'])
						);
					}

					$html .= empty($control['title']) ? '' : sprintf('<label class="title">%s</label>', $control['title']);
					$html .= sprintf(
						'%s%s%s',
						sprintf(
							'<div class="media-widget-preview%s" data-text-preview="%s">%s</div>',
							$image_only ? '' : ' media',
							$text['preview'],
							empty($media_html) ? sprintf('<div class="placeholder">%s</div>', $text['preview']) : $media_html
						),
						sprintf(
							'<p class="media-widget-buttons">%s%s</p>',
							sprintf(
								'<button type="button" class="button choose-media%s not-selected">%s</button>',
								$image_only ? '' : ' media',
								$text['choose']
							),
							sprintf(
								'<button type="button" class="button remove-media %s">%s</button>',
								empty($media_html) ? 'selected' : 'not-selected',
								$text['remove']
							)
						),
						$input_html
					);
					break;
				case 'title':
					$html .= empty($control['title']) ? '' : sprintf('<label class="title">%s</label>', $control['title']);
					break;
				case 'description':
					$html .= empty($control['title']) ? '' : sprintf('<label class="title">%s</label>', $control['title']);
					$html .= empty($control['description']) ? '' : sprintf('<span class="description homepage-widget-control-description">%s</span>', $control['description']);
					break;
				case 'textarea':
					$html .= empty($control['title']) ? '' : sprintf('<label class="title">%s</label>', $control['title']);
					$html .= sprintf('<textarea id="%s" name="%s" %s>%s</textarea>', $field_id, $field_name, $field_attr, $value);
					break;
				case 'html':
					$html = $control['html'];
					break;
				case 'slider':
					$after_text = isset( $control['after_text'] ) ? $control['after_text'] : '';
					$html .= sprintf( '<label class="title">%s%s</label>', 
						empty( $control['title'] ) ? '' : $control['title'],
						sprintf(
							'<span class="amount opacity" style="float: right; "><input readonly="readonly" id="%s" name="%s" type="text" value="%s" %s />%s</span>',
							$field_id, 
							$field_name, 
							$value, 
							$field_attr,
							$after_text
						)
					);
					$html .= sprintf(
						'<div class="ui-slider loader-ui-slider" data-value="%s" %s></div>',
						$value, 
						empty( $control['slider_attr'] ) ? '' : $this->get_attrs( $control['slider_attr'] )
					);
					break;
				default:
					$html .= empty($control['title']) ? '' : sprintf('<label class="title" for="%s">%s</label> ', $field_id, $control['title']);
					$html .= sprintf(
						'<input id="%s" name="%s" type="%s" value="%s" %s/>%s', 
						$field_id, 
						$field_name, 
						$control['type'], 
						$value, 
						$field_attr,
						empty($control['text_after']) ? '' : $control['text_after']
					);
			}
			return $html;
		}
		/**
		* Render current widget setting form elements
		* 	Will wrap each element with <p>
		*/
		public function render(){
			if($this->controls && is_array($this->controls)){
				$result = array();
				$wid 	= $this->id;
				$tmpls 	= array(
					'radio-with-thumbnail' 	=> '<div class="item-wrapper item-type-%s" id="%s-%s">%s</div>',
					'image'					=> '<div class="media-widget-control item-wrapper item-type-%s" id="%s-%s">%s</div>',
					'editor'				=> '<div class="editor-widget-control item-wrapper item-type-%s" id="%s-%s">%s</div>',
					'slider'				=> '<div class="slider-widget-control item-wrapper item-type-%s" id="%s-%s">%s</div>',
					'default' 				=> '<p class="item-wrapper item-type-%s" id="%s-%s">%s</p>'
				);
				$keys 	= array_keys($tmpls);	
				foreach($this->controls as $id => $control){
					$html = $this->render_control($control);
					$type = in_array($control['type'], $keys) ? $control['type'] : 'default';
					empty($html) ? '' : array_push($result, sprintf($tmpls[$type], $control['type'], $wid, $control['id'], $html));
				}
				print(implode('', $result));
			}
		}
		/**
		* Output the html attributes string by given attributes
		* @param array attributes list
		* @return html attributes html
		*/
		private function get_attrs($attrs){
			$html = '';
			if(!empty($attrs) && is_array($attrs)){
				foreach($attrs as $name => $val){
					$html .= sprintf('%s="%s" ', $name, $val);
				}
			}
			return $html;
		}
		/**
		* The buildin function to output the setting form
		* @param array current setting values
		*/
		public function form($instance){
			$this->instance = $instance;
			$this->render();
		}
		/**
		 * Handles updating the setting values.
		 */
		public function update($new_instance, $old_instance){
			$old_instance 	= array_merge($this->defaults, $old_instance);
			$instance 		= $old_instance;
			if(!empty($this->controls) && is_array($this->controls)){
				foreach($this->controls as $id => $control){
					$default_cb	 	= array($this, 'sanitize_cb');
					$sanitize_cb 	= empty($control['sanitize_cb']) || !is_callable($control['sanitize_cb']) ? $default_cb : $control['sanitize_cb'];
					$instance[$id] 	= call_user_func($sanitize_cb, $new_instance[$id], $control, $old_instance[$id]);
				}
			}
			return $instance;
		}
		/**
		* Get widget main content
		* 	This function must be overwritten by child class
		* @param html string
		*/
		protected function get_content(){
			return '';
		}
		/**
		* Handles widget output
		* @param array widget arguments
		* @param array current widget setting values
		*/
		public function widget($args, $instance){
			$this->instance = $instance;

			$title 			= $this->get_title($args);
			$before_widget	= $this->get_before_widget($args);
			$content 		= $this->get_content();

			printf(
				'%s<div class="container">%s<div class="section-content">%s</div></div>%s',
				$before_widget,
				$title,
				$content,
				$args['after_widget']
			);
		}
		/**
		* Fallback sanitize callback function
		* @param mix current value
		* @param array control
		* @param mix default value 
		* @return mix always return the current value
		*/
		private function sanitize_cb($val, $control, $default){
			return $val;
		}
		/**
		* Helper function to get widget title
		* @param array sidebar settings
		* @return html string
		*/
		protected function get_title($args){
			$title 	 		= $this->get_value('title');
			$title_align 	= $this->get_value('title-align');
			if(!empty($title)){
				$title_class = empty($title_align) ? '' : ' align-center';
				$title = sprintf(
					'%s%s%s',
					sprintf($args['before_title'], $title_class),
					apply_filters( 'widget_title', $title ),
					$args['after_title']
				);
			}
			return $title;
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
					$styles	   .= empty($image_src) ? '' : sprintf('background-image: url(%s);', $image_src);
					do_action('fallsky_reset_frontend_options');
				}
			}
			$class = $this->get_section_class($class);

			return $this->replace_before_widget($class, $styles, $args['before_widget']);
		}
		/**
		* Helper function to generate any extra section classes
		*  This function can be overwritten by child class if needed
		* @param array classes
		* @return array classes
		*/
		protected function get_section_class($class){
			return $class;
		}
		/**
		* Helper function to generate paddings for homepage widgets
		* @param string current style attributes
		* @return string style attributes
		*/
		protected function get_paddings($styles){
			$cids 		= array_keys($this->controls);
			$paddings 	= array_intersect($cids, array('padding-top', 'padding-bottom'));
			if($paddings && (count($paddings) > 0)){
				$defaults 	= $this->defaults;
				$values 	= array();
				foreach($paddings as $p){ 
					$value = intval($this->get_value($p));
					if(($value >= 0) && (intval($defaults[$p]) != $value)){
						array_push($values, sprintf('%s: %spx;', $p, $value));
					}
				}
				if(!empty($values)){
					$styles = empty($styles) ? '' : sprintf(' %s', $styles);
					$styles = sprintf('%s%s', implode(' ', $values), $styles);
				}
			}
			return $styles;
		}
		/**
		* Helper function to add custom class and inline styles to widget wrapper
		* @param array class names
		* @param string styles
		* @param string before widget string
		* @return string before widget string
		*/
		protected function replace_before_widget($class, $styles, $html){
			$class 	= empty($class) ? false : implode(' ', $class);
			$class 	= $class ? sprintf(' %s', $class) : '';
			$styles = $this->get_paddings($styles);
			return str_replace(
				$this->custom_class,
				empty($styles) ? $class : sprintf('%s" style="%s', $class, $styles),
				$html
			);
		}
		/**
		* Static function to enqueue javascript file for the customized widgets
		* 	Also output the javascript variables needed
		*/
		public static function enqueue_widget_scripts(){
			$version 	= FALLSKY_ASSETS_VERSION;
			$script_uri = FALLSKY_ASSETS_URI . 'js/admin/fallsky-widgets.min.js';
			$style_uri	= FALLSKY_ASSETS_URI . 'css/admin/fallsky-widgets.css';
			$dependency = array('jquery', 'editor', 'wp-util', 'wp-color-picker', 'fallsky-functions-lib');

			wp_enqueue_style('fallsky-widgets', 	$style_uri, array(), $version);
			wp_register_script('fallsky-widgets', 	$script_uri, $dependency, $version, true);
			wp_localize_script('fallsky-widgets', 	'fallskyWidgets', apply_filters('fallsky_widget_js_vars', array()));
			wp_enqueue_script('fallsky-widgets');
		}
		/**
		* Static function to output the editor field template for the customized widgets
		*/
		public static function render_control_template_scripts(){
			printf(
				'<script type="text/html" id="tmpl-widget-editor-field">%s</script>',
				sprintf(
					'<div id="wp-%1$s-wrap" class="wp-core-ui wp-editor-wrap tmce-active">
						<div id="wp-%1$s-editor-tools" class="wp-editor-tools hide-if-no-js">
							<div id="wp-%1$s-media-buttons" class="wp-media-buttons">
								<button type="button" id="insert-media-button" class="button insert-media add_media" data-editor="%1$s"><span class="wp-media-buttons-icon"></span>%2$s</button>
							</div>%5$s
							<div class="wp-editor-tabs">
								<button type="button" id="%1$s-tmce" class="wp-switch-editor switch-tmce" data-wp-editor-id="%1$s">%3$s</button>
								<button type="button" id="%1$s-html" class="wp-switch-editor switch-html" data-wp-editor-id="%1$s">%4$s</button>
							</div>
						</div>
						<div id="wp-%1$s-editor-container" class="wp-editor-container"><div id="qt_%1$s_toolbar" class="quicktags-toolbar"></div><textarea class="widefat"></textarea></div>
					</div>',
					'[[fallsky-widget-editor-id]]',
					esc_html__('Add Media', 'fallsky'),
					esc_html__('Visual', 'fallsky'),
					esc_html__('Text', 'fallsky'),
					class_exists('Fallsky_Extension') 
						? sprintf(
							'<button type="button" id="insert-loftocean-shortcode-button" class="button insert-loftocean-shortcode" data-editor="%s">%s</button>',
							'[[fallsky-widget-editor-id]]',
							esc_html__('Add Shortcode', 'fallsky')
						) : ''
				)
			);
		}
	}
	// Add the action to enqueue js script for widgets.php, it will be called by customize.php
	add_action('admin_print_scripts-widgets.php', 	array('Fallsky_Widget', 'enqueue_widget_scripts'));
	add_action('admin_footer-widgets.php', 			array('Fallsky_Widget', 'render_control_template_scripts'));

	/**
	* Theme widget control sanitize callback functions
	*	For type checkbox, select, text, number, textarea, MCEeditor, color ...
	* @param mix current value
	* @param array control
	* @param mix old value
	* @return mix return the current value if passed, otherwise return the old value
	*/
	if(!function_exists('fallsky_widget_sanitize_checkbox')){
		function fallsky_widget_sanitize_checkbox($new_value, $control, $old_value){
			return empty($new_value) ? '' : 'on';
		}
	}
	if(!function_exists('fallsky_widget_sanitize_choice')){
		function fallsky_widget_sanitize_choice($new_value, $control, $old_value){
			$choices = empty($control['choices']) ? false : array_keys($control['choices']);
			return ($choices && in_array($new_value, $choices)) ? $new_value : $old_value;
		}
	}
	if(!function_exists('fallsky_widget_sanitize_choices')){
		function fallsky_widget_sanitize_choices($new_value, $control, $old_value){
			if(empty($new_value)){
				return array();
			}

			$passed		= true;
			$choices 	= empty($control['choices']) ? false : array_keys($control['choices']);
			if($choices){
				foreach($new_value as $val){
					if(!in_array($val, $choices)){
						$pass = false;
						break;
					}
				}
			}
			return $passed ? $new_value : $old_value;
		}
	}
	if(!function_exists('fallsky_widget_sanitize_text')){
		function fallsky_widget_sanitize_text($new_value, $control, $old_value){
			return empty($new_value) ? '' : sanitize_text_field($new_value);
		}
	}
	if(!function_exists('fallsky_widget_sanitize_number')){
		function fallsky_widget_sanitize_number($new_value, $control, $old_value){
			return absint($new_value);
		}
	}
	if(!function_exists('fallsky_widget_sanitize_html')){
		function fallsky_widget_sanitize_html($new_value, $control, $old_value){
			return empty($new_value) ? '' : apply_filters('format_to_edit', $new_value);
		}
	}
	if(!function_exists('fallsky_widget_sanitize_color')){
		function fallsky_widget_sanitize_color($new_value, $control, $old_value){
			$color = sanitize_hex_color($new_value);
			return $color ? $color : '';
		}
	}
	if(!function_exists('fallsky_widget_sanitize_url')){
		function fallsky_widget_sanitize_url($new_value, $control, $old_value){
			return empty($new_value) ? '' : esc_url($new_value);
		}
	}
	if(!function_exists('fallsky_widget_sanitize_media')){
		function fallsky_widget_sanitize_media($new_value, $control, $old_value){
			return in_array($new_value['type'], array('image', 'video')) || (isset($new_value['type']) && empty($new_value['id']))
				? array('id' => intval($new_value['id']), 'type' => $new_value['type']) : $old_value;
		}
	}
}
