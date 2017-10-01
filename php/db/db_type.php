<?php

class db_type {
	public static function get($id) {
		$ret = db::load(db::table("type"), $id);
		return $ret;
	}

	public static function all() {
		$liste = db::findAll(db::table("type"));
		return $liste;
	}
	
	public static function active() {
		$liste = db::find(db::table("type"), " active > 0 ");
		return $liste;
	}
}