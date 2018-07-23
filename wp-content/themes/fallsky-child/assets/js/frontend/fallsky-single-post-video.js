(function($){
	"use strict";

	var NativeHandler, YouTubeHandler, VimeoHandler;

	window.Fallsky_Single_post_Background_Video = function(html){
		this.activeHandler = false;
		this.handlers = {
			nativeVideo: 	new NativeHandler(),
			youtube: 		new YouTubeHandler(),
			vimeo: 			new VimeoHandler()
		};
		return this.init(html);
	}

	Fallsky_Single_post_Background_Video.prototype = {
		init: function(html){
			var self = this;
			for(var id in this.handlers){
				var handler = this.handlers[id];
				if('test' in handler && handler.test(html)){
					self.activeHandler = handler.initialize.call(handler, html); 
					break;
				}
			} 
			return this.activeHandler ? this.activeHandler : false;
		}
	};

	function BaseHandler(){}
	BaseHandler.prototype = {
		deferred: false,
		initialize: function(html){
			var handler 	= this;
			this.html 		= html;
			this.deferred 	= $.Deferred(); 
			this.$container = $('<div>', {'class': 'fallsky-media-wrapper hide'}).appendTo($('body'));
			this.container 	= this.$container.get(0);
			this.ready();
			this.events();
			this.deferred.promise();
			return this;
		},
		ready: function() {},
		setVideo: function(node){
			this.$container.append($('<div>', {'class' : 'close-button', 'text': 'close'})).append(node);
		},
		test: function() {
			return false;
		},
		getDimensions: function(){
			var cwidth 	= this.$container.width() 	|| 16,
				cheight = this.$container.height() 	|| 9,
				cratio	= cheight / cwidth,
				ratio 	= this.container.ratio || (9 / 16);
			return ratio > cratio ? {'width': cwidth, 'height': cwidth * ratio} : {'height': cheight, 'width': cheight / ratio};
		},
		resizeVideo: function(){
			var video 		= this.video,
				dimension 	= this.getDimensions();
			video.width 	= dimension.width;
			video.height 	= dimension.height;
		},
		events: function(){
			var handler = this;
			this.$container.on('click', '.close-button', function(e){
				handler.pause();
			});
		},
		play: function(){
			this.$container.addClass('fallsky-media-fullscreen-playing show').removeClass('hide');
			this.resizeVideo();
			$('body').css('overflow', 'hidden');
			this.playVideo();
		},
		playVideo: function(){},
		pauseVideo: function(){},
		pause: function(){
			this.pauseVideo();
			this.$container.removeClass('fallsky-media-fullscreen-playing show').addClass('hide');
			$('body').css('overflow', '');
			$('.play-video-btn').removeClass('playing');
		}
	};

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

	NativeHandler = BaseHandler.extend({
		test: function(html){
			var video = document.createElement('video'),
				$html = $(html), found = false, type = false,
				regex = /<video.*>.*<\/video>/, match = regex.exec(html);
			if(match){
				this.video = match[0];
				return true;
			}
			return false;
		},
		ready: function(){
			var handler = this, video = $(this.video).get(0);

			video.autoplay 	= false;
			video.loop 		= 'loop';

			this.video = video;
			handler.setVideo(video);
			$(handler.video).on('loadedmetadata', function(){
				handler.container.ratio = (this.videoHeight || 9) / (this.videoWidth || 16);
				handler.deferred.resolve();
			});
			$(window).on('resize', function(){
				if(handler.container.ratio && handler.$container.hasClass('show')){
					handler.resizeVideo.call(handler);
				}
			});
		},
		playVideo: function(){
			this.video.play();
		},
		pauseVideo: function(){
			this.video.pause();
		}
	});

	YouTubeHandler = BaseHandler.extend({
		regex: /^.*(?:(?:youtu\.be\/|v\/|vi\/|u\/\w\/|embed\/)|(?:(?:watch)?\?v(?:i)?=|\&v(?:i)?=))([^#\&\?'"]*).*/,
		test: function(html){
			return this.regex.exec(html);
		},
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
		loadVideo: function() {
			var handler = this,
				video 	= $('<div>', {'class': 'youtube-video'}).get(0),
				vid 	= this.html.match(this.regex)[1];

			$.getJSON('https://noembed.com/embed', {format: 'json', url: ('https://www.youtube.com/watch?v=' + vid)}, function(data){
				var dimensions;
				handler.container.ratio = (data.height || 9) / (data.width || 16);
				dimensions = handler.getDimensions.call(handler);

				handler.setVideo(video);
				handler.player = new YT.Player(video, {
					videoId: 	vid,
					width: 		dimensions.width,
					height: 	dimensions.height,
					events: 	{
						onReady: function(e){
//							e.target.mute();
							handler.video = handler.$container.find('.youtube-video').get(0);
							handler.deferred.resolve();
						},
						onStateChange: function(e){
							if(YT.PlayerState.ENDED === e.data){
								e.target.playVideo();
							}
						}
					},
					playerVars: {
						autoplay: 		0,
						controls: 		1,
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
					if(handler.container.ratio && handler.$container.hasClass('show') && handler.video){
						handler.resizeVideo.call(handler);
					}
				});
			});
		},
		playVideo: function(){
			this.player.playVideo();
		},
		pauseVideo: function(){
			this.player.pauseVideo();
		}
	});

	VimeoHandler = BaseHandler.extend({
		regex: /https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)/,
	 	test: function(html){
			return this.regex.exec(html);
		},
		ready: function() {
			this.loadVideo();
		},
		loadVideo: function() {
			var handler = this,
				vid 	= this.html.match(this.regex)[3],
				video 	= $('<div>', {'id': 'vimeo-video-' + vid}).get(0);

			if(Vimeo && Vimeo.Player){
				var options = { 'id': vid, 'loop': true };
				handler.setVideo(video);
				handler.player = new Vimeo.Player('vimeo-video-' + vid, options);
				Promise.all([handler.player.getVideoWidth(), handler.player.getVideoHeight()]).then(function(dimensions) {
					handler.container.ratio = dimensions[1] / dimensions[0]; 
					handler.video = handler.$container.find('iframe').get(0);
					handler.deferred.resolve();
					$(window).on('resize', function(){
						if(handler.container.ratio && handler.$container.hasClass('show')){
							handler.resizeVideo.call(handler);
						}
					});
				});
			}
		},
		playVideo: function(){
			this.player.play();
		},
		pauseVideo: function(){
			this.player.pause();
		},
		resizeVideo: function(){
			var video 		= this.video,
				$container 	= this.$container;
			video.width 	= $container.width();
			video.height 	= $container.height();
		},
	});
})(jQuery);