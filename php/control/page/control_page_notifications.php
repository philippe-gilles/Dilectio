<?php

class control_page_notifications extends control_page {
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
		$this->frame->set_nom_page("notifications");
		$this->frame->set_tiers("icomoon", "jquery", "jconfirm", "mdl", "mdlicons");
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
		$tab_profile[$profile_label] = array("id" => "security", "icon" => $profile_icon, "href" => "security");
		// Notifications
		$profile_label = lang_i18n::trad($this->langue, "notification_some");
		$profile_icon = "bell";
		$tab_profile[$profile_label] = array("id" => "notifications", "icon" => $profile_icon);

		$navigation = $component_nav->nav_config($tab_profile, "notifications");
		$this->layout->set_navigation($navigation);

		/* Panneau principal : body */
		$this->layout->set_principal(null);

		/* Fin configuration pour le layout */
		db::close();
	}
}