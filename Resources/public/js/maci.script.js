
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
		$('.page_slides .slide, .fllscrn').each(function(i,el) {
			$(el).css('min-height', ( $(window).height() ) + 'px' );
		});
	}).resize();

	$('.page_slides .slide .button-next').each(function(i,el) {
		$(el).click(function(e) {
			e.preventDefault();
			var nxt = $(el).parents('.slide').first();
			nxt = nxt.next().length ? nxt.next() : nxt.parent().next();
			$(window).scrollTo( nxt , 600 );
		});
	});

	$(window).scroll(function(e) {
		$('.page_container, .page_slides .slide, .tgglscrn').not('page_slides').each(function(i,el) {
			var
				wnd = $(window).height(),
				lmt = wnd / 3,
				lmb = wnd - lmt,
				scr = $(window).scrollTop(),
				ofs = $(el).offset().top,
				hgt = $(el).height(),
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
	}).scroll();

	// $('[dropdown=dropdown]').dropdown();

	// $(".maci-album-item").fancybox();

});

})(jQuery);
