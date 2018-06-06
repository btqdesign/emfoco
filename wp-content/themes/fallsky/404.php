<?php
/**
* Template for 404 page
*/
get_header(); ?>

		<div class="main">
			<div class="container">
				<div id="primary" class="content-area">
					<div class="container">
						<header class="page-404-page-header">
							<h1 class="page-title"><?php esc_html_e('404', 'fallsky'); ?></h1>
							<h2><?php esc_html_e('Page Not Found', 'fallsky'); ?></h2>
						</header>
						
						<div class="page-404-container">
							<p><?php esc_html_e('Sorry but we couldn\'t find the page you are looking for. It might have been moved or deleted. Perhaps searching can help.', 'fallsky'); ?></p>
							
							<div class="search"><?php get_search_form(); ?></div>
						</div>
					</div>
				</div>
			</div> 
		</div>

<?php get_footer(); ?>
