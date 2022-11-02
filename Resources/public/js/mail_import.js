
(function($){

var maciPageImport = function (options) {

	var form, subscribers, index

	_obj = {

	saveSubscriber: function(data)
	{
		if(!data['mail'] || !data['locale'])
		{
			index++;
			_obj.saveNextSubscriber();
			return;
		}
		data['_CHECK_REPOSITORY'] = 'check';
		$.ajax({
			type: 'POST',
			data: {
				'data': {
					'new': {
						'section': 'mails',
						'entity': 'subscriber',
						'params': data
					}
				}
			},
			url: '/mcm/ajax',
			success: function(d,s,x) {
				_obj.saveNextSubscriber();
			},
			error: function(d,s,x) {
				form.find("#import_submit").show();
				form.find("#import_data").val("Error.");
			}
		});
	},

	saveNextSubscriber: function(_form)
	{
		if (subscribers.length <= index) {
			_obj.end();
		}
		_obj.saveSubscriber(subscribers[index]);
		index++;
		form.find("#import_data").val("Importing: " + index + " of " + subscribers.length);
	},

	end: function()
	{
		form.find("#import_submit").show();
		form.find("#import_data").val("End.");
	},

	importTxt: function()
	{
		var imdata = form.find("#import_data").val().split("\n");
		var locale = form.find("#import_locale").val();
		subscribers = [];

		for (var i = 0; i < imdata.length; i++)
		{
			var d = imdata[i].split(',');

			if (d.length == 1 && d[0].match(/@/))
			{
				subscribers.push({
					'mail': d[0],
					'locale': locale
				});
			}
			else if (d.length == 2 && (d[0].match(/@/) || d[1].match(/@/)))
			{
				subscribers.push(d[0].match(/@/) ? {
					'mail': d[0].trim(),
					'name': d[1].trim(),
					'locale': locale
				} : {
					'mail': d[1].trim(),
					'name': d[0].trim(),
					'locale': locale
				});
			}
			else if (d.length == 3 && (d[0].match(/@/) || d[1].match(/@/)))
			{
				subscribers.push(d[0].match(/@/) ? {
					'mail': d[0].trim(),
					'name': d[1].trim(),
					'locale': d[2].trim().length == 2 ? d[2].trim() : locale
				} : {
					'mail': d[1].trim(),
					'name': d[0].trim(),
					'locale': d[2].trim().length == 2 ? d[2].trim() : locale
				});
			}
		}

		index = 0;
		// console.log(subscribers);
		_obj.saveNextSubscriber();
	},

	importXml: function()
	{
		data = $(form.find("#import_data").val()).find('Fornitori1');
		list = [];
		data.each(function(i, row) {
			row_data = {
				'locale': form.find("#import_locale").val()
			};
			$(row).children().each(function(j, field) {
				switch (field['nodeName']) {
					case 'CAP':
						// row_data['address']['cap'] = $(field).text();
						break;
					case 'CELLULARE':
						// row_data['mobile'] = $(field).text();
						break;
					case 'COGNOME':
						// row_data['surname'] = $(field).text();
						break;
					case 'DATANASCITA':
						// row_data['birthdate'] = $(field).text();
						break;
					case 'INDIRIZZO':
						// row_data['address']['address'] = $(field).text();
						break;
					case 'LOCALITà':
						// row_data['address']['city'] = $(field).text();
						break;
					case 'CITTà':
						// row_data['address']['state'] = $(field).text();
						break;
					case 'NEWSLETTER':
						// row_data['newsletter'] = "1";
						break;
					case 'SMS':
						// row_data['sms'] = $(field).text();
						break;
					case 'TELEFONI':
						// row_data['phone'] = $(field).text();
						break;
					case 'UOMO':
						// row_data['sex'] = $(field).text();
						break;
					case 'EMAIL':
						row_data['mail'] = $(field).text();
						break;
					case 'NOME':
						row_data['name'] = $(field).text();
						break;
					case 'AUTORIZZO':
					case 'EMEIL':
					case 'IDFORNITORE':
						break;
				}
				// row_data['address']['name'] = row_data['name'];
				// row_data['address']['surname'] = row_data['surname'];
				// row_data['address']['country'] = row_data['country'];
			});
			list[i] = row_data;
		});
		console.log(list);
	},

	set: function(_form)
	{
		form = _form;
		form.find("#import_submit_txt").click(function(e) {
			e.preventDefault();
			form.find("#import_submit").hide();
			_obj.importTxt();
		});
		// form.find("#import_submit_xml").click(function(e) {
		// 	e.preventDefault();
		// 	form.find("#import_submit").hide();
		// 	_obj.importXml();
		// });
	},

	foo: function() {}

	}; // end _obj

	// Play!
	_obj.set($('#import_form'));

	return _obj;

}


$(document).ready(function(e) {

	maciPageImport();

});

})(jQuery);
