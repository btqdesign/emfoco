<!DOCTYPE html>
<html <?php language_attributes(); ?> class="<?php echo wp_is_mobile() ? 'mobile ' : ''; ?>no-js no-svg">
	<head>
		<meta charset="<?php bloginfo('charset'); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="profile" href="//gmpg.org/xfn/11">
		<?php if( is_singular() && pings_open( get_queried_object() ) ) : ?>
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
		<?php endif; ?>
		<?php wp_head(); ?>
	</head>

	<body <?php body_class(); ?>>
		<?php do_action( 'fallsky_ads', 'site_top' ); ?>
		<div id="page">
			<?php do_action( 'fallsky_site_header' ); ?>
			<?php is_singular( 'post' ) ? do_action( 'fallsky_post_nav' ) : ''; ?>

			<!-- #content -->
			<div id="content" <?php fallsky_content_class(); ?>>
				<?php do_action( 'fallsky_before_main_content' );  ?>