
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
				_obj.setParent(d.id);
				_obj.showAlert(d);
				_obj.reset();
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
		$.ajax({
			type: 'POST',
			data: {
				'setId': setInput.val(),
				'products': products
			},
			url: '/record/export-products',
			success: function(d,s,x) {
				console.log(d);
				_obj.findProduct();
				qtaInput.val(1);
			}
		});
	},

	showList: function(data)
	{
		if (alertNode)
		{
			alertNode.remove();
			alertNode = false;
		}

		console.log(data);

		out.html('');

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

		$('<button class="btn btn-primary" />').appendTo(out).click(function(e) {
			e.preventDefault();
			_obj.export(qtaInput.val(), 'sale');
		}).text('Sell (Sel.Qta)');

		$('<button class="btn btn-primary" />').appendTo(out).click(function(e) {
			e.preventDefault();
			_obj.export('all', 'sale');
		}).text('Sell All').css('marginLeft', '8px');

		$('<button class="btn btn-primary" />').appendTo(out).click(function(e) {
			e.preventDefault();
			_obj.export(qtaInput.val(), 'back');
		}).text('Back (Sel.Qta)').css('marginLeft', '8px');

		$('<button class="btn btn-primary" />').appendTo(out).click(function(e) {
			e.preventDefault();
			_obj.export('all', 'back');
		}).text('Back All').css('marginLeft', '8px');

		$('<button class="btn btn-primary" />').appendTo(out).click(function(e) {
			e.preventDefault();
			_obj.export(qtaInput.val(), 'return');
		}).text('Return (Sel.Qta)').css('marginLeft', '8px');

		$('<button class="btn btn-primary" />').appendTo(out).click(function(e) {
			e.preventDefault();
			_obj.export('all', 'return');
		}).text('Return All').css('marginLeft', '8px');
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

		console.log(products);

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

	check: function()
	{
		return setInput.val() == 'null' && typeInput.val() != 'quantity' && typeInput.val() != 'check';
	},

	barcodeChange: function(e)
	{
		if (_obj.check())
			return;

		if (codeInput.val() == 'barcode')
		{
			if (barcodeInput.val().trim().length != 13)
				return;

			_obj.saleRecord();
		}
		else
		{
			if (barcodeInput.val().trim().length < 6)
			{
				out.html('');
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
			form.find('#btn_export').attr('href', '/record/reports/export-set/' + setInput.val());
			out.html('');
		});
		typeInput.change(function(e) {
			_obj.toggleBarcode();
			out.html('');
		}).change();
		codeInput.change(function(e) {
			var s = $(this).val();
			barcodeInput.prev().text(s[0].toUpperCase() + s.slice(1));
			out.html('');
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
