$(document).foundation();
$(function() {
	$('.new')
	.on('click','button[data-purpose=step1]',function() {
		$(this).text('Loading ...');
		var msg = {};
		msg.type = 'error';
		if (Foundation.libs.abide.parse_patterns($('input[name=gdoc_id]'))[0]) {
			$('button[data-purpose=step1]').prop('disabled',true);
			$.ajax({
				url:'fauxly.php?ajax='+$('input[name=ajax]').val()+'&reason=checker&id='+$('input[name=gdoc_id]').val(),
				success:function(d){
					try {
						d = JSON.parse(d);
						d = d.headers[0];
						success = true;
					}
					catch (e) {
						msg.msg = 'FauxDB cannot find the specified sheet. Make sure the ID is correct and that the authorized account has access to the sheet. If you continue to have trouble, email dherman@ydr.com';
						alert_user(msg,true);
						success = false;
						$('button[data-purpose=step1]').prop('disabled',false);
					}
					if (success) {
						if ($msg) {
							$(msg).fadeOut().promise().done(function() { $msg.remove(); });
							$(msg) = false;
						}
						$('input[name=gdoc_id]').prop('disabled',true);
						scrollTo($('button[data-purpose=step1]'),$('.step2'));
						for (var i = 0; i < d.length; i++) {
							$target = $('ul.fields').eq(0);
							$('<li data-field='+i+'>'+d[i]+'</li>').appendTo($target);
						}
						$('ul.fields').sortable({connectWith:"ul.fields"}).disableSelection();
						$('input[name=ajax]').remove();
					}
				}
			});	
		}
		else {
			msg.msg = 'Please enter a Google Sheet ID';
			alert_user(msg);
		}
	})
	.on('click','button[data-purpose=step2]',function() {
		scrollTo($('button[data-purpose=step2]'),$('.step3'));
	})
	.on('click','button[data-purpose=submit]',function(e) {
		var msg = {};
		if (Foundation.libs.abide.parse_patterns($('input'))[0]) {
			var data = {};
			data.fields = {};
			$('input').each(function() {
				data[$(this).attr('name')] = $(this).val();
			});
			$('.fields').each(function() {
				data.fields[$(this).attr('data-purpose')] = [];
				fieldarr = data.fields[$(this).attr('data-purpose')];
				$(this).children('li').each(function(field) {
					fieldarr.push($(this).attr('data-field'));
				});
			})
			data = encodeURI(JSON.stringify(data));
			stuff = data;
			$.ajax({
				url: 'fauxly.php?purpose=add&data='+data,
				success: function(d) {
					d = JSON.parse(d);
					if (d.type == 'success') {
						window.location.href = 'admin.php';
					}
					else {
						alert_user(d,true);
					}
				}
			});
		}
		else {
			msg.type = 'error';
			msg.msg = 'Please make sure every field is filled out before submitting';
			alert_user(msg,true);
		}
	});
	$('.admin')
		.on('click','i.fi-x',function() {
			if (confirm('Are you sure you want to delete '+$(this).parents('tr').children().eq(0).text()+'?')) {
				$.ajax({
					url: 'fauxly.php?purpose=del_db&ajax='+$('.ajax').attr('data-ajax')+'&id='+$(this).parents('tr').attr('data-id'),
					success: function(d) {
						stuff = d;
						d = JSON.parse(d);
						if (d.action = 'refresh') {
							window.location.href = window.location.href;
						}
						alert_user(d);
					}
				})
			}
		});

});

function scrollTo(obj1,obj2) {
	$(obj1).hide();
	$(obj2).fadeIn().promise().done(function() {$(obj2).removeClass('hide'); });
	$("html,body").animate({ scrollTop: $(obj2).offset().top }, "slow");
};

function alert_user(msg,timeout) {
	if ($msg) {
		if ($msg.timeout) {
			clearTimeout($msg.timeout);
		}
		close_msg(true);
	}
	else {
		show_msg();
	}
	function show_msg() {
		timeout = (timeout) ? timeout : false;
		$msg = $('<div data-alert class="alert-box text-center '+msg.type+'">'+msg.msg+'<a href="#" class="close">&times;</a></div>').prependTo($('body'));
		$msg.on('click','a.close',function() {
			close_msg();
		});
		if (!timeout) {
			$msg.timeout = setTimeout(close_msg,3000);
		}
	}
	function close_msg(show) {
		$msg.fadeOut().promise().done(function() {
			$msg.remove();
			if (show) {
				show_msg();
			}
		});
	}
};

var stuff;
var $msg = false;