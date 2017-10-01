<?php

class tool_url_avatar extends tool_avatar {
	public function __construct($user_id, $url) {
		$path_tmp = __DILECTIO_PROFILES."profile-".$user_id."/";
		/* Détection du type */
		$link = new tool_url($url);
		$url_valide = $link->is_valid();
		if ($url_valide) {
			$link->preload();
			if ($link->is_image()) {
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
		parent::__construct($user_id);
	}
	
	protected function move_uploaded_file($from, $to) {
		$ret = @rename($from, $to);
		return $ret;
	}
}
