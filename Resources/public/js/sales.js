
(function($, d3){

var maciShopImport = function (options) {

	var form, records, index, barcodeInput, alertNode,

	_obj = {

	getSets: function() {
		var select = form.find('#sales_set');
		$.ajax({
			type: 'POST',
			data: {
				'data': {
					'list': {
						'section': 'shop',
						'entity': 'record_set'
					}
				}
			},
			url: '/mcm/ajax',
			success: function(d,s,x) {
				if (!d.list.length) {
					select.parent().hide();
					return;
				}
				for (var i = d.list.length - 1; i >= 0; i--) {
					$('<option/>').attr('value', d.list[i].id).text(d.list[i].id + ": " + d.list[i].label).appendTo(select);
					if (21 < select.find('option').length) break;
				}
			}
		});
	},

	getRecords: function() {
		$.ajax({
			type: 'POST',
			data: {
				'data': {
					'list': {
						'section': 'shop',
						'entity': 'record',
						'filters': {
							'type': 'purchas',
							'barcode': barcodeInput.val()
						}
					}
				}
			},
			url: '/mcm/ajax',
			success: function(d,s,x) {
				_obj.showData(d);
			}
		});
	},

	setParent: function(data) {
		var select = form.find('#import_set');
		if (select.val() == "null") return;
		$.ajax({
			type: 'POST',
			data: {
				'data': {
					'add': {
						'section': 'shop',
						'entity': 'record',
						'id': data.new.id,
						'relation': 'parent',
						'ids': [select.val()]
					}
				}
			},
			url: '/mcm/ajax'
		});
	},

	saveRecord: function(data) {
		$.ajax({
			type: 'POST',
			data: {
				'data': {
					'new': {
						'section': 'shop',
						'entity': 'record',
						'data': data
					}
				}
			},
			url: '/mcm/ajax',
			success: function(d,s,x) {
				console.log(d);
			}
		});
	},

	showData: function(data) {
		var wrapper = $("<div/>").addClass('container-fluid m-3').css('padding', '20px').insertAfter(barcodeInput);
		for (var i = data.list.length - 1; i >= 0; i--) {
			var item = data.list[i];
			var row = $("<div/>").addClass('row').css('padding', '2px').prependTo(wrapper);
			var vals = [data.list[i]['code'], data.list[i]['brand'], data.list[i]['category']];
			for (var j = 0; j < data.list[i]['data']['variants'].length; j++) {
				vals[vals.length] = data.list[i]['data']['variants'][j]['value'];
			}
			vals[vals.length] = data.list[i]['price'];
			$("<div/>").addClass('col-xs-6').appendTo(row).text(vals.join(', '));
			$("<button/>").addClass('btn btn-info').appendTo(row).click(function (e) {
				e.preventDefault();
				delete item['data']['imported'];
				item['data']['original'] = "Record#" + item.id;
				item['quantity'] = 1;
				item['type'] = 'sale';
				delete item['id'];
				delete item['recorded'];
				_obj.saveRecord(item);
			}).text('Select').wrap("<div/>").parent().addClass('col-xs-6');
		}
	},

	setBarcodeInput: function() {
		barcodeInput = form.find("#data-barcode");
		barcodeInput.on('change', function(e) {
			if (barcodeInput.val().length != 13) return;
			_obj.getRecords();
		});
		if (barcodeInput.val().length == 13) barcodeInput.change();
	},

	set: function(_form) {
		form = _form;
		form.find("#import_submit").click(function(e) {
			e.preventDefault();
			fileInput.click();
		});
		_obj.getSets();
		_obj.setBarcodeInput();
	}

	}; // end _obj

	// Play!
	_obj.set($('#sales_form'));

	return _obj;

}


$(document).ready(function(e) {

	maciShopImport();

});

})(jQuery, d3);
