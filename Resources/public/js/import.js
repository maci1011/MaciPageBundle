
(function($){

var maciShopImport = function (options) {

	var form, records, index, alertNode,

	_obj = {

	saveTestCategory: function(data) {
		$.ajax({
			type: 'POST',
			data: {
				'data': {
					'remove': {
						'section': 'shop',
						'entity': 'product',
						'id': 4,
						'trash': false
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
		alertNode = $("<div/>").addClass('alert alert-info mt-2').appendTo(form.find("#import_data").parent());
		index = -1;
		_obj.saveNext();
	},

	saveNext: function(_form) {
		if (records.length <= index) {
			_obj.end();
		}
		index++;
		alertNode.text("Importing: " + index + " of " + records.length + ".");
		if (index < 1) _obj.saveRecord(records[index]);
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
			var dt = {};
			$(row).find('Cell').each(function(i, el) {
				if (fields[i] == false) return;
				dt[fields[i]] = $(el).text().trim();
			});
			records[index] = {
				'import': dt
			};
			index++;
		});
		console.log(records);
		_obj.start();
	},

	set: function(_form) {
		form = _form;
		form.find("#import_submit").click(function(e) {
			e.preventDefault();
			// form.find("#import_submit").hide();
			_obj.importXml();
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
