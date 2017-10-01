<?php
require_once __DILECTIO_THIRD."zapcal/third_zapcal.php";

/* Contrôle de la session (à remonter dans le router ?) */
tool_session::ouvrir();
$session_ok = tool_session::verifier(false);
if (!($session_ok)) {die();}

/* Contrôle du paramètre agenda_id */
$agenda_id = (int) tool_post::Post("agenda-id");

if ($agenda_id > 0) {
	db::open(__DILECTIO_PREFIXE_DB);

	$agenda = db::load(db::table("post_agenda"), $agenda_id);
	if (!(is_null($agenda))) {
		$title = $agenda->label;
		$description = $agenda->caption;
		$event_date = $agenda->date;
		$event_time = $agenda->time;
		$event_start = $event_date;
		if (strlen($event_time) > 0) {
			$event_start .= " ".$event_time;
		}
		db::close();

		/* Header ICAL */
		header("Content-type: text/calendar; charset=utf-8");
		header("Content-Disposition: attachment; filename=dilectio-cal-".md5($agenda_id).".ics");

		// create the ical object
		$icalobj = new ZCiCal();

		// create the event within the ical object
		$eventobj = new ZCiCalNode("VEVENT", $icalobj->curnode);

		// add title
		$eventobj->addNode(new ZCiCalDataNode("SUMMARY:" . $title));

		// add start date
		$eventobj->addNode(new ZCiCalDataNode("DTSTART:" . ZCiCal::fromSqlDateTime($event_start)));

		// add end date
		// $eventobj->addNode(new ZCiCalDataNode("DTEND:" . ZCiCal::fromSqlDateTime($event_end)));

		// UID is a required item in VEVENT, create unique string for this event
		// Adding your domain to the end is a good way of creating uniqueness
		$uid = uniqid()."@dilect.io";
		$eventobj->addNode(new ZCiCalDataNode("UID:" . $uid));

		// DTSTAMP is a required item in VEVENT
		$eventobj->addNode(new ZCiCalDataNode("DTSTAMP:" . ZCiCal::fromSqlDateTime()));

		// Add description
		$eventobj->addNode(new ZCiCalDataNode("Description:" . ZCiCal::formatContent($description)));

		// write iCalendar feed to stdout
		echo $icalobj->export();
	}
}
