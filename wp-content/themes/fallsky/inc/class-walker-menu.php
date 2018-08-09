<?php
if(class_exists('Walker_Nav_Menu')){
	// For main menu to generate mage menu related elements
	class Fallsky_Walker_Nav_Menu extends Walker_Nav_Menu {
		public function display_element($element, &$children_elements, $max_depth, $depth, $args, &$output){
			if(!$element){
				return;
			}

			$id_field = $this->db_fields['id'];
			$id       = $element->$id_field;

			//display this element
			$this->has_children = !empty($children_elements[$id]);
			if(isset($args[0]) && is_array($args[0])){
				$args[0]['has_children'] = $this->has_children; // Back-compat.
			}

			$cb_args = array_merge(array(&$output, $element, $depth), $args);
			call_user_func_array(array($this, 'start_el'), $cb_args);

			// descend only when the depth is right and there are childrens for this element
			if(($max_depth == 0 || ($max_depth > $depth + 1)) && isset( $children_elements[$id])){
				if(!$this->is_mega_category($element, $depth)){
					foreach($children_elements[$id] as $child){
						if(!isset($newlevel)){
							$newlevel = true;
							//start the child delimiter
							$cb_args = array_merge( array(&$output, $depth), $args);
							call_user_func_array(array($this, 'start_lvl'), $cb_args);
						}
						$this->display_element( $child, $children_elements, $max_depth, $depth + 1, $args, $output );
					}
				}
				unset($children_elements[$id]);
			}
			if(isset($newlevel) && $newlevel){
				//end the child delimiter
				$cb_args = array_merge(array(&$output, $depth), $args);
				call_user_func_array(array($this, 'end_lvl'), $cb_args);
			}

			//end this element
			$cb_args = array_merge( array(&$output, $element, $depth), $args);
			call_user_func_array(array($this, 'end_el'), $cb_args);
		}
		public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0){
			$classes = empty( $item->classes ) ? array() : (array)$item->classes;
			// $depth starts from 0, so 2 means third level
			if($depth > 1){
				if(in_array('menu-item-has-children', $classes)){
					$item->classes = array_diff($classes, array('menu-item-has-children'));
				}
			}
			// Support mega menu for first level only
			if($depth > 0){
				if(in_array('mega-menu', $classes)){
					$item->classes = array_diff($classes, array('mega-menu'));
				}
			}
			// If is category and has its child category, add class menu-item-has-children
			if($this->is_mega_category($item, $depth)){
				$term_id = $item->object_id;
				$terms = get_terms('category', array('parent' => $term_id));
				if(!is_wp_error($terms) && (count($terms) > 0)){
					$item->classes = array_merge($classes, array('menu-item-has-children'));
				};
			}
			parent::start_el($output, $item, $depth, $args, $id);
		}
		public function end_el(&$output, $item, $depth = 0, $args = array()){
			if($this->is_mega_category($item, $depth)){ 
				$term_id = $item->object_id;
				$terms = get_terms('category', array('parent' => $term_id));
				$ppp = (!is_wp_error($terms) && (count($terms) > 0)) ? 3 : 4;
				$query = new WP_Query(array('posts_per_page' => $ppp, 'cat' => $term_id, 'offset' => 0));
				if($query->have_posts()){
					$output .= '<ul class="sub-menu" style="display: none;">';
					if(!is_wp_error($terms) && (count($terms) > 0)){
						$tmpl = '<li class="sub-cat-list"><ul>%s</ul></li><li class="sub-cat-posts">%s</li>';
						$cat_list = sprintf(
							'<li class="current" data-id="cat-%s"><a href="%s">%s</a></li>',
							$term_id,
							get_term_link(intval($term_id), 'category'),
							esc_html__('All', 'fallsky')
						);
						$post_list = $this->post_list($query, '<div class="sub-cat current cat-' . $term_id . '"><ul>', '</ul></div>');
						foreach($terms as $t){
							$term_id = $t->term_id;
							$query = new WP_Query(array('posts_per_page' => $ppp, 'cat' => $term_id, 'offset' => 0));
							if($query->have_posts()){
								$term_id = $t->term_id;
								$cat_list .= sprintf('<li data-id="cat-%s"><a href="%s">%s</a></li>',
									$term_id,
									get_term_link($t, 'category'),
									$t->name
								);
								$post_list .= $this->post_list($query, '<div class="sub-cat cat-' . $term_id . '"><ul>', '</ul></div>');
							}
						} 
						$output .= sprintf($tmpl, $cat_list, $post_list);
					}
					else{
						$output .= $this->post_list($query);
					}
					$output .= '</ul>';
				}
			}
			parent::end_el($output, $item, $depth, $args);
		}	
		public function start_lvl( &$output, $depth = 0, $args = array() ) {
			if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
				$t = '';
				$n = '';
			} else {
				$t = "\t";
				$n = "\n";
			}
			$indent = str_repeat( $t, $depth );
			$output .= "{$n}{$indent}<ul class=\"sub-menu\" style=\"display: none;\">{$n}";
		}
		private function is_mega_category($item, $depth){
			error_log("objeto:" .$item->object);
			$deep=in_array('mega-menu', (array)$item->classes) && ($depth == 0);
			$export = var_export($deep , true);
			error_log("bool:" .$export);
			return in_array('mega-menu', (array)$item->classes) && ($depth == 0) && ($item->object == 'category' || $item->object == 'sector');
		}
		private function post_list($query, $before = '', $after = ''){
			ob_start();
			print($before);
			do_action('fallsky_set_frontend_options', 'mega_menu_post');
			while($query->have_posts()) : $query->the_post();
				$has_thumbnail = has_post_thumbnail();
				$link = get_permalink(); ?>
				<li>
					<div class="post mega-menu-post<?php if($has_thumbnail){ echo ' has-post-thumbnail'; } ?>">
						<?php if($has_thumbnail) : ?>
						<figure class="featured-img">
							<?php echo fallsky_get_preload_bg(array('tag' => 'a', 'attrs' => array('href' => $link))); ?>
						</figure>
						<?php endif; ?>
						<div class="post-content">
							<div class="post-header">
								<p class="post-title">
									<a href="<?php print($link); ?>"><?php the_title(); ?></a>
								</p>
							</div>
							<?php $this->show_meta(); ?>
						</div>
					</div>
				</li> <?php
			endwhile; wp_reset_postdata();
			do_action('fallsky_reset_frontend_options');
			print($after);
			return ob_get_clean();
		}
		private function show_meta(){
			return '';
		}
	}

	// Waler class for fullscreen site header
	class Fallsky_Walker_Fullscreen_Nav_Menu extends Walker_Nav_Menu{
		/*
		 * @description add a wrapper div
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param int    $depth  Depth of menu item. Used for padding.
		 * @param array  $args   An array of wp_nav_menu() arguments.
		 */
		public function start_lvl(&$output, $depth = 0, $args = array()){
			$indent = str_repeat("\t", $depth);
			$wrap = ($args->theme_location === 'primary') ? sprintf('<button class="dropdown-toggle" aria-expanded="false"><span class="screen-reader-text">%s</span></button>', esc_html__('expand child menu', 'fallsky')) : '';
			$output .=  "\n$indent$wrap<ul class=\"sub-menu\">\n";
		}
	}
}
