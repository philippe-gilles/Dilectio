<?php

class control_page_thread extends control_page {
	private $id = -1;
	private $fragment = -1;
	private $langue = null;

	public function __construct($id = 0, $fragment = 0) {
		/* Récupération de l'id */
		$this->id = $id;
		$this->fragment = $fragment;

		/* Configuration de la langue */
		$this->langue = tool_session::lire_param("lang");
		lang_i18n::init($this->langue);

		/* Déclaration du cadre et du layout */
		$this->frame = new view_frame_session();
		$this->layout = new view_layout_session_2();
	}

	protected function configure() {
		db::open(__DILECTIO_PREFIXE_DB);

		/* Configuration pour le cadre */
		$this->frame->set_nom_page("thread");
		$this->frame->set_tiers("icomoon", "jquery", "jconfirm", "mdl", "mdlext", "mdlicons", "niceselect", "flatpickr");
		$types = db_type::active();
		foreach($types as $type) {
			$this->frame->set_type($type->name);
		}
		$extra_js = $this->extra_js();
		$this->frame->set_extra_js($extra_js);
		/* Fin configuration pour le cadre */

		/* Configuration pour le layout */
		
		/* Bouton "nouvelle conversation" inactif si déjà ouvert */
		if ($this->id == 0) {
			$this->layout->set_new_conversation_active(false);
		}

		/* Alias */
		$user_id = tool_session::lire_param("profil_id");
		$profil = db_profile::get($user_id);
		$this->layout->set_alias($profil->alias);

		/* Panneau latéral */
		$types = db_type::active();
		$component_nav = new view_component_nav($this->langue);
		$tab_types = array();
		foreach($types as $type) {
			$entry_name = __DILECTIO_LANG_PREFIXE_TYPE.$type->name."_name";
			$label_lang = lang_i18n::trad($this->langue, $entry_name);
			$tab_types[$label_lang] = array("id" => $type->id, "icon" => $type->icon);
		}
		tool_string::ksort_utf8($this->langue, $tab_types);
		$navigation = $component_nav->nav_types($tab_types, ($this->id > 0));
		$this->layout->set_navigation($navigation);
		
		/* Panneau principal : header */
		$thread = db_thread::get($this->id);
		$component_input = new view_component_input($this->langue);
		$tab_categories = array();
		db_category::tab_categories($tab_categories);
		$principal_header = $component_input->form_thread($this->id, $tab_categories, $thread->label, $thread->category_id);
		$this->layout->set_principal_header($principal_header);
		
		/* Tous les posts vont être réputés lus */
		if ($this->id > 0) {
			$unread = db_post::unread_posts_in_thread_by($this->id, $user_id);
			foreach($unread as $post) {
				$read = db_read::instance();
				$read->post_id = $post->id;
				$read->profile_id = $user_id;
				db::store($read);
			}
			$unread = db_post::unread_posts_in_thread_by_modifier($this->id, $user_id);
			foreach($unread as $post) {
				$read = db_read::instance();
				$read->post_id = $post->id;
				$read->profile_id = $user_id;
				db::store($read);
			}
		}

		/* Panneau principal : body */
		$container = o::div_div(null, _id, "dilectio-thread-".$this->id, _class, "dilectio-thread-container", "data-fragment", $this->fragment);
		$this->layout->set_principal_body($container);

		/* Fin configuration pour le layout */
		db::close();
	}
	
	private function extra_js() {
		$label_confirm_title = lang_i18n::trad($this->langue, "warning");
		$label_confirm_msg_delete = lang_i18n::trad($this->langue, "post_delete_warning");
		$extra_js = "var confirm_title = \"".$label_confirm_title."\";";
		$extra_js .= "var confirm_msg_delete = \"".$label_confirm_msg_delete."\";";
		return $extra_js;
	}
}