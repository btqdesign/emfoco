<?php
/**
* Main sidebar template
*/

$sidebar_id = apply_filters( 'fallsky_get_sidebar_id', 'main-sidebar' );

if ( ! empty( $sidebar_id ) && apply_filters( 'fallsky_show_site_sidebar', true ) && is_active_sidebar( $sidebar_id ) ) : ?>
	<!-- .sidebar .widget-area -->
	<aside id="secondary"<?php fallsky_site_sidebar_class(); fallsky_site_sidebar_attrs(); ?>>
		<!-- .sidebar-container -->
		<div class="sidebar-container">
			<?php dynamic_sidebar( $sidebar_id ); ?>
		</div> <!-- end of .sidebar-container -->
	</aside><!-- .sidebar .widget-area -->
<?php endif; ?>