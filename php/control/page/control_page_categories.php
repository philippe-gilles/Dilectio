<?php

class control_page_categories extends control_page {
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
		$this->frame->set_nom_page("categories");
		$this->frame->set_tiers("icomoon", "jquery", "jconfirm", "mdl", "mdlicons", "nestable");

		// Javascript pour nestable
		$extra_js = $this->extra_js();
		$this->frame->set_extra_js($extra_js);
		/* Fin configuration pour le cadre */

		/* Configuration pour le layout */

		/* Alias */
		$user_id = tool_session::lire_param("profil_id");
		$profil = db_profile::get($user_id);
		$this->layout->set_alias($profil->alias);

		/* Panneau latéral */
		$component_nav = new view_component_nav($this->langue);
		$tab_config = array();
		// Catégories
		$config_label = lang_i18n::trad($this->langue, "category_some");
		$config_icon = "tree";
		$tab_config[$config_label] = array("id" => "categories", "icon" => $config_icon);
		// Notifications
		$config_label = lang_i18n::trad($this->langue, "notification_some");
		$config_icon = "bell";
		$tab_config[$config_label] = array("id" => "notifications", "icon" => $config_icon);
		// Thèmes
		$config_label = lang_i18n::trad($this->langue, "themes");
		$config_icon = "paint-format";
		$tab_config[$config_label] = array("id" => "themes", "icon" => $config_icon);
		// Langues
		$config_label = lang_i18n::trad($this->langue, "languages");
		$config_icon = "books";
		$tab_config[$config_label] = array("id" => "languages", "icon" => $config_icon);

		$navigation = $component_nav->nav_config($tab_config, "categories");
		$this->layout->set_navigation($navigation);

		/* Panneau principal : body */
		$component_nestable = new view_component_nestable($this->langue);

		// Boutons de gestion de l'arborescence
		$html = $component_nestable->expand_collapse_buttons();
		
		// Arborescence
		$tab_nested_categories = array();
		db_category::tab_nested_categories($tab_nested_categories);
		$html .= $component_nestable->list_categories($tab_nested_categories);
		
		// Nouvelle catégorie
		$html .= $component_nestable->new_category();

		$this->layout->set_principal($html);

		/* Fin configuration pour le layout */
		db::close();
	}
	
	private function extra_js() {
		$icone_expand = "<i class=\"material-icons\">arrow_drop_down</i>"; // Forçage du double-quote
		$icone_bouton_expand = o::button_button($icone_expand, _class, "mdl-button mdl-js-button mdl-button--icon dilectio-nestable-expand", "data-action", "expand");
		$icone_collapse = "<i class=\"material-icons\">arrow_drop_up</i>"; // Forçage du double-quote
		$icone_bouton_collapse = o::button_button($icone_collapse, _class, "mdl-button mdl-js-button mdl-button--icon dilectio-nestable-collapse", "data-action", "collapse");

		$label_confirm_title = lang_i18n::trad($this->langue, "warning");
		$label_confirm_msg_delete = lang_i18n::trad($this->langue, "category_delete_warning");
		$extra_js = "var confirm_title = \"".$label_confirm_title."\";";
		$extra_js .= "var confirm_msg_delete = \"".$label_confirm_msg_delete."\";";
		$extra_js .= "$(document).ready(function() {";
		$extra_js .= "$(\"#nestable-categories\").nestable({";
		$extra_js .= "rootClass: 'dilectio-nestable', ";
		$extra_js .= "expandBtnHTML: '".$icone_bouton_expand."',";
		$extra_js .= "collapseBtnHTML: '".$icone_bouton_collapse."'});";
		$extra_js .= "});";
		return $extra_js;
	}
}