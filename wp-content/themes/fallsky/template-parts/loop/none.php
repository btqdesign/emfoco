<?php
/**
* Loop template for nothing found
*/

$error_messge = is_search() 
	? esc_html__( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'fallsky' )
		: esc_html__( 'It looks like nothing was found. Maybe try a search?', 'fallsky' );
?>

	<article class="no-article">	
		<header class="post-header">
			<h2 class="post-title"><?php esc_html_e( 'Nothing Found', 'fallsky' ); ?></h2>
		</header>	
		<div class="post-entry">
			<p><?php print( $error_messge ); ?></p>
			<div class="search"><?php get_search_form(); ?></div>
		</div><!-- end of post-entry  -->
	</article>