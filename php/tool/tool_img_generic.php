<?php 

abstract class tool_img_generic {
	const EXTENSION_IMAGE_JPEG = "jpeg";
	const EXTENSION_IMAGE_JPG = "jpg";
	const EXTENSION_IMAGE_PNG = "png";
	const EXTENSION_IMAGE_GIF = "gif";
	
	/* Propriétés et accesseurs */
	protected $src = null;
	protected $width = 0;
	protected $height = 0;
	protected $ext = null;
	public function get_width() {return $this->width;}
	public function get_height() {return $this->height;}
	public function get_extension() {return $this->ext;}

	protected function calculer_delta($crop_width, $crop_height) {
		if ($crop_height == 0) {$rapport_1 = 1;}
		else {$rapport_1 = (float) (((float) $this->width) / ((float) $this->height));}
		if ($crop_height == 0) {$rapport_0 = 1;}
		else {$rapport_0 = (float) (((float) $crop_width) / ((float) $crop_height));}
		if ($rapport_0 > $rapport_1) {
			$delta_l = 0;
			$delta_h = (int) (($this->height * $crop_width - $this->width * $crop_height) / (2 * max($crop_width, 1)));
		}
		elseif ($rapport_0 < $rapport_1) {
			$delta_l = (int) (($this->width * $crop_height - $this->height * $crop_width) / (2 * max($crop_height,1)));
			$delta_h = 0;
		}
		else {
			$delta_l = 0;
			$delta_h = 0;
		}
		return array($delta_l, $delta_h);
	}

	// Grand merci à http://www.jonefox.com/ !!!
	protected function png_has_transparency($filename) {
		if (strlen($filename) == 0 || !file_exists($filename)) return false;
		if (ord(file_get_contents($filename, false, null, 25, 1)) & 4) return true;
		$contents = file_get_contents($filename);
		if (stripos($contents, 'PLTE') !== false && stripos($contents, 'tRNS') !== false) return true;
		return false;
	}
	
	/**
	 * https://stackoverflow.com/questions/280658/can-i-detect-animated-gifs-using-php-and-gd
	 * Original at http://it.php.net/manual/en/function.imagecreatefromgif.php#59787
	 **/
	protected function is_animated_gif($filename) {
		$ret = false;
		$raw = @file_get_contents($filename);
		if ($raw !== false) {	
			$offset = 0;
			$frames = 0;
			while ($frames < 2) {
				$where1 = strpos($raw, "\x00\x21\xF9\x04", $offset);
				if ($where1 === false) {
					break;
				}
				else {
					$offset = $where1 + 1;
					$where2 = strpos($raw, "\x00\x2C", $offset);
					if ($where2 === false) {
						break;
					}
					else {
						if ($where1 + 8 == $where2)	{
							$frames ++;
						}
						$offset = $where2 + 1;
					}
				}
			}
			$ret = ($frames > 1);
		}
		return $ret;
	}
	
	protected function get_extension_fichier($fichier) {
		$ret = strtolower(pathinfo($fichier, PATHINFO_EXTENSION));
		if ($ret === self::EXTENSION_IMAGE_JPEG) {$ret = self::EXTENSION_IMAGE_JPG;}
		return $ret;
	}
	
	protected function is_extension_media($ext) {
		$ret = (!(strcmp($ext, self::EXTENSION_IMAGE_JPG)));
		$ret = $ret || (!(strcmp($ext, self::EXTENSION_IMAGE_PNG)));
		$ret = $ret || (!(strcmp($ext, self::EXTENSION_IMAGE_GIF)));
		return $ret;
	}
}