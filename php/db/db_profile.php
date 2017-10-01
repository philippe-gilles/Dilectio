<?php

class db_profile extends db_table {
	public static function last_login($arg) {
		$profile = db::findOne(db::table("profile"), " last_login = ? ", array($arg));
		return $profile;
	}
}