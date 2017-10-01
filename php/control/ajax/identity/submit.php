<?php

/*
 * Dilectio : Script AJAX pour identity
 *
 * Gallery :
 *
	Array
	(
		[profile_image] => data/profiles/profile-2/avatar.png
		[profile_image-type] => gallery
		[profile_image-file-filename] => 
	)
 *
 * Url :
 *
	Array
	(
		[profile_image] => https://cdn1.iconfinder.com/data/icons/ninja-things-1/1772/ninja-simple-512.png
		[profile_image-type] => url
		[profile_image-file-filename] => 
	)
 *
 * Upload :
 *
	Array
	(
		[profile_image] => images.jpg
		[profile_image-type] => upload
		[profile_image-file-filename] => images.jpg
	)
	Array
	(
		[profile_image-file] => Array
			(
				[name] => images.jpg
				[type] => image/jpeg
				[tmp_name] => C:\xampp7\tmp\phpAD6D.tmp
				[error] => 0
				[size] => 7845
			)

	)
 */

/* Contrôle de la session (à remonter dans le router ?) */
tool_session::ouvrir();
$session_ok = tool_session::verifier(false);
if (!($session_ok)) {
	echo json_encode(array("valide" => false));
	die();
}

/* Récupération du profil */
$user_id = (int) tool_session::lire_param("profil_id");
if ($user_id < 1) {
	echo json_encode(array("valide" => false));
	die();
}

/* Récupération de la langue */
$langue = tool_session::lire_param("lang");
lang_i18n::init($langue);

/* Contrôle du paramètre alias */
$post_alias = trim(tool_post::Post("alias"));
$alias = @filter_var($post_alias, FILTER_SANITIZE_STRING); 

/* Contrôle du paramètre email */
$post_email = trim(tool_post::Post("email"));
$email = @filter_var($post_email, FILTER_SANITIZE_EMAIL);

/* Contrôle du paramètre langue */
$post_lang = trim(tool_post::Post("lang"));
$lang = @filter_var($post_lang, FILTER_SANITIZE_STRING);

if ((strlen($alias) == 0) || (strlen($email) == 0) || (strlen($lang) == 0)) {
	echo json_encode(array("valide" => false));
	die();
}

/* Image du profil */
$ret = false;
$message = "";
$avatar_image = __DILECTIO_PROFILES."profile-".$user_id."/avatar.png";
$post_image_type = trim(tool_post::Post("profile_image-type"));
$image_type = @filter_var($post_image_type, FILTER_SANITIZE_STRING);
if (!(strcmp($image_type, "gallery"))) {
	$post_gallery_image = trim(tool_post::Post("profile_image"));
	$gallery_image = @filter_var($post_gallery_image, FILTER_SANITIZE_URL);
	$gallery_name = @basename($gallery_image);
	$gallery_path = __DILECTIO_PROFILES."default/".$gallery_name;
	if (@file_exists($gallery_path)) {
		@copy($gallery_path, $avatar_image);
	}
	$ret = true;
}
else if (!(strcmp($image_type, "upload"))) {
	$post_file = tool_post::File("profile_image-file");
	if (!(is_null($post_file))) {
		$file = new tool_post_file($post_file);
		$file->set_authorized_ext(array("jpg", "jpeg", "png", "gif"));
		$file->load();
		if ($file->is_valid()) {
			$avatar = new tool_post_avatar($user_id, $file);
			$ret = $avatar->move_and_resize_uploaded_file($message);
		}
		else {
			$message = $file->message();
		}
	}
	else {
		$message = "upload_err_no_file";
	}
}
else if (!(strcmp($image_type, "url"))) {
	$url = tool_post::Post("profile_image");
	if (strlen($url) > 0) {
		$avatar = new tool_url_avatar($user_id, $url);
		$ret = $avatar->move_and_resize_uploaded_file($message);
	}
	else {
		$message = "url_avatar_err_empty";
	}
}
else {
	$ret = true;
}

if (!($ret)) {
	$titre = lang_i18n::trad($langue, "error");
	$msg = lang_i18n::trad($langue, $message);
	echo json_encode(array("valide" => false, "titre" => $titre, "msg" => $msg));
	die();
}

/* Traitements en BDD */
db::open(__DILECTIO_PREFIXE_DB);
$profile = db_profile::get($user_id);
if (!(is_null($profile))) {
	$profile->alias = $alias;
	$profile->email = $email;
	$profile->language = $lang;
	db::store($profile);
	tool_session::ecrire_param("lang", $lang);
}
db::close();

echo json_encode(array("valide" => true));

