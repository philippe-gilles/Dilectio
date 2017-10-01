<?php

class control_image_original extends control_image {
	public function __construct($user_id, $photo_id) {
		$this->prefixe = "original";
		parent::__construct($user_id, $photo_id);
	}
	
	public function render_error() {
		header("Content-Type: image/png");
		$image = imagecreatetruecolor($this->width, $this->height);
		$bg = imagecolorallocate($image, 255, 0, 0 );
		imagefilledrectangle($image, 0, 0, $this->width, $this->height, $bg);
		imagepng($image);
	}
}
