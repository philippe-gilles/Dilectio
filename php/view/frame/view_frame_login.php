<?php

/*
 * Dilectio : Cadre de page avant connexion
 */

class view_frame_login extends view_frame {
	public function open() {
		/* Déclaration des tiers */
		o_html::declarer_theme(__DILECTIO_THEME_DEFAUT);
		foreach($this->tiers as $tiers) {o_html::declarer_tiers($tiers);}

		/* Ouverture */
		o_b::doctype();
		o_b::html("lang", __DILECTIO_LANGUE_DEFAUT, _n);

		/* HEAD ***************************************************************/
		o_b::head();

		/* Meta */
		o_html::meta();

		/* Favicon */
		o_html::favicon();

		/* Title */
		o_html::title();

		/* Tiers */
		o_html::tiers_head(__DILECTIO_LANGUE_DEFAUT);

		/* Thème */
		o_html::theme_head(__DILECTIO_LANGUE_DEFAUT);

		/* CSS */
		o_html::css_page("login");

		o_b::_head(_n);
		
		/* BODY ***************************************************************/
		o_b::body(_class, "dilectio_fond_page_login", _n);

	}

	public function close() {
		/* Tiers */
		o_html::tiers_body(__DILECTIO_LANGUE_DEFAUT);

		/* Thème */
		o_html::theme_body(__DILECTIO_LANGUE_DEFAUT);

		/* JS propre à la page */
		o_html::js_page("login");

		/* Fermeture */
		o_b::_body(_n);
		o_b::_html(_n);
		
		/* Flush de la partie body */
		o_b::flush();
	}
}