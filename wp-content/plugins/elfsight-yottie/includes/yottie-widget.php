<?php

if (!defined('ABSPATH')) exit;

if (!class_exists('YottieWidget')) {
	/**
	 * Adds YottieWidget widget.
	 */
	class YottieWidget extends WP_Widget {

		/**
		 * Register widget with WordPress.
		 */
		function __construct() {
			parent::__construct(
				'YottieWidget',
				__('Yottie Widget', ELFSIGHT_YOTTIE_TEXTDOMAIN),
				array('description' => __('Yottie - YouTube Channel Plugin', ELFSIGHT_YOTTIE_TEXTDOMAIN))
			);
		}

		/**
		 * Front-end display of widget.
		 *
		 * @see WP_Widget::widget()
		 *
		 * @param array $args     Widget arguments.
		 * @param array $instance Saved values from database.
		 */
		public function widget($args, $instance) {
			extract($instance, EXTR_SKIP);

			if (!empty($instance['id'])) {
				echo do_shortcode('[yottie id="' . $instance['id'] . '"]');
			}
		}

		/**
		 * Back-end widget form.
		 *
		 * @see WP_Widget::form()
		 *
		 * @param array $instance Previously saved values from database.
		 */
		public function form($instance) {
			global $wpdb;

			$widgets_table_name = elfsight_yottie_widgets_get_table_name();
			$select_sql = '
				SELECT id, name FROM `' . esc_sql($widgets_table_name) . '`
				WHERE `active` = "1" ORDER BY `id` DESC
			';

			$widgets = $wpdb->get_results($select_sql, ARRAY_A);?>

			<?php if(!empty($widgets)) {?>
				<p>
					<label for="<?php echo $this->get_field_id('id'); ?>"><?php _e('Select Gallery:', ELFSIGHT_YOTTIE_TEXTDOMAIN); ?></label>
					<select class='widefat' id="<?php echo $this->get_field_id('id'); ?>" name="<?php echo $this->get_field_name('id'); ?>">
						<option value="0">— Select —</option>
						<?php foreach ($widgets as $widget) { ?>
							<option value="<?php echo $widget['id'] ?>"<?php echo (!empty($instance['id']) && $instance['id'] == $widget['id']) ? ' selected' : ''; ?>><?php echo $widget['name']; ?></option>
						<?php } ?>
					</select>
				</p>
			<?php } else { ?>
				<p>
					<?php _e('No Yottie galleries yet.', ELFSIGHT_YOTTIE_TEXTDOMAIN); ?>
                	<a href="<?php echo esc_url(admin_url('admin.php?page=elfsight-yottie')); ?>#/add-gallery/" data-yt-admin-page="add-gallery"><?php _e('Create the first one.', ELFSIGHT_YOTTIE_TEXTDOMAIN); ?></a>
				</p>
			<?php }
		}

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @see WP_Widget::update()
		 *
		 * @param array $new_instance Values just sent to be saved.
		 * @param array $old_instance Previously saved values from database.
		 *
		 * @return array Updated safe values to be saved.
		 */
		public function update($new_instance, $old_instance) {
			$instance = $old_instance;
		    $instance['id'] = !empty($new_instance['id']) ? $new_instance['id'] : '';

		    return $instance;
		}
	}

	if(!function_exists('elfsight_yottie_register_widget')) {
		function elfsight_yottie_register_widget() {
		    register_widget('YottieWidget');
		}
		add_action('widgets_init', 'elfsight_yottie_register_widget');	
	}
}

?>