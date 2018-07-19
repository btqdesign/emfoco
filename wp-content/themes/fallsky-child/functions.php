<?php
	add_action('wp_enqueue_scripts', 'fallsky_child_enqueue_scripts');
	function fallsky_child_enqueue_scripts(){
		wp_enqueue_style('fallsky-child-theme-style', get_stylesheet_uri(), array('fallsky-theme-style'));
	}

	add_filter('fallsky_inline_style_handler', 'fallsky_child_inline_style_handler', 999);
	function fallsky_child_inline_style_handler($handler){
		return 'fallsky-child-theme-style';
	}



	function create_posttype() {

		$labels = array(
			'name'                => _x( 'Productores', 'Post Type General Name', 'twentythirteen' ),
			'singular_name'       => _x( 'Productor', 'Post Type Singular Name', 'twentythirteen' ),
			'menu_name'           => __( 'Productores', 'twentythirteen' ),
			'parent_item_colon'   => __( 'Prodcutor padre', 'twentythirteen' ),
			'all_items'           => __( 'Todos los productores', 'twentythirteen' ),
			'view_item'           => __( 'Ver productor', 'twentythirteen' ),
			'add_new_item'        => __( 'Añadir nuevo productor', 'twentythirteen' ),
			'add_new'             => __( 'Añadir nuevo', 'twentythirteen' ),
			'edit_item'           => __( 'Editar productor', 'twentythirteen' ),
			'update_item'         => __( 'Actualizar productor', 'twentythirteen' ),
			'search_items'        => __( 'Buscar productor', 'twentythirteen' ),
			'not_found'           => __( 'No se encontro', 'twentythirteen' ),
			'not_found_in_trash'  => __( 'No se encontro en la', 'twentythirteen' ),
		);
	
		register_post_type( 'Productores',
				// CPT Options
					array(
						'labels' => array(
							'name' => __( 'Productores' ),
							'singular_name' => __( 'Productor' )
						),
						'public'              => true,
						'has_archive'         => true,
						'show_ui'             => true,
						'show_in_menu'        => true,
						'show_in_nav_menus'   => true,
						'show_in_admin_bar'   => true,
						'menu_position'       => 5,
						'can_export'          => true,
						'has_archive'         => true,
						'exclude_from_search' => false,
						'publicly_queryable'  => true,
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
 
 register_taxonomy('Sectores',array('post'), array(
	 'hierarchical' => true,
	 'labels' => $labels,
	 'show_ui' => true,
	 'show_admin_column' => true,
	 'query_var' => true,
	 'rewrite' => array( 'slug' => 'Sector' ),
 ));
 
 }

 $args = array(
 'public'   => true,
 '_builtin' => false
 
 ); 





