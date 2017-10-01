<?php

class view_layout_session_1 extends view_layout_session {

	private $principal_hidden = null;
	public function set_principal_hidden($principal_hidden) {$this->principal_hidden = $principal_hidden;}

	protected function render_principal() {
		o_b::main(_class, "mdl-layout__content dilectio-principal", _n);

		/* Première période : aujourd'hui */
		o_b::div(_id, "periode-0", _class, "dilectio-periode-grille", _n);
		o_b::div_div(null, _id, "titre-periode-0", _class, "dilectio-titre-extraits", _n);
		o_b::div_div(null, _id, "grille-periode-0", _class, "mdl-grid dilectio-grille-extraits", _n);
		o_b::_div(_n);

		/* Deuxième période : récents */
		o_b::div(_id, "periode-1", _class, "dilectio-periode-grille", _n);
		o_b::div_div(null, _id, "titre-periode-1", _class, "dilectio-titre-extraits", _n);
		o_b::div_div(null, _id, "grille-periode-1", _class, "mdl-grid dilectio-grille-extraits", _n);
		o_b::_div(_n);
		
		/* Période suivante */
		o_b::div(_id, "periode-2", _class, "dilectio-periode-grille", _n);
		o_b::div_div(null, _id, "titre-periode-2", _class, "dilectio-titre-extraits", _n);
		o_b::div_div(null, _id, "grille-periode-2", _class, "mdl-grid dilectio-grille-extraits", _n);
		o_b::_div(_n);

		/* Autres périodes : bouton abandonné au bénéfice de l'infinite scroll
		$label_more = lang_i18n::trad($this->langue, "home_more");
		o_b::div(_id, "periode-more", _class, "dilectio-periode-more", _n);
		o_b::button_button($label_more, _id, "button-more", _class, "mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-color--primary-dark dilectio-periode-more-button");
		o_b::_div(_n);
		*/
		
		/* Marque de fin */
		o_b::hr(_id, "periode-end", _class, "dilectio-periode-end");

		o_b::_main(_n);

		/* Obfuscator pour latéral droit */
		o_b::div_div(null, _class, "mdl-layout__obfuscator-right", _n);
		
		/* Caché */
		o_b::div(_class, "dilectio-hidden", _n);
		o_b::w($this->principal_hidden);
		o_b::_div(_n);
	}
}