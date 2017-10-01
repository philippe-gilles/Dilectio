<?php

/*
 * Dilectio : Script AJAX pour accueil
 */

/* Contrôle de la session (à remonter dans le router) */
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
$component = new view_component_card($langue);

/* Contrôle du paramètre id_periode */
$id_periode = (int) tool_post::Post("id_periode");

/* Contrôle du paramètre id_periode */
$id_next = (int) tool_post::Post("id_next");

/* Initialisations */
$titre = "";
$html_posts = "";
$more = false;

/* Accès à la BDD */
db::open(__DILECTIO_PREFIXE_DB);

/* Posts système */
if ($id_periode == 0) {
	$threads = db_thread::pending();
	foreach($threads as $thread) {
		$html_posts .= $component->post_pending($thread->id);
	}
}

/* Posts par période */
if ($id_periode == 0) {
	$titre = lang_i18n::trad($langue, "home_today");
	$posts = db_post::today();
}
else {
	if ($id_periode == 1) {
		$titre = lang_i18n::trad($langue, "home_recent");
		$posts = db_post::recent();
	}
	else {
		$posts = db_post::older($id_next);
	}
	$nb_posts = count($posts);
	if ($nb_posts > __DILECTIO_PERIOD_MAX_THUMBS) {
		array_pop($posts);
		$more = true;
	}
}
$date_newer = 0;
$date_older = 0;
foreach($posts as $post) {
	$date = strtotime($post->creation);
	if ($date_newer == 0) {$date_newer = $date;}
	$date_older = $date;
	$type_id = $post->type_id;
	$type = db_type::get($type_id);
	$nom_type = $type->name;
	$classe_type = "type_".$nom_type;
	if (class_exists($classe_type)) {
		$html_post = $classe_type::excerpt($post->profile_id, $post->id, $post->type_post_id);
		$html_posts .= $component->post_card($user_id, $post, $html_post);
	}
}
db::close();

/* Si on n'a pas trouvé de dates intervalle on met par défaut à aujourd'hui */
if ($date_older == 0) {
	$date_newer = time();
	$date_older = time();
}
$date_newer -= ($date_newer % 86400);
$date_older -= ($date_older % 86400);

/* Création du titre pour les périodes > 1 */
if (strlen($titre) == 0) {
	$titre = $component->format_intervalle($date_newer, $date_older);
}

echo json_encode(array("valide" => true, "id" => $id_periode, "newer" => $date_newer, "older" => $date_older, "titre" => "<h2>".$titre."</h2>", "html" => $html_posts, "more" => $more));

