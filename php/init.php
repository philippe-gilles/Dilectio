<?php

/*
 * Dilectio : Initialisations
 */

/* Déclaration des chemins */
define("__DILECTIO_DB", "base/");
define("__DILECTIO_ASSETS", "assets/");
define("__DILECTIO_CSS", __DILECTIO_ASSETS."css/");
define("__DILECTIO_JS", __DILECTIO_ASSETS."js/");
define("__DILECTIO_LANG", __DILECTIO_ASSETS."lang/");
define("__DILECTIO_IMAGES", __DILECTIO_ASSETS."images/");
define("__DILECTIO_PHP", "php/");
define("__DILECTIO_PHP_LANG_CACHE", __DILECTIO_PHP."lang/cache/");
define("__DILECTIO_PHP_CONTROL", __DILECTIO_PHP."control/");
define("__DILECTIO_PHP_VIEW", __DILECTIO_PHP."view/");
define("__DILECTIO_DATA", "data/");
define("__DILECTIO_PROFILES", __DILECTIO_DATA."profiles/");
define("__DILECTIO_THEMES", __DILECTIO_DATA."themes/");
define("__DILECTIO_THIRD", "third/");
define("__DILECTIO_TYPES", "types/");

/* Constantes non configurables */
define("__DILECTIO_FONT_LOGO", "Caesar Dressing");

/* Fonction d'autoload */
function dilectio_autoload($nom_classe) {
	$chemin = __DILECTIO_PHP;
	$tab_nom_classe = explode("_", $nom_classe);
	if (count($tab_nom_classe) > 0) {
		$prefixe = $tab_nom_classe[0];
		if ($prefixe === "third") {
			$chemin = __DILECTIO_THIRD.substr($nom_classe, strlen($prefixe)+1)."/";
		}
		else if ($prefixe === "type") {
			$chemin = __DILECTIO_TYPES.substr($nom_classe, strlen($prefixe)+1)."/";
		}
		else if ($prefixe === "control") {
			$sous_prefixe = $tab_nom_classe[1];
			$chemin = __DILECTIO_PHP_CONTROL.$sous_prefixe."/";
		}
		else if ($prefixe === "view") {
			$sous_prefixe = $tab_nom_classe[1];
			$chemin = __DILECTIO_PHP_VIEW.$sous_prefixe."/";
		}
		else if ($prefixe === "theme") {
			$chemin = __DILECTIO_THEMES.substr($nom_classe, strlen($prefixe)+1)."/";
			/* Le PHP du thème s'appelle toujours theme.php */
			$nom_classe =  "theme";
		}
		else {$chemin .= $prefixe."/";}
	}
	if (is_dir($chemin)) {
		$script = $chemin.$nom_classe.".php";
		if (is_file($script)) {
			require_once($script);
		}
		else {
			die("ERREUR : Impossible de charger la classe ".$nom_classe." dans ".$chemin);
		}
	}
}

/* Activation de l'autoload */
spl_autoload_register("dilectio_autoload");