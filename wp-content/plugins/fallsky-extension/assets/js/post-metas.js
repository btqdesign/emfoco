(function($){
	"use strict";
	function loftocean_update_like($el){
		if($el && $el.length){
			var id = $el.attr('data-post-id'), $like = $('.article-like[data-post-id=' + id + ']');
			if($like && $like.length){
				$like.each(function(){
					var count = $(this).find('.counts').text() || '0', $counts = $(this).children('.counts');
					if((count.indexOf('K') !== -1) || (count.indexOf('M') !== -1)){
						$counts.text(count);
					}
					else if(count == 999){
						$counts.text('1K');
					}
					else{
						count = parseInt(count) + 1;
						$counts.text(count);
					}
					$(this).addClass('liked');
				});
			}
		}
	}
	$(document).ready(function(){
		if($('.article-like').length){
			$('body').on('click', '.article-like', function(e){ 
				var $button = $(this);
				if($button.hasClass('liked')){ return false; }
				if($button.data('loftocean-liked')){ return false; }

				$button.data('loftocean-liked', true);
				var id = $button.attr('data-post-id'), cookie_name = 'loftocean_post_likes_post-' + id;
				if(id && (!LoftOcean_Cookie.get(cookie_name) || ('done' !== LoftOcean_Cookie.get(cookie_name)))){
					var data = { 'action': loftocean_ajax.like.action, 'post_id': id };
					$.post(loftocean_ajax.url, data).done(function(){
						LoftOcean_Cookie.set('loftocean_post_likes_post-' + id, 'done', 30);
						loftocean_update_like($button);
					});
				}
			});
		}
		if($('.side-share-icons > a, .tweet-it, .social-share-icons > a').length){
			$('body').on('click', '.side-share-icons > a, .tweet-it, .social-share-icons > a', function(e){
				e.preventDefault();
				var self = $(this), prop = self.attr('data-props') ? self.attr('data-props') : 'width=555,height=401';
				window.open(self.attr('href'), self.attr('title'), prop);
				return false;
			});
		}
	});

	var LoftOcean_Cookie = {
		set: function (name, value, days) {
	       var expires = "";
	        if(days) {
	           var date = new Date();
	           date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
	           expires = "; expires=" + date.toGMTString();
	       }
	       document.cookie = name + "=" + value + expires + "; path=/";
	   },
	   get: function (name) {
	        var nameEQ = name + "=";
	        var ca = document.cookie.split(";");
	        for (var i = 0; i < ca.length; i++) {
	            var c = ca[i];
	            while (c.charAt(0) == " ") c = c.substring(1, c.length);
	            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
	        }
	        return null;
	    },
	    remove: function (name) {
	        Cookie.create(name, "", -1);
	    }
	};
})(jQuery);