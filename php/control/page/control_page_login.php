<?php

class control_page_login extends control_page {

	public function __construct() {
		$this->frame = new view_frame_login();
		$this->layout = new view_layout_login();
	}

	protected function configure() {
		/* Configuration pour le cadre */
		$this->frame->set_tiers("jquery", "jconfirm", "mdl", "mdlicons");

		db::open(__DILECTIO_PREFIXE_DB);

		/* Configuration pour le layout */
		$profils = db_profile::all();
		$this->layout->set_profils($profils);

		db::close();
		
		$langues = array();
		foreach($profils as $profil) {$langues[] = $profil->language;}
		tool_file::merge_types($langues);
	}
}