<?php
/*
 * Dilectio : Classe d'output HTML
 */

define("_id", "id");
define("_checked", "checked");
define("_class", "class");
define("_content", "content");
define("_disabled", "disabled");
define("_enctype", "enctype");
define("_for", "for");
define("_height", "height");
define("_href", "href");
define("_method", "method");
define("_name", "name");
define("_rel", "rel");
define("_readonly", "readonly");
define("_required", "required");
define("_selected", "selected");
define("_sizes", "sizes");
define("_src", "src");
define("_style", "style");
define("_target", "target");
define("_title", "title");
define("_type", "type");
define("_value", "value");
define("_width", "width");
define("_n", "\n");

class o {
	public static function doctype() {
		$o = "<!doctype html>\n";
		return $o;
	}

	public static function comment($str) {
		$o = sprintf("\n<!-- %s -->\n", $str);
		return $o;
	}

	public static function ouvrir($tag, $args) {
		$final = "";
		$nb_args = count($args);
		/* Si le nombre d'arguments est impair le dernier correspond au final */
		if ($nb_args % 2 == 1) {
			$final = $args[$nb_args - 1];
			$nb_args -= 1;
		}

		$liste_args = "";
		for ($i = 0;$i < $nb_args;$i += 2) {
			$liste_args .= sprintf(" %s=\"%s\"", $args[$i], $args[$i+1]);
		}
		$o = sprintf("<%s%s>%s", $tag, $liste_args, $final);
		return $o;
	}

	public static function fermer($tag, $final = "") {
		$o = sprintf("</%s>%s", $tag, $final);
		return $o;
	}
	
	public static function ouvrir_fermer($tag, $str, $args) {
		$final = "";
		$nb_args = count($args);
		/* Si le nombre d'arguments est impair le dernier correspond au final */
		if ($nb_args % 2 == 1) {
			$final = array_pop($args);
		}
		$o = self::ouvrir($tag, $args);
		$o .= $str;
		$o .= self::fermer($tag, $final);
		return $o;
	}

	public static function icomoon($arg, $tab_args = array()) {
		$class = "icon-".$arg;
		$args = "";
		foreach($tab_args as $nom_arg => $val_arg) {
			if (strcmp($nom_arg, "class")) {
				$args .= sprintf(" %s='%s'", $nom_arg, $val_arg);
			}
			else {
				$class .= " ".$val_arg;
			}
		}
		$ret = sprintf("<span class='%s'%s></span>", $class, $args);
		return $ret;
	}

	public static function mdlicon($arg, $tab_args = array()) {
		$class = "material-icons";
		$args = "";
		foreach($tab_args as $nom_arg => $val_arg) {
			if (strcmp($nom_arg, "class")) {
				$args .= sprintf(" %s='%s'", $nom_arg, $val_arg);
			}
			else {
				$class .= " ".$val_arg;
			}
		}
		$ret = sprintf("<i class='%s'%s>%s</i>", $class, $args, $arg);
		return $ret;
	}
	
	public static function __callStatic($methode, $args) {
		$tab_methode = explode("_", $methode);
		/* Premier fragment nul : fermeture */
		if (strlen($tab_methode[0]) == 0) {
			$tag = $tab_methode[1];
			if (count($args) > 0) {
				$ret = self::fermer($tag, $args[0]);
			}
			else {
				$ret = self::fermer($tag);
			}
			return $ret;
		}
		/* Premier fragment non nul : ouverture */
		else {
			$tag = $tab_methode[0];
			if (isset($tab_methode[1])) {
				if ($tab_methode[0] === "input") {
					$args = array_merge(array(_type, $tab_methode[1]), $args);
					$ret = self::ouvrir($tag, $args);
				}
				else if ($tab_methode[0] === $tab_methode[1]) {
					$str = array_shift($args);
					$ret = self::ouvrir_fermer($tag, $str, $args);
				}
				else {
					die("ERREUR : Appel d'une méthode o:: inconnue");
				}
			}
			else {
				$ret = self::ouvrir($tag, $args);
			}
			return $ret;
		}
	}
}