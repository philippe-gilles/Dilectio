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

/* Contrôle du paramètre post_id */
$post_id = (int) tool_post::Post("post_id");

/* Constitution du HTML */
$valide = false;
$html = "";
if ($post_id > 0) {
	db::open(__DILECTIO_PREFIXE_DB);
	$post = db_post::get($post_id);
	if (!(is_null($post))) {
		$type_id = $post->type_id;
		$type_post_id = $post->type_post_id;
		if (($type_id > 0) && ($type_post_id > 0)) {
			$type = db_type::get($type_id);
			if (!(is_null($type))) {
				$nom_type = $type->name;
				$classe_type = "type_".$nom_type;
				if (class_exists($classe_type)) {
					$html = $classe_type::edit($langue, $post_id, $type_id, $type_post_id);
					$valide = true;
				}
			}
		}
	}
	db::close();
}

echo json_encode(array("valide" => $valide, "html" => $html));

