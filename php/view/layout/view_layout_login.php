<?php

class view_layout_login extends view_layout {
	/* Configuration du layout */
	private $profils = null;
	public function set_profils($profils) {$this->profils = $profils;}

	/* Rendering */
	public function render() {
		/* Ouverture du layout */
		o_b::div(_class, "dilectio-login-layout", _n);

		/* Titre */
		o_b::h1_h1("Dilectio", _class, "dilectio-logo dilectio_bandeau_titre", _n);

		/* Images */
		o_b::div(_id, "login", _class, "dilectio_panneau_login", _n);
		foreach($this->profils as $profil) {
			$this->render_image_profil($profil);
		}

		/* Formulaires */
		o_b::div(_class, "dilectio_panneau_login_mdp", _n);
		foreach($this->profils as $profil) {
			lang_i18n::init($profil->language);
			if (strlen($profil->last_login) == 0) {
				$profil->last_login = md5(time() - (100 * $profil->id));
				db::store($profil);
			}
			$placeholder = lang_i18n::trad($profil->language, "login_password");
			$this->render_form_profil($profil, $placeholder);
		}
		o_b::_div(_n);
		o_b::_div(_n);

		/* Fermeture du layout */
		o_b::_div(_n);
	}

	private function render_image_profil(&$profil) {
		o_b::div(_id, "profil-".$profil->id, _class, "dilectio_panneau_login_image");
		o_b::a(_id, "image-".$profil->id, _class, "dilectio_panneau_lien_image", _href, "#");
		o_b::img(_src, __DILECTIO_PROFILES."profile-".$profil->id."/avatar.png");
		o_b::_a();
		o_b::_div(_n);
	}
	
	private function render_form_profil(&$profil, $placeholder) {
		o_b::form(_id, "login-form-".$profil->id, _class, "dilectio_form_login", _method, "post");
		o_b::input_text(_name, "dilectio");
		o_b::input_text(_name, "alias", _value, $profil->alias);
		$this->render_form_profil_password($profil, $placeholder);
		o_b::input_hidden(_name, "lang", _value, $profil->language);
		o_b::input_hidden(_name, "profil-id", _value, $profil->last_login);
		o_b::br();
		$this->render_form_profil_submit();
		o_b::_form(_n);
	}
	
	private function render_form_profil_password(&$profil, $placeholder) {
		o_b::div(_class, "mdl-textfield mdl-js-textfield");
		$id = "mot-de-passe-".$profil->id;
		o_b::input_password(_id, $id, _name, "mot-de-passe", _class, "mdl-textfield__input");
		o_b::label_label($placeholder, _for, $id, _class, "mdl-textfield__label");
		o_b::_div(_n);
	}
	
	private function render_form_profil_submit() {
		$icone_done = o::mdlicon("done", array("style" => "color:#060;font-weight:bold;"));
		o_b::button_button($icone_done, _type, "submit", _class, "mdl-button mdl-js-button mdl-button--fab mdl-button--mini-fab mdl-js-ripple-effect", _style, "background:#888!important;");
	}
}