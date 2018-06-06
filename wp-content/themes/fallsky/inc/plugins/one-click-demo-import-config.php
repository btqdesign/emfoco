<?php
function fallsky_ocdi_import_files () {
	$dir 		= trailingslashit( get_template_directory() ) . 'inc/ocdi/';
	$image 		= get_template_directory_uri() . '/inc/ocdi/%s/screenshot.jpg';
	$configs 	= array(
		array('name' => 'Default Demo 1', 	'content' => 'demo1', 			'path' => 'demo1'),
		array('name' => 'Default Demo 2', 	'content' => 'demo2', 			'path' => 'demo2'),
		array('name' => 'Default Demo 3', 	'content' => 'demo3', 			'path' => 'demo3'),
		array('name' => 'Default Demo 4', 	'content' => 'demo4', 			'path' => 'demo4'),
		array('name' => 'Travel Style 1', 	'content' => 'travel-style1', 	'path' => 'travel-style1'),
		array('name' => 'Travel Style 2', 	'content' => 'travel-style2', 	'path' => 'travel-style2')
	);
	$demos 		= array();
	foreach( $configs as $config ) {
		$content 	= $dir . $config[ 'content' ] . '/content.xml';
		$widgets 	= $dir . $config[ 'content' ] . '/widgets.wie';
		$customize 	= $dir . $config[ 'path' ] 	. '/customization.dat';
		$screenshot = sprintf( $image, $config[ 'path' ] );
		$demos[] = array(
			'import_file_name'             => $config[ 'name' ],
			'local_import_file'            => $content,
			'local_import_widget_file'     => $widgets,
			'import_preview_image_url'     => $screenshot,
			'local_import_customizer_file' => $customize
		);
	}
	return $demos;
}
add_filter( 'pt-ocdi/import_files', 'fallsky_ocdi_import_files' );


function fallsky_ocdi_after_import_setup( $selected_import ) {
	$demo 	= $selected_import['import_file_name'];
	$demo_folders = array( 
		'Default Demo 1' => 'demo1', 
		'Default Demo 2' => 'demo2', 
		'Default Demo 3' => 'demo3', 
		'Default Demo 4' => 'demo4',
		'Travel Style 1' => 'travel-style1',
		'Travel Style 2' => 'travel-style2'
	);
	$demos 	= array_keys( $demo_folders );
	if( !empty( $demo ) && in_array( $demo, $demos ) ) {
		$demos = array();
		$widgets_file = trailingslashit( get_template_directory() ) . 'inc/ocdi/' . $demo_folders[ $demo ] . '/homepage-widgets.dat';
		if( file_exists( $widgets_file ) ) {
			$homepage_widgets = OCDI\Helpers::data_from_file( $widgets_file );
			if( !is_wp_error( $homepage_widgets ) ) {
				$homepage_widgets = maybe_unserialize( $homepage_widgets );
				if( !empty( $homepage_widgets ) && is_array( $homepage_widgets ) ) {
					foreach( $homepage_widgets as $hwn => $hwv ) {
						update_option( $hwn, $hwv );
					}
				}
			}
		}
	}

	// Change the default mc4wp form id 
	$form_id = fallsky_get_default_mc4wp_form_id();
	if( !empty( $form_id ) ) {
		update_option( 'mc4wp_default_form_id', $form_id );
	}


	// Assign menus to their locations.
	$main_menu 		= get_term_by( 'name', 'Main Menu', 'nav_menu' );
	$social_menu 	= get_term_by( 'name', 'Social Menu', 'nav_menu' );

	set_theme_mod( 'nav_menu_locations', array(
		'primary' 	=> $main_menu->term_id,
		'social'	=> $social_menu->term_id,
	) );

	// Assign front page and posts page (blog page).
	$front_page_id = get_page_by_title( 'Homepage' );
	$blog_page_id  = get_page_by_title( 'Blog' );

	if( !empty( $front_page_id ) || !empty( $blog_page_id ) ) {
		update_option( 'show_on_front', 	'page' );
		if( !empty($front_page_id)){
			update_option('page_on_front', 	$front_page_id->ID);
		}
		if(!empty($blog_page_id)){
			update_option('page_for_posts', $blog_page_id->ID);
		}
	}
}
add_action('pt-ocdi/after_import', 'fallsky_ocdi_after_import_setup');

