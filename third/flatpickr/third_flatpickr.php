<?php

/**
 * Dilectio : Classe pour l'interfaçage du tiers flatpickr
 */

class third_flatpickr extends dilectio_third implements dilectio_extension {
	public static function head($langue) {
		$css = self::path()."flatpickr.min.css";
		o_b::link(_rel , "stylesheet", _href, $css, _n);
	}

	public static function body($langue) {
		$script = self::path()."flatpickr.min.js";
		o_b::script_script("", _type , "application/javascript", _src , $script, _n);
		if ((strlen($langue) > 0) && strncmp($langue, "en", 2)) {
			$script = self::path()."l10n/".$langue.".js";
			o_b::script_script("", _type , "application/javascript", _src , $script, _n);
		}
		$script = "var flatpickr_default_options = {";
		$script .= "enableTime: true,";
		$script .= "altInput: true,";
		$options_l10n = self::l10n($langue);
		foreach($options_l10n as $option => $valeur) {
			$script .= $option.": ".$valeur.",";
		}
		$script .= "'plugins': [new confirmDatePlugin({";
		$script .= "confirmIcon: \"<i></i>\",";
		$script .= "confirmText: \"OK\",";
		$script .= "showAlways: true})]";
		$script .= "};";
		o_b::script_script($script, _type , "application/javascript", _n);		
	}
	
	private static function l10n($langue) {
		switch($langue) {
			case "fr":
				return array("altFormat" => "'j F Y à H\\\\hi'", "locale" => "'fr'", "time_24hr" => "true");
			default:
				return array("altFormat" => "'F j,Y at h:i K'", "locale" => "'en'", "time_24hr" => "false");
		}
	}
}
