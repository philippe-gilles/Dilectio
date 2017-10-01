<?php
require_once __DILECTIO_THIRD."zapcal/third_zapcal.php";
require_once __DILECTIO_THIRD."phpmailer/third_phpmailer.php";

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

/* Contrôle du paramètre agenda_id */
$agenda_id = (int) tool_post::Post("agenda-id");
if ($agenda_id < 1) {
	echo json_encode(array("valide" => false));
	die();
}

/* Récupération de la langue */
$langue = tool_session::lire_param("lang");
lang_i18n::init($langue);

db::open(__DILECTIO_PREFIXE_DB);

$valide = false;
$agenda = db::load(db::table("post_agenda"), $agenda_id);
$user = db_profile::get($user_id);
if ((!(is_null($agenda))) && (!(is_null($user)))) {
	$title = $agenda->label;
	$description = $agenda->caption;
	$event_date = $agenda->date;
	$event_time = $agenda->time;
	$event_start = $event_date;
	if (strlen($event_time) > 0) {
		$event_start .= " ".$event_time;
	}
	db::close();

	// create the ical object
	$icalobj = new ZCiCal();

	// create the event within the ical object
	$eventobj = new ZCiCalNode("VEVENT", $icalobj->curnode);

	// add title
	$eventobj->addNode(new ZCiCalDataNode("SUMMARY:" . $title));

	// add start date
	$eventobj->addNode(new ZCiCalDataNode("DTSTART:" . ZCiCal::fromSqlDateTime($event_start)));

	// UID is a required item in VEVENT, create unique string for this event
	// Adding your domain to the end is a good way of creating uniqueness
	$uid = uniqid()."@dilect.io";
	$eventobj->addNode(new ZCiCalDataNode("UID:" . $uid));

	// DTSTAMP is a required item in VEVENT
	$eventobj->addNode(new ZCiCalDataNode("DTSTAMP:" . ZCiCal::fromSqlDateTime()));

	// Add description
	$eventobj->addNode(new ZCiCalDataNode("Description:" . ZCiCal::formatContent($description)));

	// write iCalendar feed to file
	$folder = __DILECTIO_PROFILES."profile-".$user_id."/agenda/";
	if (!(@is_dir($folder))) {@mkdir($folder, 0700, true);}
	$file = "dilectio-cal-".md5($agenda_id).".ics";
	$ret = file_put_contents($folder.$file, $icalobj->export());
	if ($ret !== false) {
		$mailer = new third_phpmailer($user->alias, $user->email);

		$titre = "Ceci est un test !";
		$message = "Bonjour,<br>Vous avez un rendez-vous d'<strong>amour</strong> n°".$agenda_id." ;-)<br><br><p style='font-size:80%'>Philippe</p>";

		$ret = $mailer->attach($folder.$file);
		if ($ret) {
			$valide = $mailer->send($titre, $message);
		}
		@unlink($folder.$file);
	}
}
db::close();
if ($valide) {
	$message = lang_i18n::trad($langue, "type_agenda_msg_send_ok");
}
else {
	$message = lang_i18n::trad($langue, "type_agenda_err_send_nok");
}

echo json_encode(array("valide" => $valide, "msg" => $message));
