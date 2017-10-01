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
$user_id = (int) tool_session::lire_param("profil_id");
if ($user_id < 1) {
	echo json_encode(array("valide" => false));
	die();
}

/* Récupération de la langue */
$langue = tool_session::lire_param("lang");
lang_i18n::init($langue);

/* Contrôle des paramètres */
$thread_title = tool_post::Post("thread-title");
if (strlen($thread_title) == 0) {
	$titre = lang_i18n::trad($langue, "error");
	$msg = lang_i18n::trad($langue, "thread_err_name_empty");
	echo json_encode(array("valide" => false, "titre" => $titre, "msg" => $msg));
	die();
}

db::open(__DILECTIO_PREFIXE_DB);
$thread_category = (int) tool_post::Post("thread-categorie");
if ($thread_category > 0) {
	$exists = db_category::exists($thread_category);
	if (!($exists)) {
		$titre = lang_i18n::trad($langue, "error");
		$msg = lang_i18n::trad($langue, "category_err_not_found");
		echo json_encode(array("valide" => false, "titre" => $titre, "msg" => $msg));
		die();
	}
}

$thread = db_thread::instance();
$thread->creation = db::now();
$thread->label = $thread_title;
$thread->category_id = $thread_category;
$thread->profile_id = $user_id;
$thread_id = db::store($thread);

db::close();

if ($thread_id == 0) {
	$titre = lang_i18n::trad($langue, "error");
	$msg = lang_i18n::trad($langue, "thread_err_could_not_create");
	echo json_encode(array("valide" => false, "titre" => $titre, "msg" => $msg));
	die();
}
tool_notification::insert_thread("creation", $thread_id);
echo json_encode(array("valide" => true, "thread_id" => $thread_id));

