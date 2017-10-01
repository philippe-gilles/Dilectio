<?php

/**
 * Dilectio : Classe pour l'interfaçage du tiers JConfirm
 */

class third_jconfirm extends dilectio_third implements dilectio_extension {
	public static function head($langue) {
		$css = self::path()."jconfirm.min.css";
		o_b::link(_rel , "stylesheet", _href, $css, _n);
	}

	public static function body($langue) {
		$script = self::path()."jconfirm.min.js";
		o_b::script_script("", _type , "application/javascript", _src , $script, _n);
		$script = "jconfirm.defaults = {useBootstrap: false, escapeKey: 'ok'};";
		$label_confirm_yes = lang_i18n::trad($langue, "msg_yes");
		$script .= "var confirm_yes = \"".$label_confirm_yes."\";";
		$label_confirm_no = lang_i18n::trad($langue, "msg_no");
		$script .= "var confirm_no = \"".$label_confirm_no."\";";
		o_b::script_script($script, _type , "application/javascript", _n);		
	}
}