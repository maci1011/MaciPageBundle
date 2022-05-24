
(function($){

var maciShopExport = function (options) {

	var form, setInput, typeInput, barcodeInput, qtaInput, wrapper, alertNode,

	_obj = {

	getSets: function() {
		$.ajax({
			type: 'POST',
			data: {
				'data': {
					'list': {
						'section': 'records',
						'entity': 'record_set',
						'filters': [{
							'field': 'type',
							'value': 'exprt'
						}]
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
		if (!id || _obj.check()) return;
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
				'quantity': qtaInput.val(),
				'type': typeInput.val()
			},
			url: '/record/export-record',
			success: function(d,s,x) {
				_obj.setParent(d.id);
				_obj.showAlert(d);
				_obj.reset();
			}
		});
	},

	showAlert: function(data) {
		if (!alertNode)
			alertNode = $("<div/>").addClass('alert alert-info mt-2')
				.css('marginTop', '16px').appendTo(barcodeInput.parents('form').first())
				.click(function(e) {
					alertNode.remove();
					alertNode = false;
				});
		alertNode.text(
			(data.success ?
				(typeInput.val() == 'quantity' ? data.code + ' - ' +
					(data.type == 'vrnts' ? 'Quantity: ' + data.tot + ' || Variant: ' : '')
				: 'Saved! ') +
				(data.variant && data.variant.length ? data.variant + ' - ' : '') +
				'Leftovers: ' + data.quantity + '.'
			: 'Error! ' + data.error)
		);
	},

	reset: function() {
		qtaInput.val(1);
		barcodeInput.val('');
		barcodeInput.focus();
	},

	check: function() {
		return setInput.val() == 'null' && typeInput.val() != 'quantity';
	},

	barcodeChange: function(e) {
		if (_obj.check()) return;
		if (barcodeInput.val().trim().length != 13) return;
		_obj.saleRecord();
	},

	toggleBarcode: function() {
		if (_obj.check())
			barcodeInput.parents('.row').first().hide();
		else
			barcodeInput.parents('.row').first().show();
	},

	set: function(_form) {
		wrapper = false;
		alertNode = false;
		form = _form;
		setInput = form.find('#sales_set');
		typeInput = form.find("#sales_type");
		qtaInput = form.find("#data_quantity");
		barcodeInput = form.find("#data_barcode");
		form.submit(function(e) {
			e.preventDefault();
		});
		setInput.change(function(e) {
			_obj.toggleBarcode();
		});
		typeInput.change(function(e) {
			_obj.toggleBarcode();
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
