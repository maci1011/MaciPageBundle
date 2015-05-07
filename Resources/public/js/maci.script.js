
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
		$('.fllscrn').each(function(i,el) {
			$(el).css('min-height', ( $(window).height() ) + 'px' );
		});
	}).resize();

	// $('[dropdown=dropdown]').dropdown();

	// $(".maci-album-item").fancybox();

});

})(jQuery);
