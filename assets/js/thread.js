/*
 * Dilectio : JS page conversation
 */

var DILECTIO = DILECTIO || {};

DILECTIO.thread = {

	show_waiting: function(form) {
		form.find(".dilectio-new-post-waiting-container").prepend("<div class='dilectio-new-post-waiting'></div>");
		form.find("button[type='submit']").attr("disabled", "disabled");
	},

	hide_waiting: function(form) {
		form.find(".dilectio-new-post-waiting-container").remove(".dilectio-new-post-waiting");
		form.find("button[type='submit']").removeAttr("disabled");
	},
	
	goto_top: function() {
		var container = $('.mdl-layout__content');
		container.animate({scrollTop: 0}, 400);
	},

	goto_bottom: function() {
		var container = $('.mdl-layout__content');
		var bottom = container[0].scrollHeight;
		container.animate({scrollTop: bottom}, 400);
	},

	scroll_to_new: function(callback, thread_id, type_id) {
		var container = $(".dilectio-thread-container");
		if (container.length == 1) {
			var fragment = $("#anchor-post-new");
			if (fragment.length == 1) {
				var offset_container = container.offset().top;
				var offset_fragment = fragment.offset().top;
				var offset = offset_fragment - offset_container;
				$(".mdl-layout__content").animate({scrollTop: offset}, 500, callback(thread_id, type_id));
			}
		}
	},

	scroll_to_fragment: function () {
		var container = $(".dilectio-thread-container");
		if (container.length == 1) {
			var fragment = container.data("fragment");
			if (fragment !== undefined) {
				var no_fragment = parseInt(fragment);
				if (no_fragment > 0) {
					var fragment = $("#anchor-post-"+no_fragment);
					if (fragment.length == 1) {
						var offset_container = container.offset().top;
						var offset_fragment = fragment.offset().top;
						var offset = offset_fragment - offset_container;
						$(".mdl-layout__content").animate({scrollTop: offset}, 500);
					}
				}
			}
		}
	},

	load_form: function (thread_id, type_id) {
		var form_container = $(".dilectio-new-post-wrapper");
		if (form_container.length == 1) {
			form_container.html("<div class='dilectio-new-post-waiting'></div>");
			$.ajax({
				type: "POST",
				url: "ajax/thread/form",
				data: {thread_id: thread_id, type_id: type_id},
				dataType: "json"
			}).done(function(data) {
				var valide = data["valide"];
				if (valide == true) {
					var html = data["html"];
					form_container.html(html);
					/* IMPORTANT : Applique MDL aux composants chargés via AJAX */
					componentHandler.upgradeDom();
				}
				else {
					form_container.empty();
				}
			}).fail(function() {form_container.empty();DILECTIO.toaster_fail_ajax();});
		}
		return false;
	},

	load_cards: function(thread_id) {
		$.ajax({
			type: "POST",
			url: "ajax/thread/load",
			data: {thread_id: thread_id},
			dataType: "json"
		}).done(function(data) {
			var valide = data["valide"];
			if (valide == true) {
				var html = data["html"];
				var container = $(".dilectio-thread-container");
				if (container.length == 1) {
					container.html(html);
					/* IMPORTANT : Applique MDL aux composants chargés via AJAX */
					componentHandler.upgradeDom();
					DILECTIO.thread.scroll_to_fragment();
				}
			}
		}).fail(function() {DILECTIO.toaster_fail_ajax();});
		return false;
	},

	load_form_type: function() {
		/* HACK pour refermer le drawer sur petits écrans                         */
		/* $('.mdl-layout').MaterialLayout.toggleDrawer() ne fonctionne pas : ??? */
		$(".mdl-layout__obfuscator.is-visible").trigger("click");
		var container = $(".dilectio-thread-container");
		if (container.length == 1) {
			var attr_id = container.attr("id");
			var thread_id = parseInt(attr_id.replace("dilectio-thread-", ""));
			if (thread_id > 0) {
				var attr_id = $(this).attr("id");
				var type_id = parseInt(attr_id.replace("nav-type-", ""));
				if (type_id > 0) {DILECTIO.thread.scroll_to_new(DILECTIO.thread.load_form, thread_id, type_id);}
			}
		}
		return false;
	},

	cancel_form_type: function() {
		$(".dilectio-new-post-wrapper").slideUp(500, function() {$(this).empty().css("display", "block");});
	},
	
	gift: function() {
		var is_checked = $(this).is(":checked");
		var label = $(this).closest(".dilectio-new-post-form-gift-label");
		if (is_checked) {
			label.addClass("mdl-color-text--accent");
		}
		else {
			label.removeClass("mdl-color-text--accent");
		}
	},

	submit_form_type: function() {
		var this_form = $(this);
		DILECTIO.thread.show_waiting(this_form);
		var form = this_form[0];
		var formData = new FormData(form);
		$.ajax({
			type: "POST",
			url: "ajax/thread/submit",
			data: formData,
			processData: false,
			contentType: false,
			dataType: "json"
		}).done(function(data) {
			DILECTIO.thread.hide_waiting(this_form);
			var valide = data["valide"];
			if (valide == true) {
				var new_container = $(".dilectio-new-post-wrapper");
				if (new_container.length == 1) {
					new_container.empty();
				}
				var html = data["html"];
				var new_anchor = $("#anchor-post-new");
				if (new_anchor.length == 1) {
					new_anchor.before(html);
					/* IMPORTANT : Applique MDL aux composants chargés via AJAX */
					componentHandler.upgradeDom();
				}
				var post_id = data["post_id"];
			}
			else {
				var type = data["type"];
				var msg = data["msg"];
				if (type === "alert") {
					var titre = data["titre"];
					$.alert({title: titre, content: msg, type: "red", 
						buttons: {
							ok: function () {
								var new_container = $(".dilectio-new-post-wrapper");
								if (new_container.length == 1) {
									new_container.empty();
								}
							}
						}
					});
				}
				else if (type === "toast") {
					DILECTIO.toaster_fail(msg);
				}
			}
		}).fail(function() {DILECTIO.toaster_fail_ajax();});
		return false;
	},

	save_form_type: function() {
		var this_form = $(this);
		DILECTIO.thread.show_waiting(this_form);
		var form = this_form[0];
		var formData = new FormData(form);
		$.ajax({
			type: "POST",
			url: "ajax/thread/save",
			data: formData,
			processData: false,
			contentType: false,
			dataType: "json"
		}).done(function(data) {
			DILECTIO.thread.hide_waiting(this_form);
			var valide = data["valide"];
			if (valide == true) {
				this_form.parent().html(data["html"]);
				/* IMPORTANT : Applique MDL aux composants chargés via AJAX */
				componentHandler.upgradeDom();		
			}
			else {
				window.location.reload();
			}
		}).fail(function() {DILECTIO.toaster_fail_ajax();});
		return false;
	},

	submit_form_thread: function() {
		var donnees = $(this).serialize();
		var container = $(".dilectio-thread-container");
		if (container.length == 1) {
			var attr_id = container.attr("id");
			var thread_id = parseInt(attr_id.replace("dilectio-thread-", ""));
			if (thread_id == 0) {
				$.ajax({
					type: "POST",
					url: "ajax/thread/new",
					data: donnees,
					dataType: "json"
				}).done(function(data) {
					var valide = data["valide"];
					if (valide == true) {
						var thread_id = parseInt(data["thread_id"]);
						window.location = "thread-"+thread_id;
					}
					else {DILECTIO.alert_return_ajax(data["msg"]);}
				}).fail(function() {DILECTIO.toaster_fail_ajax();});
			}
			else {
				$.ajax({
					type: "POST",
					url: "ajax/thread/update",
					data: donnees,
					dataType: "json"
				}).done(function(data) {
					var valide = data["valide"];
					if (valide == true) {
						$("#thread-title-disabled").val($("#thread-title").val());
						$(".dilectio-thread-info").removeClass("dilectio-thread-form-hidden").addClass("dilectio-thread-form-visible");
						$(".dilectio-thread-form").removeClass("dilectio-thread-form-visible").addClass("dilectio-thread-form-hidden");
						var toast = data["toast"];
						DILECTIO.toaster_success(toast);
					}
					else {DILECTIO.alert_return_ajax(data["msg"]);}
				}).fail(function() {DILECTIO.toaster_fail_ajax();});
			}
		}
		return false;
	},
	
	toggle_form_edit: function() {
		$(".dilectio-thread-form").removeClass("dilectio-thread-form-hidden").addClass("dilectio-thread-form-visible");
		$(".dilectio-thread-info").removeClass("dilectio-thread-form-visible").addClass("dilectio-thread-form-hidden");
	},
	
	cancel_form_edit: function() {
		$(".dilectio-thread-info").removeClass("dilectio-thread-form-hidden").addClass("dilectio-thread-form-visible");
		$(".dilectio-thread-form").removeClass("dilectio-thread-form-visible").addClass("dilectio-thread-form-hidden");
		$("#thread-title").val($("#thread-title-disabled").val());
	},
	
	post_edit: function() {
		var attr_id = $(this).attr("id");
		var post_id = parseInt(attr_id.replace("edit-", ""));
		if (post_id >= 0) {
			$(".dilectio-new-post-form").remove();
			$.ajax({
				type: "POST",
				url: "ajax/thread/edit",
				data: {post_id : post_id},
				dataType: "json"
			}).done(function(data) {
				var valide = data["valide"];
				if (valide == true) {
					var html = data["html"];
					form_container = $("#article-"+post_id);
					if (form_container.length == 1) {
						form_container.parent().html(html);
						/* IMPORTANT : Applique MDL aux composants chargés via AJAX */
						componentHandler.upgradeDom();
					}
				}
				else {alert("NOK");}
			}).fail(function() {DILECTIO.toaster_fail_ajax();});
		}
	},

	post_delete: function() {
		var attr_id = $(this).attr("id");
		var post_id = parseInt(attr_id.replace("delete-", ""));
		if (post_id >= 0) {

			$.confirm({
				title: confirm_title,
				content: confirm_msg_delete,
				type: "orange",
				buttons: {
					Yes: {
						text: confirm_yes,
						action: function () {
							$.ajax({
								type: "POST",
								url: "ajax/thread/post_delete",
								data: {post_id: post_id},
								dataType: "json"
							}).done(function(data) {
								var valide = data["valide"];
								alert(valide);
							}).fail(function() {DILECTIO.toaster_fail_ajax();});
						}
					},
					No: {text: confirm_no}
				},
				useBootstrap: false
			});

		}
	},

	change_emotion: function() {
		var parent_ul = $(this).closest("ul.dilectio-post-emotions-menu");
		if (parent_ul.length == 1) {
			var post_attr_id = parent_ul.attr("id");
			var post_id = parseInt(post_attr_id.replace("menu-emotion-", ""));
			if (post_id > 0) {
				var attr_id = $(this).attr("id");
				var emotion_id = parseInt(attr_id.replace("emotion-", ""));
				if (emotion_id >= 0) {
					$.ajax({
						type: "POST",
						url: "ajax/thread/emotion",
						data: {post_id: post_id, emotion_id: emotion_id},
						dataType: "json"
					}).done(function(data) {
						var valide = data["valide"];
						if (valide) {
							var dest_id = parseInt(data["post_id"]);
							var html = data["html"];
							var dest = $("#post-emotion-"+dest_id);
							if (dest.length == 1) {
								dest.html(html);
								/* IMPORTANT : Applique MDL aux composants chargés via AJAX */
								componentHandler.upgradeDom();
							}
						}
					}).fail(function() {DILECTIO.toaster_fail_ajax();});
				}
			}
		}
	},

	change_favorite: function() {
		var attr_id = $(this).attr("id");
		var post_id = parseInt(attr_id.replace("toggle-favorite-", ""));
		if (post_id > 0) {
			var is_checked = ($(this).is(":checked"))?1:0;
			$.ajax({
				type: "POST",
				url: "ajax/thread/favorite",
				data: {post_id: post_id, is_checked: is_checked},
				dataType: "json"
			}).done(function(data) {
				var valide = data["valide"];
				if (valide == true) {
					var fav_id = parseInt(data["fav_id"]);
					var icone = $("#toggle-favorite-"+fav_id).siblings(".material-icons");
					if (icone.length > 0) {
						icone.toggleClass("dilectio-post-favori-faux").toggleClass("dilectio-post-favori-vrai");
					}
				}
			}).fail(function() {DILECTIO.toaster_fail_ajax();});
		}
	},
	
	open_curtain: function() {
		var this_handle = $(this);
		this_handle.attr("disabled", "disabled");
		var attr_id = this_handle.attr("id");
		var post_id = parseInt(attr_id.replace("gift-", ""));
		if (post_id > 0) {
			$.ajax({
				type: "POST",
				url: "ajax/thread/curtain",
				data: {post_id: post_id},
				dataType: "json"
			}).done(function(data) {
				this_handle.animate({opacity: 0}, 2500, function() {this_handle.remove();});
				$("#curtain-"+post_id).animate({height: 0}, 3000);
			}).fail(function() {DILECTIO.toaster_fail_ajax();});
		}
	}
}

$(document).ready(function() {
	/* Gestion du formulaire thread */
	$("#thread-categorie").dilectioNiceSelect();
	$(".dilectio-thread-edit").click(DILECTIO.thread.toggle_form_edit);
	$("#thread-cancel").click(DILECTIO.thread.cancel_form_edit);
	$(".dilectio-thread-form").submit(DILECTIO.thread.submit_form_thread);
	
	/* Boutons top/bottom */
	$(document).on("click", "#goto-top", DILECTIO.thread.goto_top);
	$(document).on("click", "#goto-bottom", DILECTIO.thread.goto_bottom);
	
	/* Formulaire pour un nouveau post */
	$(".dilectio-navigation-type-post").click(DILECTIO.thread.load_form_type);

	/* Annulation d'un nouveau post */
	$(".dilectio-principal-body").on("click", "#new-post-cancel", DILECTIO.thread.cancel_form_type);

	/* Gestion du bouton "gift" */
	$(".dilectio-principal-body").on("change", "#new-post-gift", DILECTIO.thread.gift);

	/* Soumission d'un nouveau post */
	$(".dilectio-principal-body").on("submit", ".dilectio-new-post-form", DILECTIO.thread.submit_form_type);

	/* Soumission d'un post existant */
	$(".dilectio-principal-body").on("submit", ".dilectio-edit-post-form", DILECTIO.thread.save_form_type);

	/* Annulation d'un post existant */
	$(".dilectio-principal-body").on("click", "#edit-post-cancel", function() {alert("cancel");});

	/* Actions */
	$(".dilectio-principal-body").on("click", ".dilectio-post-edit", DILECTIO.thread.post_edit);
	$(".dilectio-principal-body").on("click", ".dilectio-post-delete", DILECTIO.thread.post_delete);

	/* Menu émotions */
	$(".dilectio-principal-body").on("click", ".dilectio-post-emotions-menu-icone", DILECTIO.thread.change_emotion);

	/* Bouton favori */
	$(".dilectio-principal-body").on("change", ".dilectio-post-favori-icone", DILECTIO.thread.change_favorite);

	/* Ouverture du rideau */
	$(".dilectio-principal-body").on("click", ".dilectio-post-gift-curtain-handle", DILECTIO.thread.open_curtain);
});

$(window).load(function() {
	/* Chargement des posts */
	var container = $(".dilectio-thread-container");
	if (container.length == 1) {
		var attr_id = container.attr("id");
		var thread_id = parseInt(attr_id.replace("dilectio-thread-", ""));
		if (thread_id > 0) {
			DILECTIO.thread.load_cards(thread_id);
		}
	}
});
