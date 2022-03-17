
(function($){

var maciShopExport = function (options) {

	var form, setInput, typeInput, barcodeInput, wrapper, alertNode,

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

	setParent: function(id) {
		if (!id || setInput.val() == "null") return;
		$.ajax({
			type: 'POST',
			data: {
				'data': {
					'add': {
						'section': 'records',
						'entity': 'record',
						'id': id,
						'relation': 'parent',
						'ids': [setInput.val()]
					}
				}
			},
			url: '/mcm/ajax'
		});
	},

	saleRecord: function() {
		$.ajax({
			type: 'POST',
			data: {
				'barcode': barcodeInput.val().trim(),
				'type': typeInput.val()
			},
			url: '/record/export-record',
			success: function(d,s,x) {
				_obj.setParent(d.id);
				_obj.showAlert(d);
			}
		});
	},

	showAlert: function(data) {
		if (!alertNode)
			alertNode = $("<div/>").addClass('alert alert-info mt-2')
				.css('marginTop', '16px').appendTo(barcodeInput.parent())
				.click(function(e) {
					alertNode.remove();
					alertNode = false;
				});
		alertNode.text((data.success ? (typeInput.val() == 'quantity' ? '' : 'Saved! ') + (data.variant && data.variant.length ? data.variant + ' - ' : '') + 'Leftovers: ' + data.quantity + '.' : 'Error! ' + data.error));
		barcodeInput.val('');
		barcodeInput.focus();
	},

	barcodeChange: function(e) {
		if (setInput.val() == 'null') return;
		if (barcodeInput.val().trim().length != 13) return;
		_obj.saleRecord();
	},

	set: function(_form) {
		wrapper = false;
		alertNode = false;
		form = _form;
		setInput = form.find('#sales_set');
		typeInput = form.find("#sales_type");
		barcodeInput = form.find("#data-barcode");
		form.submit(function(e) {
			e.preventDefault();
		});
		setInput.change(function(e) {
			if (setInput.val() == 'null') barcodeInput.parent().hide();
			else barcodeInput.parent().show();
		}).change();
		barcodeInput.on('keypress', _obj.barcodeChange).keypress();
		_obj.getSets();
	}

	}; // end _obj

	// Play!
	_obj.set($('#sales_form'));

	return _obj;

}


$(document).ready(function(e) {

	maciShopExport();

});

})(jQuery);
