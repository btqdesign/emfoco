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
 
        register_post_type( 'Sectores',
        // CPT Options
            array(
                'labels' => array(
                    'name' => __( 'Sectores' ),
                    'singular_name' => __( 'Sector' )
                ),
                'public' => true,
                'has_archive' => true,
                'rewrite' => array('slug' => 'Sectores'),
            )
        );
    }
    // Hooking up our function to theme setup
    add_action( 'init', 'create_posttype' );
     

    function sectores_init() {
        // create a new taxonomy
        register_taxonomy(
            'Sectores',
            'post',
            array(
                'label' => __( 'Sectores' ),
                'rewrite' => array( 'slug' => 'Sector' ),
                'capabilities' => array(
                    'assign_terms' => 'edit_guides',
                    'edit_terms' => 'publish_guides'
                )
            )
        );
    }
    add_action( 'init', 'sectores_init' );



    //hook into the init action and call create_book_taxonomies when it fires
add_action( 'init', 'create_topics_hierarchical_taxonomy', 0 );
 
//create a custom taxonomy name it topics for your posts
 
function create_topics_hierarchical_taxonomy() {

// Now register the taxonomy
 
  register_taxonomy('topics',array('post'), array(
    'hierarchical' => true,
    'labels' => $labels,
    'show_ui' => true,
    'show_admin_column' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'topic' ),
  ));
 
}