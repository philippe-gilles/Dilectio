<?php

class control_page_security extends control_page {
	private $langue = null;

	public function __construct() {
		/* Configuration de la langue */
		$this->langue = tool_session::lire_param("lang");
		lang_i18n::init($this->langue);

		/* Déclaration du cadre et du layout */
		$this->frame = new view_frame_session();
		$this->layout = new view_layout_session_3();
	}

	protected function configure() {
		db::open(__DILECTIO_PREFIXE_DB);

		/* Configuration pour le cadre */
		$this->frame->set_nom_page("security");
		$this->frame->set_tiers("icomoon", "jquery", "jconfirm", "mdl", "mdlicons", "zxcvbn");
		// Javascript pour internationalisation zxcvbn
		$extra_js = $this->extra_js();
		$this->frame->set_extra_js($extra_js);
		/* Fin configuration pour le cadre */

		/* Configuration pour le layout */

		/* Alias */
		$user_id = tool_session::lire_param("profil_id");
		$profile = db_profile::get($user_id);
		$this->layout->set_alias($profile->alias);

		/* Panneau latéral */
		$component_nav = new view_component_nav($this->langue);
		$tab_profile = array();
		// Identity
		$profile_label = lang_i18n::trad($this->langue, "identity_title");
		$profile_icon = "user";
		$tab_profile[$profile_label] = array("id" => "identity", "icon" => $profile_icon, "href" => "identity");
		// Security
		$profile_label = lang_i18n::trad($this->langue, "security_title");
		$profile_icon = "key";
		$tab_profile[$profile_label] = array("id" => "security", "icon" => $profile_icon);
		// Notifications
		$profile_label = lang_i18n::trad($this->langue, "notification_some");
		$profile_icon = "bell";
		$tab_profile[$profile_label] = array("id" => "notifications", "icon" => $profile_icon, "href" => "notifications");

		$navigation = $component_nav->nav_config($tab_profile, "security");
		$this->layout->set_navigation($navigation);

		/* Panneau principal : body */
		$component = new view_component_input($this->langue);
		$label_change_password = lang_i18n::trad($this->langue, "security_change_password");
		$html = o::form(_id, "dilectio-security-form-".$user_id, _class, "dilectio-security-form", _method, "post");
		$html .= o::h2_h2($label_change_password, _class, "");
		$html_password = $component->change_password();
		$html .= o::div_div($html_password, _class, "dilectio-security-form-container");
		$html_submit = $component->submit_button("save", true);
		$html .= o::div_div($html_submit, _class, "dilectio-security-form-container");
		$html .= $component->comment_password_submit();
		$html .= o::_form();
		$this->layout->set_principal($html);

		/* Fin configuration pour le layout */
		db::close();
	}
	
	private function extra_js() {
		$extra_js = "$(document).ready(function() {";
		$extra_js .= "DILECTIO.security.init(\"".__DILECTIO_THIRD."\");";
		$extra_js .= "});\n";

		return $extra_js;
	}
}