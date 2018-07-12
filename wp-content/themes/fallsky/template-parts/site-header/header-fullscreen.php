<?php
/**
* Theme fullscreen site header
*/
?>


	<div<?php fallsky_fullscreen_site_header_class(); ?>>
		<div class="fullscreen-bg"></div>
		<div class="container">
			<span class="close-button"><?php esc_html_e( 'Close', 'fallsky' ); ?></span>
			<?php do_action( 'fallsky_fullscreen_site_header_content' ); ?>
		</div>
	</div>