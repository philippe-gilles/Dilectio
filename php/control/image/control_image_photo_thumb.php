<?php

class control_image_photo_thumb extends control_image_photo {
	public function __construct($user_id, $image_id) {
		$this->prefixe = "thumb";
		parent::__construct($user_id, $image_id);
	}

	public function render_error() {
		$url = __DILECTIO_IMAGES."thumb-red-error.png";
		if (@file_exists($url)) {
			header("Content-Type: image/png");
			readfile($url);
		}
	}
}
