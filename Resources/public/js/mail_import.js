
(function($){

var maciPageImport = function (options) {

	var form, subscribers, index

	_obj = {

	checkUsers: function()
	{
		if (!confirm('LOCALE setted???'))
			return;

		$.ajax({
			type: 'POST',
			data: {
				'set_locale': form.find("#import_locale").val()
			},
			url: '/mailer/check_subscriptions',
			success: function(d,s,x) {
				console.log(d.messages);
			},
			error: function(d,s,x) {}
		});
	},

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
				var fld = field['nodeName'].toLowerCase();
				switch (fld)
				{
					case 'mail':
					case 'email':
						row_data['mail'] = $(field).text();
						break;
					case 'name':
					case 'nome':
						row_data['name'] = $(field).text();
						break;
				}
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
		form.find("#checkSubscriptions").click(function(e) {
			e.preventDefault();
			_obj.checkUsers();
		});
	}

	}; // end _obj

	// Play!
	_obj.set($('#import_form'));

	return _obj;

}


$(document).ready(function(e) {

	maciPageImport();

});

})(jQuery);
