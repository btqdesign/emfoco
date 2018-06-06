<?php
/**
* Customize section search screen configuration files.
*/

if(!class_exists('Fallsky_Customize_Search_Screen')){
	class Fallsky_Customize_Search_Screen extends Fallsky_Customize_Base {
		public function __construct(){
			parent::__construct();

			add_action('wp_ajax_fallsky_search', array($this, 'ajax_search'));
			add_action('wp_ajax_nopriv_fallsky_search', array($this, 'ajax_search'));
		}
		public function register_controls($wp_customize){
			global $fallsky_default_settings;

			// // Panel
			$wp_customize->add_panel('fallsky_panel_search_screen', array(
				'title'    => esc_html__('Search Screen', 'fallsky'),
				'priority' => 20
			));
			// Sections
			$wp_customize->add_section('fallsky_section_search_screen_content', array(
				'title' => esc_html__('Content Options', 'fallsky'),
				'panel' => 'fallsky_panel_search_screen'
			));
			$wp_customize->add_section('fallsky_section_search_screen_design', array(
				'title' => esc_html__('Design Options', 'fallsky'),
				'panel' => 'fallsky_panel_search_screen'
			));

			// Settings
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_search_screen_notes', array(
				'default'			=> '',
				'transport'			=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_empty'
			)));

			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_search_show_category', array(
				'default'			=> $fallsky_default_settings['fallsky_search_show_category'],
				'transport'			=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_checkbox'
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_search_show_category_count', array(
				'default'   		=> $fallsky_default_settings['fallsky_search_show_category_count'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_checkbox',
				'dependency'		=> array(
					'fallsky_search_show_category' => array('value' => array('on'))
				)
			)));

			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_search_bg_color', array(
				'default'   		=> $fallsky_default_settings['fallsky_search_bg_color'],
				'transport'			=> 'postMessage',
				'sanitize_callback' => 'sanitize_hex_color'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_search_text_color', array(
				'default'   		=> $fallsky_default_settings['fallsky_search_text_color'],
				'transport'			=> 'postMessage',
				'sanitize_callback' => 'sanitize_hex_color'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_search_bg_image', array(
				'default'   		=> $fallsky_default_settings['fallsky_search_bg_image'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'absint'
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_search_bg_size', array(
				'default'			=> $fallsky_default_settings['fallsky_search_bg_size'],
				'transport'			=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_choice',
				'dependency' 		=> array(
					'fallsky_search_bg_image' => array('value' => array(''), 'operator' => 'not in')
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_search_bg_repeat', array(
				'default'   		=> $fallsky_default_settings['fallsky_search_bg_repeat'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_checkbox',
				'dependency' 		=> array(
					'fallsky_search_bg_image' => array('value' => array(''), 'operator' => 'not in')
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_search_bg_position_x', array(
				'default'			=> $fallsky_default_settings['fallsky_search_bg_position_x'],
				'transport'			=> 'postMessage',
				'sanitize_callback' => 'sanitize_text_field',
				'dependency' 		=> array(
					'fallsky_search_bg_image' => array('value' => array(''), 'operator' => 'not in')
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_search_bg_position_y', array(
				'default'  			=> $fallsky_default_settings['fallsky_search_bg_position_y'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'sanitize_text_field',
				'dependency' 		=> array(
					'fallsky_search_bg_image' => array('value' => array(''), 'operator' => 'not in')
				)
			)));

			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_search_enable_overlay', array(
				'default'  			=> $fallsky_default_settings['fallsky_search_enable_overlay'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_checkbox',
				'dependency' 		=> array(
					'fallsky_search_bg_image' => array('value' => array(''), 'operator' => 'not in')
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_search_overlay_color', array(
				'default'  			=> $fallsky_default_settings['fallsky_search_overlay_color'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'sanitize_hex_color',
				'dependency' 		=> array(
					'fallsky_search_bg_image' 		=> array('value' => array('',), 'operator' => 'not in'),
					'fallsky_search_enable_overlay' => array('value' => array('on'))
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_search_overlay_opacity', array(
				'default'  			=> $fallsky_default_settings['fallsky_search_overlay_opacity'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'absint',
				'dependency' 		=> array(
					'fallsky_search_bg_image' 		=> array('value' => array('',), 'operator' => 'not in'),
					'fallsky_search_enable_overlay' => array('value' => array('on'))
				)
			)));

			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_search_no_border', array(
				'default'			=> $fallsky_default_settings['fallsky_search_no_border'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_checkbox'
			)));

			// Controls
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_search_screen_content_options_notes', array(
				'type' 			=> 'notes',
				'description' 	=> esc_html__('Click on the search button in site header to preview', 'fallsky'),
				'section' 		=> 'fallsky_section_search_screen_content',
				'settings' 		=> 'fallsky_search_screen_notes'
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_search_show_category', array(
				'type' 			=> 'checkbox',
				'label_first'	=> true,
				'label' 		=> esc_html__('Display Categories', 'fallsky'),
				'section' 		=> 'fallsky_section_search_screen_content',
				'settings' 		=> 'fallsky_search_show_category'
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_search_show_category_count', array(
				'type' 			=> 'checkbox',
				'label_first'	=> true,
				'label' 		=> esc_html__('Display Post Counts', 'fallsky'),
				'section' 		=> 'fallsky_section_search_screen_content',
				'settings' 		=> 'fallsky_search_show_category_count'
			)));

			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_search_screen_design_options_notes', array(
				'type' 			=> 'notes',
				'description' 	=> esc_html__('Click on the search button in site header to preview', 'fallsky'),
				'section' 		=> 'fallsky_section_search_screen_design',
				'settings' 		=> 'fallsky_search_screen_notes'
			)));
			$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'fallsky_search_bg_color', array(
				'label' 	=> esc_html__('Background Color', 'fallsky'),
				'section' 	=> 'fallsky_section_search_screen_design',
				'settings' 	=> 'fallsky_search_bg_color'
			)));
			$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'fallsky_site_header_text_color', array(
				'label' 	=> esc_html__('Text Color', 'fallsky'),
				'section' 	=> 'fallsky_section_search_screen_design',
				'settings' 	=> 'fallsky_search_text_color'
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_search_bg_image', array(
				'type' 		=> 'image_id',
				'label' 	=> esc_html__('Background Image', 'fallsky'),
				'section' 	=> 'fallsky_section_search_screen_design',
				'settings' 	=> 'fallsky_search_bg_image'
			)));
			$wp_customize->add_control(new WP_Customize_Background_Position_Control($wp_customize, 'fallsky_search_bg_position', array(
				'label' 			=> esc_html__('Image Position', 'fallsky'),
				'section'			=> 'fallsky_section_search_screen_design',
				'active_callback' 	=> 'fallsky_customize_control_active_cb',
				'settings' 			=> array(
					'x' => 'fallsky_search_bg_position_x',
					'y' => 'fallsky_search_bg_position_y'
				)
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_search_bg_size', array(
				'type' 				=> 'select',
				'label' 			=> esc_html__('Size', 'fallsky'),
				'section' 			=> 'fallsky_section_search_screen_design',
				'settings' 			=> 'fallsky_search_bg_size',
				'active_callback' 	=> 'fallsky_customize_control_active_cb',
				'choices' 			=> array(
					'auto' 		=> esc_html__('Original', 'fallsky'),
					'contain' 	=> esc_html__('Fit to Screen', 'fallsky'),
					'cover'		=> esc_html__('Fill Screen', 'fallsky')
				)
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_search_bg_repeat', array(
				'type' 				=> 'checkbox',
				'label' 			=> esc_html__('Repeat', 'fallsky'),
				'section' 			=> 'fallsky_section_search_screen_design',
				'settings' 			=> 'fallsky_search_bg_repeat',
				'active_callback' 	=> 'fallsky_customize_control_active_cb'
			)));

			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_search_enable_overlay', array(
				'type' 					=> 'checkbox',
				'label_first'		 	=> true,
				'label' 				=> esc_html__('Add an overlay', 'fallsky'),
				'section' 				=> 'fallsky_section_search_screen_design',
				'settings' 				=> 'fallsky_search_enable_overlay',
				'active_callback' 		=> 'fallsky_customize_control_active_cb'
			)));
			$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'fallsky_search_overlay_color', array(
				'label' 			=> esc_html__('Overlay color', 'fallsky'),
				'section'  			=> 'fallsky_section_search_screen_design',
				'settings' 			=> 'fallsky_search_overlay_color',
				'active_callback' 	=> 'fallsky_customize_control_active_cb'
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_search_overlay_opacity', array(
				'type' 				=> 'number_slider',
				'label' 			=> esc_html__('Overlay opacity', 'fallsky'),
				'section' 			=> 'fallsky_section_search_screen_design',
				'settings' 			=> 'fallsky_search_overlay_opacity',
				'active_callback' 	=> 'fallsky_customize_control_active_cb',
				'after_text'		=> esc_html__('%', 'fallsky'),
				'input_attrs'		=> array(
					'data-min'	=> '0',
					'data-max'	=> '100',
					'data-step'	=> '1'
				)
			)));

			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_search_no_border', array(
				'type' 			=> 'checkbox',
				'label_first'	=> true,
				'label' 		=> esc_html__('Remove border', 'fallsky'),
				'section' 		=> 'fallsky_section_search_screen_design',
				'settings' 		=> 'fallsky_search_no_border'
			)));
		}
		public function frontend_js_vars($vars = array()){
			$vars['ajax_search'] = array(
				'url' 		=> esc_js(admin_url('admin-ajax.php')),
				'action' 	=> 'fallsky_search'
			);
			return $vars;
		}
		protected function get_custom_styles(){
			global $fallsky_default_settings, $fallsky_is_preview;
			$styles = array();

			$styles['search-screen-bg-color'] = fallsky_get_style(
				'fallsky_search_bg_color',
				'.search-screen .fullscreen-bg',
				'background-color: %s;'
			);
			$styles['search-screen-text-color'] = fallsky_get_style(
				'fallsky_search_text_color',
				'.search-screen .container',
				'color: %s;'
			);
			// Background Image for ".search-screen > .container"
			$bg_image_id = absint(fallsky_get_theme_mod('fallsky_search_bg_image'));
			if( !empty($bg_image_id) || $fallsky_is_preview ) {
				$image = empty( $bg_image_id ) ? false : fallsky_get_image_src( $bg_image_id, 'fallsky_large', false );
				if($image || $fallsky_is_preview){
					$position_x 	= esc_attr( fallsky_get_theme_mod( 'fallsky_search_bg_position_x' ) );
					$position_y 	= esc_attr( fallsky_get_theme_mod( 'fallsky_search_bg_position_y' ) );
					$size 			= esc_attr( fallsky_get_theme_mod( 'fallsky_search_bg_size' ) );
					$repeat 		= fallsky_module_enabled( 'fallsky_search_bg_repeat' );

					if( !empty($image ) ) {
						$styles['search-screen-bg-image'] = sprintf(
							'.search-screen .fullscreen-bg { %s }',
							sprintf('background-image: url(%s);', esc_url_raw($image))
						);
					}
					$styles['search-screen-bg-image-size'] = sprintf(
						'.search-screen .fullscreen-bg { %s }',
						sprintf('background-size: %s;', $size)
					);
					$styles['search-screen-bg-image-repeat'] = sprintf(
						'.search-screen .fullscreen-bg { %s }',
						sprintf('background-repeat: %s;', ($repeat ? 'repeat' : 'no-repeat'))
					);
					$styles['search-screen-bg-image-position-x'] = sprintf(
						'.search-screen .fullscreen-bg { %s }',
						sprintf('background-position-x: %s;', $position_x)
					);
					$styles['search-screen-bg-image-position-y'] = sprintf(
						'.search-screen .fullscreen-bg { %s }',
						sprintf('background-position-y: %s;', $position_y)
					);

					$enable_overlay 	= fallsky_module_enabled('fallsky_search_enable_overlay');
					$overlay_opacity 	= absint(fallsky_get_theme_mod('fallsky_search_overlay_opacity'));
					if($enable_overlay || $fallsky_is_preview){
						$styles['search-overlay-color'] = fallsky_get_style(
							'fallsky_search_overlay_color',
							'.search-screen.has-overlay .fullscreen-bg:after',
							'background: %s;'
						);
						if($overlay_opacity != $fallsky_default_settings['fallsky_search_overlay_opacity']){
							$styles['search-overlay-opacity'] = sprintf(
								'.search-screen.has-overlay .fullscreen-bg:after { %s }',
								sprintf('opacity: %s;', $overlay_opacity / 100)
							);
						}
					}
				}
			}

			return $styles;
		}
		public function frontend_actions(){
			add_action( 'wp_footer', array($this, 'search_screen' ), 1 );
		}
		public function ajax_search(){
			$query 	= new WP_Query(array(
				's' 					=> $_POST['s'], 
				'offset' 				=> 0, 
				'post_type' 			=> 'post', 
				'post_status' 			=> 'publish',
				'posts_per_page'		=> 3, 
				'ignore_sticky_posts' 	=> 1
			));

			$html_posts = sprintf('<h4 class="title">%s</h4>', esc_html__('Search Results', 'fallsky'));
			if($query->have_posts()){
				$html_posts .= '<ul class="results-list">';
				do_action('fallsky_set_frontend_options', 'ajax_search_result');
				while($query->have_posts()){
					$query->the_post();
					$thumbnail_class 	= '';
					$thumbnail_html		= '';
					$post_url			= get_permalink();

					if(has_post_thumbnail()){
						$thumbnail_class = ' has-post-thumbnail';
						$thumbnail_html	 = sprintf(
							'<figure class="featured-img">%s</figure>',
							fallsky_get_preload_bg( array(
								'tag' 	=> 'a', 
								'attrs' => array( 'href' => $post_url ), 
								'id' 	=> get_post_thumbnail_id()
							), true )
						);
					}
					$html_posts .= sprintf(
						'<li><div class="post%s">%s<div class="post-content">%s</div></div></li>',
						$thumbnail_class,
						$thumbnail_html,
						sprintf(
							'<div class="post-header"><p class="post-title"><a href="%s">%s</a></p></div>%s',
							$post_url,
							get_the_title(),
							fallsky_meta_date(false)
						)
					);
				}
				$html_posts .= '</ul>';
				do_action('fallsky_reset_frontend_options');
			}
			else{
				$html_posts .= sprintf(
					'<div class="ajax-search-no-result"><p>%s</p></div>',
					esc_html__('Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'fallsky')
				);
			}
			wp_send_json_success($html_posts);
		}
		public function search_screen(){
			global $fallsky_is_preview;

			$class 					= array('search-screen');
			$show_category 			= fallsky_module_enabled('fallsky_search_show_category');
			$show_category_count 	= fallsky_module_enabled('fallsky_search_show_category_count');
			$category_hide			= $fallsky_is_preview && !$show_category ? ' hide' : '';
			$category_count_hide	= $fallsky_is_preview && !$show_category_count ? ' hide' : '';
			$categories 			= get_terms(array('taxonomy' => 'category'));
			// Search screen html wrap
			$html_wrap 	= '<div class="%s"><div class="fullscreen-bg"></div><div class="container">%s</div></div>';
			$content 	= sprintf('<span class="close-button">%s</span>', esc_html__('Close', 'fallsky'));
			$content   .= sprintf(
				'<div class="search">%s%s</div>',
				get_search_form(false),
				sprintf('<span class="hint">%s</span>', esc_html__('Press Enter Key to see all results', 'fallsky'))
			);
			if(fallsky_get_theme_mod('fallsky_search_no_border')){
				array_push($class, 'no-border');
			}
			$bg_image_id = absint( fallsky_get_theme_mod( 'fallsky_search_bg_image' ) );
			if( !empty( $bg_image_id ) ) {
				$background_image 	= empty( $bg_image_id ) ? false : fallsky_get_image_src( $bg_image_id, 'fallsky_large', false );
				$enable_overlay 	= fallsky_module_enabled( 'fallsky_search_enable_overlay' );
				if( $background_image && $enable_overlay ) {
					array_push( $class, 'has-overlay' );
				}
			}
			if( !is_wp_error( $categories ) && ( $show_category || $fallsky_is_preview ) ) {
				$list_wrap	= '<div class="shortcuts-cat%s"><ul>%s</ul></div>';
				$item_wrap 	= '<li><a href="%s" title="%s"><span class="category-name">%s</span>%s</a></li>';
				$count_wrap = ' <span class="counts%s">%d</span>';
				$html_list 	= '';
				foreach( $categories as $t ) {
					$html_list .= sprintf(
						$item_wrap,
						get_term_link( $t ), 
						esc_attr( $t->name ),
						esc_html( $t->name ),
						$show_category_count || $fallsky_is_preview ? sprintf( $count_wrap, $category_count_hide, $t->count ) : ''
					);
				}
				$content .= sprintf($list_wrap, $category_hide, $html_list);
			}
			$content .= '<div class="search-results hide"></div>';
			printf($html_wrap, implode(' ', $class), $content);
		}
	}
	new Fallsky_Customize_Search_Screen();
}