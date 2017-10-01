<?php

/**
 * Dilectio : Classe pour l'interfaçage du tiers JQuery
 */

class third_jquery extends dilectio_third implements dilectio_extension {
	public static function head($langue) {}

	public static function body($langue) {
		$script = self::path()."jquery.min.js";
		o_b::script_script("", _type , "application/javascript", _src , $script, _n);
	}
}