
(function($){

var maciShopManager = function (options) {

	var form, alertNode,

	_obj = {

	sendCmd: function() {
		var cmd = select.val();
		$.ajax({
			type: 'POST',
			data: {
				'cmd': cmd
			},
			url: '/admin/order/manage/' + form.attr('data-id'),
			success: function(d,s,x) {
				console.log('S--> ' + cmd + ' <---');
				console.log(d);
				// window.location.reload();
			},
			error: function(d,s,x) {
				console.log('E--> ' + cmd + ' <---');
				console.log(d);
			}
		});
	},

	set: function(_form) {
		form = _form;
		select = form.find('#actions_select');
		submit = form.find('#submit');
		submit.click(function(e) {
			e.preventDefault();
			// if(!confirm("Confirm?")) return;
			_obj.sendCmd();
		});
	}

	}; // end _obj

	// Play!
	_obj.set($('#order_actions_form'));

	return _obj;

}


$(document).ready(function(e) {

	maciShopManager();

});

})(jQuery);
