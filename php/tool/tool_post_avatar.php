<?php

class tool_post_avatar extends tool_avatar {
	public function __construct($user_id, &$file) {
		$src = $file->get_tmp_name();
		if ((@file_exists($src)) && (@is_uploaded_file($src))) {
			list($width, $height) = @getimagesize($src);
			if (($width > 0) && ($height > 0)) {
				$this->src = $src;
				$this->width = $width;
				$this->height = $height;
				$dest = $file->get_name();
				$this->ext = (strlen($dest) > 0)?$this->get_extension_fichier($dest):self::EXTENSION_IMAGE_JPG;
			}
		}
		parent::__construct($user_id);
	}
	
	protected function move_uploaded_file($from, $to) {
		$ret = @move_uploaded_file($from, $to);
		return $ret;
	}
}
