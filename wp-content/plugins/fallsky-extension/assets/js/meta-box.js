(function($){
	$(document).ready(function(){
		$('body').on('change', 'input[name=loftocean-featured-post-inline-edit]', function(e){
			var featured_ajax = loftocean_meta && loftocean_meta.featured_post ? loftocean_meta.featured_post : false;
			if($(this).attr('data-id') && featured_ajax){
				var url = featured_ajax.url,
					data = {
						'action': featured_ajax.action,
						'post_id': $(this).attr('data-id'),
						'loftocean_featured_post': ($(this).attr('checked') ? 'on' : 'off'),
						'nonce': featured_ajax.nonce
					};
				$.post(url, data);
			}
		})
		.on('change', 'select.loftocean-author-widget-choose-by', function(e){
			var val = $(this).val(),
				$lists = $(this).parent().siblings('.author-list-choices');
			$lists.css('display', 'none');
			switch(val){
				case 'name':
					$lists.filter('.author-list-by-name').css('display', '');
					break;
				case 'role':
					$lists.filter('.author-list-by-role').css('display', '');
					break;
			}
		})
		.on('click', '.loftocean-post-counter-wrap a', function(e){
			e.preventDefault();
			var $a = $(this), $input = $a.siblings('input').first(),
				$not_edit = $a.add($a.siblings('a')).not('.edit');
			if($a.hasClass('edit')){
				$a.css('display', 'none').siblings('a').css('display', '');
				$input.removeAttr('readonly').attr('previous-value', $input.val());
			}
			else{
				$not_edit.css('display', 'none');
				$a.siblings('a.edit').css('display', '');
				if($a.hasClass('cancel')) $input.val($input.attr('previous-value'));
				$input.attr('readonly', 'readonly');
			}
		})
		.on('change', '#post_author_override', function(e){
			var uid = $(this).val(), $co_authors = $('#loftocean-single-post-co-authors');
			$co_authors.children().css('display', '').filter('[value=' + uid + ']').css('display', 'none').removeAttr('selected');
		});

		var $media = false;
		if($('#loftocean-tmpl-format-meta-box').length && $('#formatdiv').length){
			var $format = $('#formatdiv'),
				$format_tmpl = $('#loftocean-tmpl-format-meta-box'),
				$title = false;
			function loftocean_sync_format_meta_box(format){
				var formats = ['gallery', 'audio', 'video'];
				$media.children('div.format').css('display', 'none');
				(format && (formats.indexOf(format) !== -1)) ? $media.children('div.format.' + format).add($title).css('display', '') : $title.css('display', 'none'); 
			}
			$format.find('.inside').append($format_tmpl.html());
			$media = $format.find('#loftocean-format-media');
			$title = $media.children('h4');
			loftocean_sync_format_meta_box(($format_tmpl.attr('data-format') || ''));
			$('body').on('change', 'input[name=post_format]', function(e){
				loftocean_sync_format_meta_box($(this).val());
			})
			.on('click', '#loftocean-format-media .format-media', function(e){
				e.preventDefault();
				if($(this).hasClass('gallery')){
					loftocean_format_media.gallery.open();
				}
				else if($(this).hasClass('video')){
					loftocean_format_media.video.open();
				}
				else if($(this).hasClass('audio')){
					loftocean_format_media.audio.open();
				}
			})
			.on('change', '#loftocean-format-media textarea', function(e){
				if($(this).siblings('input[type=hidden]').length){
					var $hidden = $(this).siblings('input[type=hidden]');
					$hidden.val('');
				}
			});
		}

		wp.loftocean = { };

		wp.loftocean_media_tools = function(){
			wp.loftocean.gallery = new wp.media({
				frame: 'post',
				state: 'gallery-edit',
				title:	wp.media.view.l10n.editGalleryTitle,
				'media-sidebar': false,
				editing: false,
			});
			wp.loftocean.audio = new wp.media({
				library: {
					type : 'audio'
				},
				title: wp.media.view.l10n.addMedia,
				multiple: false
			});
			wp.loftocean.video = new wp.media({
				library: {
					type : 'video'
				},
				title: wp.media.view.l10n.addMedia,
				multiple: false
			});

			wp.loftocean.gallery.on('update', function(selection){
				var state = wp.loftocean.gallery.state();
				selection = selection || state.get('selection');

				if(!selection) return ;
				
				$media.find('.gallery-id').val(wp.loftocean.gallery.states.get('gallery-edit').get('library').pluck('id').join(','));
				$media.find('.gallery-code').val(wp.media.gallery.shortcode(selection).string());
			})
			.on('open', function(){
				var controller = wp.loftocean.gallery.states.get('gallery-edit'),
					library	= controller.get('library'),
					ids  = $media.find('.gallery-id').val();
				if(ids){
					ids = ids.split(',');
					ids.forEach(function(id){
						var attachment = wp.media.attachment(id);
						attachment.fetch();
						library.add(attachment ? [attachment] : []);
					});
				}
			});

			wp.loftocean.video.on('select', function(selection){
				var state = wp.loftocean.video.state(),
					video = {},
					attrs = {};
				selection = selection || state.get('selection').first();
				attrs= selection.toJSON();
				video = { width: attrs.width, height: attrs.height, src: attrs.url };
				$media.find('.video-id').val(attrs.id);
				$media.find('.video-code').val(wp.media.video.shortcode(video).string());
			})
			.on('open', function(){
				var selection = wp.loftocean.video.state().get('selection'),
					id = $media.find('.video-id').val();
				if(id){
					var attachment = wp.media.attachment(id);
					attachment.fetch();
					selection.add(attachment ? [attachment] : []);
				}
			});

			wp.loftocean.audio.on('select', function(selection){
				var state = wp.loftocean.audio.state(),
					audio = {},
					attrs = {};
				selection = selection || state.get('selection').first();
				attrs= selection.toJSON();
				audio['src'] = attrs.url;
				$media.find('.audio-id').val(attrs.id);
				$media.find('.audio-code').val(wp.media.audio.shortcode(audio).string());
			})
			.on('open', function(){
				var selection = wp.loftocean.audio.state().get('selection'),
					id = $media.find('.audio-id').val();
				if(id){
					var attachment = wp.media.attachment(id);
					attachment.fetch();
					selection.add(attachment ? [attachment] : []);
				}
			});

			return wp.loftocean;
		};

		var loftocean_format_media = new wp.loftocean_media_tools();
		
		if($('.page-video-meta-box').length){
			$media = $('.page-video-meta-box')
			$('body').on('click', '.loftocean-page-upload-video', function(e){
				e.preventDefault();
				loftocean_format_media.video.open();
			})
			.on('change', '.page-video-meta-box textarea', function(e){
				if($(this).siblings('input[type=hidden]').length){
					var $hidden = $(this).siblings('input[type=hidden]');
					$hidden.val('');
				}
			});
		}
	});
})(jQuery);