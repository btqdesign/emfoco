(function($){
	var is_retina = ('devicePixelRatio' in window) && (parseInt(window.devicePixelRatio) >= 2),
		image_data_name = is_retina ? 'data-loftocean-retina-image' : 'data-loftocean-normal-image';

	// Replace images if needed
	$.fn.loftocean_image_preloader = function(){ 
		var $bg_images = $(this).attr('data-loftocean-image') ? $(this) : $(this).find('[data-loftocean-image=1]');
		if($bg_images.length){
			return $bg_images.each(function(){ 
				var self = $(this);
				if(self.attr('data-loftocean-image')){
					var name = self.prop('tagName'), image = self.attr(image_data_name);
					$(new Image()).on('load', function(){
						('IMG' == name) ? self.attr('src', image).removeAttr('style') : self.css({'background-image': 'url(' + image + ')', 'filter': ''});
						self.removeAttr('data-loftocean-retina-image').removeAttr('data-loftocean-normal-image').removeAttr('data-loftocean-image');
					}).attr('src', image);
				}
			});
		}
		return this;
	};
	setTimeout(function(){ $('body').loftocean_image_preloader(); }, 0);
	$('body').on('loftocean.preloader.done', function(e, elem){
		if(elem && $(elem).length && $(elem).find('[data-loftocean-image=1]').length){
			var $elem = $(elem);
			$elem.find('[data-loftocean-image=1]').addBack().each(function(){
				var $bg = $(this);
				if($bg.length && $bg.attr('data-loftocean-image')){
					var name = $bg.prop('tagName'), image = $bg.attr(image_data_name); 
					('IMG' == name) ? $bg.attr('src', image).removeAttr('style') : $bg.css({'background-image': 'url(' + image + ')', 'filter': ''});
					$bg.removeAttr('data-loftocean-retina-image').removeAttr('data-loftocean-normal-image').removeAttr('data-loftocean-image');
				}
			});
		}
	})
	.on('click', '#page .loftocean-gallery-zoom', function(e){
		e.preventDefault();
		var $body 	= $('body'),
			$wrap 	= $(this).parent(),
			$slick 	= $wrap.children('.image-gallery').first();
		if($body.hasClass('gallery-zoom')){
			$body.removeClass('gallery-zoom');
			$wrap.removeClass('fullscreen');
		}
		else{
			$body.addClass('gallery-zoom');
			$wrap.addClass('fullscreen');
		}
		$slick.slick('slickSetOption', 'speed', 500, true);
	})
	.on('click', '.post-content-gallery.justified-gallery-initialized .gallery-item', function(e){
		e.preventDefault();
		var gallery_id = $(this).closest('.justified-gallery-initialized').data('gallery-id'); console.log(gallery_id);
		if(gallery_id && $('.loftocean-popup-sliders .' + gallery_id).length){
			var $body = $('body'), index = $(this).index(),
				$wrap = $('.loftocean-popup-sliders .' + gallery_id),
				$slick = $wrap.children('.image-gallery').first();
			if(!$body.hasClass('gallery-zoom')){
				$body.addClass('gallery-zoom');
				$wrap.addClass('fullscreen').removeClass('hide');
				$slick.slick('slickGoTo', index).slick('slickSetOption', 'speed', 500, true);
			}
		}
	})
	.on('click', '.loftocean-popup-sliders .loftocean-popup-gallery-close', function(e){
		e.preventDefault();
		var $body = $('body'), $wrap = $(this).parent();
		if($body.hasClass('gallery-zoom')){
			$body.removeClass('gallery-zoom');
			$wrap.removeClass('fullscreen').addClass('hide');
		}
	});
}(jQuery));