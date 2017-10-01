<?php

class view_component_input extends view_component {
	public function clear_filter() {
		$icone_clear_filter = o::mdlicon("highlight_off");
		$label = lang_i18n::trad($this->langue, "clearfilters");
		$o = o::button_button($icone_clear_filter, _id, "clear-filters", _class, "mdl-button mdl-js-button mdl-button--icon dilectio-lateral-position-absolue", _title, $label, _disabled, "disabled");
		return $o;
	}

	public function filter_category($tab_categories) {
		$label = lang_i18n::trad($this->langue, "category_one");
		$o = o::comment("Catégorie")
			.o::label_label($label, _class, "dilectio-navigation-label dilectio-navigation-label-espace", _for, "select-categorie", _n)
			.$this->select_category("filter-categorie", "category_all", $tab_categories, 0, "wide");
		return $o;
	}
	
	public function filter_type($tab_types) {
		$label = lang_i18n::trad($this->langue, "type_one");
		$o = o::comment("Type")
			.o::label_label($label, _class, "dilectio-navigation-label", _for, "select-type", _n)
			.$this->select_type("filter-type", $tab_types);
		return $o;
	}
	
	public function slider_date($max_date) {
		$max_date -= ($max_date % 86400);
		$year_today = (int) date("Y");
		$year_max = (int) date("Y", $max_date);
		$str_month = lang_i18n::trad($this->langue, "month_".date("n", $max_date));
		$format_date = date("j", $max_date)." ".$str_month;
		if ($year_today > $year_max) {
			$format_date .= " ".$year_max;
		}
		$label = lang_i18n::trad($this->langue, "date");
		$o = o::comment("Date")					
			.o::label_label($label, _class, "dilectio-navigation-label", _for, "slider-date", _n)
			.o::p(_class, "dilectio-slider")
			.o::input_range(_id, "slider-date", _class, "mdl-slider mdl-js-slider", "min", ($max_date - 86400), "max", $max_date, _value, $max_date, "step", "86400")
			.o::span_span($format_date, _id, "slider-caption", _class, "dilectio-slider-caption mdl-color-text--primary")
			.o::_p(_n);
		return $o;
	}
	
	public function checkbox_filters() {
		$o = o::comment("Cases à cocher")
			.o::div(_class, "dilectio-navigation-groupe-options", _n)
			.$this->checkbox_filter_unread()
			.$this->checkbox_filter_mine()
			.$this->checkbox_filter_favorite()
			.o::_div();
		return $o;
	}

	public function toggle_views() {
		$icone_view_list = o::mdlicon("view_list", array("class" => "dilectio-navigation-toggle-view-list"));
		$icone_view_gallery = o::mdlicon("panorama", array("class" => "dilectio-navigation-toggle-view-gallery"));
		$icone_view_calendar = o::mdlicon("date_range", array("class" => "dilectio-navigation-toggle-view-calendar"));
		$lien_view_list = o::a_a($icone_view_list, _id, "toggle-view-list", _class, "dilectio-navigation-toggle-view-link", _href, "#");
		$lien_view_gallery = o::a_a($icone_view_gallery, _id, "toggle-view-gallery", _class, "dilectio-navigation-toggle-view-link", _href, "#");
		$lien_view_calendar = o::a_a($icone_view_calendar, _id, "toggle-view-calendar", _class, "dilectio-navigation-toggle-view-link", _href, "#");
		$lien_clearfix = o::a_a(null, _style, "clear:both;");
		$o = o::p_p($lien_view_list.$lien_view_gallery.$lien_view_calendar.$lien_clearfix, _class, "dilectio-navigation-toggle-views");
		return $o;
	}
	
	public function generic_field($type, $id, $entry, $value = "", $required = false) {
		$label = lang_i18n::trad($this->langue, $entry);
		$o = o::div(_class, "mdl-textfield mdl-js-textfield mdl-textfield--floating-label dilectio-form-generic-field");
		if ($required) {
			$o .= o::input(_type, $type, _id, $id, _name, $id, _class, "mdl-textfield__input", _value, $value, _required, "required");
		}
		else {
			$o .= o::input(_type, $type, _id, $id, _name, $id, _class, "mdl-textfield__input", _value, $value);
		}
		$o .= o::label_label($label, _class, "mdl-textfield__label", _for, $id).o::_div(_n);
		return $o;
	}
	
	public function select_language() {
		$o = "";
		$list_lang = @glob(__DILECTIO_LANG."*", GLOB_ONLYDIR);
		foreach($list_lang as $dir_lang) {
			$lang = @basename($dir_lang);
			if (strlen($lang) > 0) {
				$id = "lang-".$lang;
				$o .= o::label(_class, "mdl-radio mdl-js-radio mdl-js-ripple-effect", _for, $id);
				if (strcmp($lang, $this->langue)) {
					$o .= o::input_radio(_id, $id, _name, "lang", _class, "mdl-radio__button", _value, $lang);
				}
				else {
					$o .= o::input_radio(_id, $id, _name, "lang", _class, "mdl-radio__button", _value, $lang, _checked, "checked");
				}
				$o .= o::span_span($lang, _class, "mdl-radio__label").o::_label();
			}
		}
		return $o;
	}

	public function avatar($user_id) {
		$label_avatar = lang_i18n::trad($this->langue, "identity_label_avatar");
		$label_gallery = lang_i18n::trad($this->langue, "identity_label_gallery");
		$label_url = lang_i18n::trad($this->langue, "identity_label_url");
		$label_upload = lang_i18n::trad($this->langue, "identity_label_upload");
		$path_avatar = __DILECTIO_PROFILES."profile-".$user_id."/avatar.png";
		$path_gallery = __DILECTIO_PROFILES."default/";
		$list_png = @glob($path_gallery."*.png");
		$list_gallery = "";
		foreach($list_png as $png) {
			$base_png = @basename($png);
			if (strlen($base_png) > 0) {
				if (strlen($list_gallery) > 0) {$list_gallery .= ",";}
				$list_gallery .= $base_png;
			}
		}
		$o = o::div(_class, "mdl-avatar mdl-js-avatar mdl-avatar--floating-label")
			.o::input_text(_id, "profile_image", _name, "profile_image", _class, "mdl-avatar__input", _value, $path_avatar, "data-i18n-gallery", $label_gallery, "data-i18n-url", $label_url, "data-i18n-upload", $label_upload, "data-gallery-src", $path_gallery, "data-gallery-list", $list_gallery)
			.o::label_label($label_avatar, _class, "mdl-avatar__label", _for, "profile_image")
			.o::_div();
		return $o;
	}

	public function form_thread($thread_id, $tab_categories, $title = "", $category_id = 0) {
		$o = o::comment("Formulaire fil de discussion");
		if ($thread_id > 0) {
			$select_categorie = $this->select_category("thread-categorie", "category_none", $tab_categories, $category_id);
			$o .= o::form(_class, "dilectio-thread-form dilectio-thread-form-hidden", _method, "post")
				.o::input_hidden(_name, "thread-id", _value, $thread_id)
				.$this->input_thread_title($title)
				.o::div_div($select_categorie, _class, "dilectio-principal-header-niceselect")
				.o::div(_style, "display:inline-block;")
				.$this->submit_button("thread_modify")
				.$this->cancel_button("thread-cancel", "cancel")
				.o::_div()
				.o::_form(_n);
		
			$o .= o::div(_class, "dilectio-thread-info dilectio-thread-form-visible");
			$select_categorie_disabled = $this->select_category_disabled("category_none", $tab_categories, $category_id);
			$o .= $this->input_thread_title($title, true);
			$o .= o::div_div($select_categorie_disabled, _class, "dilectio-principal-header-niceselect");
			
			$icone_edit = o::mdlicon("edit");
			$label_post_edit = lang_i18n::trad($this->langue, "edition");
			$actions = o::button_button($icone_edit, _id, "edit-thread-".$thread_id, _class, "mdl-button mdl-js-button mdl-button--icon dilectio-thread-edit", _title, $label_post_edit);

			$icone_trash = o::mdlicon("delete");
			$label_post_delete = lang_i18n::trad($this->langue, "delete");
			$actions .= o::button_button($icone_trash, _id, "delete-thread-".$thread_id, _class, "mdl-button mdl-js-button mdl-button--icon dilectio-thread-delete", _title, $label_post_delete);
			$o .= $actions;
			$o .= o::_div(_n);
		}
		else {
			$select_categorie = $this->select_category("thread-categorie", "category_none", $tab_categories, $category_id);
			$o .= o::form(_class, "dilectio-thread-form", _method, "post")
				.o::input_hidden(_name, "thread-id", _value, $thread_id)
				.$this->input_thread_title($title)
				.o::div_div($select_categorie, _class, "dilectio-principal-header-niceselect")
				.$this->submit_button("thread_create")
				.o::_form(_n);
		}
		return $o;
	}

	public function change_password() {
		$label_new_password = lang_i18n::trad($this->langue, "security_new_password");
		$label_repeat_password = lang_i18n::trad($this->langue, "security_repeat_password");
		$o = o::div(_class, "mdl-textfield mdl-js-textfield")
			.o::input_password(_id, "zxcvbn-password", _name, "password-1", _class, "mdl-textfield__input", "placeholder", $label_new_password, "data-score", 0)
			.o::_div()
			.o::br()
			.o::div(_class, "mdl-textfield mdl-js-textfield")
			.o::input_password(_id, "repeat-password", _name, "password-2", _class, "mdl-textfield__input", "placeholder", $label_repeat_password)
			.o::_div()
			.o::div(_class, "dilectio-security-gradient-container")
			.o::div_div(null, _id, "password-score", _class, "dilectio-security-gradient-obfuscator")
			.o::_div()
			.o::div(_class, "dilectio-security-suggestion-container")
			.o::p_p(null, _id, "password-suggestions")
			.o::_div();
		return $o;
	}

	public function comment_password_submit() {
		$label_password_stronger = lang_i18n::trad($this->langue, "security_password_stronger");
		$label_password_repeated = lang_i18n::trad($this->langue, "security_password_repeated");
		$o = o::p_p($label_password_stronger, _id, "submit-stronger", _class, "dilectio-security-submit-nocomment")
			.o::p_p($label_password_repeated, _id, "submit-repeated", _class, "dilectio-security-submit-nocomment")
			.o::p_p(null, _id, "submit-ready", _class, "dilectio-security-submit-comment");
		return $o;
	}

	public function submit_button($entry, $disabled = false) {
		$label_submit = lang_i18n::trad($this->langue, $entry);
		if ($disabled) {
			$o = o::button_button($label_submit, "type", "submit", _class, "mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent dilectio-thread-button", _disabled, "disabled");
		}
		else {
			$o = o::button_button($label_submit, "type", "submit", _class, "mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent dilectio-thread-button");
		}
		return $o;
	}

	public function cancel_button($id, $entry) {
		$label_cancel = lang_i18n::trad($this->langue, $entry);
		$o = o::button_button($label_cancel, _id, $id, "type", "button", _class, "mdl-button mdl-js-button mdl-button--raised dilectio-thread-button");
		return $o;
	}

	/* Privé ************************************************************/
	private function select_category($id, $entry_null_val, $tab_categories, $selected_option = 0, $class = "") {
		$label_null_val = lang_i18n::trad($this->langue, $entry_null_val);
		$o = o::select(_id, $id, _name, $id, _class, $class, _n)
			.o::option_option($label_null_val, _value, 0, "data-level", 0, _n);
		foreach($tab_categories as $id => $info_category) {
			if ($id == $selected_option) {
				$o .= o::option_option($info_category["label"], _value, $id, "data-level", $info_category["level"], _selected, "selected", _n);
			}
			else {
				$o .= o::option_option($info_category["label"], _value, $id, "data-level", $info_category["level"], _n);
			}
		}
		$o .= o::_select(_n);
		return $o;
	}
	
	private function select_category_disabled($entry_null_val, $tab_categories, $selected_option = 0) {
		$label_null_val = lang_i18n::trad($this->langue, $entry_null_val);
		$info_category = $tab_categories[$selected_option];
		$o = o::div(_class, "dilectio-nice-select-disabled dilectio-nice-select-not-null-val", _n)
			.o::span_span($info_category["label"])
			.o::_div();
		return $o;
	}
	
	private function select_type($id, $tab_types) {
		$option_all = lang_i18n::trad($this->langue, "type_all");
		$o = o::select(_id, $id,  _class, "wide", _n)
			.o::option_option($option_all, _value, 0, _n);
			
		foreach($tab_types as $label => $info_type) {
			$o .= o::option_option($label, _value, $info_type["id"], "data-icomoon", $info_type["icon"], _n);
		}
		$o .= o::_select(_n);
		return $o;
	}

	private function checkbox_filter_unread() {
		$label_unread = lang_i18n::trad($this->langue, "unreadposts");
		$o = o::label(_class, "mdl-switch mdl-js-switch mdl-js-ripple-effect mdl-color-text--white", _for, "filter-read", _n)
			.o::input_checkbox(_id, "filter-read", _class, "mdl-switch__input", _n)
			.o::span_span($label_unread, _class, "mdl-switch__label dilectio-navigation-label", _n)
			.o::_label();
		return $o;
	}
	
	private function checkbox_filter_mine() {
		$label_mine = lang_i18n::trad($this->langue, "myposts");
		$o = o::label(_class, "mdl-switch mdl-js-switch mdl-js-ripple-effect mdl-color-text--white", _for, "filter-mine", _n)
			.o::input_checkbox(_id, "filter-mine", _class, "mdl-switch__input", _n)
			.o::span_span($label_mine, _class, "mdl-switch__label dilectio-navigation-label", _n)
			.o::_label();
		return $o;
	}

	private function checkbox_filter_favorite() {
		$label_favorite = lang_i18n::trad($this->langue, "favoriteposts");
		$o = o::label(_class, "mdl-switch mdl-js-switch mdl-js-ripple-effect mdl-color-text--white", _for, "filter-favorite", _n)
			.o::input_checkbox(_id, "filter-favorite", _class, "mdl-switch__input", _n)
			.o::span_span($label_favorite, _class, "mdl-switch__label dilectio-navigation-label", _n)
			.o::_label();
		return $o;
	}

	private function input_thread_title($title = "", $disabled = false) {
		$o = o::div(_class, "mdl-textfield mdl-js-textfield dilectio-principal-header-title");
		if ($disabled) {
			$o .= o::input_text(_id, "thread-title-disabled", _class, "mdl-textfield__input dilectio-thread-title", _value, $title, _disabled, "disabled");
		}
		else {
			$o .= o::input_text(_id, "thread-title", _name, "thread-title", _class, "mdl-textfield__input dilectio-thread-title", _value, $title);
			$o .= o::label_label("Title", _class, "mdl-textfield__label", _for, "thread-title");
		}
		$o .= o::_div(_n);
		return $o;
	}
}