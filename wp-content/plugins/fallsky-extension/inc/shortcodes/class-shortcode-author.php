<?php
/**
* Shortcode Author
*/

if(!class_exists('LoftOcean_Shortcode_Author')) {
	class LoftOcean_Shortcode_Author {
		private $class = '';
		private $id = 'loftocean-shortcode-author';
		private $name = 'lo_author';
		private $type = 'complex';
		private $html = '<i class=""></i><span class="title">%s</span>';
		public function __construct() {
			add_action('admin_init', array($this, 'init'));
			add_shortcode($this->name, array($this, 'parse'));
		}
		/**
		* @description init shortcode author
		*/
		public function init() {
			add_action('loftocean_shortcodes', array($this, 'shortcode_btn'));
			add_action('loftocean_shortcodes_setting_tmpl', array($this, 'shortcode_tmpl'));
		}
		/**
		* @description show the button html
		* @param string with format 
		*   %1$s class name
		*   %2$s id 
		*   %3$s shortcode name
		*   %4$s shortcode type, with or without settings
		*   %5$s shortcode title and any icons
		*/
		public function shortcode_btn($tmpl) {
			$html = sprintf($this->html, esc_html__('Author List', 'loftocean'));
			printf($tmpl, $this->class, $this->id, $this->name, $this->type, $html);
		}
		/**
		* @description shortcode setting tmpl
		*/
		public function shortcode_tmpl() { ?>
			<script type="text/html" id="tmpl-loftocean-shortcode-<?php print($this->name); ?>">
				<div class="control" id="author-list-layout">
					<span class="control-title"><?php esc_html_e('Layout', 'loftocean'); ?></span>
					<label for="layout-list">
						<input type="radio" name="author-layout" id="layout-list" value="list" checked /> <?php esc_html_e('List', 'loftocean'); ?>
					</label>
					<label for="layout-1col">
						<input type="radio" name="author-layout" id="layout-1col" value="1col" /> <?php esc_html_e('Grid 1 Col', 'loftocean'); ?>
					</label>
					<label for="layout-2cols">
						<input type="radio" name="author-layout" id="layout-2cols" value="2cols" /> <?php esc_html_e('Grid 2 Cols', 'loftocean'); ?>
					</label>
					<label for="layout-3cols">
						<input type="radio" name="author-layout" id="layout-3cols" value="3cols" /> <?php esc_html_e('Grid 3 Cols', 'loftocean'); ?>
					</label>
					<label for="layout-4cols">
						<input type="radio" name="author-layout" id="layout-4cols" value="4cols" /> <?php esc_html_e('Grid 4 Cols', 'loftocean'); ?>
					</label>
				</div>
				<div class="control switcher" id="author-bio">
					<label for="hide-bio">
						<input type="checkbox" id="hide-bio" name="hide-bio" value="on" />
						<?php esc_html_e('Hide Bio', 'loftocean'); ?>
					</label>
				</div>
				<div class="control switcher" id="author-socials">
					<label for="hide-social-icons">
						<input type="checkbox" id="hide-social-icons" name="hide-social-icons" value="on" />
						<?php esc_html_e('Hide Social Icons', 'loftocean'); ?>
					</label>
				</div>
				<div class="control switcher" id="author-post-count">
					<label for="hide-post-count">
						<input type="checkbox" id="hide-post-count" name="hide-post-count" value="on" />
						<?php esc_html_e('Hide Post Count', 'loftocean'); ?>
					</label>
				</div>
				<div class="control multiple-controls" id="select-by">
					<span class="control-title"><?php esc_html_e('Choose', 'loftocean'); ?></span>
					<select name="show-author-by">
						<option value="all"><?php esc_html_e('All', 'loftocean'); ?></option>
						<option value="name"><?php esc_html_e('By Name', 'loftocean'); ?></option>
						<option value="role"><?php esc_html_e('By Role', 'loftocean'); ?></option>
					</select>
					<div class="by-name" style="display: none;">
					<?php
						$users = get_users(array('fields' => array('id', 'display_name')));
						if(is_array($users) && (count($users) > 0)){
							$html = '';
							foreach($users as $u){
								$html .= sprintf(
									'<label for="author-name-%1$s"><input type="checkbox" name="author-name" id="author-name-%1$s" value="%1$s">%2$s</label>', 
									$u->id,
									$u->display_name
								);
							}
							print($html);
						}
					?>
					</div>
					<div class="by-role" style="display: none;">
					<?php 
						$roles = get_editable_roles();
						if(is_array($roles) && (count($roles) > 0)){
							$html = '';
							foreach($roles as $r => $attr){
								$html .= sprintf(
									'<label for="author-role-%1$s"><input type="checkbox" name="author-roles" id="author-role-%1$s" value="%1$s" />%2$s</label>', 
									$r, 
									$attr['name']
								);
							}
							print($html);
						}
					?>
					</div>
				</div>
			</script> <?php
		}
		/**
		* @description parse shortcode
		* @param array, shortcode attributes
		* @param string, content
		* @return string
		*/
		public function parse($atts, $content = ''){
			$layouts = array(
				'list'  => 'layout-list',
				'1col'  => 'layout-grid cols-1', 
				'2cols' => 'layout-grid cols-2',
				'3cols' => 'layout-grid cols-3',
				'4cols' => 'layout-grid cols-4'
			);
			$atts = shortcode_atts(array(
				'layout'     => 'list',
				'bio'        => 'show',
				'icons'      => 'show',
				'post_count' => 'show',
				'show_by'    => 'all',
				'names'      => '',
				'roles'      => ''
			), $atts, $this->name);

			$args = array('fields' => array('id', 'display_name', 'user_email'));
			switch($atts['show_by']){
				case 'name':
					if(!empty($atts['names'])){
						$args = array_merge($args, array('include' => explode(',', $atts['names'])));
					}
					break;
				case 'role':
					if(!empty($atts['roles'])){
						$args = array_merge($args, array('role__in' => explode(',', $atts['roles'])));
					}
					break;
			}
			$html = '';
			$class = in_array($atts['layout'], array_keys($layouts)) ? $layouts[$atts['layout']] : '';
			$wrap = '<div class="authors-list %s">%s</div>';
			$item_wrap = ('list' == $atts['layout']) ? '<div class="author-info">%s%s%s%s</div>' : '%s%s%s%s';
			$bio_wrap  = ('list' == $atts['layout']) ? '%s' : '<div class="author-info">%s</div>';
			$authors = get_users($args);
			foreach($authors as $u){
				$post_count = count_user_posts($u->id);
				if($post_count > 0){
					$url    = get_author_posts_url($u->id);
					$name   = esc_html($u->display_name); 
					$avatar = get_avatar($u->user_email, 150);
					$bio    = apply_filters('widget_text_content', get_the_author_meta('description', $u->id));
					$icons  = $this->get_user_socials($u->id);
					$count_text = ($post_count > 1) ? sprintf('<span>%d %s</span>', $post_count, esc_html__('Articles', 'loftocean'))
						: sprintf('<span>%d %s</span>', $post_count, esc_html__('Article', 'loftocean'));

					$html .= sprintf(
						'<div class="authors-list-item">%s%s</div>',
						empty($avatar) ? '' : sprintf('<div class="author-photo"><a class="author-link" href="%s">%s</a></div>', $url, $avatar),
						sprintf(
							$item_wrap,
							empty($name) ? '' : sprintf('<h5><a class="author-link" href="%s">%s</a></h5>', $url, $name),
							('hide' == $atts['post_count']) ? '' : $count_text,
							('hide' == $atts['bio']) ? '' : sprintf($bio_wrap, $bio),
							(('hide' == $atts['icons']) || empty($icons)) ? '' : $icons
						)
					);
				}
			}

			return empty($html) ? '' : sprintf($wrap, $class, $html);
		}
		/**
		* @description get social icons for given author
		* @param int author id
		* @return string
		*/
		private function get_user_socials($user_id){
			$website 	= get_the_author_meta('url', $user_id);
			$gplus 		= get_the_author_meta('googleplus', $user_id);
			$facebook 	= get_user_meta($user_id, 'facebook', true);
			$twitter 	= get_user_meta($user_id, 'twitter', true);
			$instagram 	= get_user_meta($user_id, 'instagram', true);
			$pinterest 	= get_user_meta($user_id, 'pinterest', true);
			if(!empty($website) || !empty($gplus) || !empty($facebook) || !empty($twitter) || !empty($instagram) || !empty($pinterest)) : 
				ob_start(); ?>
				<div class="author-social">
					<ul class="social-nav">
						<?php if(!empty($website)) : ?><li><a href="<?php print($website); ?>" title="website"><?php esc_html_e('Website', 'loftocean'); ?></a></li><?php endif; ?>
						<?php if(!empty($gplus)) : ?><li><a href="<?php print($gplus); ?>" title="google plus"><?php esc_html_e('Google Plus', 'loftocean'); ?></a></li><?php endif; ?>
						<?php if(!empty($twitter)) : ?><li><a href="<?php print($twitter); ?>" title="twitter"><?php esc_html_e('Twitter', 'loftocean'); ?></a></li><?php endif; ?>
						<?php if(!empty($facebook)) : ?><li><a href="<?php print($facebook); ?>" title="facebook"><?php esc_html_e('Facebook', 'loftocean'); ?></a></li><?php endif; ?>
						<?php if(!empty($instagram)) : ?><li><a href="<?php print($instagram); ?>" title="instagram"><?php esc_html_e('Instagram', 'loftocean'); ?></a></li><?php endif; ?>
						<?php if(!empty($pinterest)) : ?><li><a href="<?php print($pinterest); ?>" title="pinterest"><?php esc_html_e('Pinterest', 'loftocean'); ?></a></li><?php endif; ?>
					</ul>
				</div><?php
				return ob_get_clean ();
			endif; 
			return '';
		}
	}
	new LoftOcean_Shortcode_Author();
}