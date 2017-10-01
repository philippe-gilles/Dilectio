<?php

/**
 * Dilectio : Classe pour l'interfaçage du tiers zxcvbn
 */

class third_zxcvbn extends dilectio_third implements dilectio_extension {
	public static function head($langue) {}

	public static function body($langue) {
		$entries = array("keyboard_patterns_etc", "avoid_repeated_characters_etc", "avoid_repeated_words_etc", "avoid_sequences_etc", "avoid_dates_and_years_etc", "very_common_etc", "word_by_itself_etc", "names_and_surnames_etc", "capitalization_etc", "reversed_words_etc", "predictable_substitutions_etc");
		$i18n_js = "security_i18n = {";
		foreach($entries as $entry) {
			$zxcvbn_entry = "zxcvbn_".$entry;
			$zxcvbn_i18n = lang_i18n::trad($langue, $zxcvbn_entry);
			$i18n_js .= sprintf("\"%s\" : \"%s\",", $zxcvbn_entry, $zxcvbn_i18n);
		}
		$i18n_js .= "}";
		o_b::script_script($i18n_js, _type , "application/javascript", _n);
	}
}