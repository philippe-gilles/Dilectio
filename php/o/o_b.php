<?php

/*
 * Dilectio : Classe de bufferisation de l'output HTML
 */

class o_b {
	private static $buffer = "";

	public function clean() {
		self::$buffer = "";
	}

	public function read() {
		return (self::$buffer);
	}
	
	public function write($str) {
		self::$buffer .= $str;
	}
	
	public function w($str) {
		self::write($str);
	}


	public function flush() {
		printf("%s\n", self::$buffer);
		self::clean();
	}

	public function dump() {
		printf("%s\n", nl2br(htmlentities(self::$buffer)));
	}

	public static function __callStatic($methode, $args) {
		self::write(call_user_func_array("o::".$methode, $args));
	}
}