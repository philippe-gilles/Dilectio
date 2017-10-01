/*
 * Dilectio : JS page security
 */

var DILECTIO = DILECTIO || {};

DILECTIO.security = {
	submit_password: function() {
		var donnees = $(this).serialize();
		$.ajax({
			type: "POST",
			url: "ajax/security/submit",
			data: donnees,
			dataType: "json"
		}).done(function(data) {
			$("#zxcvbn-password").val("");
			$("#repeat-password").val("");
			var valide = data["valide"];
			var toast = data["toast"];
			if (valide == false) {
				DILECTIO.toaster_fail(toast);
			}
			else {
				DILECTIO.toaster_success(toast);
			}
		}).fail(function() {DILECTIO.toaster_fail_ajax();});
		return false;
	},

	suggestion_entry: function(suggestion) {
		var entry = "";
		switch(suggestion) {
			case 'Straight rows of keys are easy to guess' :
			case 'Short keyboard patterns are easy to guess' :
			case 'Use a longer keyboard pattern with more turns' :
				entry = "zxcvbn_keyboard_patterns_etc";break;
			case 'Repeats like "aaa" are easy to guess' :
				entry = "zxcvbn_avoid_repeated_characters_etc";break;
			case 'Repeats like "abcabcabc" are only slightly harder to guess than "abc"' :
			case 'Avoid repeated words and characters' :
				entry = "zxcvbn_avoid_repeated_words_etc";break;
			case 'Sequences like abc or 6543 are easy to guess' :
			case 'Avoid sequences' :
				entry = "zxcvbn_avoid_sequences_etc";break;
			case 'Recent years are easy to guess' :
			case 'Avoid recent years' :
			case 'Avoid years that are associated with you' :
			case 'Dates are often easy to guess' :
			case 'Avoid dates and years that are associated with you' :
				entry = "zxcvbn_avoid_dates_and_years_etc";break;
			case 'This is similar to a commonly used password' :
			case 'This is a top-10 common password' :
			case 'This is a very common password' :
				entry = "zxcvbn_very_common_etc";break;
			case 'A word by itself is easy to guess' :
				entry = "zxcvbn_word_by_itself_etc";break;
			case 'Names and surnames by themselves are easy to guess' :
			case 'Common names and surnames are easy to guess' :
				entry = "zxcvbn_names_and_surnames_etc";break;
			case "Capitalization doesn't help very much" :
			case 'All-uppercase is almost as easy to guess as all-lowercase' :
				entry = "zxcvbn_capitalization_etc";break;
			case "Reversed words aren't much harder to guess" :
				entry = "zxcvbn_reversed_words_etc";break;
			case "Predictable substitutions like '@' instead of 'a' don't help very much" :
				entry = "zxcvbn_predictable_substitutions_etc";break;
		}
		return entry;
	},

	manage_submit: function() {
		var button_submit = $(".dilectio-security-form button[type='submit']");
		if (button_submit.length > 0) {
			disabled = false;
			var score = parseInt($("#zxcvbn-password").attr("data-score"));
			var password = $("#zxcvbn-password").val();
			var repeat = $("#repeat-password").val();
			if (password.length == 0) {
				$("#submit-stronger").css("display", "none");
				$("#submit-repeated").css("display", "none");
				$("#submit-ready").css("display", "block");
				button_submit.attr("disabled", "disabled");
			}
			else if (score < 3) {
				$("#submit-stronger").css("display", "block");
				$("#submit-repeated").css("display", "none");
				$("#submit-ready").css("display", "none");
				button_submit.attr("disabled", "disabled");
			}
			else if (password !== repeat) {
				$("#submit-stronger").css("display", "none");
				$("#submit-repeated").css("display", "block");
				$("#submit-ready").css("display", "none");
				button_submit.attr("disabled", "disabled");
			}
			else {
				$("#submit-stronger").css("display", "none");
				$("#submit-repeated").css("display", "none");
				$("#submit-ready").css("display", "block");
				button_submit.removeAttr("disabled");
			}
		}
	},

	eval_strength: function() {
		var password = $(this).val();
		if (password.length > 0) {
			result = zxcvbn(password);
			var score = result.score;
			$(this).attr("data-score", score);
			$("#password-score").css("width", (25 * (4 - score))+"%");
			var warnings = "";
			var suggestions = "";
			var feedback = result.feedback;
			if (feedback) {
				/* Traitement du warning */
				var translations_suggestions = [];
				var feedback_warning = feedback.warning;
				if (feedback_warning.length > 0) {
					var warning_entry = DILECTIO.security.suggestion_entry(feedback_warning);
					if (warning_entry.length > 0) {
						translations_suggestions.push(warning_entry);
					}
				}

				/* Traitement des suggestions */
				var feedback_suggestions = feedback.suggestions;
				var nb_suggestions = feedback_suggestions.length;
				var suggestion_entry = "";
				for (var i = 0;i < nb_suggestions;i++) {
					suggestion_entry = DILECTIO.security.suggestion_entry(feedback_suggestions[i]);
					if (suggestion_entry.length > 0) {
						if (translations_suggestions.indexOf(suggestion_entry) < 0) {
							translations_suggestions.push(suggestion_entry);
						}
					}
				}
				var nb_translations = translations_suggestions.length;
				var translation_entry = "";
				for (var i = 0;i < nb_translations;i++) {
					translation_entry = translations_suggestions[i];
					if (translation_entry.length > 0) {
						translation = security_i18n[translation_entry];
						if (translation !== undefined) {
							if (translation.length > 0) {
								suggestions = suggestions + translation + " ";
							}
						}
					}
				}
			}
			$("#password-suggestions").html(suggestions);
		}
		else {
			$(this).attr("data-score", 0);
			$("#password-score").css("width", "100%");
			$("#password-suggestions").html("");
		}
		DILECTIO.security.manage_submit();
	},
	
	init: function(path) {
		$.ajax({
			cache: true,
			dataType: "script",
			url: path+"zxcvbn/zxcvbn.js"
		}).done(function(content) {
			$(document).on("keyup", "#zxcvbn-password", DILECTIO.security.eval_strength);
			$(document).on("change", "#zxcvbn-password", DILECTIO.security.eval_strength);
			$(document).on("keyup", "#repeat-password", DILECTIO.security.manage_submit);
			$(document).on("change", "#repeat-password", DILECTIO.security.manage_submit);
		}).fail(function() {alert("FAIL AJAX");});
	}
}

$(document).ready(function() {
	/* Soumission du formulaire */
	$(".dilectio-principal").on("submit", ".dilectio-security-form", DILECTIO.security.submit_password);
});
