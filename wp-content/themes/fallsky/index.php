<?php 
/**
* Site main template
*/

get_header(); ?>

	<div class="main">
		<div class="container">
			<div id="primary" class="content-area"><?php fallsky_main_content(); ?></div>
			<?php get_sidebar(); ?>
		</div> 
	</div>

<?php get_footer(); ?>