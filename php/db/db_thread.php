<?php

class db_thread extends db_table {
	public static function pending() {
		$liste = db::find(db::table("thread"), " WHERE id NOT IN (SELECT thread_id FROM ".db::table("post").") ORDER BY creation ");
		return $liste;
	}

	/*
	 * Ecriture directe en SQL pour optimiser la vitesse de traitement
	 */
	public static function uncategorize($category_id) {
		$sql = "UPDATE '".db::table("thread")."' ";
		$sql .= "SET category_id = 0 ";
		$sql .= "WHERE category_id = ".$category_id;
		db::exec($sql);
	}
}