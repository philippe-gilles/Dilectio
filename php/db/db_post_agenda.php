<?php

class db_post_agenda extends db_table {
	public static function events($month, $year) {
		$events = db::find(db::table("post_agenda"), " cast(strftime('%m',  date) as int) = :month AND cast(strftime('%Y',  date) as int) = :year ORDER BY date, time ", array(":month" => $month, ":year" => $year));
		return $events;
	}
	
}