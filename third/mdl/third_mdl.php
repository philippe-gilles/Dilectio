<?php

/**
 * Dilectio : Classe pour l'interfaçage du tiers MDL
 */

class third_mdl extends dilectio_third implements dilectio_extension {
	public static function head($langue) {}

	public static function body($langue) {
		$script = self::path()."material.min.js";
		o_b::script_script("", _type , "application/javascript", _src , $script, _n);
	}
}