<?php


/**
 * Classe de gestion des paramètres file (from PL3)
 */

class tool_post_file {
	private $file = null;
	private $valide = false;
	private $message = null;

	private $name = null;
	private $type = null;
	private $tmp_name = null;
	private $error = 0;
	private $size = 0;

	private $nom_final = null;
	private $extension_finale = null;
	private $unauthorized_ext = array("asp", "bat", "cab", "com", "dll", "exe", "ini", "js", "msi", "php", "sh", "sql", "vbs", "zip");
	private $authorized_ext = array();

	public function __construct($file) {
		$this->file = $file;
	}

	public function set_authorized_ext($authorized_ext) {
		$this->authorized_ext = $authorized_ext;
	}

	public function load() {
		$this->valide = false;
		if (isset($this->file["error"])) {
			$this->error = $this->file["error"];
			if ($this->error != UPLOAD_ERR_OK) {
				switch ($this->error) {
					case UPLOAD_ERR_INI_SIZE:
					case UPLOAD_ERR_FORM_SIZE:
						$this->message = "upload_err_size_max";
						break;
					case UPLOAD_ERR_PARTIAL:
						$this->message = "upload_err_partial";
						break;
					case UPLOAD_ERR_NO_FILE:
						$this->message = "upload_err_no_file";
						break;
					case UPLOAD_ERR_NO_TMP_DIR:
						$this->message = "upload_err_no_tmp";
						break;
					case UPLOAD_ERR_CANT_WRITE:
						$this->message = "upload_err_no_write";
						break;
					case UPLOAD_ERR_EXTENSION:
						$this->message = "upload_err_server";
						break;
					default:
						$this->message = "error_unknown";
				}
			}
			else {
				$this->name = isset($this->file["name"])?$this->file["name"]:null;
				$this->type = isset($this->file["type"])?$this->file["type"]:null;
				$this->tmp_name = isset($this->file["tmp_name"])?$this->file["tmp_name"]:null;
				$this->size = isset($this->file["size"])?$this->file["size"]:0;

				if (strlen($this->tmp_name) == 0) {
					$this->message = "upload_err_no_tmp_name";
				}
				else if (!is_uploaded_file($this->tmp_name)) {
					$this->message = "upload_err_no_tmp_file";
				}
				else if (strlen($this->name) == 0) {
					$this->message = "upload_err_filename_empty";
				}
				else if (($this->size == 0) || (filesize($this->tmp_name) == 0)) {
					$this->message = "upload_err_file_empty";
				}
				else {
					$extension_finale = strtolower(substr(strrchr($this->name,"."),1));
					if (strlen($extension_finale) == 0) {
						$this->message = "upload_err_ext_missing";
					}
					else if (in_array($extension_finale, $this->unauthorized_ext)) {
						$this->message = "upload_err_ext_unauthorized";
					}
					else if ((count($this->authorized_ext) > 0) && (!(in_array($extension_finale, $this->authorized_ext)))) {
						$this->message = "upload_err_ext_unauthorized";
					}
					else {
						$this->extension_finale = $extension_finale;
						$nom_sans_extension = substr($this->name, 0, strlen($this->name)-strlen($extension_finale)-1);
						$this->nom_final = $nom_sans_extension;
						$this->valide = true;
					}
				}
			}
		}
		else {
			$this->message = "error_unknown";
		}
	}
	
	public function delete_tmp() {
		if (@file_exists($this->tmp_name)) {return (@unlink($this->tmp_name));}
	}
	
	public function copy_to_final($nom_fichier_final) {
		$ret = @move_uploaded_file($this->tmp_name, $nom_fichier_final);
		return $ret;
	}

	public function is_valid() {return $this->valide;}
	public function message() {return $this->message;}

	public function get_name() {return $this->name;}
	public function get_type() {return $this->type;}
	public function get_tmp_name() {return $this->tmp_name;}
	public function get_error() {return $this->error;}
	public function get_size() {return $this->size;}
	
	public function get_final_name() {return $this->nom_final;}
	public function get_final_ext() {return $this->extension_finale;}
	
	/* 
	 * UPLOAD MAX SIZE : FROM DRUPAL
	 * https://api.drupal.org/api/drupal/includes%21file.inc/function/file_upload_max_size/7.x
	 */
	public static function file_upload_max_size() {
		static $max_size = -1;

		if ($max_size < 0) {
			// Start with post_max_size.
			$max_size = self::parse_size(ini_get('post_max_size'));

			// If upload_max_size is less, then reduce. Except if upload_max_size is
			// zero, which indicates no limit.
			$upload_max = self::parse_size(ini_get('upload_max_filesize'));
			if ($upload_max > 0 && $upload_max < $max_size) {
				$max_size = $upload_max;
			}
		}
		return $max_size;
	}

	private static function parse_size($size) {
		$unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
		$size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
		if ($unit) {
			// Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
			return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
		}
		else {
			return round($size);
		}
	}
}

