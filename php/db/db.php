<?php

/**
 * Dilectio : Classe de gestion de la base de données
 */
 
require_once __DILECTIO_THIRD."redbean/rb.php";

class db extends R {
	private static $prefixe = null;

	public static function open($prefixe = "") {
		self::$prefixe = $prefixe;
		@chmod(__DILECTIO_DB, 0700);
		$database = __DILECTIO_DB."/db.sqlite";
		@chmod($database, 0700);
		R::setup("sqlite:".$database);
		R::freeze(true);
	}
	
	public static function table($table) {
		$ret = "";
		if (strlen(self::$prefixe) > 0) {
			$ret .= self::$prefixe."_";
		}
		$ret .= $table;
		return $ret;
	}
	
	// Pour éviter le plantage de R::dispense sur les noms avec underscore
	public static function instance($table) {
        return R::getRedBean()->dispense($table); 
    }
	
	public function now() {
		$ret = date("Y-m-d H:i:s");
		return $ret;
	}
}