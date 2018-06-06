<?php
/**
 * Custom theme template tags
 */

if(!function_exists('fallsky_the_custom_logo')) :
	/**
	* @description show custom logo
	*/
	function fallsky_the_custom_logo(){
		if(function_exists('the_custom_logo')){
			$image_id = intval(get_theme_mod('custom_logo', false));
			if(!empty($image_id)){
				global $fallsky_render_custom_logo, $fallsky_custom_logo_filter_added;
				$fallsky_render_custom_logo = true;
				if(empty($fallsky_custom_logo_filter_added)){
					// Change the width/height attributes for attachment
					add_filter('wp_get_attachment_image_src', 'fallsky_change_custom_logo_width', 10, 4);
					$fallsky_custom_logo_filter_added = true;
				}
				$image = get_post($image_id);
				$link_class	= array('custom-logo-link');
				$trans_logo_id = fallsky_get_transparent_site_header_logo_id();
				$trans_logo_html = '';
				if(!empty($trans_logo_id)){
					array_push($link_class, 'with-logo-alt');
					$classes = apply_filters('fallsky_site_logo_class_in_customize_preview', array($link_class, array('custom-logo-alt')));
					$link_class 	 	= $classes[0];
					$trans_logo_class 	= $classes[1];
					$trans_logo_attach 	= get_post($trans_logo_id);
					$trans_logo_image 	= wp_get_attachment_image_src($trans_logo_id, 'full');
					$trans_logo_alt  	= fallsky_get_image_alt($trans_logo_id);
					if(empty($trans_logo_alt)){
						$trans_logo_alt = esc_attr(get_bloginfo('name', 'display'));
					}
					$trans_logo_html	= (strpos($trans_logo_attach->post_mime_type, 'svg') !== false) ? sprintf(
						'<img src="%s" class="%s" alt="%s" width="%s">',
						$trans_logo_image[0],
						implode(' ', $trans_logo_class),
						$trans_logo_alt,
						$width
					) :  wp_get_attachment_image($trans_logo_id, 'full', false, array('alt' => $trans_logo_alt, 'class' => 'custom-logo-alt')); 
				}
				if(!empty($image) && (strpos($image->post_mime_type, 'svg') !== false)){
					$image = wp_get_attachment_image_src($image_id, 'full');
					$width = absint(fallsky_get_theme_mod('fallsky_site_logo_width'));
					printf('<a href="%s" class="%s" rel="home" itemprop="url"><img src="%s"class="custom-logo" alt="%s" width="%s">%s</a>',
						esc_url(home_url('/')),
						implode(' ', $link_class),
						esc_url_raw($image[0]),
						fallsky_get_image_alt($image_id),
						$width,
						$trans_logo_html
					);
				}
				else{
					$logo = get_custom_logo();
					if(!empty($logo) && !empty($trans_logo_html)){
						$link_class = implode(' ', $link_class);
						$logo = str_replace(array('custom-logo-link', '</a>'), array($link_class, sprintf('%s</a>', $trans_logo_html)), $logo);
					}
					print($logo);
				}
				$fallsky_render_custom_logo = false;
			}
		}
	}
endif;

if(!function_exists('fallsky_site_branding')) :
	/**
	 * Prints HTML of site branding.
	 *
	 * Create your own function to override in a child theme.
	 *
	 * @since 1.0.0
	 */
	function fallsky_site_branding(){ ?>
		<!-- .site-branding -->
		<div class="site-branding">
			<?php fallsky_the_custom_logo(); ?>
			<?php if(display_header_text()) : ?>
				<p class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a></p>
				<?php
				    $description = get_bloginfo('description', 'display');
					if(!empty($description) || is_customize_preview()) : ?>
						<p class="site-description"><?php echo esc_html($description); ?></p>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<!-- End of .site-branding --> <?php
	}
endif;

if( !function_exists( 'fallsky_main_content' ) ) :
	/**
	 * Prints main content
	 * Create your own function to override in a child theme.
	 *
	 * @param string action name
	 * @since 1.0.0
	 */
	function fallsky_main_content( $action = 'fallsky_main_content' ) {
		if( has_action( $action ) ) {
			do_action($action);
		}
		else {
			while( have_posts() ) {
				the_post();
				the_content();
				if( is_singular() ) {
					if( comments_open() || get_comments_number() ) {
						comments_template();
					}
				}
			}
		}
	}

endif;

if( !function_exists( 'fallsky_get_responsive_image' ) ) :
	/**
	* Get img tag with featured image for better user experience
	* Create your own function to override in a child theme.
	* @since 1.0.0
	* @param array settings
	* 		id 		int, 	image id
	* 		size 	string, image size
	*		class 	string, html tag class name
	*		html 	string, html string inside tag 
	*		tag 	string, html tag
	*		attrs 	array, 	html tag attributes
	* @param boolean flag identify use call preloader image filter(if exists)
	* @return html element attributes string
	 */
	function fallsky_get_responsive_image( $args = array(), $no_filter = false ) {
		$options 	= array_merge(array( 'id' => null, 'size' => 'full' ), $args );
		$image_id 	= empty( $options['id'] ) ? get_post_thumbnail_id() : $options['id'];
		$image_size = apply_filters( 'fallsky_image_size', empty( $options['size'] ) ? 'full' : $options['size'] );
		$caption 	= wp_get_attachment_caption( $image_id );
		$image_alt 	= fallsky_get_image_alt( $image_id );
		if( has_filter('loftocean_get_responsive_image' ) && !$no_filter ) {
			$attrs = empty( $caption ) ? array( 'alt' => $image_alt ) : array( 'title' => esc_attr($caption), 'alt' => $image_alt );
			return apply_filters('loftocean_get_responsive_image', '', $image_id, $image_size, array( 'attrs' => $attrs ) );
		}
		else{
			$image_src = fallsky_get_image_src( $image_id, $image_size, false );
			return empty( $image_src ) ? '' : wp_image_add_srcset_and_sizes( sprintf (
				'<img src="%s"%s %s>', 
				$image_src, 
				empty( $caption ) ? '' : sprintf( ' title="%s"', esc_attr( $caption ) ),
				sprintf( 'alt="%s"', $image_alt )
			), wp_get_attachment_metadata( $image_id ), $image_id );
		}
	}
endif;

if( !function_exists( 'fallsky_get_preload_bg' ) ) :
	/**
	* Get div tag with featured image as background image for better user experience
	* Create your own function to override in a child theme.
	* @since 1.0.0
	* @param array settings
	* 		id 		int, 	image id
	* 		size 	string, image size
	*		class 	string, html tag class name
	*		html 	string, html string inside tag 
	*		tag 	string, html tag
	*		attrs 	array, 	html tag attributes
	* @param boolean flag identify use call preloader image filter(if exists)
	* @return html element attributes string
	*/
	function fallsky_get_preload_bg( $args = array(), $no_filter = false ) {
		$default_sizes = array( 'full', 'full' );
		$options 	= array_merge( array(
			'id' 	=> null, 
			'sizes' => $default_sizes, 
			'class' => 'featured-img-container', 
			'html' 	=> '', 
			'tag' 	=> 'div', 
			'attrs' => ''
		), $args );

		$image_id 	= empty( $options['id'] ) ? get_post_thumbnail_id() : $options['id'];
		$image_size = apply_filters( 'fallsky_image_sizes', empty( $options['sizes'] ) ? $default_sizes : $options['sizes'] );
		if( empty( $options['tag'] ) ) {
			$options['tag'] = 'div';
		}

		if( has_filter( 'loftocean_get_preload_bg' ) && !$no_filter ) {
			unset($options['id'], $options['size']);
			return apply_filters( 'loftocean_get_preload_bg', '', $image_id, $image_size, $options );
		}
		else {
			$image_attr = fallsky_get_preload_bg_attrs( array( 'id' => $image_id, 'sizes' => $options['sizes'] ), $no_filter );
			return empty( $image_attr ) ? '' : sprintf(
				'<%1$s%2$s style="%3$s%4$s" %5$s>%6$s</%1$s>', 
				$options['tag'],
				empty( $options['class'] ) ? '' : sprintf( ' class="%s"', $options['class'] ),
				empty( $options['attrs']['style'] ) ? '' : sprintf( '%s ', $options['attrs']['style'] ),
 				$image_attr,
				empty( $options['attrs'] ) ? '' : fallsky_get_html_attributes( $options['attrs'] ),
				$options['html']
			);
		}
	}
endif;

if( !function_exists( 'fallsky_get_preload_bg_attrs' ) ) :
	/**
	* Get html attributes for preload image
	* Create your own function to override in a child theme.
	* @since 1.1.0
	* @param array settings
	* 		id 		int, 	image id
	* 		sizes 	string, image size
	* @param boolean flag identify use call preloader image filter(if exists)
	* @return html element attributes string
	*/
	function fallsky_get_preload_bg_attrs( $args = array(), $no_filter = false ) {
		$default_image_sizes = array( 'full', 'full' );
		$options = array_merge( array (
			'id' 	=> null, 
			'sizes' => $default_image_sizes
		), $args );

		$image_id 		= empty( $options['id'] ) ? get_post_thumbnail_id() : $options['id'];
		$image_sizes 	= apply_filters( 
			'fallsky_image_sizes', 
			( ( empty( $options['sizes'] ) || !is_array( $options['sizes'] ) ) ? $default_image_sizes : $options['sizes'] ) 
		);

		if( has_filter( 'loftocean_get_preload_bg_attrs' ) && !$no_filter ) {
			return apply_filters( 'loftocean_get_preload_bg_attrs', '', $image_id, $image_sizes );
		}
		else{			
			$image_src = fallsky_get_image_src( $image_id, $image_sizes[0] );
			return empty( $image_src ) ? '' : sprintf( 'background-image: url(%s);', esc_url( $image_src ) );
		}
	}
endif;

if(!function_exists('fallsky_list_featured_section')) :
	/**
	 * Get img tag with featured image for better user experience
	 *
	 * Create your own function to override in a child theme.
	 *
	 * @since 1.0.0
	 * @param boolean featured image shown type
	 * 		1. True in tag <img>
	 * 		2. False show as background image of <div>
	 */
	function fallsky_list_featured_section($img_tag = true){
		$post_url 		= get_permalink();
		$post_format 	= get_post_format();
		$has_thumbnail 	= has_post_thumbnail();
		$gallery 		= fallsky_get_post_gallery();
		$is_gallery 	= ('gallery' == $post_format) && !empty($gallery);
		if($is_gallery || $has_thumbnail) : ?>
			<div class="featured-img">
				<a href="<?php print($post_url); ?>"><?php print( $is_gallery ? $gallery : ( $img_tag ? fallsky_get_responsive_image() : fallsky_get_preload_bg() ) ); ?></a>			
			</div> <?php
		endif;
	}
endif;

if(!function_exists('fallsky_get_post_categories')) :
	/**
	 * Prints HTML with category for current post
	 * Create your own function to override in a child theme.
	 * @since 1.0.0
	 */
	function fallsky_get_post_categories($args = array(), $echo = true){
		$args = array_merge(array(
			'wrap_tag' 		=> 'div',
			'wrap_id' 		=> '',
			'wrap_class' 	=> 'cat-links',
			'seperator' 	=> ', '
		), $args);
		$categories = get_the_category_list($args['seperator']);
		if(!empty($categories)){
			$html = sprintf(
				'<%1$s%2$s%3$s>%4$s</%1$s>',
				empty($args['wrap_tag'])	? 'div' : $args['wrap_tag'],
				empty($args['wrap_id']) 	? '' 	: sprintf(' id="%s"', $args['wrap_id']),
				empty($args['wrap_class']) 	? '' 	: sprintf(' class="%s"', $args['wrap_class']),
				$categories
			);
			if($echo){
				print($html);
			}
			else{
				return $html;
			}
		}
	}
endif;

if(!function_exists('fallsky_post_tags')) :
	/**
	 * Prints HTML with tags for current post.
	 * Create your own function to override in a child theme.
	 * @since 1.0.0
	 */
	function fallsky_post_tags(){
		$tags_list = get_the_tag_list();
		if(!empty($tags_list)){
			printf('<aside class="post-tag-cloud"><div class="tagcloud">%s</div></aside>', $tags_list);
		}
	}
endif;

if(!function_exists('fallsky_meta_excerpt')) :
	/**
	* Prints post meta excerpt for current post
	* Create your own function to override in a child theme.
	* @since 1.0.0
	*/
	function fallsky_meta_excerpt(){
		$excerpt = get_the_excerpt();
		if(!empty($excerpt)){
			printf(
				'<div class="post-excerpt">%s</div>',
				$excerpt
			);
		}
	}
endif;

if(!function_exists('fallsky_meta_author')) :
	/**
	 * Prints post meta author for current post
	 * Create your own function to override in a child theme.
	 * @since 1.0.0
	 */
	function fallsky_meta_author(){
		$authors 	= fallsky_get_post_authors();
		$list 		= array();
		foreach($authors as $author){
			$list[]	= sprintf(
				'<a href="%s">%s</a>', 
				esc_url(get_author_posts_url($author->ID)), 
				esc_html($author->display_name)
			);
		}
		printf(
			'<div class="meta-item">%s</div>',
			sprintf(
				esc_html__('by %s', 'fallsky'),
				implode(', ', $list)
			)
		);
	}
endif;

if(!function_exists('fallsky_meta_date')) :
	/**
	 * Prints post meta date information for current post.
	 * Create your own function to override in a child theme.
	 * @since 1.0.0
	 */
	function fallsky_meta_date($echo = true){
		$date = sprintf(
			'<div class="meta-item"><a href="%s"><time class="published" datetime="%s">%s</time></a></div>',
			get_permalink(),
			esc_attr(get_the_date('c')),
			get_the_date()
		);
		if($echo){ 
			print($date);
		}
		else{
			return $date;
		}
	}
endif;

if(!function_exists('fallsky_meta_view')) :
	/**
	 * Prints post meta date information for current post.
	 * Create your own function to override in a child theme.
	 * @since 1.0.0
	 */
	function fallsky_meta_view($class = ''){
		if(class_exists('Fallsky_Extension')){
			$views = apply_filters('loftocean_view_count_number', 0);
			printf(
				'<div class="meta-item%s">%s</div>',
				empty($class) ? '' : sprintf(' %s', $class),
				sprintf(
					esc_html__('%s %s', 'fallsky'),
					$views,
					preg_match('/[KM]/i', $views) || ($views > 1) ? esc_html__('Views', 'fallsky') : esc_html__('View', 'fallsky')
				)
			);
		}
	}
endif;

if(!function_exists('fallsky_meta_like')) :
	/**
	 * Prints post meta date information for current post.
	 * Create your own function to override in a child theme.
	 * @since 1.0.0
	 */
	function fallsky_meta_like($class = ''){
		if(class_exists('Fallsky_Extension')){
			$likes = apply_filters('loftocean_like_count_number', 0);
			printf(
				'<div class="meta-item%s">%s</div>',
				empty($class) ? '' : sprintf(' %s', $class),
				sprintf(
					esc_html__('%s %s', 'fallsky'),
					$likes,
					preg_match('/[KM]/i', $likes) || ($likes > 1) ? esc_html__('Likes', 'fallsky') : esc_html__('Like', 'fallsky')
				)
			);
		}
	}
endif;

if(!function_exists('fallsky_meta_comment')) :
	/**
	 * Prints post meta comment link.
	 *
	 * Create your own function to override in a child theme.
	 *
	 * @since 1.0.0
	 */
	function fallsky_meta_comment(){
		if(post_password_required()){
			return false;
		}
		else{
			$num = get_comments_number();
			if(comments_open() || $num){
				$url = is_singular() ? '' : sprintf('%s#comment-section', get_permalink(get_the_ID()));
				if($num == 0){
					printf('<div class="meta-item"><a href="%s">%s</a></div>', $url, esc_html__('Leave a comment', 'fallsky'));
				}
				else{
					printf(
						'<div class="meta-item"><a href="%s">%s</a></div>', 
						$url, 
						sprintf(esc_html(_n('%s Comment', '%s Comments', $num, 'fallsky')), $num)
					);
				}
			}
		}
	}
endif;

if(!function_exists('fallsky_meta_edit_link')) :
	/**
	 * Print link to edit a post or page.
	 *
	 * Create your own function to override in a child theme.
	 *
	 * @since 1.0.0
	*/
	function fallsky_meta_edit_link() {
		edit_post_link(
			esc_html__('Edit', 'fallsky'),
			'<div class="meta-item edit-link">',
			'</div>'
		);
	}
endif;

if(!function_exists('fallsky_page_edit_link')) :
	/**
	 * Print link to edit a page or attachment.
	 *
	 * Create your own function to override in a child theme.
	 *
	 * @since 1.0.0
	*/
	function fallsky_page_edit_link() {
		print('<footer class="post-meta">');
		fallsky_meta_edit_link();
		print('</footer>');
	}
endif;

if(!function_exists('fallsky_author_socials')) : 
	/**
	* Print or return author social list.
	* 	Create your own function to override in a child theme.
	* @param boolean to return or print the result
	*/
	function fallsky_author_socials($echo = true){
		ob_start();
		do_action('loftocean_user_social');
		$html = ob_get_clean();
		if(!empty($html)){
			$html = wp_kses_post($html);
			if($echo){
				print($html);
			}
			else{
				return $html;
			}
		}
	}
endif;

if(!function_exists('fallsky_get_post_gallery')) :
	/**
	 * Prints gallery html for post with post_format as gallery.
	 *
	 * Create your own function to override in a child theme.
	 *
	 * @since 1.0.0
	 */
	function fallsky_get_post_gallery(){
		if('gallery' == get_post_format()){
			$media = fallsky_get_gallery_post_media();
			if($media && has_shortcode($media, 'gallery')
				&& preg_match_all('/' . get_shortcode_regex() . '/s', $media, $matches, PREG_SET_ORDER)){
				// Get the gallery shortcode
				$wrap 		= '<div class="image-gallery">%s</div>';
				$item_wrap 	= '<div class="gallery-item%s">%s</div>';
				foreach($matches as $shortcode){
					if('gallery' === $shortcode[2]){
						$gallery 	= (array)shortcode_parse_atts($shortcode[3]);
						$html 		= '';
						$ids 		= array();
						if(!empty($gallery['ids'])){
							$ids = explode(',', $gallery['ids']);
						}
						else{
							$images = get_attached_media('image');
							foreach($images as $img){
								array_push($ids, $img->ID);
							}
						}
						if(!empty($ids)){
							$index = 0;
							foreach($ids as $id){
								$bg_html = fallsky_get_preload_bg( array( 'class' => '', 'id' => $id ) );
								if(!empty($bg_html)){
									$first = ($index++ === 0);
									$html .= sprintf(
										$item_wrap, 
										$first ? ' first' : '" style="display: none;', 
										$bg_html
									);
								}
							}
							if(!empty($html)){
								return sprintf($wrap, $html);
							}
						}
					}
				}
			}
		}
		return '';
	}
endif;

