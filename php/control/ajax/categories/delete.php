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

db::open(__DILECTIO_PREFIXE_DB);

/* Toutes les conversations de cette catégorie passent en indéfini */
db_thread::uncategorize($category_id);

/* Effacement de la catégorie */
db_category::delete($category_id);

db::close();

echo json_encode(array("valide" => true, "done_id" => $category_id));

