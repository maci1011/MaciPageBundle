
(function($){

$(document).ready(function(e) {

	$('img[cover]').each(function(i,el) {
		var src = $(el).attr('src'),
			cvr = $(el).attr('cover');
		$(el).mouseenter(function(e) {
			$(el).attr('src', cvr);
		}).mouseleave(function(e) {
			$(el).attr('src', src);
		});
	});

	$(window).resize(function(e) {
		$('.page_slides > .slide.slider > .slide-wrapper > .carousel.slide, .page_slider > .carousel.slide').each(function(i,el) {
			var _h = ( $(window).height() < 800 ? $(window).height() : 800 );
			$(el).css('maxHeight', ( _h ) + 'px' );
		});
		$('.page_slides > .slide').not('.slider').not('.gallery').children('.slide-wrapper').each(function(i,el) {
			var _a = ( $(el).parent().innerHeight() - $(el).parent().height() ),
				_h = $(el).innerHeight() - $(el).height(), _b, _t;
			_a += _h;
			_a = ( _a < 0 ? 0 : _a );
			_h = ( _h < 0 ? 0 : _h );
			$(el).children(':visible').each(function() {
				_h += $(this).outerHeight(true);
			});
			_b = ( $(window).height() - _a );
			_t = ( ( _a + _h ) < _b ? _b : _h );
			_t = ( 400 < _t ? _t : 400 );
			_t = ( _t < 800 ? _t : 800 );
			$(el).height( _t + 'px' );
			if ( $(el).children(':visible').length == 1 && $(el).children(':visible').first().hasClass('carousel') ) {
				$(el).children(':visible').first().height(_t + 'px');
			}
		});
	}).resize();

	$('.slide .button-next').each(function(i,el) {
		$(el).click(function(e) {
			e.preventDefault();
			var nxt = $(el).parents('.slide').first();
			nxt = nxt.next().length ? nxt.next() : nxt.parent().next();
			$(window).scrollTo( nxt , 600 );
		});
	});
	$('.slide .button-next').last().parents('.slide').find('.button-next').hide();

	$('.set-cookie').each(function(i,el) {
		$(el).click(function(e) {
			e.preventDefault();
			$.ajax({
				type: 'GET',
				data: {},
				url: $(el).attr('href')
			});
			$(el).parents('.container, .popup').first().hide();
		});
	});

	$('.media-preview-video').each(function(i,el) {
		$(el).click(function(e) {
			e.preventDefault();
			var ct = $('<div/>').attr('class', 'media-video-container');
			ct.appendTo('body');
			var cn = $('<div/>').attr('class', 'media-video-content');
			cn.appendTo(ct);
			var cx = $('<a/>').attr('class', 'media-video-close').click(function(e) {
				e.preventDefault();
				ct.remove();
			}).appendTo(ct);
			$('<span/>').attr('class', 'glyphicon glyphicon-remove').appendTo(cx);
			cn.html($(el).next('.media-preview-video-content').html());
			cc = cn.children().first();
			cn.width(cc.width());
			if (cc.height() < $(window).height()) {
				cc.css('marginTop', ( ( $(window).height() - cc.height() ) / 3 ) + 'px' );
			}
		});
	});

	$(window).scroll(function(e) {

		$('.page_container, .page_slides > .slide, .tgglscrn').not('.page_slides').each(function(i,el) {
			var
				wnd = $(window).height(),
				lmt = wnd / 3,
				lmb = wnd - lmt,
				scr = $(window).scrollTop(),
				ofs = $(el).offset().top,
				hgt = $(el).innerHeight(),
				dff = ofs - scr,
				btt = dff + hgt
			;
			if ( dff < lmb && lmt < btt ) {
				$(el).addClass('active');
				$(el).removeClass('inactive');
			} else {
				$(el).removeClass('active');
				$(el).addClass('inactive');
			}
		});

		if ($(window).scrollTop() < 350) {
			$('.navbar-fixed-top').addClass('on-top');
			$('.navbar-fixed-top').removeClass('on-scrolling');
		} else {
			$('.navbar-fixed-top').removeClass('on-top');
			$('.navbar-fixed-top').addClass('on-scrolling');
		}

	}).scroll();

});

$(window).load(function(e) {

	$(window).resize();

});

})(jQuery);
