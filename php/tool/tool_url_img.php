<?php

class tool_url_img extends tool_img {
	public function __construct($url, $path_tmp) {
		/* Détection du type */
		$link = new tool_url($url);
		$url_valide = $link->is_valid();
		if ($url_valide) {
			$link->preload();
			if ($link->is_image()) {
				if (!(@is_dir($path_tmp))) {@mkdir($path_tmp, 0700, true);}
				$extension = $link->copy_to_local($path_tmp, "tmp");
				if (!(is_null($extension))) {
					$src = $path_tmp."tmp.".$extension;
					list($width, $height) = @getimagesize($src);
					if (($width > 0) && ($height > 0)) {
						$this->src = $src;
						$this->width = $width;
						$this->height = $height;
						$this->ext = $extension;
					}
				}
			}
		}
	}
	
	protected function move_uploaded_file($from, $to) {
		$ret = @rename($from, $to);
		if ($ret) {@chmod($to, 0700);}
		return $ret;
	}
}
