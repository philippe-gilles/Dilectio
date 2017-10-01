<?php

class view_component {
	protected $langue = null;

	public function __construct($langue = __DILECTIO_LANGUE_DEFAUT) {
		$this->langue = $langue;
	}

	public function format_horodatage($datetime_info) {
		if (date("Ymd", $datetime_info) == date("Ymd")) {
			$format = lang_i18n::trad($this->langue, "format_time");
			$horodatage = date($format, $datetime_info);
		}
		else if (date("Y", $datetime_info) == date("Y")) {
			$no_jour = (int) date("j", $datetime_info);
			$no_mois = (int) date("n", $datetime_info);
			$nom_mois = lang_i18n::trad($this->langue, "month_".$no_mois);
			$format = lang_i18n::trad($this->langue, "format_date_month_name");
			$horodatage = str_replace("mmmm", $nom_mois, $format);
			$horodatage = str_replace("d", $no_jour, $horodatage);
		}
		else {
			$format = lang_i18n::trad($this->langue, "format_date_long");
			$horodatage = date($format, $datetime_info);
		}
		return $horodatage;
	}
	
	public function format_intervalle($first_date, $last_date) {
		$first_format = $this->format_horodatage($first_date);
		$last_format = $this->format_horodatage($last_date);
		if (strcmp($first_format, $last_format)) {
			$ret = $first_format." - ".$last_format;
		}
		else {
			$ret = $first_format;
		}
		return $ret;
	}
}