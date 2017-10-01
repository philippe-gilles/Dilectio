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

/* Contrôle du paramètre thread_id */
$thread_id = (int) tool_post::Post("thread_id");

/* Constitution du HTML */
$html = "";
if ($thread_id > 0) {
	db::open(__DILECTIO_PREFIXE_DB);
	$posts = db_post::thread($thread_id);
	$html_posts = "";
	foreach($posts as $post) {
		$type_id = $post->type_id;
		$type = db_type::get($type_id);
		$nom_type = $type->name;
		$classe_type = "type_".$nom_type;
		if (class_exists($classe_type)) {
			$html_post = $classe_type::post($post->profile_id, $post->id, $post->type_post_id);
			$html_posts .= $component->post($user_id, $post, $html_post);
		}
	}
	db::close();
	$html_posts .= $component->new_post();
}

echo json_encode(array("valide" => true, "html" => $html_posts));

