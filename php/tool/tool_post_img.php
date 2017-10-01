<?php

class tool_post_img extends tool_img {
	public function __construct(&$file) {
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
	}
	
	protected function move_uploaded_file($from, $to) {
		$ret = @move_uploaded_file($from, $to);
		if ($ret) {@chmod($to, 0700);}
		return $ret;
	}
}
