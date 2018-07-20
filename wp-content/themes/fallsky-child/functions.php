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







