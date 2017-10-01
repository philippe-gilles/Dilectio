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

/* Contrôle du paramètre thread_id */
$thread_id = (int) tool_post::Post("thread-id");

/* Contrôle du paramètre type_id */
$type_id = (int) tool_post::Post("type-id");

/* Contrôle du paramètre gift */
$gift = tool_post::Post("gift");
$is_gift = !(is_null($gift));

/* Constitution du HTML */
$html = "";
$type_message = "";
$message = "";
$post_id = -1;
if (($thread_id > 0) && ($type_id > 0)) {
	db::open(__DILECTIO_PREFIXE_DB);
	$type = db_type::get($type_id);
	$nom_type = $type->name;
	$classe_type = "type_".$nom_type;
	if (class_exists($classe_type)) {
		$type_post_id = $classe_type::submit($user_id, $type_message, $message);
		if ($type_post_id  > 0) {
			$post = db_post::instance();
			$post->profile_id = $user_id;
			$post->creation = db::now();
			$post->type_id = $type_id;
			$post->type_post_id = $type_post_id;
			$post->thread_id = $thread_id;
			$post->is_gift = $is_gift;
			$post_id = db::store($post);
			if ($post_id > 0) {
				$component = new view_component_post($langue);
				$html_type = $classe_type::post($user_id, $post_id, $type_post_id);
				$html = $component->post($user_id, $post, $html_type);
				tool_notification::insert_post("creation", $post_id);
			}
		}
		else {
			/* On ne renvoie un message que s'il y a quelque chose à dire ! */
			if (strlen($message) > 0) {
				$titre = lang_i18n::trad($langue, "error");
				$msg = lang_i18n::trad($langue, $message);
				echo json_encode(array("valide" => false, "type" => $type_message, "titre" => $titre, "msg" => $msg));
				die();
			}
		}
	}
	db::close();
}

echo json_encode(array("valide" => true, "post_id" => $post_id, "html" => $html, "msg" => $message));

