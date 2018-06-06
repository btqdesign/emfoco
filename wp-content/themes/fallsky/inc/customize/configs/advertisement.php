<?php
/**
* Customize section advertisement configuration files.
*/


if(!class_exists('Fallsky_Customize_Advertisement')){
	class Fallsky_Customize_Advertisement extends Fallsky_Customize_Base {
		private $sections 				= array();
		private $section_ids 			= array();
		private $current_ad_id 			= false;
		private $render_ads 			= false;
		private static $filter_added 	= false;
		public function __construct(){
			$this->sections = array(
				'site_top' 						=> esc_html__('Site Top', 'fallsky'),
				'before_single_post_content' 	=> esc_html__('Before Single Post Content', 'fallsky'),
				'after_single_post_content'		=> esc_html__('After Single Post Content', 'fallsky'),
				'before_single_page_content'	=> esc_html__('Before Single Page Content', 'fallsky'),
				'after_single_page_content'		=> esc_html__('After Single Page Content', 'fallsky')
			);
			$this->section_ids = array_keys($this->sections);
			parent::__construct();
		}
		public function register_controls($wp_customize){
			global $fallsky_default_settings;

			// Panel
			$wp_customize->add_panel(new WP_Customize_Panel($wp_customize, 'fallsky_panel_advertisement', array(
				'title' 	=> esc_html__('Advertisement', 'fallsky'),
				'priority' 	=> 65
			)));
			
			foreach($this->sections as $sid => $stitle){
				// Sections
				$wp_customize->add_section('fallsky_section_advertisement_' . $sid, array(
					'title' => $stitle,
					'panel' => 'fallsky_panel_advertisement'
				));


				// Settings
				$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_ads_source_' . $sid, array(
					'default'   		=> $fallsky_default_settings['fallsky_ads_source_' . $sid],
					'transport' 		=> 'refresh',
					'sanitize_callback' => 'fallsky_sanitize_choice'
				)));
				$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_ads_custom_url_' . $sid, array(
					'default'   		=> $fallsky_default_settings['fallsky_ads_custom_url_' . $sid],
					'transport' 		=> 'refresh',
					'sanitize_callback' => 'esc_url_raw',
					'dependency' 		=> array(
						'fallsky_ads_source_' . $sid => array('value' => array('custom'))
					)
				)));
				$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_ads_custom_image_' . $sid, array(
					'default'   		=> $fallsky_default_settings['fallsky_ads_custom_image_' . $sid],
					'transport' 		=> 'refresh',
					'sanitize_callback' => 'absint',
					'dependency' 		=> array(
						'fallsky_ads_source_' . $sid => array('value' => array('custom'))
					)
				)));
				$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_ads_custom_image_width_' . $sid, array(
					'default'   		=> $fallsky_default_settings['fallsky_ads_custom_image_width_' . $sid],
					'transport' 		=> 'refresh',
					'sanitize_callback' => 'absint',
					'dependency' 		=> array(
						'fallsky_ads_source_' . $sid 		=> array('value' => array('custom')),
						'fallsky_ads_custom_image_' . $sid 	=> array('value' => array('', '0', 0), 'operator' => 'not in')
					)
				)));
				$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_ads_custom_new_tab_' . $sid, array(
					'default'   		=> $fallsky_default_settings['fallsky_ads_custom_new_tab_' . $sid],
					'transport' 		=> 'refresh',
					'sanitize_callback' => 'fallsky_sanitize_checkbox',
					'dependency' 		=> array(
						'fallsky_ads_source_' . $sid => array('value' => array('custom'))
					)
				)));
				$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_ads_embed_code_' . $sid, array(
					'default'   		=> $fallsky_default_settings['fallsky_ads_embed_code_' . $sid],
					'transport' 		=> 'refresh',
					'sanitize_callback' => 'fallsky_sanitize_html',
					'dependency' 		=> array(
						'fallsky_ads_source_' . $sid => array('value' => array('embed'))
					)
				)));

				// Controls
				$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_ads_source_' . $sid, array(
					'type' 		=> 'select',
					'label' 	=> esc_html__('Ad source', 'fallsky'),
					'section' 	=> 'fallsky_section_advertisement_' . $sid,
					'settings' 	=> 'fallsky_ads_source_' . $sid,
					'choices' 	=> array(
						'custom' 	=> esc_html__('Custom Banner', 'fallsky'),
						'embed' 	=> esc_html__('Embed Code', 'fallsky')
					)
				)));
				$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_ads_custom_url_' . $sid, array(
					'type' 				=> 'text',
					'label' 			=> esc_html__('URL', 'fallsky'),
					'section' 			=> 'fallsky_section_advertisement_' . $sid,
					'settings' 			=> 'fallsky_ads_custom_url_' . $sid,
					'active_callback' 	=> 'fallsky_customize_control_active_cb'
				)));
				$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_ads_custom_image_' . $sid, array(
					'type' 				=> 'image_id',
					'label' 			=> esc_html__('Ad image', 'fallsky'),
					'section' 			=> 'fallsky_section_advertisement_' . $sid,
					'settings' 			=> 'fallsky_ads_custom_image_' . $sid,
					'active_callback' 	=> 'fallsky_customize_control_active_cb'
				)));
				$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_ads_custom_image_width_' . $sid, array(
					'type' 				=> 'number_with_unit',
					'label' 			=> esc_html__('Ad image width', 'fallsky'),
					'section' 			=> 'fallsky_section_advertisement_' . $sid,
					'settings' 			=> 'fallsky_ads_custom_image_width_' . $sid,
					'active_callback' 	=> 'fallsky_customize_control_active_cb'
				)));
				$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_ads_custom_new_tab_' . $sid, array(
					'type' 				=> 'checkbox',
					'label' 			=> esc_html__('Open link in a new tab', 'fallsky'),
					'label_first'		=> true,
					'section' 			=> 'fallsky_section_advertisement_' . $sid,
					'settings' 			=> 'fallsky_ads_custom_new_tab_' . $sid,
					'active_callback' 	=> 'fallsky_customize_control_active_cb'
				)));
				$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_ads_embed_code_' . $sid, array(
					'type' 				=> 'textarea',
					'label' 			=> esc_html__('Embed code', 'fallsky'),
					'section' 			=> 'fallsky_section_advertisement_' . $sid,
					'settings' 			=> 'fallsky_ads_embed_code_' . $sid,
					'active_callback'	=> 'fallsky_customize_control_active_cb'
				)));
			}
		}
		public function frontend_actions(){
			add_action( 'fallsky_ads', array( $this, 'show_ads' ) );
			if( !self::$filter_added ) {
				add_filter( 'wp_get_attachment_image_src', array( $this, 'customize_image_size' ), 999, 4 );
				self::$filter_added = true;
			}
		}
		public function show_ads( $id ) { 
			$ids = array_keys( $this->sections ); 
			if( in_array( $id, $ids ) ) {
				$source 	= fallsky_get_theme_mod( 'fallsky_ads_source_' . $id ); 
				$content 	= '';
				if( 'custom' == $source ) {
					$url 		= fallsky_get_theme_mod( 'fallsky_ads_custom_url_' . $id );
					$image_id 	= fallsky_get_theme_mod( 'fallsky_ads_custom_image_' . $id );
					$target 	= fallsky_module_enabled( 'fallsky_ads_custom_new_tab_' . $id );
					$image_src	= empty( $image_id ) ? '' : fallsky_get_image_src( $image_id, 'full' );
					if( !empty( $image_src ) ) {
						$this->current_ad_id 	= $id;
						$this->render_ads 		= true;
						$image 	= wp_get_attachment_image($image_id, 'full', false, array('class' => '', 'alt' => fallsky_get_image_alt($image_id)));
						$wrap 	= empty( $url ) ? '%s' : sprintf( '<a href="%s"%s>%s</a>', esc_url( $url ), ( $target ? ' target="_blank"' : '' ), '%s' );
						$content = sprintf( $wrap, preg_replace( '/(width="\d+"|height="\d+")/i', '', $image ) );
						$this->render_ads 		= false;
						$this->current_ad_id 	= '';
					}
				}
				else{
					$content = fallsky_get_theme_mod( 'fallsky_ads_embed_code_' . $id );
				}
				if( !empty( $content ) ) {
					printf(
						'<div class="%s">%s</div>',
						'site_top' == $id ? 'sitetop-ad hide' : 'ad-banner', 
						$content
					);
				}
			}
		}
		public function customize_image_size($image, $id, $size, $icon){
			if($this->render_ads && !empty($this->current_ad_id)){
				$width = intval(fallsky_get_theme_mod('fallsky_ads_custom_image_width_' . $this->current_ad_id));
				if(!empty($image[1])){
					$image[2] = $width / $image[1] * $image[2];
					$image[1] = $width;
				}
			}
			return $image;
		}
	}
	new Fallsky_Customize_Advertisement();
}