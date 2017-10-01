<?php

// Classe abstraite image photo
abstract class control_image_photo extends control_image {
	public function __construct($user_id, $image_id) {
		$this->root = "photo";
		parent::__construct($user_id, $image_id);
	}

    public function configure() {
		db::open(__DILECTIO_PREFIXE_DB);

		/* Configuration pour le layout */
		$photo = db_post_photo::get($this->image_id);
		$this->extension = $photo->extension;
		$this->width = $photo->width;
		$this->height = $photo->height;

		db::close();
	}
}