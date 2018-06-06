/**
* Copyright (c) Loft.Ocean
* http://www.loftocean.com
*/

(function(api, $, parent){
	"use strict";

	/** Global jQuery objects **/
	var homepage_custom_content_timer = '',
		$body = $('body'), $page = $('#page'), $head = $('head'), 
		$home = $('body.home'), $category = $('body.category'), $tag = $('body.tag'), 
		$category_index = $('body.category-index-page'), $single = $('body.single.single-post'),
		$more = $('.more-btn .more-link.button span'), 
		$fullscreen_site_header = $('.fallsky-fullmenu'),
		$search_screen = $('.search-screen'), $site_footer = $('.site-footer'),
		$site_sidebar = $('#secondary.sidebar'),
		$site_footer_bottom = $site_footer.find('.footer-bottom'),
		refresh_slider_ids = [			
			'fallsky_home_featured_posts_maybe_refresh',	
			'fallsky_home_featured_posts'
		];

	/**
	* Get attachment url
	* @param string attachment id
	* @return mix if attachment exists attachment url, otherwise boolean false
	*/
	function getAttachmentUrl(id){
		if(id && parent && parent.wp && parent.wp.media){
			var attachment = parent.wp.media.attachment(id);
			attachment.fetch();
			return attachment && attachment.attributes ? attachment.get('url') : false;
		}
		return false;
	}
	/**
	* Set inline styles to <head>
	* @param style id
	* @param string style
	*/
	function updateStyle(id, style){
		var $style 	= $head.find('#' + id);
		style 	= style || '';
		if(!$style.length){
			$style = $('<style>', {'id': id}).appendTo($head);
		}
		$style.html(style);
	}
	/**
	* Get selector string by join the array together
	* @param array selector list
	* @return string selector 
	*/
	function getSelector(list){
		return list.join(', ');
	}
	/***
	* Get customize control
	* @param string control id
	* @return customize control
	*/
	function getControl(id){
		return parent.wp && parent.wp.customize && parent.wp.customize.control ? parent.wp.customize.control(id) : null;
	}
	/**
	* Show the top x instagram feeds
	* @param jQuery object array, instagram list
	* @param int the number of item to show
	*/
	function showInstagramItems($feeds, num){
		var length = $feeds.length;
		if(length){
			if(length > num){
				var $hide_first = $feeds.eq(num);
				$feeds.addClass('hide');
				$hide_first.prevAll().removeClass('hide');
			}
			else{
				$feeds.removeClass('hide');
			}
		}
	}
	/**
	* Update settings for slick slider
	* @param jquery object slider
	* @param object new settings
	*/
	function updateSlickSlider($slider, settings){
		if($slider.length && settings){
			$slider.slick('slickSetOption', settings, true);
		}
	}
	/**
	* Update slick slider arguments if needed
	* @param string argument element id
	* @param string argument element value
	* @param boolean to also update the current slider
	*/
	function updateSliderArguments(id, value){
		if(fallsky && fallsky.featured_slider_customize){
			$.each(fallsky.featured_slider_customize, function(aid, args){
				fallsky.featured_slider_customize[aid][id] = value;
			});
		}
	} 
	/***
	* Add event handler for selective refresh to enable slick slider if needed
	* 	Event handler for partial-content-rendered
	*/
	api.selectiveRefresh.bind('partial-content-rendered', function(placement){
		var $slider = $(placement.addedContent).find('.top-slider');
		if($slider.length && (refresh_slider_ids.indexOf(placement.partial.id) !== -1)){
			var style = $slider.data('style');
			$slider = $('.top-slider'); 
			if(fallsky.featured_slider_customize && fallsky.featured_slider_customize[style]){
				var settings = $.extend({}, fallsky.featured_slider_customize[style]);
				fallsky_slick_slider($slider, settings);
			}
		}
		var $bgs = $(placement.addedContent).find('.top-slider, .top-blocks');
		if($bgs.length && $.fn.loftocean_image_preloader){
			$('.top-slider, .top-blocks').loftocean_image_preloader();
		}
	});

	/** Customize setting event hanlder if their transort are set with postMessage **/
	api('blogname', function(value){
		value.bind(function(to){
			$('.site-title a').text(to);
		});
	});
	api('blogdescription', function(value){
		value.bind(function(to){
			$('.site-description').text(to);
		});
	});
	api('fallsky_page_background_image', function(value){
		value.bind(function(to){
			var url = to ? getAttachmentUrl(to) : false;
			updateStyle('fallsky-page-bg-image', (url ? '#page { background-image: url(' + url + '); }' : ''));
		});
	});
	api('fallsky_page_background_position_x', function(value){
		value.bind(function(to){
			to ? updateStyle('fallsky-page-bg-image-position-x', '#page { background-position-x: ' + to + '; }') : '';
		});
	});
	api('fallsky_page_background_position_y', function(value){
		value.bind(function(to){
			to ? updateStyle('fallsky-page-bg-image-position-y', '#page { background-position-y: ' + to + '; }') : '';
		});
	});
	api('fallsky_page_background_size', function(value){
		value.bind(function(to){
			to ? updateStyle('fallsky-page-bg-image-size', '#page { background-size: ' + to + '; }') : '';
		});
	});
	api('fallsky_page_background_repeat', function(value){
		value.bind(function(to){
			updateStyle('fallsky-page-bg-image-repeat', '#page { background-repeat: ' + (to ? 'repeat' : 'no-repeat') + '; }');
		});
	});
	api('fallsky_page_background_attachment', function(value){
		value.bind(function(to){
			updateStyle('fallsky-page-bg-image-attachment', '#page { background-attachment: ' + (to ? 'scroll' : 'fixed') + '; }');
		});
	});
	api('fallsky_site_layout', function(value){
		value.bind(function(to){
			if($body.length){
				$body.removeClass('site-layout-fullwidth site-layout-boxed site-layout-frame').addClass(to);
				('site-layout-fullwidth' == to) ? $body.removeClass('custom-background') : $body.addClass('custom-background');
			}
		});
	});

	api('fallsky_dark_color_scheme_custom_text', function(value){
		value.bind(function(to){
			$more.length ? $more.text(to) : '';
		});
	});
	api('fallsky_site_color_scheme', function(value){
		value.bind(function(to){
			$body.removeClass('light-color dark-color').addClass(to);
		});
	});
	api('fallsky_accent_color', function(value){
		value.bind(function(to){
			to == 'custom' ? $body.addClass('primary-color-enabled') : $body.removeClass('primary-color-enabled');
		});
	});
	api('fallsky_light_color_scheme_custom_bg', function(value){
		value.bind(function(to){
			if(to){
				updateStyle(
					'fallsky-site-light-color-custom-bg',
					getSelector([
						'.light-color',
						'.light-color #page'
					]) + '{ background-color: ' + to + '; }'
				);
			}
			else{
				updateStyle('fallsky-site-light-color-custom-bg', '');
			}
		});
	});
	api('fallsky_light_color_scheme_custom_text', function(value){
		value.bind(function(to){
			if(to){
				updateStyle(
					'fallsky-site-light-color-custom-text',
					getSelector([
						'.light-color',
						'.light-color .post-entry h1',
						'.light-color .post-entry h2',
						'.light-color .post-entry h3',
						'.light-color .post-entry h4',
						'.light-color .post-entry h5',
						'.light-color .post-entry h6',
						'.light-color .post-entry form label',
						'.light-color blockquote'
					]) + '{ color: ' + to + '; }'
				);
			}
			else{
				updateStyle('fallsky-site-light-color-custom-text', '');
			}
		});
	});
	api('fallsky_light_color_scheme_custom_content', function(value){
		value.bind(function(to){
			if(to){
				updateStyle(
					'fallsky-site-light-color-custom-content',
					getSelector([
						'.light-color .post-entry'
					]) + '{ color: ' + to + '; }'
				);
			}
			else{
				updateStyle('fallsky-site-light-color-custom-content', '');
			}
		});
	});
	api('fallsky_dark_color_scheme_custom_bg', function(value){
		value.bind(function(to){
			if(to){
				updateStyle(
					'fallsky-site-dark-color-custom-bg',
					getSelector([
						'.dark-color',
						'.dark-color #page'
					]) + '{ background-color: ' + to + '; }'
				);
			}
			else{
				updateStyle('fallsky-site-dark-color-custom-bg', '');
			}
		});
	});
	api('fallsky_dark_color_scheme_custom_text', function(value){
		value.bind(function(to){
			if(to){
				updateStyle(
					'fallsky-site-dark-color-custom-text',
					getSelector([
						'.dark-color',
						'.dark-color .post-entry h1',
						'.dark-color .post-entry h2',
						'.dark-color .post-entry h3',
						'.dark-color .post-entry h4',
						'.dark-color .post-entry h5',
						'.dark-color .post-entry h6',
						'.dark-color .post-entry form label',
						'.dark-color blockquote'
					]) + '{ color: ' + to + '; }'
				);
			}
			else{
				updateStyle('fallsky-site-dark-color-custom-text', '');
			}
		});
	});
	api('fallsky_dark_color_scheme_custom_content', function(value){
		value.bind(function(to){
			if(to){
				updateStyle(
					'fallsky-site-dark-color-custom-content',
					getSelector([
						'.dark-color .post-entry'
					]) + '{ color: ' + to + '; }'
				);
			}
			else{
				updateStyle('fallsky-site-dark-color-custom-bg', '');
			}
		});
	});

	//Site header
	api('fallsky_sticky_site_header', function(value){
		value.bind(function(to){
			var $site_header = $('#masthead'), $content = $('#content');
			$site_header.attr('data-sticky', to);
			if(!to){
				$site_header.removeClass('sticky');
				$content.css('padding-top', '');
			}
		});
	});
	api('fallsky_enable_hamburge_menu_button', function(value){
		value.bind(function(to){
			var $site_header = $('#masthead');
			to ? $site_header.addClass('menu-btn-show') : $site_header.removeClass('menu-btn-show');
		});
	});
	api('fallsky_hamburge_menu_button_style', function(value){
		value.bind(function(to){
			var $toggle_btn = $('#masthead #menu-toggle');
			to ? $toggle_btn.addClass('icon-only') : $toggle_btn.removeClass('icon-only');
		});
	});
	api('fallsky_no_space_between_site_header_and_content', function(value){
		value.bind(function(to){
			to ? $body.addClass('remove-page-top-space') : $body.removeClass('remove-page-top-space');
		});
	});
	api('fallsky_show_search_button', function(value){
		value.bind(function(to){
			var $search_btn = $('#masthead #site-header-search');
			if($search_btn.length){
				to ? $search_btn.removeClass('hide') : $search_btn.addClass('hide');
			}
		});
	});
	api('fallsky_search_button_style', function(value){
		value.bind(function(to){
			var $search_btn = $('#masthead #site-header-search');
			if($search_btn.length){
				var $btn = $search_btn.children('.search-button');
				$search_btn.removeClass('text-only icon-only').addClass(to);
				('text-only' == to) ? $btn.html($btn.text())
					: $('<span>', {'class': 'screen-reader-text', 'text': $btn.text()}).appendTo($btn.html(''));
			}
		});
	});
	api('header_image', function(value){
		value.bind(function(to){
			var style_bg_image = '';
			switch(to){
				case 'random-uploaded-image':
					var control = getControl('header_image');
					if(control && $(control.container).find('.header-view img').length){
						var $images = $(control.container).find('.header-view img'),
							index 	= Math.floor(Math.random() * $images.length);
						style_bg_image = '#page .site-header { background-image: url(' + $images.eq(index).attr('src') + '); }';
					}
					break;
				case 'remove-header':
					break;
				default:
					style_bg_image = '#page .site-header { background-image: url(' + to + '); }';
			}
			updateStyle('fallsky-site-header-bg-image', style_bg_image);
		});
	});
	api('fallsky_site_header_color_scheme', function(value){
		value.bind(function(to){
			var $site_header = $('#masthead');
			$site_header.removeClass('site-header-color-light site-header-color-dark').addClass(to);
		});
	});
	api('fallsky_site_header_bg_color', function(value){
		value.bind(function(to){
			updateStyle('fallsky-site-header-bg-color', (to ? '#page .site-header {background-color: ' + to + '; }' : ''));
		});
	});

	// Fullscreen site header
	api('fallsky_fullscreen_menu_show_search_form', function(value){
		value.bind(function(to){
			var $search_form = $fullscreen_site_header.find('.search');
			if($search_form.length){
				to ? $search_form.removeClass('hide') : $search_form.addClass('hide');
			}
		});
	});
	api('fallsky_fullscreen_menu_show_social_menu', function(value){
		value.bind(function(to){
			var $social_menu = $fullscreen_site_header.find('.social-navigation');
			if($social_menu.length){
				to ? $social_menu.removeClass('hide') : $social_menu.addClass('hide');
			}
		});
	});
	api('fallsky_fullscreen_menu_copyright_text', function(value){
		value.bind(function(to){
			var $copyright = $fullscreen_site_header.find('.text');
			if($copyright.length){
				to ? $copyright.html($('<div>').append(to).html()).removeClass('hide')
					: $copyright.html('').addClass('hide');
			}
		});
	});
	api('fallsky_fullscreen_menu_bg_image', function(value){
		value.bind(function(to){
			var url = to ? getAttachmentUrl(to) : false;
			updateStyle('fallsky-fullscreen-menu-bg-image', (url ? '.fallsky-fullmenu .fullscreen-bg { background-image: url(' + url + '); }' : ''));
			if($fullscreen_site_header.length){
				var enabled = api('fallsky_fullscreen_menu_enable_overlay')();
				(url && enabled) ? $fullscreen_site_header.addClass('has-overlay') : $fullscreen_site_header.removeClass('has-overlay');
			}
		});
	});
	api('fallsky_fullscreen_menu_bg_position_x', function(value){
		value.bind(function(to){
			to ? updateStyle('fallsky-fullscreen-menu-bg-image-position-x', '.fallsky-fullmenu .fullscreen-bg { background-position-x: ' + to + '; }') : '';
		});
	});
	api('fallsky_fullscreen_menu_bg_position_y', function(value){
		value.bind(function(to){
			to ? updateStyle('fallsky-fullscreen-menu-bg-image-position-y', '.fallsky-fullmenu .fullscreen-bg { background-position-y: ' + to + '; }') : '';
		});
	});
	api('fallsky_fullscreen_menu_bg_size', function(value){
		value.bind(function(to){
			to ? updateStyle('fallsky-fullscreen-menu-bg-image-size', '.fallsky-fullmenu .fullscreen-bg { background-size: ' + to + '; }') : '';
		});
	});
	api('fallsky_fullscreen_menu_bg_repeat', function(value){
		value.bind(function(to){
			updateStyle('fallsky-fullscreen-menu-bg-image-repeat', '.fallsky-fullmenu .fullscreen-bg { background-repeat: ' + (to ? 'repeat' : 'no-repeat') + '; }');
		});
	});
	api('fallsky_fullscreen_menu_bg_attachment', function(value){
		value.bind(function(to){
			updateStyle('fallsky-fullscreen-menu-bg-image-attachment', '.fallsky-fullmenu .fullscreen-bg { background-attachment: ' + (to ? 'scroll' : 'fixed') + '; }');
		});
	});
	api('fallsky_fullscreen_menu_bg_color', function(value){
		value.bind(function(to){
			updateStyle('fallsky-fullscreen-menu-bg-color', '.fallsky-fullmenu .fullscreen-bg { background-color: ' + to + '; }');
		});
	});
	api('fallsky_fullscreen_menu_text_color', function(value){
		value.bind(function(to){
			updateStyle('fallsky-fullscreen-menu-text-color', '.fallsky-fullmenu .container { color: ' + to + '; }');
		});
	});

	api('fallsky_fullscreen_menu_enable_overlay', function(value){
		value.bind(function(to){
			if($fullscreen_site_header.length){
				to ? $fullscreen_site_header.addClass('has-overlay') : $fullscreen_site_header.removeClass('has-overlay')
			}
		});
	});
	api('fallsky_fullscreen_menu_overlay_color', function(value){
		value.bind(function(to){
			updateStyle('fallsky-fullscreen-menu-overlay-color', (to ? '.fallsky-fullmenu.has-overlay .fullscreen-bg:after { background: ' + to + '; }' : ''));
		});
	});
	api('fallsky_fullscreen_menu_overlay_opacity', function(value){
		value.bind(function(to){
			updateStyle('fallsky-fullscreen-menu-overlay-opacity', '.fallsky-fullmenu.has-overlay .fullscreen-bg:after { opacity: ' + (to / 100) + '; }');
		});
	});

	api('fallsky_fullscreen_menu_no_border', function(value){
		value.bind(function(to){
			if($fullscreen_site_header.length){
				to ? $fullscreen_site_header.addClass('no-border') : $fullscreen_site_header.removeClass('no-border');
			}
		});
	});

	// Search screen
	api('fallsky_search_show_category', function(value){
		value.bind(function(to){
			var $categories = $search_screen.find('.shortcuts-cat');
			if($categories.length){
				to ? $categories.removeClass('hide') : $categories.addClass('hide');
			}
		});
	}); 
	api('fallsky_search_show_category_count', function(value){
		value.bind(function(to){
			var $count = $search_screen.find('.shortcuts-cat .counts');
			if($count.length){
				to ? $count.removeClass('hide') : $count.addClass('hide');
			}
		});
	});
	api('fallsky_search_bg_image', function(value){
		value.bind(function(to){
			var url = to ? getAttachmentUrl(to) : false;
			updateStyle('fallsky-search-screen-bg-image', (url ? '.search-screen .fullscreen-bg { background-image: url(' + url + '); }' : ''));
			if($search_screen.length){
				var enabled = api('fallsky_search_enable_overlay')();
				(url && enabled) ? $search_screen.addClass('has-overlay') : $search_screen.removeClass('has-overlay');
			}
		});
	});
	api('fallsky_search_bg_position_x', function(value){
		value.bind(function(to){
			to ? updateStyle('fallsky-search-screen-bg-image-position-x', '.search-screen .fullscreen-bg { background-position-x: ' + to + '; }') : '';
		});
	});
	api('fallsky_search_bg_position_y', function(value){
		value.bind(function(to){
			to ? updateStyle('fallsky-search-screen-bg-image-position-y', '.search-screen .fullscreen-bg { background-position-y: ' + to + '; }') : '';
		});
	});
	api('fallsky_search_bg_size', function(value){
		value.bind(function(to){
			to ? updateStyle('fallsky-search-screen-bg-image-size', '.search-screen .fullscreen-bg { background-size: ' + to + '; }') : '';
		});
	});
	api('fallsky_search_bg_repeat', function(value){
		value.bind(function(to){
			updateStyle('fallsky-search-screen-bg-image-repeat', '.search-screen .fullscreen-bg { background-repeat: ' + (to ? 'repeat' : 'no-repeat') + '; }');
		});
	});
	api('fallsky_search_bg_attachment', function(value){
		value.bind(function(to){
			updateStyle('fallsky-search-screen-bg-image-attachment', '.search-screen .fullscreen-bg { background-attachment: ' + (to ? 'scroll' : 'fixed') + '; }');
		});
	});
	api('fallsky_search_bg_color', function(value){
		value.bind(function(to){
			updateStyle('fallsky-search-screen-bg-color', '.search-screen .fullscreen-bg { background-color: ' + to + '; }');
		});
	});
	api('fallsky_search_text_color', function(value){
		value.bind(function(to){
			updateStyle('fallsky-search-screen-text-color', '.search-screen .container { color: ' + to + '; }');
		});
	});
	api('fallsky_search_enable_overlay', function(value){
		value.bind(function(to){
			if($search_screen.length){
				to ? $search_screen.addClass('has-overlay') : $search_screen.removeClass('has-overlay')
			}
		});
	});
	api('fallsky_search_overlay_color', function(value){
		value.bind(function(to){
			updateStyle('fallsky-search-overlay-color', (to ? '.search-screen.has-overlay .fullscreen-bg:after { background: ' + to + '; }' : ''));
		});
	});
	api('fallsky_search_overlay_opacity', function(value){
		value.bind(function(to){
			updateStyle('fallsky-search-overlay-opacity', '.search-screen.has-overlay .fullscreen-bg:after { opacity: ' + (to / 100) + '; }');
		});
	});
	api('fallsky_search_no_border', function(value){
		value.bind(function(to){
			if($search_screen.length){
				to ? $search_screen.addClass('no-border') : $search_screen.removeClass('no-border');
			}
		});
	});



	// Site footer
	api('fallsky_site_footer_enable_instagram', function(value){
		value.bind(function(to){
			var $instagram = $site_footer.find('#fallsky-site-footer-instagram');
			if($instagram.length){
				to ? $instagram.removeClass('hide') : $instagram.addClass('hide');
			}
		});
	});
	api('fallsky_site_footer_instagram_title', function(value){
		value.bind(function(to){
			var $instagram_title = $site_footer.find('#fallsky-site-footer-instagram .widget-title');
			if($instagram_title.length && $instagram_title.children('a').length){
				to ? $instagram_title.removeClass('hide').children('a').text($('<div>', {'text': to}).text())
						: $instagram_title.addClass('hide').children('a').text('');
			}
		});
	});
	api('fallsky_site_footer_instagram_title_layout', function(value){
		value.bind(function(to){
			var $instagram_title = $site_footer.find('#fallsky-site-footer-instagram .widget-title');
			if($instagram_title.length){
				to ? $instagram_title.addClass('overlay-title') : $instagram_title.removeClass('overlay-title');
			}
		});
	});
	api('fallsky_site_footer_instagram_columns', function(value){
		value.bind(function(to){
			var $instagram = $site_footer.find('#fallsky-site-footer-instagram');
			if($instagram.length){
				var rows = 1;
				to = to && (to < 9 && to > 3) ? to : 6;
				$instagram.data('columns', to).removeClass('column-4 column-5 column-6 column-7 column-8').addClass('column-' + to);
				showInstagramItems($instagram.find('li'), to * rows);
			}
		});
	});
	api('fallsky_site_footer_instagram_rows', function(value){
		value.bind(function(to){
			var $instagram = $site_footer.find('#fallsky-site-footer-instagram');
			if($instagram.length){
				var columns = $instagram.data('columns');
				to = to && (to < 4 && to > 0) ? to : 1;
				$instagram.data('rows', to);
				showInstagramItems($instagram.find('li'), to * columns);
			}
		});
	});
	api('fallsky_site_footer_instagram_fullwidth', function(value){
		value.bind(function(to){
			var $instagram = $site_footer.find('#fallsky-site-footer-instagram');
			if($instagram.length){
				to ? $instagram.addClass('fullwidth') : $instagram.removeClass('fullwidth');
			}
		});
	});
	api('fallsky_site_footer_instagram_space', function(value){
		value.bind(function(to){
			var $items 	= $site_footer.find('#fallsky-site-footer-instagram li'),
				$ul 	= $site_footer.find('#fallsky-site-footer-instagram ul');
			if($items.length){
				to = to && (to > 0 && to < 31) ? to : 0;
				to ? ($items.css('padding', to + 'px'), $ul.css('margin', '-' + to + 'px')) 
					: $items.css('padding', '0px'), $ul.css('margin', '0px');
			}
		});
	});
	api('fallsky_site_footer_bottom_layout', function(value){
		value.bind(function(to){
			if($site_footer_bottom.length){
				to ? $site_footer_bottom.addClass('column-2') : $site_footer_bottom.removeClass('column-2');
			}
		});
	});
	api('fallsky_site_footer_bottom_enable_menu', function(value){
		value.bind(function(to){
			if($site_footer_bottom.length){
				var $footer_menu = $site_footer_bottom.find('.preview-footer-bottom-menu')
				if($footer_menu.length){
					to ? $footer_menu.removeClass('hide') : $footer_menu.addClass('hide');
				}
			}
		});
	});
	api('fallsky_site_footer_bottom_text', function(value){
		value.bind(function(to){
			if($site_footer_bottom.length){
				var $text = $site_footer_bottom.find('.footer-site-info');
				if($text.length){
					to ? $text.removeClass('hide').find('.textwidget').html($('<div>', {'html': to}).html()) 
						: $text.addClass('hide').find('.textwidget').html('');
				}
			}
		});
	});
	api('fallsky_site_footer_bg_image', function(value){
		value.bind(function(to){
			var url = to ? getAttachmentUrl(to) : false;
			updateStyle('fallsky-site-footer-bg-image', (url ? '#page .site-footer { background-image: url(' + url + '); }' : ''));
		});
	});
	api('fallsky_site_footer_bg_position_x', function(value){
		value.bind(function(to){
			to ? updateStyle('fallsky-site-footer-bg-image-position-x', '#page .site-footer { background-position-x: ' + to + '; }') : '';
		});
	});
	api('fallsky_site_footer_bg_position_y', function(value){
		value.bind(function(to){
			to ? updateStyle('fallsky-site-footer-bg-image-position-y', '#page .site-footer { background-position-y: ' + to + '; }') : '';
		});
	});
	api('fallsky_site_footer_bg_size', function(value){
		value.bind(function(to){
			to ? updateStyle('fallsky-site-footer-bg-image-size', '#page .site-footer { background-size: ' + to + '; }') : '';
		});
	});
	api('fallsky_site_footer_bg_repeat', function(value){
		value.bind(function(to){
			updateStyle('fallsky-site-footer-bg-image-repeat', '#page .site-footer { background-repeat: ' + (to ? 'repeat' : 'no-repeat') + '; }');
		});
	});
	api('fallsky_site_footer_bg_attachment', function(value){
		value.bind(function(to){
			updateStyle('fallsky-site-footer-bg-image-attachment', '#page .site-footer { background-attachment: ' + (to ? 'scroll' : 'fixed') + '; }');
		});
	});
	api('fallsky_site_footer_bg_color', function(value){
		value.bind(function(to){
			to ? updateStyle('fallsky-site-footer-bg-color', '#page .site-footer { background-color: ' + to + '; }')
				: updateStyle('fallsky-site-footer-bg-color', '');
		});
	});
	api('fallsky_site_footer_text_color', function(value){
		value.bind(function(to){
			to ? updateStyle('fallsky-site-footer-text-color', '#page .site-footer { color: ' + to + '; }')
				: updateStyle('fallsky-site-footer-text-color','');
		});
	});
	api('fallsky_site_footer_color_scheme', function(value){
		value.bind(function(to){
			if($site_footer.length){
				$site_footer.removeClass('light-color dark-color').addClass(to);
			}
		});
	});

	// Site sidebar
	api('fallsky_sidebar_enable_sticky', function(value){
		value.bind(function(to){
			if($site_sidebar.length){
				to ? $site_sidebar.attr('data-sticky', 'sidebar-sticky') : $site_sidebar.attr('data-sticky', '');
				if(!to){
					$('#secondary .sidebar-container').css({'position': '', 'top': ''});
				}
			}
		});
	});
	api('fallsky_sidebar_widgets_style', function(value){
		value.bind(function(to){
			if($site_sidebar.length){
				$site_sidebar.removeClass('with-border with-bg');
				to ? $site_sidebar.addClass(to) : '';
			}
		});
	});
	api('fallsky_sidebar_widgets_bg_color', function(value){
		value.bind(function(to){
			to ? updateStyle('fallsky-site-sidebar-bg-color', '#secondary.sidebar.with-bg .widget { background-color: ' + to + '; }')
				: updateStyle('fallsky-site-sidebar-bg-color','');
		});
	});
	api('fallsky_sidebar_widgets_border_color', function(value){
		value.bind(function(to){
			to ? updateStyle('fallsky-site-sidebar-border-color', '#secondary.sidebar.with-border .widget { border-color: ' + to + '; }')
				: updateStyle('fallsky-site-sidebar-border-color', '');
		});
	});
	api('fallsky_sidebar_widgets_text_color', function(value){
		value.bind(function(to){
			to ? updateStyle('site-sidebar-text-color', '#secondary.sidebar.with-bg .widget, #secondary.sidebar.with-border .widget { color: ' + to + '; }')
				: updateStyle('site-sidebar-text-color','');
		});
	});

	// Homepage
	api('fallsky_home_transparent_site_header', function(value){
		value.bind(function(to){
			var $homepage_header = $('body.front-page #masthead');
			if($homepage_header.length){
				to ? $homepage_header.addClass('transparent') : $homepage_header.removeClass('transparent');
			}
		});
	});
	var sidebar_options = {
		'home': 		'body.front-page',
		'category': 	'body.category',
		'tag': 			'body.tag',
		'author': 		'body.author',
		'search': 		'body.search',
		'date': 		'body.date',
		'post_format': 	'body.archive.tax-post_format',
		'blog': 		'body.blog',
		'category_index': 'body.category-index-page'
	};
	$.each(sidebar_options, function(i, body){
		api('fallsky_' + i + '_sidebar', function(value){
			value.bind(function(to){
				var $body_wrap = $(body); 
				if($body_wrap.length){
					$body_wrap.find('#content').removeClass('with-sidebar-left with-sidebar-right').addClass(to);
					if($site_sidebar.length){
						to ? $site_sidebar.removeClass('hide') : $site_sidebar.addClass('hide');
					}
				}
			});
		});
	});
	api('fallsky_home_posts_slider_auto_play', function(value){
		value.bind(function(to){
			if($home.length){
				var $slider = $home.find('.top-slider .slider-wrapper');
				if($slider.length){
					var enable = !!to;
					updateSliderArguments('autoplay', enable);
					updateSlickSlider($slider, {'autoplay': enable});
				}
			}
		});
	});
	api('fallsky_home_posts_slider_auto_play_pause_duration', function(value){
		value.bind(function(to){
			if($home.length){
				var $slider = $home.find('.top-slider .slider-wrapper');
				if($slider.length){
					var pause = parseInt(to) * 1000;
					updateSliderArguments('autoplaySpeed', pause);
					updateSlickSlider($slider, {'autoplaySpeed': pause});
				}
			}
		});
	});
	api( 'fallsky_home_posts_slider_pause_on_hover', function( value ) {
		value.bind( function( to ) {
			if( $home.length ) {
				var $slider = $home.find( '.top-slider .slider-wrapper' );
				if( $slider.length ) {
					var enable = !!to;
					updateSliderArguments( 'pauseOnHover', enable );
					updateSlickSlider( $slider, { 'pauseOnHover': enable } );
				}
			}
		} );
	} );
	api('fallsky_home_posts_slider_hide_category', function(value){
		value.bind(function(to){
			if($home.length){
				var $categories = $home.find('.top-slider .slider-wrapper .cat-links');
				if($categories.length){
					to ? $categories.addClass('hide') : $categories.removeClass('hide');
				}
			}
		});
	});
	api('fallsky_home_posts_hide_excerpt', function(value){
		value.bind(function(to){
			if($home.length){
				var $excerpts = $home.find('.top-slider .slider-wrapper .post-excerpt');
				if($excerpts.length){
					to ? $excerpts.addClass('hide') : $excerpts.removeClass('hide');
				}
			}
		});
	});

	api('fallsky_home_posts_block_hide_category', function(value){
		value.bind(function(to){
			if($home.length){
				var $categories = $home.find('.top-blocks .blocks-wrapper .cat-links');
				if($categories.length){
					to ? $categories.addClass('hide') : $categories.removeClass('hide');
				}
			}
		});
	});

	api('fallsky_home_custom_content_editor', function(value){
		value.bind(function(to){
			var $featured = $('.featured-section.style-custom');
			if($home.length && $featured.length){
				homepage_custom_content_timer ? clearTimeout(homepage_custom_content_timer) : '';
				homepage_custom_content_timer = setTimeout(function(){
					parent.wp.customize.previewer.refresh();
				}, 1000);
			}
		});
	});
	api('fallsky_home_custom_content_bg_image', function(value){
		value.bind(function(to){
			var url 	= to ? getAttachmentUrl(to) : false,
				$bg 	= $('.featured-section.style-custom .section-bg').length ? $('.featured-section.style-custom .section-bg') 
								: $('<div>', {'class': 'section-bg'}).prependTo($('.featured-section.style-custom > .custom-content')),
				$bg_img = $bg.find('.section-bg-img'); 
			if(url){
				var background_image = 'url(' + url + ')';
				$bg_img.length ? $bg_img.css('background-image', background_image) 
					: $('<div>', {'class': 'section-bg-img', 'style': 'background-image: ' + background_image + ';'}).appendTo($bg);
			}
			else{
				$bg_img.length ? $bg.html('') : '';
			}
		});
	});
	api('fallsky_home_custom_content_bg_position_x', function(value){
		value.bind(function(to){
			to ? updateStyle(
				'fallsky-home-custom-content-bg-position-x', 
				'.featured-section.style-custom .section-bg .section-bg-img { background-position-x: ' + to + '; }'
			) : '';
		});
	});
	api('fallsky_home_custom_content_bg_position_y', function(value){
		value.bind(function(to){
			to ? updateStyle(
				'fallsky-home-custom-content-bg-position-y', 
				'.featured-section.style-custom .section-bg .section-bg-img { background-position-y: ' + to + '; }'
			) : '';
		});
	});
	api('fallsky_home_custom_content_bg_size', function(value){
		value.bind(function(to){
			to ? updateStyle(
				'fallsky-home-custom-content-bg-size', 
				'.featured-section.style-custom .section-bg .section-bg-img { background-size: ' + to + '; }'
			) : '';
		});
	});
	api('fallsky_home_custom_content_bg_repeat', function(value){
		value.bind(function(to){
			updateStyle(
				'fallsky-home-custom-content-bg-repeat', 
				'.featured-section.style-custom .section-bg .section-bg-img { background-repeat: ' + (to ? 'repeat' : 'no-repeat') + '; }'
			);
		});
	});
	api('fallsky_home_custom_content_bg_attachment', function(value){
		value.bind(function(to){
			updateStyle(
				'fallsky-home-custom-content-bg-attachment', 
				'.featured-section.style-custom .section-bg .section-bg-img { background-attachment: ' + (to ? 'scroll' : 'fixed') + '; }'
			);
		});
	});
	api('fallsky_home_custom_content_bg_color', function(value){
		value.bind(function(to){
			if($home.length){
				if(to){
					updateStyle(
						'fallsky-home-custom-content-bg-color',
						'.featured-section.style-custom .section-bg { background-color: ' + to + '; }'
					);
				}
				else{
					updateStyle('fallsky-home-custom-content-bg-color', '');
				}
			}
		});
	});
	api('fallsky_home_custom_content_text_color', function(value){
		value.bind(function(to){
			if($home.length){
				if(to){
					updateStyle(
						'fallsky-home-custom-content-text-color',
						'.featured-section.style-custom { color: ' + to + '; }'
					);
				}
				else{
					updateStyle('fallsky-home-custom-content-text-color', '');
				}
			}
		});
	});
	api('fallsky_home_custom_content_height', function(value){
		value.bind(function(to){
			if($home.length){
				if(to){
					var height = 'min-height: ' + to + 'px;';
					updateStyle(
						'fallsky-home-custom-content-height',
						'.featured-section.style-custom .custom-content { ' + height + ' }'
					);
				}
				else{
					updateStyle('fallsky-home-custom-content-height', '');
				}
			}
		});
	});

	// Category
	api('fallsky_category_show_image', function(value){
		value.bind(function(to){
			if($category.length && $category.find('.page-header .featured-media-section').length){
				var $media_bg 	= $category.find('.page-header .featured-media-section'),
					$site_logo 	= $('.site-branding .custom-logo-link .custom-logo-alt');
				if(to){
					$category.addClass('page-header-with-bg'); 
					$media_bg.removeClass('hide');
					if($site_logo.length){
						$site_logo.removeClass('hide').closest('.custom-logo-link').addClass('with-logo-alt');
					}
				}
				else{
					$category.removeClass('page-header-with-bg'); 
					$media_bg.addClass('hide');
					if($site_logo.length){
						$site_logo.addClass('hide').closest('.custom-logo-link').removeClass('with-logo-alt');
					}
				}
				fallsky.pages.all.add_padding = !fallsky.pages.all.no_padding();
			}
		});
	});
	api('fallsky_category_show_subcategory_filter', function(value){
		value.bind(function(to){
			if($category.length && $category.find('#primary .cat-filter').length){
				var $filter = $category.find('#primary .cat-filter');
				to ? $filter.removeClass('hide') : $filter.addClass('hide');
			}
		});
	});
	var $archive_page_widget_cat = false, $widget_cat = false, widget_cat_pages = ['category', 'category-index-page'];
	$.each(widget_cat_pages, function(pi, pv){
		$archive_page_widget_cat = $('body.' + pv);
		if($archive_page_widget_cat.length && $archive_page_widget_cat.find('.widget.fallsky-widget_cat').length){
			$widget_cat = $archive_page_widget_cat.find('.widget.fallsky-widget_cat');
			var widget_cats = ['category_subcategory', 'category_index'];
			$.each(widget_cats, function(i, v){
				api('fallsky_' + v + '_style', function(value){
					value.bind(function(to){
						$widget_cat.removeClass('style-rectangle style-circle').addClass(to);
					});
				});
				api('fallsky_' + v + '_layout', function(value){
					value.bind(function(to){
						$widget_cat.removeClass('column-2 column-3 column-4 column-5').addClass(to);
					});
				});
				api('fallsky_' + v + '_show_post_count', function(value){
					value.bind(function(to){
						var $counts = $widget_cat.find('.cat-meta-wrapper .counts');
						to ? $counts.removeClass('hide') : $counts.addClass('hide');
					});
				});
			});
			return false;
		}
	});

	// Tag
	api('fallsky_tag_show_image', function(value){
		value.bind(function(to){
			if($tag.length && $tag.find('.page-header .featured-media-section').length){
				var $media_bg 	= $tag.find('.page-header .featured-media-section'),
					$site_logo 	= $('.site-branding .custom-logo-link .custom-logo-alt');
				if(to){
					$tag.addClass('page-header-with-bg');
					$media_bg.removeClass('hide');
					if($site_logo.length){
						$site_logo.removeClass('hide').closest('.custom-logo-link').addClass('with-logo-alt');
					}
				}
				else{
					$tag.removeClass('page-header-with-bg');
					$media_bg.addClass('hide');
					if($site_logo.length){
						$site_logo.addClass('hide').closest('.custom-logo-link').removeClass('with-logo-alt');
					}
				}
				fallsky.pages.all.add_padding = !fallsky.pages.all.no_padding();
			}
		});
	});

	// WooCommerce  
	api('fallsky_woocommerce_show_cart', function(value){
		value.bind(function(to){
			var $cart = $('#masthead #site-header-cart');
			if($cart.length){
				to ? $cart.removeClass('hide') : $cart.addClass('hide');
			}
		});
	});
	api('fallsky_woocommerce_cart_button_style', function(value){
		value.bind(function(to){
			var $cart = $('#masthead #site-header-cart');
			if($cart.length){
				to ? $cart.addClass('icon-only') : $cart.removeClass('icon-only');
			}
		});
	});

	// Single
	api('fallsky_sticky_post_nav', function(value){
		value.bind(function(to){
			if($single.length){
				to ? $('.post-nav').removeClass('hide') : $('.post-nav').addClass('hide');
			}
		});
	}); 
	api('fallsky_post_nav_color_scheme', function(value){
		value.bind(function(to){
			if($single.length){
				$('.post-nav').removeClass('light-color dark-color').addClass(to);
			}
		});
	});
	api('fallsky_post_nav_bg_color', function(value){
		value.bind(function(to){
			if($single.length){
				to ? updateStyle(
					'fallsky-post-nav-bg-color',
					'#page .post-nav { background-color: ' + to + '; }'
				) : updateStyle('fallsky-post-nav-bg-color', '');
			}
		});
	});
	api('fallsky_single_post_show_sharing_buttons_on_mobile', function(value){
		value.bind(function(to){
			var $sharing = $('.side-share-icons');
			if($single.length && $sharing.length){
				to ? $sharing.addClass('mobile-sticky') : $sharing.removeClass('mobile-sticky');
			}
		});
	});
	api('fallsky_single_post_related_posts_title', function(value){
		value.bind(function(to){
			var $title = $('.related-posts-title');
			if($single.length && $title.length){
				$title.text($('<div>', {'text': to}).text());
			}
		});
	});
	api('fallsky_comment_fold_reply_form', function(value){
		value.bind(function(to){
			var $reply = $('#comments .click-to-reply');
			if($single.length && $reply.length){
				to ? $reply.removeClass('clicked') : $reply.addClass('clicked');
			}
		});
	});

	// Typography hide widget title decor line
	api('fallsky_typography_hide_widget_title_decor', function(value){
		value.bind(function(to){
			to ? $body.addClass('hide-widget-title-decor') : $body.removeClass('hide-widget-title-decor');
		});
	});
	api('fallsky_typography_hide_section_title_decor', function(value){
		value.bind(function(to){
			to ? $body.addClass('hide-section-title-decor') : $body.removeClass('hide-section-title-decor');
		});
	});

	// Typography category links
	api('fallsky_typography_category_links_font', function(value){
		value.bind(function(to){
			var font_type = ('text-font' == to) ? 'text' : 'heading',
				font_family = api('fallsky_typography_' + font_type + '_font-family')(); 
			updateStyle(
				'fallsky-typography-category-links-font',
				'.cat-links { font-family: ' + font_family + '; }'
			);
		});
	});
	api('fallsky_typography_category_links_color', function(value){ 
		value.bind(function(to){
			var is_accent_color = ('accent-color' == to),
				accent_color = api('fallsky_accent_custom_color')(); console.log('change color' + (is_accent_color ? 'yes' : 'no'));
			updateStyle(
				'fallsky-typography-category-links-color',
				is_accent_color ? getSelector([
					'.primary-color-enabled .featured-section .top-blocks.style-blocks-1 .cat-links',
					'.primary-color-enabled .posts:not(.layout-overlay) .cat-links',
					'.primary-color-enabled.single .site-content .post-header-text .cat-links'
				]) + ' { color: ' + accent_color + '; }' : ''
			);
		});
	});
})(wp.customize, jQuery, parent);	