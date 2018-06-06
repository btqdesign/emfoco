<?php
/* 
 *************************************************************************************
 * Since version 1.1.0
 *		1. Add support for svg image
 *************************************************************************************
 */

if( !class_exists( 'LoftOcean_SVG_Image' ) ) {
	class LoftOcean_SVG_Image {
		function __construct(){
			add_action( 'admin_init', 						array( $this, 'add_svg_support' ) );
			add_filter( 'wp_check_filetype_and_ext', 		array( $this, 'check_file_type' ), 100, 4 );
			add_filter( 'wp_generate_attachment_metadata',	array( $this, 'generate_svg_metadata' ), 10, 2 );
		}

		public function generate_svg_metadata( $metadata, $attachment_id ) {
			if( get_post_mime_type( $attachment_id ) == 'image/svg+xml' ) {
				$svg_path 	= get_attached_file( $attachment_id );
				$dimensions = $this->svg_dimensions( $svg_path );
				$metadata['width'] 	= $dimensions->width;
				$metadata['height'] = $dimensions->height;
			}
			return $metadata;
		}
		public function check_file_type( $data, $file, $filename, $mimes ){
			if( substr( $filename, -4 ) == '.svg' ){
				$data['ext'] 	= 'svg';
				$data['type'] 	= 'image/svg+xml';
			}
			if( substr( $filename, -5 ) == '.svgz' ){
				$data['ext'] 	= 'svgz';
				$data['type'] 	= 'image/svg+xml';
			}
			return $data;
		}
		public function add_svg_support() {
			function svg_thumbs( $content ) {
				return apply_filters( 'final_output', $content );
			}
			ob_start( 'svg_thumbs' );

			add_action( 'admin_head', 	array( $this, 'svg_css_fix' ) );
			add_filter( 'final_output', array( $this, 'final_output' ) );
			add_filter( 'upload_mimes', array( $this, 'add_svg_mime' ) );
			add_filter( 'wp_prepare_attachment_for_js', array( $this, 'prepare_svg_for_js' ), 10, 3 );
		}

		public function final_output( $content ){
			$content = str_replace(
				'<# } else if ( \'image\' === data.type && data.sizes && data.sizes.full ) { #>',
				'<# } else if ( \'svg+xml\' === data.subtype ) { #>
					<img class="details-image" src="{{ data.url }}" draggable="false" />
				<# } else if ( \'image\' === data.type && data.sizes && data.sizes.full ) { #>',
				$content
			);

			$content = str_replace(
				'<# } else if ( \'image\' === data.type && data.sizes ) { #>',
				'<# } else if ( \'svg+xml\' === data.subtype ) { #>
					<div class="centered">
						<img src="{{ data.url }}" class="thumbnail" draggable="false" />
					</div>
				<# } else if ( \'image\' === data.type && data.sizes ) { #>',
				$content
			);

			return $content;
		}

		public function svg_css_fix(){
			echo '<style>img[src$=".svg"],img[src$=".svgz"]{ width:90%; height:auto; }</style>';
		}

		public function add_svg_mime( $mimes = array() ){
			$mimes['svg'] 	= 'image/svg+xml';
			$mimes['svgz'] 	= 'image/svg+xml';
			return $mimes;
		}

		function prepare_svg_for_js( $response, $attachment, $meta ) {
			if( $response['mime'] == 'image/svg+xml' && empty( $response['sizes'] ) ){
				$svg_path = get_attached_file( $attachment->ID );
				if( !file_exists( $svg_path ) ){
					$svg_path = $response['url'];
				}
				$dimensions = $this->svg_dimensions( $svg_path );
				$response['sizes'] = array(
					'full' => array(
						'url' 			=> $response['url'],
						'width' 		=> $dimensions->width,
						'height' 		=> $dimensions->height,
						'orientation' 	=> $dimensions->width > $dimensions->height ? 'landscape' : 'portrait'
					)
				);
			}
			return $response;
		}

		private function svg_dimensions( $svg ){ print_r(getimagesize($svg));
			$svg 	= simplexml_load_file( $svg );
			$width 	= 0;
			$height = 0;
			if( $svg ) {
				$attributes = $svg->attributes();
				if( isset( $attributes->width, $attributes->height ) ) {
					$width 	= floatval( $attributes->width );
					$height = floatval( $attributes->height );
				}
				else if( isset( $attributes->viewBox ) ) {
					$sizes = explode( ' ', $attributes->viewBox );
					if( isset( $sizes[2], $sizes[3] ) ){
						$width 	= floatval( $sizes[2] );
						$height = floatval( $sizes[3] );
					}
				}
			}
			return (object)array( 'width' => $width, 'height' => $height );
		}
	}
	add_action( 'after_setup_theme', function(){ new LoftOcean_SVG_Image(); } );
}