<?php

/*
 * Dilectio : Script AJAX pour login
 */

/* Messages dans la langue par défaut */
lang_i18n::init(__DILECTIO_LANGUE_DEFAUT);
$titre_erreur = lang_i18n::trad(__DILECTIO_LANGUE_DEFAUT, "error");
$message_erreur = lang_i18n::trad(__DILECTIO_LANGUE_DEFAUT, "login_bad_login");

/* Contrôle du paramètre langue */
$post_lang = trim(tool_post::Post("alias"));
$lang = @filter_var($post_lang, FILTER_SANITIZE_STRING); 
$lang = tool_post::Post("lang");
if ((strlen($lang) < 2) || (strlen($lang) > 5)) {
	/* TODO : HONEYPOT !!! */
	echo json_encode(array("valide" => false, "titre" => $titre_erreur, "msg" => $message_erreur));
	die();
}

/* Contrôle du paramètre profil id */
$post_profil_id = trim(tool_post::Post("profil-id"));
$profil_id = @filter_var($post_profil_id, FILTER_SANITIZE_STRING); 
if (strlen($profil_id) == 0) {
	/* TODO : HONEYPOT !!! */
	echo json_encode(array("valide" => false, "titre" => $titre_erreur, "msg" => $message_erreur));
	die();
}

/* Contrôle du paramètre login */
$post_dilectio = trim(tool_post::Post("dilectio"));
if (strlen($post_dilectio) > 0) {
	/* TODO : HONEYPOT !!! */
	echo json_encode(array("valide" => false, "titre" => $titre_erreur, "msg" => $message_erreur));
	die();
}

/* Recherche du last_login */
db::open(__DILECTIO_PREFIXE_DB);
$profil = db_profile::last_login($profil_id);
if (is_null($profil)) {
	/* TODO : HONEYPOT !!! */
	echo json_encode(array("valide" => false, "titre" => $titre_erreur, "msg" => $message_erreur));
	die();
}

/* Contrôle du paramètre alias (vérifier l'intérêt) */
$post_alias = trim(tool_post::Post("alias"));
$alias = @filter_var($post_alias, FILTER_SANITIZE_STRING);
if (strcmp($alias, $profil->alias)) {
	/* TODO : HONEYPOT !!! */
	echo json_encode(array("valide" => false, "titre" => $titre_erreur, "msg" => $message_erreur));
	die();
}

/* On a passé les premiers tests : récupération des infos */
$profil_id = $profil->id;
$langue = (strlen($profil->language) > 0)?$profil->language:__DILECTIO_LANGUE_DEFAUT;
$theme = (strlen($profil->theme) > 0)?$profil->theme:__DILECTIO_THEME_DEFAUT;

/* Passage dans la langue du profil */
lang_i18n::init($langue);
$titre_erreur = lang_i18n::trad($langue, "error");
$message_erreur = lang_i18n::trad($langue, "login_bad_password");

/* Contrôle du paramètre mot de passe */
$mot_de_passe = tool_post::Post("mot-de-passe");
if (strlen($mot_de_passe) == 0) {
	echo json_encode(array("valide" => false, "titre" => $titre_erreur, "msg" => $message_erreur));
	die();
}

/* Vérification du mot de passe */
$match = password_verify($mot_de_passe, $profil->password);
if (!($match)) {
	echo json_encode(array("valide" => false, "titre" => $titre_erreur, "msg" => $message_erreur));
	die();
}

/* Démarrage de la session */
$session_ok = tool_session::demarrer();
if (!($session_ok)) {
	echo json_encode(array("valide" => false, "titre" => $titre_erreur, "msg" => $message_erreur));
	die();
}

$last_login_time = time();
$last_login = md5($last_login_time);
while (!(is_null(db_profile::last_login($last_login)))) {
	$last_login_time -= 100;
	$last_login = md5($last_login_time);
}
$profil->last_login = $last_login;
db::store($profil);
db::close();

/* Enregistrement du profil connecté */
tool_session::ecrire_param("profil_id", $profil->id);
tool_session::ecrire_param("lang", $langue);
tool_session::ecrire_param("theme", $theme);

echo json_encode(array("valide" => true));

