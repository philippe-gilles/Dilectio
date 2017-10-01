<?php

abstract class view_layout_session extends view_layout {
	protected $profil_id = null;
	protected $langue = null;
	
	/* Configuration du layout */
	private $home_active = true;
	public function set_home_active($home_active) {$this->home_active = $home_active;}
	private $new_conversation_active = true;
	public function set_new_conversation_active($new_conversation_active) {$this->new_conversation_active = $new_conversation_active;}
	private $alias = null;
	public function set_alias($alias) {$this->alias = $alias;}
	private $lateral = null;
	public function set_lateral($lateral) {$this->lateral = $lateral;}
	private $navigation = null;
	public function set_navigation($navigation) {$this->navigation = $navigation;}
	
	public function __construct() {
		/* Récupération des paramètres de session */
		$this->profil_id = tool_session::lire_param("profil_id");
		$this->langue = tool_session::lire_param("lang");
	}

	public function render() {
		/* Toaster pour messages de réussite */
		o_b::div(_id, "toaster-success", _class, "mdl-js-snackbar mdl-snackbar dilectio-toaster-success");
		o_b::div_div(null, _class, "mdl-snackbar__text");
		o_b::button_button(null, _class, "mdl-snackbar__action");
		o_b::_div(_n);

		/* Toaster pour messages d'échec */
		o_b::div(_id, "toaster-fail", _class, "mdl-js-snackbar mdl-snackbar dilectio-toaster-fail");
		o_b::div_div(null, _class, "mdl-snackbar__text");
		o_b::button_button(null, _class, "mdl-snackbar__action");
		o_b::_div(_n);

		/* Ouverture du layout */
		o_b::div(_class, "mdl-layout mdl-js-layout mdl-layout--fixed-drawer mdl-layout--fixed-header dilectio-layout", _n);

		/* Bandeau horizontal supérieur */
		$this->render_header_superieur();
		
		/* Panneau latéral gauche */
		o_b::div(_class, "mdl-layout__drawer dilectio-lateral", _n);
		$this->render_header_lateral();		
		$this->render_nav_lateral();
		o_b::w($this->lateral);
		o_b::_div(_n);
		
		/* Panneau latéral droit */
		$label_notifications = lang_i18n::trad($this->langue, "notification_some");
		$notifications = o::span_span($label_notifications, _id, "notification-title", _class, "mdl-layout-title");
		o_b::div_div($notifications, _id, "notification-panel", _class, "mdl-layout__drawer-right dilectio-lateral-droit", "data-time", "0", _n);
		
		/* Panneau principal */
		$this->render_principal();

		/* Fermeture du layout */
		o_b::_div(_n);
	}
	
	protected function render_header_superieur() {
		o_b::header(_class, "mdl-layout__header dilectio-superieur", _n);
		o_b::div(_class, "mdl-layout__header-row dilectio-barre-menu", _n);

		/* Vers l'accueil */
		$icone_home = o::mdlicon("home");
		if ($this->home_active) {
			$title_home = lang_i18n::trad($this->langue, "home_title");
			o_b::a_a($icone_home, _class, "mdl-button mdl-js-button mdl-button--fab mdl-button--mini-fab mdl-button--colored mdl-js-ripple-effect", _title, $title_home, _href, "home");
		}
		else {
			o_b::span_span($icone_home, _class, "mdl-button mdl-button--fab mdl-button--mini-fab", _disabled, "disabled");
		}
		
		o_b::span_span("&nbsp;", _class, "dilectio-bouton-separateur");

		/* Nouvelle conversation */
		$icone_add = o::mdlicon("add");
		if ($this->new_conversation_active) {
			$title_add = lang_i18n::trad($this->langue, "new_conversation");
			o_b::a_a($icone_add, _class, "mdl-button mdl-js-button mdl-button--fab mdl-button--mini-fab mdl-button--colored mdl-js-ripple-effect", _title, $title_add,_href, "thread-0", _n);
		}
		else {
			o_b::span_span($icone_add, _class, "mdl-button mdl-button--fab mdl-button--mini-fab", _disabled, "disabled");
		}

		/* Saut à droite */
		o_b::div_div("", _class, "mdl-layout-spacer", _n);

		/* Logo */
		o_b::span_span("Dilectio", _class, "mdl-layout-title mdl-color-text--primary-dark dilectio-logo dilectio-logo-superieur", _n);

		/* Menu */
		$id_menu = "dilectio-top-menu";
		$icone_menu = o::mdlicon("more_vert");
		o_b::button_button($icone_menu, _id, $id_menu, _class, "mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--icon dilectio-superieur-bouton-menu");
		o_b::ul(_class, "mdl-menu mdl-js-menu mdl-js-ripple-effect mdl-menu--bottom-right", _for, $id_menu);
		
		$icone_li = o::mdlicon("settings", array("class" => "mdl-color-text--primary dilectio-menu-item-icone"));
		$label_li = lang_i18n::trad($this->langue, "configuration");
		o_b::li_li($icone_li.$label_li, _class, "mdl-menu__item dilectio-mdl-menu-item", "data-href", "categories");

		$icone_li = o::mdlicon("info", array("class" => "mdl-color-text--primary dilectio-menu-item-icone"));
		$label_li = lang_i18n::trad($this->langue, "about");
		o_b::li_li($icone_li.$label_li, _class, "mdl-menu__item dilectio-mdl-menu-item", "data-href", "#");

		$icone_li = o::mdlicon("power_settings_new", array("class" => "dilectio-menu-item-icone-shutdown dilectio-menu-item-icone"));
		$label_li = lang_i18n::trad($this->langue, "logout");
		o_b::li_li($icone_li.$label_li, _class, "mdl-menu__item dilectio-mdl-menu-item separator", "data-href", "close");
		o_b::_ul();
		
		/* Notifications */
		$icone_notification = o::mdlicon("notifications");
		o_b::span_span($icone_notification, _id, "notification", _class, "mdl-badge mdl-badge--overlap dilectio-notification-button");
	
		o_b::_div(_n);
		o_b::_header(_n);
	}

	protected function render_header_lateral() {
		o_b::header(_class, "dilectio-lateral-profil", _n);
	
		/* Menu du profil */
		o_b::div(_class, "mdl-color-text--white dilectio-lateral-avatar-menu", _n);

		/* Image du profil */
		o_b::img(_class, "dilectio-lateral-avatar", _src, __DILECTIO_PROFILES."profile-".$this->profil_id."/avatar.png", _n);
		
		/* Alias du profil */
		o_b::span_span(htmlentities($this->alias, ENT_QUOTES, "UTF-8"), _style, "margin-left:16px;");

		/* Saut à droite */
		o_b::div_div(null, _class, "mdl-layout-spacer", _n);
		
		/* Liste du menu */
		$id_menu = "dilectio-side-menu";
		$icone_menu = o::mdlicon("arrow_drop_down");
		o_b::button_button($icone_menu, _id, $id_menu, _class, "mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--icon", _n);
		o_b::ul(_class, "mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect", _for, $id_menu, _n);

		$icone_li = o::mdlicon("account_circle", array("class" => "mdl-color-text--accent dilectio-menu-item-icone"));
		$label_li = lang_i18n::trad($this->langue, "my_profile");
		o_b::li_li($icone_li.$label_li, _class, "mdl-menu__item dilectio-mdl-menu-item", "data-href", "identity", _n);

		$icone_li = o::mdlicon("format_paint", array("class" => "mdl-color-text--accent dilectio-menu-item-icone"));
		$label_li = lang_i18n::trad($this->langue, "appearance");
		o_b::li_li($icone_li.$label_li, _class, "mdl-menu__item", _n);

		$icone_li = o::mdlicon("equalizer", array("class" => "mdl-color-text--accent dilectio-menu-item-icone"));
		$label_li = lang_i18n::trad($this->langue, "stats");
		o_b::li_li($icone_li.$label_li, _class, "mdl-menu__item", _n);

		o_b::_ul(_n);
		
		o_b::_div(_n);

		o_b::_header(_n);		
	}
	
	protected function render_nav_lateral() {
		o_b::nav(_class, "mdl-navigation dilectio-navigation", _n);
		o_b::w($this->navigation);
		o_b::_nav(_n);
	}
	
	abstract protected function render_principal();
}