(function($){
	"use strict";

	window.wp = window.wp || {};

	wp.fallskyWidgets = (function(){
		var editorParams		= false,
			widgetDependency 	= fallskyWidgets.widget_dependency || {},
			widgetItems 		= fallskyWidgets.widget_json || {},
			widgetsDone			= [],
			themeWidgets 		= fallskyWidgets.widgets || [];
		/**
		* Actual widget-added event handler function 
		* 	1. Check if any tinymce editor exists
		* 	2. Check if any color picker exists 
		*/
		function widgetAdded(e, widgetContainer){
			var widgetForm, idBase, widgetId, animatedCheckDelay = 50, renderWhenAnimationDone;
			// Note: '.form' appears in the customizer, whereas 'form' on the widgets admin screen.
			widgetForm = widgetContainer.find('> .widget-inside > .form, > .widget-inside > form'); 

			idBase = widgetForm.find('> .id_base').val();
			if(-1 === themeWidgets.indexOf(idBase)){
				return;
			}
			// Prevent initializing already-added widgets.
			widgetId = widgetForm.find('.widget-id').val();
			if(-1 !== widgetsDone.indexOf(widgetId)){
				return;
			}
			/*
			 * Render the widget once the widget parent's container finishes animating,
			 * as the widget-added event fires with a slideDown of the container.
			 * This ensures that the textarea is visible and enable the colorpicker, tinymce editor...
			 */
			renderWhenAnimationDone = function(){
				if(!widgetContainer.hasClass('open')){
					setTimeout(renderWhenAnimationDone, animatedCheckDelay);
				} 
				else{
					widgetsDone.push(widgetId);
					widgetFromInit(widgetContainer, idBase, false);
				}
			};
			renderWhenAnimationDone();
		}
		/*
		*
		*/
		function widgetUpdated(e, widgetContainer){
			if('widgets' !== window.pagenow){
				return ;
			}

			var widgetForm, idBase;
			// Note: '.form' appears in the customizer, whereas 'form' on the widgets admin screen.
			widgetForm = widgetContainer.find('> .widget-inside > .form, > .widget-inside > form'); 

			idBase = widgetForm.find('> .id_base').val();
			if(-1 === themeWidgets.indexOf(idBase)){
				return;
			}
			widgetFromInit(widgetContainer, idBase, true);
		}
		/**
		* Test if any special elements need to initialize, which including tinymce editor, colorpicker
		* @param jQuery object widget form
		*/
		function widgetFromInit(container, idBase, updated){
			var $colors = container.find('input.fallsky-color-picker'),
				$editor = container.find('.editor-widget-control.item-type-editor'),
				$image 	= container.find('.item-wrapper.item-type-image'),
				$number = container.find('input[type=number]'),
				$slider = container.find('.slider-widget-control');
			if($number.length){
				initNumber($number);
			}
			if($image.length){
				initImage($image);
			}
			if($colors.length){
				initColorPicker($colors);
			}
			if($editor.length && !updated){
				$editor.each(function(){
					initTinyMCE($(this).find('.editor-textarea-wrap textarea').data('id'), $(this));
				});
			}
			if($slider.length){
				initSlider($slider);
			}

			if((idBase in widgetDependency) && (idBase in widgetItems)){
				var deps = widgetDependency[idBase], its = widgetItems[idBase];
				container.find('.fallsky-widget-item').on('change', function(){
					var itemID = $(this).data('fallsky-widget-item-id'); 
					if(itemID in deps){
						widgetFormChanged(container, deps[itemID], its);
					}
				});
				$.each(deps, function(pid, items){
					widgetFormChanged(container, items, its);
				});
			}
		}
		/**
		* Initialize input[type=number] element, add change event handler
		* @param jQuery object
		*/
		function initNumber($number){
			$number.on('change', function(e){
				var val = parseInt($(this).val()),
					min = $(this).attr('min'),
					max = $(this).attr('max'); 
				if(min && (val < parseInt(min))){ 
					$(this).val(min);
				}
				else if(max && (val > parseInt(max))){
					$(this).val(max);
				}
			});
		}
		/**
		* Initialize element with type image, add events handler
		* @param jQuery object
		*/
		function initImage($image){
			$image.each(function(){
				var $container = $(this);
				$container.on('click', '.button.choose-media, .media-widget-preview', function(e){
					e.preventDefault();
					var $target 	= $(this),
						mediaType 	= $target.hasClass('media') ? 'media' : 'image';

					if(('media' == mediaType) && $target.hasClass('media-widget-preview') && $target.children('video').length){
						var video = $target.children('video').get(0);
						video.paused ? video.play() : video.pause();
					}
					else{
						var $input = $target.hasClass('media-widget-preview')
								? $target.siblings('input[type=hidden]') : $target.parent().siblings('input[type=hidden]');
						fallsky_media.open($input.first(), mediaType);
					}
				})
				.on('click', '.button.remove-media', function(e){
					e.preventDefault();
					var $this = $(this), $wrap = $this.parent(), $preview = $wrap.siblings('.media-widget-preview');
					$this.removeClass('not-selected').addClass('selected');
					$wrap.siblings('input[type=hidden]').val('').first().trigger('change');
					$preview.html('').append($('<div>', {'class': 'placeholder', 'text': $preview.attr('data-text-preview')}));
				})
				.on('fallsky.media.changed', 'input.fallsky-widget-item[type=hidden]', function(e, media){
					e.preventDefault();
					if(media && (-1 !== ['image', 'video'].indexOf(media.type))){ 
						var type 		= media.type,
							url 		= !media.sizes ?  media.url
								: (media.sizes.medium ? media.sizes.medium.url : (media.sizes.thumbnail ? media.sizes.thumbnail.url : media.url)),
							$media 		= $((('image' == type) ? '<img>' : '<video>'), {'class': 'attachment-thumb', 'src': url}),
							$input 		= $(this),
							$preview 	= $input.siblings('.media-widget-preview'),
							$buttons 	= $input.siblings('.media-widget-buttons'),
							$type 		= $input.siblings('input[type=hidden]');

						$preview.html('').append($media);
						$buttons.children('.button.remove-media').removeClass('selected').addClass('not-selected');
						$type.length ? $type.val(type) : '';
						$input.val(media.id).trigger('change');
					}
				});
			});
		}
		/**
		* Initialize element with type slider, add events handler
		* @param jQuery object
		*/
		function initSlider($slider){
			$slider.each(function(){
				var $elem = $(this).find('.loader-ui-slider'),
					$input = $(this).find('input').first();
				$elem.slider({
					'range': 	'min',
					'min': 		$elem.data('min'),
					'max': 		$elem.data('max'),
					'value': 	$elem.data('value'),
					'step': 	$elem.data('step'),
					'slide': 	function(event, ui){
						$input.val(ui.value).trigger('change');
					}
				});
			});
		}
		/**
		* Determine to show the widget elements if they have dependency set
		* @param jQuery object form
		* @param array list of element id related to current element changed
		* @param object items list with item id and its dependency settings
		*/	
		function widgetFormChanged(container, dependency, items){
			$.each(dependency, function(i, v){
				var $item = container.find('[data-fallsky-widget-item-id=' + v + ']');
				if($item.length && items[v]){
					var pass = true;
					$item = $item.closest('.item-wrapper');
					$.each(items[v], function(pid, attr){
						var operator	= attr.operator || 'in',
							$pitem 		= container.find('[data-fallsky-widget-item-id=' + pid + ']'),
							pvalue 	 	= ['radio', 'checkbox'].indexOf($pitem.first().attr('type')) !== -1 
								? $pitem.filter(':checked').val() : $pitem.val(); 
						if($pitem.attr('type') && (['radio', 'checkbox'].indexOf($pitem.attr('type')) !== -1)){
							pvalue = $pitem.filter(':checked').length ? pvalue : '';
						}
						if(((operator === 'in') && (attr.value.indexOf(pvalue) === -1))
							|| ((operator === 'not in') && (attr.value.indexOf(pvalue) !== -1))){
							pass = false;
							return false;
						}
					});
					pass ? $item.show() : $item.hide();
				}
			});
		}	
		/**
		* Initialize tinymce eidtor using the featured area custom content editor params
		* @param string attribute id of element <textarea>
		* @param jQuery object
		*/
		function initTinyMCE(id, $container){
			var control = $container, changeDebounceDelay = 1000, textarea, triggerChangeIfDirty, needsTextareaChangeTrigger = false, previousValue;
			textarea = control.find('.editor-textarea-wrap textarea');
			previousValue = textarea.val();
			triggerChangeIfDirty = function(){
				var updateWidgetBuffer = 300; // See wp.customize.Widgets.WidgetControl._setupUpdateUI() which uses 250ms for updateWidgetDebounced.
				if(control.editor.isDirty()){
					if(wp.customize && wp.customize.state){
						wp.customize.state('processing').set( wp.customize.state('processing').get() + 1);
						_.delay(function(){
							wp.customize.state('processing').set(wp.customize.state('processing').get() - 1);
						}, updateWidgetBuffer);
					}
					textarea.val(wp.editor.getContent(id));
				}
				// Trigger change on textarea when it has changed so the widget can enter a dirty state.
				if(needsTextareaChangeTrigger && previousValue !== textarea.val()){ 
					textarea.trigger('change');
					needsTextareaChangeTrigger = false;
					previousValue = textarea.val();
				}
			};
			function buildEditor(){
				var editor, onInit, mceSettings, qtSettings, tmpl,
					tmplEditorID 	= 'fallsky-widget-editor-id', 
					in_mceInit 		= tinyMCEPreInit && tinyMCEPreInit.mceInit 	&& (tmplEditorID in tinyMCEPreInit.mceInit) && window.tinymce,
					in_qtInit 		= tinyMCEPreInit && tinyMCEPreInit.qtInit 	&& (tmplEditorID in tinyMCEPreInit.qtInit) 	&& quicktags;
				if(!in_mceInit || !in_qtInit){
					return;
				}

				// Abort building if the textarea is gone, likely due to the widget having been deleted entirely.
				if(!textarea.length){
					return;
				}

				// Destroy any existing editor so that it can be re-initialized after a widget-updated event.
				if(tinymce.get(id)){
					var mceInstance 	= window.tinymce.get(id),
						qtInstance 		= window.QTags.getInstance(id),
						$editor_wrap 	= $container.find('#wp-' + id + '-wrap');

					textarea.val(wp.editor.getContent(id));
					if(mceInstance){
						mceInstance.remove(); 
					}
					if(qtInstance){
						qtInstance.remove();
					}
					if($editor_wrap.length){
						$editor_wrap.remove();
					}
				}

				if(!editorParams){
					editorParams = $.extend({}, {'mce': tinyMCEPreInit.mceInit[tmplEditorID], 'qt': tinyMCEPreInit.qtInit[tmplEditorID]});
				}
				// Start to initialize the editor settings and enable editors
				mceSettings = $.extend({}, editorParams.mce, {'selector': ('#' + id), 'body_class': editorParams.mce.body_class.replace(tmplEditorID, id)}),
				qtSettings 	= $.extend({}, editorParams.qt,	{'id': id});

				tmpl = $('#tmpl-widget-editor-field').html();
				tmpl = $(tmpl.replace(/\[\[fallsky-widget-editor-id\]\]/g, id));
				tmpl.find('textarea').attr('id', id).val(textarea.val());
				$container.append(tmpl);
				window.tinymce.init(mceSettings);
				quicktags(qtSettings);
				//window.wpActiveEditor = id;

				editor = window.tinymce.get(id);
				if(!editor){
					return;
				}
				onInit = function(){
					// When a widget is moved in the DOM the dynamically-created TinyMCE iframe will be destroyed and has to be re-built.
					$(editor.getWin()).on('unload', function(){
						_.defer(buildEditor);
					});
				};

				if(editor.initialized){
					onInit();
				} 
				else{
					editor.on('init', onInit);
				}

				control.editorFocused = false;
				tmpl.find('textarea').on('keyup change blur', function(){
					needsTextareaChangeTrigger = true;
					editor.setDirty(true); // Because pasting doesn't currently set the dirty state.
					triggerChangeIfDirty();
				});
				editor.on('focus', function(){
					control.editorFocused = true;
				});
				editor.on('paste', function(){
					editor.setDirty(true); // Because pasting doesn't currently set the dirty state.
					triggerChangeIfDirty();
				});
				editor.on('NodeChange', function(){
					needsTextareaChangeTrigger = true;
				});
				editor.on('NodeChange', _.debounce(triggerChangeIfDirty, changeDebounceDelay));
				editor.on('blur hide', function onEditorBlur(){
					control.editorFocused = false;
					triggerChangeIfDirty();
				});
				control.editor = editor;
			}
			buildEditor();
		}
		/**
		* Initialize color picker
		* @param jQuery object need to enable color picker
		*/
		function initColorPicker($picker){
			$picker.each(function(){
				var $color_picker = $(this);
				$color_picker.wpColorPicker({
					change: function(event, ui){
						var color = ui.color ? ui.color.toString() : '';
						$color_picker.val(color).trigger('change');
					},
					clear: function(){
						$color_picker.val(''); 
						$(this).trigger('change');
					}
				});
			});
		}

		$(document)
			.on('widget-added', widgetAdded)
			.on('widget-synced widget-updated', widgetUpdated)
			.ready(function(){
				if('widgets' !== window.pagenow){
					return;
				}

				var widgetContainers = $('.widgets-holder-wrap:not(#available-widgets)').find('div.widget');
				widgetContainers.one('click.toggle-widget-expanded', function(){
					widgetAdded(new jQuery.Event('widget-added'), $(this));
				});
			});
	})();
})(jQuery);