<?php
/**
 * Plugin Name: BTQ emfoco
 * Plugin URI: https://hotel.idevol.net/wp-content/plugins/plugin%20emfoco/emfoco.php
 * Description: Creador de post master
 * Version: 1.0
 * Author: BTQ Design
 * Author URI: http://btqdesign.com/
 * Requires at least: 4.9.6
 * Tested up to: 4.9.6
 * 
 * Text Domain: btq-emfoco
 * Domain Path: /languages
 * 
 * @package btq-emfoco
 * @category Core
 * @author BTQ Design
 */


// Exit if accessed directly
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Declara el Widget de BTQ Login en VisualCompouser.
 *
 * @author José Antonio del Carmen
 * @return void Widget de BTQ popup en VisualCompouser.
 */
function btq_emfoco_VC() {
	vc_map(array(
    'name'     => __( 'BTQ emfoco', 'btq-emfoco' ),
		'base'     => 'btq-emfoco',
		'class'    => '',
		'category' => __( 'Content', 'btq-emfoco'),
		'icon'     => plugins_url( 'assets/images/iconos' . DIRECTORY_SEPARATOR . 'btqdesign-logo.png', __FILE__ )
	));
}
add_action( 'vc_before_init', 'btq_emfoco_VC' );



function create_posttype() {
    register_post_type( 'Productores',
            // CPT Options
                array(
                    'labels' => array(
                        'name' => __( 'Productores' ),
                        'singular_name' => __( 'Productor' )
                    ),
                    'public' => true,
                    'has_archive' => true,
                    'rewrite' => array('slug' => 'Productor'),
                )
            );
        }
    add_action( 'init', 'create_posttype' );




    //hook into the init action and call create_book_taxonomies when it fires
add_action( 'init', 'create_topics_hierarchical_taxonomy', 0 );
 
//create a custom taxonomy name it topics for your posts
 
function create_topics_hierarchical_taxonomy() {
 
// Add new taxonomy, make it hierarchical like categories
//first do the translations part for GUI
 
  $labels = array(
    'name' => _x( 'Sectores', 'taxonomy general name' ),
    'singular_name' => _x( 'Sector', 'taxonomy singular name' ),
    'search_items' =>  __( 'Buscar en: sectores' ),
    'all_items' => __( 'Todos los sectores' ),
    'parent_item' => __( 'Sector padre' ),
    'parent_item_colon' => __( 'Sector padre:' ),
    'edit_item' => __( 'Editar Sector' ), 
    'update_item' => __( 'Actualizar Sector' ),
    'add_new_item' => __( 'Añadir Sector' ),
    'new_item_name' => __( 'Nuevo nombres del Sector' ),
    'menu_name' => __( 'Sectores' ),
  );    
 
// Now register the taxonomy
 
  register_taxonomy('topics',array('post'), array(
    'hierarchical' => true,
    'labels' => $labels,
    'show_ui' => true,
    'show_admin_column' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'Sector' ),
  ));
 
}



/*
* Creating a function to create our CPT
*/
 
function custom_post_type() {
 
    // Set UI labels for Custom Post Type
        $labels = array(
            'name'                => _x( 'Movies', 'Post Type General Name', 'twentythirteen' ),
            'singular_name'       => _x( 'Movie', 'Post Type Singular Name', 'twentythirteen' ),
            'menu_name'           => __( 'Movies', 'twentythirteen' ),
            'parent_item_colon'   => __( 'Parent Movie', 'twentythirteen' ),
            'all_items'           => __( 'All Movies', 'twentythirteen' ),
            'view_item'           => __( 'View Movie', 'twentythirteen' ),
            'add_new_item'        => __( 'Add New Movie', 'twentythirteen' ),
            'add_new'             => __( 'Add New', 'twentythirteen' ),
            'edit_item'           => __( 'Edit Movie', 'twentythirteen' ),
            'update_item'         => __( 'Update Movie', 'twentythirteen' ),
            'search_items'        => __( 'Search Movie', 'twentythirteen' ),
            'not_found'           => __( 'Not Found', 'twentythirteen' ),
            'not_found_in_trash'  => __( 'Not found in Trash', 'twentythirteen' ),
        );
         
    // Set other options for Custom Post Type
         
        $args = array(
            'label'               => __( 'movies', 'twentythirteen' ),
            'description'         => __( 'Movie news and reviews', 'twentythirteen' ),
            'labels'              => $labels,
            // Features this CPT supports in Post Editor
            'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
            // You can associate this CPT with a taxonomy or custom taxonomy. 
            'taxonomies'          => array( 'genres' ),
            /* A hierarchical CPT is like Pages and can have
            * Parent and child items. A non-hierarchical CPT
            * is like Posts.
            */ 
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => 5,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'page',
        );
         
        // Registering your Custom Post Type
        register_post_type( 'movies', $args );
     
    }
     
    /* Hook into the 'init' action so that the function
    * Containing our post type registration is not 
    * unnecessarily executed. 
    */
     
    add_action( 'init', 'custom_post_type', 0 );