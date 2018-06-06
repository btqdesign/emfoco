<?php
/**
* Facebook widget
*/

add_action('wp_enqueue_scripts', 'loftocean_widget_facebook');
function loftocean_widget_facebook(){
	if(is_active_widget( false, false, 'loftocean-facebook', true)){
		wp_enqueue_script('loftocean-facebook', FALLSKY_PLUGIN_URI . 'assets/js/facebook-jssdk.min.js', array(), FALLSKY_PLUGIN_ASSETS_VERSION, true);
	}
}
// Facebook page widget
class LoftOcean_Widget_Facebook extends WP_Widget {
	function __construct(){
		$class = 'loftocean-widget_facebook';
		$title = apply_filters('loftocean_facebook_widget_name', esc_html__('LoftOcean Facebook', 'loftocean'));
		$widget_ops = array(
			'classname' => $class,
			'description' => esc_html__('Show your Facebook Page.', 'loftocean'),
			'customize_selective_refresh' => true,
		);
		parent::__construct('loftocean-facebook', $title, $widget_ops);
		$this->alt_option_name = 'loftocean-widget_facebook';
	}
	/**
	 * Outputs the content of facebook page.
	 */
	public function widget($args, $instance){
		if(!isset($args['widget_id'])){
			$args['widget_id'] = $this->id;
		}

		$username = !empty($instance['username']) ? esc_attr($instance['username']) : '';
		$title = isset($instance['title']) ? esc_html($instance['title']) : esc_html__('Like on Facebook', 'loftocean');

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters('widget_title', $title, $instance, $this->id_base);

		if(!empty($username)){			
			$output = $args['before_widget'];
			if(!empty($title)){
				$output .= $args['before_title'] . $title . $args['after_title'];
			}
			$output .= '<div id="' . $this->id . '-wrap">';

			//* Main Facebook Feed
			$output .= '<div class="fb-page" ';
			$output .= 'data-href="' . esc_js((false !== strpos($username, 'facebook.com')) ?  $username : ('https://facebook.com/' . $username)) . '" ';
			$output .= 'data-width="320" ';
			$output .= 'data-height="500" ';
			$output .= 'data-tabs="" ';
			$output .= 'data-hide-cover="0" ';
			$output .= 'data-show-facepile="1" ';
			$output .= 'data-hide-cta="0" ';
			$output .= 'data-small-header="0" ';
			$output .= 'data-adapt-container-width="1">';
			$output .= '</div>';

			// end wrapper
			$output .= '</div>';
			$output .= $args['after_widget'];

			print($output);
		}
	}
	/**
	 * Handles updating the settings for facebook widget
	 */
	public function update($new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['username'] = sanitize_text_field($new_instance['username']);
		$instance['title'] = sanitize_text_field($new_instance['title']);
		return $instance;
	}
	/**
	 * Outputs the settings form for the Recent Posts widget.
	 */
	public function form($instance){
		$username  = isset( $instance['username'] ) ? esc_attr( $instance['username'] ) : '';
		$title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : esc_html__('Like on Facebook', 'loftocean'); ?>

		<p><label for="<?php print($this->get_field_id( 'username' )); ?>"><?php esc_html_e('Facebook Username:', 'loftocean'); ?></label>
		<input class="widefat" id="<?php print($this->get_field_id( 'username' )); ?>" name="<?php print($this->get_field_name('username')); ?>" type="text" value="<?php print($username); ?>" /></p>

		<p><label for="<?php print($this->get_field_id( 'title' )); ?>"><?php esc_html_e('Title:', 'loftocean'); ?></label>
		<input class="widefat" id="<?php print($this->get_field_id( 'title' )); ?>" name="<?php print($this->get_field_name('title')); ?>" type="text" value="<?php print($title); ?>" /></p> <?php
	}
}
