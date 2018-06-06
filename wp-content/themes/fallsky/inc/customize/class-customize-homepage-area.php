<?php
/**
* Theme Customize Homepage Area class
* 	Implements homepage areas management in the Customizer.
*/

final class Fallsky_Customize_Homepage_Area {
	// WP_Customize_Manager instance
	public $manager;
	// Section id
	private $section_id 		= '';
	// Homapage Area id
	private $area_id 			= '';
	// Control settings for homepage widgets
	private $homepage_widgets 	= array();
	//Mapping of setting type to setting ID pattern.
	protected $setting_id_patterns = array(
		'widget_instance' 	=> '/^widget_(?P<id_base>.+?)(?:\[(?P<widget_number>\d+)\])?$/',
		'homepage_area' 	=> '/^fallsky_homepage_area_\[(?P<area_id>.+?)\]$/',
	);
	/**
	* @param WP_Customize_Manager $manager Customize manager bootstrap instance.
	* @param string section id
	* @param string area id
	*/
	public function __construct($manager, $section, $area){
		$this->manager		= $manager;
		$this->section_id 	= $section;
		$this->area_id 		= $area;

		add_action('customize_register', 		array($this, 'schedule_customize_register'), 1);
		add_filter('fallsky_customize_js_vars', array($this, 'homepage_widget_controls'));
	}
	/**
	* List whether each registered widget can be use selective refresh.
	* 	If the theme does not support the customize-selective-refresh-widgets feature,
	* 		then this will always return an empty array.
	* @global WP_Widget_Factory $wp_widget_factory
	* @return array Mapping of id_base to support. If theme doesn't support
	*               selective refresh, an empty array is returned.
	*/
	public function get_selective_refreshable_widgets(){
		global $wp_widget_factory;
		if(!current_theme_supports('customize-selective-refresh-widgets')){
			return array();
		}
		if(!isset($this->selective_refreshable_widgets)){
			$this->selective_refreshable_widgets = array();
			foreach($wp_widget_factory->widgets as $wp_widget){
				$this->selective_refreshable_widgets[$wp_widget->id_base] = !empty($wp_widget->widget_options['customize_selective_refresh']);
			}
		}
		return $this->selective_refreshable_widgets;
	}

	/**
	* Determines if a widget supports selective refresh.
	*
	* @param string $id_base Widget ID Base.
	* @return bool Whether the widget can be selective refreshed.
	*/
	public function is_widget_selective_refreshable($id_base){
		$selective_refreshable_widgets = $this->get_selective_refreshable_widgets();
		return !empty($selective_refreshable_widgets[$id_base]);
	}
	/**
	* Ensures widgets are available for all types of previews.
	*
	* When in preview, hook to {@see 'customize_register'} for settings after WordPress is loaded
	* so that all filters have been initialized (e.g. Widget Visibility).
	*/
	public function schedule_customize_register() {
		is_admin() ? $this->customize_register() : add_action('wp', array($this, 'customize_register'));
	}
	/**
	* Registers Customizer controls for all homepage area.
	* @global array $wp_registered_widgets
	* @global array $wp_registered_widget_controls
	*/
	public function customize_register() {
		global $wp_registered_widgets, $wp_registered_widget_controls;
		$new_setting_ids = array();

		if(!empty($this->section_id) && !empty($this->area_id)){
			$area_widgets 	= array();
			$widget_ids 	= get_theme_mod($this->area_id);
			if(empty($widget_ids)){
				$widget_ids = array();
			}

			$section_id = $this->section_id;
			// Add a control for each active widget (located in a sidebar).
			foreach($widget_ids as $i => $widget_id){
				// Skip widgets that may have gone away due to a plugin being deactivated.
				if(!isset($wp_registered_widgets[$widget_id])){
					continue; 
				}

				$registered_widget 	= $wp_registered_widgets[$widget_id];
				$setting_id 		= $this->get_setting_id($widget_id);
				$id_base 			= $wp_registered_widget_controls[$widget_id]['id_base'];
				$setting_args 		= $this->get_setting_args($setting_id);
				if(!$this->manager->get_setting($setting_id)){
					$this->manager->add_setting($setting_id, $setting_args);
				}
				$new_setting_ids[] = $setting_id;

				$control = new WP_Widget_Form_Customize_Control($this->manager, $setting_id, array(
					'label'          => $registered_widget['name'],
					'section'        => $section_id,
					'sidebar_id'     => $section_id,
					'widget_id'      => $widget_id,
					'widget_id_base' => $id_base,
					'priority'       => $i,
					'width'          => $wp_registered_widget_controls[$widget_id]['width'],
					'height'         => $wp_registered_widget_controls[$widget_id]['height'],
					'is_wide'        => $this->is_wide_widget( $widget_id ),
				) );
				$area_widgets[$setting_id] = $control;
			}
			$this->homepage_widgets[$this->area_id] = $area_widgets;
		}
		if($this->manager->settings_previewed()){
			foreach($new_setting_ids as $new_setting_id){
				$this->manager->get_setting( $new_setting_id )->preview();
			}
		}
	}
	/**
	* Converts a widget_id into its corresponding Customizer setting ID (option name).
	*
	* @param string $widget_id Widget ID.
	* @return string Maybe-parsed widget ID.
	*/
	public function get_setting_id($widget_id){
		$parsed_widget_id = $this->parse_widget_id($widget_id);
		$setting_id       = sprintf('widget_%s', $parsed_widget_id['id_base']);

		if(!is_null( $parsed_widget_id['number'])){
			$setting_id .= sprintf('[%d]', $parsed_widget_id['number']);
		}
		return $setting_id;
	}

	/**
	* Determines whether the widget is considered "wide".
	*
	* Core widgets which may have controls wider than 250, but can still be shown
	* in the narrow Customizer panel. The RSS and Text widgets in Core, for example,
	* have widths of 400 and yet they still render fine in the Customizer panel.
	*
	* This method will return all Core widgets as being not wide, but this can be
	* overridden with the {@see 'is_wide_widget_in_customizer'} filter.
	*
	* @global $wp_registered_widget_controls
	*
	* @param string $widget_id Widget ID.
	* @return bool Whether or not the widget is a "wide" widget.
	*/
	public function is_wide_widget($widget_id){
		global $wp_registered_widget_controls;

		$width 		= $wp_registered_widget_controls[$widget_id]['width'];
		$is_wide 	= ($width > 250);

		/**
		 * Filters whether the given widget is considered "wide".
		 *
		 * @since 3.9.0
		 *
		 * @param bool   $is_wide   Whether the widget is wide, Default false.
		 * @param string $widget_id Widget ID.
		 */
		return apply_filters('is_wide_widget_in_customizer', $is_wide, $widget_id);
	}

	/**
	* Converts a widget ID into its id_base and number components.
	*
	* @param string $widget_id Widget ID.
	* @return array Array containing a widget's id_base and number components.
	*/
	public function parse_widget_id($widget_id){
		$parsed = array(
			'number' => null,
			'id_base' => null,
		);

		if(preg_match( '/^(.+)-(\d+)$/', $widget_id, $matches)){
			$parsed['id_base'] = $matches[1];
			$parsed['number']  = intval($matches[2]);
		} else {
			// likely an old single widget
			$parsed['id_base'] = $widget_id;
		}
		return $parsed;
	}
	/**
	* Retrieves common arguments to supply when constructing a Customizer setting.
	*
	* @param string $id        Widget setting ID.
	* @param array  $overrides Array of setting overrides.
	* @return array Possibly modified setting arguments.
	*/
	public function get_setting_args($id, $overrides = array()){
		$args = array(
			'type'       => 'option',
			'capability' => 'edit_theme_options',
			'default'    => array(),
		);

		if(preg_match($this->setting_id_patterns['homepage_area'], $id, $matches)){
			$args['sanitize_callback'] 		= array($this, 'sanitize_homepage_area');
			$args['sanitize_js_callback'] 	= array($this, 'sanitize_homepage_area_js_instance');
			$args['transport'] 				= current_theme_supports('customize-selective-refresh-widgets') ? 'postMessage' : 'refresh';
		} 
		else if(preg_match( $this->setting_id_patterns['widget_instance'], $id, $matches)){
			$args['sanitize_callback'] 		= array( $this, 'sanitize_widget_instance' );
			$args['sanitize_js_callback'] 	= array( $this, 'sanitize_widget_js_instance' );
			$args['transport'] 				= $this->is_widget_selective_refreshable( $matches['id_base'] ) ? 'postMessage' : 'refresh';
		}

		$args = array_merge($args, $overrides);

		/**
		* Filters the common arguments supplied when constructing a Customizer setting.
		*
		* @see WP_Customize_Setting
		*
		* @param array  $args Array of Customizer setting arguments.
		* @param string $id   Widget setting ID.
		*/
		return apply_filters('widget_customizer_setting_args', $args, $id);
	}

	/**
	* Ensures sidebar widget arrays only ever contain widget IDS.
	*
	* Used as the 'sanitize_callback' for each $sidebars_widgets setting.
	*
	* @since 3.9.0
	*
	* @param array $widget_ids Array of widget IDs.
	* @return array Array of sanitized widget IDs.
	*/
	public function sanitize_homepage_area($widget_ids){
		$widget_ids = array_map('strval', (array)$widget_ids);
		$sanitized_widget_ids = array();
		foreach($widget_ids as $widget_id){
			$sanitized_widget_ids[] = preg_replace('/[^a-z0-9_\-]/', '', $widget_id);
		}
		return $sanitized_widget_ids;
	}
	/**
	* Sanitizes a widget instance.
	*
	* Unserialize the JS-instance for storing in the options. It's important that this filter
	* only get applied to an instance *once*.
	*
	* @param array $value Widget instance to sanitize.
	* @return array|void Sanitized widget instance.
	*/
	public function sanitize_widget_instance($value){
		if($value === array()){
			return $value;
		}
		if(empty($value['is_widget_customizer_js_value']) || empty($value['instance_hash_key']) || empty($value['encoded_serialized_instance'])){
			return;
		}

		$decoded = gzuncompress($value['encoded_serialized_instance']); 
		if(false === $decoded){
			return;
		}

		if(!hash_equals($this->get_instance_hash_key($decoded), $value['instance_hash_key'])){
			return;
		}

		$instance = unserialize($decoded);
		if(false === $instance){
			return;
		}

		return $instance;
	}
	/**
	* Converts a widget instance into JSON-representable format.
	*
	* @param array $value Widget instance to convert to JSON.
	* @return array JSON-converted widget instance.
	*/
	public function sanitize_widget_js_instance($value){
		if(empty( $value['is_widget_customizer_js_value'])){
			$serialized = serialize($value);

			$value = array(
				'encoded_serialized_instance'   => gzcompress($serialized), 
				'title'                         => empty($value['title']) ? '' : $value['title'],
				'is_widget_customizer_js_value' => true,
				'instance_hash_key'             => $this->get_instance_hash_key($serialized),
			);
		}
		return $value;
	}

	/**
	* Strips out widget IDs for widgets which are no longer registered.
	*
	* One example where this might happen is when a plugin orphans a widget
	* in a sidebar upon deactivation.
	*
	* @global array $wp_registered_widgets
	*
	* @param array $widget_ids List of widget IDs.
	* @return array Parsed list of widget IDs.
	*/
	public function sanitize_homepage_area_js_instance($widget_ids){
		global $wp_registered_widgets;
		$widget_ids = array_values(array_intersect($widget_ids, array_keys($wp_registered_widgets)));
		return $widget_ids;
	}
	/*
	* Export the homepage widget controls
	*/
	public function homepage_widget_controls($vars){
		if(!empty($this->homepage_widgets)){
			$json = array();
			foreach($this->homepage_widgets as $area_id => $controls){
				$settings = array();
				foreach($controls as $id => $control){
					$settings[$id] = $control->json();
				}
				$json[$area_id] = $settings;
			}
			$vars['homepage_area_settings'] = $json;
		}

		return $vars;
	}
}
