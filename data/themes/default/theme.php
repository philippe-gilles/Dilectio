<?php

/**
 * Dilectio : Classe pour l'interfaçage du thème default
 */

class theme_default extends dilectio_theme implements dilectio_extension {
	public static function head($langue) {
		$css = self::path()."material.min.css";
		o_b::link(_rel , "stylesheet", _href, $css, _n);
		$css = self::path()."theme.css";
		o_b::link(_rel , "stylesheet", _href, $css, _n);
	}

	public static function body($langue) {}
}