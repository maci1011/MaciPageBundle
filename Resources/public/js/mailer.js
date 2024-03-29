
(function($){

var maciPage = function (options) {

	var _defaults = {},

		subscribers = false,
		locales,
		_last_action,

		mailId, mail,
		selected_sub,
		recipients, selected_re,
		_send_stop,

		search,
		locale,

		sending,

		_send_timeout = 2000 // Send Mails Interval

	;

	_obj = {

	getSubscribersList: function()
	{
		subscribers = [];
		$.ajax({
			type: 'POST',
			data: {
				'data': {
					'list': {
						'section': 'mails',
						'entity': 'subscriber',
						'filters': [{
							'field': 'removed',
							'value': false
						}]
					}
				}
			},
			url: '/mcm/ajax',
			success: function(d,s,x) {
				if (Array.isArray(d.list) && d.list.length)
				{
					subscribers = d.list;
					locales = [];
					for (var i = 0; i < subscribers.length; i++)
						if (!locales.includes(subscribers[i].locale))
							locales.push(subscribers[i].locale);
					_obj.refreshMail();
				} else {
					$('#content').html('');
					$('<h3/>').text('No Subscribers!').appendTo('#content');
				}
			}
		});
	},

	getMail: function(id)
	{
		if (!_obj.checkData(id))
			return false;

		$.ajax({
			type: 'POST',
			data: {
				'data': {
					'item': {
						'section': 'mails',
						'entity': 'mail',
						'id': mailId
					}
				}
			},
			url: '/mcm/ajax',
			success: function(d,s,x)
			{
				mail = d.item;

				if (mail.data)
				{
					if (mail.data.recipients)
					{
						recipients = [];
						$.each(mail.data.recipients, function(i, v) {
							recipients.push(v);
						});
					}
					else
						recipients = [];
				}

				_obj.showMenu();
				_last_action = window.location.hash;
				_obj.showLastAction();
			}
		});
	},

	refreshMail: function()
	{
		_obj.getMail(mailId);
	},

	checkData: function(id)
	{
		id = parseInt(id);

		if (id < 1)
			return false;

		_obj.resetMailData();

		mailId = id;

		if (subscribers === false || !subscribers.length)
		{
			_obj.getSubscribersList();
			return false;
		}

		return true;
	},

	resetMailData: function()
	{
		mailId = false;
		mail = false;
		sending = false;
		search = false;
		locale = false;
		
		$('#actions').html('');
		$('#content').html('');
	},

	resetMail: function(id) {
		$.ajax({
			type: 'POST',
			data: {
				'data': {
					'edit': {
						'section': 'mails',
						'entity': 'mail',
						'id': mailId,
						'data': {
							'resetData': null
						}
					}
				}
			},
			url: '/mcm/ajax',
			success: function(d,s,x) {
				alert('success!');
			}
		});
	},

	saveData: function()
	{
		var list = [];

		for (var i = 0; i < subscribers.length; i++)
		{
			if (recipients.includes(subscribers[i].id))
			{
				list[subscribers[i].mail] = subscribers[i].name;
			}
			// else if (sent.includes(subscribers[i].id))
			// {
			// 	list.push(_obj.getRecipient(subscribers[i].id));
			// }
		}

		var data = mail.data;
		data.recipients = list.length ? list : 'null';
		mail.data = data;

		// var data = {
		// 	'recipients': list
		// };
		// mail.data = data;
	},

	getNexts: function()
	{
		$.ajax({
			type: 'POST',
			data: {
				'id': mailId
			},
			url: '/mailer/get-nexts',
			success: function(d,s,x) {
				if (d.end)
				{
					_obj.showList([]);
					_obj.stop();
				}
				else
				{
					_obj.showList(d.list);
					_obj.showSendMailButton();
				}
			}
		});
	},

	sendNext: function()
	{
		if (!sending)
			return;

		$.ajax({
			type: 'POST',
			data: {
				'id': mailId
			},
			url: '/mailer/send-next',
			success: function(d,s,x)
			{
				_send_stop++;

				// _obj.getNexts();
				// _obj.refreshMail();

				if (d.end || 99 < _send_stop)
				{
					_obj.stop();
					_obj.refreshMail();
					return;
				}

				_obj.showSendMail();

				setTimeout(function() {
					_obj.sendNext();
				}, _send_timeout);
			}
		});
	},

	showMenu: function()
	{
		$('#actions').html('');

		$('<h3/>').text('Actions').appendTo('#actions');
		var actionsUl = $('<ul/>').attr('class', 'navbar-nav').appendTo('#actions');
		actionsUl.wrap('<nav/>').parent().attr('class', 'navbar navbar-dark py-0');

		$('<a/>').attr('class', 'nav-link').attr('href', '#subscribers')
		.text('Subscribers').appendTo(actionsUl).click(function(e) {
			actionsUl.children().removeClass('active');
			_obj.showSubscribers();
		}).wrap('<li/>').parent().attr('class', 'nav-item');

		$('<a/>').attr('class', 'nav-link').attr('href', '#recipients')
		.text('Recipients').appendTo(actionsUl).click(function(e) {
			actionsUl.children().removeClass('active');
			_obj.showRecipients();
		}).wrap('<li/>').parent().attr('class', 'nav-item');

		$('<a/>').attr('class', 'nav-link').attr('href', '#sendMail')
		.text('Send Mail').appendTo(actionsUl).click(function(e) {
			actionsUl.children().removeClass('active');
			_obj.showSendMail();
		}).wrap('<li/>').parent().attr('class', 'nav-item');

		$('<a/>').attr('class', 'nav-link').attr('href', '#sents')
		.text('Sended').appendTo(actionsUl).click(function(e) {
			actionsUl.children().removeClass('active');
			_obj.showSents();
		}).wrap('<li/>').parent().attr('class', 'nav-item');

		/*
		$('<a/>').attr('class', 'nav-link').attr('href', '#resetData')
		.text('Reset Mail').appendTo(actionsUl).click(function(e) {
			actionsUl.children().removeClass('active');
			if (confirm('Sure?')) {
				_obj.resetMail();
			}
		}).wrap('<li/>').parent().attr('class', 'nav-item');
		*/

		$('<h3/>').text('Mail').appendTo('#actions');
		var mailUl = $('<ul/>').attr('class', 'navbar-nav').appendTo('#actions');
		var sent = _obj.getSents();
		mailUl.wrap('<nav/>').parent().attr('class', 'navbar navbar-dark py-0');

		$('<span/>').html('Id: <b>' + mail.id + '</b>').attr('class', 'navbar-text').appendTo(mailUl).wrap('<li/>').parent().attr('class', 'nav-item');
		$('<span/>').html('Name: <b>' + mail.name + '</b>').attr('class', 'navbar-text').appendTo(mailUl).wrap('<li/>').parent().attr('class', 'nav-item');
		$('<span/>').html('Subject: <b>' + mail.subject + '</b>').attr('class', 'navbar-text').appendTo(mailUl).wrap('<li/>').parent().attr('class', 'nav-item');

		$('<span/>').html('Sended To: <b>' + sent.length + ' / ' + recipients.length + '</b>').attr('class', 'navbar-text').appendTo(mailUl).wrap('<li/>').parent().attr('class', 'nav-item');

		$('<a/>').attr('class', 'nav-link').attr('href', '/mailer/show/' + mail.token).attr('target', '_black')
		.html('<i class="fas fa-eye"></i> Show Template').appendTo(mailUl).wrap('<li/>').parent().attr('class', 'nav-item');
	},

	showLastAction: function()
	{
		if (_last_action == "#subscribers")
			$('a[href="#subscribers"]').click();

		else if (_last_action == "#recipients")
			$('a[href="#recipients"]').click();

		else if (_last_action == "#sendMail")
			$('a[href="#sendMail"]').click();

		else if (_last_action == "#sents")
			$('a[href="#sents"]').click();

		else $('a[href="#subscribers"]').click();
	},

	showSubscribers: function()
	{
		_obj.stop();

		$('a[href="#subscribers"]').parent().addClass('active');
		_last_action = "#subscribers";

		$('#content').html('');
		$('<h3/>').text('Subscribers:').appendTo('#content');

		if (subscribers.length === 0)
		{
			$('<h3/>').text('No Subscribers.').appendTo('#content');
			return;
		}

		selected_sub = [];
		var list, subsList = _obj.getSubscribers();

		if (!subsList.length)
		{
			$('<h3/>').text('All Subscribers added.').appendTo('#content');
			return;
		}

		if (search && search.length)
		{
			list = [];
			for (var i = 0; i < subsList.length; i++)
				if (((subsList[i].name && subsList[i].name.match(search)) || subsList[i].mail.match(search)) &&
					(!locale || locale == 'none' || subsList[i].locale == locale))
					list.push(subsList[i]);
		}
		else if (locale && locale != 'none')
		{
			list = [];
			for (var i = 0; i < subsList.length; i++)
				if (subsList[i].locale == locale)
					list.push(subsList[i]);
		}
		else list = subsList;

		if (list.length)
			_obj.printList(list);
		else
			$('<h3/>').text('No matches.').appendTo('#content');

		var bar = $('<div/>').addClass('select-bar').appendTo('#content');

		$('<button/>').appendTo(bar).click(function(e) {
			e.preventDefault();
			_obj.selectAll();
		}).text('Select All').attr('class', 'btn ml-auto mt-3');

		$('<button/>').appendTo(bar).click(function(e) {
			e.preventDefault();
			_obj.deselectAll();
		}).text('Deselect All').attr('class', 'btn ml-auto mt-3');

		bar = $('<div/>').addClass('filters-bar form-inline').appendTo('#content');

		$('<input id="search" type="text" placeholder="Search" />').change(function(e) {
			e.preventDefault();
			var v = $(this).val(), s = search;
			search = v;
			if (v != s)
				_obj.showSubscribers();
		}).attr('class', 'form-control ml-auto mt-3').val(search ? search : null).appendTo(bar).focus();

		var locSel = $('<select id="localesFilter" />').appendTo(bar).change(function(e) {
			e.preventDefault();
			var v = $(this).val(), l = locale;
			locale = v;
			if (v != l)
				_obj.showSubscribers();
		}).attr('class', 'form-control ml-2 mt-3');

		$('<option value="none" />').text('All Locales').appendTo(locSel);

		for (var i = 0; i < locales.length; i++)
			$('<option value="' + locales[i] + '" />').text(locales[i].toUpperCase()).appendTo(locSel);

		if (locale) locSel.val(locale);

		bar = $('<div/>').addClass('action-bar').appendTo('#content');

		$('<button/>').appendTo(bar).click(function(e) {
			e.preventDefault();
			_obj.addSubscribers();
		}).text('Add Selected').attr('class', 'btn btn-success ml-auto mt-3');

		$('<button/>').appendTo(bar).click(function(e) {
			e.preventDefault();
			_obj.addAllSubscribers();
		}).text('Add All').attr('class', 'btn btn-success ml-2 mt-3');
	},

	printList: function(list)
	{
		var ul = $('<ul/>').attr('class', 'navbar-nav ml-auto').appendTo('#content');
		ul.wrap('<nav/>').parent().attr('id', 'currentList').attr('class', 'navbar navbar-dark py-0');

		for (var i = 0; i < list.length; i++)
		{
			$('<a/>').attr('class', 'nav-link').attr('href', '#').appendTo(ul).click(function(e) {
				e.preventDefault();
				$(this).parent().toggleClass('active');
				if ($(this).parent().hasClass('active'))
					_obj.selectSubscriber($(this).attr('d-mail'));
				else
					_obj.deselectSubscriber($(this).attr('d-mail'));
			}).mouseenter(function (e) {
				$(this).text($(this).attr('d-mail'));
			}).mouseleave(function (e) {
				$(this).text($(this).attr('d-name'));
			}).attr('d-mail', list[i].mail).attr('d-name', list[i].name ? list[i].name : list[i].mail.split('@')[0]).mouseleave()
				.wrap('<li/>')
				.parent()
				.attr('class', 'nav-item')
			;
		}
	},

	showRecipients: function()
	{
		_obj.stop();

		$('a[href="#recipients"]').parent().addClass('active');
		_last_action = "#recipients";

		$('#content').html('');
		$('<h3/>').text('Recipients:').appendTo('#content');

		var list = _obj.getRecipients();
		if (!list.length)
		{
			$('<h3/>').text('No Recipients.').appendTo('#content');
			return;
		}

		var ul = $('<ul/>').attr('class', 'navbar-nav ml-auto').appendTo('#content');
		ul.wrap('<nav/>').parent().attr('id', 'currentList').attr('class', 'navbar navbar-dark py-0');

		selected_re = [];
		for (var i = 0; i < list.length; i++)
		{
			$('<a/>').attr('class', 'nav-link').attr('href', '#').appendTo(ul).click(function(e) {
				e.preventDefault();
				$(this).parent().toggleClass('active');
				if ($(this).parent().hasClass('active'))
					selected_re.push($(this).attr('d-mail'));
				else
					selected_re.splice(selected_re.indexOf($(this).attr('d-mail')), 1);
			}).mouseenter(function (e) {
				$(this).text($(this).attr('d-mail'));
			}).mouseleave(function (e) {
				$(this).text($(this).attr('d-name'));
			}).attr('d-mail', list[i].mail).attr('d-name', list[i].name ? list[i].name : list[i].mail.split('@')[0]).mouseleave()
				.wrap('<li/>')
				.parent()
				.attr('class', 'nav-item')
			;
		}

		$('<button/>').appendTo('#content').click(function(e) {
			e.preventDefault();
			_obj.removeSubscribers();
		}).text('Remove Selected').attr('class', 'btn btn-success ml-auto mt-3');

		$('<button/>').appendTo('#content').click(function(e) {
			e.preventDefault();
			_obj.removeAllSubscribers();
		}).text('Remove All').attr('class', 'btn btn-success ml-2 mt-3');
	},

	showSendMail: function()
	{
		$('a[href="#sendMail"]').parent().addClass('active');
		_last_action = "#sendMail";
		_obj.getNexts();
	},

	showList: function(list)
	{
		if (_last_action != "#sendMail")
			return;

		$('#content').html('');
		$('<h3/>').text('Next:').appendTo('#content');

		if (!list.length)
		{
			$('<h3/>').text('No Recipients.').appendTo('#content');
			return;
		}

		var ul = $('<ul/>').attr('class', 'navbar-nav ml-auto').appendTo('#content');
		ul.wrap('<nav/>').parent().attr('id', 'currentList').attr('class', 'navbar navbar-dark py-0');

		for (var i = 0; i < list.length; i++)
		{
			$('<a/>').attr('class', 'nav-link').attr('href', '#').appendTo(ul).mouseenter(function (e) {
				$(this).text($(this).attr('d-mail'));
			}).mouseleave(function (e) {
				$(this).text($(this).attr('d-name'));
			}).attr('d-mail', list[i].mail).attr('d-name', list[i].name ? list[i].name : list[i].mail.split('@')[0]).mouseleave()
				.wrap('<li/>')
				.parent()
				.attr('class', 'nav-item')
			;

			if (6 < ul.children().length)
			{
				$("<span/>").addClass('navbar-text').text('...').wrap('<li/>').attr('class', 'nav-item').appendTo(ul);
				break;
			}
		}
	},

	showSendMailButton: function()
	{
		if (_last_action != "#sendMail")
			return;

		if (sending)
		{
			var rl = _obj.getRecipients();
			$('<button/>').attr('id', 'buttonSend').appendTo('#content').click(function(e) {
				e.preventDefault();
				$(this).remove();
				_obj.stop();
				_obj.showSendMailButton();
			}).attr('class', 'btn btn-info ml-auto mt-3')
				.text('Sending Mails' + (
					100 < rl.length ? ' - Autostop: ' + _send_stop + ' / 100' : null
				) + ' - Click to Stop Now');
		}
		else
			$('<button/>').attr('id', 'buttonSend').appendTo('#content').click(function(e) {
				e.preventDefault();
				$(this).remove();
				_obj.play();
			}).text('Send Mails').attr('class', 'btn btn-success ml-auto mt-3');
	},

	showSents: function()
	{
		_obj.stop();
		$('a[href="#sents"]').parent().addClass('active');
		_last_action = "#sents";
		$('#content').html('');
		$('<h3/>').text('Sended To:').appendTo('#content');

		var list = _obj.getSents();
		if (!list.length)
		{
			$('<h3/>').text('No Recipients.').appendTo('#content');
			return;
		}

		var ul = $('<ul/>').attr('class', 'navbar-nav ml-auto').appendTo('#content');
		ul.wrap('<nav/>').parent().attr('class', 'navbar navbar-dark py-0');

		for (var i = 0; i < list.length; i++)
		{
			$('<a/>').attr('class', 'nav-link').attr('href', '#').appendTo(ul).mouseenter(function (e) {
				$(this).text($(this).attr('d-mail'));
			}).mouseleave(function (e) {
				$(this).text($(this).attr('d-name'));
			}).attr('d-mail', list[i].mail).attr('d-name', list[i].name ? list[i].name : list[i].mail.split('@')[0]).mouseleave()
				.wrap('<li/>')
				.parent()
				.attr('class', 'nav-item')
			;
		}
	},

	countMap: function(map)
	{
		var i = 0;
		$.each(map, function(e, v) {
			i++;
		});
		return i;
	},

	getSubscriber: function(mail)
	{
		var list = _obj.getSubscribers();

		for (var i = 0; i < list.length; i++)
			if (mail == list[i].mail) return list[i];

		return false;
	},

	addSubscribers: function()
	{
		var savelist = {}, _list = [];

		if (selected_sub.length < 210)
		{
			_list = selected_sub;
			selected_sub = [];
		}
		else
		{
			_list = selected_sub.slice(0, 200);
			selected_sub = selected_sub.slice(200, selected_sub.length);
		}

		for (var i = 0; i < _list.length; i++)
		{
			var e = _obj.getSubscriber(_list[i]);
			if (!e) continue;
			savelist[e['mail']] = e['name'];
		}

		$.ajax({
			type: 'POST',
			data: {
				'data': {
					'edit': {
						'section': 'mails',
						'entity': 'mail',
						'id': mailId,
						'data': {
							'addRecipients': savelist
						}
					}
				}
			},
			url: '/mcm/ajax',
			success: function(d,s,x) {
				if (0 < selected_sub.length)
				{
					_obj.addSubscribers();
					return;
				}
				_obj.refreshMail();
			}
		});
	},

	addAllSubscribers: function()
	{
		if (!confirm('Confirm?')) return;

		selected_sub = [];
		var list = _obj.getSubscribers();

		if (!list.length)
			return;

		for (var i = 0; i < list.length; i++)
			selected_sub.push(list[i].mail);

		_obj.addSubscribers();
	},

	selectSubscriber: function(mail)
	{
		if (-1 == selected_sub.indexOf(mail))
			selected_sub.push(mail);
	},

	deselectSubscriber: function(mail)
	{
		var i = selected_sub.indexOf(mail);
		if (-1 < i)
			selected_sub.splice(i, 1);
	},

	removeSubscribers: function()
	{
		var savelist = [];

		if (selected_re.length < 210)
		{
			savelist = selected_re;
			selected_re = [];
		}
		else
		{
			savelist = selected_re.slice(0, 200);
			selected_re = selected_re.slice(200, selected_re.length);
		}

		$.ajax({
			type: 'POST',
			data: {
				'data': {
					'edit': {
						'section': 'mails',
						'entity': 'mail',
						'id': mailId,
						'data': {
							'removeRecipients': savelist
						}
					}
				}
			},
			url: '/mcm/ajax',
			success: function(d,s,x) {
				if (0 < selected_re.length)
				{
					_obj.removeSubscribers();
					return;
				}
				_obj.refreshMail();
			}
		});
	},

	removeAllSubscribers: function()
	{
		if (!confirm('Confirm?')) return;

		selected_re = [];
		var list = _obj.getRecipients();

		if (!list.length)
			return;

		for (var i = 0; i < list.length; i++)
			selected_re.push(list[i].mail);

		_obj.removeSubscribers();
	},

	selectAll: function()
	{
		$('#currentList').find('a').each(function() {
			if (!$(this).parent().hasClass('active')) $(this).click();
		});
	},

	deselectAll: function()
	{
		$('#currentList').find('a').each(function() {
			if ($(this).parent().hasClass('active')) $(this).click();
		});
	},

	// getRecipientIndex: function(id)
	// {
	// 	for (var i = mail.data.recipients.length - 1; i >= 0; i--)
	// 	{
	// 		if (mail.data.recipients[i]['id'] == id) {
	// 			return i;
	// 		}
	// 	}
	// 	return false;
	// },

	// getRecipient: function(id)
	// {
	// 	var i = _obj.getRecipientIndex(id);
	// 	if (i) return mail.data.recipients[i];
	// 	return false;
	// },

	// getRecipientData: function(id)
	// {
	// 	var i = _obj.getRecipient(id);
	// 	if (i) return i['sent'];
	// 	return false;
	// },

	getSents: function()
	{
		var list = [];
		for (var i = recipients.length - 1; i >= 0; i--)
		{
			if (recipients[i]['sended'])
				list.push(recipients[i]);
		}
		return list;
	},

	getRecipients: function()
	{
		var list = [];

		for (var i = recipients.length - 1; i >= 0; i--)
		{
			if (!recipients[i]['sended'])
				list.push(recipients[i]);
		}
		return list;
	},

	hasRecipient: function(mail)
	{
		for (var i = recipients.length - 1; i >= 0; i--)
		{
			if (recipients[i].mail == mail)
				return true;
		}
		return false;
	},

	getSubscribers: function()
	{
		var list = [];
		for (var i = subscribers.length - 1; i >= 0; i--)
		{
			if (!_obj.hasRecipient(subscribers[i].mail))
				list.push(subscribers[i]);
		}
		return list;
	},

	play: function()
	{
		_send_stop = 0;
		sending = true;
		_obj.sendNext();
	},

	stop: function()
	{
		sending = false;
	}

	}; // end _obj

	// Play!
	_obj.getMail($('#mail').attr('data-id'));

	return _obj;

}


$(document).ready(function(e) {

	maciPage();

});

})(jQuery);
