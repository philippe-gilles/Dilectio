<?php

/*
 * Dilectio : Type article
 */

class type_article extends dilectio_type implements dilectio_interface {

	public static function head($excerpt_only = false) {
		$o = "";
		if (!($excerpt_only)) {
			$css = self::path()."trumbo/ui/trumbowyg.min.css";
			$o = o::link(_rel , "stylesheet", _href, $css, _n);
			$css = self::path()."trumbo/ui/trumbowyg.colors.min.css";
			$o .= o::link(_rel , "stylesheet", _href, $css, _n);
		}
		return $o;
	}

	public static function body($excerpt_only = false) {
		$o = "";
		if (!($excerpt_only)) {
			$js = self::path()."trumbo/trumbowyg.min.js";
			$o .= o::script_script(null, _type , "application/javascript", _src , $js, _n);
			$langue = tool_session::lire_param("lang");
			if ((strlen($langue) > 0) && strncmp($langue, "en", 2)) {
				$js_lang = self::path()."trumbo/langs/".$langue.".min.js";
				if (file_exists($js_lang)) {
					$o .= o::script_script(null, _type , "application/javascript", _src , $js_lang, _n);
				}
			}
			$js = self::path()."trumbo/trumbowyg.colors.min.js";
			$o .= o::script_script(null, _type , "application/javascript", _src , $js, _n);
		}
		return $o;
	}

	public static function excerpt($profil_id, $post_id, $type_post_id) {
		$post = db::load(db::table("post_article"), $type_post_id);
		$text = tool_string::truncate($post->text);
		$o = o::div_div($text, _class, "dilectio-type-article-extrait-wrapper");
		return $o;
	}

	public static function post($profil_id, $post_id, $type_post_id) {
		$post = db::load(db::table("post_article"), $type_post_id);
		$o = o::div_div($post->text, _class, "dilectio-type-article-post-wrapper");
		return $o;
	}

	public static function form($langue, $thread_id, $type_id) {
		$script = self::javascript($langue, "type-article-content");
		$label_submit = lang_i18n::trad($langue, "thread_create");
		$label_cancel = lang_i18n::trad($langue, "cancel");
		$label_gift = lang_i18n::trad($langue, "gift");
		$o = o::form(_id, "dilectio-new-post-form-".$thread_id."-".$type_id, _class, "dilectio-new-post-form", _method, "post")
			.o::textarea_textarea(null, _id, "type-article-content", _name, "type-article-content")
			.o::input_hidden(_name, "thread-id", _value, $thread_id)
			.o::input_hidden(_name, "type-id", _value, $type_id)
			.self::form_buttons($label_gift, $label_submit, $label_cancel)
			.o::_form(_n)
			.o::script_script($script, _type , "application/javascript", _n);
		return $o;
	}

	public static function edit($langue, $post_id, $type_id, $type_post_id) {
		$o = "";
		$post = db::load(db::table("post_article"), $type_post_id);
		if (!(is_null($post))) {
			$script = self::javascript($langue, "type-article-content-".$type_post_id);
			$label_submit = lang_i18n::trad($langue, "thread_modify");
			$label_cancel = lang_i18n::trad($langue, "cancel");
			$o = o::form(_id, "dilectio-edit-post-form-".$type_id."-".$type_post_id, _class, "dilectio-edit-post-form", _method, "post")
				.o::textarea_textarea($post->text, _id, "type-article-content-".$type_post_id, _name, "type-article-content")
				.o::input_hidden(_name, "post-id", _value, $post_id)
				.o::input_hidden(_name, "type-post-id", _value, $type_post_id)
				.o::input_hidden(_name, "type-id", _value, $type_id)
				.o::p(_class, "dilectio-edit-post-form-field", _style, "text-align:center;")
				.o::button_button($label_submit, "type", "submit", _class, "mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent")
				.o::button_button($label_cancel, _id, "edit-post-cancel", "type", "button", _class, "mdl-button mdl-js-button mdl-button--raised")
				.o::_p()
				.o::_p()
				.o::_form(_n)
				.o::script_script($script, _type , "application/javascript", _n);
		}
		return $o;
	}

	public static function submit($profil_id, &$type_message, &$message) {
		$ret_id = -1;
		$type_message = self::TYPE_MESSAGE_SILENT;
		$content = tool_post::Post("type-article-content");
		$content_stripped = strip_tags($content);
		if (strlen($content_stripped) > 0) {
			$post_article = db::instance(db::table("post_article"));
			$post_article->text = tool_string::autolink($content, array("class", "dilectio-lien-texte-standard"));
			$ret_id = db::store($post_article);
		}
		return $ret_id;
	}
	
	public static function save($profil_id, $type_post_id, &$message) {
		$ret = false;
		$content = tool_post::Post("type-article-content");
		$content_stripped = strip_tags($content);
		if (strlen($content_stripped) > 0) {
			$post_article = db_post_article::get($type_post_id);
			if (!(is_null($post_article))) {
				$post_article->text = tool_string::autolink($content, array("class", "dilectio-lien-texte-standard"));
				db::store($post_article);
				$ret = true;
			}
		}
		return $ret;
	}
	
	private static function javascript($langue, $id) {
		$script = "$('#".$id."').trumbowyg({";
		if ((strlen($langue) > 0) && strncmp($langue, "en", 2)) {
			$script .= "lang: '".$langue."',";
		}
		$script .= "btns: [['p', 'h1', 'h2', 'h3'],['strong', 'em', 'underline', 'del'], ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'], ['unorderedList', 'orderedList'],['removeformat'],['foreColor', 'backColor']],";
		$script .= "removeformatPasted: true,";
		$script .= "});\n";
		$script .= "$('.trumbowyg-editor').get(0).focus();\n";
		return $script;
	}

}