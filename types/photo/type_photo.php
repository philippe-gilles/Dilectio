<?php

/*
 * Dilectio : Type photo
 */
 
class type_photo extends dilectio_type implements dilectio_interface {
	public static function head($excerpt_only = false) {}
	
	public static function body($excerpt_only = false) {}

	public static function excerpt($profil_id, $post_id, $type_post_id) {
		$src_thumb = "photo/thumb/".$profil_id."/".$type_post_id;
		$src_original = "photo/original/".$profil_id."/".$type_post_id;
		$o = o::div_div("", _class, "dilectio-type-photo-extrait", _style, "background-image: url('".$src_thumb."');", "data-original", $src_original);
		return $o;
	}

	public static function post($profil_id, $post_id, $type_post_id) {
		$post = db::load(db::table("post_photo"), $type_post_id);
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
		$o = o::div(_class, "dilectio-image-wrapper ".$class_ratio, _style, "width:".$width."px;")
			.o::img(_class, "dilectio-image", _src, "photo/original/".$profil_id."/".$type_post_id)
			.o::_div();
		if (strlen($post->caption) > 0) {
			$o .= o::p_p($post->caption, _class, "dilectio-type-photo-caption");
		}

		return $o;
	}

	public static function form($langue, $thread_id, $type_id) {
		$max_size = tool_post_file::file_upload_max_size();
		$max_real = (__DILECTIO_UPLOAD_MAX_FILESIZE > 0)?min($max_size, (int) __DILECTIO_UPLOAD_MAX_FILESIZE):$max_size;
		$max_size_mo = sprintf(" (max %.1f Mo)", ($max_real / (1024 * 1024)));
		$label_file = lang_i18n::trad($langue, "type_photo_file").$max_size_mo;
		$label_caption = lang_i18n::trad($langue, "type_photo_caption");
		$label_submit = lang_i18n::trad($langue, "thread_create");
		$label_cancel = lang_i18n::trad($langue, "cancel");
		$label_gift = lang_i18n::trad($langue, "gift");
		$o = o::form(_id, "dilectio-new-post-form-".$thread_id."-".$type_id, _class, "dilectio-new-post-form", _method, "post", _enctype, "multipart/form-data")
			.o::div(_class, "dilectio-new-post-form-photo-fields dilectio-new-post-waiting-container")
			.o::div(_class, "mdl-file mdl-js-file dilectio-new-post-form-photo-field-file")
			.o::input_file(_id, "type-photo-file", _name, "type-photo-file", _required, "required")
			.o::label_label($label_file, _class, "mdl-file__label", _for, "type-photo-file")
			.o::_div()

			.o::div(_class, "mdl-textfield mdl-js-textfield dilectio-new-post-form-photo-field-caption")
			.o::input_text(_id, "type-photo-caption", _name, "type-photo-caption", _class, "mdl-textfield__input")
			.o::label_label($label_caption, _class, "mdl-textfield__label", _for, "type-photo-caption")
			.o::_div(_n)
			
			.((__DILECTIO_UPLOAD_MAX_FILESIZE > 0)?(o::input_hidden(_name, "MAX_FILE_SIZE", _value, __DILECTIO_UPLOAD_MAX_FILESIZE)):"")
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
		$post_file = tool_post::File("type-photo-file");
		if (!(is_null($post_file))) {
			$file = new tool_post_file($post_file);
			$file->set_authorized_ext(array("jpg", "jpeg", "png", "gif"));
			$file->load();
			if ($file->is_valid()) {
				$image = new tool_post_img($file);
				$chemin = __DILECTIO_PROFILES."profile-".$profil_id."/photo/";
				$image->set_destination($chemin, "original-tmp", "thumb-tmp");
				$ret = $image->move_and_resize_uploaded_file($message);
				if ($ret) {
					$width = $image->get_width();
					$height = $image->get_height();
					$extension = $image->get_extension();
					$caption = tool_post::Post("type-photo-caption");
					$post_photo = db::instance(db::table("post_photo"));
					$post_photo->caption = is_null($caption)?"":$caption;
					$post_photo->width = (int) $width;
					$post_photo->height = (int) $height;
					$post_photo->extension = $extension;
					$ret_id = db::store($post_photo);
					if ($ret_id > 0) {
						@rename($chemin."original-tmp.".$extension, $chemin."original-".$ret_id.".".$extension);
						@rename($chemin."thumb-tmp.".$extension, $chemin."thumb-".$ret_id.".".$extension); 
					}
				}
			}
			else {
				$message = $file->message();
			}
		}
		else {
			$message = "upload_err_no_file";
		}
		return $ret_id;
	}
}