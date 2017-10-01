<?php

/**
 * Dilectio : Classe pour l'interfaçage du tiers altorouter
 */
require_once "third/altorouter/AltoRouter.php";

class router {
	public static function load($classe_page, $arg = null) {
		require_once "php/init.php";

		try {
			$page = (is_null($arg))?new $classe_page():new $classe_page($arg);
		}
		catch (Exception $e) {
			die("ERREUR : Impossible de router la classe ".$classe_page);
		}
		$page->render();
	}

	public static function load_page($classe_page, $arg_1 = null, $arg_2 = null) {
		require_once "php/init.php";

		/* Contrôle de la session */
		tool_session::ouvrir_et_verifier();

		/* Rendu de la page */
		if (class_exists($classe_page)) {
			try {
				$page = is_null($arg_1)?new $classe_page():(is_null($arg_2)?new $classe_page($arg_1):new $classe_page($arg_1, $arg_2));
			}
			catch (Exception $e) {
				die("ERREUR : Impossible de charger la page ".$classe_page);
			}
			$page->render();
		}
		else {
			die("ERREUR : Impossible de charger la page ".$classe_page);
		}
	}
	
	public static function load_image($classe_image, $arg_1 = null, $arg_2 = null) {
		require_once "php/init.php";

		/* Contrôle de la session */
		tool_session::ouvrir();
		$session_ok = tool_session::verifier();
		if (!($session_ok)) {
			$url = __DILECTIO_IMAGES."red-error.png";
			if (@file_exists($url)) {
				header("Content-Type: image/png");
				readfile($url);
			}
			die();
		}

		/* Rendu de la page */
		if (class_exists($classe_image)) {
			try {
				$image = is_null($arg_1)?new $classe_image():(is_null($arg_2)?new $classe_image($arg_1):new $classe_image($arg_1, $arg_2));
			}
			catch (Exception $e) {
				die("ERREUR : Impossible de charger l'image ".$classe_image);
			}
			$image->render();
		}
		else {
			die("ERREUR : Impossible de charger l'image ".$classe_image);
		}
	}
	
	public static function load_ajax($page, $script) {
		require_once "php/init.php";
		$url = "php/control/ajax/".$page."/".$script.".php";
		require_once $url;
	}
}