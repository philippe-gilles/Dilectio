<?php

abstract class tool_img extends tool_img_generic {
	protected $original_dest = null;
	protected $original_width = __DILECTIO_IMAGE_ORIGINAL_MAX_WIDTH;
	protected $original_height = __DILECTIO_IMAGE_ORIGINAL_MAX_HEIGHT;
	protected $original_compression = __DILECTIO_IMAGE_ORIGINAL_COMPRESSION;
	protected $thumb_dest = null;
	protected $thumb_width = __DILECTIO_IMAGE_THUMB_WIDTH;
	protected $thumb_height = __DILECTIO_IMAGE_THUMB_HEIGHT;
	protected $thumb_compression = __DILECTIO_IMAGE_THUMB_COMPRESSION;

	public function set_destination($dest, $original_name, $thumb_name) {
		if (!(@is_dir($dest))) {@mkdir($dest, 0700, true);}
		$this->original_dest = $dest.$original_name.".".$this->ext;
		$this->thumb_dest = $dest.$thumb_name.".".$this->ext;
	}

	public function move_and_resize_uploaded_file(&$message) {
		$ret = false;
		if (strlen($this->src) > 0) {
			if ($this->is_extension_media($this->ext)) {
				$ret = $this->resize_uploaded_file();
				if ($ret) {$ret = $this->generate_thumb();}
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
		$ret = true;
		$crop_width = ($this->width > (int) $this->original_width)?(int) $this->original_width:0;
		$crop_height = ($this->height > (int) $this->original_height)?(int) $this->original_height:0;
		if (($crop_width > 0) && ($crop_height > 0)) {
			$ratio_width = (float) (((float) $this->width) / ((float) $crop_width));
			$ratio_height = (float) (((float) $this->height) / ((float) $crop_height));
			$ratio = (float) max($ratio_width, $ratio_height);
			$largeur = (int) floor(((float) $this->width) / $ratio);
			$hauteur = (int) floor(((float) $this->height) / $ratio);
		}
		else if (($crop_width > 0) && ($crop_height <= 0)) {
			$largeur = $crop_width;
			$hauteur = $this->height * (float) (((float) $crop_width) / ((float) max($this->width,1)));
		}
		else if (($crop_width <= 0) && ($crop_height > 0)) {
			$hauteur = $crop_height;
			$largeur = $this->width * (float) (((float) $crop_height) / ((float) max($this->height,1)));
		}

		if (($crop_width > 0) || ($crop_height > 0)) {
			$this->retailler_original($this->width, $this->height, $largeur, $hauteur);
		}
		else {
			$ret = $this->move_uploaded_file($this->src, $this->original_dest);
		}
		return $ret;
	}

	protected function generate_thumb() {
		$ret = false;
		if (@file_exists($this->original_dest)) {
			$ret = true;

			// La nouvelle source devient l'original */
			$this->src = $this->original_dest;

			// Taille du thumb */
			$crop_width = ($this->width > (int) $this->thumb_width)?(int) $this->thumb_width:0;
			$crop_height = ($this->height > (int) $this->thumb_height)?(int) $this->thumb_height:0;

			if (($crop_width > 0) && ($crop_height > 0)) {
				list($delta_l, $delta_h) = $this->calculer_delta($crop_width, $crop_height);
				$largeur = $crop_width;$hauteur = $crop_height;
			}
			else if (($crop_width > 0) && ($crop_height <= 0)) {
				$delta_l = 0;$delta_h = 0;$largeur = $crop_width;
				$hauteur = $this->height * (float) (((float) $crop_width) / ((float) max($this->width,1)));
			}
			else if (($crop_width <= 0) && ($crop_height > 0)) {
				$delta_l = 0;$delta_h = 0;$hauteur = $crop_height;
				$largeur = $this->width * (float) (((float) $crop_height) / ((float) max($this->height,1)));
			}
			if (($crop_width > 0) || ($crop_height > 0)) {
				$this->retailler_thumb($this->width, $this->height, $largeur, $hauteur, $delta_l, $delta_h);
			}
			else {
				$ret = @copy($this->src, $this->thumb_dest);
				if ($ret) {@chmod($this->thumb_dest, 0700);}
			}
		}
		return $ret;
	}
	
	protected function retailler_original($largeur_image, $hauteur_image, $nouvelle_largeur, $nouvelle_hauteur) {
		switch($this->ext) {
			case self::EXTENSION_IMAGE_JPG:
				$this->retailler_jpg($largeur_image, $hauteur_image, $nouvelle_largeur, $nouvelle_hauteur, 0, 0, $this->original_compression, $this->original_dest);
				break;
			case self::EXTENSION_IMAGE_PNG:
				$this->retailler_png($largeur_image, $hauteur_image, $nouvelle_largeur, $nouvelle_hauteur, 0, 0, $this->original_compression, $this->original_dest);
				break;
			case self::EXTENSION_IMAGE_GIF:
				$this->retailler_gif($largeur_image, $hauteur_image, $nouvelle_largeur, $nouvelle_hauteur, 0, 0, $this->original_compression, $this->original_dest);
				break;
		}
		$this->width = (int) $nouvelle_largeur;
		$this->height = (int) $nouvelle_hauteur;
		@unlink($this->src);
	}
	
	protected function ajuster_original($largeur_image, $hauteur_image, $nouvelle_largeur, $nouvelle_hauteur, $delta_largeur, $delta_hauteur) {
		switch($this->ext) {
			case self::EXTENSION_IMAGE_JPG:
				$this->retailler_jpg($largeur_image, $hauteur_image, $nouvelle_largeur, $nouvelle_hauteur, $delta_largeur, $delta_hauteur, $this->original_compression, $this->original_dest);
				break;
			case self::EXTENSION_IMAGE_PNG:
				$this->retailler_png($largeur_image, $hauteur_image, $nouvelle_largeur, $nouvelle_hauteur, $delta_largeur, $delta_hauteur, $this->original_compression, $this->original_dest);
				break;
			case self::EXTENSION_IMAGE_GIF:
				$this->retailler_gif($largeur_image, $hauteur_image, $nouvelle_largeur, $nouvelle_hauteur, $delta_largeur, $delta_hauteur, $this->original_compression, $this->original_dest);
				break;
		}
		$this->width = (int) $nouvelle_largeur;
		$this->height = (int) $nouvelle_hauteur;
		@unlink($this->src);
	}

	protected function retailler_thumb($largeur_image, $hauteur_image, $nouvelle_largeur, $nouvelle_hauteur, $delta_largeur, $delta_hauteur) {
		switch($this->ext) {
			case self::EXTENSION_IMAGE_JPG:
				$this->retailler_jpg($largeur_image, $hauteur_image, $nouvelle_largeur, $nouvelle_hauteur, $delta_largeur, $delta_hauteur, $this->thumb_compression, $this->thumb_dest);
				break;
			case self::EXTENSION_IMAGE_PNG:
				$this->retailler_png($largeur_image, $hauteur_image, $nouvelle_largeur, $nouvelle_hauteur, $delta_largeur, $delta_hauteur, $this->thumb_compression, $this->thumb_dest);
				break;
			case self::EXTENSION_IMAGE_GIF:
				$this->retailler_gif($largeur_image, $hauteur_image, $nouvelle_largeur, $nouvelle_hauteur, $delta_largeur, $delta_hauteur, $this->thumb_compression, $this->thumb_dest);
				break;
		}
	}
	
	protected function retailler_jpg($largeur_image, $hauteur_image, $nouvelle_largeur, $nouvelle_hauteur, $delta_largeur, $delta_hauteur, $compression, $dest) {
		$src_r = imagecreatefromjpeg($this->src);
		if ($src_r) {
			$dst_r = ImageCreateTrueColor($nouvelle_largeur, $nouvelle_hauteur);
			if ($dst_r) {
				imagecopyresampled($dst_r, $src_r,
									0, 0, 
									$delta_largeur, $delta_hauteur, 
									$nouvelle_largeur, $nouvelle_hauteur, 
									$largeur_image - (2*$delta_largeur), $hauteur_image - (2*$delta_hauteur));
				$qualite = $compression;
				$ret = imagejpeg($dst_r, $dest, $qualite);
				if ($ret) {@chmod($dest, 0700);}
				imagedestroy($dst_r);
			}
			imagedestroy($src_r);
		}
	}
	
	protected function retailler_png($largeur_image, $hauteur_image, $nouvelle_largeur, $nouvelle_hauteur, $delta_largeur, $delta_hauteur, $compression, $dest) {
		$src_r = imagecreatefrompng($this->src);
		if ($src_r) {
			$src_alpha = $this->png_has_transparency($this->src);
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
									$largeur_image - (2*$delta_largeur), $hauteur_image - (2*$delta_hauteur));
				/* En cas d'image non transparente on reduit à une image avec palette (pb de taille) */
				if (!$src_alpha) {
					$tmp = ImageCreateTrueColor($nouvelle_largeur, $nouvelle_hauteur);
					ImageCopyMerge($tmp, $dst_r, 0, 0, 0, 0, $nouvelle_largeur, $nouvelle_hauteur, 100);
					ImageTrueColorToPalette($dst_r, false, 8192);
					ImageColorMatch($tmp, $dst_r);
					ImageDestroy($tmp);
				}
				$qualite = (int) ($compression / 10);
				$ret = imagepng($dst_r, $dest, $qualite);
				if ($ret) {@chmod($dest, 0700);}
				imagedestroy($dst_r);
			}
			imagedestroy($src_r);
		}
	}
	
	protected function retailler_gif($largeur_image, $hauteur_image, $nouvelle_largeur, $nouvelle_hauteur, $delta_largeur, $delta_hauteur, $compression, $dest) {
		$src_r = imagecreatefromgif($this->src);
		if ($src_r) {
			$dst_r = ImageCreateTrueColor($nouvelle_largeur, $nouvelle_hauteur);
			if ($dst_r) {
				imagecopyresampled($dst_r, $src_r,
									0, 0, 
									$delta_largeur, $delta_hauteur, 
									$nouvelle_largeur, $nouvelle_hauteur, 
									$largeur_image - (2*$delta_largeur), $hauteur_image - (2*$delta_hauteur));
				$ret = imagegif($dst_r, $dest);
				if ($ret) {@chmod($dest, 0700);}
				imagedestroy($dst_r);
			}
			imagedestroy($src_r);
		}
		return $ret;
	}
}
