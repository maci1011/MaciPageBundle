
(function($){

var maciShopExport = function (options) {

	var form, setInput, typeInput, codeInput, barcodeInput, qtaInput, wrapper, alertNode, out,

	_obj = {

	getSets: function()
	{
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
				if (!d.list.length)
				{
					setInput.parent().hide();
					return;
				}
				for (var i = 0; i < d.list.length; i++)
				{
					$('<option/>').attr('value', d.list[i].id).text(d.list[i].id + ": " + d.list[i].label).appendTo(setInput);
					if (21 < setInput.find('option').length) break;
				}
			}
		});
	},

	setParent: function(id)
	{
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

	saleRecord: function()
	{
		$.ajax({
			type: 'POST',
			data: {
				'barcode': barcodeInput.val().trim(),
				'quantity': qtaInput.val(),
				'type': typeInput.val()
			},
			url: '/record/export-record',
			success: function(d,s,x) {
				_obj.reset();
				_obj.setParent(d.id);
				_obj.showAlert(d);
			}
		});
	},

	findProduct: function()
	{
		$.ajax({
			type: 'POST',
			data: {
				'data': {
					'list': {
						'section': 'shop',
						'entity': 'product',
						'filters': [{
							'field': 'code',
							'method': 'LIKE',
							'value': barcodeInput.val().trim()
						}]
					}
				}
			},
			url: '/mcm/ajax',
			success: function(d,s,x) {
				_obj.showList(d);
			}
		});
	},

	exportProducts: function(products)
	{
		if (codeInput.val() != 'check' && setInput.val() == 'null')
		{
			alert('Select a Record Set!');
			return;
		}

		$.ajax({
			type: 'POST',
			data: {
				'setId': setInput.val(),
				'products': products
			},
			url: '/record/export-products',
			success: function(d,s,x) {
				console.log(d);
				qtaInput.val(1);
				barcodeInput.keypress();
			}
		});
	},

	getProductsList: function(type)
	{
		$.ajax({
			type: 'POST',
			data: {
				'type': type
			},
			url: '/record/export-list',
			success: function(d,s,x) {
				_obj.showList(d);
			}
		});
	},

	showList: function(data)
	{
		_obj.resetOutput();

		if (!data || !data.list || !data.list.length)
		{
			$('<h5/>').text('No result.').appendTo(out);
			return;
		}

		var tbody = $('<tbody/>')
			.appendTo(out)
			.wrap('<table class="table table-striped" />')
		;

		var thead = $('<tr/>');
		$('<th/>').html('&nbsp;').attr('width', '50px').appendTo(thead);
		$('<th/>').text('Code').attr('width', '150px').appendTo(thead);
		$('<th/>').text('Name').attr('width', '250px').appendTo(thead);
		$('<th/>').text('Color').attr('width', '200px').appendTo(thead);
		$('<th/>').text('Variant').appendTo(thead);
		$('<th/>').text('Buyd').attr('width', '56px').appendTo(thead);
		$('<th/>').text('Qta').attr('width', '56px').appendTo(thead);
		$('<th/>').text('Sell').attr('width', '56px').appendTo(thead);
		thead.wrap('<thead/>').parent().prependTo(tbody.parent());

		for (var i = data.list.length - 1; i >= 0; i--)
		{
			if (data.list[i].type == 'simple')
			{
				if (!(['quantity', 'check'].includes(typeInput.val())) && data.list[i].quantity == 0)
					continue;

				var tr = $('<tr/>');
				$('<td/>').html('<input type="checkbox" class="productItemId" pid="' + data.list[i].id + '" pva="__null__" qta="' + data.list[i].quantity + '">').appendTo(tr);
				$('<td/>').text(data.list[i].code).appendTo(tr);
				$('<td/>').text(data.list[i].name).appendTo(tr);
				$('<td/>').text(data.list[i].variant).appendTo(tr);
				$('<td/>').text('-').appendTo(tr);
				$('<td/>').text(data.list[i].buyed).appendTo(tr);
				$('<td/>').text(data.list[i].quantity).appendTo(tr);
				$('<td/>').text(data.list[i].selled).appendTo(tr);
				tr.appendTo(tbody);
			}
			else
			{
				var variants = data.list[i].data.variants;
				for (var j = variants.length - 1; j >= 0; j--)
				{
					if (!(['quantity', 'check'].includes(typeInput.val())) && variants[j].quantity == 0)
						continue;

					var tr = $('<tr/>');
					$('<td/>').html('<input type="checkbox" class="productItemId" pid="' + data.list[i].id + '" pva="' + variants[j].name + '" qta="' + variants[j].quantity + '">').appendTo(tr);
					$('<td/>').text(data.list[i].code).appendTo(tr);
					$('<td/>').text(data.list[i].name).appendTo(tr);
					$('<td/>').text(data.list[i].variant).appendTo(tr);
					$('<td/>').text(variants[j].name).appendTo(tr);
					$('<td/>').text(variants[j].buyed).appendTo(tr);
					$('<td/>').text(variants[j].quantity).appendTo(tr);
					$('<td/>').text(variants[j].selled).appendTo(tr);
					tr.appendTo(tbody);
				}
			}
		}

		if (typeInput.val() == 'quantity')
			return;

		var label = typeInput.val();
		label = label[0].toUpperCase() + label.slice(1);

		if (typeInput.val() == 'check')
		{
			$('<button class="btn btn-primary" />').appendTo(out).click(function(e) {
				e.preventDefault();
				_obj.export(1, 'check');
			}).text(label + ' Products');
			return;
		}

		$('<button class="btn btn-primary" />').appendTo(out).click(function(e) {
			e.preventDefault();
			_obj.export(qtaInput.val(), typeInput.val());
		}).text(label + ' (Sel.Qta)');

		$('<button class="btn btn-primary" />').appendTo(out).click(function(e) {
			e.preventDefault();
			_obj.export('all', typeInput.val());
		}).text(label + ' All').css('marginLeft', '8px');
	},

	export: function(quantity, type)
	{
		var selected = $('.productItemId:checked');
		if (!selected.length)
		{
			alert('Nothing selected!');
			return;
		}

		var products = [];
		selected.each(function(i, el) {
			products.push({
				'id': parseInt($(el).attr('pid')),
				'variant': $(el).attr('pva') == '__null__' ? null : $(el).attr('pva'),
				'quantity': quantity == 'all' ? parseInt($(el).attr('qta')) : (
					parseInt(quantity) <= parseInt($(el).attr('qta')) ? parseInt(quantity) : parseInt($(el).attr('qta'))
				),
				'type': type
			});
		});

		// console.log(products);

		_obj.exportProducts(products);
	},

	showAlert: function(data)
	{
		if (!alertNode)
		{
			out.html('');
			alertNode = $("<div/>").addClass('alert alert-info mt-2')
				.css('marginTop', '16px').appendTo(out)
				.click(function(e) {
					alertNode.remove();
					alertNode = false;
				});
		}

		alertNode.text(
			(data.success ?
				(typeInput.val() == 'check' ? (
					data.edited ? 'Data have been corrected. - ' : 'Data are correct. - '
				) : '') +
				(typeInput.val() == 'quantity' || typeInput.val() == 'check' ? data.code + ' - ' +
					(data.type == 'vrnts' ? 'Quantity: ' + data.tot + ' || Variant: ' : '')
				: 'Saved! ') +
				(data.variant && data.variant.length ? data.variant + ' - ' : '') +
				'Leftovers: ' + data.quantity + '.'
			: 'Error! ' + data.error)
		);
	},

	reset: function()
	{
		qtaInput.val(1);
		barcodeInput.val('');
		barcodeInput.focus();
	},

	resetOutput: function()
	{
		if (alertNode)
		{
			alertNode.remove();
			alertNode = false;
		}

		out.html('');
	},

	check: function()
	{
		return setInput.val() == 'null' && typeInput.val() != 'quantity' && typeInput.val() != 'check';
	},

	barcodeChange: function(e)
	{
		if (_obj.check())
			return;

		barcodeInput.parent().show();

		if (codeInput.val() == 'barcode')
		{
			if (barcodeInput.val().trim().length != 13)
				return;

			_obj.saleRecord();
		}
		else if (codeInput.val() == 'olders')
		{
			barcodeInput.parent().hide();

			_obj.getProductsList('olders');
		}
		else if (codeInput.val() == 'negatives')
		{
			barcodeInput.parent().hide();

			_obj.getProductsList('negatives');
		}
		else
		{
			if (barcodeInput.val().trim().length < 6)
			{
				_obj.resetOutput();
				return;
			}

			_obj.findProduct();
		}
	},

	toggleBarcode: function()
	{
		if (_obj.check()) barcodeInput.parents('.toggleContainer').first().hide();
		else barcodeInput.parents('.toggleContainer').first().show();
	},

	set: function(_form)
	{
		wrapper = false;
		alertNode = false;
		form = _form;
		setInput = form.find('#sales_set');
		typeInput = form.find("#sales_type");
		codeInput = form.find("#code_type");
		qtaInput = form.find("#data_quantity");
		barcodeInput = form.find("#data_barcode");
		out = form.find("#output");
		form.submit(function(e) {
			e.preventDefault();
		});
		setInput.change(function(e) {
			_obj.toggleBarcode();
			form.find('#btn_export').attr('href', '/record/reports/export-set/' + setInput.val()).show();
			if (setInput.val() == 'null')
				form.find('#btn_export').hide();
			_obj.resetOutput();
			barcodeInput.keypress();
		}).change();
		typeInput.change(function(e) {
			_obj.toggleBarcode();
			_obj.resetOutput();
			barcodeInput.keypress();
		});
		codeInput.change(function(e) {
			var s = $(this).val();
			barcodeInput.prev().text(s[0].toUpperCase() + s.slice(1));
			_obj.resetOutput();
			barcodeInput.keypress();
		}).change();
		barcodeInput.val('').on('keypress', _obj.barcodeChange).keypress();
		_obj.getSets();
		_obj.resetOutput();
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
