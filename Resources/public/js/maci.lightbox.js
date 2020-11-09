/*
 * 		Maci Lightbox jQuery plugin
 *		Version for Bootstrap 3.4
 */

var maciLightbox = function (options) {

	var _defaults = {},
		lightbox = $('<div/>', {'class': 'lightbox'}).appendTo('body').hide(),
		bar = $('<div/>', {'class': 'container'}).appendTo(lightbox).wrap($('<div/>', {'class': 'navbar navbar-expand-lg navbar-light bg-light flex-column'})),
		bar_header = $('<div/>', {'class': 'navbar-header'}).appendTo(bar),
		bar_ul = $('<ul/>', {'class': 'nav navbar-nav navbar-right'}).appendTo(bar),
		container = $('<div/>', {'class': 'lightbox-container'}).appendTo(lightbox),
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
			$('<span/>', {'class': 'navbar-brand mr-4'}).html($(a).attr('data-title')).appendTo(bar_header);
		}
		// Album Index
		if ($(a).attr('data-lightbox') && 1 < $(list).length) {
			var list = $('a[data-lightbox=' + $(a).attr('data-lightbox') + ']'),
				str = ($(list).index(a) + 1) + ' / ' + $(list).length
			;
			$('<span/>', {'class': 'navbar-text'}).text(str).appendTo(bar_ul).wrap('<li class="nav-item"/>');
		}
		// View [+]
		$('<a/>', {'class': 'nav-link lightbox-open-button', 'target': '_blank', 'href': $(a).attr('href')}).html('<span class="fas fa-search" aria-hidden="true"/>').appendTo(bar_ul).wrap('<li class="nav-item"/>');
		// Fullscreen
		if (fullscreenElement.requestFullScreen || fullscreenElement.webkitRequestFullScreen || fullscreenElement.mozRequestFullScreen || fullscreenElement.msRequestFullScreen) {
			var icon_full = $('<span class="icon-full fas fa-expand-arrows-alt" aria-hidden="true"/>');
			var icon_reduce = $('<span class="icon-reduce fas fa-compress-arrows-alt" aria-hidden="true"/>');
			var fullscreen_button = $('<a/>', {'class': 'nav-link lightbox-fullscreen-button', 'href': ''}).click(function(e) {
				e.preventDefault();
				_obj.toggleFullscreen();
				$(this).toggleClass('on-fullscreen');
			}).append(icon_full).append(icon_reduce).appendTo(bar_ul).wrap('<li class="nav-item"/>');
			if (_obj.isInFullscreen(fullscreenElement)) {
				$(fullscreen_button).addClass('on-fullscreen');
			}
		}
		// Close [X]
		$('<a/>', {'class': 'nav-link lightbox-close-button', 'href': ''}).click(function(e) {
			e.preventDefault();
			lightbox.hide();
		}).html('<span class="fas fa-times" aria-hidden="true"/>').appendTo(bar_ul).wrap('<li class="nav-item"/>');
		// Image Containers
		var image = $('<img/>', {'class': 'lightbox-image img-fluid'}).appendTo(container);
		var image_wrapper = image.wrap($('<div/>', {'class': 'lightbox-imape-wrapper container'})).parent().hide();
		var slider = image_wrapper.wrap($('<div/>', {'class': 'lightbox-slider'})).parent();
		// Image Infos Container
		var image_info = $('<div/>', {'class': 'lightbox-image-info'});
		// Brand
		if ($(a).attr('data-brand')) {
			$('<img/>', {'src': $(a).attr('data-brand'), 'class': 'lightbox-image-brand-image'}).html($(a).attr('data-description')).appendTo(image_info).wrap($('<div/>', {'class': 'lightbox-image-brand container'}));
		}
		// Description
		if ($(a).attr('data-description')) {
			$('<div/>', {'class': 'lightbox-image-description container'}).html($(a).attr('data-description')).appendTo(image_info);
		}
		if (image_info.children().length) {
			image_info.appendTo(container).hide();
		}
		// Album Controllers
		if ($(a).attr('data-lightbox') && 1 < $('a[data-lightbox=' + $(a).attr('data-lightbox') + ']').length) {
			// Prev
			$('<a/>', {'class': 'lightbox-controller carousel-control left', 'href': ''}).click(function(e) {
				e.preventDefault();
				_obj.prev(a);
			}).html('<span class="fas fa-angle-left" aria-hidden="true"/>').appendTo(slider);
			// Next
			$('<a/>', {'class': 'lightbox-controller carousel-control right', 'href': ''}).click(function(e) {
				e.preventDefault();
				_obj.next(a);
			}).html('<span class="fas fa-angle-right" aria-hidden="true"/>').appendTo(slider);
		}
		// Loader Icon
		var loader_icon = $('<div/>', {'class': 'lightbox-loader-icon'}).appendTo(lightbox);
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
