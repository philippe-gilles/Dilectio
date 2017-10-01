/*
 * Dilectio : JS page profil
 */

var DILECTIO = DILECTIO || {};

DILECTIO.identity = {
	iterations: 0,

	wait_for_avatar: function() {
		var avatar = $(".mdl-avatar__image");
		if (avatar.length == 0) {
			DILECTIO.identity.iterations += 1;
			if (DILECTIO.identity.iterations < 20) {
				setTimeout(DILECTIO.identity.wait_for_avatar, 150);
			}
			else {
				DILECTIO.identity.iterations = 0;
			}
		}
		else {
			DILECTIO.identity.iterations = 0;
			avatar.css("font-family", "'object-fit: cover;'");
			objectFitImages(".mdl-avatar__image");
			avatar.on("load", function () {objectFitImages(".mdl-avatar__image");});
		}
	},

	submit_profile: function() {
		var this_form = $(this);
		var form = this_form[0];
		var formData = new FormData(form);
		$.ajax({
			type: "POST",
			url: "ajax/identity/submit",
			data: formData,
			processData: false,
			contentType: false,
			dataType: "json"
		}).done(function(data) {
			var valide = data["valide"];
			if (valide == true) {
				window.location.reload();
			}
			else {
				var titre = data["titre"];
				var msg = data["msg"];
				$.alert({title: titre, content: msg, type: "red"});
			}
		}).fail(function() {DILECTIO.toaster_fail_ajax();});
		return false;
	}
}

$(document).ready(function() {
	/* Soumission du formulaire */
	$(".dilectio-principal").on("submit", ".dilectio-profile-form", DILECTIO.identity.submit_profile);

	/* Le polyfill ne peut être appliqué que lorsque MDL Ext a chargé l'image */
	setTimeout(DILECTIO.identity.wait_for_avatar, 150);
});