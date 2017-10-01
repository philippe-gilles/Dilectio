<?php

/**
 * Dilectio : Classe pour l'interfaçage du tiers dilectio-nice-select
 */

class third_niceselect extends dilectio_third implements dilectio_extension {
	public static function head($langue) {
		$css = self::path()."dilectio-nice-select.css";
		o_b::link(_rel , "stylesheet", _href, $css, _n);
	}

	public static function body($langue) {
		$script = self::path()."dilectio-nice-select.js";
		o_b::script_script("", _type , "application/javascript", _src , $script, _n);
	}
}