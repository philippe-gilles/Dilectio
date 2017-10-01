<?php

/*
 * Dilectio : Script AJAX pour thread
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

/* Contrôle du paramètre type_post_id */
$post_id = (int) tool_post::Post("post-id");

/* Contrôle du paramètre type_post_id */
$type_post_id = (int) tool_post::Post("type-post-id");

/* Contrôle du paramètre type_id */
$type_id = (int) tool_post::Post("type-id");

/* Constitution du HTML */
$valide = false;
$html = "";
$message = "";
if (($post_id > 0) && ($type_post_id > 0) && ($type_id > 0)) {
	db::open(__DILECTIO_PREFIXE_DB);
	$post = db_post::get($post_id);
	if (!(is_null($post))) {
		$href = "thread-".$post->thread_id."_".$post_id;
		$type = db_type::get($type_id);
		$nom_type = $type->name;
		$classe_type = "type_".$nom_type;
		if (class_exists($classe_type)) {
			$ret = $classe_type::save($user_id, $type_post_id, $message);
			if ($ret) {
				$component = new view_component_post($langue);
				$html_type = $classe_type::post($user_id, $post_id, $type_post_id);
				$html = $component->post($user_id, $post, $html_type, false);
				/* Notification uniquement si le post a déjà été lu */
				$is_read = db_read::post_read($post_id);
				if ($is_read) {
					tool_notification::insert_post("modification", $post_id);
				}
				$valide = true;
			}
		}
	}
}

echo json_encode(array("valide" => $valide, "post_id" => $post_id, "html" => $html, "msg" => $message));

