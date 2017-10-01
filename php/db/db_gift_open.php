<?php

class db_gift_open extends db_table {
	public static function is_opened_by($post_id, $profile_id) {
		$read = db::findOne(db::table("gift_open"), " post_id = :post_id AND profile_id = :profile_id ", array(":post_id" => $post_id, ":profile_id" => $profile_id));
		return (!(is_null($read)));
	}
}