<?php
/**
* Theme upgrader class
*	To update the settings for update between versions
*/
if(!class_exists('Fallsky_Upgrader')){
	class Fallsky_Upgrader {
		private $version = '';
		/**
		* If the previous verion if older than current version,
		*	do the upgrade and update theme version
		*/
		function __construct(){
			$this->version = FALLSKY_THEME_VERSION;

			$old_version = get_theme_mod('theme-version', '0.1');
			if(version_compare($old_version, $this->version, '<')){
				if(version_compare($old_version, '1.0.0', '<')){
					$this->initial_settings();
				}
				$this->update_version();
			}
		}
		/**
		* Initial settings
		*/
		private function initial_settings(){
			$post_widgets 	= get_option('widget_fallsky-homepage-widget-posts');
			$homepage_area 	= get_theme_mod('fallsky_homepage_main_area');
			$post_widgets 	= empty($post_widgets) || is_array($post_widgets) 
				? array() : array_diff_key($post_widgets, array('_multiwidget' => 1));
			if(empty($homepage_area) && empty($post_widgets)){
				$post_widgets = array(
					'_multiwidget' 	=> 1,
					'3' => array(
						'title' 				=> esc_html__('Latest Articles', 'fallsky'),
						'title-align' 			=> 'on',
						'color' 				=> 'custom',
						'custom-color-scheme' 	=> 'light-color',
						'custom-bg-color' 		=> '#f7f9f9',
						'padding-top' 			=> 50,
						'padding-bottom' 		=> 70,
						'filter-by' 			=> 'latest',
						'category' 				=> '',
						'post-format' 			=> 'standard',
						'layout' 				=> 'masonry',
						'list-column' 			=> 1,
						'masonry-column' 		=> 3,
						'card-column' 			=> 1,
						'card-color'			=> 'light-card',
						'grid-column' 			=> 2,
						'overlay-column' 		=> 1,
						'overlay-mix-column' 	=> '1-2-mix',
						'show-post-excerpt' 	=> 'on',
						'show-read-more' 		=> 'on',
						'center-text'			=> '',
						'post-meta-title' 		=> '',
						'post-meta-category' 	=> 'on',
						'post-meta-author' 		=> 'on',
						'post-meta-date' 		=> 'on',
						'post-meta-view' 		=> '',
						'post-meta-like' 		=> '',
						'post-meta-comment' 	=> '',
						'number' 				=> get_option('posts_per_page', 10),
						'pagination' 			=> 'on',
						'fullwidth' 			=> ''
					)
				);
				
				$homepage_area = array('fallsky-homepage-widget-posts-3');
				update_option('widget_fallsky-homepage-widget-posts', $post_widgets);
				set_theme_mod('fallsky_homepage_main_area', $homepage_area);
			}
		}
		/**
		* @description update version number to db
		*/
		private function update_version(){
			set_theme_mod('theme-version', $this->version);
		}
	}
	add_action('after_setup_theme', function(){ new Fallsky_Upgrader(); });
}