/*
 * 		Maci Lightbox jQuery plugin
 *		Version for Bootstrap 3.4
 */

var maciLightbox = function (options) {

	var _defaults = {},
		lightbox = $('<div/>', {'class': 'maci-lightbox'}).appendTo('body').hide(),
		bar = $('<div/>', {'class': 'container'}).appendTo(lightbox).wrap($('<div/>', {'class': 'navbar navbar-fixed-top navbar-inverse'})),
		bar_header = $('<div/>', {'class': 'navbar-header'}).appendTo(bar),
		bar_ul = $('<ul/>', {'class': 'nav navbar-nav navbar-right'}).appendTo(bar),
		container = $('<div/>', {'class': 'maci-lightbox-container'}).appendTo(lightbox)
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
		$('<a/>', {'target': '_blank', 'href': ($(a).attr('data-full') ? $(a).attr('data-full') : $(a).attr('href'))}).html('<span class="glyphicon glyphicon-zoom-in" aria-hidden="true"/>').appendTo(bar_ul).wrap('<li/>');
		// Close [X]
		$('<a/>', {'class': 'maci-lightbox-close-button', 'href': ''}).click(function(e) {
			e.preventDefault();
			lightbox.hide();
		}).html('<span class="glyphicon glyphicon-remove" aria-hidden="true"/>').appendTo(bar_ul).wrap('<li/>');
		// Image
		var image = $('<img/>', {'class': 'maci-lightbox-image img-responsive'}).appendTo(container);
		var image_wrapper =image.wrap($('<div/>', {'class': 'maci-lightbox-imape-wrapper container'})).parent().hide();
		var slider = image_wrapper.wrap($('<div/>', {'class': 'maci-lightbox-slider'})).parent();
		image.on('load', function(e) {
			image_wrapper.show();
			if (slider.height() < $(window).height()) {
				slider.height($(window).height() + 'px');
			}
		}).attr('src', $(a).attr('href'));
		var image_info = $('<div/>', {'class': 'maci-lightbox-image-info'});
		// Brand
		if ($(a).attr('data-brand')) {
			$('<img/>', {'src': $(a).attr('data-brand'), 'class': 'maci-lightbox-image-brand-image'}).html($(a).attr('data-description')).appendTo(image_info).wrap($('<div/>', {'class': 'maci-lightbox-image-brand container'}));
		}
		// Description
		if ($(a).attr('data-description')) {
			$('<div/>', {'class': 'maci-lightbox-image-description container'}).html($(a).attr('data-description')).appendTo(image_info);
		}
		if ($(image_info).children().length) {
			$(image_info).appendTo(container);
		}
		// Album Controllers
		if (!$(a).attr('data-lightbox')) {
			return;
		}
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
	}

	};

	$('a[data-lightbox]').each(function(i,a) {
		$(a).click(function(e) {
			e.preventDefault();
			_obj.set(a);
		});
	});

	return _obj;

}
