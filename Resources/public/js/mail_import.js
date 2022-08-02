
(function($){

var maciPageImport = function (options) {

	var form, subscribers, index

	_obj = {

	saveSubscriber: function(data) {
		if(!data['mail'] || !data['locale']) {
			index++;
			_obj.saveNextSubscriber();
			return;
		}
		$.ajax({
			type: 'POST',
			data: {
				'data': {
					'new': {
						'section': 'mails',
						'entity': 'subscriber',
						'data': data
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

	saveNextSubscriber: function(_form) {
		if (subscribers.length <= index) {
			_obj.end();
		}
		_obj.saveSubscriber({ 'mail': subscribers[index].trim() });
		index++;
		form.find("#import_data").val("Importing: " + index + " of " + subscribers.length);
	},

	end: function() {
		form.find("#import_submit").show();
		form.find("#import_data").val("End.");
	},

	importTxt: function() {
		subscribers = form.find("#import_data").val().split("\n");
		for (var i = 0; i < subscribers.length; i++) {
			if(!subscribers[i]['country']) {
				subscribers[i]['country'] = form.find("#import_country").val();
			}
			if(!subscribers[i]['locale']) {
				subscribers[i]['locale'] = form.find("#import_locale").val();
			}
			if(!subscribers[i]['sex']) {
				subscribers[i]['sex'] = form.find("#import_sex_1").val();
			}
		}
		index = 0;
		_obj.saveNextSubscriber();
	},

	importXml: function() {
		data = $(form.find("#import_data").val()).find('Fornitori1');
		list = [];
		data.each(function(i, row) {
			row_data = {
				'country': form.find("#import_country").val(),
				'locale': form.find("#import_locale").val(),
				'sex': form.find("#import_sex_1").val(),
				'address': {}
			};
			$(row).children().each(function(j, field) {
				switch (field['nodeName']) {
					case 'AUTORIZZO':
						break;
					case 'CAP':
						row_data['address']['cap'] = $(field).text();
						break;
					case 'CELLULARE':
						row_data['mobile'] = $(field).text();
						break;
					case 'COGNOME':
						row_data['surname'] = $(field).text();
						break;
					case 'DATANASCITA':
						row_data['birthdate'] = $(field).text();
						break;
					case 'EMAIL':
						row_data['mail'] = $(field).text();
						break;
					case 'EMEIL':
						break;
					case 'IDFORNITORE':
						break;
					case 'INDIRIZZO':
						row_data['address']['address'] = $(field).text();
						break;
					case 'LOCALITà':
						row_data['address']['city'] = $(field).text();
						break;
					case 'CITTà':
						row_data['address']['state'] = $(field).text();
						break;
					case 'NEWSLETTER':
						row_data['newsletter'] = "1";
						break;
					case 'NOME':
						row_data['name'] = $(field).text();
						break;
					case 'SMS':
						row_data['sms'] = $(field).text();
						break;
					case 'TELEFONI':
						row_data['phone'] = $(field).text();
						break;
					case 'UOMO':
						row_data['sex'] = $(field).text();
						break;
				}
				row_data['address']['name'] = row_data['name'];
				row_data['address']['surname'] = row_data['surname'];
				row_data['address']['country'] = row_data['country'];
			});
			list[i] = row_data;
		});
		console.log(list);
	},

	set: function(_form) {
		form = _form;
		form.find("#import_submit_txt").click(function(e) {
			e.preventDefault();
			form.find("#import_submit").hide();
			// _obj.importTxt();
		});
		form.find("#import_submit_xml").click(function(e) {
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

	maciPageImport();

});

})(jQuery);
