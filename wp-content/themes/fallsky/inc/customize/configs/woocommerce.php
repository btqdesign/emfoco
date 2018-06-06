<?php
/**
* Customize section woocommerce configuration files.
*/

if(!class_exists('Fallsky_Customize_WooCommerce')){
	class Fallsky_Customize_WooCommerce extends Fallsky_Customize_Base {
		public function __construct(){
			parent::__construct();
			require_once FALLSKY_THEME_INC . 'customize/frontend-render/class-woocommerce.php';
		}
		public function register_controls($wp_customize){
			global $fallsky_default_settings;

			// Section
			$wp_customize->add_section('fallsky_section_woocommerce', array(
				'title'    => esc_html__('Shop', 'fallsky'),
				'priority' => 55
			));

			// Settings
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_woocommerce_site_header_group', array(
				'default'   		=> '',
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_empty'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_woocommerce_show_cart', array(
				'default'  		 	=> $fallsky_default_settings['fallsky_woocommerce_show_cart'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_checkbox'
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_woocommerce_cart_button_style', array(
				'default'   		=> $fallsky_default_settings['fallsky_woocommerce_cart_button_style'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_choice',
				'dependency'		=> array(
					'fallsky_woocommerce_show_cart' => array('value' => array('on'))
				)
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_woocommerce_sidebar_group', array(
				'default'   		=> '',
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_empty'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_woocommerce_sidebar_general', array(
				'default'  			=> $fallsky_default_settings['fallsky_woocommerce_sidebar_general'],
				'transport'			=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_woocommerce_sidebar_single', array(
				'default'  			=> $fallsky_default_settings['fallsky_woocommerce_sidebar_single'],
				'transport'			=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_woocommerce_sidebar_content', array(
				'default'  			=> $fallsky_default_settings['fallsky_woocommerce_sidebar_content'],
				'transport'			=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_woocommerce_archive_group', array(
				'default'   		=> '',
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_empty'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_woocommerce_archive_layout', array(
				'default'  			=> $fallsky_default_settings['fallsky_woocommerce_archive_layout'],
				'transport'			=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_woocommerce_archive_style', array(
				'default'  			=> $fallsky_default_settings['fallsky_woocommerce_archive_style'],
				'transport'			=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_woocommerce_archive_overlay_color_scheme', array(
				'default'  			=> $fallsky_default_settings['fallsky_woocommerce_archive_overlay_color_scheme'],
				'transport'			=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_choice',
				'dependency'		=> array(
					'fallsky_woocommerce_archive_style' => array('value' => array('overlay'))
				)
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_woocommerce_products_per_page', array(
				'default'  			=> $fallsky_default_settings['fallsky_woocommerce_products_per_page'],
				'transport'			=> 'refresh',
				'sanitize_callback' => 'absint'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_woocommerce_archive_show_title', array(
				'default'   		=> $fallsky_default_settings['fallsky_woocommerce_archive_show_title'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_checkbox'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_woocommerce_archive_show_price', array(
				'default'  			=> $fallsky_default_settings['fallsky_woocommerce_archive_show_price'],
				'transport'			=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_checkbox'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_woocommerce_archive_show_rating', array(
				'default'  			=> $fallsky_default_settings['fallsky_woocommerce_archive_show_rating'],
				'transport'			=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_checkbox'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_woocommerce_archive_show_sale', array(
				'default'  			=> $fallsky_default_settings['fallsky_woocommerce_archive_show_sale'],
				'transport'			=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_checkbox'
			)));

			// Controls
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_woocommerce_site_header_group', array(
				'type' 		=> 'group',
				'label'		=> esc_html__('Site Header', 'fallsky'),
				'section'	=> 'fallsky_section_woocommerce',
				'settings'	=> 'fallsky_woocommerce_site_header_group',
				'children'	=> array(
					new Fallsky_Customize_Control($wp_customize, 'fallsky_woocommerce_show_cart', array(
						'type' 			=> 'checkbox',
						'label_first'	=> true,
						'label'			=> esc_html__('Show Cart Button in site header', 'fallsky'),
						'settings'		=> 'fallsky_woocommerce_show_cart',
						'section'		=> 'fallsky_section_woocommerce'
					)),
					new Fallsky_Customize_Control($wp_customize, 'fallsky_woocommerce_cart_button_style', array(
						'type'				=> 'select',
						'label' 			=> esc_html__('Cart Button Style', 'fallsky'),
						'section' 			=> 'fallsky_section_woocommerce',
						'settings' 			=> 'fallsky_woocommerce_cart_button_style',
						'active_callback'	=> 'fallsky_customize_control_active_cb',
						'choices' 			=> array(
							'' 			=> esc_html__('Text', 'fallsky'),
							'icon-only' => esc_html__('Icon', 'fallsky')
						)
					))
				)
			)));

			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_woocommerce_sidebar_group', array(
				'type' 		=> 'group',
				'label'		=> esc_html__('Sidebar', 'fallsky'),
				'section'	=> 'fallsky_section_woocommerce',
				'settings'	=> 'fallsky_woocommerce_sidebar_group',
				'children'	=> array(
					new Fallsky_Customize_Control($wp_customize, 'fallsky_woocommerce_sidebar_general', array(
						'type'		=> 'radio',
						'label' 	=> esc_html__('Products Archive Sidebar Layout', 'fallsky'),
						'section' 	=> 'fallsky_section_woocommerce',
						'settings' 	=> 'fallsky_woocommerce_sidebar_general',
						'choices' 	=> array(
							''						=> esc_html__('No Sidebar', 'fallsky'),
							'with-sidebar-right' 	=> esc_html__('Right Sidebar', 'fallsky'),
							'with-sidebar-left' 	=> esc_html__('Left Sidebar', 'fallsky')
						)
					)),
					new Fallsky_Customize_Control($wp_customize, 'fallsky_woocommerce_sidebar_single', array(
						'type'		=> 'radio',
						'label' 	=> esc_html__('Single Product Sidebar Layout', 'fallsky'),
						'section' 	=> 'fallsky_section_woocommerce',
						'settings' 	=> 'fallsky_woocommerce_sidebar_single',
						'choices' 	=> array(
							''						=> esc_html__('No Sidebar', 'fallsky'),
							'with-sidebar-right' 	=> esc_html__('Right Sidebar', 'fallsky'),
							'with-sidebar-left' 	=> esc_html__('Left Sidebar', 'fallsky')
						)
					)),
					new Fallsky_Customize_Control($wp_customize, 'fallsky_woocommerce_sidebar_content', array(
						'type'		=> 'select',
						'label' 	=> esc_html__('Sidebar Content', 'fallsky'),
						'section' 	=> 'fallsky_section_woocommerce',
						'settings' 	=> 'fallsky_woocommerce_sidebar_content',
						'choices' 	=> array(
							'main-sidebar' => esc_html__('Main Sidebar', 'fallsky'),
							'shop-sidebar' => esc_html__('Shop Sidebar', 'fallsky')
						)
					))
				)
			)));

			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_woocommerce_archive_group', array(
				'type' 		=> 'group',
				'label'		=> esc_html__('Shop Page & Archive', 'fallsky'),
				'section'	=> 'fallsky_section_woocommerce',
				'settings'	=> 'fallsky_woocommerce_archive_group',
				'children'	=> array(
					new Fallsky_Customize_Control($wp_customize, 'fallsky_woocommerce_archive_layout', array(
						'type'		=> 'select',
						'label' 	=> esc_html__('Products Layout', 'fallsky'),
						'section' => 'fallsky_section_woocommerce',
						'settings' => 'fallsky_woocommerce_archive_layout',
						'choices' 	=> array(
							'2' => esc_html__('Grid 2 Columns', 'fallsky'),
							'3' => esc_html__('Grid 3 Columns', 'fallsky'),
							'4' => esc_html__('Grid 4 Columns', 'fallsky')
						)
					)),
					new Fallsky_Customize_Control($wp_customize, 'fallsky_woocommerce_archive_style', array(
						'type'		=> 'select',
						'label' 	=> esc_html__('Products Style', 'fallsky'),
						'section' 	=> 'fallsky_section_woocommerce',
						'settings' 	=> 'fallsky_woocommerce_archive_style',
						'choices' 	=> array(
							''	 		=> esc_html__('Normal', 'fallsky'),
							'overlay' 	=> esc_html__('Overlay', 'fallsky')
						)
					)),
					new Fallsky_Customize_Control($wp_customize, 'fallsky_woocommerce_archive_overlay_color_scheme', array(
						'type' 				=> 'radio',
						'label' 			=> esc_html__('Overlay Color Scheme', 'fallsky'),
						'section'			=> 'fallsky_section_woocommerce',
						'settings'			=> 'fallsky_woocommerce_archive_overlay_color_scheme',
						'active_callback'	=> 'fallsky_customize_control_active_cb',
						'choices' 			=> array(
							'overlay-light-color' 	=> esc_html__('Light', 'fallsky'),
							'overlay-dark-color'	=> esc_html__('Dark', 'fallsky')
						)
					)),
					new Fallsky_Customize_Control($wp_customize, 'fallsky_woocommerce_products_per_page', array(
						'type' 			=> 'number',
						'label' 		=> esc_html__('Show at most x products per page', 'fallsky'),
						'section' 		=> 'fallsky_section_woocommerce',
						'settings' 		=> 'fallsky_woocommerce_products_per_page',
						'input_attrs'	=> array('min' => 1)
					)),
					new Fallsky_Customize_Control($wp_customize, 'fallsky_woocommerce_archive_show_title', array(
						'type' 			=> 'checkbox',
						'label_first'	=> true,
						'label' 		=> esc_html__('Show Title', 'fallsky'),
						'section' 		=> 'fallsky_section_woocommerce',
						'settings' 		=> 'fallsky_woocommerce_archive_show_title'
					)),
					new Fallsky_Customize_Control($wp_customize, 'fallsky_woocommerce_archive_show_price', array(
						'type' 			=> 'checkbox',
						'label_first'	=> true,
						'label' 		=> esc_html__('Show Price', 'fallsky'),
						'section' 		=> 'fallsky_section_woocommerce',
						'settings' 		=> 'fallsky_woocommerce_archive_show_price'
					)),
					new Fallsky_Customize_Control($wp_customize, 'fallsky_woocommerce_archive_show_rating', array(
						'type' 			=> 'checkbox',
						'label_first'	=> true,
						'label' 		=> esc_html__('Show Rating', 'fallsky'),
						'section' 		=> 'fallsky_section_woocommerce',
						'settings' 		=> 'fallsky_woocommerce_archive_show_rating'
					)),
					new Fallsky_Customize_Control($wp_customize, 'fallsky_woocommerce_archive_show_sale', array(
						'type' 			=> 'checkbox',
						'label_first'	=> true,
						'label' 		=> esc_html__('Show On Sale Label', 'fallsky'),
						'section' 		=> 'fallsky_section_woocommerce',
						'settings' 		=> 'fallsky_woocommerce_archive_show_sale'
					))
				)
			)));

			$wp_customize->remove_control('woocommerce_catalog_columns');
			$wp_customize->remove_control('woocommerce_catalog_rows');
		}
	}
	if(class_exists('WooCommerce')){
		new Fallsky_Customize_WooCommerce();
	}
}