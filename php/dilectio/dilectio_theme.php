<?php

/**
 * Dilectio : Classe de gestion du HTML des thèmes
 */

abstract class dilectio_theme {
	public static function path() {
		$classe = get_called_class();
		$theme = str_replace("theme_", "", $classe);
		$ret = __DILECTIO_THEMES.$theme."/";
		return $ret;
	}
}