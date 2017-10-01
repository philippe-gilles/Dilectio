<?php

class control_image_thumb extends control_image {
	public function __construct($user_id, $photo_id) {
		$this->prefixe = "thumb";
		parent::__construct($user_id, $photo_id);
	}

	public function render_error() {
		$url = __DILECTIO_IMAGES."thumb-red-error.png";
		if (@file_exists($url)) {
			header("Content-Type: image/png");
			readfile($url);
		}
	}
}
