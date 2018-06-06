<?php
// Author widget
class LoftOcean_Widget_Author extends WP_Widget {
	public function __construct() {
		$class = apply_filters('loftocean_author_widget_class', 'loftocean-widget_author_list');
		$title = apply_filters('loftocean_author_widget_name', esc_html__('LoftOcean Author List', 'loftocean'));
		$widget_ops = array(
			'classname' => $class,
			'description' => esc_html__('Show your author list.', 'loftocean'),
			'customize_selective_refresh' => true,
		);
		parent::__construct('loftocean-author', $title, $widget_ops);
		$this->alt_option_name = 'loftocean-widget_author_list';
	}
	/**
	 * Outputs the content for the current Recent Posts widget instance.
	 */
	public function widget( $args, $instance ) {
		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}

		$title = !empty( $instance['title']) ? $instance['title'] : esc_html__('Authors', 'loftocean');
		$title = esc_html($title);
		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters('widget_title', $title, $instance, $this->id_base);

		$hide_photo = !empty($instance['hide_photo']);
		$hide_post_count = !empty($instance['hide_post_count']);
		$hide_role = !empty($instance['hide_role']);

		$choose_by = isset($instance['choose_by'] ) ? $instance['choose_by'] : 'all'; 
		$choose_by = in_array($choose_by, array('name', 'role', 'all')) ? $choose_by : 'all';

		$list = isset($instance['list']) ? $instance['list'] : array();
		$list = is_array($list) ? $list : array();
		$sort_by = isset($instance['sort_by']) ? $instance['sort_by'] : 'name';

		$html = '';
		$fields = array('id', 'display_name', 'user_email');
		$author_args = array('fields' => $fields, 'orderby' => 'display_name');
		if('count' == $sort_by){
			$author_args['orderby']	= 'post_count';
			$author_args['order'] = 'DESC';
		}
		if(!empty($list)){
			$author_args = ('name' == $choose_by) ? array_merge($author_args, array('include' => $list))
				: (('role' == $choose_by) ? array_merge($author_args, array('role__in' => $list)) : $author_args);
		}
		$authors = get_users($author_args);
		$authors = $this->formatting($authors);
		foreach($authors as $u){
			$url    = $u['url'];
			$name   = $u['name']; 
			$avatar = $hide_photo ? '' : $u['avatar'];
			$count  = $hide_post_count ? '' : sprintf('<span class="post-count">(%d)</span>', $u['post_count']);
			$role   = $hide_role ? '' : sprintf('<span class="role">%s</span>', $u['display_role']); 
			$html .= sprintf(
				'<li><a href="%s">%s<div class="author-info"><h4 class="author-name">%s %s</h4>%s</div></a></li>',
				$url,
				empty($avatar) ? '' : sprintf('<div class="author-photo">%s</div>', $avatar),
				$name,
				$count,
				$role
			);
		}

		if(!empty($html)){
			print($args['before_widget']); 
			if(!empty($title)){
				printf('%s%s%s', $args['before_title'], $title, $args['after_title']);
			}
			printf('<ul>%s</ul>', $html); 
			print($args['after_widget']);
		}
	}
	/**
	 * Handles updating the settings.
	 */
	public function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['title'] = sanitize_text_field($new_instance['title']);
		$instance['hide_photo'] = isset($new_instance['hide_photo']) ? 'on' : false;
		$instance['hide_post_count'] = isset($new_instance['hide_post_count']) ? 'on' : false;
		$instance['hide_role'] = isset($new_instance['hide_role']) ? 'on' : false;
		$sort_by = isset($new_instance['sort_by']) ? $new_instance['sort_by'] : 'name';
		$instance['sort_by'] = in_array($sort_by, array('name', 'count')) ? $sort_by : 'name';

		$choose_by = isset($new_instance['choose_by']) ? $new_instance['choose_by'] : 'all';
		$instance['choose_by'] = in_array($choose_by, array('name', 'role', 'all')) ? $choose_by : 'all';
		$list = array();
		switch($choose_by){
			case 'name':
				$list = isset($new_instance['list_by_name']) ? $new_instance['list_by_name'] : array();
				break;
			case 'role':
				$list = isset($new_instance['list_by_role']) ? $new_instance['list_by_role'] : array();
				break;
		}
		$instance['list'] = $list;

		return $instance;
	}
	/**
	 * Outputs the settings form.
	 */
	public function form($instance){
		$title = isset($instance['title']) ? esc_attr($instance['title']) : esc_attr__ ('Authors', 'loftocean');
		$hide_photo = isset($instance['hide_photo'] ) ? $instance['hide_photo'] : '';
		$hide_post_count = isset($instance['hide_post_count'] ) ? $instance['hide_post_count'] : ''; 
		$hide_role = isset($instance['hide_role'] ) ? $instance['hide_role'] : ''; 
		$choose_by = isset($instance['choose_by'] ) ? $instance['choose_by'] : 'all'; 
		$choose_by = in_array($choose_by, array('name', 'role', 'all')) ? $choose_by : 'all';
		$list = isset($instance['list']) ? $instance['list'] : array();
		$list = is_array($list) ? $list : array();
		$sort_by = isset($instance['sort_by']) ? $instance['sort_by'] : 'name'; ?>

		<p>
			<label for="<?php print($this->get_field_id( 'title' )); ?>"><?php esc_html_e('Title:', 'loftocean'); ?></label>
			<input class="widefat" id="<?php print($this->get_field_id( 'title' )); ?>" name="<?php print($this->get_field_name('title')); ?>" type="text" value="<?php print($title); ?>" />
		</p>

		<p>
			<input type="checkbox" id="<?php print($this->get_field_id('hide_photo')); ?>" name="<?php print($this->get_field_name('hide_photo')); ?>" value="on" <?php checked('on', $hide_photo); ?> />
			<label for="<?php print($this->get_field_id('hide_photo')); ?>"><?php esc_html_e('Hide Photo', 'loftocean'); ?></label>
		</p>

		<p>
			<input type="checkbox" id="<?php print($this->get_field_id('hide_post_count')); ?>" name="<?php print($this->get_field_name('hide_post_count')); ?>" value="on" <?php checked('on', $hide_post_count); ?> />
			<label for="<?php print($this->get_field_id('hide_post_count')); ?>"><?php esc_html_e('Hide Post Count', 'loftocean'); ?></label>
		</p>

		<p>
			<input type="checkbox" id="<?php print($this->get_field_id('hide_role')); ?>" name="<?php print($this->get_field_name('hide_role')); ?>" value="on" <?php checked('on', $hide_role); ?> />
			<label for="<?php print($this->get_field_id('hide_role')); ?>"><?php esc_html_e('Hide Role', 'loftocean'); ?></label>
		</p>

		<p>
			<label for="<?php print($this->get_field_id('choose_by')); ?>"><?php esc_html_e('Choose', 'loftocean'); ?></label>
			<select id="<?php print($this->get_field_id('choose_by')); ?>" name="<?php print($this->get_field_name('choose_by')); ?>" class="loftocean-author-widget-choose-by">
				<option value="all" <?php selected('all', $choose_by); ?>><?php esc_html_e('All', 'loftocean'); ?></option>
				<option value="name" <?php selected('name', $choose_by); ?>><?php esc_html_e('Name', 'loftocean'); ?></option>
				<option value="role" <?php selected('role', $choose_by); ?>><?php esc_html_e('Role', 'loftocean'); ?></option>
			</select>
		</p>

		<p class="author-list-choices author-list-by-name"<?php echo ('name' == $choose_by) ? '' : ' style="display: none;"' ?>>
			<select name="<?php print($this->get_field_name('list_by_name')); ?>[]" multiple size="5" style="width: 95%;">
			<?php
				$users = get_users(array('fields' => array('id', 'display_name')));
				if(is_array($users) && (count($users) > 0)){
					$html = '';
					$choose_by_name = ($choose_by == 'name');
					foreach($users as $u){
						$html .= sprintf(
							'<option value="%s"%s>%s</option>', 
							$u->id,
							($choose_by_name && in_array($u->id, $list)) ? ' selected' : '',
							$u->display_name
						);
					}
					print($html);
				}
			?>
			</select>
		</p>

		<p class="author-list-choices author-list-by-role"<?php echo ($choose_by == 'role') ? '' : ' style="display: none;"' ?>>
			<select name="<?php print($this->get_field_name('list_by_role')); ?>[]" multiple size="5" style="width: 95%;">
			<?php 
				$roles = get_editable_roles();
				if(is_array($roles) && (count($roles) > 0)){
					$html = '';
					$choose_by_role = ('role' == $choose_by);
					foreach($roles as $r => $attr){
						$html .= sprintf(
							'<option value="%s"%s>%s</option>', 
							$r, 
							($choose_by_role && in_array($r, $list)) ? ' selected' : '',
							$attr['name']
						);
					}
					print($html);
				}
			?>
			</select>
		</p>

		<p>
			<label for="<?php print($this->get_field_id('sort_by')); ?>"><?php esc_html_e('Sort by', 'loftocean'); ?></label>
			<select id="<?php print($this->get_field_id('sort_by')); ?>" name="<?php print($this->get_field_name('sort_by')); ?>">
				<option value="name" <?php selected('name', $sort_by); ?>><?php esc_html_e('Name', 'loftocean'); ?></option>
				<option value="count" <?php selected('count', $sort_by); ?>><?php esc_html_e('Post Count', 'loftocean'); ?></option>
			</select>
		</p> <?php

	}
	private function formatting($authors){
		function_exists('get_editable_roles') ? '' : require_once(ABSPATH . 'wp-admin/includes/user.php');
		$editable_roles = get_editable_roles();
		$list = array();
		foreach($authors as $a){
			$id = $a->id;
			$post_count = count_user_posts($id);
			if($post_count > 0){
				$metas 			= get_userdata($id);
				$role  			= $metas->roles[0];
				$detail 		= isset($editable_roles[$role]) ? $editable_roles[$role] : array('name' => ucfirst($role));
				$display_role 	= translate_user_role($detail['name']);
				$url    		= get_author_posts_url($id);
				$name   		= esc_html($a->display_name); 
				$avatar 		= get_avatar($a->user_email, 40);
				array_push($list, array(
					'id' => $id,
					'url' => $url,
					'role' => $role,
					'display_role' => $display_role,
					'name' => $name,
					'avatar' => $avatar,
					'post_count' => $post_count
				));
			}
		}
		return $list;
	}
}