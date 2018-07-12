<?php
/**
 * Support to plugin Polylang
 * 	1. Register homepage widget title
 */
if( !class_exists( 'Fallsky_Polylang' ) && class_exists( 'PLL_Admin_Strings' ) ) {
	class Fallsky_Polylang {
		private $strings = false; 
		public function __construct() {
			add_filter( 'pll_get_strings', array( $this, 'register_strings' ) );
			add_filter( 'fallsky_multilingual_text', 'pll__', 1 );
			add_filter( 'widget_text_content', 'pll__', 1 );
		}
		public function register_strings( $strings ) {
			$label = array(
				'widget_title' 			=> esc_html__( 'Widget title', 'fallsky' ),
				'widget_content' 		=> esc_html__( 'Widget Custom Content text', 'fallsky' ),
				'widget_button'			=> esc_html__( 'Widget Call to Action button text', 'fallsky' ),
				'widget_heading'		=> esc_html__( 'Widget Call to Action heading text', 'fallsky' ),
				'widget_description'	=> esc_html__( 'Widget Call to Action description text', 'fallsky' ),
				
			);

			global $wp_registered_widgets;
			$widgets = fallsky_get_theme_mod( 'fallsky_homepage_main_area' );
			if ( !empty( $widgets ) && is_array( $widgets ) ) {
				$this->strings = empty( $strings ) ? array() : $strings;
				foreach ( $widgets as $widget ) {
					// nothing can be done if the widget is created using pre WP2.8 API :(
					// there is no object, so we can't access it to get the widget options
					if ( ! isset( $wp_registered_widgets[ $widget ]['callback'][0] ) || ! is_object( $wp_registered_widgets[ $widget ]['callback'][0] ) || ! method_exists( $wp_registered_widgets[ $widget ]['callback'][0], 'get_settings' ) ) {
						continue;
					}

					$widget_settings = $wp_registered_widgets[ $widget ]['callback'][0]->get_settings();
					$number = $wp_registered_widgets[ $widget ]['params'][0]['number'];

					// don't enable widget translation if the widget is visible in only one language or if there is no title
					if ( empty( $widget_settings[ $number ]['pll_lang'] ) ) {
						if ( isset( $widget_settings[ $number ]['title'] ) && $title = $widget_settings[ $number ]['title'] ) {
							$this->get_string( $title, $label['widget_title'] );
						}
						if ( isset( $widget_settings[ $number ]['description'] ) && $description = $widget_settings[ $number ]['description'] ) {
							$this->get_string( $description, $label['widget_description'], true );
						}
						if ( isset( $widget_settings[ $number ]['heading'] ) && $heading = $widget_settings[ $number ]['heading'] ) {
							$this->get_string( $heading, $label['widget_heading'] );
						}
						if ( isset( $widget_settings[ $number ]['button-text'] ) && $button = $widget_settings[ $number ]['button-text'] ) {
							$this->get_string( $button, $label['widget_button'] );
						}
						if ( isset( $widget_settings[ $number ]['content'] ) && $content = $widget_settings[ $number ]['content'] ) {
							$this->get_string( $content, $label['widget_content'], true );
						}
					}
				}
			}
			return array_merge( $strings, $this->strings );
		}
		private function get_string( $text, $name, $multiline = false ) {
			if ( $text && is_scalar( $text ) ) {
				$this->strings[ md5( $text ) ] = array( 
					'name' 		=> $name, 
					'string' 	=> $text, 
					'context' 	=> 'Fallsky Homepage Widget',
					'multiline' => $multiline 
				);
			}
		}
	}
	new Fallsky_Polylang();
}