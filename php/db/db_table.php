<?php

abstract class db_table {
	/* Loads the record with specified id */
	public static function get($id) {
		$classe = get_called_class();
		$table = str_replace("db_", "", $classe);
		$ret = db::load(db::table($table), $id);
		return $ret;
	}

	/* Loads all records */
	public static function all() {
		$classe = get_called_class();
		$table = str_replace("db_", "", $classe);
		$liste = db::findAll(db::table($table));
		return $liste;
	}

	/* Instantiates new record */
	public static function instance() {
		$classe = get_called_class();
		$table = str_replace("db_", "", $classe);
		$instance = db::instance(db::table($table));
		return $instance;
	}
	
	/* Check if record exists with specified id */
	public static function exists($id) {
		$classe = get_called_class();
		$table = str_replace("db_", "", $classe);
		$instance = db::findOne(db::table($table), " id = :id ", array(":id" => $id));
		return (!(is_null($instance)));
	}
}