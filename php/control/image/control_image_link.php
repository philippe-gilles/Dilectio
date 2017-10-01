<?php

// Classe abstraite image link
abstract class control_image_link extends control_image {
	public function __construct($user_id, $image_id) {
		$this->root = "link";
		parent::__construct($user_id, $image_id);
	}

    public function configure() {
		db::open(__DILECTIO_PREFIXE_DB);

		/* Configuration pour le layout */
		$link = db_post_link::get($this->image_id);
		$this->extension = $link->extension;
		$this->width = $link->width;
		$this->height = $link->height;

		db::close();
	}
}