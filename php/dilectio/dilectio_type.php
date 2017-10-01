<?php

/**
 * Dilectio : Classe de gestion du HTML des types
 */

abstract class dilectio_type {
	public static function path() {
		$classe = get_called_class();
		$type = str_replace("type_", "", $classe);
		$ret = __DILECTIO_TYPES.$type."/";
		return $ret;
	}
	
	public static function form_buttons($label_gift, $label_submit, $label_cancel) {
		$o = o::p(_class, "dilectio-new-post-form-field", _style, "text-align:center;")
			.o::label(_class, "mdl-icon-toggle mdl-js-icon-toggle mdl-js-ripple-effect dilectio-new-post-form-gift-label", _for, "new-post-gift", _title, $label_gift)
			.o::input_checkbox(_id, "new-post-gift", _name, "gift", _class, "mdl-icon-toggle__input")
			.o::icomoon("gift", array("class" => "dilectio-new-post-form-gift"))
			.o::_label()
			.o::button_button($label_submit, "type", "submit", _class, "mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent")
			.o::button_button($label_cancel, _id, "new-post-cancel", "type", "button", _class, "mdl-button mdl-js-button mdl-button--raised")
			.o::_p();
		return $o;
	}
}