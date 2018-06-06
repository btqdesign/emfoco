<?php
/**
* Attachment related frontend render class.
*/

if(!class_exists('Fallsky_Attachment_Render')){
	class Fallsky_Attachment_Render {
		public function __construct(){
			add_filter('the_content', 			array($this, 'main_content'));
			add_filter('fallsky_page_layout', 	array($this, 'page_layout'), 999);
			add_filter('body_class', 			array($this, 'body_class'), 999);
		}
		/*
		* Always return empty string for attachment page
		*/
		public function page_layout($layout){
			return '';
		}
		/**
		* Print attachment main content
		*/
		public function main_content($content){
			$title 		= get_the_title();
			$caption 	= wp_get_attachment_caption(get_the_ID());
			if(!empty($title)){
				$content = sprintf(
					'<header class="post-header">%s</header>%s',
					the_title('<h1 class="post-title">', '</h1>', false),
					$content
				);
			}
			if(!empty($caption)){
				$content .= sprintf('<p class="wp-caption-text">%s</p>', $caption);
			}

			if(current_user_can('edit_post', get_the_ID())){
				ob_start();
				fallsky_page_edit_link();
				$content .= ob_get_clean();
			}

			return $content;
		}
		/**
		* Add class name for attachment
		*/
		public function body_class($class){
			return array_merge($class, array('single-attachment', 'single'));
		}
	}
	new Fallsky_Attachment_Render();
}