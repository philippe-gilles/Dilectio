<?php

/**
 * Dilectio : Classe de gestion du HTML des tiers
 */

abstract class dilectio_third {
	public static function path() {
		$classe = get_called_class();
		$tiers = str_replace("third_", "", $classe);
		$ret = __DILECTIO_THIRD.$tiers."/";
		return $ret;
	}
}