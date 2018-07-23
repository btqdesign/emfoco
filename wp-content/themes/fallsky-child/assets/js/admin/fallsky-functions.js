(function($){
	"use strict";

	// Theme customized media lib
	window.fallsky_media = {
		input: '',
		frame: '',
		frames: {},
		mediaFrame: function(){
			if(!this.frames[this.frame]){
				this.frames[this.frame] = wp.media({
					id: 'fallsky-image-uploader',
					// frame: 'post', 
					// state: 'insert',
					editing: true,
					library: {
						type : 'image' == this.frame ? 'image' : ['image', 'video']
					},
					multiple: false  // Set this to true to allow multiple files to be selected
				})
				.on('select', function(){
					var media = fallsky_media.frames[fallsky_media.frame].state().get('selection').first().toJSON();
					fallsky_media.input.trigger('fallsky.media.changed', media);
					fallsky_media.input = ''; // reset input
				})
				.on('open', function(){
					var selection = fallsky_media.frames[fallsky_media.frame].state().get('selection'),
						image_id  = fallsky_media.input.val();
					selection.reset();
					if(image_id && (image_id !== '')){
						var attachment = wp.media.attachment(image_id);
						attachment.fetch(); 
						selection.add(attachment ? [attachment] : []);
					}
				});
			}
			return this.frames[this.frame];
		},
		open: function($input, frame){
			this.input = $input.first();
			this.frame = frame || 'image';
			this.mediaFrame().open();
		}
	};

	window.fallskyInitEditor = function(id, settings, $container){
		window.tinymce.init($.extend(settings.mce, {
			init_instance_callback: function(editor){
				editor.on('Dirty', function(e){
					var content = wp.editor.getContent(id);
					$container.find('textarea').val(content).trigger('change');
				});
				if($container.find('.wp-editor-wrap').length){
					$container.find('.wp-editor-wrap').removeClass('html-active').addClass('tmce-active');
				}
			}
		}));
		settings.qt ? quicktags(settings.qt) : '';
	}
	$('document').ready(function(){
		var $static_hp_content 	= $('select#fallsky_static_homepage_content');
		if($static_hp_content.length){
			var $show_on_front 	= $('[name=show_on_front][type=radio]'),
				$page_on_front 	= $('select#page_on_front');
			if($show_on_front.length){
				$show_on_front.on('change', function(){
					('posts' == $(this).val()) || ($page_on_front.val() < 1) 
						? $static_hp_content.attr('disabled', 'disabled') : $static_hp_content.removeAttr('disabled');
				});
			}
			if($page_on_front.length){
				$page_on_front.on('change', function(){
					var val = $(this).val();
					val && (val > 0) ? $static_hp_content.removeAttr('disabled') : $static_hp_content.attr('disabled', 'disabled');
				});
			}
		}
	});
})(jQuery);