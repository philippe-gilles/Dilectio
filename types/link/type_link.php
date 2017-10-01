<?php

/*
 * Dilectio : Type lien
 */
 
class type_link extends dilectio_type implements dilectio_interface {
	const LINK_TYPE_HTML = 0;
	const LINK_TYPE_IMAGE = 1;
	const LINK_TYPE_OTHER = 2;

	public static function head($excerpt_only = false) {}
	public static function body($excerpt_only = false) {}

	public static function excerpt($profil_id, $post_id, $type_post_id) {
		$o = "";
		$post = db::load(db::table("post_link"), $type_post_id);
		$site = $post->site;
		$url = $post->url;
		switch ($post->type) {
			case self::LINK_TYPE_HTML:
				$image_is_loaded = $post->image_is_loaded;
				$o .= o::div(_class, "dilectio-type-link-extrait-html");
				if ($image_is_loaded) {
					$class_img = "dilectio-type-link-extrait-html-img".((strlen($site) > 0)?"-site":"");
					$o .= o::div_div(null, _class, $class_img, _style, "background-image: url('link/thumb/".$profil_id."/".$type_post_id."');");
					$padding_infos = "10px";
				}
				else {
					$padding_infos = "0";
				}
				$title = $post->title;
				$description = $post->description;
				$class_infos = "dilectio-type-link-extrait-html-infos".((strlen($site) > 0)?"-site":"");
				$o .= o::div(_class, $class_infos, _style, "padding-left:".$padding_infos);
				$o .= o::h4_h4($title);
				$o .= o::p_p($description);
				$o .= o::_div();
				$o .= o::_div();
				if (strlen($site) > 0) {
					$o .= o::a_a($site, _class, "dilectio-lien-texte-standard dilectio-type-link-extrait-site", _href, $url, _target, "_blank");
				}
				break;
			case self::LINK_TYPE_IMAGE:
				if (strlen($site) > 0) {
					$o = o::div_div(null, _class, "dilectio-type-link-extrait-img-site", _style, "background-image: url('link/thumb/".$profil_id."/".$type_post_id."');")
						.o::a_a($site, _class, "dilectio-lien-texte-standard dilectio-type-link-extrait-site", _href, $url, _target, "_blank");
				}
				else {
					$o = o::div_div(null, _class, "dilectio-type-link-extrait-img", _style, "background-image: url('link/thumb/".$profil_id."/".$type_post_id."');");
				}
				break;
			case self::LINK_TYPE_OTHER:
				if (strlen($site) > 0) {
					$o = o::a_a($site, _class, "dilectio-lien-texte-standard dilectio-type-link-post-site", _href, $url, _target, "_blank");
				}
				else {
					$o = o::a_a($url, _class, "dilectio-lien-texte-standard dilectio-type-link-post-site", _href, $url, _target, "_blank");
				}
				if (strlen($post->caption) > 0) {
					$o .= o::p_p($post->caption, _class, "dilectio-type-link-caption");
				}
				break;
		}
		return $o;
	}

	public static function post($profil_id, $post_id, $type_post_id) {
		$o = "";
		$post = db::load(db::table("post_link"), $type_post_id);
		$site = $post->site;
		$url = $post->url;
		switch ($post->type) {
			case self::LINK_TYPE_HTML:
				$o = "";
				$title = $post->title;
				$description = $post->description;
				if (strlen($site) > 0) {
					$o .= o::a_a($site, _class, "dilectio-lien-texte-standard dilectio-type-link-post-site", _href, $url, _target, "_blank");
				}
				$o .= o::div(_class, "dilectio-type-link-post-html-infos");
				$o .= o::h4_h4($title);
				$o .= o::p_p($description);
				$o .= o::_div();
				$image_is_loaded = $post->image_is_loaded;
				if ($image_is_loaded) {
					$width = (int) $post->width;
					$height = (int) $post->height;
					$image_ratio = ($width == 0)?1:(int) floor((100 * $height) / $width);
					if ($width > $height) {
						$container_ratio = floor($image_ratio / 2) * 2;
					}
					else {
						$container_ratio = floor($image_ratio / 10) * 10;
					}
					$class_ratio = "dilectio-image-wrapper-".$container_ratio;
					$o .= o::div(_class, "dilectio-image-wrapper ".$class_ratio, _style, "width:".$width."px;")
						.o::img(_class, "dilectio-image", _src, "link/original/".$profil_id."/".$type_post_id)
						.o::_div();
				}
				break;
			case self::LINK_TYPE_IMAGE:
				$width = (int) $post->width;
				$height = (int) $post->height;
				$image_ratio = ($width == 0)?1:(int) floor((100 * $height) / $width);
				if ($width > $height) {
					$container_ratio = floor($image_ratio / 2) * 2;
				}
				else {
					$container_ratio = floor($image_ratio / 10) * 10;
				}
				$class_ratio = "dilectio-image-wrapper-".$container_ratio;
				$o = (strlen($site) > 0)?o::a_a($site, _class, "dilectio-lien-texte-standard dilectio-type-link-post-site", _href, $url, _target, "_blank"):"";
				$o .= o::div(_class, "dilectio-image-wrapper ".$class_ratio, _style, "width:".$width."px;")
					.o::img(_class, "dilectio-image", _src, "link/original/".$profil_id."/".$type_post_id)
					.o::_div();
				break;
			case self::LINK_TYPE_OTHER:
				if (strlen($site) > 0) {
					$o = o::a_a($site, _class, "dilectio-lien-texte-standard dilectio-type-link-post-site", _href, $url, _target, "_blank");
				}
				else {
					$o = o::a_a($url, _class, "dilectio-lien-texte-standard dilectio-type-link-post-site", _href, $url, _target, "_blank");
				}
				break;
		}
		if (strlen($post->caption) > 0) {
			$o .= o::p_p($post->caption, _class, "dilectio-type-link-caption");
		}
		return $o;
	}

	public static function form($langue, $thread_id, $type_id) {
		$label_url = lang_i18n::trad($langue, "type_link_url");
		$label_caption = lang_i18n::trad($langue, "type_link_caption");
		$label_submit = lang_i18n::trad($langue, "thread_create");
		$label_cancel = lang_i18n::trad($langue, "cancel");
		$label_gift = lang_i18n::trad($langue, "gift");
		$o = o::form(_id, "dilectio-new-post-form-".$thread_id."-".$type_id, _class, "dilectio-new-post-form", _method, "post")
			.o::div(_class, "dilectio-new-post-form-link-fields dilectio-new-post-waiting-container")

			.o::div(_class, "mdl-textfield mdl-js-textfield dilectio-new-post-form-link-field-url")
			.o::input_url(_id, "type-link-url", _name, "type-link-url", _class, "mdl-textfield__input", _required, "required")
			.o::label_label($label_url, _class, "mdl-textfield__label", _for, "type-link-url")
			.o::_div(_n)
	
			.o::div(_class, "mdl-textfield mdl-js-textfield dilectio-new-post-form-link-field-caption")
			.o::input_text(_id, "type-link-caption", _name, "type-link-caption", _class, "mdl-textfield__input")
			.o::label_label($label_caption, _class, "mdl-textfield__label", _for, "type-link-caption")
			.o::_div(_n)

			.o::input_hidden(_name, "thread-id", _value, $thread_id)
			.o::input_hidden(_name, "type-id", _value, $type_id)
			.o::_div()
			.self::form_buttons($label_gift, $label_submit, $label_cancel)
			.o::_form(_n);
		return $o;
	}
	
	public static function submit($profil_id, &$type_message, &$message) {
		$ret_id = -1;
		$type_message = self::TYPE_MESSAGE_ALERT;
		$url = tool_post::Post("type-link-url");
		$link = new tool_url($url);
		$url_valide = $link->is_valid();
		if ($url_valide) {
			$url_loaded = $link->load();
			if ($url_loaded) {
				if ($link->is_html()) {$link->retrieve_info();}
				$caption = tool_post::Post("type-link-caption");
				$post_link = db::instance(db::table("post_link"));
				$post_link->url = $url;
				$post_link->caption = is_null($caption)?"":$caption;
				$ret_id = db::store($post_link);
				if ($link->is_html()) {
					$post_link->type = self::LINK_TYPE_HTML;
					$post_link->site = $link->get_site();
					$post_link->title = $link->get_title();
					$post_link->description = $link->get_description();
					$post_link->image = $link->get_image();
					if (strlen($post_link->image) > 0) {
						$chemin = __DILECTIO_PROFILES."profile-".$profil_id."/link/";
						$image = new tool_url_img($post_link->image, $chemin);
						$image->set_destination($chemin, "original-".$ret_id, "thumb-".$ret_id);
						$ret = $image->move_and_resize_uploaded_file($message);
						if ($ret) {
							$post_link->width = (int) $image->get_width();
							$post_link->height = (int) $image->get_height();
							$post_link->extension = $image->get_extension();
							$post_link->image_is_loaded = 1;
						}
					}
				}
				else if ($link->is_image()) {
					$post_link->type = self::LINK_TYPE_IMAGE;
					$post_link->site = $link->get_host();
					$post_link->image = $url;
					if (strlen($post_link->image) > 0) {
						$chemin = __DILECTIO_PROFILES."profile-".$profil_id."/link/";
						$image = new tool_url_img($post_link->image, $chemin);
						$image->set_destination($chemin, "original-".$ret_id, "thumb-".$ret_id);
						$ret = $image->move_and_resize_uploaded_file($message);
						if ($ret) {
							$post_link->width = (int) $image->get_width();
							$post_link->height = (int) $image->get_height();
							$post_link->extension = $image->get_extension();
							$post_link->image_is_loaded = 1;
						}
					}
				}
				else {
					$post_link->type = self::LINK_TYPE_OTHER;
					$post_link->site = $link->get_host();
				}
				db::store($post_link);
			}
			else {
				$message =  "type_link_err_url_unloaded";
			}
		}
		else {
			$message = "type_link_err_no_url";
		}
		return $ret_id;
	}
}