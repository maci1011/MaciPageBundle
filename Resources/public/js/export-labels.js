
(function($){

var maciShopExport = function (options) {

	var form, getLink, setInput, path,

	_obj = {

	getSets: function() {
		$.ajax({
			type: 'POST',
			data: {
				'data': {
					'list': {
						'section': 'records',
						'entity': 'record_set',
						'filters': {
							'type': 'exprt'
						}
					}
				}
			},
			url: '/mcm/ajax',
			success: function(d,s,x) {
				if (!d.list.length) {
					setInput.parent().hide();
					return;
				}
				for (var i = 0; i < d.list.length; i++) {
					$('<option/>').attr('value', d.list[i].id).text(d.list[i].id + ": " + d.list[i].label).appendTo(setInput);
					if (21 < setInput.find('option').length) break;
				}
			}
		});
	},

	set: function(_form) {
		form = _form;
		form.submit(function(e) {
			e.preventDefault();
		});
		getLink = form.find('#getLink');
		path = getLink.attr('href');
		setInput = form.find('#labels_set');
		setInput.change(function(e) {
			if (setInput.val() == 'null') getLink.parent().hide();
			else
			{
				getLink.parent().show();
				getLink.attr('href', path + '?setId=' + setInput.val());
			}
		}).change();
		_obj.getSets();
	}

	}; // end _obj

	// Play!
	_obj.set($('#labels_form'));

	return _obj;
}


$(document).ready(function(e) {

	maciShopExport();

});

})(jQuery);
