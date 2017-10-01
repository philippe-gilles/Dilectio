<?php

class control_image_link_original extends control_image_link {
	public function __construct($user_id, $image_id) {
		$this->prefixe = "original";
		parent::__construct($user_id, $image_id);
	}
	
	public function render_error() {
		header("Content-Type: image/png");
		$image = imagecreatetruecolor($this->width, $this->height);
		$bg = imagecolorallocate($image, 255, 0, 0 );
		imagefilledrectangle($image, 0, 0, $this->width, $this->height, $bg);
		imagepng($image);
	}
}
