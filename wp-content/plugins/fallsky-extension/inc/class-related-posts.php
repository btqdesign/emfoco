<?php
// Get the realted posts by category or tag
if(!class_exists('LoftOcean_Related_Posts')){
	class LoftOcean_Related_Posts{
		function __construct(){
			add_filter('loftocean_related_posts', array($this, 'related_posts'), 10, 3);
		}
		/**
		* Get related posts
		* @param object WP_Query results
		* @param string term type, category or tag ...
		* @param number posts number needed, default to 3
		* @return object WP_Qurey results 
		*/
		public function related_posts($related, $term_type, $ppp = 4){
			$term_types = array('category', 'tag', 'author');
			if(!empty($term_type) && in_array($term_type, $term_types) && (intval($ppp) > 0)){ 
				$args 		= array('posts_per_page'=> intval($ppp), 'post__not_in'=> array(get_the_ID()), 'orderby' => 'rand');
				$cats 		= wp_get_post_categories(get_the_ID(), array('fields' => 'ids'));
				$tags 		= wp_get_post_tags(get_the_ID(), array('fields' => 'ids'));
				$author_id 	= get_the_author_meta('ID');
				$run_query 	= false;
				switch($term_type){
					case 'category':
						if(!empty($cats)){
							$run_query = true;
							$args['category__in'] = $cats;
						}
						break;
					case 'tag':
						if(!empty($tags)){
							$run_query = true;
							$args['tag__in'] = $tags;
						}
						break;
					default:
						if(!empty($author_id)){
							$run_query = true;
							$args['author'] = $author_id;
						}
				}
				$related = $run_query ? new WP_Query($args) : $related;
			}
			return $related;
		}
	}
	new LoftOcean_Related_Posts();
}