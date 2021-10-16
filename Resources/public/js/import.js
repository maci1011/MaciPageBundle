
(function($){

var maciShopImport = function (options) {

	var form, records, index, alertNode,

	_obj = {

	saveTestCategory: function(data) {
		$.ajax({
			type: 'POST',
			data: {
				'data': {
					'add': {
						'section': 'shop',
						'entity': 'product',
						'item': 8,
						'relation': 'preview',
						'ids': [3]
					}
				}
			},
			url: '/mcm/ajax',
			success: function(d,s,x) {
				console.log('end test');
			}
		});
	},

	saveTestProduct: function(data) {
		$.ajax({
			type: 'POST',
			data: {
				'data': {
					'new': {
						'section': 'shop',
						'entity': 'product',
						'data': {
							'name': 'Prodotto di Prova',
							'price': '120.00',
							'locale': 'it',
							'status': 'new'
						}
					}
				}
			},
			url: '/mcm/ajax',
			success: function(d,s,x) {
				_obj.saveTestCategory(d);
			}
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
				_obj.saveNext();
			},
			error: function(d,s,x) {
				form.find("#import_data").val("Error at index " + index + ".\n" + form.find("#import_data").val());
				_obj.saveNext();
			}
		});
	},

	start: function(_form) {
		if (!records.length) {
			alert('No Data.');
			_obj.end();
		}
		alertNode = $("<div/>").addClass('alert alert-info').appendTo(form.find("#import_data").parent());
		index = -1;
	},

	saveNext: function(_form) {
		if (records.length <= index) {
			_obj.end();
		}
		index++;
		alertNode.text("Importing: " + index + " of " + records.length + ".");
		_obj.saveRecord(records[index]);
	},

	end: function() {
		alertNode.remove();
		form.find("#import_submit").show();
		form.find("#import_data").val("End.");
	},

	importXml: function() {
		var data = $(form.find("#import_data").val());
		var fields = [];
		data.find('Row').first().find('Cell').each(function(i, el) {
			if(!$(el).text().length) fields[i] = false;
			fields[i] = $(el).text().trim();
		});
		records = [];
		index = 0;
		var fieldsLen = data.find('Row').first().find('Cell').length;
		data.find('Row').not(':first').each(function(ri, row) {
			if($(row).find('Cell').length != fieldsLen) return;
			var _data = {};
			$(row).find('Cell').each(function(i, el) {
				if (fields[i] == false) return;
				_data[fields[i]] = $(el).text().trim();
			});
			records[index] = {
				'type': 'purchas',
				'label': (_data['Articolo'] + "/" + _data['Descr.Marchio'] + "/" + _data['Descr.Cat.Mer'] + "/" + _data['Descr.Colore'] + "/" + _data['Tgl']),
				'price': _data['Uni:XXEUR025'],
				'quantity': _data['Quantità'],
				'data': {'original': _data}
			};
			index++;
		});
		console.log(records);
		// _obj.start();
	},

	set: function(_form) {
		form = _form;
		form.find("#import_submit").click(function(e) {
			e.preventDefault();
			// form.find("#import_submit").hide();
			_obj.saveTestCategory();
		});
	},

	foo: function() {}

	}; // end _obj

	// Play!
	_obj.set($('#import_form'));

	return _obj;

}


$(document).ready(function(e) {

	maciShopImport();

});

})(jQuery);
