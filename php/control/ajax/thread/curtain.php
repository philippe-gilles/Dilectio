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

/* Contrôle du paramètre post_id */
$post_id = (int) tool_post::Post("post_id");

/* Constitution du HTML */
db::open(__DILECTIO_PREFIXE_DB);
if ($post_id > 0) {
	$gift_open = db_gift_open::instance();
	$gift_open->post_id = $post_id;
	$gift_open->profile_id = $user_id;
	db::store($gift_open);
	tool_notification::insert_post("unwrap", $post_id);
}
db::close();

echo json_encode(array("valide" => true));

