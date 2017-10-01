<?php

class view_layout_session_2 extends view_layout_session {

	/* Configuration du layout */
	private $principal_header = null;
	public function set_principal_header($principal_header) {$this->principal_header = $principal_header;}
	private $principal_body = null;
	public function set_principal_body($principal_body) {$this->principal_body = $principal_body;}

	protected function render_principal() {
		o_b::main(_class, "mdl-layout__content dilectio-principal", _n);
		o_b::div(_class, "dilectio-principal-header", _n);
		o_b::w($this->principal_header);
		o_b::_div(_n);
		o_b::div(_class, "dilectio-principal-body", _n);
		o_b::w($this->principal_body);
		o_b::_div(_n);
		o_b::_main(_n);

		o_b::a_a(o::mdlicon("arrow_upward"),  _id, "goto-top", _href, "#", _class, "mdl-button mdl-js-button mdl-button--fab mdl-button--mini-fab dilectio-principal-goto-top");
		o_b::a_a(o::mdlicon("arrow_downward"),  _id, "goto-bottom", _href, "#", _class, "mdl-button mdl-js-button mdl-button--fab mdl-button--mini-fab dilectio-principal-goto-bottom");

		/* Obfuscator pour latéral droit */
		o_b::div_div(null, _class, "mdl-layout__obfuscator-right", _n);
	}
}