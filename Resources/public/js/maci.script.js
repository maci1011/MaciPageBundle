
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
		$('.page_slides > .slide, .fllscrn').each(function(i,el) {
			$(el).css('min-height', ( $(window).height() ) + 'px' );
		});
		$('.page_slides > .slide.slider .item').each(function(i,el) {
			$(el).css('height', ( $(window).height() ) + 'px' );
		});
		$('.slide > .slide-wrapper').each(function(i,el) {
			var _a = ( $(el).parent().innerHeight() - $(el).parent().height() ),
				_b = ( $(window).height() - ( _a < 0 ? 0 : _a ) )
			;
			_a = ( $(el).children().height() + $(el).innerHeight() - $(el).height() );
			_b = ( _b < _a ? _a : _b );
			$(el).css('height', ( _b  ) + 'px' );
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
		if ($(window).scrollTop() < 50) {
			$('.navbar-fixed-top').addClass('on-top');
			$('.navbar-fixed-top').removeClass('on-scrolling');
		} else {
			$('.navbar-fixed-top').removeClass('on-top');
			$('.navbar-fixed-top').addClass('on-scrolling');
		}
	}).scroll();

});

})(jQuery);
