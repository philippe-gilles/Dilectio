<?php

/**
 * Dilectio : Classe de gestion pour l'intégration HTML
 */

class o_html {
	protected static $theme = null;
	protected static $tab_tiers = array();
	protected static $tab_types = array();

	public static function declarer_theme($arg) {
		self::$theme = $arg;
	}

	public static function declarer_tiers() {
		foreach (func_get_args() as $arg) {
			self::$tab_tiers[] = $arg;
		}
	}

	public static function declarer_type() {
		foreach (func_get_args() as $arg) {
			self::$tab_types[] = $arg;
		}
	}

	public static function meta() {
		o_b::comment("Initialisations");
		o_b::meta("charset", "utf-8", _n);
		o_b::meta("http-equiv", "x-ua-compatible", _content , "ie=edge", _n);
		o_b::meta(_name, "viewport", _content , "width=device-width, initial-scale=1.0, shrink-to-fit=no", _n);
		o_b::meta(_name, "robots", _content, "noindex, nofollow", _n);
	}

	public static function favicon() {	
		o_b::comment("Favicon");
		o_b::link(_rel , "apple-touch-icon", _sizes, "180x180", _href , "apple-touch-icon.png", _n);
		o_b::link(_rel , "icon", _type, "image/png", _sizes, "32x32", _href , "favicon-32x32.png", _n);
		o_b::link(_rel , "icon", _type, "image/png", _sizes, "16x16", _href , "favicon-16x16.png", _n);
		o_b::link(_rel , "manifest", _href , "manifest.json", _n);
		o_b::link(_rel , "mask-icon", _href , "safari-pinned-tab.svg", "color", "#aa0000", _n);	
		o_b::meta(_name, "theme-color", _content , "#ffffff", _n);
	}
	
	public static function title($arg = "") {
		o_b::comment("Titre");
		if (strlen($arg) > 0) {$arg = " | ".$arg;}
		$title = sprintf("DILECTIO%s", $arg);
		o_b::title_title($title, _n);
	}
	
	public static function css_page($page) {
		o_b::comment("Styles de base page ".$page);
    	o_b::link("rel" , "stylesheet", _href, __DILECTIO_CSS."dilectio.css", _n);
		$css_page = __DILECTIO_CSS.$page.".css";
		if (file_exists($css_page)) {
			o_b::link("rel" , "stylesheet", "href" , $css_page, _n);
		}
		self::css_google_font(__DILECTIO_FONT_LOGO);
	}
	
	public static function js_page($page) {
		$js_page = __DILECTIO_JS.$page.".js";
		if (file_exists($js_page)) {
			o_b::comment("Javascript attaché à la page");
			o_b::script_script("", _type , "application/javascript", _src , $js_page, _n);
		}
	}
	
	public static function css_google_font($arg) {
		$arg_url = preg_replace('/\s+/', '+',$arg);
		$font_url = "https://fonts.googleapis.com/css?family=".$arg_url;
		o_b::comment("Style externe : Google Font ".$arg);
    	o_b::link("rel" , "stylesheet", "href" , $font_url, _n);
	}
	
	public static function tiers_head($langue) {
		$ret = "";
		$nb_tiers = count(self::$tab_tiers);
		if ($nb_tiers > 0) {
			o_b::comment("Head tiers");
			foreach(self::$tab_tiers as $tiers) {
				$classe_tiers = "third_".$tiers;
				if (class_exists($classe_tiers)) {
					$classe_tiers::head($langue);
				}
				else {
					die("ERREUR : Le tiers ".$tiers." est introuvable");
				}
			}
		}
	}
	
	public static function tiers_body($langue) {
		$nb_tiers = count(self::$tab_tiers);
		if ($nb_tiers > 0) {
			o_b::comment("Body tiers");
			foreach(self::$tab_tiers as $tiers) {
				$classe_tiers = "third_".$tiers;
				if (class_exists($classe_tiers)) {
					$classe_tiers::body($langue);
				}
				else {
					die("ERREUR : Le tiers ".$tiers." est introuvable");
				}
			}
		}
	}
	
	public static function theme_head($langue) {
		$ret = "";
		$theme = self::$theme;
		o_b::comment("Head thème ".$theme);
		$classe_theme = "theme_".$theme;
		if (class_exists($classe_theme)) {
			$classe_theme::head($langue);
		}
		else {
			die("ERREUR : Le thème ".$theme." est introuvable");
		}
	}
	
	public static function theme_body($langue) {
		$theme = self::$theme;
		o_b::comment("Body thème ".$theme);
		$classe_theme = "theme_".$theme;
		if (class_exists($classe_theme)) {
			$classe_theme::body($langue);
		}
		else {
			die("ERREUR : Le thème ".$theme." est introuvable");
		}

	}
	
	public static function types_head($excerpt_only = false) {
		$css_type = __DILECTIO_CSS."types.css";
		o_b::comment("Head types");
		o_b::link("rel" , "stylesheet", "href" , $css_type, _n);
		$nb_types = count(self::$tab_types);
		if ($nb_types > 0) {
			$html = "";
			foreach(self::$tab_types as $type) {
				$classe_type = "type_".$type;
				if (class_exists($classe_type)) {
					$html = $classe_type::head($excerpt_only);
				}
				else {
					die("ERREUR : Le type ".$type." est introuvable");
				}
				o_b::w($html);
			}
		}
	}
	
	public static function types_body($excerpt_only = false) {
		o_b::comment("Body types");
		$js_type = __DILECTIO_JS."types.js";
		o_b::script_script("", _type , "application/javascript", _src , $js_type, _n);
		$nb_types = count(self::$tab_types);
		if ($nb_types > 0) {
			$html = "";
			foreach(self::$tab_types as $type) {
				$classe_type = "type_".$type;
				if (class_exists($classe_type)) {
					$html = $classe_type::body($excerpt_only);
				}
				else {
					die("ERREUR : Le type ".$type." est introuvable");
				}
				o_b::w($html);
			}
		}
	}
}