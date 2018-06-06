(function($){
	var loftocean = loftocean || {};
	loftocean.lo_author = {
		init_settings: function(){},
		shortcode: function(content){
			var settings = this.get_settings(),
				attrs = '';
			$.each(settings, function(i, v){
				if('name' != i){
					attrs += ' ' + i + '="' + v + '"';
				}
			});
			return content + '[' + settings.name + attrs + ']';
		},
		get_settings: function(){
			var $generator = $('.loftocean-shortcode-generator-wrap'),
				authors = false,
				type = 'all', 
				settings = {};
			settings.name       = $generator.find('input[name=loftocean-shortcode-name]').val();
			settings.layout     = $generator.find('input[name=author-layout]:checked').val();
			settings.bio        = $generator.find('input[name=hide-bio]:checked').length ? 'hide' : 'show';
			settings.icons      = $generator.find('input[name=hide-social-icons]:checked').length ? 'hide' : 'show';
			settings.post_count = $generator.find('input[name=hide-post-count]:checked').length ? 'hide' : 'show';
			settings.show_by    = $generator.find('select[name=show-author-by]').val();
			if('name' == settings.show_by){
				type = 'names';
				settings.names = '';
				authors = $generator.find('input[name=author-name]:checked').length ? $generator.find('input[name=author-name]:checked') : false;
			}
			else if('role' == settings.show_by){
				settings.roles = '';
				authors = $generator.find('input[name=author-roles]:checked').length ? $generator.find('input[name=author-roles]:checked') : false;
				type = 'roles';
			}
			if(authors && authors.length){
				var values = []; 
				authors.each(function(){
					values.push($(this).val());
				});
				settings[type] = values.join(',');
			}
			return settings;
		}
	};
	loftocean.lo_drop_caps = {
		init_settings: function(content){
			$('.loftocean-shortcode-generator-wrap textarea[name=drop-caps-content]').val(content);
		},
		shortcode: function(content){
			var settings = this.get_settings(),
				attrs = '';
			$.each(settings, function(i, v){
				if('name' != i){
					attrs += ' ' + i + '="' + v + '"';
				}
			});
			return '[' + settings.name + attrs + ']' + content + '[/' + settings.name + ']';
		},
		get_settings: function(){
			var $generator = $('.loftocean-shortcode-generator-wrap'),
				settings = {};
			settings.name  = $generator.find('input[name=loftocean-shortcode-name]').val();
			settings.style = $generator.find('input[name=drop-caps-style]:checked').val();
			settings.tag   = $generator.find('select[name=drop-caps-wrap]').val();
			return settings;
		}
	};
	loftocean.lo_highlight = {
		init_settings: function(){},
		shortcode: function(content){
			var settings = this.get_settings(),
				attrs = '';
			$.each(settings, function(i, v){
				if('name' != i){
					attrs += ' ' + i + '="' + v + '"';
				}
			});
			return '[' + settings.name + attrs + ']' + content + '[/' + settings.name + ']';
		},
		get_settings: function(){
			var $generator = $('.loftocean-shortcode-generator-wrap'),
				settings = {};
			settings.name  = $generator.find('input[name=loftocean-shortcode-name]').val();
			settings.style = $generator.find('input[name=highlight-style]:checked').val();
			return settings;
		}
	};
	loftocean.lo_tweet = {
		init_settings: function(){},
		shortcode: function(content){
			var settings = this.get_settings(),
				attrs = '';
			$.each(settings, function(i, v){
				if('name' != i){
					attrs += ' ' + i + '="' + v + '"';
				}
			});
			return '[' + settings.name + attrs + ']' + content + '[/' + settings.name + ']';
		},
		get_settings: function(){
			var $generator = $('.loftocean-shortcode-generator-wrap'),
				settings = {};
			settings.name  = $generator.find('input[name=loftocean-shortcode-name]').val();
			settings.via   = $generator.find('input[name=tweet-account]').val();
			settings.style = $generator.find('input[name=tweet-style]:checked').val();
			if(settings.style == 'paragraph'){
				settings.tag = $generator.find('select[name=tweet-wrap]').val();
			}
			return settings;
		}
	};
	loftocean.lo_row = {
		init_settings: function(content){
			var $generator = $('.loftocean-shortcode-generator-wrap'),
				$settings = $generator.find('.loftocean-shortcode-generator-body .shortcode-settings');
			if(content){
				$settings.find('[name=column1-content]').val(content);
				$settings.find('[name=column2-content]').val(content);
			}
		},
		shortcode: function(content){
			var settings = this.get_settings();

			return "[" + settings.name + "]\n"
				+ "[lo_column size=\"1/2\"]" + settings.column1_content + "[/lo_column]\n"
				+ "[lo_column size=\"1/2\"]" + settings.column2_content + "[/lo_column]\n"
				+ "[/" + settings.name + "]";
		},
		get_settings: function(){
			var $generator = $('.loftocean-shortcode-generator-wrap'),
				settings = {};
			settings.name = $generator.find('input[name=loftocean-shortcode-name]').val();
			settings.column1_content = $generator.find('[name=column1-content]').val();
			settings.column2_content = $generator.find('[name=column2-content]').val();

			return settings;
		}
	};
	loftocean.lo_column = {
		init_settings: function(content){
			var $generator = $('.loftocean-shortcode-generator-wrap'),
				$settings = $generator.find('.loftocean-shortcode-generator-body .shortcode-settings');
			content ? $settings.find('[name=column-content]').val(content) : '';
		},
		shortcode: function(content){
			var settings = this.get_settings(),
				attrs = '';
			$.each(settings, function(i, v){
				if(['name', 'content'].indexOf(i) === -1){
					attrs += ' ' + i + '="' + v + '"';
				}
			});
			content = settings.content;
			return '[' + settings.name + attrs + ']' + content + '[/' + settings.name + ']';
		},
		get_settings: function(){
			var $generator = $('.loftocean-shortcode-generator-wrap'),
				settings = {};
			settings.name = $generator.find('input[name=loftocean-shortcode-name]').val();
			settings.size = $generator.find('input[name=column-sizes]:checked').val();
			settings.content = $generator.find('[name=column-content]').val();
			return settings;
		}
	};
	loftocean.lo_tabs = {
		init_settings: function(content){
			var $generator = $('.loftocean-shortcode-generator-wrap'),
				$settings = $generator.find('.loftocean-shortcode-generator-body .shortcode-settings');
			if(content){
				$settings.find('[name=tab1-content]').val(content);
				$settings.find('[name=tab2-content]').val(content);
			}
		},
		shortcode: function(content){
			var settings = this.get_settings(),
				attrs = '';
			$.each(settings, function(i, v){
				if(['name', 'tab1_title', 'tab1_content', 'tab2_title', 'tab2_content'].indexOf(i) === -1){
					attrs += ' ' + i + '="' + v + '"';
				}
			});

			return "[" + settings.name + attrs + "]\n"
				+ "[lo_tab title=\"" + settings.tab1_title + "\"]" + settings.tab1_content + "[/lo_tab]\n"
				+ "[lo_tab title=\"" + settings.tab2_title + "\"]" + settings.tab2_content + "[/lo_tab]\n"
				+ "[/" + settings.name + "]";
		},
		get_settings: function(){
			var $generator = $('.loftocean-shortcode-generator-wrap'),
				settings = {};
			settings.name = $generator.find('input[name=loftocean-shortcode-name]').val();
			settings.direction = $generator.find('input[name=tabs-direction]:checked').val();
			settings.tab1_title = $generator.find('input[name=tab1-title]').val();
			settings.tab1_content = $generator.find('[name=tab1-content]').val();
			settings.tab2_title = $generator.find('input[name=tab2-title]').val();
			settings.tab2_content = $generator.find('[name=tab2-content]').val();
			return settings;
		}
	};
	loftocean.lo_tab = {
		init_settings: function(content){
			var $generator = $('.loftocean-shortcode-generator-wrap'),
				$settings = $generator.find('.loftocean-shortcode-generator-body .shortcode-settings');
			content ? $settings.find('[name=tab-content]').val(content) : '';
		},
		shortcode: function(content){
			var settings = this.get_settings(),
				attrs = '';
			$.each(settings, function(i, v){
				if(['name', 'content'].indexOf(i) === -1){
					attrs += ' ' + i + '="' + v + '"';
				}
			});
			content = settings.content;
			return '[' + settings.name + attrs + ']' + content + '[/' + settings.name + ']';
		},
		get_settings: function(){
			var $generator = $('.loftocean-shortcode-generator-wrap'),
				settings = {};
			settings.name = $generator.find('input[name=loftocean-shortcode-name]').val();
			settings.title = $generator.find('input[name=tab-title]').val();
			settings.content = $generator.find('[name=tab-content]').val();
			return settings;
		}
	};
	loftocean.lo_accordions = {
		init_settings: function(content){
			var $generator = $('.loftocean-shortcode-generator-wrap'),
				$settings = $generator.find('.loftocean-shortcode-generator-body .shortcode-settings');
			if(content){
				$settings.find('[name=accordion1-content]').val(content);
				$settings.find('[name=accordion2-content]').val(content);
			}
		},
		shortcode: function(content){
			var settings = this.get_settings(),
				attrs = '';
			$.each(settings, function(i, v){
				if(['name', 'accordion1_title', 'accordion1_content', 'accordion2_title', 'accordion2_content'].indexOf(i) === -1){
					attrs += ' ' + i + '="' + v + '"';
				}
			});
			content = content ? content : 'Accordion Content';
			return "[" + settings.name + attrs + "]\n"
				+ "[lo_accordion title=\"" + settings.accordion1_title + "\"]" + settings.accordion1_content + "[/lo_accordion]\n"
				+ "[lo_accordion title=\"" + settings.accordion2_title + "\"]" + settings.accordion2_content + "[/lo_accordion]\n"
				+ "[/" + settings.name + "]";
		},
		get_settings: function(){
			var $generator = $('.loftocean-shortcode-generator-wrap'),
				settings = {};
			settings.name = $generator.find('input[name=loftocean-shortcode-name]').val();
			settings.accordion1_title = $generator.find('input[name=accordion1-title]').val();
			settings.accordion1_content = $generator.find('[name=accordion1-content]').val();
			settings.accordion2_title = $generator.find('input[name=accordion2-title]').val();
			settings.accordion2_content = $generator.find('[name=accordion2-content]').val();
			return settings;
		}
	};
	loftocean.lo_accordion = {
		init_settings: function(content){
			var $generator = $('.loftocean-shortcode-generator-wrap'),
				$settings = $generator.find('.loftocean-shortcode-generator-body .shortcode-settings');
			content ? $settings.find('[name=accordion-content]').val(content) : '';
		},
		shortcode: function(content){
			var settings = this.get_settings(),
				attrs = '';
			$.each(settings, function(i, v){
				if(['name', 'content'].indexOf(i) === -1){
					attrs += ' ' + i + '="' + v + '"';
				}
			});
			content = settings.content;
			return '[' + settings.name + attrs + ']' + content + '[/' + settings.name + ']';
		},
		get_settings: function(){
			var $generator = $('.loftocean-shortcode-generator-wrap'),
				settings = {};
			settings.name = $generator.find('input[name=loftocean-shortcode-name]').val();
			settings.title = $generator.find('input[name=accordion-title]').val();
			settings.content = $generator.find('[name=accordion-content]').val();
			if($generator.find('input[name=accordion-open]:checked').length){
				settings.open = 'on';
			}
			return settings;
		}
	};
	loftocean.lo_divider = {
		init_settings: function(){
			var $generator = $('.loftocean-shortcode-generator-wrap'),
				$settings = $generator.find('.loftocean-shortcode-generator-body .shortcode-settings');
			$settings.find('input[name=divider-color]').wpColorPicker();
		},
		shortcode: function(content){
			var settings = this.get_settings(),
				attrs = '';
			$.each(settings, function(i, v){
				if('name' != i){
					attrs += ' ' + i + '="' + v + '"';
				}
			});
			return '[' + settings.name + attrs + ']';
		},
		get_settings: function(){
			var $generator = $('.loftocean-shortcode-generator-wrap'),
				settings = {};
			settings.name = $generator.find('input[name=loftocean-shortcode-name]').val();
			settings.style = $generator.find('input[name=divider-style]:checked').val();
			settings.size = $generator.find('select[name=divider-size]').val();
			settings.color = $generator.find('input[name=divider-color]').wpColorPicker('color');
			return settings;
		}
	};
	loftocean.lo_button = {
		init_settings: function(text){
			var $generator = $('.loftocean-shortcode-generator-wrap'),
				$settings = $generator.find('.loftocean-shortcode-generator-body .shortcode-settings');
			text ? $settings.find('input[name=button-text]').val(text) : '';
			$settings.find('input[name=button-bg-custom-color]').wpColorPicker();
			$settings.find('input[name=button-text-custom-color]').wpColorPicker();
		},
		shortcode: function(content){
			var settings = this.get_settings(),
				attrs = '';
			$.each(settings, function(i, v){
				if(['name', 'text'].indexOf(i) === -1){
					attrs += ' ' + i + '="' + v + '"';
				}
			});
			content = settings.text;
			return '[' + settings.name + attrs + ']' + content + '[/' + settings.name + ']';
		},
		get_settings: function(){
			var $generator = $('.loftocean-shortcode-generator-wrap'),
				settings = {};
			settings.name = $generator.find('input[name=loftocean-shortcode-name]').val();
			if($generator.find('input[name=button-url]').val()){
				settings.url = $generator.find('input[name=button-url]').val();
			}
			if($generator.find('input[name=button-target]:checked').length){
				settings.target = 'new';
			}
			settings.size = $generator.find('input[name=button-size]:checked').val();
			if($generator.find('input[name=button-bg-color]:checked').val() == 'custom'){
				settings.background = $generator.find('input[name=button-bg-custom-color]').wpColorPicker('color');
			}
			if($generator.find('input[name=button-text-color]:checked').val() == 'custom'){
				settings.color = $generator.find('input[name=button-text-custom-color]').wpColorPicker('color');
			}
			settings.text = $generator.find('input[name=button-text]').val();
			return settings;
		}
	};

	function loftocean_sc_get_content() {
		var hasQuicktags = typeof QTags !== 'undefined';

		if(wpActiveEditor){
			if(tinymce && tinymce.activeEditor && !tinymce.activeEditor.isHidden()){
				return tinymce.activeEditor.selection.getContent({format: 'html'});
			}
			else if(hasQuicktags){
				var sel, startPos, endPos, text, canvas = document.getElementById(wpActiveEditor);

				if(!canvas){
					return '';
				}

				if(document.selection){ //IE
					canvas.focus();
					sel = document.selection.createRange();
					return sel.text;
				} 
				else if(canvas.selectionStart || canvas.selectionStart === 0){ // FF, WebKit, Opera
					text = canvas.value;
					startPos = canvas.selectionStart;
					endPos = canvas.selectionEnd;
					return (endPos >= startPos) ? text.substring(startPos, endPos) : '';
				} 
			}
		}
		return '';
	}
	function loftocean_sc_set_content(html) {
		if(html && send_to_editor){
			send_to_editor(html);
			return true;
		}
		return false;
	}
	$(document).ready(function(){
		if($('[name=loftocean-shortcode-color]').length) $('[name=loftocean-shortcode-color]').wpColorPicker(); 

		$('body').on('click', '#insert-loftocean-shortcode-button', function(e){
			e.preventDefault();
			var $sc_generator = $('.loftocean-shortcode-generator-wrap');
			if($sc_generator.length) {
				$('body').addClass('loftocean-shortcode-generator-open').css('overflow', 'hidden');
				$sc_generator.addClass('show').css('display', 'block');
			}
		})
		.on('click', '.loftocean-shortcode-generator-wrap .shortcode-home', function(e){
			e.preventDefault();
			$('.loftocean-shortcode-generator-wrap').find('.shortcode-settings').html('').css('display', '')
				.end().find('.shortcode-list').css('display', 'block');
		})
		.on('click', '.loftocean-shortcode-generator-wrap .close', function(e){
			e.preventDefault();
			var $sc_generator = $('.loftocean-shortcode-generator-wrap');
			if($sc_generator.length) {
				$('body').removeClass('loftocean-shortcode-generator-open').css('overflow', '');
				$sc_generator.removeClass('show').css('display', '')
					.find('.shortcode-settings').html('')
					.end().find('.shortcode-list').css('display', '');
			}
		})
		.on('click', '.loftocean-shortcode-generator-wrap .shortcode-btn', function(e){
			e.preventDefault();
			var sc = $(this).attr('data-shortcode'),
				$sc_generator = $('.loftocean-shortcode-generator-wrap');
			if(sc){
				if(('complex' == $(this).attr('data-type')) && $('#tmpl-loftocean-shortcode-' + sc).length){
					var html = $('#tmpl-loftocean-shortcode-header').html() 
							+ $('#tmpl-loftocean-shortcode-' + sc).html() 
							+ $('#tmpl-loftocean-shortcode-footer').html();

					$sc_generator.find('.shortcode-settings').html(html).css('display', 'block')
						.end().find('.shortcode-list').css('display', 'none')
						.end().find('input[name=loftocean-shortcode-name]').val(sc);

					if(loftocean && loftocean[sc] && loftocean[sc].init_settings){
						loftocean[sc].init_settings(loftocean_sc_get_content());
					}
				}
				else {
					var selection = loftocean_sc_get_content(),
						shortcode = loftocean[sc] ? loftocean[sc].shortcode(selection) : selection + '[' + sc + ']';
					loftocean_sc_set_content(shortcode);
					$('.loftocean-shortcode-generator-wrap .close').trigger('click');
				}
			}
		})
		.on('click', '.loftocean-shortcode-generator-wrap .insert', function(e){
			e.preventDefault();
			var sc = $('.loftocean-shortcode-generator-wrap input[name=loftocean-shortcode-name]').val();
			if(loftocean && loftocean[sc]){
				var selection = loftocean_sc_get_content(),
					shortcode = loftocean[sc] ? loftocean[sc].shortcode(selection) : selection;
				loftocean_sc_set_content(shortcode);
			}
			$('.loftocean-shortcode-generator-wrap .close').trigger('click');
		});

		$('.loftocean-shortcode-generator-wrap .shortcode-settings').on('change', '[name=show-author-by]', function(e){
			var val = $(this).val();
			$(this).siblings('div').css('display', 'none');
			$(this).siblings('.by-' + val).css('display', '');
		})
		.on('change', '[name=tweet-style]', function(e){
			var val = $(this).val(),
				$tag = $('.loftocean-shortcode-generator-wrap .shortcode-settings #tweet-it-wrap-tag');
			(val == 'inline') ? $tag.css('display', 'none') : $tag.css('display', 'block');
		})
		.on('change', '[name=button-bg-color], [name=button-text-color]', function(e){
			var $colorPicker = $(this).parent().siblings('.color-picker-wrapper');
			$colorPicker.css('display', (($(this).val() == 'custom') ? '' : 'none'));
		});
	});
})(jQuery);