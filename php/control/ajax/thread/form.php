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

/* Contrôle du paramètre thread_id */
$thread_id = (int) tool_post::Post("thread_id");

/* Contrôle du paramètre type_id */
$type_id = (int) tool_post::Post("type_id");

/* Constitution du HTML */
$html = "";
if (($thread_id > 0) && ($type_id > 0)) {
	db::open(__DILECTIO_PREFIXE_DB);
	$type = db_type::get($type_id);
	$nom_type = $type->name;
	$classe_type = "type_".$nom_type;
	if (class_exists($classe_type)) {
		$html = $classe_type::form($langue, $thread_id, $type_id);
	}
	db::close();
}

echo json_encode(array("valide" => true, "html" => $html));

