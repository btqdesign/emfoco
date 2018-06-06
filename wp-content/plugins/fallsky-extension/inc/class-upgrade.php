<?php
/* 
 *************************************************************************************
 * Initial verison
 *		1. Initilize the post like view count for each post
 *************************************************************************************
 */
if(!class_exists('LoftOcean_Upgrade')){
	class LoftOcean_Upgrade{
		private $version = '';
		/**
		* If the previous verion if older than current version,
		*	do the upgrade and update theme version
		*/	
		public function __construct(){
			$this->version = FALLSKY_PLUGIN_VERSION;

			$old_version = get_option('fallsky_extension_version', '0.1');
			if(version_compare($old_version, $this->version, '<')){
				if(version_compare($old_version, '1.0.0', '<')){
					$this->init_like_view_count();
				}
				$this->update_version();
			}
		}
		private function init_like_view_count(){
			$all = new WP_Query(array('fields' => 'ids', 'posts_per_page' => -1, 'post_type' => 'post'));
			if($all->have_posts()){
				while($all->have_posts()){
					$all->the_post();
					$pid 	= get_the_ID();
					$view 	= get_post_meta($pid, 'loftocean-view-count', true);
					$like 	= get_post_meta($pid, 'loftocean-like-count', true);
					if(empty($view)) update_post_meta($pid, 'loftocean-view-count', 0);
					if(empty($like)) update_post_meta($pid, 'loftocean-like-count', 0);
				}
				$all->wp_reset_postdata();
			}
		}
		private function update_version(){
			update_option('fallsky_extension_version', $this->version);
		}
	}
	new LoftOcean_Upgrade();
}