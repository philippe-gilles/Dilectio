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
$user_id = tool_session::lire_param("profil_id");

/* Récupération de la langue */
$langue = tool_session::lire_param("lang");
lang_i18n::init($langue);
$component = new view_component_post($langue);

/* Contrôle du paramètre post_id */
$post_id = (int) tool_post::Post("post_id");
if ($post_id == 0) {
	echo json_encode(array("valide" => false));
	die();
}

/* Contrôle du paramètre emotion */
$emotion_id = tool_post::Post("emotion_id");

/* Constitution du HTML */
$html = "";
db::open(__DILECTIO_PREFIXE_DB);
if ($emotion_id > 0) {
	$ret = db_post_emotion::emotionned($post_id, $emotion_id);
	if ($ret === false) {
		echo json_encode(array("valide" => false));
		die();
	}
	tool_notification::insert_post("emotionned", $post_id);
}
else {
	$ret = db_post_emotion::unemotionned($post_id);
	if ($ret === false) {
		echo json_encode(array("valide" => false));
		die();
	}
	tool_notification::insert_post("unemotionned", $post_id);
}
$html = $component->menu_emotions($user_id, $post_id);
db::close();

echo json_encode(array("valide" => true, "post_id" => $post_id, "html" => $html));

