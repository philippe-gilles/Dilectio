<?php

class db_post extends db_table {
	public static function post($name, $type_post_id) {
		$post = db::findOne(db::table("post"), "  type_id = (SELECT id FROM ".db::table("type")." WHERE name='".$name."') AND type_post_id = ? ", array($type_post_id));
		return $post;
	}

	public static function first_today() {
		$post = db::findOne(db::table("post"), " creation = ( SELECT MAX(creation) FROM ".db::table("post")." ) ");
		return $post;
	}

	public static function today() {
		$today = date("Y-m-d")." 00:00:00";
		$liste = db::find(db::table("post"), " creation >= ? ORDER BY creation DESC ", array($today));
		return $liste;
	}
	
	public static function recent() {
		$today = date("Y-m-d")." 00:00:00";
		/* On en ramène 1 de plus pour détecter la nécessité du "more" */
		$nb_max_posts = 1 + (int) __DILECTIO_PERIOD_MAX_THUMBS;
		$liste = db::find(db::table("post"), " creation < ? ORDER BY creation DESC LIMIT ".$nb_max_posts, array($today));
		return $liste;
	}
	
	public static function older($id_than) {
		/* On en ramène 1 de plus pour détecter la nécessité du "more" */
		$nb_max_posts = 1 + (int) __DILECTIO_PERIOD_MAX_THUMBS;
		$liste = db::find(db::table("post"), " id < ? ORDER BY creation DESC LIMIT ".$nb_max_posts, array($id_than));
		return $liste;
	}

	public static function thread($thread_id) {
		$liste = db::find(db::table("post"), " thread_id = ? ORDER BY creation ASC ", array($thread_id));
		return $liste;
	}
	
	public static function unread_posts_in_thread_by($thread_id, $profile_id) {
		$liste = db::find(db::table("post"), " thread_id = :thread_id AND profile_id <> :profile_id AND id NOT IN (SELECT post_id FROM ".db::table("read")." INNER JOIN ".db::table("post")." ON (".db::table("read").".post_id = ".db::table("post").".id) WHERE thread_id = :thread_id AND ".db::table("read").".profile_id = :profile_id)", array(":thread_id" => $thread_id, ":profile_id" => $profile_id, ":thread_id" => $thread_id, ":profile_id" => $profile_id));
		return $liste;
	}

	public static function unread_posts_in_thread_by_modifier($thread_id, $modifier_profile_id) {
		$liste = db::find(db::table("post"), " thread_id = :thread_id AND modifier_profile_id > 0 AND modifier_profile_id <> :modifier_profile_id AND id NOT IN (SELECT post_id FROM ".db::table("read")." INNER JOIN ".db::table("post")." ON (".db::table("read").".post_id = ".db::table("post").".id) WHERE thread_id = :thread_id AND ".db::table("read").".profile_id = :modifier_profile_id)", array(":thread_id" => $thread_id, ":modifier_profile_id" => $modifier_profile_id, ":thread_id" => $thread_id, ":modifier_profile_id" => $modifier_profile_id));
		return $liste;
	}

}
