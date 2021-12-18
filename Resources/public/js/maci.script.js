
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

	var slides = $('.page_slides > .slide');

	if (0 < slides.length) {
		if (slides.length < 3) {

			slides.find('.button-next').hide();

		} else {

			slides.not(slides.last()).each(function(j,slide) {
				$(slide).find('.button-next > a').each(function(i,el) {
					$(el).click(function(e) {
						e.preventDefault();
						$(window).scrollTo( $(slide).next() , 600 );
					});
				});
			});

			slides.last().find('.button-next > a').each(function(i,el) {
				$(el).click(function(e) {
					e.preventDefault();
					$(window).scrollTo($('#main'), 600 );
				});
			});

		}
	}

	$('.set-cookie-button').each(function(i,el) {
		$(el).click(function(e) {
			e.preventDefault();
			$.ajax({
				type: 'GET',
				data: {},
				url: $(el).attr('href')
			});
			$(el).parents('.cookieContainer').first().remove();
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

	$('.product-child-cart').hide();
	$('.product-select-variant-container').show();

	$('button.product-child-btn.btn').each(function(i,el) {
		$(el).click(function(e) {
			e.preventDefault();
			$('.product-select-variant-container').hide();
			$('.product-child-cart').hide();
			$( $(el).attr('data') ).show();
		});
	});

	// Window

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
			$('.navbar.fixed-top').addClass('on-top');
			$('.navbar.fixed-top').removeClass('on-scrolling');
		} else {
			$('.navbar.fixed-top').removeClass('on-top');
			$('.navbar.fixed-top').addClass('on-scrolling');
		}

	}).scroll();

	$('select.variants-selector').each(function(i,el) {
		$(el).change(function(e) {
			$(el).parent().find('.variant-add-wrapper').hide();
			$(el).parent().find('#variant_' + $(el).val()).show();
		});
		$(el).parent().find('.variant-add-wrapper').hide().first().show();
	});

	// Lightbox

	maciLightbox();

});

$(window).on('load', function(e) {

	$(window).scroll();

});

})(jQuery);
