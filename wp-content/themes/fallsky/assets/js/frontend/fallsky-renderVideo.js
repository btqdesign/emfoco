/* global YT */
(function(window, settings, $){
	"use strict";
	var NativeHandler, YouTubeHandler;
	/** @namespace wp */
	window.wp = window.wp || {};
	// Fail gracefully in unsupported browsers.
	if(!('addEventListener' in window)){
		return;
	}

	/**
	 * Trigger an event.
	 * @param {Element} target HTML element to dispatch the event on.
	 * @param {string} name Event name.
	 */
	function trigger(target, name){
		var evt;

		if('function' === typeof window.Event){
			evt = new Event(name);
		} 
		else{
			evt = document.createEvent('Event');
			evt.initEvent(name, true, true);
		}
		target.dispatchEvent(evt);
	}

	/**
	 * @class
	 */
	function CustomBgVideo() {
		this.handlers = {
			nativeVideo: new NativeHandler(),
			youtube: new YouTubeHandler()
		};
	}

	CustomBgVideo.prototype = {
		/**
		 * If the environment supports video, loops through registered handlers
		 * until one is found that can handle the video.
		 */
		initialize: function() {
			if(this.supportsVideo()){
				for(var id in this.handlers){
					var handler = this.handlers[ id ];

					if('test' in handler && handler.test(settings)){
						this.activeHandler = handler.initialize.call(handler, settings);

						// Dispatch custom event when the video is loaded.
						trigger(document, 'fallsky_home_custom_content_bg_video_loaded');
						break;
					}
				}
			}
		},

		/**
		 * Determines if the current environment supports video.
		 * Themes and plugins can override this method to change the criteria.
		 * @return {boolean}
		 */
		supportsVideo: function() {
			// Don't load video on small screens. @todo: consider bandwidth and other factors.
			if(!settings && (window.innerWidth < settings.minWidth) || (window.innerHeight < settings.minHeight)){
				return false;
			}
			return true;
		},

		/**
		 * Base handler for custom handlers to extend.
		 *
		 * @type {BaseHandler}
		 */
		BaseVideoHandler: BaseHandler
	};

	/**
	 * Create a video handler instance.
	 * @class
	 */
	function BaseHandler(){}

	BaseHandler.prototype = {
		/**
		 * Initialize the video handler.
		 *
		 * @param {object} settings Video settings.
		 */
		initialize: function(settings){
			var handler 	= this;
			this.settings 	= settings;
			this.$container = $('.featured-section.style-custom .section-bg').length 
				? $('.featured-section.style-custom .section-bg') 
					: $('<div>', {'class': 'section-bg'}).prependTo($('.featured-section.style-custom > .custom-content'));
			this.container 	= this.$container.get(0);
			this.ready();
		},
		/**
		 * Ready method called after a handler is initialized.
		 *
		 * @abstract
		 */
		ready: function() {},
		/**
		 * Append a video node to the header container.
		 * @param {Element} node HTML element.
		 */
		setVideo: function(node){
			this.$container.css({'overflow': 'hidden', 'pointer-events': 'none'})
				.find('.fallsky-featured-area-custom-content-video').remove()
				.end().append($(node).css({'position': 'absolute'}));
		},
		/**
		 * Whether the handler can process a video.
		 *
		 * @abstract
		 * @param {object} settings Video settings.
		 * @return {boolean}
		 */
		test: function() {
			return false;
		},
		/**
		* Get dimensions for current video
		* @return {'width': xxx, 'height': xxx}
		*/
		getDimensions: function(){
			var cwidth 	= this.$container.width() 	|| 16,
				cheight = this.$container.height() 	|| 9,
				cratio	= cheight / cwidth,
				ratio 	= this.container.ratio || (9 / 16);
			return ratio > cratio ? {'width': cwidth, 'height': cwidth * ratio} : {'height': cheight, 'width': cheight / ratio};
		},
		/**
		* Resize video after window resized or video initialized
		*/
		resizeVideo: function(){
			var video 		= this.video,
				dimension 	= this.getDimensions();
			video.width 			= dimension.width;
			video.height 			= dimension.height;
			video.style.maxWidth 	= 'none';
		}
	};

	/**
	 * Create a custom handler.
	 * @param {object} protoProps Properties to apply to the prototype.
	 * @return CustomHandler The subclass.
	 */
	BaseHandler.extend = function(protoProps){
		var prop;

		function CustomHandler(){
			var result = BaseHandler.apply(this, arguments);
			return result;
		}

		CustomHandler.prototype 			= Object.create(BaseHandler.prototype);
		CustomHandler.prototype.constructor = CustomHandler;

		for(prop in protoProps){
			CustomHandler.prototype[prop] = protoProps[prop];
		}
		return CustomHandler;
	};

	/**
	 * Native video handler.
	 *
	 * @class
	 */
	NativeHandler = BaseHandler.extend(/** @lends wp.NativeHandler.prototype */{
		/**
		 * Whether the native handler supports a video.
		 *
		 * @param {object} settings Video settings.
		 * @return {boolean}
		 */
		test: function(settings){
			var video = document.createElement('video');
			return video.canPlayType(settings.mimeType);
		},
		/**
		 * Set up a native video element.
		 */
		ready: function(){
			var handler = this,
				video 	= document.createElement('video');

			video.id 		= 'fallsky-featured-area-custom-content-video';
			video.autoplay 	= 'autoplay';
			video.loop 		= 'loop';
			video.muted	 	= 'muted';

			this.video 		= video;
			handler.setVideo(video);
			$(video).on('loadedmetadata', function(){
				handler.container.ratio = (this.videoHeight || 9) / (this.videoWidth || 16);
				handler.resizeVideo.call(handler);
			});
			$(window).on('resize', function(){
				if(handler.container.ratio){
					handler.resizeVideo.call(handler);
				}
			});
			video.src = this.settings.videoUrl;
		}
	});

	/**
	 * YouTube video handler.
	 * @class wp.YouTubeHandler
	 */
	YouTubeHandler = BaseHandler.extend(/** @lends wp.YouTubeHandler.prototype */{
		/**
		 * Whether the handler supports a video.
		 *
		 * @param {object} settings Video settings.
		 * @return {boolean}
		 */
		test: function( settings ) {
			return 'video/x-youtube' === settings.mimeType;
		},

		/**
		 * Set up a YouTube iframe.
		 * Loads the YouTube IFrame API if the 'YT' global doesn't exist.
		 */
		ready: function() {
			var handler = this;

			if('YT' in window){
				YT.ready(handler.loadVideo.bind(handler));
			} 
			else{
				var tag 	= document.createElement('script');
				tag.src 	= 'https://www.youtube.com/iframe_api';
				tag.onload 	= function(){
					YT.ready(handler.loadVideo.bind(handler));
				};
				document.getElementsByTagName('head')[0].appendChild(tag);
			}
		},
		/**
		 * Load a YouTube video.
		 */
		loadVideo: function() {
			var handler 		= this,
				video 			= document.createElement('div'),
				// @link http://stackoverflow.com/a/27728417
				VIDEO_ID_REGEX 	= /^.*(?:(?:youtu\.be\/|v\/|vi\/|u\/\w\/|embed\/)|(?:(?:watch)?\?v(?:i)?=|\&v(?:i)?=))([^#\&\?]*).*/,
				vid 			= this.settings.videoUrl.match(VIDEO_ID_REGEX)[1];

			$.getJSON('https://noembed.com/embed', {format: 'json', url: ('https://www.youtube.com/watch?v=' + vid)}, function(data){
				var dimensions;
				handler.container.ratio = (data.height || 9) / (data.width || 16);
				dimensions = handler.getDimensions.call(handler);

				video.id = 'fallsky-featured-area-custom-content-video';
				handler.setVideo(video);

				handler.player = new YT.Player(video, {
					videoId: 	vid,
					width: 		dimensions.width,
					height: 	dimensions.height,
					events: 	{
						onReady: function(e){
							e.target.mute();
							handler.video = handler.$container.find('#fallsky-featured-area-custom-content-video').get(0);
						},
						onStateChange: function( e ) {
							if(YT.PlayerState.ENDED === e.data){
								e.target.playVideo();
							}
						}
					},
					playerVars: {
						autoplay: 		1,
						controls: 		0,
						disablekb: 		1,
						fs: 			0,
						iv_load_policy: 3,
						loop: 			1,
						modestbranding: 1,
						playsinline: 	1,
						rel: 			0,
						showinfo: 		0
					}
				});
				$(window).on('resize', function(){
					if(handler.container.ratio && handler.video){
						handler.resizeVideo.call(handler);
					}
				});
			});
		}
	});

	// Initialize the custom header when the DOM is ready.
	window.wp.featuredAreaCustomContentVideo = new CustomBgVideo();
	document.addEventListener('DOMContentLoaded', window.wp.featuredAreaCustomContentVideo.initialize.bind(window.wp.featuredAreaCustomContentVideo), false);

	// Selective refresh support in the Customizer.
	if('customize' in window.wp){
		window.wp.customize.selectiveRefresh.bind('render-partials-response', function(response){
			if('fallsky_featured_custom_content_bg_video_settings' in response){
				settings = response.fallsky_featured_custom_content_bg_video_settings;
			}
		});

		window.wp.customize.selectiveRefresh.bind('partial-content-rendered', function(placement){
			if('fallsky_home_custom_content_bg_video' === placement.partial.id){
				window.wp.featuredAreaCustomContentVideo.initialize();
			}
		});
	}

})(window, window.fallsky_featured_custom_content_bg_video_settings || false, jQuery);
