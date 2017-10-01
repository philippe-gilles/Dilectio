<?php

/**
 * Dilectio : Classe pour l'interfaçage du tiers object fit image
 */

class third_objectfit extends dilectio_third implements dilectio_extension {
	public static function head($langue) {}

	public static function body($langue) {
		$script = self::path()."objectfit.js";
		o_b::script_script("", _type , "application/javascript", _src , $script, _n);
	}
}