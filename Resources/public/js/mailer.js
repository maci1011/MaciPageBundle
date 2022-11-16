
(function($){

var maciPage = function (options) {

	var _defaults = {},

		subscribers = false,
		_last_action,

		mailId, mail,
		selected_sub,
		recipients, selected_re,
		_send_stop,

		sending,

		_send_timeout = 2000 // Send Mails Interval

	;

	_obj = {

	getSubribersList: function()
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
			_obj.getSubribersList();
			return false;
		}

		return true;
	},

	resetMailData: function()
	{
		mailId = false;
		mail = false;
		sending = false;
		
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
				_obj.refreshMail();

				if (d.end || 99 < _send_stop)
				{
					_obj.stop();
					_obj.refreshMail();
					return;
				}

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
			_obj.showSubribers();
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

	showSubribers: function()
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

		selected_sub = {};
		var list = _obj.getSubribers();

		if (!list.length)
		{
			$('<h3/>').text('All Subscribers added.').appendTo('#content');
			return;
		}

		var ul = $('<ul/>').attr('class', 'navbar-nav ml-auto').appendTo('#content');
		ul.wrap('<nav/>').parent().attr('class', 'navbar navbar-dark py-0');

		for (var i = 0; i < list.length; i++)
		{
			$('<a/>').attr('class', 'nav-link').attr('href', '#').appendTo(ul).click(function(e) {
				e.preventDefault();
				$(this).parent().toggleClass('active');
				if ($(this).parent().hasClass('active'))
					selected_sub[$(this).attr('d-mail')] = $(this).attr('d-name');
				else
					selected_sub.delete($(this).attr('d-mail'));
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
			_obj.addSubribers();
		}).text('Add Subscribers').attr('class', 'btn btn-success ml-auto mt-3');

		$('<button/>').appendTo('#content').click(function(e) {
			e.preventDefault();
			_obj.addAllSubribers();
		}).text('Add All').attr('class', 'btn btn-success ml-2 mt-3');
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
		ul.wrap('<nav/>').parent().attr('class', 'navbar navbar-dark py-0');

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
			_obj.removeSubribers();
		}).text('Remove Subscribers').attr('class', 'btn btn-success ml-auto mt-3');

		$('<button/>').appendTo('#content').click(function(e) {
			e.preventDefault();
			_obj.removeAllSubribers();
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
		ul.wrap('<nav/>').parent().attr('id', 'sendingList').attr('class', 'navbar navbar-dark py-0');

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
			$('<button/>').attr('id', 'buttonSend').appendTo('#content').click(function(e) {
				e.preventDefault();
				$(this).remove();
				_obj.stop();
				_obj.showSendMailButton();
			}).text('Sending Mails...').attr('class', 'btn btn-info ml-auto mt-3');
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
		$.each(selected_sub, function(e, v) {
			i++;
		});
		return i;
	},

	addSubribers: function()
	{
		var savelist = {}, c = false;

		if (_obj.countMap(selected_sub) < 210)
		{
			savelist = selected_sub;
			selected_sub = {};
		}
		else
		{
			$.each(selected_sub, function(m, n) {
				if (_obj.countMap(savelist) < 200)
				{
					savelist[m] = n;
					selected_sub.delete(m);
				}
			});
			c = true;
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
				if (c)
				{
					_obj.addSubribers();
					return;
				}
				_obj.refreshMail();
			}
		});
	},

	addAllSubribers: function()
	{
		selected_sub = {};
		var list = _obj.getSubribers();

		if (!list.length)
			return;

		for (var i = 0; i < list.length; i++)
			selected_sub[list[i].mail] = list[i].name;

		_obj.addSubribers();
	},

	removeSubribers: function()
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
					_obj.removeSubribers();
					return;
				}
				_obj.refreshMail();
			}
		});
	},

	removeAllSubribers: function()
	{
		selected_re = [];
		var list = _obj.getRecipients();

		if (!list.length)
			return;

		for (var i = 0; i < list.length; i++)
			selected_re.push(list[i].mail);

		_obj.removeSubribers();
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

	getSubribers: function()
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
