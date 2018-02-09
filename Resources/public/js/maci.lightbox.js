/*
 * 		Maci Lightbox jQuery plugin
 *		Version for Bootstrap 3.4
 */

var maciLightbox = function (options) {

	var _defaults = {},
		lightbox = $('<div/>', {'class': 'maci-lightbox'}).appendTo('body').hide(),
		bar = $('<div/>', {'class': 'container'}).appendTo(lightbox).wrap($('<div/>', {'class': 'navbar navbar-fixed-top navbar-default'})),
		bar_header = $('<div/>', {'class': 'navbar-header'}).appendTo(bar),
		bar_ul = $('<ul/>', {'class': 'nav navbar-nav navbar-right'}).appendTo(bar),
		container = $('<div/>', {'class': 'maci-lightbox-container'}).appendTo(lightbox),
		fullscreenElement = document.documentElement
	;

	_obj = {

	set: function(a) {
		lightbox.show();
		bar_ul.html(null);
		bar_header.html(null);
		container.html(null);
		// Album Title
		if ($(a).attr('data-title')) {
			$('<span/>', {'class': 'navbar-brand'}).html($(a).attr('data-title')).appendTo(bar_header);
		}
		// Album Index
		if ($(a).attr('data-lightbox')) {
			var list = $('a[data-lightbox=' + $(a).attr('data-lightbox') + ']'),
				str = ($(list).index(a) + 1) + ' / ' + $(list).length
			;
			$('<span/>', {'class': 'navbar-text'}).text(str).appendTo(bar_ul).wrap('<li/>');
		}
		// View [+]
		$('<a/>', {'class': 'maci-lightbox-open-button', 'target': '_blank', 'href': $(a).attr('href')}).html('<span class="glyphicon glyphicon-zoom-in" aria-hidden="true"/>').appendTo(bar_ul).wrap('<li/>');
		// Fullscreen
		if (fullscreenElement.requestFullScreen || fullscreenElement.webkitRequestFullScreen || fullscreenElement.mozRequestFullScreen || fullscreenElement.msRequestFullScreen) {
			var icon_full = $('<span class="icon-full glyphicon glyphicon-resize-full" aria-hidden="true"/>');
			var icon_reduce = $('<span class="icon-reduce glyphicon glyphicon-resize-small" aria-hidden="true"/>');
			var fullscreen_button = $('<a/>', {'class': 'maci-lightbox-fullscreen-button', 'href': ''}).click(function(e) {
				e.preventDefault();
				_obj.toggleFullscreen();
				$(this).toggleClass('on-fullscreen');
			}).append(icon_full).append(icon_reduce).appendTo(bar_ul).wrap('<li/>');
			if (_obj.isInFullscreen(fullscreenElement)) {
				$(fullscreen_button).addClass('on-fullscreen');
			}
		}
		// Close [X]
		$('<a/>', {'class': 'maci-lightbox-close-button', 'href': ''}).click(function(e) {
			e.preventDefault();
			lightbox.hide();
		}).html('<span class="glyphicon glyphicon-remove" aria-hidden="true"/>').appendTo(bar_ul).wrap('<li/>');
		// Image Containers
		var image = $('<img/>', {'class': 'maci-lightbox-image img-responsive'}).appendTo(container);
		var image_wrapper = image.wrap($('<div/>', {'class': 'maci-lightbox-imape-wrapper container'})).parent().hide();
		var slider = image_wrapper.wrap($('<div/>', {'class': 'maci-lightbox-slider'})).parent();
		// Image Infos Container
		var image_info = $('<div/>', {'class': 'maci-lightbox-image-info'});
		// Brand
		if ($(a).attr('data-brand')) {
			$('<img/>', {'src': $(a).attr('data-brand'), 'class': 'maci-lightbox-image-brand-image'}).html($(a).attr('data-description')).appendTo(image_info).wrap($('<div/>', {'class': 'maci-lightbox-image-brand container'}));
		}
		// Description
		if ($(a).attr('data-description')) {
			$('<div/>', {'class': 'maci-lightbox-image-description container'}).html($(a).attr('data-description')).appendTo(image_info);
		}
		if (image_info.children().length) {
			image_info.appendTo(container).hide();
		}
		// Album Controllers
		if ($(a).attr('data-lightbox')) {
			// Prev
			$('<a/>', {'class': 'maci-lightbox-controller carousel-control left', 'href': ''}).click(function(e) {
				e.preventDefault();
				_obj.prev(a);
			}).html('<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"/>').appendTo(slider);
			// Next
			$('<a/>', {'class': 'maci-lightbox-controller carousel-control right', 'href': ''}).click(function(e) {
				e.preventDefault();
				_obj.next(a);
			}).html('<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"/>').appendTo(slider);
		}
		// Loader Icon
		var loader_icon = $('<div/>', {'class': 'maci-lightbox-loader-icon'}).appendTo(lightbox);
		// Load Image !
		image.on('load', function(e) {
			loader_icon.remove();
			$(image_info).show();
			image_wrapper.show();
			if (container.height() < $(window).height()) {
				var diff = container.height() - slider.height();
				slider.height(($(window).height() - diff) + 'px');
				image.css('marginTop', (
					( $(window).height() - image.height() - parseInt(image_wrapper.css('paddingTop')) - parseInt(image_wrapper.css('paddingBottom')) - diff ) / 2 ) + 'px'
				);
			}
		}).attr('src', ($(a).attr('data-image') ? $(a).attr('data-image') : $(a).attr('href')));
	},

	next: function(a) {
		var list = $('a[data-lightbox=' + $(a).attr('data-lightbox') + ']'), index = $(list).index(a);
		if (index == $(list).length - 1) {
			_obj.set($(list).first());
			return;
		}
		_obj.set($(list).eq(index + 1));
	},

	prev: function(a) {
		var list = $('a[data-lightbox=' + $(a).attr('data-lightbox') + ']'), index = $(list).index(a);
		if (index == 0) {
			_obj.set($(list).last());
			return;
		}
		_obj.set($(list).eq(index - 1));
	},

	fullscreen: function(element, requestMethod) {
	    if (requestMethod) { // Native full screen.
	        requestMethod.call(element);
	    } else if (typeof window.ActiveXObject !== "undefined") { // Older IE.
	        var wscript = new ActiveXObject("WScript.Shell");
	        if (wscript !== null) {
	            wscript.SendKeys("{F11}");
	        }
	    }
	},

	cancelFullscreen: function(element) {
		var requestMethod = element.cancelFullScreen || element.webkitCancelFullScreen || element.mozCancelFullScreen || element.exitFullscreen;
		_obj.fullscreen(element, requestMethod);
	},

	requestFullscreen: function(element) {
		var requestMethod = element.requestFullScreen || element.webkitRequestFullScreen || element.mozRequestFullScreen || element.msRequestFullScreen;
		_obj.fullscreen(element, requestMethod);
	},

	isInFullscreen: function(element) {
		return (document.fullScreenElement && document.fullScreenElement !== null) ||  (document.mozFullScreen || document.webkitIsFullScreen);
	},

	toggleFullscreen: function() {
        if (_obj.isInFullscreen(fullscreenElement)) {
            _obj.cancelFullscreen(document);
        } else {
            _obj.requestFullscreen(fullscreenElement);
        }
        return false;
    }

	}; // _obj

	$('a[data-lightbox]').each(function(i,a) {
		$(a).click(function(e) {
			e.preventDefault();
			_obj.set(a);
		});
	});

	return _obj;

}
