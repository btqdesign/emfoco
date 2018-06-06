<?php
/**
* Global helper functions
*/


/**
* @description helper function get theme mode value with theme custom filter
* @return string
*/
function fallsky_get_theme_mod($id){
	global $fallsky_default_settings;
	return apply_filters('fallsky_theme_mod', get_theme_mod($id, $fallsky_default_settings[$id]), $id);
}
/**
* @description test if module enabled
* @return boolean if the customize setting value === 'on', return true, otherwise return false
*/
function fallsky_module_enabled($id){
	return ('on' === fallsky_get_theme_mod($id));
}
/**
* @description recalculate the custom logo width and height
* @return array (src, width, height)
*/
function fallsky_change_custom_logo_width($image, $id, $size, $icon){
	global $fallsky_render_custom_logo;
	if(!empty($fallsky_render_custom_logo)){
		$width = absint(fallsky_get_theme_mod('fallsky_site_logo_width'));
		if((intval($width) > 0) && !empty($image[1])){
			$width = intval($width);
			$image[2] = $width / $image[1] * $image[2];
			$image[1] = $width;
		}
	}
	return $image;
}
/**
* @description get logo id for transparent site header
* @return mix int if exists, otherwise false
*/
function fallsky_get_transparent_site_header_logo_id(){
	$logo_id = fallsky_get_theme_mod('fallsky_transparent_site_header_logo'); 
	if(!empty($logo_id) && (false !== get_post_status($logo_id))){
		global $fallsky_is_preview;
		$is_transparent_site_header = apply_filters('fallsky_is_transparent_site_header', array(false, false));
		if($is_transparent_site_header[0]){
			return $logo_id;
		}
		else if($is_transparent_site_header[1] && $fallsky_is_preview){
			add_filter('fallsky_site_logo_class_in_customize_preview', 'fallsky_site_logo_class_in_cusomize_preview');
			return $logo_id;
		}
	}
	return false;
}
/**
* @description filter for site logo in customize preview page
* @param array of classes, ['array of logo wrap class', 'array of transparent site header logo class']
* @return array of classes
*/
function fallsky_site_logo_class_in_cusomize_preview($class = array()){
	if(is_array($class) && (count($class) == 2)){
		$class[0] = array_diff($class[0], array());
		$class[1] = array_merge($class[1], array('hide'));
	}
	return $class;
}
/**
* @description helper function to test if is doing ajax for front page
* @return boolean return true if doing ajax and set is_front_page to true, otherwise false
*/
function fallsky_is_ajax_on_frontpage(){
	return wp_doing_ajax() && !empty($_REQUEST['is_front_page']);
}
/**
* Test if current in front page
* 	Used inside pre_get_posts filter, otherwise use function fallsky_is_front_page()
* @return boolean
*/
function fallsky_is_customize_front_page($query = false){
	global $wp_query;
	$query 				= ($query === false) ? $wp_query : $query;
	$query_vars 		= $query->query_vars;
	$front_page_id 		= get_option('page_on_front');
	$posts_page_id 		= get_option('page_for_posts');
	$is_static_page 	= ('page' == get_option('show_on_front')) && (!empty($front_page_id) || !empty($posts_page_id));
	$query_page 		= empty($query_vars['pagename']) ? null : get_page_by_path($query_vars['pagename']);
	$query_page_id 		= empty($query_vars['page_id']) ? (empty($query_page) ? false : $query_page->ID) : $query_vars['page_id'];
	$is_home 			= $query->is_home();
	$is_from_customize 	= apply_filters('fallsky_is_static_homepage_content_from_customize', false);

	$is_latest_front 	= (!$is_static_page && $is_home) || ($is_static_page && $is_home && empty($front_page_id));
	$is_static_front	= $is_static_page && !empty($front_page_id) && ($query_page_id === $front_page_id) && $is_from_customize;

	return $is_latest_front || $is_static_front;
}
/**
* Test if current in front page with content from customize
* @return boolean 
*/
function fallsky_is_front_page(){
	return defined('FALLSKY_IS_FRONT_PAGE') && FALLSKY_IS_FRONT_PAGE;
}
/**
* Test if current in static front page 
* 	Used inside pre_get_posts filter, otherwise use is_front_page core function
* @return boolean
*/
function fallsky_is_static_front_page_from_customize($query){
	$query_vars 		= $query->query_vars;
	$is_static_page 	= ('page' == get_option('show_on_front'));
	$front_page_id 		= get_option('page_on_front');
	$is_customize_front = apply_filters('fallsky_is_static_homepage_content_from_customize', false);

	return $is_static_page && !empty($query_vars['page_id']) && ($query_vars['page_id'] == $front_page_id) && $is_customize_front;
}
/**
* Test if current in static front page and using the static page content
*/
function fallsky_is_static_front_page_from_page(){
	$is_static_page 	= ('page' == get_option('show_on_front'));
	$front_page_id 		= get_option('page_on_front');
	$is_customize_front = apply_filters('fallsky_is_static_homepage_content_from_customize', false);

	return $is_static_page && !empty($front_page_id) && is_page($front_page_id) && !$is_customize_front;
}
/**
* Get static homepage page id
* @return mix int if exists, otherwise return boolean false
*/
function fallsky_get_page_on_front(){
	$front_page_id 		= get_option('page_on_front');
	$is_from_customize 	= apply_filters('fallsky_is_static_homepage_content_from_customize', false);
	return ('page' == get_option('show_on_front')) && !empty($front_page_id) && $is_from_customize ? $front_page_id : false;
}
/**
* Get post count for category, including its child category's posts
* @param wp_term category object
* @param boolean includes the post from its child categories
* @return int post count 
*/
function fallsky_get_category_post_count($term, $include = true){
	if($term instanceof WP_Term){
		if($include){
			$posts = new WP_Query(array('post_type' => 'post', 'post_status' => 'publish', 'cat' => $term->term_id, 'fields' => 'ids'));
			return $posts->found_posts;
		}
		else{
			return $term->count;
		}
	}
	return false;
}
/**
* is_** function to test current pagination is ajax based
*/
function fallsky_is_ajax_pagination(){
	$pagination = fallsky_get_theme_mod('fallsky_pagination_style');
	return in_array($pagination, array('ajax-more', 'ajax-infinite'));
}
/**
* @description get class attributes
* @param array default value
* @param string filter name
* @return string
*/
function fallsky_class($val = array(), $filter = ''){
	$class = empty($filter) ? $val : apply_filters($filter, $val);
	$class = array_filter($class);
	return empty($class) ? '' : sprintf(' class="%s"', implode(' ', $class));
}
/**
* @description get site header
*/
function fallsky_get_site_header(){
	get_template_part(
		'template-parts/site-header/header'
	);
}
/**
* @description custom classes for site header
*/
function fallsky_site_header_class(){
	echo fallsky_class(
		array('site-header'), 
		'fallsky_site_header_class'
	);
}
/**
* @description custom classes for fullscreen site header
*/
function fallsky_fullscreen_site_header_class(){
	echo fallsky_class(
		array('fallsky-fullmenu'), 
		'fallsky_fullscreen_site_header_class'
	);
}
/**
* @description custom classes for div#content
*/
function fallsky_content_class(){
	echo fallsky_class(
		array('site-content', fallsky_get_page_layout()), 
		'fallsky_content_class'
	);
}
/**
* @description test whether display sidebar
* @return boolean
*/
function fallsky_get_page_layout(){
	global $fallsky_archive_type;
	$layout_id = '';
	if($fallsky_archive_type){
		$layout_id = 'fallsky_' . $fallsky_archive_type . '_sidebar';
	}
	$layout_id = apply_filters('fallsky_page_layout_id', $layout_id);

	$layout = is_404() ? ''
		: (empty($layout_id) ? 'with-sidebar-right' : esc_attr(fallsky_get_theme_mod($layout_id)));
	return apply_filters('fallsky_page_layout', $layout);
}
/**
* @description display primary site nav
*/
function fallsky_primary_nav(){
	printf(
		'<div id="site-header-menu" class="site-header-menu">%s</div>',
		wp_nav_menu(array(
			'echo'				=> false,
			'theme_location' 	=> 'primary',
			'container' 		=> 'nav',
			'container_id' 		=> 'site-navigation',
			'container_class' 	=> 'main-navigation',
			'menu_id' 			=> 'menu-main-menu',
			'menu_class' 		=> 'primary-menu',
			'depth' 			=> 3,
			'walker' 			=> new Fallsky_Walker_Nav_Menu()
		))
	);
}

/**
* @descriptionn display secondary site menu
*/
function fallsky_secondary_nav($args = array()){
	wp_nav_menu(array_merge(array(
		'theme_location' 	=> 'secondary',
		'container' 		=> 'nav',
		'container_id' 		=> 'secondary-navigation',
		'container_class' 	=> 'secondary-navigation',
		'menu_id' 			=> 'menu-secondary-menu',
		'menu_class' 		=> 'secondary-menu',
		'depth' 			=> 1
	), $args));
}
/**
* @description get social icon list array
* @return array social list
*/
function fallsky_get_socials($args = array()){
	$nav = '';
	if(has_nav_menu('social')){
		$nav = wp_nav_menu(array_merge(array(
			'theme_location' 	=> 'social',
			'depth' 			=> 1,
			'echo' 				=> false
		), $args));
	}
	return $nav;
}
/**
* @description display social list for site header
*/
function fallsky_social_menu($args = array()){
	echo fallsky_get_socials(array_merge(array(
		'container' 		=> 'nav',
		'container_class' 	=> 'social-navigation',
		'menu_id' 			=> 'menu-social-menu',
		'menu_class' 		=> 'social-nav'
	), $args));
}
/**
* @description class for site main sidebar
*/
function fallsky_site_sidebar_class(){
	echo fallsky_class(
		array('sidebar', 'widget-area'),
		'fallsky_site_sidebar_class'
	);
}
/**
* @description custom attributes for site main sidebar
*/
function fallsky_site_sidebar_attrs(){
	printf(
		' data-sticky="%s"', 
		fallsky_module_enabled('fallsky_sidebar_enable_sticky') ? 'sidebar-sticky' : ''
	);
}
/**
* @description custom classes for site header
*/
function fallsky_site_footer_class(){
	echo fallsky_class(
		array('site-footer'),
		'fallsky_site_footer_class'
	);
}
/**
* @description helper function to get image src
* @param int image id
* @param string image size
* @param boolean if filter the image size
* @return string image url
*/
function fallsky_get_image_src( $id, $size = false, $filter = true ) {
	if(!empty($id)){
		$size 	= empty($size) ? 'full' : $size;
		if( $filter ){
			$size = apply_filters('fallsky_image_size', $size); 
		}
		$image = wp_get_attachment_image_src($id, $size);
		return $image ? $image[0] : false;
	}
	return false;
}
/**
* Get category index page id
* @return mix return page id if exists and valid otherwise return false
*/
function fallsky_get_category_index_page_id(){
	$page_id = get_option('fallsky_category_index_page_id');
	if(empty($page_id)){
		return false;
	}
	if('page' == get_option('show_on_front', 'posts')){
		$pages = fallsky_get_static_pages();
		if(in_array($page_id, $pages)){
			return false;
		}
	}
	return $page_id;
}

/**
* Get static page ids
* @param boolean only wp core static pages
* @param boolean 
* @return array
*/
function fallsky_get_static_pages($core = true){
	$pages = ('page' == get_option('show_on_front', 'posts')) 
		? array(get_option('page_for_posts'), get_option('page_on_front')) : array();
	if(!$core){
		array_push($pages, get_option('fallsky_category_index_page_id'));
		$pages = apply_filters('fallsky_static_pages', $pages);
	}
	return array_unique($pages);
}
/**
* Get current page header layout
* @return string
*/
function fallsky_get_page_header_layout(){
	return apply_filters('fallsky_get_page_header_layout', '');
}
/**
* Get gallery post format media
* @return string
*/
function fallsky_get_gallery_post_media(){
	if('gallery' == get_post_format()){
		return apply_filters('loftocean_get_post_format_media', false);
	}
	return false;
}
/**
* Test if current post is gallery and actually has the gallery set
* @return boolean
*/
function fallsky_has_featured_gallery(){
	global $fallsky_list_args;
	return ('gallery' == get_post_format()) && !empty($fallsky_list_args['media']) && has_shortcode($fallsky_list_args['media'], 'gallery');
}
/**
* Side sharing buttons for single post
*/
function fallsky_share_buttons(){
	if(class_exists('LoftOcean_Post_Metas')){
		$class 	= array('side-share-icons'); 
		$enable = array();
		fallsky_module_enabled('fallsky_single_post_facebook_sharing')    ? array_push($enable, 'facebook') : '';
		fallsky_module_enabled('fallsky_single_post_twitter_sharing')     ? array_push($enable, 'twitter') : '';
		fallsky_module_enabled('fallsky_single_post_pinterest_sharing')   ? array_push($enable, 'pinterest') : '';
		fallsky_module_enabled('fallsky_single_post_google_plus_sharing') ? array_push($enable, 'google_plus') : '';

		if(fallsky_module_enabled('fallsky_single_post_show_sharing_buttons_on_mobile')){
			array_push($class, 'mobile-sticky');
		}
		do_action('loftocean_post_meta_sharing', $enable, implode(' ', $class));
	}
}
/**
* Get post authors
* @return object array users
*/
function fallsky_get_post_authors(){
	$author_ids = has_filter('loftocean_post_authors') ? apply_filters('loftocean_post_authors', array()) : array(get_the_author_meta('ID'));
	return get_users(array('include' => $author_ids));
}
/**
* Parse html attributes
* @param array list of attributes
* @return string 
*/
function fallsky_get_html_attributes($attrs = array()){
	if(!empty($attrs)){
		if(is_array($attrs)){
			$items = array();
			foreach($attrs as $key => $value){
				if(!empty($key) && !empty($value)){
					$items[] = sprintf('%s="%s"', $key, $value);
				}
			}
			return empty($items) ? '' : implode(' ', $items);
		}
		else{
			return $attrs;
		}
	}
}
/**
* Get image alt text
* @param int image id
* @return string image alt text
*/
function fallsky_get_image_alt($image_id){
	if(!empty($image_id) && (false !== get_post_status($image_id))){
		$alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
		if(empty($alt)){
			$attachment = get_post($image_id);
			$post_id = $attachment->post_parent;
			$post_title = !empty($post_id) && (false !== get_post_status($post_id)) ? get_the_title($post_id) : false;
			if(!empty($post_title)){
				return esc_attr($post_title);
			}
			else{
				return empty($attachment->post_title) ? esc_attr($attachment->post_name) : esc_attr($attachment->post_title);
			}
		}
		else{
			return esc_attr($alt);
		}
	}

	return '';
}
/**
* Convert tax slug to id
* @param mix string or array
* @param string taxonomy
* @return mix string or array
*/
function fallsky_convert_tax_slug2id($slugs, $tax = 'category'){
	if(!empty($slugs) && !empty($tax)){
		if(is_array($slugs)){
			$ids = array();
			foreach($slugs as $slug){
				$term = get_term_by('slug', $slug, $tax);
				if(!empty($term)){
					array_push($ids, $term->term_id);
				}
			}
			return empty($ids) ? false : $ids;
		}
		else if(is_string($slugs)){
			$term = get_term_by('slug', $slugs, $tax);
			return empty($term) ? false : $term->term_id;
		}
	}
	return false;
}
/**
* Get default mc4wp form id
*/
function fallsky_get_default_mc4wp_form_id(){
	if(function_exists('mc4wp')){ 
		$forms = fallsky_mc4w_forms();
		return !empty($forms) && (count($forms) > 1) ? array_keys($forms)[1] : '';
	}
	return '';
}
/**
* Get featured image caption html
*/
function fallsky_get_featured_image_caption_html(){
	if(has_post_thumbnail()){
		$caption = wp_get_attachment_caption(get_post_thumbnail_id());
		if(!empty($caption)){
			return sprintf('<div class="image-caption">%s</div>', $caption);
		}
	}

	return '';
}
