<?php

class db_read extends db_table {
	public static function post_read($post_id) {
		$reads = db::find(db::table("read"), " post_id = :post_id ", array(":post_id" => $post_id));
		return (count($reads) > 0);
	}

	public static function post_read_by($post_id, $profile_id) {
		$read = db::findOne(db::table("read"), " post_id = :post_id AND profile_id = :profile_id ", array(":post_id" => $post_id, ":profile_id" => $profile_id));
		return (!(is_null($read)));
	}
}