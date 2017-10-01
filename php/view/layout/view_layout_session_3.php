<?php

class view_layout_session_3 extends view_layout_session {

	/* Configuration du layout */
	private $principal = null;
	public function set_principal($principal) {$this->principal = $principal;}

	protected function render_principal() {
		o_b::main(_class, "mdl-layout__content dilectio-principal", _n);
		o_b::w($this->principal);
		o_b::_main(_n);
		
		/* Obfuscator pour latéral droit */
		o_b::div_div(null, _class, "mdl-layout__obfuscator-right", _n);
	}
}