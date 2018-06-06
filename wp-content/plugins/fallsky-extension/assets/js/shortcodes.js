(function($){
	"use strict";
	// For shortcode tabs
	var $tabs = $('.lo-tabs');
	if($tabs.length){
		$tabs.on('click', '.lo-tabs-titles > a', function(e){
			e.preventDefault();
			if($(this).hasClass('active')) return ;

			var $a = $(this), $wrap = $a.parent(),
				index = $wrap.children().index($a),
				$contents = $wrap.siblings('.lo-tabs-content').children();
			$a.siblings('.active').removeClass('active').end().addClass('active');
			$contents.css('display', 'none').eq(index).css('display', '');
		})
		.each(function(){
			if($(this).find('.lo-tabs-titles > a').length){
				$(this).find('.lo-tabs-titles > a').first().trigger('click');
				$(this).removeAttr('style');
			}
		});
	}

	// For shortcode accordions
	var $accordions = $('.accordions');
	if($accordions.length){
		$accordions.accordion({
			'header': '.accordion-title', 
			'collapsible': true,
			'heightStyle': 'content'
		})
		.each(function(){
			if($(this).children('.accordion-item.open').length){
				$(this).accordion('option', 'active', $(this).children().index($(this).children('.accordion-item.open').first()));
			}
		});
	}
	
})(jQuery);