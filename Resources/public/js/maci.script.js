
(function($){

$(document).ready(function(e) {

	$('.maci_order_form').each(function(i,el) {
		$(el).submit(function(e){

			$(el).append($('<input/>', {
				'name': 'form[map][foo]',
				'type': 'hidden',
				'value': 'bar'
			}));

			var p =  $(el).parents('.maci-product');

			if (p.length) {

				var id = p.attr('id').split('_')[1];

				$(el).append($('<input/>', {
					'name': 'form[map][products]['+ i +'][id]',
					'type': 'hidden',
					'value': id
				}));

			}

		});
	});

	// $(".maci-album-item").fancybox();

});

})(jQuery);
