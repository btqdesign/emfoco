<?php
/**
* Loop template for post list with layout card
* @global $fallsky_list_args global  arguments for loop
*	1. Read More button text
*	2. Show which post meta will show
* 	
*/
global $fallsky_list_args;
$post_meta 		= isset( $fallsky_list_args['post_meta'] ) ? $fallsky_list_args['post_meta'] : array();
$column			= isset( $fallsky_list_args['column'] ) ? $fallsky_list_args['column'] : false;
$post_class 	= get_post_class();
$post_url 		= get_permalink();
$footer_meta 	= array_intersect( $post_meta, array( 'author', 'date', 'view', 'like', 'comment' ) );
?>
	<article class="<?php echo implode( ' ', $post_class ); ?>">
		<?php if ( in_array( 'sticky', $post_class ) ) : ?><div class="sticky-icon"></div><?php endif; ?>
		<?php fallsky_list_featured_section( 1 == $column ); ?>
		
		<div class="post-content">
			<header class="post-header">
				<h2 class="post-title">
					<a href="<?php print( $post_url ); ?>"><?php the_title(); ?></a>
				</h2>
				<?php if ( in_array( 'category', $post_meta ) ){ fallsky_get_post_categories(); } ?>
			</header>
			
			<?php if ( in_array( 'excerpt', $post_meta ) ) { fallsky_meta_excerpt(); } ?>

			<?php if ( ! empty( $footer_meta ) ) : ?>
			<footer class="post-meta">
				<?php if ( in_array( 'author', 	$footer_meta ) ) { fallsky_meta_author(); } ?>
				<?php if ( in_array( 'date', 	$footer_meta ) ) { fallsky_meta_date(); } ?>
				<?php if ( in_array( 'view', 	$footer_meta ) ) { fallsky_meta_view(); } ?>
				<?php if ( in_array( 'like', 	$footer_meta ) ) { fallsky_meta_like(); } ?>
				<?php if ( in_array( 'comment', $footer_meta ) ) { fallsky_meta_comment(); } ?>
			</footer>
			<?php endif; ?>

			<?php if ( $fallsky_list_args['show_read_more_btn'] ) : ?>
			<div class="more-btn">
				<a class="read-more-btn" href="<?php print( $post_url ); ?>"><span><?php print( $fallsky_list_args['read_more'] ); ?></span></a>
			</div>
			<?php endif; ?>
		</div>
	</article>