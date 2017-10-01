<?php

/**
 * Dilectio : Classe pour l'interfaçage du tiers swipebox
 */

class third_swipebox extends dilectio_third implements dilectio_extension {
	public static function head($langue) {
		$css = self::path()."css/swipebox.min.css";
		o_b::link(_rel , "stylesheet", _href, $css, _n);
	}

	public static function body($langue) {
		$script = self::path()."js/jquery.swipebox.min.js";
		o_b::script_script("", _type , "application/javascript", _src , $script, _n);
	}
}