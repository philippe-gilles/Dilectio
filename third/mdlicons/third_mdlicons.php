<?php

/**
 * Dilectio : Classe pour l'interfaçage du tiers icones MDL
 */

class third_mdlicons extends dilectio_third implements dilectio_extension {
	public static function head($langue) {
		$css = self::path()."material-icons.css";
		o_b::link(_rel , "stylesheet", _href, $css, _n);
	}
	
	public static function body($langue) {}
}