<?php
$loop_col 	= 3;
$loop_class = array('products', 'layout-grid');

$loop_col = intval(fallsky_get_theme_mod('fallsky_woocommerce_archive_layout'));
$loop_col = ($loop_col > 1 && $loop_col < 5) ? $loop_col : 3;
if('overlay' == fallsky_get_theme_mod('fallsky_woocommerce_archive_style')){
	$loop_class = array_merge($loop_class, array('style-overlay', esc_attr(fallsky_get_theme_mod('fallsky_woocommerce_archive_overlay_color_scheme'))));
}

$loop_name 	= '';
// For woocommerce version 3.3.0 and above
if(function_exists('wp_get_loop_prop')){
	$loop_name = wc_get_loop_prop('name');
}
else{
	global $woocommerce_loop;
	$loop_name = isset($woocommerce_loop['name']) ? $woocommerce_loop['name'] : '';
}
if(!empty($loop_name)){
	switch($loop_name){
		case 'up-sells':
		case 'related':
			$loop_col = 3;
			break;
		case 'cross-sells':
			$loop_col = 2;
			break;
	}	
}

array_push($loop_class, 'cols-' . $loop_col);
?>
<ul class="<?php echo implode(' ', $loop_class); ?>">
