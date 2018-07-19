<?php
	add_action('wp_enqueue_scripts', 'fallsky_child_enqueue_scripts');
	function fallsky_child_enqueue_scripts(){
		wp_enqueue_style('fallsky-child-theme-style', get_stylesheet_uri(), array('fallsky-theme-style'));
	}

	add_filter('fallsky_inline_style_handler', 'fallsky_child_inline_style_handler', 999);
	function fallsky_child_inline_style_handler($handler){
		return 'fallsky-child-theme-style';
	}



// La función no será utilizada antes del 'init'.
add_action( 'init', 'my_custom_init' );
/* Here's how to create your customized labels */
function my_custom_init() {
	$labels = array(
	'name' => _x( 'Libros', 'post type general name' ),
        'singular_name' => _x( 'Libro', 'post type singular name' ),
        'add_new' => _x( 'Añadir nuevo', 'book' ),
        'add_new_item' => __( 'Añadir nuevo Libro' ),
        'edit_item' => __( 'Editar Libro' ),
        'new_item' => __( 'Nuevo Libro' ),
        'view_item' => __( 'Ver Libro' ),
        'search_items' => __( 'Buscar Libros' ),
        'not_found' =>  __( 'No se han encontrado Libros' ),
        'not_found_in_trash' => __( 'No se han encontrado Libros en la papelera' ),
        'parent_item_colon' => ''
    );
 
    // Creamos un array para $args
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
 
    register_post_type( 'libro', $args ); /* Registramos y a funcionar */
}







 // Lo enganchamos en la acción init y llamamos a la función create_book_taxonomies() cuando arranque
add_action( 'init', 'create_book_taxonomies', 0 );
 
// Creamos dos taxonomías, género y autor para el custom post type "libro"
function create_book_taxonomies() {
	// Añadimos nueva taxonomía y la hacemos jerárquica (como las categorías por defecto)
	$labels = array(
	'name' => _x( 'Géneros', 'taxonomy general name' ),
	'singular_name' => _x( 'Género', 'taxonomy singular name' ),
	'search_items' =>  __( 'Buscar por Género' ),
	'all_items' => __( 'Todos los Géneros' ),
	'parent_item' => __( 'Género padre' ),
	'parent_item_colon' => __( 'Género padre:' ),
	'edit_item' => __( 'Editar Género' ),
	'update_item' => __( 'Actualizar Género' ),
	'add_new_item' => __( 'Añadir nuevo Género' ),
	'new_item_name' => __( 'Nombre del nuevo Género' ),
);
register_taxonomy( 'genero', array( 'libro' ), array(
	'hierarchical' => true,
	'labels' => $labels, /* ADVERTENCIA: Aquí es donde se utiliza la variable $labels */
	'show_ui' => true,
	'query_var' => true,
	'rewrite' => array( 'slug' => 'genero' ),
));



