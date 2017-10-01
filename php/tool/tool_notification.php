<?php

class tool_notification {
	public static function insert_thread($action, $thread_id) {
		self::insert("thread_".$action, $thread_id, 0);
	}

	public static function insert_post($action, $post_id) {
		self::insert("post_".$action, 0, $post_id);
	}

	public static function insert($action, $thread_id = 0, $post_id  = 0) {
		/* Récupération du user */
		$user_id = tool_session::lire_param("profil_id");

		/* Insertion en BDD */
		$ret_id = db_notification::insert($user_id, "notification_".$action, $thread_id, $post_id);
		return $ret_id;
	}
	
}