/*
 * Dilectio : JS page categories
 */

var DILECTIO = DILECTIO || {};

DILECTIO.categories = {
	
	change: function() {
		var tab_data = [];
		var tab_nested = $(this).nestable("asNestedSet");
		for(var i = 0; i < tab_nested.length; i++) {
			var nested = tab_nested[i];
			var id = (nested["id"] === null)?"0":parseInt(nested["id"]);
			if (id > 0) {
				var parent_id = (nested["parent_id"] === null)?"0":parseInt(nested["parent_id"]);
				tab_data.push({"id": id, "parent_category_id": parent_id});
			}
		}
		
		if (tab_data.length > 0) {
			$.ajax({
				type: "POST",
				url: "ajax/categories/change",
				data: {tab_data: tab_data},
				dataType: "json"
			}).done(function(data) {
				var valide = data["valide"];
			}).fail(function() {DILECTIO.toaster_fail_ajax();});
		}
	},

	category_focusin: function(field, touche) {
		var attr_id = field.attr("id");
		var category_id = parseInt(attr_id.replace("field-", ""));
		if (category_id >= 0) {
			if (touche == 13) {
				DILECTIO.categories.category_done(category_id);
			}
			else if (touche == 27) {
				DILECTIO.categories.category_undo(category_id);
			}
			else {
				DILECTIO.categories.mode_focusin(category_id);
			}
		}
	},

	mode_focusin: function(category_id) {
		$("#delete-"+category_id).css("display", "none");
		$("#done-"+category_id).css("display", "block");
		$("#undo-"+category_id).css("display", "block");
	},

	mode_focusout: function(category_id) {
		$("#delete-"+category_id).css("display", "block");
		$("#done-"+category_id).css("display", "none");
		$("#undo-"+category_id).css("display", "none");
		$("#field-"+category_id).blur();
	},
	
	category_new: function() {
		$.ajax({
			type: "POST",
			url: "ajax/categories/new",
			dataType: "json"
		}).done(function(data) {
			var valide = data["valide"];
			if (valide) {
				var html = data["html"];
				$("#nestable-categories-root").append(html);
				/* IMPORTANT : Applique MDL aux composants chargés via AJAX */
				componentHandler.upgradeDom();
			}
		}).fail(function() {DILECTIO.toaster_fail_ajax();});
	},

	category_done: function(category_id) {
		var label = $("#field-"+category_id).val();
		$.ajax({
			type: "POST",
			url: "ajax/categories/done",
			data: {category_id: category_id, label: label},
			dataType: "json"
		}).done(function(data) {
			var valide = data["valide"];
			if (valide) {
				var done_id = parseInt(data["done_id"]);
				if (done_id > 0) {
					DILECTIO.categories.mode_focusout(done_id);
				}
			}
		}).fail(function() {DILECTIO.toaster_fail_ajax();});
	},
	
	category_undo: function(category_id) {
		$.ajax({
			type: "POST",
			url: "ajax/categories/undo",
			data: {category_id: category_id},
			dataType: "json"
		}).done(function(data) {
			var valide = data["valide"];
			if (valide) {
				var undo_id = parseInt(data["undo_id"]);
				if (undo_id > 0) {
					var label = data["label"];
					if (label.length > 0) {
						$("#field-"+undo_id).val(label);
						DILECTIO.categories.mode_focusout(undo_id);
					}
				}
			}
		}).fail(function() {DILECTIO.toaster_fail_ajax();});
	},

	category_delete: function() {
		var attr_id = $(this).attr("id");
		var category_id = parseInt(attr_id.replace("delete-", ""));
		if (category_id >= 0) {
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
								url: "ajax/categories/delete",
								data: {category_id: category_id},
								dataType: "json"
							}).done(function(data) {
								var valide = data["valide"];
								if (valide) {
									window.location.reload();
								}
							}).fail(function() {DILECTIO.toaster_fail_ajax();});
						}
					},
					No: {text: confirm_no}
				},
				useBootstrap: false
			});

		}
	},
	
	collapse_all: function() {
		$("#nestable-categories").nestable("collapseAll");
	},
	
	expand_all: function() {
		$("#nestable-categories").nestable("expandAll");
	}
}

$(document).ready(function() {
	/* Actions */
	$("#nestable-categories").change(DILECTIO.categories.change);
	$(".dilectio-principal").on("keydown", ".dilectio-config-category-name", function(e) {
		DILECTIO.categories.category_focusin($(this), e.which);
	});
	$(".dilectio-principal").on("click", ".dilectio-config-category-done", function() {
		var attr_id = $(this).attr("id");
		var category_id = parseInt(attr_id.replace("done-", ""));
		if (category_id > 0) {DILECTIO.categories.category_done(category_id);}
	});
	$(".dilectio-principal").on("click", ".dilectio-config-category-undo", function() {
		var attr_id = $(this).attr("id");
		var category_id = parseInt(attr_id.replace("undo-", ""));
		if (category_id > 0) {DILECTIO.categories.category_undo(category_id);}
	});
	$(".dilectio-principal").on("click", ".dilectio-config-category-delete", DILECTIO.categories.category_delete);
	$(".dilectio-principal").on("click", ".dilectio-config-category-new-button", DILECTIO.categories.category_new);
	$(".dilectio-principal").on("click", ".dilectio-config-category-tree-collapse", DILECTIO.categories.collapse_all);
	$(".dilectio-principal").on("click", ".dilectio-config-category-tree-expand", DILECTIO.categories.expand_all);
	$(".dilectio-principal").on("click", ".dilectio-config-category-tree-sort", function() {window.location.reload();});
});
