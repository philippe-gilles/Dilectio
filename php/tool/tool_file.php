<?php

class tool_file {
	public static function merge_types($langues, $force = false) {
		self::merge_js_types($force);
		self::merge_css_types($force);
		$langues_unique = array_unique($langues);
		foreach($langues_unique as $langue) {
			self::merge_lang_types($langue, $force);
		}
	}

	public static function merge_js_types($force = false) {
		$target = "types.js";
		if ((!(@file_exists(__DILECTIO_JS.$target))) || ($force)) {
			$js_assetic = new third_assetic(__DILECTIO_JS, $target);
			$js_assetic->add_glob(__DILECTIO_TYPES."*/js/*.js");
			$js_assetic->save();
		}
	}
	
	public static function merge_css_types($force = false) {
		$target = "types.css";
		if ((!(@file_exists(__DILECTIO_CSS.$target))) || ($force)) {
			$css_assetic = new third_assetic(__DILECTIO_CSS, "types.css");
			$css_assetic->add_glob(__DILECTIO_TYPES."*/css/*.css");
			$css_assetic->save();
		}
	}
	
	public static function merge_lang_types($langue, $force = false) {
		$target = "lang_".$langue.".ini";
		if ((!(@file_exists(__DILECTIO_LANG.$target))) || ($force)) {
			$ini_assetic = new third_assetic(__DILECTIO_LANG, $target);
			$ini_assetic->add_file(__DILECTIO_LANG.$langue."/".$target);
			$ini_assetic->add_glob(__DILECTIO_TYPES."*/lang/".$langue."/lang_".$langue.".ini");
			$ini_assetic->save();
		}
	}
}