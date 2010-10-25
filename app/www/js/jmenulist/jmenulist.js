/**
 * plugin to show a simple menulist
 * @author laurent jouanneau
 * @licence BSD style licence
 */

;(function($){

	$.fn.jmenulist = function(settings){
		var defaultSettings = {
			showSpeed:        'fast',
			hideSpeed:        'fast'
		}
		settings = $.extend({}, defaultSettings, settings);

		$(this).find('li').mouseenter(function(event){
			if ($(this).hasClass('hover'))
				return;

			var children = $(this).children('ul');
			if (children.length == 0)
				return;

			var parent = children.parent();
			parent.addClass('hover');
			if ($.browser.msie) {
				var pos = parent.position();
				children.css('left', parseInt(pos['left']));
			}
			children.fadeIn (settings.showSpeed);

		}).mouseleave (function(event) {
			var children = $(this).children('ul');
			if (children.length == 0)
				return;
			children.parent().removeClass('hover');
			children.fadeOut (settings.hideSpeed);
		});
	};
})(jQuery);