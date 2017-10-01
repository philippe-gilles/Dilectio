/*
 * Dilectio : JS général
 */

var DILECTIO = DILECTIO || {};

DILECTIO = {
	timer_id: 0,
	
	callback_timeout: function() {
		var panel = $("#notification-panel");
		if (panel.length > 0) {
			var time = parseInt(panel.attr("data-time"));
			DILECTIO.update_notifications(time);
			DILECTIO.start_timeout();
		}
	},

	start_timeout: function() {
		DILECTIO.timer_id = window.setTimeout(DILECTIO.callback_timeout, 10000);
	},
	
	stop_timeout: function() {
		if (DILECTIO.timer_id > 0) {
			window.clearTimeout(DILECTIO.timer_id);
			DILECTIO.timer_id = 0;
		}
	},
	
	update_badge: function(val, starting_from) {
		var badge = $("#notification");
		if (badge.length > 0) {
			var old_val = badge.attr("data-badge");
			if ((typeof old_val !== typeof undefined) && (old_val !== false)) {
				val += parseInt(old_val);
			}
			if (val > 0) {
				badge.attr("data-badge", val);
			}
			else {
				badge.removeAttr("data-badge");
			}
		}
	},
	
	update_panel: function(html) {
		var title = $("#notification-title");
		if (title.length > 0) {
			title.after(html);
		}
	},
	
	update_time: function(time) {
		var panel = $("#notification-panel");
		if (panel.length > 0) {
			panel.attr("data-time", time);
		}
	},

	update_notifications: function(starting_from) {
		var starting_from = (typeof starting_from !== "undefined")?starting_from:0;
		$.ajax({
			type: "POST",
			url: "ajax/general/notifications",
			data: {starting_from: starting_from},
			dataType: "json"
		}).done(function(data) {
			var valide = data["valide"];
			if (valide == true) {
				var val = parseInt(data["count"]);
				DILECTIO.update_badge(val, starting_from);
				var html = data["html"];
				DILECTIO.update_panel(html);
				var time = data["time"];
				DILECTIO.update_time(time);
			}
		}).fail(function() {DILECTIO.toaster_fail_ajax();});
	},

	notification_is_open: function() {
		var ret = $(".mdl-layout__drawer-right").hasClass("notification-active");
		return ret;
	},

	open_notification: function() {
		$(".mdl-layout__drawer-right").addClass("notification-active");
		$("#notification").css("cursor", "default");
	},
	
	close_notification: function() {
		$(".mdl-layout__drawer-right").removeClass("notification-active");
		$.ajax({
			type: "POST",
			url: "ajax/general/read",
			dataType: "json"
		}).done(function(data) {
			var valide = data["valide"];
			if (valide == true) {
				var badge = $("#notification");
				if (badge.length > 0) {
					badge.removeAttr("data-badge");
				}
			}
			$("#notification").css("cursor", "pointer");
		}).fail(function() {DILECTIO.toaster_fail_ajax();});
	},
	
	toaster: function(elem, message) {
		if (elem.length > 0) {
			var data = {message: message};
			var toast_elem = elem[0];
			toast_elem.MaterialSnackbar.showSnackbar(data);
		}
	},
	
	toaster_success: function(message) {
		DILECTIO.toaster($("#toaster-success"), message);
	},

	toaster_fail: function(message) {
		DILECTIO.toaster($("#toaster-fail"), message);
	},
	
	toaster_fail_ajax: function() {
		DILECTIO.toaster($("#toaster-fail"), error_ajax_script);
	},
	
	alert_return_ajax: function(data) {
		var titre = data["titre"];
		var msg = data["msg"];
		if (msg.length > 0) {$.alert({title: titre, content: msg, type: "red"});}
	}
}

$(document).ready(function() {
	/* Notifications */
	$("#notification").click(function() {
		if (DILECTIO.notification_is_open()) {
			DILECTIO.close_notification();
		}
		else {
			DILECTIO.open_notification();
		}
	});

	$(".mdl-layout__obfuscator-right").click(function() {
		if (DILECTIO.notification_is_open()) {
			DILECTIO.close_notification();
		}
		else {
			DILECTIO.open_notification();
		}
	});
	
	/* Hrefs dans les items de menu (mdl déconseille les ancres : ???) */
	$(document).on("click", ".dilectio-mdl-menu-item", function() {
		var href = $(this).data("href");
		if (href.length > 0) {window.location = href;}
	});
	
	DILECTIO.update_notifications();
	DILECTIO.start_timeout();
});
