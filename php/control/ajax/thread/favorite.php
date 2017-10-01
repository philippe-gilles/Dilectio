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

/* Contrôle du paramètre post_id */
$post_id = (int) tool_post::Post("post_id");

/* Contrôle du paramètre checked */
$is_checked = tool_post::Post("is_checked");

/* Constitution du HTML */
db::open(__DILECTIO_PREFIXE_DB);
if ($is_checked > 0) {
	$ret = db_favorite::favorite($post_id);
	if ($ret === false) {
		echo json_encode(array("valide" => false));
		die();
	}
	tool_notification::insert_post("favorite", $post_id);
}
else {
	$ret = db_favorite::unfavorite($post_id);
	if ($ret === false) {
		echo json_encode(array("valide" => false));
		die();
	}
	tool_notification::insert_post("unfavorite", $post_id);
}
db::close();

echo json_encode(array("valide" => true, "fav_id" => $post_id));

