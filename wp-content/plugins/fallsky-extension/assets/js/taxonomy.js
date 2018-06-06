/**
* For WP admin
*   1. Custom media uploader
*	2. Media clear after ajax updated
*/
(function($){
	$(document).ready(function(){
		var loftocean_media = {
			input: '',
			mediaFrame: function(){
				if(!this.frame){
					this.frame = wp.media({
						id: 'loftocean-tax-image-uploader',
						// frame: 'post', 
						// state: 'insert',
						editing: true,
						library: {
							type : 'image'
						},
						multiple: false  // Set this to true to allow multiple files to be selected
					})
					.on('select', function(){
						var image 	= loftocean_media.frame.state().get('selection').first().toJSON(),
							url 	= (image.sizes && image.sizes.thumbnail) ? image.sizes.thumbnail.url : image.url;

						loftocean_media.input.val(image.id).trigger('change')
						 	.siblings('.loftocean-upload-image').html($('<img>', {'src': url, 'style': 'max-width: 50%;'}))
							.siblings('.loftocean-remove-image').css('display', 'block');
						// reset input
						loftocean_media.input = '';
					})
					.on('open', function(){
						var selection = loftocean_media.frame.state().get('selection'),
							image_id  = loftocean_media.input.val();
						selection.reset();
						if(image_id && (image_id !== '')){
							var attachment = wp.media.attachment(image_id);
							attachment.fetch(); 
							selection.add(attachment ? [attachment] : []);
						}
					});
				}
				return this.frame;
			},
			open: function($input){
				this.input = $input;
				this.mediaFrame().open();
			}
		};
		
		$('body').on('click', '.loftocean-upload-image', function(e){
			e.preventDefault();
			loftocean_media.open($(this).siblings('input[type=hidden]').first());
		})
		.on('click', '.loftocean-remove-image', function(e){
			e.preventDefault();
			var $upload = $(this).siblings('.loftocean-upload-image').first();
			$(this).siblings('input[type=hidden]').first().val('').trigger('change');
			$(this).css('display', 'none');
			$upload.text($upload.attr('data-upload'));
		});

		if($('input[value=add-tag]').length && $('.term-img-wrap').length){
			$(document).ajaxComplete(function(event, request, options){
				if(request && (4 === request.readyState) && (200 === request.status) && request.responseXML){
					var res = wpAjax.parseAjaxResponse( request.responseXML, 'ajax-response');
					if(!res || res.errors || !$('.term-img-wrap').length || !$('.term-img-wrap').find('.loftocean-remove-image').length){
						return;
					}
					$('.term-img-wrap').find('.loftocean-remove-image').trigger('click');
				}
			});
		}
	});
})(jQuery);