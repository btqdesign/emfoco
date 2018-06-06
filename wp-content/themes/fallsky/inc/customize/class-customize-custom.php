<?php
/**
* Theme customize related class, sanitize functions
*	1.  Theme customize config base class 
*	2.  Theme customize class Fallsky_Customize_Setting
*	3.  Theme customize class Fallsky_Customize_Control
*	4.  Theme customize sanitize function fallsky_sanitize_checkbox
*	5.  Theme customize sanitize function fallsky_sanitize_html
*	6.  Theme customize sanitize function fallsky_sanitize_choice
*	7.  Theme customize sanitize function fallsky_sanitize_mutiple_choices
*	8.  Theme customize sanitize function fallsky_sanitize_empty
*	9.  Theme customize helper function fallsky_get_terms
*	10. Theme customize helper function fallsky_mc4w_forms
*/

if(!class_exists('Fallsky_Customize_Base')){
	/**
	* Theme customize config base class
	*	Each config class will extend this class
	*		1. Action to register customize setting, panel, section and control
	*		2. Filter to add js variables for cutomize.php
	*		3. Filter to add custom styles based on theme settings for frontend
	*		4. Filter to add js variables based on theme settings for frontend 
	*
	* @author Loft.Ocean
	* @since 1.0.0
	*/
	class Fallsky_Customize_Base {
		public function __construct(){
			add_action( 'wp', 						array( $this, 'frontend_actions' ), 1 );
			add_action( 'customize_register', 		array($this, 'register_controls' ) );
			add_action( 'customize_preview_init',	array($this, 'preview_scripts' ) );

			add_filter( 'fallsky_frontend_js_vars', 	array( $this, 'frontend_js_vars' ) );
			add_filter( 'fallsky_custom_styles', 		array( $this, 'custom_styles' ) );
			add_filter( 'fallsky_customize_js_vars', 	array( $this, 'customize_js_vars' ) );
			add_filter( 'fallsky_preview_js_vars',		array( $this, 'preview_js_vars' ) );
			add_filter( 'fallsky_get_css_variables', 	array( $this, 'get_css_variables' ) );
			add_filter( 'fallsky_get_fallback_css', 	array( $this, 'get_fallback_css' ) );
		}
		protected function get_custom_styles() { return array(); }
		public function get_css_variables($vars) { return $vars; }
		public function get_fallback_css($styles) { return $styles; }
		public function register_controls($wp_customize) { }
		public function frontend_js_vars($vars = array()) { return $vars; }
		public function customize_js_vars($vars = array()) { return $vars; }
		public function preview_js_vars($vars = array()) { return $vars; }
		public function frontend_actions(){ }
		public function preview_scripts(){ add_action('wp_head', array($this, 'preview_custom_styles'), 99); }
		public function custom_styles($styles = ''){
			$style_list = $this->get_custom_styles(); 
			if(!empty($style_list)){
				$styles .= implode(' ', $style_list);
			}
			return $styles;
		}
		public function preview_custom_styles(){ 
			$style_list = $this->get_custom_styles();
			$template = '<style id="fallsky-%s">%s</style>';
			if(!empty($style_list)){
				foreach($style_list as $id => $style){
					printf($template, $id, $style);
				}
			}
		}
	}
}


if(class_exists('WP_Customize_Control') && class_exists('WP_Customize_Setting')){
	/**
	* Theme customized setting class to add dependency property and rewrite the json function to print this property to frontend
	*	To determine the display of control if they dependen on some other settings
	*
	* @author Loft.Ocean
	* @since 1.0.0
	*/
	class Fallsky_Customize_Setting extends WP_Customize_Setting {
		public $dependency = array();
		private static $homepage_widgets = array(
			'widget_fallsky-homepage-widget-posts', 
			'widget_fallsky-homepage-widget-banner', 
			'widget_fallsky-homepage-widget-featured-category', 
			'widget_fallsky-homepage-widget-call-action', 
			'widget_fallsky-homepage-widget-custom-content',
			'widget_fallsky-homepage-widget-mc4wp-singup', 
			'widget_fallsky-homepage-widget-products',
			'widget_fallsky-homepage-widget-product-categories'
		);
		public function json() {
			$json = parent::json();
			return empty($this->dependency) ? $json : array_merge($json, array(
				'dependency' => $this->dependency
			));
		}		
		protected function update($value){
			// Update the value first to make sure current value is the latest
			$updated = parent::update($value);
			if ( 'fallsky_homepage_main_area' == $this->id ){
				$this->filter_homepage_widgets();
			}
			return $updated;
		}
		/*
		* Filter homepage widgets which is not in use
		*/
		private function filter_homepage_widgets(){
			$value = fallsky_get_theme_mod( 'fallsky_homepage_main_area' );
			if ( empty( $value ) ) {
				foreach ( self::$homepage_widgets as $hw ) {
					update_option( $hw, array() );
				}
			}
			else {
				$widgets = array();
				foreach ( $value as $w ) {
					if ( preg_match( '/(.+)-([\d]+)/', $w, $m ) ) { 
						$key = sprintf( 'widget_%s', $m[1] );
						$id = $m[2];
						if( array_key_exists( $key, $widgets ) ) {
							array_push( $widgets[ $key ], $id ); 
						}
						else {
							$widgets[ $key ] = array( $id );
						}
					}
				}
				foreach ( self::$homepage_widgets as $hw ) {
					if ( array_key_exists( $hw, $widgets ) ) {
						$option = get_option( $hw );
						if( !empty( $option ) ) {
							foreach ( $option as $wid => $wv ) {
								if ( !in_array( $wid, $widgets[ $hw ] ) ) {
									unset( $option[$wid] );
								}
							}
							update_option( $hw, $option );
						}
					}
					else {
						update_option( $hw, array() );
					}
				}
			}
		}
	}

	/**
	* Theme customized control class to add more control types or modify the default control type
	*
	* @author Loft.Ocean
	* @since 1.0.0
	*/
	class Fallsky_Customize_Control extends WP_Customize_Control {
		public $after_text 					= '';
		public $input_class 				= '';
		public $label_first 				= false;
		public $description_below 			= false;
		public $with_bg						= false;
		public $wrap_id						= '';
		public $children 					= array();
		public $extra_setting 				= false;
		private $render_theme_editor 		= false;
		static private $editor_filter_added = false;
		public function render_content(){
			$description_wrap = empty($this->description_link) ? '%s' : '<a href="' . $this->description_link . '" target="_blank">%s</a>';
			switch($this->type){
				case 'title_only':
					printf('<h3>%s</h3>', esc_html($this->label));
					empty($this->description) ? '' : printf('<span class="description customize-control-description">%s</span>', $this->description);
					break;
				case 'radio':
					if(empty( $this->choices)){
						return;
					}
					if($this->with_bg && !empty($this->wrap_id)){
						$control_id = $this->id;
						$attrs		= sprintf('name="_customize-radio-%s" %s', $control_id, $this->get_link());
						$value 		= $this->value();
						$item_html	= '';
						$content 	= !empty($this->label) ? sprintf('<span class="customize-control-title">%s</span>', esc_html($this->label)) : '';
						$list_wrap 	= '<div id="%s">%s</div>';
						$item_wrap	= '<label for="%1$s" title="%2$s">'
							. '<input id="%1$s" class="fallsky-radiobtn" type="radio" value="%3$s" %4$s>'
							. '<span class="thumbnail"></span>'
							. '<span class="thumbnail-title">%5$s</span>'
							. '</label>';
						foreach($this->choices as $val => $title){
							$item_html .= sprintf(
								$item_wrap,
								sprintf('%s-%s', $control_id, $val),
								esc_attr($title),
								$val,
								sprintf('%s %s', $attrs, checked($val, $value, false)), 
								esc_html($title)
							);
						}
						$content .= sprintf($list_wrap, $this->wrap_id, $item_html);
						$content .= '<div class="customize-control-notifications-container"></div>';
						print($content);
					}
					else{
						$description = '';
						if($this->description_below && !empty($this->description)){
							$description = sprintf('<span class="description customize-control-description">%s</span>', $this->description);
							$this->description = '';
						}
						parent::render_content();
						print($description);
					}
					break;
				case 'checkbox':
					if($this->label_first) : ?>
						<label class="title-first-checkbox"> <?php 
							if(!empty($this->label)) : ?>
								<span class="customize-control-title"><?php echo esc_html($this->label); ?></span> <?php 
							endif; ?>
							<input type="checkbox" value="<?php echo esc_attr($this->value()); ?>" <?php $this->link(); ?> <?php checked('on', $this->value()); ?>> <?php
							if(!empty($this->description)) :
								printf(
									$description_wrap, 
									'<span class="description customize-control-description">' . $this->description . '</span>'
								);
							endif; ?>
						</label>
						<div class="customize-control-notifications-container"></div> <?php
					else :
						parent::render_content();
					endif;
					break;
				case 'multiple_checkbox':
					if(empty($this->choices)){
						return false; 
					} ?>
					<?php if(!empty($this->label)) : ?>
						<h3 class="customize-control-title"><?php echo esc_html($this->label); ?></h3>
					<?php endif;
					if(!empty($this->description) && empty($this->description_below)) : ?>
						<span class="description customize-control-description"><?php print($this->description); ?></span> <?php 
					endif;
					$values = (array)$this->value();
					$manager = $this->manager;
					echo '<div class="multiple-checkbox-wrap">';
					foreach($this->choices as $value => $attr){
						$value = $attr['value'];
						$setting = $manager->get_setting($attr['setting']);
						if($setting) : ?>
							<label>
								<input type="checkbox" data-customize-setting-link="<?php print($attr['setting']); ?>" value="<?php echo esc_attr($value); ?>" <?php checked($value, $setting->value()); ?> ><?php print($attr['label']); ?>
							</label> <?php
						endif;
					}
					echo '</div>';

					if(!empty($this->description) && !empty($this->description_below)) : ?>
						<span class="description customize-control-description"><?php print($this->description); ?></span> <?php 
					endif;
					echo '<div class="customize-control-notifications-container"></div>';
					break;
				case 'number_slider':
					if(empty( $this->input_attrs)){
						return;
					}

					echo '<label class="amount opacity">';
					if(!empty( $this->label)) : ?>
						<span class="customize-control-title" style="display: inline;"><?php echo esc_html($this->label); ?></span>
					<?php endif; ?>
					<span class="<?php print($this->input_class); ?>" style="float: right;">
						<input readonly="readonly" type="text" <?php $this->link(); ?> value="<?php echo esc_attr( $this->value() ); ?>" >
						<?php print($this->after_text); ?>
					</span>
					<?php echo '</label>'; ?>
					<div class="ui-slider loader-ui-slider" data-value="<?php print($this->manager->get_setting($this->id)->value()); ?>" <?php $this->input_attrs(); ?>></div>
					<div class="customize-control-notifications-container"></div> <?php
					break;
				case 'number_with_unit': ?>
					<label>
						<?php if(!empty( $this->label)) : ?>
							<span class="customize-control-title" style="display: inline-block;"><?php echo esc_html($this->label); ?></span>
						<?php endif;
						if (!empty($this->description)) : ?>
							<span class="description customize-control-description"><?php print($this->description); ?></span>
						<?php endif; ?>
						<span class="inline-block number-with-label-wrapper" style="float: right;">
							<input style="width: 70px;" class="<?php echo esc_attr($this->input_class); ?>" type="number" <?php $this->input_attrs(); ?> value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> /> <?php echo empty($this->after_text) ? '' : $this->after_text; ?>
						</span>
						<div class="customize-control-notifications-container"></div>
					</label> <?php
					break;
				case 'multiple_selection':
					if(empty($this->choices)){
						return false; 
					} ?>
					<label>
						<?php if(!empty($this->label)) : ?>
							<span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
						<?php endif;
						if (!empty($this->description)) : ?>
							<span class="description customize-control-description"><?php print($this->description); ?></span>
						<?php endif; ?>

						<select <?php $this->link(); ?> multiple> <?php
							$val = $this->value();
							$options = '';
							foreach($this->choices as $value => $label){
								$options .= sprintf(
									'<option value="%s"%s>%s</option>',
									esc_attr($value),
									!empty($val) && is_array($val) && in_array($value, $val) ? ' selected' : '',
									esc_html($label)
								);
							}
							print($options); ?>
						</select>
					</label>
					<div class="customize-control-notifications-container"></div> <?php
					break;
				case 'image_id':
					$image_id = intval($this->value()); 
					$remove_style = ' style="display: none;"';
					$image = $class = $image_style = $placeholder_style = '';
					if(!empty($image_id) && wp_get_attachment_image_src($image_id, 'medium')){
						$tmp = wp_get_attachment_image_src($image_id, 'medium');
						$image = sprintf('<div class="thumbnail thumbnail-image"><img class="attachment-thumb" src="%s" alt="%s" /></div>', esc_url($tmp[0]), fallsky_get_image_alt($image_id));
						$class = ' attachment-media-view-image';
						$remove_style = ''; 
						$placeholder_style = ' style="display: none;"';
			    	} ?>
					<label for="<?php print($this->id); ?>-button">
						<span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
					</label>
					<div class="attachment-media-view<?php print($class); ?>">
						<div class="placeholder"<?php print($placeholder_style); ?>><?php esc_html_e('No image selected', 'fallsky'); ?></div>
						<?php print($image); ?>
						<div class="actions">
							<button type="button" class="button remove-button fallsky-customize-remove-image"<?php print($remove_style); ?>><?php esc_html_e('Remove', 'fallsky'); ?></button>
							<button type="button" class="button upload-button fallsky-customize-upload-image" id="<?php print($this->id); ?>-button"><?php esc_html_e('Select Image', 'fallsky'); ?></button>	
							<div style="clear:both"></div>
						</div>
						<input type="hidden" value="<?php print($image_id); ?>" <?php $this->link(); ?> />
					</div> 
					<div class="customize-control-notifications-container"></div> <?php
					break;
				case 'notes':
					if(!empty($this->description)){ ?>
						<span class="description customize-control-description"><?php print($this->description); ?></span> <?php
					}
					break;
				case 'mce_editor':
					if(!empty( $this->label)) : ?>
						<span class="customize-control-title" style="display: inline-block;"><?php echo esc_html($this->label); ?></span>
					<?php endif;
					if (!empty($this->description)) : ?>
						<span class="description customize-control-description"><?php print($this->description); ?></span>
					<?php endif; 
					$this->render_theme_editor = true;
					$this->add_editor_filter();
					wp_editor($this->value(), $this->id, array('media_buttons' => true));
					$this->render_theme_editor = false;
					echo '<div class="customize-control-notifications-container"></div>';
					break;
				case 'button': 
					if(!empty($this->label)) : ?>
						<span class="customize-control-title"><?php echo esc_html($this->label); ?></span> <?php 
					endif;
					if(!empty($this->description)) : ?>
						<span class="description customize-control-description"><?php echo esc_html($this->description); ?></span> <?php 
					endif; ?>
					<input type="button" <?php $this->link(); ?> <?php $this->input_attrs(); ?> value="<?php print($this->value()); ?>" class="button button-primary" />
					<div class="customize-control-notifications-container"></div> <?php
					break;
				case 'group':
					if(empty($this->children)){
						return ;
					}
					if(!empty($this->label)){
						printf('<h3 class="group-title">%s</h3>', esc_html($this->label));
					}
					if(!empty($this->description)){
						printf('<span class="description customize-control-description">%s</span>', $this->description);
					}
					print('<ul class="group-controls-wrap"></ul>');
					break;
				case 'homepage_area':
					if(!empty($this->label)) : ?>
						<span class="customize-control-title"><?php echo esc_html($this->label); ?></span> <?php 
					endif; ?>
					<ul class="fallsky-homepage-area-wrap"></ul>
					<button type="button" class="button add-new-widget" aria-expanded="false" aria-controls="available-widgets">
						<?php esc_html_e('Add a Widget', 'fallsky'); ?>
					</button> <?php
					break;
				default:
					parent::render_content();
			}
		}
		/**
		* Add filter for theme control editor
		*/
		private function add_editor_filter(){
			if(!self::$editor_filter_added){
				add_filter('the_editor', array($this, 'mce_editor_html'), 999);
				self::$editor_filter_added = true;
			}
		}
		/**
		* @description add data-customize-setting-link attribute to editor
		* 	Called by core function wp_editor
		* @param $html string
		* @return string
		*/
		public function mce_editor_html($html){
			if($this->render_theme_editor){
				$id = $this->id;
				$data_link = $this->get_link();
				if(strpos($html, $id) !== false){
					$html = str_replace('id="' . $id . '">', 'id="' . $id . '" ' . $data_link . '>' , $html);
				}
			}
			return $html;
		}
		/**
		* @description add children to json
		*/
		public function to_json(){
			parent::to_json();
			switch($this->type){
				case 'group':
					if(!empty($this->children)){
						$children = array();
						foreach($this->children as $sub_control){
							$children[$sub_control->id] = $sub_control->json();
						}
						$this->json['children'] = $children;
					}
					break;
				case 'homepage_area':
					$this->json['homepage_area_widgets'] = $this->setting->json();
					break;
			}
		}
	}
}

if(!function_exists('fallsky_cutomize_control_active_cb')){
	/**
	* Customize control active callback function to test whether display current control.
	*    Dependency settings could be more than one, treat as logical AND.
	*    Each dependency setting value is array, so testing will be based on in/not in.
	* @param object WP_Csutomize_Control to test on 
	* @return boolean if depenency test not enable return true, otherwise test if current value is in the list given.
	*/
	function fallsky_customize_control_active_cb($control){
		if($control instanceof WP_Customize_Control){
			$manager  = $control->manager; 
			$settings = $control->settings; 
			$setting = !empty($settings['default']) ? $settings['default'] : false;
			$setting = ($control instanceof WP_Customize_Background_Position_Control) ? $settings['x'] : $setting;

			if($setting instanceof Fallsky_Customize_Setting){  
				$dependency = $setting->dependency;
				if(!empty($dependency)){
					foreach($dependency as $id => $attrs){
						if(empty($attrs['value'])){ // If not provide the test value list, return false
							return false;
						}
						if($manager->get_setting($id) instanceof WP_Customize_Setting){
							// Test operator, potential value: in/not in. The default is in.
							$is_not = !empty($attrs['operator']) && (strtolower($attrs['operator']) == 'not in');
							$value = $manager->get_setting($id)->value();
							$values = $attrs['value'];
							if(($is_not && in_array($value, $values)) || (!$is_not && !in_array($value, $values))){
								return false;
							}
						}
					}
				}
				return true;
			}
		}
		return false;
	}
}

if(!function_exists('fallsky_sanitize_checkbox')){
	/**
	* Check the switch checkbox value
	*
	* @param string the value from user
	* @return mix if set return string 'on', otherwise return false
	*/
	function fallsky_sanitize_checkbox($input){
		return empty($input) ? false : 'on';
	}
}

if(!function_exists('fallsky_sanitize_html')){
	/**
	* Check the html
	*
	* @param string the value from user
	* @return mix if set return string 'on', otherwise return false
	*/
	function fallsky_sanitize_html($text){ 
		return empty($text) ? '' : apply_filters('format_to_edit', $text);
	}
}

if(!function_exists('fallsky_sanitize_choice')){
	/**
	* Check the value is one of the choices from customize control
	*
	* @param string the value from user
	* @param object customize setting object
	* @return string the value from user or the default setting value
	*/

	function fallsky_sanitize_choice($input, $setting){
		$control = $setting->manager->get_control($setting->id);
		if($control instanceof WP_Customize_Control){
			$choices = $control->choices;
			return (array_key_exists($input, $choices) ? $input : $setting->default);
		}
		else{
			return $input;
		}
	}
}

if(!function_exists('fallsky_sanitize_mutiple_choices')){
	/**
	* Check the array if all the element is from the choices of customize control
	*
	* @param array the value from user
	* @param object WP_Customize_Setting object, its id must be same as the control's.
	* @return string the value from user or the default setting value
	*/
	function fallsky_sanitize_mutiple_choices($input, $setting){ 
		$control = $setting->manager->get_control($setting->id);
		if($control instanceof WP_Customize_Control){
			$choices = $control->choices;
			if(is_array($input)){
				foreach($input as $i){
					if(!array_key_exists($i, $choices)){
						return $setting->default;
					}
				}
				return $input;
			}
		}
		else{
			return $input;
		}
	}
}		

if(!function_exists('fallsky_sanitize_array')){
	/**
	* Sanitize function for customize control homepage area
	*
	* @param string the value from user
	* @param object customize setting object
	* @return string the value from user or the default setting value
	*/
	function fallsky_sanitize_array($input, $setting){
		return is_array($input) && !empty($input) ? $input : $setting->default;
	}
}

if(!function_exists('fallsky_sanitize_empty')){
	/**
	* Sanitize function for customize control *_title, which are just title. Always return false
	*
	* @param string the value from user
	* @param object customize setting object
	* @return string the value from user or the default setting value
	*/
	function fallsky_sanitize_empty($input, $setting){ 
		return false;
	}
}

if(!function_exists('fallsky_get_terms')){
	/*
	* Get terms array by given argument
	*
	* @param array refer to https://developer.wordpress.org/reference/functions/get_terms/
	* @param boolean flag to add the all option or not
	* @param mix the all option label, if not provided use the default 'All'
	* @return array of terms with term_id as index and term name as value
	*/
	function fallsky_get_terms($tax, $all = true, $all_label = false){
		$terms = get_terms(array('taxonomy' => $tax));
		if(!is_wp_error($terms)){
			$array = $all ? array('' => (empty($all_label) ? esc_html__('All', 'fallsky') : $all_label)) : array();
			foreach($terms as $t){
				$array[$t->slug] = $t->name;
			}
			return $array;
		}
		return array();
	}
}

if(!function_exists('fallsky_get_menus')){
	/**
	* Get menu list
	* @return array of menu list with menu id as index and menu name as value
	*/
	function fallsky_get_menus(){
		$menu_list = array();
		$nav_menus = wp_get_nav_menus();
		if(count($nav_menus)){
			$menu_list[''] = sprintf('&mdash; %s &mdash;', esc_html__('Choose a Menu', 'fallsky'));
			foreach($nav_menus as $nav){
				$menu_list[$nav->term_id] = esc_html($nav->name);
			}
		}
		else{
			$menu_list = array('' => esc_html__('No Menu set yet', 'fallsky'));
		}
		return $menu_list;
	}
}

if(!function_exists('fallsky_mc4w_forms')){
	/*
	* Get Mailchimp for WP forms
	* @return array of Mailchimp for WP forms with form_id as index and form title as value
	*/
	function fallsky_mc4w_forms(){
		$forms = get_posts(array(
			'posts_per_page'	=> -1,
			'post_type' 		=> 'mc4wp-form'
		));
		if(!is_wp_error($forms)){
			$array = array('' => esc_html__('Choose Form', 'fallsky'));
			foreach($forms as $f){
				$array[$f->ID] = apply_filters('the_title', $f->post_title);
			}
			return $array;
		}
		return array();
	}
}

if(!function_exists('fallsky_get_bg_image_position')){
	/**
	* Get background image position
	* @param string current value
	* @param string
	* @return string
	*/
	function fallsky_get_bg_image_position($val, $x = true){
		$values = ($x) ? array('left', 'center', 'right') : array('top', 'center', 'bottom');
		return in_array($val, $values) ? $val : reset($values);
	}
}

if(!function_exists('fallsky_get_selector')){
	/**
	* Get style selector
	* @param array selector list
	* @return string selector string
	*/
	function fallsky_get_selector($lists){
		return join($lists, ', ');
	}
}

if(!function_exists('fallsky_get_style')){
	/**
	* @description get the css style
	* @param string setting id
	* @param string selector string
	* @param string css style for printing
	* @param string setting value
	* @return string
	*/
	function fallsky_get_style($id, $selector, $style, $value = false){
		global $fallsky_default_settings;
		// If not provided, get the setting value
		if(empty($value)) $value = fallsky_get_theme_mod($id);

		if(strtolower($fallsky_default_settings[$id]) != strtolower($value)){
			return sprintf(
				'%s { %s }',
				$selector,
				sprintf($style, $value)
			);
		}
		return '';
	}
}

