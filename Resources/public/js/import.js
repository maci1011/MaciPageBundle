
(function($){

var maciShopImport = function (options) {

	var form, select, records, fileInput, alertNode, saveditems,

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
							'value': 'imprt'
						}]
					}
				}
			},
			url: '/mcm/ajax',
			success: function(d,s,x) {
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

	loadUnsettedRecords: function() {
		$.ajax({
			type: 'POST',
			data: {
				'ids': saveditems
			},
			url: '/record/load-unsetted-records',
			success: function(d,s,x) {

				var endNode = $("<div/>").addClass('alert alert-success mt-2')
					.css('marginTop', '16px').appendTo(form)
					.text("End of Import and Load.");

				setTimeout(function() {
					endNode.remove();
				}, 5000);

			}
		});
	},

	saveRecords: function() {
		// if (0 < index) return _obj.end();

		var savelist = [];

		if (records.length < 50)
		{
			savelist = records;
			records = [];
		}
		else
		{
			savelist = records.slice(0, 47);
			records = records.slice(47, records.length);
		}

		$.ajax({
			type: 'POST',
			data: {
				'data': {
					'new': {
						'section': 'records',
						'entity': 'record',
						'params': savelist,
						'relations': {
							'parent': select.val()
						}
					}
				}
			},
			url: '/mcm/ajax',
			success: function(d,s,x) {
				// _obj.setParent(d);
				_obj.end(d);
			},
			error: function(d,s,x) {
				console.log('Error!');
				_obj.end(false);
			}
		});
	},

	sendCmd: function(cmd) {
		// if (0 < index) return _obj.end();
		$.ajax({
			type: 'POST',
			data: {
				'cmd': cmd,
				'setId': select.val()
			},
			url: '/record/load-unsetted-records',
			success: function(d,s,x) {
				console.log('---> ' + cmd + ' <---');
				console.log(d);
			},
			error: function(d,s,x) {
				console.log('---> ' + cmd + ' <---');
				console.log('Debug Records Error!');
			}
		});
	},

	start: function(_form) {
		if (!records.length)
		{
			alert('No Data.');
			return;
		}

		alertNode = $("<div/>").addClass('alert alert-info mt-2').css('marginTop', '16px').appendTo(form)
			.text("Importing " + records.length + " records...");

		select.parent().hide();
		submit.parent().hide();

		saveditems = [];
		_obj.saveRecords();
	},

	end: function(data) {
		if (!data || !data.new.success)
		{
			alertNode.text("Error. End!");
			setTimeout(function() {
				alertNode.remove();
			}, 5000);
			return;
		}

		for (var i = 0; i < data.new.list.length; i++)
		{
			if (data.new.list[i].success) saveditems.push(data.new.list[i].id);
		}

		if (0 < records.length)
		{
			var len = saveditems.length + records.length;
			alertNode.text("Importing: " + saveditems.length + " of " + len + " records...");
			_obj.saveRecords();
			return;
		}

		select.parent().show();
		submit.parent().show();
		form.find("#import_data").val('');

		alertNode.text("Imported: " + saveditems.length + ". End!");
		setTimeout(function() {
			alertNode.remove();
		}, 5000);

		_obj.loadUnsettedRecords();
	},

	importXml: function(data) {
		var s = data.indexOf('<Table');
		if(s == -1) return;
		var e = data.indexOf('</Table>') + 8;
		data = $(data.substr(s, e - s));

		var fields = false, rows = data.find('Row');
		records = [];

		for (var i = 0; i < rows.length; i++)
		{
			var cells = rows.first().find('Cell');
			rows = rows.not(':first');
			if (cells.length < 4) continue;
			fields = [];
			cells.each(function(i, el)
			{
				if($(el).attr('ss:Index') == '1024') return;
				ss = parseInt($(el).attr('ss:Index')) - 1;
				while(fields.length < ss) fields.push('field_' + fields.length);
				fields.push(
					$(el).text().trim().length ? $(el).text().trim() : 'field_' + fields.length
				);
			});
			break;
		}

		if (!rows.length)
		{
			alert('No Data.');
			return;
		}

		rows.each(function(ri, row)
		{
			if($(row).find('Cell').length < 4) return;

			var index = 0, dt = {};

			$(row).find('Cell').each(function(i, el)
			{
				ss = parseInt($(el).attr('ss:Index')) - 1;
				if(ss == 1023) return;
				if (-1 < ss && index < ss) index = ss;
				while (fields.length <= index) fields.push('field_' + (fields.length - 1));
				if($(el).text().trim().length)
					dt[fields[index]] = $(el).text().trim();
				index++;
			});

			records.push({
				'type': 'purchas',
				'import': dt
			});
		});

		console.log(records);

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
		select = form.find('#import-set');
		submit = form.find("#import-submit");
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
		form.find('#check-qta').click(function(e) {
			e.preventDefault();
			_obj.sendCmd('check_qta');
		});
		form.find('#reset-qta').click(function(e) {
			e.preventDefault();
			if(!confirm("Confirm?")) return;
			_obj.sendCmd('reset_qta');
		});
		form.find('#getNF-order').click(function(e) {
			e.preventDefault();
			_obj.sendCmd('get_nf');
		});
		form.find('#resetNF-order').click(function(e) {
			e.preventDefault();
			if(!confirm("Confirm?")) return;
			_obj.sendCmd('reset_nf');
		});
		form.find('#reload-order').click(function(e) {
			e.preventDefault();
			if(!confirm("Confirm?")) return;
			_obj.sendCmd(null);
		});
		form.find('#reload-products').click(function(e) {
			e.preventDefault();
			if(!confirm("Confirm?")) return;
			_obj.sendCmd('reload_pr');
		});
		form.find('#reload-nf-records').click(function(e) {
			e.preventDefault();
			if(!confirm("Confirm?")) return;
			_obj.sendCmd('reload_nf_recs');
		});
		form.find('#reload-records').click(function(e) {
			e.preventDefault();
			if(!confirm("Confirm?")) return;
			_obj.sendCmd('reload_recs');
		});
		form.find('#version').click(function(e) {
			e.preventDefault();
			if(!confirm("Confirm?")) return;
			_obj.sendCmd('version');
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
