<?php

// Classe abstraite image
abstract class control_image {
	protected $root = null;
	protected $prefixe = null;
	protected $user_id = 0;
	protected $image_id = 0;
	protected $extension = null;
	protected $width = 0;
	protected $height = 0;

	public function __construct($user_id, $image_id) {
		$this->user_id = $user_id;
		$this->image_id = $image_id;
	}

    abstract public function configure();

	public function render() {
		$this->configure();
		if (in_array($this->extension, array("jpg", "png", "gif"))) {
			$url = __DILECTIO_PROFILES."profile-".$this->user_id."/".$this->root."/".$this->prefixe."-".$this->image_id.".".$this->extension;
			if (@file_exists($url)) {
				$mime_type = (strcmp($this->extension, "jpg")?$this->extension:"jpeg");
				header("Content-Type: image/".$mime_type);
				readfile($url);
				die();
			}
		}
		$this->render_error();
	}
	
    abstract public function render_error();
}