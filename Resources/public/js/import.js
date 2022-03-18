
(function($){

var maciShopImport = function (options) {

	var form, select, records, index, fileInput, alertNode, toLoad,

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
							'type': 'imprt'
						}
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
				for (var i = 0; i < d.list.length; i++) {
					$('<option/>').attr('value', d.list[i].id).text(d.list[i].id + ": " + d.list[i].label).appendTo(select);
					if (21 < select.find('option').length) break;
				}
			}
		});
	},

	setParent: function(data) {
		if (select.val() == "null") return;
		$.ajax({
			type: 'POST',
			data: {
				'data': {
					'add': {
						'section': 'records',
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
			data: {
				'ids': toLoad
			},
			url: '/record/load-unsetted-records',
			success: function(d,s,x) {
				console.log('End of Import and Load.');
			}
		});
	},

	saveRecord: function(data) {
		// if (0 < index) return _obj.end();;
		$.ajax({
			type: 'POST',
			data: {
				'data': {
					'new': {
						'section': 'records',
						'entity': 'record',
						'data': data
					}
				}
			},
			url: '/mcm/ajax',
			success: function(d,s,x) {
				_obj.setParent(d);
				toLoad[toLoad.length] = d.new.id;
				_obj.saveNext();
			},
			error: function(d,s,x) {
				console.log('Error!');
				_obj.saveNext();
			}
		});
	},

	saveNext: function(_form) {
		if (records.length <= index)
		{
			_obj.end();
			return;
		}
		index++;
		alertNode.text("Importing: " + index + " of " + records.length + ".");
		_obj.saveRecord(records[index]);
	},

	start: function(_form) {
		if (!records.length)
		{
			alert('No Data.');
			return;
		}
		alertNode = $("<div/>").addClass('alert alert-info mt-2').css('marginTop', '16px').appendTo(form.find("#dataFile").parent());
		index = -1;
		toLoad = [];
		_obj.saveNext();
	},

	end: function() {
		alertNode.text("Imported: " + index + " of " + records.length + ". End!");
		setTimeout(function() {
			alertNode.remove();
		}, 5000);
		form.find("#import_submit").show();
		form.find("#import_data").val('');
		_obj.loadUnsettedRecords();
	},

	importXml: function(data) {
		var s = data.indexOf('<Table');
		if(s == -1) return;
		var e = data.indexOf('</Table>') + 8;
		data = $(data.substr(s, e - s));
		var fields = [];
		data.find('Row').first().find('Cell').each(function(i, el) {
			if($(el).text().length) fields[i] = $(el).text();
			else fields[i] = false;
		});
		records = [];
		index = 0;
		var fieldsLen = data.find('Row').first().find('Cell').length - 1;
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
		// console.log(records);
		if(confirm("Items to import: " + records.length + "."))
			_obj.start();
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

	set: function(_form) {
		form = _form;
		select = form.find('#import_set');
		submit = form.find("#import_submit");
		getLabels = form.find('#getLabels');
		labelsPath = getLabels.attr('href');
		getReport = form.find('#getReport');
		reportPath = getReport.attr('href');
		select.change(function(e) {
			if (select.val() == 'null') submit.parent().hide();
			else
			{
				submit.parent().show();
				getLabels.attr('href', labelsPath + '?setId=' + select.val());
				getReport.attr('href', reportPath + '?setId=' + select.val());
			}
		}).change();
		submit.click(function(e) {
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
