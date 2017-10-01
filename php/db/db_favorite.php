<?php

class db_favorite extends db_table {
	public static function post_favorite_by($post_id, $profile_id) {
		$favorite = db::findOne(db::table("favorite"), " post_id = :post_id AND profile_id = :profile_id ", array(":post_id" => $post_id, ":profile_id" => $profile_id));
		return (!(is_null($favorite)));
	}
	
	public static function post_favorite_by_not($post_id, $profile_id) {
		$favorites = db::find(db::table("favorite"), " post_id = :post_id AND profile_id <> :profile_id ", array(":post_id" => $post_id, ":profile_id" => $profile_id));
		return (count($favorites) > 0);
	}

	public static function favorite($post_id) {
		$ret_id = false;
		$user_id = tool_session::lire_param("profil_id");
		$favorite = self::instance();
		$favorite->post_id = $post_id;
		$favorite->profile_id = $user_id;
		$ret_id = db::store($favorite);
		return $ret_id;
	}

	public static function unfavorite($post_id) {
		$ret = false;
		$user_id = tool_session::lire_param("profil_id");
		$favorite = db::findOne(db::table("favorite"), " post_id = :post_id AND profile_id = :profile_id ", array(":post_id" => $post_id, ":profile_id" => $user_id));
		if (!(is_null($favorite))) {
			db::trash($favorite);
			$ret = true;
		}
		return $ret;
	}
}