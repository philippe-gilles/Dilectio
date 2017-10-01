<?php

abstract class tool_avatar extends tool_img_generic {
	const AVATAR_WIDTH = 256;
	const AVATAR_HEIGHT = 256;

	protected $dest = null;	
	protected $user_id = 0;

	public function __construct($user_id) {
		if ($user_id > 0) {
			$this->user_id = $user_id;
			$path = __DILECTIO_PROFILES."profile-".$user_id."/";
			$this->dest = $path."avatar.png";
		}
	}

	public function move_and_resize_uploaded_file(&$message) {
		$ret = false;
		if (strlen($this->src) > 0) {
			if ($this->is_extension_media($this->ext)) {
				$ret = $this->resize_uploaded_file();
				if (!($ret)) {$message = "upload_err_file_install_failed";}
			}
			else {
				$message = "upload_err_unknown_file_format";
				@unlink($this->src);
			}
		}
		else {
			$message = "upload_err_no_tmp_file";
		}
		return $ret;
	}
	
	abstract protected function move_uploaded_file($from, $to);

	protected function resize_uploaded_file() {
		list($delta_l, $delta_h) = $this->calculer_delta(self::AVATAR_WIDTH, self::AVATAR_HEIGHT);
		$this->retailler(self::AVATAR_WIDTH, self::AVATAR_HEIGHT, $delta_l, $delta_h);
		return true;
	}
	
	protected function retailler($nouvelle_largeur, $nouvelle_hauteur, $delta_largeur, $delta_hauteur) {
		$src_r = null;
		switch($this->ext) {
			case self::EXTENSION_IMAGE_JPG:
				$src_r = imagecreatefromjpeg($this->src);
				$src_alpha = false;
				break;
			case self::EXTENSION_IMAGE_PNG:
				$src_r = imagecreatefrompng($this->src);
				$src_alpha = $this->png_has_transparency($this->src);
				break;
			case self::EXTENSION_IMAGE_GIF:
				$src_r = imagecreatefromgif($this->src);
				$src_alpha = false;
				break;
		}
		if ($src_r) {
			$dst_r = ImageCreateTrueColor($nouvelle_largeur, $nouvelle_hauteur);
			if ($dst_r) {
				if ($src_alpha) {
					imagealphablending($dst_r, false);
					imagesavealpha($dst_r, true);
				}
				imagecopyresampled($dst_r, $src_r,
									0, 0, 
									$delta_largeur, $delta_hauteur, 
									$nouvelle_largeur, $nouvelle_hauteur, 
									($this->width) - (2*$delta_largeur), ($this->height) - (2*$delta_hauteur));
				/* En cas d'image non transparente on reduit à une image avec palette (pb de taille) */
				if (!$src_alpha) {
					$tmp = ImageCreateTrueColor($nouvelle_largeur, $nouvelle_hauteur);
					ImageCopyMerge($tmp, $dst_r, 0, 0, 0, 0, $nouvelle_largeur, $nouvelle_hauteur, 100);
					ImageTrueColorToPalette($dst_r, false, 8192);
					ImageColorMatch($tmp, $dst_r);
					ImageDestroy($tmp);
				}
				$ret = imagepng($dst_r, $this->dest, 9);
				imagedestroy($dst_r);
			}
			imagedestroy($src_r);
		}
		@unlink($this->src);
	}
}
