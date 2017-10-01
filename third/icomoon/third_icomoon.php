<?php

/**
 * Dilectio : Classe pour l'interfaçage du tiers Icomoon
 */

class third_icomoon extends dilectio_third implements dilectio_extension {
	public static function head($langue) {
		$css = self::path()."style.css";
		o_b::link(_rel , "stylesheet", _href, $css, _n);
	}

	public static function body($langue) {}
}