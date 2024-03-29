<?php
/**
* Theme option default values
*/

global $fallsky_default_settings;

$fallsky_default_settings = apply_filters('fallsky_default_settings', array(
	'fallsky_fallback_customize_css'							=> '',
	'fallsky_site_logo_width'									=> 160,
	'fallsky_transparent_site_header_logo'						=> '',

	/** section general layouts **/
	'fallsky_page_background_image' 							=> '',
	'fallsky_page_background_position_x' 						=> 'center',
	'fallsky_page_background_position_y' 						=> 'center',
	'fallsky_page_background_size' 								=> 'auto',
	'fallsky_page_background_repeat' 							=> '',
	'fallsky_page_background_attachment' 						=> '',

	'fallsky_site_layout' 										=> 'site-layout-fullwidth',

	'fallsky_site_layout_boxed_width' 							=> 1200,

	'fallsky_site_container_width'								=> '', 

	/** section general posts **/
	'fallsky_read_more_text' 									=> esc_html__('Read More', 'fallsky'),
	'fallsky_pagination_style'									=> 'ajax-more',

	/** comments settings **/
	'fallsky_comment_fold_reply_form'							=> '',
	'fallsky_comment_location'									=> 'after_post_content',

	'fallsky_post_excerpt_length_for_layout_masonry' 			=> '30',
	'fallsky_post_excerpt_length_for_layout_list_1col' 			=> '20',
	'fallsky_post_excerpt_length_for_layout_list_2col' 			=> '15',
	'fallsky_post_excerpt_length_for_layout_zigzag' 			=> '20',
	'fallsky_post_excerpt_length_for_layout_grid' 				=> '20',
	'fallsky_post_excerpt_length_for_layout_card' 				=> '30',

	/** section general colors **/
	'fallsky_site_color_scheme'									=> 'light-color',
	'fallsky_accent_color'										=> 'custom',
	'fallsky_accent_custom_color'								=> '#C09F68',

	'fallsky_light_color_scheme_custom_bg'						=> '#FFFFFF',
	'fallsky_light_color_scheme_custom_text'					=> '#212121',
	'fallsky_light_color_scheme_custom_content'		 			=> '#515151',

	'fallsky_dark_color_scheme_custom_bg'						=> '#282828',
	'fallsky_dark_color_scheme_custom_text'						=> '#FFFFFF',
	'fallsky_dark_color_scheme_custom_content'					=> '#E2E2E2',

	/** section general image slider **/
	'fallsky_mobile_image_slider_arrows_style'					=> '', 

	/** section general instagram **/
	'fallsky_instagram_clear_cache'								=> '',
	'fallsky_instagram_render_type'								=> '',

	/** section site header layout **/
	'fallsky_site_header_layout'								=> 'site-header-layout-1',
	'fallsky_enable_hamburge_menu_button'						=> 'on',
	'fallsky_hamburge_menu_button_style'						=> 'icon-only',	
	'fallsky_sticky_site_header'								=> 'sticky-scroll-up',	
	'fallsky_no_space_between_site_header_and_content'			=> '',
	'fallsky_site_header_show_social_menu'						=> '',
	'fallsky_show_search_button'								=> 'on',
	'fallsky_search_button_style'								=> 'icon-only',

	/** section site header design options **/
	'fallsky_site_header_color_scheme'							=> 'site-header-color-dark',
	'fallsky_site_header_bg_color'								=> '',

	/** section site header transparent */
	'fallsky_home_transparent_site_header'						=> '',
	'fallsky_single_post_template1_transparent_site_header' 	=> 'on',
	'fallsky_single_page_layout2_transparent_site_header'		=> 'on',
	'fallsky_archive_pages_transparent_site_header'				=> 'on',

	/** section fullscreen menu content options **/
	'fallsky_fullscreen_menu_show_search_form'					=> 'on',
	'fallsky_fullscreen_menu_show_social_menu'					=> 'on',
	'fallsky_fullscreen_menu_copyright_text'					=> esc_html__('Your custom text &copy; Copyright 2018. All rights reserved.', 'fallsky'),

	/** section fullscreen menu design options **/
	'fallsky_fullscreen_menu_bg_color'							=> '#F7F7F7',
	'fallsky_fullscreen_menu_text_color'						=> '#212121',
	'fallsky_fullscreen_menu_bg_image'							=> '',
	'fallsky_fullscreen_menu_bg_size'							=> 'auto',
	'fallsky_fullscreen_menu_bg_repeat'							=> '',
	'fallsky_fullscreen_menu_bg_position_x'						=> 'center',
	'fallsky_fullscreen_menu_bg_position_y'						=> 'center',
	'fallsky_fullscreen_menu_enable_overlay' 					=> 'on',
	'fallsky_fullscreen_menu_overlay_color'						=> '#000000',
	'fallsky_fullscreen_menu_overlay_opacity'					=> '40',
	'fallsky_fullscreen_menu_no_border'							=> '',

	/** section search content options **/
	'fallsky_search_show_category'								=> 'on',
	'fallsky_search_show_category_count'						=> 'on',

	/** section search design options **/
	'fallsky_search_bg_color'									=> '#F7F7F7',
	'fallsky_search_text_color'									=> '#212121',
	'fallsky_search_bg_image'									=> '',
	'fallsky_search_bg_size'									=> 'auto',
	'fallsky_search_bg_repeat'									=> '',
	'fallsky_search_bg_position_x'								=> 'center',
	'fallsky_search_bg_position_y'								=> 'center',
	'fallsky_search_enable_overlay' 							=> 'on',
	'fallsky_search_overlay_color'								=> '#000000',
	'fallsky_search_overlay_opacity'							=> '40',
	'fallsky_search_no_border'									=> '',

	/** section site footer content options **/
	'fallsky_site_footer_enable_instagram'						=> '',
	'fallsky_site_footer_instagram_username'					=> '',
	'fallsky_site_footer_instagram_title'						=> '',
	'fallsky_site_footer_instagram_title_layout'				=> '',
	'fallsky_site_footer_instagram_columns'						=> 6,
	'fallsky_site_footer_instagram_fullwidth'					=> 'on',
	'fallsky_site_footer_instagram_space'						=> 0,
	'fallsky_site_footer_instagram_new_tab'						=> '',
	'fallsky_site_footer_bottom_layout'							=> '',
	'fallsky_site_footer_bottom_enable_menu'					=> '',
	'fallsky_site_footer_bottom_menu_type'						=> 'social',
	'fallsky_site_footer_bottom_menu'							=> '',
	'fallsky_site_footer_bottom_text'							=> esc_html__('Footer text &copy; Copyright 2018. All rights reserved.', 'fallsky'),

	/** section site footer design options **/
	'fallsky_site_footer_bg_image'								=> '',
	'fallsky_site_footer_bg_size'								=> 'auto',
	'fallsky_site_footer_bg_repeat'								=> '',
	'fallsky_site_footer_bg_position_x'							=> 'center',
	'fallsky_site_footer_bg_position_y'							=> 'center',
	'fallsky_site_footer_bg_attachment'							=> '',
	'fallsky_site_footer_color_scheme'							=> 'dark-color',
	'fallsky_site_footer_bg_color'								=> '',
	'fallsky_site_footer_text_color'							=> '',
	'fallsky_site_footer_bottom_color'							=> 'inherit',
	'fallsky_site_footer_bottom_custom_bg_color'				=> '',
	'fallsky_site_footer_bottom_custom_text_color'				=> '',

	/** section sidebar **/
	'fallsky_sidebar_enable_sticky'								=> 'on',
	'fallsky_sidebar_widgets_style'								=> '',
	'fallsky_sidebar_widgets_bg_color'							=> '',
	'fallsky_sidebar_widgets_border_color'						=> '',
	'fallsky_sidebar_widgets_text_color'						=> '',

	/** section homepage fullwidth freatured area **/
	'fallsky_home_show_fullwidth_featured_area'					=> '',
	'fallsky_home_fullwidth_featured_area_type'					=> 'posts-slider',
	'fallsky_home_posts_slider_style'							=> 'style-slider-1',
	'fallsky_home_posts_slider_post_number'						=> '3',
	'fallsky_home_posts_slider_auto_play'						=> 'on',
	'fallsky_home_posts_slider_auto_play_pause_duration'		=> '5',
	'fallsky_home_posts_slider_pause_on_hover'					=> 'on',
	'fallsky_home_posts_slider_hide_category'					=> '',
	'fallsky_home_posts_hide_excerpt'							=> '',

	'fallsky_home_posts_blocks_style'							=> 'style-blocks-1',
	'fallsky_home_posts_block_hide_category'					=> '',

	'fallsky_home_posts_by'										=> 'category',
	'fallsky_home_categories'									=> array(''),
	'fallsky_home_exclude_posts_from_main_content'				=> '',

	'fallsky_home_custom_content_editor'						=> '',
	'fallsky_home_custom_content_bg_color'						=> '',
	'fallsky_home_custom_content_bg_image'						=> '',
	'fallsky_home_custom_content_bg_size'						=> 'auto',
	'fallsky_home_custom_content_bg_repeat'						=> '',
	'fallsky_home_custom_content_bg_position_x'					=> 'center',
	'fallsky_home_custom_content_bg_position_y'					=> 'center',
	'fallsky_home_custom_content_bg_attachment'					=> '',
	'fallsky_home_custom_content_bg_video'						=> '',
	'fallsky_home_custom_content_external_bg_video'				=> '',
	'fallsky_home_custom_content_text_color'					=> '#000000',
	'fallsky_home_custom_content_height_type'					=> 'custom',
	'fallsky_home_custom_content_height'						=> '600',

	'fallsky_homepage_main_area' 								=> array(),

	/** section homepage main content area **/
	'fallsky_home_sidebar'										=> '',

	/** section archive page category **/
	'fallsky_category_show_page_header'							=> 'on',
	'fallsky_category_show_image'								=> 'on',
	'fallsky_category_show_subcategory_filter'					=> 'on',
	'fallsky_category_sidebar'									=> 'with-sidebar-right',
	'fallsky_category_posts_layout'								=> 'masonry',
	'fallsky_category_column_list'								=> '1',
	'fallsky_category_column_masonry'							=> '2',
	'fallsky_category_column_card'								=> '1',
	'fallsky_category_card_color'								=> 'light-card',
	'fallsky_category_column_grid'								=> '2',
	'fallsky_category_column_overlay'							=> '1',
	'fallsky_category_column_overlay_mix'						=> '1-2-mix',
	'fallsky_category_image_orientation'						=> '',
	'fallsky_category_show_post_meta_excerpt'					=> 'on',
	'fallsky_category_show_read_more_btn'						=> 'on',
	'fallsky_category_masonry_center_text'						=> '',
	'fallsky_category_show_post_meta_category'					=> 'on',
	'fallsky_category_show_post_meta_author' 					=> '',
	'fallsky_category_show_post_meta_date'	 					=> '',
	'fallsky_category_show_post_meta_view'	 					=> '',
	'fallsky_category_show_post_meta_like'	 					=> '',
	'fallsky_category_show_post_meta_comment' 					=> '',
	'fallsky_category_posts_per_page'							=> get_option('posts_per_page', 10),
	'fallsky_category_content'									=> 'posts',
	'fallsky_category_subcategory_style'						=> 'style-rectangle',
	'fallsky_category_subcategory_layout'						=> 'column-3',
	'fallsky_category_subcategory_show_post_count'				=> '',

	/** section archive page author **/
	'fallsky_author_sidebar'									=> '',
	'fallsky_author_posts_layout'								=> 'grid',
	'fallsky_author_column_list'								=> '1',
	'fallsky_author_column_masonry'								=> '2',
	'fallsky_author_column_card'								=> '1',
	'fallsky_author_card_color'									=> 'light-card',
	'fallsky_author_column_grid'								=> '3',
	'fallsky_author_column_overlay'								=> '1',
	'fallsky_author_column_overlay_mix'							=> '1-2-mix',
	'fallsky_author_image_orientation'							=> '',
	'fallsky_author_show_post_meta_excerpt'						=> 'on',
	'fallsky_author_show_read_more_btn'							=> 'on',
	'fallsky_author_masonry_center_text'						=> '',
	'fallsky_author_show_post_meta_category'					=> 'on',
	'fallsky_author_show_post_meta_author' 						=> '',
	'fallsky_author_show_post_meta_date'	 					=> '',
	'fallsky_author_show_post_meta_view'	 					=> '',
	'fallsky_author_show_post_meta_like'	 					=> '',
	'fallsky_author_show_post_meta_comment' 					=> '',
	'fallsky_author_posts_per_page'								=> get_option('posts_per_page', 10),

	/** section archive page search **/	
	'fallsky_search_sidebar'									=> '',
	'fallsky_search_posts_layout'								=> 'grid',
	'fallsky_search_column_list'								=> '1',
	'fallsky_search_column_masonry'								=> '2',
	'fallsky_search_column_card'								=> '1',
	'fallsky_search_card_color'									=> 'light-card',
	'fallsky_search_column_grid'								=> '4',
	'fallsky_search_column_overlay'								=> '1',
	'fallsky_search_column_overlay_mix'							=> '1-2-mix',
	'fallsky_search_image_orientation'							=> '',
	'fallsky_search_show_post_meta_excerpt'						=> '',
	'fallsky_search_show_read_more_btn'							=> 'on',
	'fallsky_search_masonry_center_text'						=> '',
	'fallsky_search_show_post_meta_category'					=> '',
	'fallsky_search_show_post_meta_author' 						=> '',
	'fallsky_search_show_post_meta_date'	 					=> 'on',
	'fallsky_search_show_post_meta_view'	 					=> '',
	'fallsky_search_show_post_meta_like'	 					=> '',
	'fallsky_search_show_post_meta_comment' 					=> '',
	'fallsky_search_posts_per_page'								=> get_option('posts_per_page', 10),

	/** section archive page tag **/
	'fallsky_tag_show_page_header'								=> 'on',
	'fallsky_tag_show_image'									=> 'on',
	'fallsky_tag_sidebar'										=> 'with-sidebar-right',
	'fallsky_tag_posts_layout'									=> 'masonry',
	'fallsky_tag_column_list'									=> '1',
	'fallsky_tag_column_masonry'								=> '2',
	'fallsky_tag_column_card'									=> '1',
	'fallsky_tag_card_color'									=> 'light-card',
	'fallsky_tag_column_grid'									=> '2',
	'fallsky_tag_column_overlay'								=> '1',
	'fallsky_tag_column_overlay_mix'							=> '1-2-mix',
	'fallsky_tag_image_orientation'								=> '',
	'fallsky_tag_show_post_meta_excerpt'						=> 'on',
	'fallsky_tag_show_read_more_btn'							=> 'on',
	'fallsky_tag_masonry_center_text'							=> '',
	'fallsky_tag_show_post_meta_category'						=> 'on',
	'fallsky_tag_show_post_meta_author'		 					=> '',
	'fallsky_tag_show_post_meta_date'	 						=> '',
	'fallsky_tag_show_post_meta_view'	 						=> '',
	'fallsky_tag_show_post_meta_like'	 						=> '',
	'fallsky_tag_show_post_meta_comment' 						=> '',
	'fallsky_tag_posts_per_page'								=> get_option('posts_per_page', 10),

	/** section archive page date **/
	'fallsky_date_sidebar'										=> 'with-sidebar-right',
	'fallsky_date_posts_layout'									=> 'masonry',
	'fallsky_date_column_list'									=> '1',
	'fallsky_date_column_masonry'								=> '2',
	'fallsky_date_column_card'									=> '1',
	'fallsky_date_card_color'									=> 'light-card',
	'fallsky_date_column_grid'									=> '2',
	'fallsky_date_column_overlay'								=> '1',
	'fallsky_date_column_overlay_mix'							=> '1-2-mix',
	'fallsky_date_image_orientation'							=> '',
	'fallsky_date_show_post_meta_excerpt'						=> 'on',
	'fallsky_date_show_read_more_btn'							=> 'on',
	'fallsky_date_masonry_center_text'							=> '',
	'fallsky_date_show_post_meta_category'						=> 'on',
	'fallsky_date_show_post_meta_author' 						=> '',
	'fallsky_date_show_post_meta_date'	 						=> '',
	'fallsky_date_show_post_meta_view'	 						=> '',
	'fallsky_date_show_post_meta_like'	 						=> '',
	'fallsky_date_show_post_meta_comment' 						=> '',
	'fallsky_date_posts_per_page'								=> get_option('posts_per_page', 10),

	/** section archive page post format **/
	'fallsky_post_format_sidebar'								=> 'with-sidebar-right',
	'fallsky_post_format_posts_layout'							=> 'masonry',
	'fallsky_post_format_column_list'							=> '1',
	'fallsky_post_format_column_masonry'						=> '2',
	'fallsky_post_format_column_card'							=> '1',
	'fallsky_post_format_card_color'							=> 'light-card',
	'fallsky_post_format_column_grid'							=> '2',
	'fallsky_post_format_column_overlay'						=> '1',
	'fallsky_post_format_column_overlay_mix'					=> '1-2-mix',
	'fallsky_post_format_image_orientation'						=> '',
	'fallsky_post_format_show_post_meta_excerpt'				=> 'on',
	'fallsky_post_format_show_read_more_btn'					=> 'on',
	'fallsky_post_format_masonry_center_text'					=> '',
	'fallsky_post_format_show_post_meta_category'				=> 'on',
	'fallsky_post_format_show_post_meta_author' 				=> '',
	'fallsky_post_format_show_post_meta_date'	 				=> '',
	'fallsky_post_format_show_post_meta_view'	 				=> '',
	'fallsky_post_format_show_post_meta_like'	 				=> '',
	'fallsky_post_format_show_post_meta_comment' 				=> '',
	'fallsky_post_format_posts_per_page'						=> get_option('posts_per_page', 10),

	/** section archive page blog **/
	'fallsky_blog_sidebar'										=> 'with-sidebar-right',
	'fallsky_blog_posts_layout'									=> 'masonry',
	'fallsky_blog_column_list'									=> '1',
	'fallsky_blog_column_masonry'								=> '2',
	'fallsky_blog_column_card'									=> '1',
	'fallsky_blog_card_color'									=> 'light-card',
	'fallsky_blog_column_grid'									=> '2',
	'fallsky_blog_column_overlay'								=> '1',
	'fallsky_blog_column_overlay_mix'							=> '1-2-mix',
	'fallsky_blog_image_orientation'							=> '',
	'fallsky_blog_show_post_meta_excerpt'						=> 'on',
	'fallsky_blog_show_read_more_btn'							=> 'on',
	'fallsky_blog_masonry_center_text'							=> '',
	'fallsky_blog_show_post_meta_category'						=> 'on',
	'fallsky_blog_show_post_meta_author' 						=> '',
	'fallsky_blog_show_post_meta_date'	 						=> '',
	'fallsky_blog_show_post_meta_view'	 						=> '',
	'fallsky_blog_show_post_meta_like'	 						=> '',
	'fallsky_blog_show_post_meta_comment' 						=> '',
	'fallsky_blog_posts_per_page'								=> get_option('posts_per_page', 10),

	/** section sinple post **/
	'fallsky_single_post_default_template'						=> 'post-template-1',
	'fallsky_single_post_default_sidebar'						=> 'with-sidebar-right',
	'fallsky_sticky_post_nav'									=> 'on',
	'fallsky_post_nav_color_scheme'								=> 'dark-color',
	'fallsky_post_nav_bg_color'									=> '',
	'fallsky_single_post_show_category'							=> 'on',
	'fallsky_single_post_show_sharing_buttons'					=> 'on',
	'fallsky_single_post_show_sharing_buttons_on_mobile'		=> 'on',
	'fallsky_single_post_facebook_sharing'						=> 'on',
	'fallsky_single_post_twitter_sharing'						=> 'on',
	'fallsky_single_post_pinterest_sharing'						=> 'on',
	'fallsky_single_post_google_plus_sharing'					=> 'on',
	'fallsky_single_post_show_tags'								=> 'on',
	'fallsky_single_post_show_author'							=> 'on',
	'fallsky_single_post_show_date'								=> 'on',
	'fallsky_single_post_show_view'								=> 'on',
	'fallsky_single_post_show_like'								=> 'on',
	'fallsky_single_post_show_comment_count'					=> 'on',
	'fallsky_single_post_show_author_info_box'					=> 'on',
	'fallsky_single_post_show_signup_form'						=> '',
	'fallsky_single_post_signup_form_id'						=> fallsky_get_default_mc4wp_form_id(),
	'fallsky_single_post_show_pagination'						=> 'on',
	'fallsky_single_post_show_related_posts'					=> 'on',
	'fallsky_single_post_related_posts_title'					=> esc_html__('You May Also Like', 'fallsky'),
	'fallsky_single_post_related_posts_by'						=> 'category',
	'fallsky_single_post_related_post_number'					=> 3,

	/** section category index page **/
	'fallsky_category_index_sidebar'							=> 'with-sidebar-right',
	'fallsky_category_index_categories'							=> 'all',
	'fallsky_category_index_style'								=> 'style-rectangle',
	'fallsky_category_index_layout'								=> 'column-3',
	'fallsky_category_index_show_post_count'					=> '',

	/** section woocommerce **/
	'fallsky_woocommerce_show_cart'								=> 'on',
	'fallsky_woocommerce_cart_button_style'						=> 'icon-only',
	'fallsky_woocommerce_sidebar_general'						=> 'with-sidebar-right',
	'fallsky_woocommerce_sidebar_single'						=> 'with-sidebar-right',
	'fallsky_woocommerce_sidebar_content'						=> 'shop-sidebar',
	'fallsky_woocommerce_archive_layout'						=> '4',
	'fallsky_woocommerce_archive_style'							=> '',
	'fallsky_woocommerce_archive_overlay_color_scheme'			=> 'overlay-light-color',
	'fallsky_woocommerce_products_per_page'						=> 12,
	'fallsky_woocommerce_archive_show_title'					=> 'on',
	'fallsky_woocommerce_archive_show_price'					=> 'on',
	'fallsky_woocommerce_archive_show_rating'					=> 'on',
	'fallsky_woocommerce_archive_show_sale'						=> 'on',

	/** section typogaghy */
	'fallsky_typography_text_font-family' 						=> "Roboto",
	'fallsky_typography_heading_font-family' 					=> "Source Sans Pro",

	'fallsky_typography_heading_font-weight' 					=> '600',
	'fallsky_typography_heading_letter-spacing' 				=> 0,
	'fallsky_typography_heading_text-transform' 				=> 'none',
	'fallsky_typography_heading_font-style' 					=> 'normal',
	'fallsky_typography_heading_line-height' 					=> '1.5',

	'fallsky_typography_content_font-size' 						=> 14,
	'fallsky_typography_content_line-height' 					=> '1.8',

	'fallsky_typography_post_title_font-weight' 				=> '600',
	'fallsky_typography_post_title_letter-spacing' 				=> '0',
	'fallsky_typography_post_title_text-transform'	 			=> 'capitalize',
	'fallsky_typography_post_title_font-style' 					=> 'normal',

	'fallsky_typography_fullwidth_post_title_style'				=> 'default',
	'fallsky_typography_fullwidth_post_title_font-weight' 		=> '600',
	'fallsky_typography_fullwidth_post_title_letter-spacing' 	=> '0',
	'fallsky_typography_fullwidth_post_title_text-transform' 	=> 'capitalize',
	'fallsky_typography_fullwidth_post_title_font-style' 		=> 'normal',

	'fallsky_typography_archive_post_title_style'				=> 'default',
	'fallsky_typography_archive_post_title_font-weight' 		=> '600',
	'fallsky_typography_archive_post_title_letter-spacing' 		=> '0',
	'fallsky_typography_archive_post_title_text-transform'		=> 'capitalize',
	'fallsky_typography_archive_post_title_font-style' 			=> 'normal',

	'fallsky_typography_single_post_title_style'				=> 'default',
	'fallsky_typography_single_post_title_font-weight' 			=> '600',
	'fallsky_typography_single_post_title_letter-spacing' 		=> '0',
	'fallsky_typography_single_post_title_text-transform'		=> 'capitalize',
	'fallsky_typography_single_post_title_font-style' 			=> 'normal',

	'fallsky_typography_widget_area_title_style'				=> 'default',
	'fallsky_typography_widget_area_title_font-weight' 			=> '600',
	'fallsky_typography_widget_area_title_letter-spacing' 		=> '0',
	'fallsky_typography_widget_area_title_text-transform'		=> 'capitalize',
	'fallsky_typography_widget_area_title_font-style' 			=> 'normal',

	'fallsky_typography_page_title_font-weight' 				=> '600',
	'fallsky_typography_page_title_letter-spacing' 				=> '0',
	'fallsky_typography_page_title_text-transform' 				=> 'capitalize',
	'fallsky_typography_page_title_font-style' 					=> 'normal',
	
	'fallsky_typography_section_widget_title_font-weight' 		=> '600',
	'fallsky_typography_section_widget_title_letter-spacing' 	=> '0',
	'fallsky_typography_section_widget_title_text-transform' 	=> 'capitalize',
	'fallsky_typography_section_widget_title_font-style' 		=> 'normal',
	'fallsky_typography_hide_widget_title_decor'				=> '',
	'fallsky_typography_hide_section_title_decor'				=> 'on',

	/** Category links **/
	'fallsky_typography_category_links_font'					=> 'heading-font',
	'fallsky_typography_category_links_color'					=> 'text-color',

	/** section advertisement site top **/
	'fallsky_ads_source_site_top' 								=> 'custom',
	'fallsky_ads_custom_url_site_top' 							=> '',
	'fallsky_ads_custom_image_site_top' 						=> '',
	'fallsky_ads_custom_image_width_site_top'					=> '1000',
	'fallsky_ads_custom_new_tab_site_top' 						=> '',
	'fallsky_ads_embed_code_site_top' 							=> '',

	/** section advertisement before single post content **/
	'fallsky_ads_source_before_single_post_content' 			=> 'custom',
	'fallsky_ads_custom_url_before_single_post_content' 		=> '',
	'fallsky_ads_custom_image_before_single_post_content' 		=> '',
	'fallsky_ads_custom_image_width_before_single_post_content'	=> '1000',
	'fallsky_ads_custom_new_tab_before_single_post_content' 	=> '',
	'fallsky_ads_embed_code_before_single_post_content' 		=> '',

	/** section advertisement after single post content **/
	'fallsky_ads_source_after_single_post_content' 				=> 'custom',
	'fallsky_ads_custom_url_after_single_post_content' 			=> '',
	'fallsky_ads_custom_image_after_single_post_content' 		=> '',
	'fallsky_ads_custom_image_width_after_single_post_content'	=> '1000',
	'fallsky_ads_custom_new_tab_after_single_post_content' 		=> '',
	'fallsky_ads_embed_code_after_single_post_content' 			=> '',

	/** section advertisement before single page content **/
	'fallsky_ads_source_before_single_page_content' 			=> 'custom',
	'fallsky_ads_custom_url_before_single_page_content' 		=> '',
	'fallsky_ads_custom_image_before_single_page_content' 		=> '',
	'fallsky_ads_custom_image_width_before_single_page_content'	=> '1000',
	'fallsky_ads_custom_new_tab_before_single_page_content' 	=> '',
	'fallsky_ads_embed_code_before_single_page_content' 		=> '',

	/** section advertisement after single page content **/
	'fallsky_ads_source_after_single_page_content' 				=> 'custom',
	'fallsky_ads_custom_url_after_single_page_content' 			=> '',
	'fallsky_ads_custom_image_after_single_page_content' 		=> '',
	'fallsky_ads_custom_image_width_after_single_page_content'	=> '1000',
	'fallsky_ads_custom_new_tab_after_single_page_content' 		=> '',
	'fallsky_ads_embed_code_after_single_page_content' 			=> '',

	/** section animations **/
	'fallsky_enable_parallax_on_homepage_fullwidth_area'		=> 'on',
	'fallsky_enable_parallax_on_page_header'					=> 'on',
	'fallsky_enable_parallax_on_post_header'					=> 'on',
	'fallsky_enable_parallax_on_zigzag_post_archive'			=> 'on',

	/** section signup form **/
	'fallsky_popup_signup_form_code'							=> ''
));