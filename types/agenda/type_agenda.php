<?php

/*
 * Dilectio : Type agenda
 */
 
class type_agenda extends dilectio_type implements dilectio_interface {
	public static function head($excerpt_only = false) {}
	public static function body($excerpt_only = false) {}

	public static function excerpt($profil_id, $post_id, $type_post_id) {
		$langue = tool_session::lire_param("lang");
	
		/* Agenda */
		$post = db::load(db::table("post_agenda"), $type_post_id);
		$o = o::div(_class, "dilectio-type-agenda-extrait-container");
		$o .= o::h4_h4($post->label, _class, "dilectio-type-agenda-extrait-label");
		$caption = $post->caption;
		if (strlen($caption) > 0) {
			$o .= o::p_p($post->caption, _class, "dilectio-type-agenda-extrait-caption");
		}
		$o .= o::_div();
		$date = $post->date;
		$date_stamp = strtotime($date);
		if ((strlen($date) > 0) && ($date_stamp !== false)) {
			$date_format = self::format_date($langue, $date_stamp);
			$date_icone = o::mdlicon("event", array("class" => "dilectio-type-agenda-extrait-icone1 mdl-color-text--accent"));
			$date_span = o::span_span($date_format, _class, "dilectio-type-agenda-extrait-date");
			$o .= o::p(_class, "dilectio-type-agenda-extrait-horodatage");
			$o .= $date_icone.$date_span;

			$time = $post->time;
			$time_stamp = strtotime($time);
			if ((strlen($time) > 0) && ($time_stamp !== false)) {
				$time_format = self::format_time($langue, $time_stamp);
				$time_icone = o::mdlicon("schedule", array("class" => "dilectio-type-agenda-extrait-icone2 mdl-color-text--accent"));
				$time_span = o::span_span($time_format, _class, "dilectio-type-agenda-extrait-time");
				$o .= $time_icone.$time_span;
			}
			$o .= o::_p();
		}
		return $o;
	}

	public static function post($profil_id, $post_id, $type_post_id) {
		$langue = tool_session::lire_param("lang");
	
		/* Agenda */
		$post = db::load(db::table("post_agenda"), $type_post_id);
		$o = o::h4_h4($post->label, _class, "dilectio-type-agenda-post-label");
		$caption = $post->caption;
		if (strlen($caption) > 0) {
			$o .= o::p_p($post->caption, _class, "dilectio-type-agenda-post-caption");
		}
		$date = $post->date;
		$date_stamp = strtotime($date);
		if ((strlen($date) > 0) && ($date_stamp !== false)) {
			$date_format = self::format_date($langue, $date_stamp);
			$date_icone = o::mdlicon("event", array("class" => "dilectio-type-agenda-post-icone1 mdl-color-text--accent"));
			$date_span = o::span_span($date_format, _class, "dilectio-type-agenda-post-date");
			$o .= o::p(_class, "dilectio-type-agenda-post-horodatage");
			$o .= $date_icone.$date_span;

			$time = $post->time;
			$time_stamp = strtotime($time);
			if ((strlen($time) > 0) && ($time_stamp !== false)) {
				$time_format = self::format_time($langue, $time_stamp);
				$time_icone = o::mdlicon("schedule", array("class" => "dilectio-type-agenda-post-icone2 mdl-color-text--accent"));
				$time_span = o::span_span($time_format, _class, "dilectio-type-agenda-post-time");
				$o .= $time_icone.$time_span;
			}
			$o .= o::_p();

			$o .= o::div(_class, "dilectio-type-agenda-post-button_container");
			$icone_download = o::mdlicon("file_download");
			$o .= o::form(_class, "dilectio-type-agenda-post-button-download", _method, "post", "action", "ajax/types/ical");
			$o .= o::input_hidden(_name, "agenda-id", _value, $type_post_id);
			$o .= o::button_button($icone_download, _class, "mdl-button mdl-js-button mdl-button--fab mdl-button--mini-fab mdl-button--colored", _type, "submit");
			$o .= o::_form();
			
			$icone_send = o::mdlicon("send");
			$o .= o::form(_class, "dilectio-type-agenda-post-button-send", _method, "post");
			$o .= o::input_hidden(_name, "agenda-id", _value, $type_post_id);
			$o .= o::button_button($icone_send, _class, "mdl-button mdl-js-button mdl-button--fab mdl-button--mini-fab mdl-button--colored", _type, "submit");
			$o .= o::_form();
			
			$o .= o::_div();
		}
		return $o;
	}

	public static function form($langue, $thread_id, $type_id) {
		$label_datetime = lang_i18n::trad($langue, "type_agenda_datetime");
		$label_title = lang_i18n::trad($langue, "type_agenda_title");
		$label_descr = lang_i18n::trad($langue, "type_agenda_description");
		$label_submit = lang_i18n::trad($langue, "thread_create");
		$label_cancel = lang_i18n::trad($langue, "cancel");
		$label_gift = lang_i18n::trad($langue, "gift");
		$o = o::form(_id, "dilectio-new-post-form-".$thread_id."-".$type_id, _class, "dilectio-new-post-form", _method, "post")
			.o::div(_class, "dilectio-new-post-form-agenda-fields")

			.o::div(_class, "mdl-textfield mdl-js-textfield dilectio-new-post-form-agenda-field-date")
			.o::input_date(_id, "type-agenda-date", _name, "type-agenda-date", _class, "mdl-textfield__input", _required, "required")
			.o::label_label($label_datetime, _class, "mdl-textfield__label", _for, "type-agenda-date")
			.o::_div(_n)

			.o::div(_class, "mdl-textfield mdl-js-textfield dilectio-new-post-form-agenda-field-title")
			.o::input_text(_id, "type-agenda-title", _name, "type-agenda-title", _class, "mdl-textfield__input", _required, "required")
			.o::label_label($label_title, _class, "mdl-textfield__label", _for, "type-agenda-title")
			.o::_div(_n)
			
			.o::div(_class, "mdl-textfield mdl-js-textfield dilectio-new-post-form-agenda-field-descr")
			.o::input_text(_id, "type-agenda-descr", _name, "type-agenda-descr", _class, "mdl-textfield__input")
			.o::label_label($label_descr, _class, "mdl-textfield__label", _for, "type-agenda-descr")
			.o::_div(_n)

			.o::input_hidden(_name, "thread-id", _value, $thread_id)
			.o::input_hidden(_name, "type-id", _value, $type_id)
			.o::_div()
			.self::form_buttons($label_gift, $label_submit, $label_cancel)
			.o::_form(_n);
		$script = "$(\"#type-agenda-date\").flatpickr(flatpickr_default_options);";
		$o .= o::script_script($script, _type , "application/javascript", _n);
		return $o;
	}
	
	public static function submit($profil_id, &$type_message, &$message) {
		$ret_id = -1;
		$type_message = self::TYPE_MESSAGE_TOAST;
		$datetime = tool_post::Post("type-agenda-date");
		if (strlen($datetime) == 0) {
			$message = "type_agenda_err_missing_datetime";
			return $ret_id;
		}
		$datetime_stamp = strtotime($datetime);
		if ($datetime_stamp === false) {
			$message = "type_agenda_err_bad_datetime";
			return $ret_id;
		}
		$title = tool_post::Post("type-agenda-title");
		if (strlen($title) == 0) {
			$message = "type_agenda_err_missing_title";
			return $ret_id;
		}
		$descr = tool_post::Post("type-agenda-descr");

		$post_agenda = db::instance(db::table("post_agenda"));
		$post_agenda->date = date("Y-m-d", $datetime_stamp);
		$post_agenda->time = date("H:i:s", $datetime_stamp);
		$post_agenda->label = $title;
		$post_agenda->caption = $descr;
		$ret_id = db::store($post_agenda);

		return $ret_id;
	}

	private static function format_date($langue, $datetime_info) {
		if (date("Ymd", $datetime_info) == date("Ymd")) {
			$horodatage = lang_i18n::trad($langue, "type_agenda_today");
		}
		else if (date("Y", $datetime_info) == date("Y")) {
			$no_jour = (int) date("j", $datetime_info);
			$no_mois = (int) date("n", $datetime_info);
			$nom_mois = lang_i18n::trad($langue, "month_".$no_mois);
			$format = lang_i18n::trad($langue, "format_date_month_name");
			$horodatage = str_replace("mmmm", $nom_mois, $format);
			$horodatage = str_replace("d", $no_jour, $horodatage);
		}
		else {
			$format = lang_i18n::trad($langue, "format_date_long");
			$horodatage = date($format, $datetime_info);
		}
		return $horodatage;
	}

	private static function format_time($langue, $datetime_info) {
		$format = lang_i18n::trad($langue, "format_time");
		$horodatage = date($format, $datetime_info);
		return $horodatage;
	}
}