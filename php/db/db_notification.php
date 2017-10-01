<?php

class db_notification extends db_table {

	public static function unread_by($user_id, $starting_from = 0) {
		if ($starting_from > 0) {
			$date_from = date("Y-m-d H:i:s", $starting_from);
			$liste = db::find(db::table("notification"), " profile_id <> :profile_id AND date > :date_from ORDER BY date DESC ", array(":profile_id" => $user_id, ":date_from" => $date_from));
		}
		else {
			$user = db_profile::get($user_id);
			$last_notif_read = $user->last_notif_read;
			if (is_null($last_notif_read)) {
				$liste = db::find(db::table("notification"), " profile_id <> :profile_id ORDER BY date DESC ", array(":profile_id" => $user_id));
			}
			else {
				$liste = db::find(db::table("notification"), " profile_id <> :profile_id AND date > :last_notif_read ORDER BY date DESC ", array(":profile_id" => $user_id, "last_notif_read" => $last_notif_read));
			}
		}
		return $liste;
	}

	public static function insert($user_id, $action, $thread_id, $post_id) {
		$ret_id = false;
		$now = date("Y-m-d H:i:s");
		$notification = self::instance();
		if (!(is_null($notification))) {
			$notification->profile_id = $user_id;
			$notification->date = $now;
			$notification->action = $action;
			$notification->thread_id = $thread_id;
			$notification->post_id = $post_id;
			$ret_id = db::store($notification);
		}
		return $ret_id;
	}
}