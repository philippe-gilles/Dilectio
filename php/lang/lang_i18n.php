<?php

/**
 * Dilectio : Classe de gestion de l'internationalisation I18N
 */

define("__DILECTIO_LANG_PREFIXE_TYPE", "type_");

require_once __DILECTIO_THIRD."php-i18n/i18n.class.php";

class lang_i18n {
	private static $i18n = array();

	public static function init($lang) {
		if (!(array_key_exists($lang, self::$i18n))) {
			$i18n = new i18n();
			$i18n->setCachePath(__DILECTIO_PHP_LANG_CACHE);
			$i18n->setFilePath(__DILECTIO_LANG."lang_{LANGUAGE}.ini");
			$i18n->setFallbackLang(__DILECTIO_LANGUE_DEFAUT);
			$i18n->setPrefix("lang_i18n_".$lang);
			$i18n->setForcedLang($lang);
			$i18n->setMergeFallback(true);
			$i18n->init();
			self::$i18n[$lang] = $i18n;
		}
	}
	
	public static function __callStatic($methode, $args) {
		if ($methode === "trad") {
			$lang = array_shift($args);
			$methode = "lang_i18n_".$lang;
			$cle = array_shift($args);
			/* Error handler pour gérer proprement le warning PHP en cas d'entrée absente */
			set_error_handler(function($errno, $errstr, $errfile, $errline, array $errcontext) {
				if (error_reporting() === 0) {return false;}
				throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
			});
			try {
				$ret = $methode($cle, $args);
				restore_error_handler();
				return $ret;
			}
			catch (Exception $e) {
				restore_error_handler();
				return "[".$cle."] ???";
			}
		}
		else {die("ERREUR : Appel d'une méthode ".$methode." non définie dans la classe lang_i18n"); }
	}
}