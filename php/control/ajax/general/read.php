<?php

/*
 * Dilectio : Script AJAX pour notifications
 */

/* Contrôle de la session (à remonter dans le router ?) */
tool_session::ouvrir();
$session_ok = tool_session::verifier(false);
if (!($session_ok)) {
	echo json_encode(array("valide" => false));
	die();
}

/* Récupération du profil */
$profil_id = tool_session::lire_param("profil_id");

/* Accès à la BDD */
db::open(__DILECTIO_PREFIXE_DB);
$profil = db_profile::get($profil_id);
if (is_null($profil)) {
	echo json_encode(array("valide" => false));
	die();
}
$profil->last_notif_read = date("Y-m-d H:i:s");
db::store($profil);
db::close();

echo json_encode(array("valide" => true));

