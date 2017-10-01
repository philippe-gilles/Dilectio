$(document).ready(function() {
	$(document).on("change", ".dilectio-type-question-post-check", function() {
		var parent_form = $(this).closest("form.dilectio-type-question-post-answer-form");
		if (parent_form.length == 1) {
			var attr_max_answers = parseInt(parent_form.attr("data-answers"));
			var answers = parent_form.find("input.dilectio-type-question-post-check:checked");
			var nb_answers = answers.length;
			var button_submit = parent_form.find("button[type='submit']");
			if (button_submit.length == 1) {
				if ((nb_answers > 0) && (nb_answers <= attr_max_answers)) {
					button_submit.removeAttr("disabled");
				}
				else {
					button_submit.attr("disabled", "disabled");
				}
			}
		}
	});

	$(document).on("submit", ".dilectio-type-question-post-answer-form", function() {
		var this_form = $(this);
		var donnees = $(this).serialize();
		$.ajax({
			type: "POST",
			url: "ajax/types/answer",
			data: donnees,
			dataType: "json"
		}).done(function(data) {
			var valide = data["valide"];
			if (valide == false) {
				var titre = data["titre"];
				var msg = data["msg"];
				if ((titre.length > 0) && (msg.length > 0)) {
					$.alert({title: titre, content: msg, type: "red"});
				}
			}
			else {
				var html = data["html"];
				this_form.replaceWith(html);
			}
		}).fail(function() {DILECTIO.toaster_fail_ajax();});
		return false;
	});

	$(document).on("click", "#type-question-choice-minus", function() {
		var input_choice = $("#type-question-number-choices");
		if (input_choice.length == 1) {
			var nb_choice = parseInt(input_choice.val());
			if (nb_choice > 1) {
				$("#type-answer-"+nb_choice).slideUp("fast");
				nb_choice -= 1;
				input_choice.val(nb_choice);
				var input_answer = $("#type-question-number-answers");
				if (input_answer.length == 1) {
					var nb_answer = parseInt(input_answer.val());
					if (nb_answer > nb_choice) {
						input_answer.val(nb_choice);
					}
				}
			}
		}
	});

	$(document).on("click", "#type-question-choice-plus", function() {
		var input_choice = $("#type-question-number-choices");
		if (input_choice.length == 1) {
			var nb_choice = parseInt(input_choice.val());
			if (nb_choice < 10) {
				nb_choice += 1;
				input_choice.val(nb_choice);
				$("#type-answer-"+nb_choice).slideDown("fast");
			}
		}
	});

	$(document).on("click", "#type-question-answer-minus", function() {
		var input_answer = $("#type-question-number-answers");
		if (input_answer.length == 1) {
			var nb_answer = parseInt(input_answer.val());
			if (nb_answer > 1) {
				nb_answer -= 1;
				input_answer.val(nb_answer);
			}
		}
	});

	$(document).on("click", "#type-question-answer-plus", function() {
		var max_answers = parseInt($("#type-question-number-choices").val());
		var input_answer = $("#type-question-number-answers");
		if (input_answer.length == 1) {
			var nb_answer = parseInt(input_answer.val());
			if (nb_answer < max_answers ) {
				nb_answer += 1;
				input_answer.val(nb_answer);
			}
		}
	});

});
