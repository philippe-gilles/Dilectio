<?php

/*
 * Dilectio : Script AJAX pour categories
 */

/* Contrôle de la session (à remonter dans le router ?) */
tool_session::ouvrir();
$session_ok = tool_session::verifier(false);
if (!($session_ok)) {
	echo json_encode(array("valide" => false));
	die();
}

/* Contrôle du paramètre tab_data */
$tab_data = isset($_POST["tab_data"])?$_POST["tab_data"]:array();
if (count($tab_data) == 0) {
	echo json_encode(array("valide" => false));
	die();
}

/* Enregistrement du label */
db::open(__DILECTIO_PREFIXE_DB);
$sql = db_category::change($tab_data);
db::close();

echo json_encode(array("valide" => true));

