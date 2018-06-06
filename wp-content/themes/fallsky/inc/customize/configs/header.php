<?php
/**
* Customize section header configuration files.
*/

if(!class_exists('Fallsky_Customize_Header')){
	class Fallsky_Customize_Header extends Fallsky_Customize_Base {
		private $with_hamburger = array();
		private $with_main_menu	= array();
		private $wrap_buttons	= array();
		public function __construct(){
			parent::__construct();
			$this->with_main_menu 	= array( 'site-header-layout-1', 'site-header-layout-5' );
			$this->with_hamburger 	= array( 'site-header-layout-1', 'site-header-layout-5', 'site-header-layout-6' );
			$this->wrap_buttons		= array( 'site-header-layout-3', 'site-header-layout-4', 'site-header-layout-5', 'site-header-layout-6' );
		}
		public function register_controls($wp_customize){
			global $fallsky_default_settings;

			// Panel
			$wp_customize->add_panel('fallsky_panel_site_header', array(
				'title'    => esc_html__('Site Header', 'fallsky'),
				'priority' => 10
			));

			// Sections
			$wp_customize->add_section('fallsky_section_header_layout', array(
				'title'	=> esc_html__('Header Layout', 'fallsky'),
				'panel' => 'fallsky_panel_site_header'
			));
			$wp_customize->add_section('fallsky_section_header_design_options', array(
				'title' => esc_html__('Design Options', 'fallsky'),
				'panel' => 'fallsky_panel_site_header'
			));
			$wp_customize->add_section( 'fallsky_section_header_transparent', array(
				'title' => esc_html__( 'Transparent Site Header', 'fallsky' ),
				'panel' => 'fallsky_panel_site_header'
			) );

			// Settings
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_site_header_layout', array(
				'default'   		=> $fallsky_default_settings['fallsky_site_header_layout'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			)));
			$wp_customize->selective_refresh->add_partial('fallsky_site_header_layout', array(
				'settings'				=> array('fallsky_site_header_layout'),
				'selector'				=> '#masthead',
				'render_callback'		=> array($this, 'site_header'),
				'container_inclusive' 	=> true
			));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_enable_hamburge_menu_button', array(
				'default'			=> $fallsky_default_settings['fallsky_enable_hamburge_menu_button'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_checkbox',
				'dependency'		=> array(
					'fallsky_site_header_layout' => array('value' => array( 'site-header-layout-1', 'site-header-layout-5', 'site-header-layout-6' ) )
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_hamburge_menu_button_style', array(
				'default'   		=> $fallsky_default_settings['fallsky_hamburge_menu_button_style'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_sticky_site_header', array(
				'default'			=> $fallsky_default_settings['fallsky_sticky_site_header'],
				'transport'			=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_no_space_between_site_header_and_content', array(
				'default'			=> $fallsky_default_settings['fallsky_no_space_between_site_header_and_content'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_checkbox'
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_search_group', array(
				'default'   		=> '',
				'sanitize_callback' => 'fallsky_sanitize_empty'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_show_search_button', array(
				'default'   		=> $fallsky_default_settings['fallsky_show_search_button'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_checkbox'
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_search_button_style', array(
				'default'  			 => $fallsky_default_settings['fallsky_search_button_style'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_choice',
				'dependency'		=> array(
					'fallsky_show_search_button' => array('value' => array('on'))
				)
			)));
			$wp_customize->add_setting(new Fallsky_Customize_Setting($wp_customize, 'fallsky_search_notes', array(
				'default'   		=> '',
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_empty',
				'dependency'		=> array(
					'fallsky_show_search_button' => array('value' => array('on'))
				)
			)));

			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_site_header_color_scheme', array(
				'default'   		=> $fallsky_default_settings['fallsky_site_header_color_scheme'],
				'transport' 		=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_choice'
			)));
			$wp_customize->add_setting(new WP_Customize_Setting($wp_customize, 'fallsky_site_header_bg_color', array(
				'default'   		=> $fallsky_default_settings['fallsky_site_header_bg_color'],
				'transport'			=> 'postMessage',
				'sanitize_callback' => 'sanitize_hex_color'
			)));

			$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_site_header_transparent_notes', array(
				'default'   		=> '',
				'transport'			=> 'postMessage',
				'sanitize_callback' => 'fallsky_sanitize_empty'
			) ) );
			$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'fallsky_home_transparent_site_header', array(
				'default'   		=> $fallsky_default_settings['fallsky_home_transparent_site_header'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_checkbox'
			) ) );
			$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_single_post_template1_transparent_site_header', array(
				'default'   		=> $fallsky_default_settings['fallsky_single_post_template1_transparent_site_header'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_checkbox'
			) ) );
			$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_single_page_layout2_transparent_site_header', array(
				'default'   		=> $fallsky_default_settings['fallsky_single_page_layout2_transparent_site_header'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_checkbox'
			) ) );
			$wp_customize->add_setting( new Fallsky_Customize_Setting( $wp_customize, 'fallsky_archive_pages_transparent_site_header', array(
				'default'   		=> $fallsky_default_settings['fallsky_archive_pages_transparent_site_header'],
				'transport' 		=> 'refresh',
				'sanitize_callback' => 'fallsky_sanitize_checkbox'
			)));

			// Controls
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_site_header_layout', array(
				'type' 		=> 'radio',
				'with_bg'	=> true,
				'wrap_id'	=> 'fallsky_header_style',
				'label' 	=> esc_html__('Site Header Layout', 'fallsky'),
				'section' 	=> 'fallsky_section_header_layout',
				'settings' 	=> 'fallsky_site_header_layout',
				'choices' 	=> array(
					'site-header-layout-1' => esc_html__('Header Layout 1', 'fallsky'),
					'site-header-layout-2' => esc_html__('Header Layout 2', 'fallsky'),
					'site-header-layout-3' => esc_html__('Header Layout 3', 'fallsky'),
					'site-header-layout-4' => esc_html__('Header Layout 4', 'fallsky'),
					'site-header-layout-5' => esc_html__('Header Layout 5', 'fallsky'),
					'site-header-layout-6' => esc_html__('Header Layout 6', 'fallsky')
				)
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_enable_hamburge_menu_button', array(
				'type' 				=> 'checkbox',
				'label_first'		=> true,
				'label' 			=> esc_html__('Also Show Hamburger Menu Button', 'fallsky'),
				'section' 			=> 'fallsky_section_header_layout',
				'settings' 			=> 'fallsky_enable_hamburge_menu_button',
				'active_callback'	=> 'fallsky_customize_control_active_cb'
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_hamburge_menu_button_style', array(
				'type' 			=> 'select',
				'label' 		=> esc_html__('Hamburger Menu Button Style', 'fallsky'),
				'section' 		=> 'fallsky_section_header_layout',
				'settings' 		=> 'fallsky_hamburge_menu_button_style',
				'choices' 		=> array(
					'' 			=> esc_html__('With Text', 'fallsky'),
					'icon-only' => esc_html__('Icon Only', 'fallsky')
				),
				'description'	=> sprintf(
					esc_html__('To edit the Fullscreen Menu\'s style, please go to %sFullscreen Menu%s section.', 'fallsky'),
					'<a href="#" class="show-panel" data-section-id="accordion-panel-fallsky_panel_menu">',
					'</a>'
				)
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_sticky_site_header', array(
				'type' 		=> 'select',
				'label' 	=> esc_html__('Sticky Header', 'fallsky'),
				'section' 	=> 'fallsky_section_header_layout',
				'settings' 	=> 'fallsky_sticky_site_header',
				'choices' 	=> array(
					''					=> esc_html__('No', 'fallsky'),
					'sticky'			=> esc_html__('Always sticky', 'fallsky'),
					'sticky-scroll-up'	=> esc_html__('Sticky when scroll up', 'fallsky')
				)
			))); 
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_no_space_between_site_header_and_content', array(
				'type' 			=> 'checkbox',
				'label_first'	=> true,
				'label' 		=> esc_html__('Remove the space between site header and main content ', 'fallsky'),
				'description'	=> esc_html__('Please don\'t tick this option when site header has a background image or a different background color from page.', 'fallsky'),
				'section' 		=> 'fallsky_section_header_layout',
				'settings' 		=> 'fallsky_no_space_between_site_header_and_content'
			)));
			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_search_group', array(
				'type' 		=> 'group',
				'label' 	=> esc_html__('Search', 'fallsky'),
				'section' 	=> 'fallsky_section_header_layout',
				'settings'	=> 'fallsky_search_group',
				'children'	=> array(
					new Fallsky_Customize_Control($wp_customize, 'fallsky_show_search_button', array(
						'type' 			=> 'checkbox',
						'label_first'	=> true,
						'label' 		=> esc_html__('Show Search Button', 'fallsky'),
						'section' 		=> 'fallsky_section_header_layout',
						'settings' 		=> 'fallsky_show_search_button'
					)),
					new Fallsky_Customize_Control($wp_customize, 'fallsky_search_button_style', array(
						'type' 				=> 'select',
						'label' 			=> esc_html__('Search Button Style', 'fallsky'),
						'section' 			=> 'fallsky_section_header_layout',
						'settings' 			=> 'fallsky_search_button_style',
						'active_callback'	=> 'fallsky_customize_control_active_cb',
						'choices'			=> array(
							'icon-only' => esc_html__('Icon', 'fallsky'), 
							'text-only' => esc_html__('Text', 'fallsky')
						)
					)),
					new Fallsky_Customize_Control($wp_customize, 'fallsky_search_notes', array(
						'type' 				=> 'notes',
						'section' 			=> 'fallsky_section_header_layout',
						'settings'			=> 'fallsky_search_notes',
						'active_callback'	=> 'fallsky_customize_control_active_cb',
						'description' 		=> sprintf(
							esc_html__('To edit the Search Screen\'s style, please go to %sSearch Screen%s section.', 'fallsky'),
							'<a href="#" class="show-panel" data-section-id="accordion-panel-fallsky_panel_search_screen">',
							'</a>'
						)
					))
				)
			)));

			$wp_customize->add_control(new Fallsky_Customize_Control($wp_customize, 'fallsky_site_header_color_scheme', array(
				'type' 		=> 'radio',
				'label' 	=> esc_html__('Color Scheme', 'fallsky'),
				'section' 	=> 'fallsky_section_header_design_options',
				'settings' 	=> 'fallsky_site_header_color_scheme',
				'choices' 	=> array(
					'site-header-color-light' 	=> esc_html__('Light', 'fallsky'),
					'site-header-color-dark' 	=> esc_html__('Dark', 'fallsky')
				)
			)));
			$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'fallsky_site_header_bg_color', array(
				'label' 	=> esc_html__('Background Color', 'fallsky'),
				'section' 	=> 'fallsky_section_header_design_options',
				'settings' 	=> 'fallsky_site_header_bg_color'
			)));

			$wp_customize->add_control(new Fallsky_Customize_Control( $wp_customize, 'fallsky_site_header_transparent_notes', array(
				'type' 			=> 'notes',
				'label' 		=> '',
				'description' 	=> esc_html__( 'In this section, you can choose to enable Transparent Absolute Header for different pages. When the option is enabled on a page, the site header background will be transparent, text in the site header will be white, and site header position will be absolute.', 'fallsky' ),
				'section' 		=> 'fallsky_section_header_transparent',
				'settings' 		=> 'fallsky_site_header_transparent_notes'
			)));
			$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_home_transparent_site_header', array(
				'type' 				=> 'checkbox',
				'label_first'	 	=> true,
				'label' 			=> esc_html__( 'On Home Page', 'fallsky' ),
				'description'		=> esc_html__( 'For best results, we recommend enabling it only when the first part of the home page is full width with background.', 'fallsky' ),
				'section' 			=> 'fallsky_section_header_transparent',
				'settings' 			=> 'fallsky_home_transparent_site_header',
			) ) );
			$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_single_post_template1_transparent_site_header', array(
				'type'			=> 'checkbox',
				'label_first'	=> true,
				'label'			=> esc_html__( 'Single Post - for Single Post Template 1', 'fallsky' ),
				'description' 	=> esc_html__( 'Only works when the post has a featured image.', 'fallsky' ),
				'section' 		=> 'fallsky_section_header_transparent',
				'settings' 		=> 'fallsky_single_post_template1_transparent_site_header'
			) ) );
			$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_single_page_layout2_transparent_site_header', array(
				'type'			=> 'checkbox',
				'label_first'	=> true,
				'label'			=> esc_html__( 'Single Page - for Page Header Layout 2', 'fallsky' ),
				'description' 	=> esc_html__( 'Only works when the page has a featured image.', 'fallsky' ),
				'section' 		=> 'fallsky_section_header_transparent',
				'settings' 		=> 'fallsky_single_page_layout2_transparent_site_header'
			) ) );
			$wp_customize->add_control( new Fallsky_Customize_Control( $wp_customize, 'fallsky_archive_pages_transparent_site_header', array(
				'type'			=> 'checkbox',
				'label_first'	=> true,
				'label'			=> esc_html__( 'Archive Pages', 'fallsky' ),
				'description' 	=> esc_html__( 'Including category/tag/shop/category index archive. Only works when the archive page has a page header background image.', 'fallsky' ),
				'section' 		=> 'fallsky_section_header_transparent',
				'settings' 		=> 'fallsky_archive_pages_transparent_site_header'
			) ) );

			$header = $wp_customize->get_control('header_image');
			if(!empty($header)){
				$header->section 	= 'fallsky_section_header_design_options';
				$header->priority 	= 1;

				$wp_customize->get_setting('header_image')->transport = 'postMessage';
				$wp_customize->get_setting('header_image_data')->transport = 'postMessage';
				$wp_customize->remove_section('header_image');
				$wp_customize->selective_refresh->remove_partial('custom_header');
				$wp_customize->selective_refresh->remove_partial('header_image');
			}
		}
		protected function get_custom_styles(){
			global $fallsky_default_settings;

			$list = array();
			$header_image_mod 	= get_theme_mod('header_image');
			$header_bg_color 	= fallsky_get_theme_mod('fallsky_site_header_bg_color');
			if(!empty($header_image_mod)){
				$header_image = '';
				switch($header_image_mod){
					case 'random-uploaded-image':
						$header_image = get_random_header_image();
						break;
					case 'remove-header':
						$header_image = '';
						break;
					default:
						$header_image = $header_image_mod;
				}

				if(!empty($header_image)){
					$list['site-header-bg-image'] = sprintf('.site-header { background-image: url(%s); }', $header_image);
				}
			}

			if($header_bg_color != $fallsky_default_settings['fallsky_site_header_bg_color']){
				$list['site-header-bg-color'] = sprintf('#page .site-header { background-color: %s; }', $header_bg_color);
			}

			return $list;
		}
		public function frontend_actions(){
			add_filter( 'fallsky_site_header_class', 	array( $this, 'site_header_class' ) );
			add_action( 'body_class', 					array( $this, 'body_class' ) );
			add_action( 'fallsky_site_header', 			array( $this, 'site_header' ) );
			add_action( 'fallsky_site_header_main_elements', array( $this, 'site_header_main_elements' ), 20 );
			add_filter( 'fallsky_transparent_archive_site_header', array( $this, 'is_archive_transparent_site_header') );
		}
		public function site_header_class($class){
			$style 				= esc_attr(fallsky_get_theme_mod('fallsky_site_header_layout'));
			$with_hamburger		= $this->with_hamburger;
			$hamburger_enabled  = fallsky_module_enabled('fallsky_enable_hamburge_menu_button');

			array_push($class, $style);
			array_push($class, esc_attr(fallsky_get_theme_mod('fallsky_site_header_color_scheme')));
			in_array($style, $with_hamburger) && $hamburger_enabled ? array_push($class, 'menu-btn-show') : '';

			return $class;
		}
		public function body_class($class){
			fallsky_module_enabled('fallsky_no_space_between_site_header_and_content') ? array_push($class, 'remove-page-top-space') : '';
			return $class;
		}
		public function site_header_main_elements(){
			global $fallsky_is_preview;

			$style 				= esc_attr( fallsky_get_theme_mod( 'fallsky_site_header_layout' ) );
			$with_main_menu 	= $this->with_main_menu;
			$wrap_buttons		= $this->wrap_buttons;
			$hamburger_style 	= esc_attr( fallsky_get_theme_mod('fallsky_hamburge_menu_button_style' ) );
			$show_search_button = fallsky_module_enabled( 'fallsky_show_search_button' );
			$wrap_div			= in_array( $style, $wrap_buttons ) ? '<div class="misc-wrapper">%s</div>' : '%s';

			printf(
				'<button id="menu-toggle" class="menu-toggle%s"><i class="icon_menu"></i>%s</button>',
				empty( $hamburger_style ) ? '' : sprintf( ' %s', $hamburger_style ),
				esc_html__( 'Menu', 'fallsky' )
			);

			// Show site main menu
			if( in_array( $style, $with_main_menu ) && has_nav_menu( 'primary' ) ) {
				fallsky_primary_nav();
			}

			ob_start();
			// Show shop cart icon in site header
			do_action( 'fallsky_cart_in_site_header' );
			// Show search button in site header
			if( $show_search_button || $fallsky_is_preview ) {
				$search_btn_style = esc_attr( fallsky_get_theme_mod( 'fallsky_search_button_style' ) );
				printf(
					'<div id="site-header-search" class="%s%s"><span class="search-button">%s</span></div>',
					$search_btn_style,
					!$show_search_button && $fallsky_is_preview ? ' hide' : '',
					( 'text-only' == $search_btn_style ) ? esc_html__( 'Search', 'fallsky' )
						: sprintf( esc_html__( '%sSearch%s', 'fallsky' ), '<span class="screen-reader-text">', '</span>' )
				);
			}
			$buttons = ob_get_clean();
			empty( $buttons ) ? '' : printf( $wrap_div, $buttons );
		}
		public function site_header() { 
			$layout = fallsky_get_theme_mod( 'fallsky_site_header_layout' ); ?>
			<header id="masthead"<?php fallsky_site_header_class(); ?> data-sticky="<?php echo fallsky_get_theme_mod('fallsky_sticky_site_header'); ?>"> 
				<?php ( 'site-header-layout-6' == $layout ) ? $this->header_style_6() : $this->header_others(); ?>
	        </header> <?php
		}
		private function header_style_6() { 
			if( has_nav_menu( 'secondary' ) ) {
				fallsky_secondary_nav(); 
			} ?>

			<div class="site-header-main">
				<div class="container">
					<?php do_action( 'fallsky_site_header_main_elements' ); ?>
				</div>
 			</div><!-- .site-header-main --> <?php
	
			if( has_nav_menu( 'primary' ) ) {
				fallsky_primary_nav();
			}
		}
		private function header_others() { ?>
			<div class="site-header-main">
				<div class="container">
					<?php do_action( 'fallsky_site_header_main_elements' ); ?>
				</div>
 			</div><!-- .site-header-main -->

			<?php /** Site secondary nav **/ ?>
			<?php if( has_nav_menu( 'secondary' ) ) { fallsky_secondary_nav(); } ?>
			<?php /** End of site secondary nav **/ 
		}
		public function customize_js_vars( $vars = array() ) {
			return array_merge( $vars, array(
				'header_label' => esc_html__( 'Header Background Image', 'fallsky' ),
				'header_description' => ''
			) );
		}
		public function is_archive_transparent_site_header( $is ) {
			return fallsky_module_enabled( 'fallsky_archive_pages_transparent_site_header' );
		}
	}
	new Fallsky_Customize_Header();
}