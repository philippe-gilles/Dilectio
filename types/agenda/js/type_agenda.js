$(document).ready(function() {

	$(document).on("submit", ".dilectio-type-agenda-post-button-send", function() {
		var this_form = $(this);
		var donnees = $(this).serialize();
		$.ajax({
			type: "POST",
			url: "ajax/types/send",
			data: donnees,
			dataType: "json"
		}).done(function(data) {
			var valide = data["valide"];
			var message = data["msg"];
			if ((message !== undefined) && (message.length > 0)) {
				if (valide == false) {
					DILECTIO.toaster($("#toaster-fail"), message);
				}
				else {
					DILECTIO.toaster($("#toaster-success"), message);
				}
			}
		}).fail(function() {DILECTIO.toaster_fail_ajax();});
		return false;
	});

});
