
(function($, d3){

var maciShopImport = function (options) {

	var form, barcodeInput, wrapper,

	_obj = {

	getSets: function() {
		var select = form.find('#sales_set');
		$.ajax({
			type: 'POST',
			data: {
				'data': {
					'list': {
						'section': 'records',
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

	setParent: function(id) {
		var select = form.find('#sales_set');
		if (select.val() == "null") return;
		$.ajax({
			type: 'POST',
			data: {
				'data': {
					'add': {
						'section': 'records',
						'entity': 'record',
						'id': id,
						'relation': 'parent',
						'ids': [select.val()]
					}
				}
			},
			url: '/mcm/ajax'
		});
	},

	saveRecord: function(item) {
		$.ajax({
			type: 'POST',
			data: item,
			url: '/record/export-record',
			success: function(d,s,x) {
				_obj.setParent(d.id);
				console.log('Saved.');
			}
		});
	},

	getProducts: function() {
		$.ajax({
			type: 'POST',
			data: {
				'data': {
					'list': {
						'section': 'shop',
						'entity': 'product',
						'filters': {
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

	showData: function(data) {
		if (wrapper) wrapper.remove();
		wrapper = $("<div/>").addClass('container-fluid m-3').css('padding', '20px').insertAfter(barcodeInput);
		for (var i = data.list.length - 1; i >= 0; i--) {
			var item = data.list[i];
			var row = $("<div/>").addClass('row').css('padding', '12px 0 2px').appendTo(wrapper);
			var vals = [data.list[i]['code'], data.list[i]['name'], data.list[i]['brand'], data.list[i]['price']];
			if (data.list[i]['type'] == 'vrnts')
			{
				$("<div/>").addClass('col-xs-6').appendTo(row).html('<strong>' + vals.join(', ') + ':</strong>');
				for (var j = 0; j < item.data.variants.length; j++) {
					var btnClass = 'btn-info';
					if (parseInt(data.list[i]['data']['variants'][j]['quantity']) < 1) btnClass = 'btn-danger';;
					row = $("<div/>").addClass('row').css('padding', '2px').appendTo(wrapper);
					$("<div/>").addClass('col-xs-6 text-right').css('paddingTop', '6px').html('<strong>' + data.list[i]['data']['variants'][j]['name'] + '</strong>').appendTo(row);
					$("<button/>").text('Select').addClass('btn ' + btnClass).appendTo(row).click(function (e) {
						e.preventDefault();
						_obj.saveRecord($(this).prop('saveRecordData'));
						barcodeInput.val('');
						wrapper.remove();
						wrapper = false;
						barcodeInput.focus();
					}).prop('saveRecordData', {id: item.id, variant: item.data.variants[j]}).wrap("<div/>").parent().addClass('col-xs-6');
				}
			}
			else
			{
				var btnClass = 'btn-info';
				if (parseInt(data.list[i]['quantity']) < 1) btnClass = 'btn-danger';
				$("<div/>").addClass('col-xs-6 text-right').css('paddingTop', '6px').appendTo(row).html('<strong>' + vals.join(', ') + '</strong>');
				$("<button/>").addClass('btn ' + btnClass).appendTo(row).click(function (e) {
					e.preventDefault();
					_obj.saveRecord({id: item.id});
					barcodeInput.val('');
					wrapper.remove();
					wrapper = false;
					barcodeInput.focus();
				}).text('Select').wrap("<div/>").parent().addClass('col-xs-6');
			}
		}
	},

	setBarcodeInput: function() {
		barcodeInput = form.find("#data-barcode");
		barcodeInput.on('change', function(e) {
			if (barcodeInput.val().length != 13) return;
			_obj.getProducts();
		}).change();
	},

	set: function(_form) {
		wrapper = false;
		form = _form;
		form.submit(function(e) {
			e.preventDefault();
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
