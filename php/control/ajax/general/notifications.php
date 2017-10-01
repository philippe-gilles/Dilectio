<?php

/*
 * Dilectio : Script AJAX pour notifications
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

/* Contrôle du paramètre starting_from */
$starting_from = (int) tool_post::Post("starting_from");

/* Récupération de la langue */
$langue = tool_session::lire_param("lang");
lang_i18n::init($langue);
$component = new view_component_notification($langue);

/* Accès à la BDD */
db::open(__DILECTIO_PREFIXE_DB);
$notifications = db_notification::unread_by($user_id, $starting_from);
$html_notifications = $component->panel($notifications);
$nb_notifications = count($notifications);
db::close();

echo json_encode(array("valide" => true, "html" => $html_notifications, "count" => $nb_notifications, "time" => time()));

