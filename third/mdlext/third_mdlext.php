<?php

/**
 * Dilectio : Classe pour l'interfaçage du tiers MDL Extension
 */

class third_mdlext extends dilectio_third implements dilectio_extension {
	public static function head($langue) {
		$css = self::path()."material.components.ext.min.css";
		o_b::link(_rel , "stylesheet", _href, $css, _n);
	}

	public static function body($langue) {
		$script = self::path()."material.components.ext.min.js";
		o_b::script_script("", _type , "application/javascript", _src , $script, _n);
	}
}