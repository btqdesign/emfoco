<?php

add_filter('final_output', function($output) {
	// Soporte HTTPS
	$output = str_replace('http:', 'https:', $output);
	$output = str_replace('https://schemas.xmlsoap.org', 'http://schemas.xmlsoap.org', $output);
	$output = str_replace('https://docs.oasisopen.org', 'http://docs.oasisopen.org', $output);
	return $output;
});

	add_action('wp_enqueue_scripts', 'fallsky_child_enqueue_scripts');
	function fallsky_child_enqueue_scripts(){
		wp_enqueue_style('fallsky-child-theme-style', get_stylesheet_uri(), array('fallsky-theme-style'));
	}

	add_filter('fallsky_inline_style_handler', 'fallsky_child_inline_style_handler', 999);
	function fallsky_child_inline_style_handler($handler){
		return 'fallsky-child-theme-style';
	}
