/**
* Copyright (c) Loft.Ocean
* http://www.loftocean.com
*/

( function( api, $ ) {
	"use strict";

	/** Add theme special class to control wrap */
	$('#customize-theme-controls').addClass('fallsky-customizer-wrapper');
	api.fallsky = api.fallsky || {};
	api.fallsky.controls = api.fallsky.controls || {};
	api.fallsky.homepage_widgets = api.fallsky.homepage_widgets || {};
	api.fallsky.homepage_area_settings = fallsky_customize.homepage_area_settings || false;

	var site_layout_id = 'fallsky_site_layout';

	/**
	* Helper function to test object is empty
	* @param object
	* @return boolean return true if the passed object is emtpy, otherwise false
	*/
	function isEmpty(obj){
		for(var key in obj) {
			if(obj.hasOwnProperty(key)){
				return false;
			}
		}
		return true;
	}
	/**
	* Get the customize control's first setting name
	* @param object customize control
	* @return mix customize setting id string if exists, otherwise boolean false 
	*/
	function getControlSettingId(control){
		var control_settings = control.settings,
			keys = Object.keys(control_settings),
			first_key = ('default' in control_settings)  ? 'default' : 
				(keys.length ? keys[0] : false);
		return first_key ? control_settings[first_key] : false;
	}
	/**
	* Adjust child controls
	* 	1. Generate the dependency object for wp.customize.setting.controls
	*	2. Get child controls and append to its parent
	*/
	function adjustControls(){
		var settings = api.settings.settings, dependency = {},
			controls = $.extend({}, api.settings.controls, api.fallsky.controls);
		$.each(controls, function(id, control){
			var setting = getControlSettingId(control);
			if(setting && settings[setting] && settings[setting].dependency){
				$.each(settings[setting].dependency, function(pid, dep){
					var element = {'control': (api.control(id) || control), 'dependency': settings[setting].dependency};
					if(pid in dependency){
						dependency[pid].push(element);
					}
					else{
						dependency[pid] = [element];
						api(pid).bind(function(to){
							api.trigger('fallsky.change', pid);
						});
					}
				});
			}
		});
		api.fallsky.dependency = dependency;
	} 
	/**
	* Get customize setting value by id
	* @param string setting id 
	* @return string setting value
	*/
	function getSettingValue(id){
		if(id in api.settings.settings){
			var settings 	= api.get(),
				setting 	= settings[id];
			return (setting === true) ? 'on' : setting;
		}
	}
	/**
	 * @param {String} widgetId
	 * @returns {Object}
	 */
	function parseWidgetId( widgetId ) {
		var matches, parsed = {
			number: null,
			id_base: null
		};

		matches = widgetId.match( /^(.+)-(\d+)$/ );
		if ( matches ) {
			parsed.id_base = matches[1];
			parsed.number = parseInt( matches[2], 10 );
		} else {
			// likely an old single widget
			parsed.id_base = widgetId;
		}

		return parsed;
	}
	/**
	 * @param {String} widgetId
	 * @returns {String} settingId
	 */
	function widgetIdToSettingId( widgetId ) {
		var parsed = parseWidgetId( widgetId ), settingId;

		settingId = 'widget_' + parsed.id_base;
		if ( parsed.number ) {
			settingId += '[' + parsed.number + ']';
		}

		return settingId;
	}

	/**
	* To deal with the event of setting changed
	*	This will decide to display the controls related or not
	*/
	api.bind('fallsky.change', function(id){
		if(id in api.fallsky.dependency){ // If current setting id is in the dependency list
			$.each(api.fallsky.dependency[id], function(index, item){ 
				var $control = item.control.container, pass = true;
				$.each(item.dependency, function(pid, attr){ // Check if all dependency are passed
					var operator = attr.operator || 'in', value = getSettingValue(pid);

					if(((operator === 'in') && (attr.value.indexOf(value) === -1))
						|| ((operator === 'not in') && (attr.value.indexOf(value) !== -1))){
						pass = false;
						return false;
					}
				});
				// Show control if passed
				pass ? $control.show() : $control.hide();
			});
		}
	});

	/** 
	* Add new control constructor for slider type control
	*	which will enable jQuery ui to control 
	**/
	api.controlConstructor.number_slider = api.Control.extend({
		ready: function(){
			var elem = this.container.find('.loader-ui-slider'),
				input = this.container.find('input[data-customize-setting-link]');
			elem.slider({
				'range': 	'min',
				'min': 		elem.data('min'),
				'max': 		elem.data('max'),
				'value': 	elem.data('value'),
				'step': 	elem.data('step'),
				'slide': 	function(event, ui){
					input.val(ui.value).trigger('change');
				}
			});
		}
	});

	/**
	* Register event handlers for customize control type number with unit
	*	To determine if current value excced the range set
	*/
	api.controlConstructor.number = api.controlConstructor.number_with_unit = api.Control.extend({
		ready: function(){
			this.container.find('input[type=number]').on('change', function(e){
				var $self 	= $(this),
					val 	= parseInt($self.val()),
					min		= $self.attr('min') ? parseInt($self.attr('min')) : 1,
					max 	= $self.attr('max') ? parseInt($self.attr('max')) : false;
				(!val || (val < min)) ? $self.val(min).trigger('change')
					: (((false !== max) && (val > max)) ? $self.val(max).trigger('change') : '');
			});
		}
	});

	/**
	* Register event handlers for customize control type image_id
	*	To open media lib, remove current image and features after image chosed
	*/
	api.controlConstructor.image_id = api.Control.extend({
		ready: function(){
			var $container = $(this.container);
			$container.on('click', '.fallsky-customize-upload-image, .attachment-thumb', function(e){
				e.preventDefault();
				fallsky_media.open($(this).parent().siblings('input[type=hidden]').first());
			})
			.on('click', '.fallsky-customize-remove-image', function(e){
				e.preventDefault();
				var $action = $(this).parent();

				$(this).css('display', 'none');
				$action.siblings('input[type=hidden]').first().val('').trigger('change');
				$action.siblings('.placeholder').removeAttr('style');
				$action.siblings('.thumbnail-image').remove();
				$action.parent().removeClass('attachment-media-view-image');
			})
			.on('fallsky.media.changed', 'input[type=hidden]', function(e, image){
				e.preventDefault();
				if(image && ('image' == image.type)){
					var $container = $(this).closest('.attachment-media-view').addClass('attachment-media-view-image'),
						url = image.sizes ? (image.sizes.medium ? image.sizes.medium.url : (image.sizes.thumbnail ? image.sizes.thumbnail.url : image.url)) : image.url,
						$image = $('<div>', {'class': "thumbnail thumbnail-image"}).append($('<img>', {'class': "attachment-thumb", 'src': url}));

					$container.children('.thumbnail-image').remove();
					$container.children('.placeholder').css('display', 'none').after($image);
					$container.find('.fallsky-customize-remove-image').removeAttr('style');
					$(this).val(image.id).trigger('change');
				}
			});
		}
	});

	/**
	* Register event hanlder for customize control type multiple_selection
	*	If the drop donw has option to select all and more than one options selected,
	*		remove the selection of all option.
	*/
	api.controlConstructor.multiple_selection = api.Control.extend({
		ready: function(){
			var $select = $(this.container).find('select[multiple]');
			if($select.length && $select.children('[value=""]').length){
				$select.on('change', function(e){
					var $options = $(this).children();

					($options.filter(':selected').length > 1) 
						? $options.filter('[value=""]').removeAttr('selected') : '';
				});
				// Check current value after page initialized
				$select.trigger('change'); 
			}
		}
	});

	/** 
	* Add new control constructor for mce_editor type control
	*	which will enable tinymce on it 
	**/
	api.controlConstructor.mce_editor = api.Control.extend({
		ready: function(){
			var control 	= this,
				id 			= control.id,
				in_ids 		= fallsky_customize && fallsky_customize.editor_ids && (fallsky_customize.editor_ids.indexOf(id) != -1),
				in_mceInit 	= tinyMCEPreInit && tinyMCEPreInit.mceInit && (id in tinyMCEPreInit.mceInit) && window.tinymce,
				in_qtInit 	= tinyMCEPreInit && tinyMCEPreInit.qtInit && (id in tinyMCEPreInit.qtInit) && quicktags;
			if(in_ids && in_mceInit && in_qtInit){
				var $container = $(control.container);
				fallskyInitEditor(id, {'mce': tinyMCEPreInit.mceInit[id], 'qt': tinyMCEPreInit.qtInit[id]}, $container);
				window.wpActiveEditor = id;
			}
		}
	});

	/**
	* Add new control constructor for button type control
	*/
	api.controlConstructor.button = api.Control.extend({
		ready: function(e){
			api.Control.prototype.ready.apply(this, arguments);
			var message = fallsky_customize.sync_message || {
					'sending'	: 'Data is syncing. Please wait. It can take a couple of minutes.',
					'done'		: 'Congratulations! Sync is completed.', 
					'fail'		: 'Sorry but unable to sync. Please try again later.'
				},
				$container = $(this.container),
				$notification = $container.find('.customize-control-notifications-container');
			$container.find('input[type=button]').on('click', function(e){
				e.preventDefault();
				var $self = $(this);
				$notification.css('display', 'none');
				if($self.attr('action') && $self.attr('nonce')){
					$self.attr('disabled', 'disabled');
					$notification.css('display', '').children().html('<li>' + message.sending + '</li>');
					wp.ajax.post($self.attr('action'), {'nonce': $self.attr('nonce')})
						.done(function(response){
							$notification.css('display', '').children().html('<li>' + message.done + '</li>');
						})
						.fail(function(response){
							$notification.css('display', '').children().html('<li>' + message.fail + '</li>');
						})
						.always(function(){ $self.removeAttr('disabled'); });
				}
			});
		}
	});

	/**
	* Add new control type group
	*	1. add child controls when ready
	*/
	api.controlConstructor.group = api.Control.extend({
		ready: function(){
			var control = this;
			if(control.params.children){
				var $wrap = control.container.find('ul.group-controls-wrap');
				$.each(control.params.children, function(cid, param){
					var Constructor = api.controlConstructor[param.type] || api.Control,
						Modified_Constructor = Constructor.extend({
							embed: function(){ 
								var control = this, inject;
								inject = function(sectionId){
									var parentContainer;
									api.section(sectionId, function(section){
										// Wait for the section to be ready/initialized
										section.deferred.embedded.done(function(){
											$wrap.append(control.container);
											control.renderContent();
											control.deferred.embedded.resolve();
										});
									});
								};
								control.section.bind(inject);
								inject(control.section.get());
							}
						}),
						options = _.extend({'params': param}, param),
						sub_controls = new Modified_Constructor(cid, options);
					api.fallsky.controls[cid] = $.extend({'container': sub_controls.container}, param);
				});
			}
		}
	});

	api.controlConstructor.homepage_widget = api.controlConstructor.widget_form.extend({
		embed: function(){ 
			var control = this, inject;
			inject = function(sectionId){
				var parentContainer;
				if(!sectionId){
					return;
				}
				// Wait for the section to be registered
				api.section(sectionId, function(section){
					// Wait for the section to be ready/initialized
					section.deferred.embedded.done(function(){
						control.params.homepage_area.$controlSection.append(control.container);
						control.renderContent();
						$(control.container).addClass('widget-rendered');
						control.deferred.embedded.resolve();
					});
				});
			};
			control.section.bind( inject );
			inject(control.section.get());
		},
		getSidebarWidgetsControl: function() {
			return this.params.homepage_area;
		},	
		_setupRemoveUI: function() {
			var control = this, replaceDeleteWithRemove,
				$removeBtn = control.container.find('.widget-control-remove');

			// Configure remove button
			$removeBtn.on('click', function(e){
				e.preventDefault();
				// Find an adjacent element to add focus to when this widget goes away
				var $adjacentFocusTarget;
				if(control.container.next().is('.customize-control-widget_form')){
					$adjacentFocusTarget = control.container.next().find('.widget-action:first');
				} 
				else if(control.container.prev().is('.customize-control-widget_form')){
					$adjacentFocusTarget = control.container.prev().find( '.widget-action:first' );
				} 
				else {
					$adjacentFocusTarget = control.container.next('.customize-control-sidebar_widgets').find('.add-new-widget:first');
				}
				control.container.slideUp( function() {
					var sidebarsWidgetsControl = control.params.homepage_area, sidebarWidgetIds, i;

					if(!sidebarsWidgetsControl){
						return;
					}

					sidebarWidgetIds = sidebarsWidgetsControl.setting().slice();
					i = _.indexOf(sidebarWidgetIds, control.params.widget_id);
					if(-1 === i){
						return;
					}

					sidebarWidgetIds.splice(i, 1);
					sidebarsWidgetsControl.setting(sidebarWidgetIds);

					$adjacentFocusTarget.focus(); // keyboard accessibility
				} );
			});
			replaceDeleteWithRemove = function() {
				$removeBtn.text(api.Widgets.data.l10n.removeBtnLabel); // wp_widget_control() outputs the button as "Delete"
				$removeBtn.attr('title', api.Widgets.data.l10n.removeBtnTooltip);
			};
			this.params.is_new ? api.bind('saved', replaceDeleteWithRemove) : replaceDeleteWithRemove();
		},
		onChangeExpanded: function(expanded, args){
			var self = this, $widget, $inside, complete, prevComplete, expandControl, $toggleBtn;

			self.embedWidgetControl(); // Make sure the outer form is embedded so that the expanded state can be set in the UI.
			if(expanded){
				self.embedWidgetContent();
			}

			// If the expanded state is unchanged only manipulate container expanded states
			if(args.unchanged){
				if(expanded){
					api.Control.prototype.expand.call(self, {
						completeCallback:  args.completeCallback
					});
				}
				return;
			}

			$widget = this.container.find( 'div.widget:first' );
			$inside = $widget.find( '.widget-inside:first' );
			$toggleBtn = this.container.find( '.widget-top button.widget-action' );

			expandControl = function(){
				// Close all other widget controls before expanding this one
				_.each(api.fallsky.homepage_widgets, function(otherControl){
					if(otherControl && (self.params.type === otherControl.params.type) && (self !== otherControl)){
						otherControl.collapse();
					}
				});

				complete = function(){
					self.container.removeClass('expanding');
					self.container.addClass('expanded');
					$widget.addClass('open');
					$toggleBtn.attr('aria-expanded', 'true');
					self.container.trigger('expanded');
				};
				if(args.completeCallback){
					prevComplete = complete;
					complete = function(){
						prevComplete();
						args.completeCallback();
					};
				}

				if(self.params.is_wide){
					$inside.fadeIn(args.duration, complete);
				} 
				else{
					$inside.slideDown(args.duration, complete);
				}

				self.container.trigger('expand');
				self.container.addClass('expanding');
			};

			if(expanded){
				if(api.section.has(self.section())){
					api.section(self.section()).expand({
						completeCallback: expandControl
					});
				} 
				else{
					expandControl();
				}
			} 
			else{
				complete = function(){
					self.container.removeClass('collapsing');
					self.container.removeClass('expanded');
					$widget.removeClass('open');
					$toggleBtn.attr('aria-expanded', 'false');
					self.container.trigger('collapsed');
				};
				if(args.completeCallback){
					prevComplete = complete;
					complete = function(){
						prevComplete();
						args.completeCallback();
					};
				}

				self.container.trigger('collapse');
				self.container.addClass('collapsing');

				if(self.params.is_wide){
					$inside.fadeOut(args.duration, complete);
				} 
				else {
					$inside.slideUp(args.duration, function(){
						$widget.css({width:'', margin:''});
						complete();
					});
				}
			}
		}
	});

	api.controlConstructor.homepage_area = api.Widgets.SidebarControl.extend({
		isReordering: false,
		ready: function() {
			this.$controlSection = this.container.find('.fallsky-homepage-area-wrap');
			this.$sectionContent = this.container.find('.fallsky-homepage-area-wrap');

			this._addWidgets();
			this._setupModel();
			this._setupSortable();
			this._setupAddition();
			this._applyCardinalOrderClassNames();
		},
		_addWidgets: function(){
			var self = this, widgetControl;
			if(api.fallsky.homepage_area_settings && api.fallsky.homepage_area_settings[self.id]){
				_.each(api.fallsky.homepage_area_settings[self.id], function(params, widgetId){ 
					widgetControl = new api.controlConstructor.homepage_widget(widgetId, {
						'params': $.extend({}, {'homepage_area': self}, params)
					});
					api.fallsky.homepage_widgets[params.widget_id] = widgetControl;
				});
			}
		},
		_setupModel: function() {
			var self = this;

			this.setting.bind(function(newWidgetIds, oldWidgetIds){
				var widgetFormControls, removedWidgetIds, priority;

				removedWidgetIds = _(oldWidgetIds ).difference( newWidgetIds);

				// Filter out any persistent widget IDs for widgets which have been deactivated
				newWidgetIds = _(newWidgetIds).filter(function(newWidgetId){
					var parsedWidgetId = parseWidgetId(newWidgetId);
					return !!api.Widgets.availableWidgets.findWhere({id_base: parsedWidgetId.id_base});
				} );

				widgetFormControls = _( newWidgetIds ).map( function( widgetId ) {
					var widgetFormControl = api.fallsky.homepage_widgets[widgetId];
					if(!widgetFormControl){
						widgetFormControl = self.addWidget( widgetId );
					}
					return widgetFormControl;
				} );

				// Sort widget controls to their new positions
				widgetFormControls.sort( function( a, b ) {
					var aIndex = _.indexOf( newWidgetIds, a.params.widget_id ),
						bIndex = _.indexOf( newWidgetIds, b.params.widget_id );
					return aIndex - bIndex;
				});

				priority = 0;
				_( widgetFormControls ).each( function ( control ) {
					control.priority( priority );
					control.section( self.section() );
					priority += 1;
				});
				self.priority( priority ); // Make sure sidebar control remains at end

				// Re-sort widget form controls (including widgets form other sidebars newly moved here)
				self._applyCardinalOrderClassNames();

				// If the widget was dragged into the sidebar, make sure the sidebar_id param is updated
				_( widgetFormControls ).each( function( widgetFormControl ) {
					widgetFormControl.params.sidebar_id = self.params.sidebar_id;
				} );

				// Cleanup after widget removal
				_( removedWidgetIds ).each( function( removedWidgetId ) {

					// Using setTimeout so that when moving a widget to another sidebar, the other sidebars_widgets settings get a chance to update
					setTimeout( function() {
						var removedControl, wasDraggedToAnotherSidebar, inactiveWidgets, removedIdBase,
							widget, isPresentInAnotherSidebar = false;

						// Check if the widget is in another sidebar
						api.each( function( otherSetting ) {
							if(otherSetting.id === self.setting.id || 0 !== otherSetting.id.indexOf('fallsky_homepage_main_area')){
								return;
							}

							var otherSidebarWidgets = otherSetting(), i;

							i = _.indexOf( otherSidebarWidgets, removedWidgetId );
							if ( -1 !== i ) {
								isPresentInAnotherSidebar = true;
							}
						} );

						// If the widget is present in another sidebar, abort!
						if ( isPresentInAnotherSidebar ) {
							return;
						}

						removedControl = api.fallsky.homepage_widgets[removedWidgetId];

						// Detect if widget control was dragged to another sidebar
						wasDraggedToAnotherSidebar = removedControl && $.contains( document, removedControl.container[0] ) && ! $.contains( self.$sectionContent[0], removedControl.container[0] );

						// Delete any widget form controls for removed widgets
						if ( removedControl && ! wasDraggedToAnotherSidebar ) {
							api.fallsky.homepage_widgets[removedControl.id] = false;
							removedControl.container.remove();
						}

						// Make old single widget available for adding again
						removedIdBase = parseWidgetId( removedWidgetId ).id_base;
						widget = api.Widgets.availableWidgets.findWhere( { id_base: removedIdBase } );
						if ( widget && ! widget.get( 'is_multi' ) ) {
							widget.set( 'is_disabled', false );
						}
					});
				});
			});
		},
		_applyCardinalOrderClassNames: function() {
			var widgetControls = [];
			_.each(this.setting(), function(widgetId){
				var widgetControl = api.fallsky.homepage_widgets[widgetId];
				if(widgetControl){
					widgetControls.push(widgetControl);
				}
			});
			this.container.find( '.reorder-toggle' ).hide();

			if(widgetControls && widgetControls.length){
				$(widgetControls).each(function(){
					$(this.container).removeClass('first-widget last-widget')
						.find('.move-widget-down, .move-widget-up').prop( 'tabIndex', 0 );
				});
				_.first(widgetControls).container.addClass('first-widget')
					.find('.move-widget-up').prop('tabIndex', -1);

				_.last(widgetControls).container.addClass( 'last-widget')
					.find('.move-widget-down').prop('tabIndex', -1);
			}
		},
		_setupSortable: function() {
			var self = this;
			this.isReordering = false;
			/**
			 * Update widget order setting when controls are re-ordered
			 */
			this.$sectionContent.sortable( {
				items: '> .customize-control-widget_form',
				handle: '.widget-top',
				axis: 'y',
				tolerance: 'pointer',
				update: function() {
					var widgetContainerIds = self.$sectionContent.sortable( 'toArray' ), widgetIds;
					widgetIds = $.map(widgetContainerIds, function(widgetContainerId){
						return $('#' + widgetContainerId).find(':input[name=widget-id]').val();
					});
					self.setting(widgetIds);
				}
			});
		},
		getWidgetFormControls: function(){
			var formControls = [];
			_(this.setting()).each(function(widgetId){
				var formControl = api.fallsky.homepage_widgets[widgetId];
				if(formControl){
					formControls.push(formControl);
				}
			});
			return formControls;
		},
		addWidget: function(widgetId){
			var self = this, controlHtml, $widget, controlType = 'widget_form', controlContainer,
				settingId, isExistingWidget, widgetFormControl, sidebarWidgets, settingArgs, setting,
				parsedWidgetId 	= parseWidgetId(widgetId),
				widgetNumber 	= parsedWidgetId.number,
				widgetIdBase 	= parsedWidgetId.id_base,
				widget 			= api.Widgets.availableWidgets.findWhere({id_base: widgetIdBase});

			if(!widget){
				return false;
			}
			if(widgetNumber && !widget.get('is_multi')){
				return false;
			}

			// Set up new multi widget
			if ( widget.get( 'is_multi' ) && ! widgetNumber ) {
				widget.set( 'multi_number', widget.get( 'multi_number' ) + 1 );
				widgetNumber = widget.get( 'multi_number' );
			}

			controlHtml = $.trim( $( '#widget-tpl-' + widget.get( 'id' ) ).html() );
			if(widget.get('is_multi')){
				controlHtml = controlHtml.replace(/<[^<>]+>/g, function(m){
					return m.replace(/__i__|%i%/g, widgetNumber);
				});
			} 
			else{
				widget.set('is_disabled', true); // Prevent single widget from being added again now
			}
			$widget = $(controlHtml);
			controlContainer = $('<li/>', {'class': 'customize-control customize-control-' + controlType, 'html': controlHtml});

			// Remove icon which is visible inside the panel
			controlContainer.find( '> .widget-icon' ).remove();

			if ( widget.get( 'is_multi' ) ) {
				controlContainer.find( 'input[name="widget_number"]' ).val( widgetNumber );
				controlContainer.find( 'input[name="multi_number"]' ).val( widgetNumber );
			}

			widgetId = controlContainer.find( '[name="widget-id"]' ).val();

			controlContainer.hide(); // to be slid-down below

			settingId = 'widget_' + widget.get( 'id_base' );
			if ( widget.get( 'is_multi' ) ) {
				settingId += '[' + widgetNumber + ']';
			}
			controlContainer.attr( 'id', 'customize-control-' + settingId.replace( /\]/g, '' ).replace( /\[/g, '-' ) );

			// Only create setting if it doesn't already exist (if we're adding a pre-existing inactive widget)
			isExistingWidget = api.has( settingId );
			if ( ! isExistingWidget ) { 
				settingArgs = {
					transport: api.Widgets.data.selectiveRefreshableWidgets[ widget.get( 'id_base' ) ] ? 'postMessage' : 'refresh',
					previewer: this.setting.previewer
				};
				setting = api.create(settingId, settingId, '', settingArgs);
				setting.set({}); // mark dirty, changing from '' to {}
			}

			widgetFormControl = new api.controlConstructor.homepage_widget(settingId, {
				settings: 		{ 'default': settingId },
				content: 		controlContainer,
				sidebar_id: 	self.params.sidebar_id,
				widget_id: 		widgetId,
				widget_id_base: widget.get( 'id_base' ),
				type: 			controlType,
				is_new: 		!isExistingWidget,
				width: 			widget.get('width'),
				height: 		widget.get('height'),
				is_wide: 		widget.get('is_wide'),
				homepage_area: 	self
			});
			api.fallsky.homepage_widgets[widgetId] = widgetFormControl;

			// Make sure widget is removed from the other homepage area
			api.each(function(otherSetting){
				if(otherSetting.id === self.setting.id){
					return;
				}
				if(0 !== otherSetting.id.indexOf('fallsky_homepage_main_area')){
					return;
				}

				var otherSidebarWidgets = otherSetting().slice(),
					i = _.indexOf(otherSidebarWidgets, widgetId);
				if(-1 !== i){
					otherSidebarWidgets.splice(i);
					otherSetting(otherSidebarWidgets);
				}
			});

			// Add widget to this homepage area
			sidebarWidgets = this.setting().slice();
			if(-1 === _.indexOf(sidebarWidgets, widgetId)){
				sidebarWidgets.push(widgetId);
				this.setting(sidebarWidgets);
			}

			controlContainer.slideDown(function(){
				if(isExistingWidget){
					widgetFormControl.updateWidget({
						instance: widgetFormControl.setting()
					});
				}
			});
			return widgetFormControl;
		}
	});

	/**
	* For site layout
	*/
	api.fallsky.site_layout = api.Control.extend({
		initialize: function(id, options){
			var control = this, settings;

			control.params = {};
			$.extend(control, options || {});
			control.id = id;

			settings = $.map(control.params.settings, function(value){
				return value;
			});

			if(settings.length){
				api.apply( api, settings.concat(function(){
					var key;
					control.settings = {};
					for(key in control.params.settings){
						control.settings[key] = api(control.params.settings[key]);
					}
					control.setting = control.settings['default'] || null;
				}) );
			}
			else{
				control.setting = null;
				control.settings = {};
			}
			control.ready();
		},
		ready: function(){
			var control = this;
			if(control.setting){
				control.setting.bind(function(value){
					control.settingChanged(value, control);
				});
				control.settingChanged(control.setting(), control);
				api.trigger('fallsky.change', control.id);
			}
		},
		settingChanged: function(value, control){
			if(value in fallsky_customize){
				$.each(fallsky_customize[value], function(id, title){
					control.updateControlTitle(id, title);
				});
			}
		},
		updateControlTitle: function(id, title){
			var c = api.control(id), $container = c ? $(c.container) : false;
			if($container && c.params.type){
				switch(c.params.type){
					case 'title_only':
						$container.find('h3').text(title);
						break;
					case 'checkbox':
						var $label = $container.children('label');
						$label.length ? $label.text(title) : '';
						break;
					default:
						var $title = $container.find('.customize-control-title');
						$title.length ? $title.text(title) : '';
				}
			}
		}
	});

	/**
	* Register event handlers after wp.customize ready
	*/
	api.bind( 'ready', function( e ) { 
		var $widgets_available 	= $('#available-widgets-list'), 
			$homepage_widgets 	= $('div[data-widget-id^="fallsky-homepage-widget"]'),
			$reorder_btn 		= $('.customize-control-sidebar_widgets .button-link.reorder-toggle');
		$('#customize-control-header_image .customizer-section-intro').html(fallsky_customize.header_description);
		$('#customize-control-header_image .current .customize-control-title').html(fallsky_customize.header_label);
		adjustControls();
		if(site_layout_id in api.settings.controls){
			new api.fallsky.site_layout(site_layout_id, {
				params: api.settings.controls[site_layout_id]
			});
		}
		if($homepage_widgets.length && $reorder_btn.length){
			$reorder_btn.remove();
		}

		$('#customize-theme-controls')
			.on('click', '.customize-control-sidebar_widgets .button.add-new-widget, .customize-control.customize-control-homepage_area .button.add-new-widget', function(e){
				if($widgets_available.length && $homepage_widgets.length){
					$(this).closest('.control-section-sidebar').length
						? $widgets_available.children().not($homepage_widgets.addClass('hide')).removeClass('hide')
							: $widgets_available.children().not($homepage_widgets.removeClass('hide')).addClass('hide');
				}
			});


		api('show_on_front', 'page_on_front', 'page_for_posts', 'fallsky_category_index_page_id', function(showOnFront, pageOnFront, pageForPosts, pageForCategory){
			var handleChange = function(){ 
				var setting = this, pageOnFrontId, pageForPostsId, pageForCategoryId, errorCode = 'category_index_page_collision';
				pageOnFrontId 		= parseInt(pageOnFront(), 10);
				pageForPostsId 		= parseInt(pageForPosts(), 10);
				pageForCategoryId 	= parseInt(pageForCategory(), 10);

				// Toggle notification when the category index page and homepage/posts page are all set and the same.
				var pages = [pageOnFrontId, pageForPostsId]; 
				if('page' === showOnFront() && pageForCategoryId && (pageOnFrontId || pageForPostsId) && (-1 != pages.indexOf(pageForCategoryId))){
					pageForCategory.notifications.add(new api.Notification(errorCode, {
						type: 'error',
						message: fallsky_customize.category_index_error_message
					}));
				} 
				else{
					pageForCategory.notifications.remove(errorCode);				
					if(setting === pageForCategory && pageForCategoryId > 0){
						api.previewer.previewUrl.set(api.settings.url.home + '?page_id=' + pageForCategoryId);
					}
				}
			};
			showOnFront.bind(handleChange);
			pageOnFront.bind(handleChange);
			pageForPosts.bind(handleChange);
			pageForCategory.bind(handleChange);
			handleChange.call(showOnFront); //, showOnFront()); // Make sure initial notification is added after loading existing changeset.
		});

		$( 'body' ).on( 'click', 'a.show-control', function( e ) {
			e.preventDefault();
			var targetID = $(this).data('control-id');
			if ( targetID ) {
				api.previewer.trigger( 'focus-control-for-setting', targetID );
			}
		} )
		.on( 'click', 'a.show-panel, a.show-section', function( e ) {
			e.preventDefault();
			var targetID = $( this ).data( 'section-id' );
			if ( targetID && $( '#' + targetID ).length ) {
				$('#' + targetID).find('.accordion-section-title').trigger('click');
			}
		} )
		.on( 'click', 'a.redirect-preview-url', function( e ) {
			e.preventDefault();
			var param = $( this ).attr( 'href' );
			if ( $( this ).hasClass( 'static-home' ) ) {
				var home_id = api.get().page_for_posts ? api.get().page_for_posts : false;
				param = home_id ? '?page_id=' + home_id : '';
			}
			if ( param && ( param != '#' ) ) {
				api.previewer.previewUrl.set( api.settings.url.home + param );
			}
		} )
		.on( 'click', '#customize-control-fallsky_instagram_clear_cache input[type=button]', function( e ){
			e.preventDefault();
			if ( wpApiSettings && wpApiSettings.root ) {
				var $self = $( this );
				$self.val( fallsky_customize['clear-instagram-cache']['sending'] ).attr( 'disabled', 'disabled' );
				var cache = {}, url = wpApiSettings.root + 'loftocean/v1/clear-instagram-cache/';
				$.get( url )
					.done( function(){
						$self.val( fallsky_customize[ 'clear-instagram-cache' ][ 'done' ] ).removeAttr( 'disabled' );
					});
			}
		} );
	} );
} ) ( wp.customize, jQuery );