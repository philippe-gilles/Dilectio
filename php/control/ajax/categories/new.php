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

/* Récupération de la langue */
$langue = tool_session::lire_param("lang");
lang_i18n::init($langue);

/* Enregistrement du label */
$html = "";
$category_id = 0;
db::open(__DILECTIO_PREFIXE_DB);
$category_id = 0;
$label = "category_new";
$category = db_category::instance();
if (!(is_null($category))) {
	$category->label = $label;
	$category->parent_category_id = 0;
	$category_id = db::store($category);
}
if ($category_id > 0) {
	$component = new view_component_nestable($langue);
	$html = $component->generate_nested_category($category_id, $label);
}

db::close();

echo json_encode(array("valide" => true, "category_id" => $category_id, "html" => $html));

