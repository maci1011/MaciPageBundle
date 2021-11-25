
(function($){

var maciShopImport = function (options) {

	var form, records, index, fileInput, alertNode,

	_obj = {

	getSets: function() {
		var select = form.find('#import_set');
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
				var select = form.find('#import_set');
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

	loadUnsettedRecords: function() {
		$.ajax({
			type: 'POST',
			url: '/shop/load-unsetted-records',
			success: function(d,s,x) {
				console.log(d);
			}
		});
	},

	saveRecord: function(data) {
		if (3 <= index) return;
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
				_obj.setParent(d);
				_obj.saveNext();
			},
			error: function(d,s,x) {
				_obj.saveNext();
			}
		});
	},

	start: function(_form) {
		if (!records.length) {
			alert('No Data.');
		}
		alertNode = $("<div/>").addClass('alert alert-info mt-2').css('marginTop', '16px').appendTo(form.find("#dataFile").parent());
		index = -1;
		_obj.saveNext();
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
		alertNode.text("End! Imported: " + index + " of " + records.length + ".");
		setTimeout(function() {
			alertNode.remove();
		}, 7000);
		form.find("#import_submit").show();
		form.find("#import_data").val('');
		_obj.loadUnsettedRecords();
	},

	setFileInput: function() {
		fileInput = form.find("#dataFile");
		fileInput.hide().on('change', function(e) {
			if (!e.target.files.length) return;
			var fr = new FileReader();
			fr.onload = function() {
				_obj.importXml(fr.result);
			}
			fr.readAsText(e.target.files[0]);
		});
	},

	importXml: function(data) {
		var s = data.indexOf('<Table');
		if(s == -1)
		{
			// _obj.importTxt(data);
			return;
		}
		var e = data.indexOf('</Table>') + 8;
		data = $(data.substr(s, e - s));
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
				'type': 'purchas',
				'import': dt
			};
			index++;
		});
		if(confirm("Items to import: " + records.length + ".")) _obj.start();
	},

	importTxt: function(data) {
		var rows = data.split("\n"), max = 0;
		for (var i = 0; i < rows.length; i++) {
			rows[i] = rows[i].trim().split(/\ +s*/);
			max = max < rows[i].length ? rows[i].length : max;
		}
		console.log(rows);
		console.log(max);
		var fields, start;
		for (var i = 0; i < rows.length; i++) {
			if(rows[i].length == max) {
				fields = rows[i];
				start = i++;
				break;
			}
		}
		var list = [];
		for (var i = start; i < rows.length; i++) {
			if(rows[i].length == max) {
				var el = [];
				for (var j = 0; j < max; j++) {
					el[fields[j]] = rows[i][j];
				}
				list[list.length] = el;
			}
		}
		console.log(list);
	},

	set: function(_form) {
		form = _form;
		form.find("#import_submit").click(function(e) {
			e.preventDefault();
			fileInput.click();
		});
		_obj.getSets();
		_obj.setFileInput();
	}

	}; // end _obj

	// Play!
	_obj.set($('#import_form'));

	return _obj;

}


$(document).ready(function(e) {

	maciShopImport();

});

})(jQuery);