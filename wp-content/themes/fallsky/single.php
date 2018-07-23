<?php 
/**
* Site main singple template
* @since 1.0.0
*/

get_header(); ?>

	<div>
		<?php get_template_part( 'template-parts/single', get_post_type() ); ?>
	</div>

<?php get_footer(); ?>