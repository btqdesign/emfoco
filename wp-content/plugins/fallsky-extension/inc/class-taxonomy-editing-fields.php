<?php
/**
* Add tax image field for Category and Post Tag editing page
*/

if( !class_exists( 'LoftOcean_Taxonomy_Editing' ) ) {
	class LoftOcean_Taxonomy_Editing {
		private $text 	= array();
		private $tax 	= array();
		public function __construct() {
			$this->tax 	= array( 'category', 'post_tag' );
			$this->text	= array(
				'choose' 	=> esc_html__( 'Choose Image', 'loftocean' ),
				'remove'	=> esc_html__( 'Remove Image', 'loftocean' ),
				'label' 	=> array(
					'category' => esc_html__( 'Category Image', 'loftocean' ),
					'post_tag' => esc_html__( 'Post Tag Image', 'loftocean' )
				),
				'description' => array(
					'category' => esc_html__( 'Image for Category', 'loftocean' ),
					'post_tag' => esc_html__( 'Image for Post Tag', 'loftocean' )
				)
			);

			add_action( 'edited_term', 							array( $this, 'save_tax_fileds' ), 10, 3 );
			add_action( 'created_term', 						array( $this, 'save_tax_fileds' ), 10, 3 );
			add_action( 'category_add_form_fields', 			array( $this, 'add_taxonomy_fields' ) );
			add_action( 'category_edit_form_fields',		 	array( $this, 'edit_taxonomy_fields' ) );
			add_action( 'post_tag_add_form_fields', 			array( $this, 'add_taxonomy_fields' ) );
			add_action( 'post_tag_edit_form_fields', 			array( $this, 'edit_taxonomy_fields' ) );
			add_action( 'admin_print_scripts-edit-tags.php', 	array( $this, 'enqueue_scripts' ) );
			add_action( 'admin_print_scripts-term.php', 		array( $this, 'enqueue_scripts' ) );

			add_filter('loftocean_get_taxonomy_image_bg',	array( $this, 'get_taxonomy_image_bg' ), 10, 4 );
		}
		/**
		* Add a image uploader for category/post_tag edit page
		* @param object term
		*/
		public function edit_taxonomy_fields($tag){
			$tax 		= array('category', 'post_tag');
			$taxonomy 	= $tag->taxonomy;
			if(in_array($taxonomy, $tax)){
				$tax_id 	= $tag->term_id;
				$img_id 	= intval(get_term_meta($tax_id, 'loftocean_tax_image', true));
				$img_src 	= empty($img_id) ? false : $this->get_taxonomy_image_src($tag, 'thumbnail'); 
				$text		= $this->text;
				printf(
					'<tr class="form-field">%s%s</tr>',
					sprintf(
						'<th scope="row" valign="top"><label for="loftocean_tax_image">%s</label></th>',
						$text['label'][$taxonomy]
					),
					sprintf(
						'<td>%s%s%s%s</td>',
						sprintf(
							'<a href="#" class="loftocean-upload-image" %s data-upload="%s">%s</a>',
							'style="display: block;"',
							$text['choose'],
							$img_src ? sprintf('<img alt="img" src="%s">', $img_src) : $text['choose']
						),
						sprintf(
							'<a href="#" class="loftocean-remove-image" %s>%s</a>',
							sprintf('style="display: %s;"', ($img_src ? 'block' : 'none')),
							$text['remove']
						),
						sprintf(
							'<input type="hidden" name="loftocean_tax_image" value="%s">',
							empty($img_id) ? '' : $img_id
						),
						sprintf(
							'<span class="description">%s</span>',
							$text['description'][$taxonomy]
						)
					)
				);
			}
		}
		/**
		* Add a image uploader for new category/post_tag page
		*/
		public function add_taxonomy_fields(){ 
			$taxonomy 	= isset($_GET['taxonomy']) ? $_GET['taxonomy'] : false;
			$tax 		= array('category', 'post_tag');
			if($taxonomy && in_array($taxonomy, $tax)){
				$text = $this->text;
				
				printf(
					'<div class="form-field term-img-wrap">%s%s%s%s</div>',
					sprintf(
						'<label for="loftocean_tax_image">%s</label>', 
						$text['label'][$taxonomy]
					),
					sprintf(
						'<a href="#" class="loftocean-upload-image" style="display: block;" data-upload="%1$s">%1$s</a>',
						$text['choose']
					),
					sprintf(
						'<a href="#" class="loftocean-remove-image" style="display:none;">%s</a>',
						$text['remove']
					),
					sprintf(
						'<input type="hidden" name="loftocean_tax_image" id="loftocean_tax_image"><p>%s</p>',
						$text['description'][$taxonomy]
					)
				);
			}
		}
		/*
		* Save taxonomy image for category/post_tag
		*/
		public function save_tax_fileds($term_id, $tt_id, $taxonomy){
			$tax = array('category', 'post_tag');
			if(in_array($taxonomy, $tax) && isset($_POST['loftocean_tax_image'])){
				update_term_meta($term_id, 'loftocean_tax_image', intval($_POST['loftocean_tax_image']));
			}
		}
		/*
		* Enqueue scripts needed for taxonomy image field
		*/
		public function enqueue_scripts(){
			wp_enqueue_media();
			wp_enqueue_script('loftocean_admin_script', FALLSKY_PLUGIN_URI . 'assets/js/taxonomy.min.js', array('jquery'), FALLSKY_PLUGIN_ASSETS_VERSION, true);
		}
		/**
		* Get taxonomy image background html for category/post_tag
		*/
		public function get_taxonomy_image_bg( $html, $term, $sizes = array( 'full', 'full' ), $args = array() ) {
			if( ( $term instanceof WP_Term ) && in_array( $term->taxonomy, $this->tax ) ) {
				$tax_id 	= $term->term_id;
				$image_id 	= intval( get_term_meta( $tax_id, 'loftocean_tax_image', true ) );
				return empty( $image_id ) ? '' : apply_filters( 'loftocean_get_preload_bg', '', $image_id, $sizes, $args );
			}
			return false;
		}
		/**
		* Get taxonomy image src for category/post_tag
		*/
		private function get_taxonomy_image_src($term, $size = false){
			if(($term instanceof WP_Term) && in_array($term->taxonomy, $this->tax)){
				$tax_id 	= $term->term_id;
				$image_id 	= intval(get_term_meta($tax_id, 'loftocean_tax_image', true));
				$image 		= wp_get_attachment_image_src($image_id, $size);
				return $image ? $image[0] : false;
			}
			return false;
		}
	}
	new LoftOcean_Taxonomy_Editing();
}