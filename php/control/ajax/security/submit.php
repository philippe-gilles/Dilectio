<?php

/*
 * Dilectio : Script AJAX pour security
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

/* Contrôle du paramètre password-1 */
$post_password_1 = trim(tool_post::Post("password-1"));
$password_1 = @filter_var($post_password_1, FILTER_SANITIZE_STRING); 

/* Contrôle du paramètre password-2 */
$post_password_2 = trim(tool_post::Post("password-2"));
$password_2 = @filter_var($post_password_2, FILTER_SANITIZE_STRING); 

if ((strlen($password_1) == 0) || (strlen($password_2) == 0) || (strcmp($password_1, $password_2))) {
	$msg = lang_i18n::trad($langue, "security_password_error");
	echo json_encode(array("valide" => false, "toast" => $msg));
	die();
}

$hash = password_hash($password_1, PASSWORD_DEFAULT);
if (strlen($hash) == 0) {
	$msg = lang_i18n::trad($langue, "security_password_error");
	echo json_encode(array("valide" => false, "toast" => $msg));
	die();
}

/* Traitements en BDD */
db::open(__DILECTIO_PREFIXE_DB);
$profile = db_profile::get($user_id);
if (!(is_null($profile))) {
	$profile->password = $hash;
	db::store($profile);
}
db::close();

$msg = lang_i18n::trad($langue, "security_password_success");
echo json_encode(array("valide" => true, "toast" => $msg));

