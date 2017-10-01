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

/* Contrôle du paramètre category_id */
$category_id = (int) tool_post::Post("category_id");
if ($category_id == 0) {
	echo json_encode(array("valide" => false));
	die();
}
/* Contrôle du paramètre label */
$label = tool_post::Post("label");
if (strlen($label) == 0) {
	echo json_encode(array("valide" => false));
	die();
}

/* Enregistrement du label */
db::open(__DILECTIO_PREFIXE_DB);
$category = db_category::get($category_id);
if (!(is_null($category))) {
	$category->label = $label;
	db::store($category);
}
db::close();

echo json_encode(array("valide" => true, "done_id" => $category_id));

