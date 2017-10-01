/*
 * Dilectio : JS page login
 */

var DILECTIO = DILECTIO || {};

DILECTIO.login = {
	open: function() {
		var attr_id = $(this).attr("id");
		var id = parseInt(attr_id.replace("image-", ""));
		if (id > 0) {
			$("#login").animate({width:"200px"}, 150);
			$("#login").find(".dilectio_panneau_login_image").not("#profil-"+id).css("display", "none");
			$("#image-"+id).removeClass("dilectio_panneau_lien_image").addClass("dilectio_panneau_lien_image_visited");
			$("#login-form-"+id).css("display", "block");
			$("input[type='password']").focus();
		}
		return false;
	},
	
	close: function() {
		var attr_id = $(this).attr("id");
		var id = parseInt(attr_id.replace("image-", ""));
		if (id > 0) {
			$("#login").css("width", "auto");
			$("#login").find(".dilectio_panneau_login_image").css("display", "inline-block");
			$("#image-"+id).removeClass("dilectio_panneau_lien_image_visited").addClass("dilectio_panneau_lien_image");
			$(".dilectio_form_login").css("display", "none");
		}
		return false;
	},
	
	submit: function() {
		var donnees = $(this).serialize();
		$.ajax({
			type: "POST",
			url: "ajax/login/login",
			data: donnees,
			dataType: "json"
		}).done(function(data) {
			var valide = data["valide"];
			if (valide == false) {
				var titre = data["titre"];
				var msg = data["msg"];
				$.alert({title: titre, content: msg, type: "red"});
			} else {
				window.location = "home";
			}
		}).fail(function() {
			alert("ERREUR : Script login en échec ou introuvable");
		});
		return false;
	}
}
		
$(document).ready(function() {
	/* Clic pour ouverture du formulaire / mot de passe */
	$("#login").on("click", ".dilectio_panneau_lien_image", DILECTIO.login.open);

	/* Clic pour fermeture du formulaire / mot de passe */
	$("#login").on("click", ".dilectio_panneau_lien_image_visited", DILECTIO.login.close);

	/* Soumission du formulaire / mot de passe */
	$("form").submit(DILECTIO.login.submit);
});