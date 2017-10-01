<?php

/*
 * Dilectio : Cadre de page sous session
 */

class view_frame_session extends view_frame {
	private $langue = null;
	private $theme = null;
	
	/* Configuration du cadre */
	private $nom_page = null;
	public function set_nom_page($nom_page) {$this->nom_page = $nom_page;}
	private $excerpt_only = false;
	public function set_excerpt_only($excerpt_only) {$this->excerpt_only = $excerpt_only;}
	private $extra_js = null;
	public function set_extra_js($extra_js) {$this->extra_js = $extra_js;}

	public function __construct() {
		/* Récupération des paramètres de session */
		$this->langue = tool_session::lire_param("lang");
		$this->theme = tool_session::lire_param("theme");
	}
	
	public function open() {
		/* Déclaration des tiers */
		o_html::declarer_theme($this->theme);
		foreach($this->tiers as $tiers) {o_html::declarer_tiers($tiers);}
		foreach($this->types as $type) {o_html::declarer_type($type);}

		/* Ouverture */
		o_b::doctype();
		o_b::html("lang", $this->langue, _n);

		/* HEAD ***************************************************************/
		o_b::head();

		/* Meta */
		o_html::meta();

		/* Favicon */
		o_html::favicon();

		/* Title */
		$title = lang_i18n::trad($this->langue, $this->nom_page."_title");
		o_html::title($title);

		/* Tiers */
		o_html::tiers_head($this->langue);

		/* Types */
		o_html::types_head($this->excerpt_only);

		/* Thème */
		o_html::theme_head($this->langue);

		/* CSS */
		o_html::css_page($this->nom_page);

		o_b::_head(_n);
		
		/* BODY ***************************************************************/
		o_b::body(_n);
	}

	public function close() {
		/* Tiers */
		o_html::tiers_body($this->langue);

		/* Types */
		o_html::types_body($this->excerpt_only);

		/* Thème */
		o_html::theme_body($this->langue);

		/* JS général */
		$js_general = __DILECTIO_JS."dilectio.js";
		o_b::comment("Javascript général");
		o_b::script_script(null, _type , "application/javascript", _src , $js_general, _n);
		$error_ajax_script = lang_i18n::trad($this->langue, "error_ajax_script");
		$var_error_ajax_script = "var error_ajax_script = \"".$error_ajax_script."\"";
		o_b::script_script($var_error_ajax_script, _type , "application/javascript", _n);
	
		/* JS propre à la page */
		o_html::js_page($this->nom_page);
		if (strlen($this->extra_js) > 0) {
			o_b::script_script($this->extra_js, _type , "application/javascript", _n);
		}

		/* Fermeture */
		o_b::_body(_n);
		o_b::_html(_n);
		
		/* Flush de la partie body */
		o_b::flush();
	}
}