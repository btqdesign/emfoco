<?php
	add_action('wp_enqueue_scripts', 'fallsky_child_enqueue_scripts');
	function fallsky_child_enqueue_scripts(){
		wp_enqueue_style('fallsky-child-theme-style', get_stylesheet_uri(), array('fallsky-theme-style'));
	}

	add_filter('fallsky_inline_style_handler', 'fallsky_child_inline_style_handler', 999);
	function fallsky_child_inline_style_handler($handler){
		return 'fallsky-child-theme-style';
	}





add_action( 'init', 'my_custom_init' );
function my_custom_init() {
	
	$labels = array(
	'name' => _x( 'Productores', 'post type general name' ),
        'singular_name' => _x( 'Productor', 'post type singular name' ),
        'add_new' => _x( 'Añadir nuevo', 'productor' ),
        'add_new_item' => __( 'Añadir nuevo Productor' ),
        'edit_item' => __( 'Editar Productor' ),
        'new_item' => __( 'Nuevo Productor' ),
        'view_item' => __( 'Ver Productor' ),
        'search_items' => __( 'Buscar Productores' ),
        'not_found' =>  __( 'No se han encontrado Productores' ),
        'not_found_in_trash' => __( 'No se han encontrado Productores en la papelera' ),
        'parent_item_colon' => ''
    );
 
    $args = array( 'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
    );
 
    register_post_type( 'productor', $args ); 
}


add_action( 'init', 'create_book_taxonomies', 0 );
 
function create_book_taxonomies() {
	$labels = array(
	'name' => _x( 'Sectores', 'taxonomy general name' ),
	'singular_name' => _x( 'Sector', 'taxonomy singular name' ),
	'search_items' =>  __( 'Buscar por Sector' ),
	'all_items' => __( 'Todos los Sectores' ),
	'parent_item' => __( 'Sector padre' ),
	'parent_item_colon' => __( 'Sector padre:' ),
	'edit_item' => __( 'Editar Sector' ),
	'update_item' => __( 'Actualizar Sector' ),
	'add_new_item' => __( 'Añadir nuevo Sector' ),
	'new_item_name' => __( 'Nombre del nuevo Sector' ),
);
register_taxonomy( 'sector', array( 'productor' ), array(
	'hierarchical' => true,
	'labels' => $labels, 
	'show_ui' => true,
	'query_var' => true,
	'rewrite' => array( 'slug' => 'Sector' ),
));

}






function categorias_add_new_meta_fields(){
	?>
	<div>
            <label for="term_meta[imagen]">
               <input type="text" name="term_meta[imagen]" size="36" id="upload_image" value=""><br>
               <input id="upload_image_button" type="button" class='button button-primary' value="Subir Imagen" />
               <br/><i>Introduce una URL o establece una imagen para este campo.</i>
            </label>
	</div>
	<?php
}
add_action( 'sector_add_form_fields', 'categorias_add_new_meta_fields', 10, 2 );


function categorias_edit_meta_fields($term){
	$t_id = $term->term_id;

	$term_meta = get_option("taxonomy_$t_id");
	?>
		<tr valign="top" class='form-field'>
			<th scope="row">Subir imagen</th>
			<td>
				<label for="upload_image">
				    <input id="upload_image" type="text" size="36" name="term_meta[imagen]" value="<?php if( esc_attr( $term_meta['imagen'] ) != "") echo esc_attr( $term_meta['imagen'] ) ; ?>" />
				    <p><input id="upload_image_button" type="button" class='button button-primary' style='width: 100px' value="Subir Imagen" />
				    <i>Introduce una URL o establece una imagen para este campo.</i></p>
				</label>
				<p><?php if( esc_attr( $term_meta['imagen'] ) != "" ) echo "<table><tr><td><i><strong>Imagen actual</strong></i>:</td><td> <img src='".esc_attr( $term_meta['imagen'] )."'></td></tr></table>"; ?></p>
			</td>
		</tr>
	<?php
}
add_action( 'sector_edit_form_fields', 'categorias_edit_meta_fields', 10, 2 );



function categorias_save_custom_meta( $term_id ) {
	if ( isset( $_POST['term_meta'] ) ) {
		$t_id = $term_id;
		$term_meta = get_option( "taxonomy_$t_id" );
		$cat_keys = array_keys( $_POST['term_meta'] );
		foreach ( $cat_keys as $key ) {
			if ( isset ( $_POST['term_meta'][$key] ) ) {
				$term_meta[$key] = $_POST['term_meta'][$key];
			}
		}
		update_option( "taxonomy_$t_id", $term_meta );
	}
}  
add_action( 'edited_sector', 'categorias_save_custom_meta', 10, 2 );  
add_action( 'create_sector', 'categorias_save_custom_meta', 10, 2 );


add_action('admin_enqueue_scripts', 'admin_scripts');

function admin_scripts() {
	if (isset($_GET[‘taxonomy’]) && $_GET[‘taxonomy’] == ‘sector’) {
        wp_enqueue_media();
        wp_register_script('admin-js', WP_PLUGIN_URL.'admin.js', array('jquery'));
        wp_enqueue_script('admin-js');
    }
}

foreach($categorias as $cat){
    $c_id = $cat->term_id;
    $term_meta = get_option("taxonomy_$c_id");
    echo("<a href='".get_term_link($c_id,"sector")."'><img src='".$term_meta['imagen']."'></a>");
}






	class Fallsky_Meta_Boxes {
		// Page
		private $default_page_layout	= 'page-header-layout-2';
		private $page_layout_options 	= array();
		private $option_keys 			= array();
		private $page_layouts 			= array();

		// Post
		private $post_default_layout 	= '';
		private $post_default_template 	= '';
		private $post_layout_options 	= array();
		private $post_layout_keys 		= array();
		private $post_template_options 	= array();
		private $post_template_keys 	= array();
		private $post_templates 		= array();
		private $post_layouts 			= array();
		public function __construct(){
			$this->page_layout_options 	= array(
				'page-header-layout-1' => esc_html__('Layout 1', 'fallsky'),
				'page-header-layout-2' => esc_html__('Layout 2', 'fallsky')
			);
			$this->option_keys 			= array_keys($this->page_layout_options);

			$this->post_layout_options 	= array(
				'' 						=> esc_html__('Default', 'fallsky'),
				'no-sidebar'			=> esc_html__('No Sidebar', 'fallsky'),
				'with-sidebar-left'		=> esc_html__('Left Sidebar', 'fallsky'),
				'with-sidebar-right'	=> esc_html__('Right Sidebar', 'fallsky')
			);
			$this->post_layout_keys 		= array_keys($this->post_layout_options);
			$this->post_template_options 	= array(
				'' 					=> esc_html__('Default', 'fallsky'),
				'post-template-1' 	=> esc_html__('Post Template 1', 'fallsky'),
				'post-template-2' 	=> esc_html__('Post Template 2', 'fallsky'),
				'post-template-3' 	=> esc_html__('Post Template 3', 'fallsky'),
				'post-template-4' 	=> esc_html__('Post Template 4', 'fallsky'),
				'post-template-5' 	=> esc_html__('Post Template 5', 'fallsky'),
				'post-template-6' 	=> esc_html__('Post Template 6', 'fallsky'),
				'post-template-7' 	=> esc_html__('Post Template 7', 'fallsky')
			);
			$this->post_template_keys 	= array_keys($this->post_template_options);

			add_action( 'add_meta_boxes', 						array( $this, 'register_meta_boxes' ) );
			add_action( 'save_post', 							array( $this, 'save_page_meta' ), 10, 3 );
			add_action( 'loftocean_post_metabox_before_html', 	array( $this, 'post_metabox' ) );
			add_action( 'loftocean_save_post', 					array( $this, 'save_post_meta' ), 10, 1 );
			add_action( 'admin_enqueue_scripts', 				array( $this, 'enqueue_scripts' ) );
			add_filter( 'fallsky_get_page_header_layout', 		array( $this, 'get_page_header_layout' ) );
			add_filter( 'fallsky_get_post_template',		 	array( $this, 'get_post_template' ) );
			add_filter( 'fallsky_get_post_sidebar_layout', 		array( $this, 'get_post_sidebar_layout' ) );
			add_filter( 'loftocean_post_metabox_title',			function($title){ 
				return esc_html__('Fallsky Single Post Options', 'fallsky'); 
			});
		}
		// Register loftloader shortcode meta box
		public function register_meta_boxes(){ 
			global $post; 
			if($post && in_array($post->post_type, array('page'))){
				$pid 			= $post->ID;
				$pages 			= array(get_option('fallsky_category_index_page_id'));
				$front_page_id 	= fallsky_get_page_on_front();

				if('page' == get_option('show_on_front', 'posts')){
					array_push($pages, get_option('page_for_posts'));
				}
				if(!empty($front_page_id)){
					array_push($pages, $front_page_id);
				}
				$pages = apply_filters('fallsky_static_pages', $pages);


				if(!in_array($pid, $pages)){
					add_meta_box('fallsky_page_meta_box', esc_html__('Fallsky Single Page Options', 'fallsky'), array($this, 'page_metabox'), 'page', 'advanced');
				}
			}
		}
		// Show meta box html
		public function post_metabox($post){
			$pid 		= $post->ID;
			$template 	= esc_attr(get_post_meta($pid, 'fallsky_single_post_template', true));
			$layout 	= esc_attr(get_post_meta($pid, 'fallsky_single_post_sidebar', true));
			if(!in_array($template, $this->post_template_keys)){
				$template = $this->post_default_template;
			}
			if(!in_array($layout, $this->post_layout_keys)){
				$layout = $this->post_default_layout;
			} ?>

			<p>
				<label for="fallsky_single_post_template"><?php esc_html_e('Post Template:', 'fallsky'); ?></label>
				<select name="fallsky_single_post_template" id="fallsky_single_post_template">
					<?php foreach($this->post_template_options as $ti => $tv){ printf('<option value="%s" %s>%s</option>', $ti, selected($ti, $template, false), $tv); } ?>
				</select>
			</p>
			<p>
				<label for="fallsky_single_post_sidebar"><?php esc_html_e('Sidebar Layout:', 'fallsky'); ?></label>
				<select name="fallsky_single_post_sidebar" id="fallsky_single_post_sidebar">
					<?php foreach($this->post_layout_options as $li => $lv){ printf('<option value="%s" %s>%s</option>', $li, selected($li, $layout, false), $lv); } ?>
				</select>
			</p> <?php
		}
		// Save post meta
		public function save_post_meta($post_id){
			$template = isset($_REQUEST['fallsky_single_post_template']) ? $_REQUEST['fallsky_single_post_template'] : '';
			$template = in_array($template, $this->post_template_keys) && !empty($template) ? $template : $this->post_default_template;
			update_post_meta($post_id, 'fallsky_single_post_template', $template);

			$layout = isset($_REQUEST['fallsky_single_post_sidebar']) ? $_REQUEST['fallsky_single_post_sidebar'] : '';
			$layout = in_array($layout, $this->post_layout_keys) && !empty($layout) ? $layout : $this->post_default_layout;
			update_post_meta($post_id, 'fallsky_single_post_sidebar', $layout);
		}
		// Show meta box html
		public function page_metabox($post){
			$layout 		= apply_filters( 'fallsky_get_page_header_layout', '' ); 
			$layouts 		= $this->page_layout_options; 
			$before_ads  	= get_post_meta($post->ID, 'fallsky_hide_before_page_content_ad', true);
			$after_ads  	= get_post_meta($post->ID, 'fallsky_hide_after_page_content_ad', true); ?>

			<input type="hidden" name="fallsky_nonce" value="<?php echo wp_create_nonce('fallsky_nonce'); ?>" />
			<p>
				<label for="fallsky_page_header_layout"><?php esc_html_e('Page Header Layout:', 'fallsky'); ?></label>
				<select name="fallsky_page_header_layout" id="fallsky_page_header_layout">
					<?php foreach($layouts as $i => $l){ printf('<option value="%s" %s>%s</option>', $i, selected($i, $layout, false), $l); } ?>
				</select>
			</p> 
			<p>
				<label><?php esc_html_e('Hide AD:', 'fallsky'); ?></label>
				<input type="checkbox" id="fallsky_hide_before_page_content_ad" name="fallsky_hide_before_page_content_ad" value="on" <?php checked('on', $before_ads); ?>>
				<label class="checkbox-label" for="fallsky_hide_before_page_content_ad"><?php esc_html_e('Hide advertisement before page content', 'fallsky'); ?></label>
			</p>
			<p>
				<label><?php esc_html_e('Hide AD:', 'fallsky'); ?></label>
				<input type="checkbox" id="fallsky_hide_after_page_content_ad" name="fallsky_hide_after_page_content_ad" value="on" <?php checked('on', $after_ads); ?>>
				<label class="checkbox-label" for="fallsky_hide_after_page_content_ad"><?php esc_html_e('Hide advertisement after page content', 'fallsky'); ?></label>
			</p><?php

		}
		// Save loftloader shortcode meta
		public function save_page_meta($post_id, $post, $update){
			if(empty($update) || !in_array($post->post_type, array('page'))){
				return;
			}

			if(wp_verify_nonce($_REQUEST['fallsky_nonce'], 'fallsky_nonce') !== false){
				$layout = isset($_REQUEST['fallsky_page_header_layout']) ? sanitize_text_field($_REQUEST['fallsky_page_header_layout']) : '';
				$layout = in_array($layout, $this->option_keys) ? $layout : $this->default_page_layout;
				update_post_meta($post_id, 'fallsky_page_header_layout', $layout);

				$hide_before_ad = empty($_REQUEST['fallsky_hide_before_page_content_ad']) ? '' : 'on';
				update_post_meta($post_id, 'fallsky_hide_before_page_content_ad', $hide_before_ad);

				$hide_after_ad = empty($_REQUEST['fallsky_hide_after_page_content_ad']) ? '' : 'on';
				update_post_meta($post_id, 'fallsky_hide_after_page_content_ad', $hide_after_ad);
			}
		}
		/**
		* Get page header layout
		* @param string default layout
		* @return changed layout
		*/
		public function get_page_header_layout($layout){
			global $post;
			$pid = $post->ID;
			if(!isset($this->page_layouts[$pid])){
				$layout = get_post_meta($post->ID, 'fallsky_page_header_layout', true);
				$this->page_layouts[$pid] = in_array($layout, $this->option_keys) ? $layout : $this->default_page_layout;
			}
			return $this->page_layouts[$pid];
		}
		/**
		* Get page header layout
		* @param string default layout
		* @param wp_post object page
		* @return changed layout
		*/
		public function get_post_template($template){
			global $post;
			$pid = $post->ID; 
			if(!isset($this->post_templates[$pid])){
				$template = get_post_meta($post->ID, 'fallsky_single_post_template', true); 
				$this->post_templates[$pid] = in_array($template, $this->post_template_keys) ? $template : $this->post_default_template;
			}
			return $this->post_templates[$pid];
		}
		/**
		* Get page header layout
		* @param string default layout
		* @param wp_post object page
		* @return changed layout
		*/
		public function get_post_sidebar_layout($layout){
			global $post;
			$pid = $post->ID;
			if(!isset($this->post_layouts[$pid])){
				$layout = get_post_meta($post->ID, 'fallsky_single_post_sidebar', true);
				$this->post_layouts[$pid] = in_array($layout, $this->post_layout_keys) ? $layout : $this->post_default_layout;
			}
			return $this->post_layouts[$pid];
		}
		/**
		* Enqueue admin post metabox style
		*/
		public function enqueue_scripts(){
			wp_enqueue_style('fallsky-meta-box', FALLSKY_ASSETS_URI . 'css/admin/fallsky-meta-box.css', array(), FALLSKY_ASSETS_VERSION);
		}
	}
	new Fallsky_Meta_Boxes();




 





