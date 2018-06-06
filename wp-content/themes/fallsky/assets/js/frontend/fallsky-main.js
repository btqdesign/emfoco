(function($){ 
	"use strict"; 
	var window_innerWidth = $(window).innerWidth(), window_innerHeight = $(window).innerHeight(), 
		$homepage_banner = $('.home-widget.ad-banner.fullwidth.large-banner-special .section-content'),
		$page = $('#page'), $site_top_ad = $('.sitetop-ad'), $site_header = $('#masthead'), loaded = false;

	fallsky 		= fallsky || {};
	fallsky.pages 	= fallsky.pages || {};

	/**
	* Sitewide helper functions
	* 	1. Reduce the resize event calling
	*/
	fallsky.helper = {
		'resizedWindow': false,
		'timerSet': false,
		'init': function(){
			var self = this;
			$(window).on('resize', function(){
				self.resizedWindow = true;
				if(!self.timerSet){
					self.setTimer();
				}
			});
		},
		'setTimer': function(){
			var self = this;
			self.timerSet = true;
			setTimeout(function(){
				if(self.resizedWindow){
					$(window).trigger('fallsky.window.resize');
					self.resizedWindow 	= false;
					self.timerSet 		= false;
				}
			}, 200);
		}
	}

	/**
	* For all pages features
	*	1. Scroll to top button
	* 	2. Sticky sidebar 
	* 	3. Sticky page header
	* 	4. Fallback css generate
	*/
	fallsky.pages.all = {
		'$window': 				false,
		'loaded': 				false,
		'is_customize': 		false,
		'add_padding': 			false,
		'parallax_elements': 	false,
		'masonry': 				false,
		'site_top_ad_loaded': 	false,
		'site_top_ad_bottom': 	0,
		'init': function(){
			var site = this;
			// Flag to tell if in customize mode
			this.is_customize = !!wp.customize;
			// Fix browsers which not support css variables
			this.fix_css_variable();
			
			this.$window = $(window);
			// Set the default scrollTop value, we will use this property to identify scroll direction
			this.$window.previousTop = this.$window.scrollTop();

			if($site_top_ad.length){
				$(document)
					.one('fallsky.site_top_ad.init', function(){
						var top = $(window).scrollTop(), ad_height = parseInt($site_top_ad.outerHeight(true, true)),
							page_top = $page.offset().top - parseInt($page.css('margin-top'));
						// For window inner width > 600 only, otherwise, just remove the inline styles
						if(window_innerWidth > 600){
							var opacity = (top <= page_top) ? 1 : (top < (page_top + ad_height) ? ((page_top + ad_height - top) / ad_height) : 0);
							$site_top_ad.css('opacity', opacity);
							(opacity === 0) ? $site_top_ad.css('pointer-events', 'none') : $site_top_ad.css('pointer-events', '');
						}
						else{
							$site_top_ad.css({'opacity': '', 'pointer-events': ''});
						}
					})
					.on('fallsky.site_top_ad.done', function(){
						var top = $(window).scrollTop();
						site.site_top_ad_bottom = $page.offset().top;
						site.site_top_ad_opacity(top);
						site.site_top_ad_loaded = true;
						site.maybe_sticky_header($(window).scrollTop(), false);
					});
			}
			else{
				site.site_top_ad_loaded = true;
				site.maybe_sticky_header($(window).scrollTop(), false);
			}

			this.$window.on('load', function(){
				site.load($(this).scrollTop());
			})
			.on('scroll', function(){
				var top = $(this).scrollTop(), goUp = false; 	
				if(Math.abs(top - site.$window.previousTop) > 2){
					goUp = top < site.$window.previousTop; 
					site.$window.previousTop = top;
					site.scroll(top, goUp);
				}
			})
			.on('resize', function(){
				site.add_padding = !site.no_padding();
			})
			.on('fallsky.window.resize', function(){
				var $target = $(this);
				site.add_padding = !site.no_padding();
				if($('.site-header').length){
					$('.site-header').removeAttr('data-threhold');
					site.scroll($(this).scrollTop(), false);
				}
			});
		},
		'load': function(top){
			var pages = this, has_site_top_ad = $site_top_ad.length;
			this.loaded = true;
			this.add_padding = !this.no_padding();
			if(top > 0){
				has_site_top_ad ? $(document).on('fallsky.site_top_ad.done', function(){ pages.maybe_sticky_sidebar(top, false); }) : this.maybe_sticky_sidebar(top, false);
				this.maybe_sticky_header(top, false);
			}
			if(fallsky.parallax_effect && fallsky.parallax_effect.length){
				has_site_top_ad ? $(document).on('fallsky.site_top_ad.done', function(){ pages.start_parallax(); }) : this.start_parallax();
			}
		},
		'scroll': function(top, goUp){
			if(this.loaded){
				this.maybe_sticky_sidebar(top, goUp);
				this.maybe_sticky_header(top, goUp);
				this.site_top_ad_opacity(top);
				if(this.parallax_elements && this.parallax_elements.length){
					this.run_parallax(top, goUp);
				}
			}
		},
		'site_top_ad_opacity': function(top){
			if(window_innerWidth > 600){
				var ad_height = $site_top_ad.outerHeight(true, true), ad_top = this.site_top_ad_bottom - ad_height,
					opacity = (top <= ad_top) ? 1 : (top < this.site_top_ad_bottom ? ((this.site_top_ad_bottom - top) / ad_height) : 0); 
				$site_top_ad.css('opacity', opacity);
				(opacity === 0) ? $site_top_ad.css('pointer-events', 'none') : $site_top_ad.css('pointer-events', '');
			}
			else{
				$site_top_ad.css({'opacity': '', 'pointer-events': ''})
			}
		},
		'get_sticky_sidebar_offset': function(){
			var body_top = $('body').hasClass('site-layout-frame') ? $('body').offset().top : $('body').offset().top - parseInt($('#page').css('margin-top')),
				init_top = body_top + 20,
				$header = $('.site-header'),
				$post_nav = $('.post-nav');

			if($post_nav.length && !$post_nav.hasClass('hide')){
				return $('.post-nav').outerHeight(true, true) + init_top;
			}
			else if($header.length && !$header.hasClass('hide') && $header.attr('data-sticky') && ('sticky' == $header.attr('data-sticky'))){
				if($header.hasClass('site-header-layout-6') && (window_innerWidth >= 1120)){
					return parseInt($header.find('#site-header-menu').outerHeight(true, true)) + parseInt(init_top);
				}
				else{
					if($header.attr('data-threhold')){
						return $header.attr('data-threhold');
					}
					else{
						var offset = $header.outerHeight(true, true) + init_top;
						$header.attr('data-threhold', offset);
						return offset;
					}
				}
			}
			return init_top;
		},
	 	'maybe_sticky_sidebar': function(top, goUp){
	 		if(this.loaded){
	 			var offset = this.get_sticky_sidebar_offset();
	 			$(document).trigger('fallsky.sidebar.change', [top, offset, goUp]);
	 		}
		},
		'no_padding': function(){
			return $('body.front-page #masthead.transparent').length
				|| ($('body.post-header-with-bg.post-template-1').length			&& $('.site-header.transparent').length)
				|| ($('body.category.page-header-with-bg').length 					&& $('.site-header.transparent').length && $('.featured-media-section .header-img').length && !$('.featured-media-section').hasClass('hide'))
				|| ($('body.tag.page-header-with-bg').length 						&& $('.site-header.transparent').length && $('.featured-media-section .header-img').length && !$('.featured-media-section').hasClass('hide'))
				|| ($('body.category-index-page.page-header-with-bg').length 		&& $('.site-header.transparent').length && $('.featured-media-section .header-img').length && !$('.featured-media-section').hasClass('hide'))
				|| ($('body.blog.page-header-with-bg').length 						&& $('.site-header.transparent').length && $('.featured-media-section .header-img').length && !$('.featured-media-section').hasClass('hide'))
				|| ($('body.post-type-archive-product.page-header-with-bg').length 	&& $('.site-header.transparent').length && $('.featured-media-section .header-img').length && !$('.featured-media-section').hasClass('hide'))
				|| ($('body.tax-product_cat.page-header-with-bg').length 			&& $('.site-header.transparent').length && $('.featured-media-section .header-img').length && !$('.featured-media-section').hasClass('hide'))
				|| ($('body.page-header-layout-2').length && $('.site-header.transparent').length && $('.featured-media-section .header-img').length && !$('.featured-media-section').hasClass('hide'));
		},
		'sticky_site_header_threshold': function(){
			var $header = $('.site-header');

			if($header.length && !$header.hasClass('hide')){
				if($header.data('sticky-threshold')){
					return $header.data('sticky-threshold');
				}
				else{
					var offset = parseInt($header.outerHeight(true, true)) + parseInt($('#page').offset().top);
					$header.data('sticky-threshold', offset);
					return offset;
				}
			}
			return 0;
		},
		'maybe_sticky_header': function(top, goUp){
			// If the site top ad not loaded, return false
			if(!this.site_top_ad_loaded){
				return false;
			}

			var $header = $('.site-header'), $content = $('#content');
			if($header.length && $content.length){
				if(!$('html.mobile').length && $('body.single.single-post').length && (window.innerWidth >= 600) && $('.post-nav').not('.hide').length){
					$header.removeClass('sticky is-sticky hide-header'); 
					$content.css('padding-top', '');
				}
				else if($header.attr('data-sticky')){ 
					var sticky_type = $header.attr('data-sticky'),
						check_top 	= this.sticky_site_header_threshold(),
						padding_top = ($header.hasClass('site-header-layout-6') && (window_innerWidth >= 1120)) 
							? $header.find('#site-header-menu').outerHeight(true, true) : $header.outerHeight(true, true);

					if('sticky' == sticky_type){
						if(!$header.hasClass('sticky') && (top >= check_top)){
							$header.addClass('sticky');
							if(this.add_padding){
								$content.css('padding-top', padding_top);
							}
						}
						else if($header.hasClass('sticky') && (top < check_top)){
							$header.removeClass('sticky');
							$content.css('padding-top', '');
						}
					}					
					else{
						 if(!$header.hasClass('is-sticky') && (top >= check_top)){
							$header.addClass('is-sticky');
							if(this.add_padding){
								$content.css('padding-top', padding_top);
							}
						}
						else if(!goUp && $header.hasClass('is-sticky')){
							$header.removeClass('show-header').addClass('hide-header');
						}
						else if(goUp && $header.hasClass('is-sticky') && (top >= check_top)){
							$header.removeClass('hide-header').addClass('show-header');
						}
						else if($header.hasClass('is-sticky') && (top < check_top)){
							$header.removeClass('is-sticky show-header hide-header');
							$content.css('padding-top', '');
						}
					}
				}
			}
		},
		'fix_css_variable': function(){
			var custom_opacity = '0.3', el = document.createElement('span');

			el.style.setProperty('--opacity', custom_opacity);
			el.style.setProperty('opacity', 'var(--opacity)');
			document.head.appendChild(el);
			if(getComputedStyle(el).opacity !== custom_opacity){
				var $fallback_tmpl = $('#fallsky-tmpl-fallback-styles');
				if($fallback_tmpl.length){
					var target_id = $fallback_tmpl.attr('data-dependency'), 
						$target = $('#' + target_id + '-inline-css').length ? $('#' + target_id + '-inline-css') : $('#' + target_id + '-css');
					if($target.length){
						$target.after($fallback_tmpl.html());
					}
				}
			}
			document.head.removeChild(el);
		},
		'add_parallax_items': function($items){
			if(!$items.length){
				return;
			}

			var $elements = $items.find('.featured-img .featured-img-container, .featured-img .gallery-item > div');
			if($elements.length){
				fallsky.pages.all.parallax_elements = fallsky.pages.all.parallax_elements.add($elements); 
			}
		},
		'start_parallax': function(){
			var $container 	= $('span', {'class': 'span-container'}),
				args 		= {
					'homepage-fullwidth': 	$('body.front-page .featured-section .post-bg-img, body.front-page .featured-section.style-custom .section-bg'),
					'page-header': 			$('.page-header .featured-media-section .header-img, body.page-header-layout-1 #content > .featured-media-section > .header-img'),
					'post-list-zigzag': 	$('.posts.layout-zigzag .featured-img .featured-img-container, .posts.layout-zigzag .featured-img .gallery-item > div'),
					'post-header': 			$('.post-header .featured-media-section .gallery-item > div, .post-header .featured-media-section .header-img, body.post-template-2 #content > .featured-media-section .gallery-item > div, body.post-template-2 #content > .featured-media-section .header-img, body.post-template-3 #content > .featured-media-section .gallery-item > div, body.post-template-3 #content > .featured-media-section .header-img'),
				};
			$.each(fallsky.parallax_effect, function(index, id){
				if((id in args) && args[id].length){
					$container = $container.add(args[id]);
				}
			});
			$container = $container.not($('.span-container'));
			if($container.length){ 
				this.parallax_elements = $container;
				this.run_parallax($(window).scrollTop(), false);
			}
		},
		'run_parallax': function(top, goUp){
			if(window.innerWidth > 1025){
				var site = this, topOffset = parseInt(this.get_sticky_sidebar_offset()) - 25,
					is_sticky_header = ($site_header.length && $site_header.data('sticky') && ('sticky' == $site_header.data('sticky')));
				this.parallax_elements.each(function(){
					var $item = $(this), $parent = $item.parent(), itemTop = $parent.offset().top, 
						is_zigzag = $item.closest('.posts.layout-zigzag').length, scrollTop = top,
						itemBottom = itemTop + $parent.outerHeight(true, true);
					// Change current top value if the site header is always sticky and current in zigzag post list
					if(is_zigzag && is_sticky_header){
						scrollTop = parseInt(top) + parseInt(topOffset);
					}
					if(scrollTop < itemTop){
						$item.css( { 'transform': '', 'transform-style': '' } );
					}
					else if((scrollTop >= itemTop) && (scrollTop <= itemBottom)){
						$item.css( { 'transform-style': 'preserve-3d', 'transform': ( 'translate3d(0, ' + (scrollTop - itemTop) / 2 + 'px, 0)' ) } );
					}
					else{
						$item.css( { 'transform-style': 'preserve-3d', 'transform': ( 'translate3d(0, ' + (itemBottom - itemTop) / 2 + 'px, 0)' ) } );
					}
				});
			}
			else{
				this.parallax_elements.css('transform', '');
			}
		},
		'process_masonry': function(reinit){
			var $masonry = $('.posts.layout-masonry'); 
			if($masonry.length){
				var $first = $masonry.first(), site = this;
				site.masonry = $masonry;
				$masonry.data('mobile-mode', false).each(function(){
					if($(this).find('.post').length){
						var $layout = $(this), list = [];
						$layout.find('.post').each(function(){ 
							list.unshift($(this).data('post-id'));
						});
						$layout.data('post-list', JSON.stringify(list));
					}
					else{
						$layout.data('post-list', false);
					}
				});
				if(!reinit){
					$(window).on('fallsky.window.resize', function(){
						if(site.masonry && site.masonry.length){
							var is_mobile = site.is_mobile_device(site.masonry), 
								current_mode_mobile = site.masonry.first().data('mobile-mode');
							if(is_mobile && !current_mode_mobile){
								site.masonry_mobile_mode(site.masonry);
							}
							else if(!is_mobile){
								if(current_mode_mobile){
									site.masonry_desktop_mode(site.masonry);
								}
								else{
									site.masonry.each(function(){
										var current = 0, lowest = false;
										$(this).find('.masonry-column').each(function(mci, mc){
											var cheight = $(this).outerHeight(true, true);
											$(this).data('column-height', cheight);
											if((cheight < lowest) || !lowest){
												lowest = $(this).outerHeight(true, true);
												current = mci;
											}
										});
										$(this).data('current', current);
									});
								}
							}
						}
					});
				}
				site.is_mobile_device($masonry) ? site.masonry_mobile_mode($masonry) 
					: (reinit ? site.masonry_desktop_mode($masonry) : $(window).on('load', function(){ site.masonry_desktop_mode($masonry, true); }));
			}
		},
		'is_mobile_device': function($layout){
			return $layout.length && $layout.find('.post').length && ($layout.first().width() <= $layout.find('.post').first().width());
		},
		'masonry_mobile_mode': function($layout){
			$layout.data('mobile-mode', true).each(function(){
				if($(this).data('post-list')){
					var list = JSON.parse($(this).data('post-list')), $posts = $(this).find('.post'), 
						$container = $(this).find('.masonry-column').data('column-height', 0).first();
					list.forEach(function(pid){  
						$container.prepend($posts.filter('.' + pid));
					});
					$(this).data('current', 0);
				}
			});
		},
		'masonry_desktop_mode': function($layout, resize){
			var option = resize ? {'trigger-sidebar-resize': true} : {};
			$layout.data('current', 0).find('.masonry-column').data('column-height', 0);
			$layout.data('mobile-mode', false).fallsky_masonry(option);
		}
	};

	fallsky.sidebar = {
		'is_sticky': 			false,
		'primary': 				false, 
		'secondary': 			false,
		'container': 			false,
		'afterSingleContent': 	false,
		'mainContent': 		 	false,
		'upThreshholdSet': 		false,
		'downThresholdSet': 	false,
		'sidebarMarginTop': 	'',
		'sidebarHeight': 		'',
		'primaryHeight': 		'',
		'delta': 				'',
		'previousAction': 		'',
		'thresholdMax': 		'',
		'thresholdMin': 		'',
		'init': function(){
			var self 				= this;
			self.primary 			= $('#primary');
			self.secondary 			= $('#secondary');
			self.container 			= $('#secondary .sidebar-container');
			self.afterSingleContent = $('body.single-post .content-after-post');
			self.mainContent 		= $('#content');

			if(self.afterSingleContent.length && self.primary.length && self.container.length && self.container.find('.widget').length){
				$(window).on('fallsky.window.resize', function(){ self.change_single_post_after_main_content_position(); });
				self.change_single_post_after_main_content_position();
			}
			if(self.container.length && self.container.find('.widget').length && self.primary.length){
				if(self.secondary.attr('data-sticky') == 'sidebar-sticky'){ 
					$(window).on('load', function(){ self.resize(); })
						.on('fallsky.window.resize', function(){ self.resize(); self.fix_sidebar(); });
					$(document).on('fallsky.sidebar.change', function(e, top, offset, goUp){ self.recalculate(top, offset, goUp); })
						.on('fallsky.sidebar.resize', function(){ self.resize(); self.fix_sidebar(); })
						.on('loftocean.facebook.rendered ajaxSuccess', function(){ self.resize(); self.fix_sidebar(); });
				}
			}
		},
		'change_single_post_after_main_content_position': function(){
			if((window_innerWidth < 1120) && !this.afterSingleContent.data('mobile-mode')){
				this.primary.append(this.afterSingleContent);
				this.afterSingleContent.data('mobile-mode', true);
			}
			else if((window_innerWidth >= 1120) && this.afterSingleContent.data('mobile-mode')){
				this.mainContent.append(this.afterSingleContent); 
				this.afterSingleContent.data('mobile-mode', false);
			}
		},
		'resize': function(){
			this.sidebarMarginTop 	= parseFloat(this.secondary.css('margin-top'));
			this.primaryHeight 		= this.primary.outerHeight(true, true); 
			this.sidebarHeight 		= this.secondary.hasClass('sidebar-sticky') ? this.container.outerHeight(true, true) 
				: (this.secondary.outerHeight(true, true) - this.sidebarMarginTop);
			this.delta 				= this.primaryHeight - this.sidebarHeight;
			this.is_sticky 			= this.test_sticky();
		},
		'test_sticky': function(){
			return (this.primary.outerWidth(true, true) < this.primary.parent().width()) // Main sidebar is not below main content
				&& ((this.delta - this.sidebarMarginTop) > 0); // Sidebar is shorter than main content
		},
		'fix_sidebar': function(){ 
			if(this.is_sticky && ('fixed' != this.container.css('position'))){
				if('static' == this.container.css('position')){
					this.recalculate($(window).scrollTop(), fallsky.pages.all.get_sticky_sidebar_offset(), false);
				}
				else{
					var self = this, run_animation = false, top = '', fixed_top = '', fix_top = '', fix_bottom = '',
						scrollTop = $(window).scrollTop(), primary_offset_top = self.primary.offset().top, 
						sidebar_height = parseFloat(self.sidebarHeight), primary_height = parseFloat(self.primaryHeight);

					if(self.sidebarHeight > window_innerHeight){ 
						if(!this.downThresholdSet){ 
							fixed_top 	= primary_offset_top - window_innerHeight;
							fix_top 	= fixed_top + sidebar_height;
							fix_bottom 	= fixed_top + primary_height;

							if((scrollTop >= fix_top) && (scrollTop <= fix_bottom)){
								top = scrollTop - self.sidebarHeight + window_innerHeight - primary_offset_top;
								run_animation = true;
							}
							else if(scrollTop > fix_bottom){
								top = self.delta;
								run_animation = true;
							}
						}
					}
					else{
						var delta = parseFloat(self.delta), sidebar_margin_top = parseFloat(self.sidebarMarginTop);
						fixed_top 	= primary_offset_top - fallsky.pages.all.get_sticky_sidebar_offset(),
						fix_bottom 	= fixed_top + delta,
						fix_top 	= fixed_top + sidebar_margin_top;

						if(scrollTop > fix_bottom){
							top = delta;
							run_animation = true;
						}
						else if((scrollTop >= fix_top) && (scrollTop <= fix_bottom)){	
							top = scrollTop - primary_offset_top + sidebar_margin_top;
							run_animation = true;
						}
					}
					if(run_animation){
						self.container.animate({'top': top}, 170, function(){ 
							self.recalculate(scrollTop, fallsky.pages.all.get_sticky_sidebar_offset(), false);
						});
					}
				}
			}
			else if( !this.is_sticky ){
				this.secondary.removeClass( 'sidebar-sticky' );
				this.container.css( { 'position': '', 'top': '' } );
			}
		},
		'recalculate': function(top, offset, goUp){
			var $primary = this.primary, $secondary = this.secondary, $container = this.container, clear = true;
			if(this.is_sticky){
				var sidebar_margin_top = parseFloat(this.sidebarMarginTop), sidebar_height = parseFloat(this.sidebarHeight),
					primary_height = parseFloat(this.primaryHeight), primary_offset_top = $primary.offset().top,
					container_offset_top = $container.offset().top, delta = parseFloat(this.delta);

				$secondary.addClass('sidebar-sticky');
				if(goUp || (sidebar_height <= window_innerHeight)){
					var fixed_top 	= primary_offset_top - offset,
						fix_bottom 	= fixed_top + delta,
						fix_top 	= fixed_top + sidebar_margin_top;

					this.downThresholdSet = false;
					if('down' == this.previousAction){
						if('fixed' == $container.css('position')){
							$container.css({'position': '', 'top': (top - primary_offset_top - sidebar_height + window_innerHeight)});
						}
						this.thresholdMin 		= $container.offset().top - offset;
						this.upThreshholdSet 	= true;
						clear 					= false;
					}
					else if(this.upThreshholdSet && (top > this.thresholdMin)){
						clear = false;
					}
					else{
						this.upThreshholdSet = false;
						if(top > fix_bottom){
							$container.css({'position': '', 'top': (delta + 'px')});
							clear = false;
						}
						else if((top >= fix_top) && (top <= fix_bottom)){	
							$container.css({'position': 'fixed', 'top': (offset + 'px')});
						 	clear = false;
						}
					}
					this.previousAction = 'up';
				}
				else{
					var fixed_top 	= primary_offset_top - window_innerHeight,
						fix_top 	= fixed_top + sidebar_height,
						fix_bottom 	= fixed_top + primary_height;

					this.upThreshholdSet = false;
					if('up' == this.previousAction){
						if('fixed' == $container.css('position')){
							$container.css({'position': '', 'top': (container_offset_top - primary_offset_top)});
						}
						this.thresholdMax 		= parseFloat(container_offset_top) + parseFloat(sidebar_height) - window_innerHeight;
						this.downThresholdSet 	= true;
						clear 					= false;
					}
					else if(this.downThresholdSet && (top < this.thresholdMax)){
						clear = false;
					}
					else{
						this.downThresholdSet = false;
						if((top >= fix_top) && (top <= fix_bottom)){
							$container.css({'position': 'fixed', 'top': (window_innerHeight - sidebar_height) + 'px'});
							clear = false;
						}
						else if(top > fix_bottom){
							$container.css({'position': '', 'top': (delta + 'px')});
							clear = false;
						}
					}
					this.previousAction = 'down';
				}
			}
			else{
				$secondary.removeClass('sidebar-sticky');
				this.previousAction = '';
			}
			if(clear){
				$container.css({'position': '', 'top': ''});
			}
		}
	};

	/**
	* Single post only features
	*	1. Post nav sticky
	* 	2. Media playing control
	*/
	fallsky.pages.single_post = {
		'init': function(){
			this.process_media();
			if($('body.single.single-post').length){
				var self = this, $post_nav = $('.post-nav'), 
					$side_share = $('.side-share-icons'), $body = $('body');
				if($('<div>').add($post_nav).add($side_share).length > 1){
					$(window).on('scroll fallsky.window.resize', function(){
						self.recalculate($(this).scrollTop());
					});
				}
			}
		},
		'recalculate': function(top){
			var single = this, bottom = top + window_innerHeight * 0.8, $content = $('.post-entry'), $article = $('#primary > .post'),
				content_top = Math.max($content.offset().top, 40), $post_nav = $('.post-nav'), $main_content = $('#primary'),
				$sides = $('<p>').add($('.side-share-icons')).not('p');

			if($sides.length){
				(bottom > content_top) && (bottom < ($main_content.offset().top + $main_content.outerHeight(true, true) - window_innerHeight / 5 - parseInt($main_content.css('padding-bottom'))))
					? $sides.addClass('show') : $sides.removeClass('show');
			}

			if($post_nav.length){
				if(window_innerWidth > 600){ 
					if(window_innerHeight > content_top){
						bottom = top + content_top * 0.8;
					}
					if(!$('body').hasClass('post-header-with-bg')){
						bottom = top;
					}
					bottom > content_top ? $post_nav.addClass('show') : $post_nav.removeClass('show');
				}
				else{
					$post_nav.removeClass('show');
				}
			}
		},
		'process_media': function(){
			var $media = $('#fallsky-single-post-media');
			if($media.length){
				var media =	$media.html(),
					regex = /<audio .*>.*<\/audio>/;
				if($('.play-audio-btn').length && regex.exec(media)){
					this.embed_audio($(media));
				}
				else if($('.play-video-btn').length){
					this.embed_video(media);
				}
			}
		},
		'embed_audio': function($media){
			var $wrap = $('<div>', {'class': 'fallsky-media-wrapper hide'}).append($media);
			$('body').append($wrap);
			$media = $wrap.find('audio');
			$('.play-audio-btn').on('click', function(){
				if($media && $media.length){
					var $btn = $(this), media = $media.get(0);
					$btn.hasClass('playing') ? 
						($btn.removeClass('playing'), media.pause()) : ($btn.addClass('playing'), media.play());
				}
			});
		},
		'embed_video': function(media){
			media = new Fallsky_Single_post_Background_Video(media); 
			if(media){
				media.deferred.done(function(){
					$('.play-video-btn').on('click', function(){
						var $btn = $(this);
						if(!$btn.hasClass('playing')){
							$btn.addClass('playing'); 
							media.play();
						}
					});
				});
			}
		}
	};
	/**
	* Little help jQuery plugin to test the object has any of the classes provided
	* @param array class list
	* @return boolean return true if have any classname provided, otherwise false
	*/
	$.fn.hasAnyClass = function(classname){
		if(Array.isArray(classname) && classname.length){
			var $target = $(this).first(), pass = false;
			$.each(classname, function(i, v){
				if($target.hasClass(v)){
					pass = true;
					return false;
				}
			});
			return pass;
		}
		return true;
	}
	/**
	* Little help jQuery plugin to test the object has all the classes provided
	* @param array class list
	* @return boolean return true if have all classname provided, otherwise false
	*/
	$.fn.hasAllClass = function(classname){
		if(Array.isArray(classname) && classname.length){
			var $target = $(this).first(), pass = true;
			$.each(classname, function(i, v){
				if(!$target.hasClass(v)){
					pass = false;
					return false;
				}
			});
			return pass;
		}
		return true;
	}
	/**
	* Litter help jQuery plugin for ajax request result 
	*	1. Make sure the media is fully loaded for some post list layout
	*	2. For v1.0 wait for images only
	*/
	$.fn.fallsky_wait_for_media = function(){
		var $images 	= $(this).find('img'),
			$gallery 	= $(this).find('.gallery-item.first'),
			deferred 	= $.Deferred(),
			length 		= 0,
			done 		= 0,
			match_url 	= /url\(\s*(['"]?)(.*?)\1\s*\)/g, 
			match 		= null;
		if(!$images.length && !$gallery.length){
			throw new TypeError(fallsky.error_text.no_media_found);
		}
		else{
			var src = [];
			$.each($images, function(){
			 	$(this).attr('src') ? src.push($(this).attr('src')) : ''; 
			});
			$.each($gallery, function(){
				while(match = match_url.exec($(this).children(':first').css('background-image'))){
					src.push(match[2]);
				}
			});
			if(!src.length){
				throw new TypeError(fallsky.error_text.no_media_found);
			}
			else{
				length = src.length;
				$.each(src, function(index, url){
					$(new Image()).on('load', function(){
						done ++;
						if(done == length){
							deferred.resolve();
						}
					}).attr('src', url);
				});

				return deferred.promise();
			}
		}
	}
	/**
	* Ajax based pagination for post list 
	* @return deferred object for done/fail/always calls
	*/
	$.fn.fallsky_ajax_pagination = function(options){
		options = $.extend({'type': 'archive', 'page': false, 'list': false, 'append': true, 'data': false}, options);
		var $target 	= $(this).first(),
			deferred 	= $.Deferred(),
			$list 		= options['list'],
			$masonry 	= $list.find('.masonry-column'),
			custom 		= {'page': options['page'], 'type': options['type'], 'data': options['data']},
			ajax_data 	= $.extend({}, fallsky.ajax_pagination, custom); // Generate the ajax data object

		// Send ajax request
		$.post(ajax_data.url, ajax_data)
			.done(function(response){
				var $html 		= $(response.data), 
					$posts		= $html.find('.list-post'), 
					$mcolumns 	= $html.find('.masonry-column'),
					$gallery 	= $html.find('.image-gallery');
				if($posts.length){
					$posts.addClass('post new-added').css('opacity', 0);
					var $media = $posts.find('img').length || $gallery.length, is_masonry = false, 
						for_media_done = $media ? $posts.fallsky_wait_for_media() : false;
					if($masonry.length){
						var pids = $list.data('post-list') ? JSON.parse($list.data('post-list')) : [];
						$posts.each(function(){
							pids.unshift($(this).data('post-id'));
						});
						$list.data('post-list',JSON.stringify(pids));
						// If in mobile mode, just append the posts
						if($list.data('mobile-mode')){
							$masonry.first().append($posts);
							$posts.removeClass('new-added').fadeTo(300, 1);
						}
						// Recalculate the height
						else{
							if($media){
								for_media_done.done(function(){
									$masonry.first().append($posts);
									$list.fallsky_masonry({'post': '.post.new-added', 'append': options['append']});
									$posts.removeClass('new-added').fadeTo(300, 1);
								});
							}
							else{
								$masonry.first().append($posts);
								$list.fallsky_masonry({'post': '.post.new-added'});
								$posts.removeClass('new-added').fadeTo(300, 1);
							}
						}
						is_masonry = true;
					}
					else{
						options['append'] ? $list.children().first().append($posts)
							: $list.children().first().html('').append($posts);
						$posts.removeClass('new-added').fadeTo(300, 1);
					}	
					if($.fn.loftocean_image_preloader){
						$posts.loftocean_image_preloader();
					}
					if($gallery.length){
						for_media_done.done(function(){ $gallery.fallsky_slick_gallery(); });
					}  
					if($list.hasClass('layout-zigzag')){
						fallsky.pages.all.add_parallax_items($posts);
					}
					// Wait for all image loaded, then sending the defer resolve
					if(for_media_done){
						for_media_done.done(function(){ deferred.resolveWith(response); });
					}
					else{ 
						deferred.resolveWith(response); 
					}
				}
				else{
					deferred.resolveWith(response);
				}
			})
			.fail(function(response){ 
				deferred.rejectWith(response); 
			});
		return deferred.promise();
	}
	/**
	* Enable ajax pagination
	*/
	$.fn.fallsky_pagination = function(){
		return $(this).each(function(){
			var $ajax_nav = $(this);
			if(fallsky.ajax_pagination && $ajax_nav.length){
				var is_infinite = $ajax_nav.hasClass('style-infinite');
				$ajax_nav.on('ajax.start', function(){
					is_infinite ? $(this).css('opacity', 1) : $(this).find('.load-more').addClass('loading');
				})
				.on('ajax.done', function(e, response){
					var $self = $(this), $html = $('<div>').html(response.data);
					if($html.find('.navigation.pagination').length){
						$self.data('page', ($self.data('page') + 1));
						$(document).trigger('fallsky.sidebar.resize');
					}
					else{
						if(is_infinite){
							$ajax_nav = false;
						}

						var $btn = $self.find('.load-more-btn');
						$btn.before($('<span>', {'class': 'load-more-btn', 'text': $btn.data('no-post-text')})).remove();
						setTimeout(function(){ $self.remove(); $(document).trigger('fallsky.sidebar.resize'); }, 1000);		
					}
				})
				.on('ajax.always', function(){
					$(this).data('fallsky-nav-processing', false);
					is_infinite ? $(this).css('opacity', 0) : $(this).find('.load-more').removeClass('loading');
				})
				.on('ajax.load', function(e){
					e.preventDefault();
					var $self = $(this), $posts = $self.siblings('.posts'), 
						args = {'page': $self.data('page'), 'type': $self.data('type'), 'list': $posts, 'data': $self.data('attrs') || {}};

					// Do nothing if the .posts not exists or ajax request is sending
					if(!$posts.length || $self.data('ajax-sending')){
						return false;
					}
					// Set arguments for widget
					switch(args['type']){
						case 'widget':
							var $widget 	= $self.closest('.home-widget').first(),
								widgetID 	= $widget.attr('id'),
								match 		= /(.*)-(\d*)/.exec(widgetID); 
							if(match && (match[1] in fallsky.widgets)){
								args['data']['widget'] 		= fallsky.widgets[match[1]];
								args['data']['widgetID'] 	= match[2];
							}
							// Throw a error if no widget found
							else{
								throw new TypeError(fallsky.error_text.no_widget_found);
							}
							break;
						case 'category':
							args['data']['filter'] 		= $self.data('filter');
							args['data']['category']	= $self.data('category');
							break;
						case 'date':
							args['data']['year']	= $self.data('year');
							args['data']['month']	= $self.data('month');
							args['data']['day']		= $self.data('day');
							break;
						case 'post_format':
							args['data']['format']	= $self.data('format');
							break;
						case 'blog':
							args['data']['latest'] = true;
						case 'tag':
						case 'author':
						case 'search':
							var type = args['type'];
							args['data'][type]	= $self.data(type);
							break;
					}

					// Set the flag to identify currently in ajax processing
					$self.data('ajax-sending', true);
					$self.trigger('ajax.start');
					$self.fallsky_ajax_pagination(args)
						.done(function(){ $self.trigger('ajax.done', this); })
						.always(function(){ 
							if($self.length && !$self.data('done')){
								$self.data('ajax-sending', false).trigger('ajax.always'); 
								setTimeout(function(){ $self.data('pause', false); }, 300);
							}
						});
				})
				.on('click', '.load-more-btn', function(e){ 
					e.preventDefault();
					// Trigger ajax request if is load more style
					if(!is_infinite && !$(this).closest('.navigation.pagination.ajax').data('ajax-sending')){ 
						$(this).closest('.navigation.pagination.ajax').trigger('ajax.load');
					}
				})
				.on('ajax.remove', function(e){
					$ajax_nav.off('ajax.start').off('ajax.done').off('ajax.always').off('ajax.load');
					if(is_infinite){
						$ajax_nav = false;
					}
				});

				if(is_infinite){ // Add envent handlers for infinite style
					$(window).on('scroll', function(e){
						if($ajax_nav && $ajax_nav.length){
							var $self = $(this), top = parseInt($self.scrollTop() + (window_innerHeight * 0.85));
							$ajax_nav.each(function(){
								if((top > parseInt($(this).offset().top)) && !$(this).data('pause')){
									$(this).data('pause', true).trigger('ajax.start'); 
									return false;
								}
							});
							if($self.data('fallsky-pagination-infinite-timer')){
								clearTimeout($self.data('fallsky-pagination-infinite-timer'));
								$self.data('fallsky-pagination-infinite-timer', false);
							}
							$self.data('fallsky-pagination-infinite-timer', setTimeout(function(){
								$ajax_nav.each(function(){
									if((top > parseInt($(this).offset().top)) && !$(this).data('ajax-sending')){
										$(this).trigger('ajax.load'); 
										return false;
									}
								});
							}, 100));
						}
					});
				}
			}
		});
	};

	/**
	* Enable masonry for post list with masonry
	* 	Actually it's alreay splite into columns, just reorder it to fit the height
	*/
	$.fn.fallsky_masonry = function(args){
		var options = $.extend({}, {'post': '.post', 'append': false, 'trigger-sidebar-resize': false}, args || {}); 
		$(this).each(function(){ 
			var $masonry = $(this), selector = options.post;
			if($masonry.hasClass('layout-masonry') && $masonry.find(selector).length){
				var columns 	= [],
					length		= $masonry.hasClass('column-3') ? 3 : 2,
					current 	= $masonry.data('current') || 0,
					$columns 	= $masonry.find('.masonry-column');
				for(var i = 0; i < length; i++){ 
					columns.push($columns.eq(i).data('column-height') || 0); 
				} 

				$masonry.find(selector).each(function(index, item){
					var $item = $(item), lowest = 0; 
					columns[current] += parseInt($item.outerHeight(true, true));
					$item.addClass('masonry-column-' + current); 

					lowest = columns[current]; 
					for(var i = (length - 1); i >= 0; i--){ 
						if(columns[i] <= lowest){ 
							lowest 	= columns[i];
							current = i; 
						}
					}
				});
				$columns.each(function(ci, co){
					var column_class = 'masonry-column-' + ci;
					$(this).append($masonry.find('.post.' + column_class).removeClass(column_class).detach());

					$(this).data('column-height', columns[ci]);

				});
				$masonry.data('current', current);
			}
		});
		if(options['trigger-sidebar-resize']){ 
	 		jQuery(document).trigger('fallsky.sidebar.resize');
	 	}
		return this;
	}

	/**
	* Enable slick slider to gallery list
	*/
	$.fn.fallsky_slick_gallery = function(){
		var gallery_slider_args = {
				dots: false,
				infinite: true,
				speed: 500,
				fade: true,
				cssEase: 'linear',
				autoplay: true,
				autoplaySpeed: 5000,
				appendArrows: false
			};
		return $(this).each(function(){
			var $gallery 	= $(this), 
				$posts 		= $gallery.closest('.posts'),
				arrows 		= false,
				custom_args = {};
			if($posts.length){
				if($posts.hasAllClass(['layout-card', 'column-1']) || $posts.hasClass('layout-standard')){
					arrows = $('<div>', {'class': 'slider-arrows'});
					$gallery.parent().after(arrows);
				}
			}
			else if($('body.single.single-post').length && $gallery.closest('.featured-media-section').length){
				arrows = $('<div>', {'class': 'slider-arrows'});
				$gallery.after(arrows);
			}
			else if($gallery.closest('.post-content-gallery').length){
				arrows = $('<div>', {'class': 'slider-arrows'});
				$gallery.after(arrows);
			}
			else if($gallery.closest('.popup-slider').length){
				arrows = $('<div>', {'class': 'slider-arrows'});
				custom_args = {autoplay: false};
				$gallery.after(arrows);
			}
			$gallery.on('init', function(e){ 
				$(this).find('.gallery-item').css('display', ''); 
			})
			.slick($.extend({}, gallery_slider_args, {'appendArrows': arrows}, custom_args));
		});
	}
	
	function fallsky_extend(obj, src){
		var newobj = {};
		$.extend(newobj, obj);
		for(var key in src){
			if(src.hasOwnProperty(key)){
				newobj[key] = src[key];
			}
		}
		return newobj;
	}
	function fallsky_init_mega_menu(){
		var $page 				= $('#page'),
			page_offset_left 	= $('#page').offset().left;
		$('#masthead #menu-main-menu .mega-menu').each(function(){
			if($('body').hasClass('rtl')){
				$(this).children('ul').css('right', -($page.width() - $(this).offset().left - $(this).outerWidth(true, true) + page_offset_left));
			}
			else{
				$(this).children('ul').css('left', -($(this).offset().left - page_offset_left));
			}
		});
		$('#masthead .sub-menu').css('display', '');
	}
	window.fallsky_slick_slider = function($slider, settings){
		var is_slider_1 = $slider.hasClass('style-slider-1'), change_slider_1_dots = '',
		change_slider_1_dots = function(){
			var $dots = $slider.find('.slick-dots');
			if($dots.length){
				window_innerWidth > 1024 ? $dots.css('display', 'block') : $dots.css('display', 'none');
			}
		}
		if(is_slider_1){
			$(window).on('fallsky.window.resize', function(){ change_slider_1_dots(); });
		}

		if($slider.length && settings){
			var currentSliderClass = 'current-post';
			if(settings.appendArrows){
				settings.appendArrows = $slider.find(settings.appendArrows);
			}
			$slider.find('.slider-wrapper').on('init', function(e, slick){
				var current = slick.slickCurrentSlide();
				$(this).find('.post').css('display', '').filter('[data-slick-index=' + current + ']').addClass(currentSliderClass); 
				if(is_slider_1){
					change_slider_1_dots();
				}
			})
			.on('afterChange', function(e, slick, currentSlide){
				var count = $(this).find('.post').length, prevSlide = (currentSlide - 1 + count) % count;
				$(this).find('[data-slick-index=' + currentSlide + ']').first().addClass(currentSliderClass);
				$(this).find('[data-slick-index=' + prevSlide + ']').first().removeClass(currentSliderClass);
			}).slick(settings);
		}
	}
	
	/**
	* Calculate the site top ad height, and assign the height as margin-top to #page
	*/
	function fallsky_site_top_ad(){ 
		if($page.length && $site_top_ad.length){
			if(window_innerWidth > 600){
				var ad_height = $site_top_ad.removeClass('hide').height();
				$(document).trigger('fallsky.site_top_ad.init');

				$page.css({'transition-duration': '0.3s', 'margin-top': ad_height});
				setTimeout(function(){ 
					$page.css('transition-duration', ''); 
					$(document).trigger('fallsky.site_top_ad.done'); 
				}, 300);
			}
			else{
				$site_top_ad.removeClass('hide').css({'opacity': ''});
				$page.css({'margin-top': '', 'transition-duration': ''});
				$(document).trigger('fallsky.site_top_ad.done'); 
			}
		}
	}

	/**
	* Fix homepage widget banners if needed
	*	1. Custom height
	*	2. Background image
	*/
	function fallsky_fix_homepage_banner_height(){
		if(window_innerWidth > 1024){
			$homepage_banner.each(function(){
				$(this).data('custom-height') ? $(this).css('height', parseInt($(this).data('custom-height'))) 	: '';
				$(this).data('bg-image') ? 		$(this).css('background-image', $(this).data('bg-image')) 		: '';
			});
		}
		else{
			$homepage_banner.css({'height': '', 'background-image': ''});
		}
	}

	// Fix homepage widget banner custom height and background image
	if($homepage_banner.length){
		$(window).on('fallsky.window.resize', function(e){
			fallsky_fix_homepage_banner_height();
		});
		fallsky_fix_homepage_banner_height();
	}

	// Process masonry posts layout
	fallsky.pages.all.process_masonry();
	/** Call the sitewide helper functions */
	fallsky.helper.init();

	/** Let render the page */
	$(document).ready(function(){  
		var	$featured_slider = $('.featured-section .top-slider');
		//Chceck mega menu
		fallsky_init_mega_menu();
		fallsky.sidebar.init();
		fallsky.pages.single_post.init();
		fallsky.pages.all.init();
		$featured_slider.length && fallsky.featured_slider ? fallsky_slick_slider($featured_slider, fallsky.featured_slider) : '';
		// Fit videos if any
		if($('body.page #primary iframe, body.single #primary iframe').length){
			$('body.page #primary, body.single #primary').fitVids();
		}

		$(window).resize(function(){
			var top = $(this).scrollTop(), $header 	= $('#masthead'), $content 	= $('#content');

			window_innerWidth = $(window).innerWidth();
			window_innerHeight = $(window).innerHeight();

			fallsky_init_mega_menu();

			if($site_top_ad.length){
				if($site_top_ad.data('timer')){
					clearTimeout($site_top_ad.data('timer'));
					$site_top_ad.data('timer', false);
				}
				$site_top_ad.data('timer', setTimeout(function(){ fallsky_site_top_ad(); }, 200));
			}
		})
		.load(function(){
			if($site_top_ad.length){
				fallsky_site_top_ad();
			}
			if(window.location.hash && ('#comment-section' == window.location.hash) && $('.article-comment > a').length){
				$site_top_ad.length
					? $(document).on('fallsky.site_top_ad.done', function(){ 
						$('.article-comment > a').first().trigger('click'); 
					}) : $('.article-comment > a').first().trigger('click');
			}
		});

		$('body').on('click', '#masthead #menu-toggle', function(e){
			e.preventDefault();
			$('.fallsky-fullmenu').addClass('show');
			$('body').css('overflow', 'hidden');
		})
		.on('click', '.fallsky-fullmenu .close-button', function(e){
			e.preventDefault();
			$('.fallsky-fullmenu').removeClass('show');
			$('body').css('overflow', '');
		})
		.on('click', '#site-header-search, .post-nav .search-button', function(e){
			e.preventDefault();
			$('.search-screen').addClass('show')
			setTimeout(function(){ $('.search-screen form .search-field').focus(); }, 450);
			$('body').css('overflow', 'hidden');
		})
		.on('click', '.search-screen .close-button', function(e){
			e.preventDefault();
			$('.search-screen').removeClass('show');
			$('body').css('overflow', '');
		})
		.on('focus', '.search-screen form .search-field', function(e){
			$('.search-screen .hint').addClass('show');
		})
		.on('blur', '.search-screen form .search-field', function(e){
			$('.search-screen .hint').removeClass('show');
		})
		.on('change', '.search-screen form .search-field', function(e){
			e.preventDefault();
			if(!$(this).is(':focus')){ 
				return false;
			}

			var $field = $(this);
			if($(this).data('timer')){
				clearTimeout($(this).data('timer'));
				$(this).data('timer', false);
			}
			$(this).data('timer', setTimeout(function(){ 
				var value 		= $.trim($field.val()),
					$result 	= $('.search-screen .search-results'),
					$button 	= $field.parent().siblings('.search-submit'),
					$shortcuts 	= $result.siblings('.shortcuts-cat');

				$field.data('timer', false);
				if(!value){
					$shortcuts.css('display', '');
					$result.addClass('hide').html('');
				}
				else{
					var data 	= fallsky.ajax_search;
					data['s'] 	= value;
					$button.addClass('searching');
					$.post(data['url'], data)
						.done(function(response){
							if(response.success && response.data){
								var $data = $('<div>').append(response['data']), 
									$list = $data.find('.results-list'), 
									has_list = $list.length;
								$shortcuts.css('display', 'none');
								has_list ? $list.css('display', 'none') : '';
								$result.removeClass('hide').html('').append($data.children());
								has_list ? $list.fadeTo(700, 1) : '';
							}
						})
						.always(function(){
							$button.removeClass('searching');
						});
				}
			}, 450));
		})
		.on('keyup', '.search-screen input[name=s]', function(e){
			if($('.search-screen').hasClass('simple')){
				return '';
			}

			$(this).trigger('change');
		})
		.on('hover', '#masthead #menu-main-menu .mega-menu .sub-cat-list li', function(e){
			if(!$(this).hasClass('current')){
				var $posts = $(this).parents('.sub-cat-list').first().siblings('.sub-cat-posts').first();
				$(this).siblings('.current').removeClass('current').end().addClass('current');
				$posts.children('.current').removeClass('current');
				$posts.children('.' + $(this).attr('data-id')).addClass('current');
			}
		})
		.on('click', '.fallsky-fullmenu.show .main-navigation .dropdown-toggle', function(e){
			e.preventDefault();
			if($(this).hasClass('toggled-on')){
				$(this).parent().find('.toggled-on').removeClass('toggled-on');
			}
			else{
				$(this).parent().siblings('li').find('.toggled-on').removeClass('toggled-on');
				$(this).addClass('toggled-on');
			}
		})
		.on('mouseenter', '.featured-section.style-blocks .blocks-wrapper .post', function(e){
			var pid 	= $(this).data('post-id'),
				$target = $('.featured-section.style-blocks .blocks-3-bg #featured-post-id-' + pid);
			if($target.length && !$target.hasClass('active')){
				$target.addClass('active').siblings().removeClass('active');
			}
		})
		.on('click', '.home-widget.call-to-action .cta-img video', function(e){
			var video = $(this).get(0);
			if(video.paused){
				video.play();
				$(this).addClass('playing');
			}
			else{
				video.pause();
				$(this).removeClass('playing');
			}
		})
		.on('click', '.article-comment > a', function(e){
			e.preventDefault();
			var $comment = $('#comments');
			if($comment.length){
				var offset_top = $comment.offset().top - fallsky.pages.all.get_sticky_sidebar_offset(); 
				$('html, body').animate({'scrollTop': offset_top}, 1);
			}
		})
		.on('click', '#comments .click-to-reply', function(e){
			e.preventDefault();
			$(this).addClass('clicked');
		})
		.on('click', '#comments .comment-reply-link', function(e){
			$('#comments .click-to-reply').addClass('hide');
			$('#comments #respond').css('display', 'block');
		})
		.on('click', '#comments #cancel-comment-reply-link', function(e){
			$('#comments #respond').css('display', '');
			$('#comments .click-to-reply').removeClass('hide');
		});

		$('body.category').on('click', '.cat-filter a', function(e){
			e.preventDefault();
			var $target = $(this);
			if(!$target.hasClass('active')){
				$.get($target.attr('href')).done(function(response){
					var $html = $(response), $ajax_nav = $('#primary .navigation.pagination.ajax');
					if($html && $html.length && $html.find('#primary').length){
						// Remove current ajax pagination
						if($ajax_nav.length){
							$ajax_nav.trigger('ajax.remove');
						}

						$('#primary').html($html.find('#primary').html());
						if($('#primary .posts .post').length){ 
							var $layout 	= $('#primary .posts'),
								$posts 		= $layout.find('.post').css('opacity', 0),
								$gallery 	= $layout.find('.image-gallery'),
								has_media 	= $layout.find('img').length || $gallery.length,
								deferred 	= has_media ? $layout.fallsky_wait_for_media() : false,
								pids 		= [];
							if($layout.hasClass('layout-masonry')){
								$posts.each(function(){
									pids.unshift($(this).data('post-id'));
								});
								$layout.data('post-list', JSON.stringify(pids));
								if(has_media){
									deferred.done(function(){
										fallsky.pages.all.process_masonry(true);
										$posts.fadeTo(400, 1);
									});
								}
								else{
									fallsky.pages.all.process_masonry(true);
									$posts.fadeTo(400, 1);
								}
							}
							else{
								$posts.fadeTo(400, 1);
							}
							if($.fn.loftocean_image_preloader){
								$posts.loftocean_image_preloader();
							}
							if($gallery.length){
								$gallery.fallsky_slick_gallery();
							}  
							if($layout.hasClass('layout-zigzag') && fallsky.parallax_effect && (-1 !== fallsky.parallax_effect.indexOf('post-list-zigzag'))){
								fallsky.pages.all.start_parallax();
							}
							if(has_media){
								deferred.done(function(){ $(document).trigger('fallsky.sidebar.resize'); });
							}
							else{
								$(document).trigger('fallsky.sidebar.resize');
							}

							$ajax_nav = $('#primary .navigation.pagination.ajax')
							if(fallsky.ajax_pagination && $ajax_nav.length){ 
								$ajax_nav.fallsky_pagination();
							}
						}
					}
				});
			}
			return false;
		});

		var $product_carousels = $('.home-widget.woocommerce.products .products.layout-carousel');
		if($product_carousels.length){
			$product_carousels.each(function(){
				var $carousel = $(this), cols = $carousel.data('slides-to-show'),
					size1024 = Math.min(cols, 5), size900 = Math.min(cols, 4), size768 = Math.min(cols, 3); 
				$carousel.slick({
					dots: 			true,
					infinite: 		true,
					slidesToShow: 	cols,
					slidesToScroll: cols,
					appendArrows: 	false,
					responsive: 	[{
						breakpoint: 1025,
						settings: {
							slidesToShow: 	size1024,
							slidesToScroll: size1024
						}
					}, {
						breakpoint: 901,
						settings: {
							slidesToShow: 	size900,
							slidesToScroll: size900
						}
					}, {
						breakpoint: 768,
						settings: {
							slidesToShow:	size768,
							slidesToScroll: size768
						}
					}, {
						breakpoint: 600,
						settings: {
						  	centerMode: 	true,
							slidesToShow:	2,
							slidesToScroll: 2
						}
					}, {
						breakpoint: 480,
						settings: {
						  	centerMode: 	true,
							slidesToShow: 	1,
							slidesToScroll: 1
						}
					}]
				});
			});
		}
		var $gallery_slider = $('.posts .format-gallery .image-gallery')
			.add($('.post-header .image-gallery'))
			.add($('.featured-media-section .image-gallery'))
			.add($('.post-content-gallery.gallery-slider .image-gallery'))
			.add($('.loftocean-popup-sliders .popup-slider.gallery-slider .image-gallery'));
		if($gallery_slider.length){
			$gallery_slider.fallsky_slick_gallery();
		}

		var $gallery_justified = $('.post-content-gallery.gallery-justified');
		if($gallery_justified.length){
			$gallery_justified.each(function(){
				$(this).children('.image-gallery').justifiedGallery({
					'rowHeight': 	$(this).data('row-height'), 
					'lastRow': 		$(this).data('last-row'), 
					'margins': 		$(this).data('margin'),
					'captions': 	false
				}).on('jg.complete', function(e){ $(this).parent().addClass('justified-gallery-initialized'); });
			});
		}

		var $ajax_nav = $('.navigation.pagination.ajax');
		if(fallsky.ajax_pagination && $ajax_nav.length){
			$ajax_nav.fallsky_pagination();
		}
	});
})(jQuery);