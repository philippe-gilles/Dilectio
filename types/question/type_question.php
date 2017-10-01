<?php

/*
 * Dilectio : Type question
 */
 
class type_question extends dilectio_type implements dilectio_interface {
	public static function head($excerpt_only = false) {}
	public static function body($excerpt_only = false) {}

	public static function excerpt($profil_id, $post_id, $type_post_id) {
		$langue = tool_session::lire_param("lang");
	
		/* Question */
		$post = db::load(db::table("post_question"), $type_post_id);
		$o = o::h4_h4($post->label, _class, "dilectio-type-question-extrait-label");

		/* Choix possibles */
		$o .= self::display_choix($langue, $post, "extrait");

		/* Réponse ou pas */
		$user_id = tool_session::lire_param("profil_id");		
		if ($user_id == $profil_id) {
			$answers = db_post_answer::to_question_by_not($type_post_id, $user_id);
			if (count($answers) == 0) {
				$icone_reponse = "check_box_outline_blank";
				$entry_reponse = "type_question_answer_nobody";
				$class_reponse = "dilectio-type-question-post-icone-sans";
			}
			else {
				$icone_reponse = "check_box";
				$entry_reponse = "type_question_answer_someone";
				$class_reponse = "mdl-button--accent";
			}
		}
		else {
			$answer = db_post_answer::to_question_by($type_post_id, $user_id);
			if (is_null($answer)) {
				$icone_reponse = "check_box_outline_blank";
				$entry_reponse = "type_question_answer_not_yet";
				$class_reponse = "dilectio-type-question-post-icone-sans";
			}
			else {
				$icone_reponse = "check_box";
				$entry_reponse = "type_question_answer_already";
				$class_reponse = "mdl-button--accent";
			}
		}
		$label_reponse = o::mdlicon($icone_reponse, array("class" => $class_reponse));
		$label_reponse .= o::span_span(lang_i18n::trad($langue, $entry_reponse));
		$o .= o::p_p($label_reponse, _class, "dilectio-type-question-extrait-reponse");
		return $o;
	}

	public static function post($profil_id, $post_id, $type_post_id) {
		$user_id = tool_session::lire_param("profil_id");		
		$langue = tool_session::lire_param("lang");

		/* Question */
		$post = db::load(db::table("post_question"), $type_post_id);
		$o = o::h4_h4($post->label, _class, "dilectio-type-question-post-label");
		
		/* Choix possibles */
		$o .= self::display_choix($langue, $post, "post");

		/* Cas où l'affichage se fait chez l'auteur de la question */
		if ($user_id == $profil_id) {
			$answers = db_post_answer::to_question_by_not($type_post_id, $user_id);
			/* Cas où il n'y a pas encore de réponses */
			if (count($answers) == 0) {
				$o .= self::display_question($langue, $post);
			}
			/* Cas où il y a des réponses */
			else {
				foreach($answers as $answer) {
					$o .= self::display_answer($langue, $post, $answer);
				}
			}
		}
		/* Cas où l'affichage ne se fait pas chez l'auteur de la question */
		else {
			$answer = db_post_answer::to_question_by($type_post_id, $user_id);
			/* Cas où le profil connecté n'a pas répondu : formulaire de réponse */
			if (is_null($answer)) {
				$o .= self::display_answer_form($langue, $post_id, $post);
			}
			/* Cas où le profil connecté a déjà répondu */
			else {
				$o .= self::display_answer($langue, $post, $answer);
			}
		}

		return $o;
	}

	public static function form($langue, $thread_id, $type_id) {
		$label_sentence = lang_i18n::trad($langue, "type_question_sentence");
		$label_choice_number = lang_i18n::trad($langue, "type_question_choice_number");
		$label_answer_number = lang_i18n::trad($langue, "type_question_answer_number");
		$label_submit = lang_i18n::trad($langue, "thread_create");
		$label_cancel = lang_i18n::trad($langue, "cancel");
		$label_gift = lang_i18n::trad($langue, "gift");
		$o = o::form(_id, "dilectio-new-post-form-".$thread_id."-".$type_id, _class, "dilectio-new-post-form", _method, "post")

			.o::div(_class, "dilectio-new-post-form-question-fields")

			.o::div(_class, "mdl-textfield mdl-js-textfield dilectio-new-post-form-question-field")
			.o::input_text(_id, "type-question-sentence", _name, "type-question-sentence", _class, "mdl-textfield__input", _required, "required")
			.o::label_label($label_sentence, _class, "mdl-textfield__label", _for, "type-question-sentence")
			.o::_div(_n)
			
			.o::div(_class, "dilectio-new-post-form-question-counter")
			.o::label_label($label_choice_number, _class, "dilectio-new-post-form-question-choices", _for, "type-question-number-choices")
			.o::button_button(o::mdlicon("remove_circle"), _id, "type-question-choice-minus", _class, "mdl-button mdl-js-button mdl-button--icon mdl-button--colored", _type, "button")
			.o::div(_class, "mdl-textfield mdl-js-textfield dilectio-new-post-form-question-number")
			.o::input_text(_id, "type-question-number-choices", _name, "number-choices", _class, "mdl-textfield__input", _readonly, "readonly", _value, "1")
			.o::_div()
			.o::button_button(o::mdlicon("add_circle"), _id, "type-question-choice-plus", _class, "mdl-button mdl-js-button mdl-button--icon mdl-button--colored", _type, "button")
			.o::_div()

			.o::div(_class, "dilectio-new-post-form-question-counter")
			.o::label_label($label_answer_number, _class, "dilectio-new-post-form-question-answers", _for, "type-question-number-answers")
			.o::button_button(o::mdlicon("remove_circle"), _id, "type-question-answer-minus", _class, "mdl-button mdl-js-button mdl-button--icon mdl-button--colored", _type, "button")
			.o::div(_class, "mdl-textfield mdl-js-textfield dilectio-new-post-form-question-number")
			.o::input_text(_id, "type-question-number-answers", _name, "number-answers", _class, "mdl-textfield__input", _readonly, "readonly", _value, "1")
			.o::_div()
			.o::button_button(o::mdlicon("add_circle"), _id, "type-question-answer-plus", _class, "mdl-button mdl-js-button mdl-button--icon mdl-button--colored", _type, "button")
			.o::_div()
			
			.o::div(_class, "dilectio-new-post-form-question-list");
		for ($cpt = 1;$cpt <= 10;$cpt++) {
			if ($cpt == 1) {
				$o .= o::div(_id, "type-answer-".$cpt, _class, "dilectio-new-post-form-answer", _style, "display:block;");
			}
			else {
				$o .= o::div(_id, "type-answer-".$cpt, _class, "dilectio-new-post-form-answer", _style, "display:none;");
			}
			$o .= o::label_label($cpt, _class, "dilectio-new-post-form-answer-label", _for, "type-question-answer-".$cpt);
			$o .= o::div(_class, "mdl-textfield mdl-js-textfield dilectio-new-post-form-answer-field");
			$o .= o::input_text(_id, "type-question-answer-".$cpt, _name, "type-question-answer-".$cpt, _class, "mdl-textfield__input");
			$o .= o::_div().o::_div(_n);
		}
		$o .= o::_div()
			.o::input_hidden(_name, "thread-id", _value, $thread_id)
			.o::input_hidden(_name, "type-id", _value, $type_id)
			.o::_div()
			.self::form_buttons($label_gift, $label_submit, $label_cancel)
			.o::_form(_n);
		return $o;
	}

	public static function submit($profil_id, &$type_message, &$message) {
		$ret_id = -1;
		$type_message = self::TYPE_MESSAGE_TOAST;
		$nb_choices = (int) tool_post::Post("number-choices");
		$nb_answers = (int) tool_post::Post("number-answers");
		$sentence = tool_post::Post("type-question-sentence");
		if (($nb_choices > 0) && ($nb_answers > 0) && ($nb_choices >= $nb_answers) && (strlen($sentence) > 0)) {
			$answers = array();
			for($cpt = 1;$cpt <= $nb_choices;$cpt++) {
				$answer = tool_post::Post("type-question-answer-".$cpt);
				if (strlen($answer) > 0) {
					if (in_array($answer, $answers)) {
						$message = "type_question_err_same_answers";
						return $ret_id;
					}
					else {
						$answers[$cpt] = $answer;
					}
				}
				else {
					$message = "type_question_err_missing_answer";
					return $ret_id;
				}
			}
			$post_question = db::instance(db::table("post_question"));
			$post_question->label = $sentence;
			$post_question->nb_choices = $nb_choices;
			$post_question->nb_answers = $nb_answers;
			for($cpt = 1;$cpt <= $nb_choices;$cpt++) {
				self::set_question_label($post_question, $cpt, $answers[$cpt]);
			}
			$ret_id = db::store($post_question);
		}
		else {
			$message = "type_question_err_bad_data";
		}
		return $ret_id;
	}

	/* Affichage des choix possibles */
	public static function display_choix($langue, &$question, $class) {
		$nb_reponses = $question->nb_answers;
		$nb_choix = $question->nb_choices;
		if ($nb_reponses > 1) {
			$label_choix = $nb_reponses." ".lang_i18n::trad($langue, "type_question_answer_some");
		}
		else {
			$label_choix = lang_i18n::trad($langue, "type_question_answer_one");
		}
		if ($nb_choix > 1) {
			$label_choix .= " ".$nb_choix;
		}
		$label_choix .= ".";
		$o = o::p_p($label_choix , _class, "dilectio-type-question-".$class."-choix");

		return $o;
	}

	/* Affichage d'une question */
	public static function display_question($langue, &$question) {
		$o = "";
		$nb_choix = $question->nb_choices;
		for($cpt = 1;$cpt <= $nb_choix;$cpt++) {
			$label = self::get_question_label($question , $cpt);
			if (strlen($label) > 0) {
				$label_reponse = o::mdlicon("check_box_outline_blank", array("class" => "dilectio-type-question-post-icone-sans"));
				$label_reponse .= o::span_span($label);
				$o .= o::p_p($label_reponse, _class, "dilectio-type-question-post-reponse");
			}
		}
		return $o;
	}

	/* Affichage d'une réponse */
	public static function display_answer($langue, &$question, &$answer) {
		$component = new view_component($langue);
		$datetime_answer = strtotime($answer->date);
		$profile_id = $answer->profile_id;
		$o = o::p(_class, "dilectio-type-question-post-reponse-auteur");
		$o .= o::img(_src, __DILECTIO_PROFILES."profile-".$profile_id."/avatar.png", _class, "dilectio-post-avatar");
		$o .= o::span_span($component->format_horodatage($datetime_answer));
		$o .= o::_p();
		$nb_choix = $question->nb_choices;
		for($cpt = 1;$cpt <= $nb_choix;$cpt++) {
			$label = self::get_question_label($question , $cpt);
			if (strlen($label) > 0) {
				$reponse = self::get_reponse($answer, $cpt);
				$icone_reponse = $reponse?"check_box":"check_box_outline_blank";
				$label_reponse = o::mdlicon($icone_reponse, array("class" => "mdl-button--accent"));
				$label_reponse .= o::span_span($label);
				$o .= o::p_p($label_reponse, _class, "dilectio-type-question-post-reponse");
			}
		}
		$caption = trim($answer->caption);
		if (strlen($caption) > 0) {
			$o .= o::p_p($caption , _class, "dilectio-type-question-post-reponse-caption");
		}
		return $o;
	}

	/* Affichage du formulaire de réponse */
	private static function display_answer_form($langue, $post_id, &$question) {
		$o = o::form(_id, "answer-form-".$question->id, _class, "dilectio-type-question-post-answer-form", _method, "post", "data-answers", $question->nb_answers, "action", "debug");
		/* Choix */
		$nb_choix = $question->nb_choices;
		for($cpt = 1;$cpt <= $nb_choix;$cpt++) {
			$label = self::get_question_label($question , $cpt);
			if (strlen($label) > 0) {
				$answer_id = "answer-".$question->id."_".$cpt;
				$check_reponse = o::label(_class, "mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect", _for, $answer_id);
				$check_reponse .= o::input_checkbox(_id, $answer_id, _name, "answer-".$cpt, _class, "mdl-checkbox__input dilectio-type-question-post-check");
				$check_reponse .= o::span_span($label, _class, "dilectio-type-question-post-check-label");
				$check_reponse .= o::_label();
				$o .= o::p_p($check_reponse, _class, "dilectio-type-question-post-check-reponse");
			}
		}
		/* Commentaire */
		$caption_id = "caption-".$question->id;
		$o .= o::div(_class, "mdl-textfield mdl-js-textfield dilectio-type-question-post-caption");
		$o .= o::input_text(_id, $caption_id, _name, "caption", _class, "mdl-textfield__input");
		$o .= o::label_label("Commentaire", _class, "mdl-textfield__label", _for, $caption_id);
		$o .= o::_div();
 
		/* Champs cachés */
		$o .= o::input_hidden(_name, "post-id", _value, $post_id);
		$o .= o::input_hidden(_name, "question-id", _value, $question->id);

		/* Soumission */
		$component = new view_component_input($langue);
		$o .= $component->submit_button("answer", true);
		$o .= o::_form(_n);
		return $o;
	}

	private static function get_question_label(&$post_question, $no_label) {
		$ret = null;
		if (is_null($post_question)) {return $ret;}
		switch($no_label) {
			case 1: return $post_question->label_choice_1;
			case 2: return $post_question->label_choice_2;
			case 3: return $post_question->label_choice_3;
			case 4: return $post_question->label_choice_4;
			case 5: return $post_question->label_choice_5;
			case 6: return $post_question->label_choice_6;
			case 7: return $post_question->label_choice_7;
			case 8: return $post_question->label_choice_8;
			case 9: return $post_question->label_choice_9;
			case 10: return $post_question->label_choice_10;
		}
		return $ret;
	}
	
	
	private static function set_question_label(&$post_question, $no_label, $label) {
		$ret = null;
		if (is_null($post_question)) {return;}
		switch($no_label) {
			case 1: $post_question->label_choice_1 = $label;break;
			case 2: $post_question->label_choice_2 = $label;break;
			case 3: $post_question->label_choice_3 = $label;break;
			case 4: $post_question->label_choice_4 = $label;break;
			case 5: $post_question->label_choice_5 = $label;break;
			case 6: $post_question->label_choice_6 = $label;break;
			case 7: $post_question->label_choice_7 = $label;break;
			case 8: $post_question->label_choice_8 = $label;break;
			case 9: $post_question->label_choice_9 = $label;break;
			case 10: $post_question->label_choice_10 = $label;break;
		}
	}
	
	private static function get_reponse(&$post_answer, $no_question) {
		$ret = false;
		if (is_null($post_answer)) {return $ret;}
		switch($no_question) {
			case 1: return $post_answer->answer_1;
			case 2: return $post_answer->answer_2;
			case 3: return $post_answer->answer_3;
			case 4: return $post_answer->answer_4;
			case 5: return $post_answer->answer_5;
			case 6: return $post_answer->answer_6;
			case 7: return $post_answer->answer_7;
			case 8: return $post_answer->answer_8;
			case 9: return $post_answer->answer_9;
			case 10: return $post_answer->answer_10;
		}
		return $ret;
	}
}