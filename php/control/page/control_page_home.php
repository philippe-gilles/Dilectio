<?php

class control_page_home extends control_page {
	private $langue = null;

	public function __construct() {
		/* Configuration de la langue */
		$this->langue = tool_session::lire_param("lang");
		lang_i18n::init($this->langue);

		/* Déclaration du cadre et du layout */
		$this->frame = new view_frame_session();
		$this->layout = new view_layout_session_1();
	}

	protected function configure() {
		db::open(__DILECTIO_PREFIXE_DB);

		/* Configuration pour le cadre ****************************************/
		$this->frame->set_nom_page("home");
		$this->frame->set_excerpt_only(true);
		$this->frame->set_tiers("icomoon", "jquery", "mdl", "mdlicons", "niceselect", "swipebox");
		$types = db_type::active();
		foreach($types as $type) {
			$this->frame->set_type($type->name);
		}
		$extra_js = $this->extra_js();
		$this->frame->set_extra_js($extra_js);
		/* Fin configuration pour le cadre */

		/* Configuration pour le layout**************************************** */
		
		/* Alias */
		$profil_id = tool_session::lire_param("profil_id");
		$profil = db_profile::get($profil_id);
		$this->layout->set_alias($profil->alias);
		
		/* Bouton "home" inactif */
		$this->layout->set_home_active(false);

		/* Panneau latéral */
		$component = new view_component_input($this->langue);
		$lateral = $component->clear_filter();
		$this->layout->set_lateral($lateral);
		
		$post_max_date = db_post::first_today();
		$max_date = strtotime($post_max_date->creation);
		$navigation = $component->slider_date($max_date);
		$navigation .= "<hr>";

		$tab_categories = array();
		db_category::tab_categories($tab_categories);
		$navigation .= $component->filter_category($tab_categories);

		$tab_types = array();
		$types = db_type::active();
		foreach($types as $type) {
			$entry_name = __DILECTIO_LANG_PREFIXE_TYPE.$type->name."_name";
			$label_lang = lang_i18n::trad($this->langue, $entry_name);
			$tab_types[$label_lang] = array("id" => $type->id, "icon" => $type->icon);
		}
		tool_string::ksort_utf8($this->langue, $tab_types);
		$navigation .= $component->filter_type($tab_types);
		$navigation .= $component->checkbox_filters();
		$navigation .= o::div_div(null, _class, "mdl-layout-spacer");
		$navigation .= $component->toggle_views();
		  
		$this->layout->set_navigation($navigation);
		
		/* Panneau principal : chargement via AJAX */
		$calendar = "";
		for($cpt = 0;$cpt < 4;$cpt++) {
			$calendar .= o::div(_id, "calendar-".$cpt, _class, "dilectio-calendar")
						.o::header_header(null)
						.o::div_div(null, _class, "day-names")
						.o::div_div(null, _class, "days")
						.o::_div(_n);
		}
		$this->layout->set_principal_hidden($calendar);

		/* Fin configuration pour le layout */
		db::close();
	}

	private function extra_js() {
		$extra_js = "var tab_month = [";
		for ($cpt = 1;$cpt <= 12;$cpt++) {
			$extra_js .= "\"".lang_i18n::trad($this->langue, "month_".$cpt)."\",";
		}
		$extra_js .= "\"\"];";

		$extra_js .= "$(window).load(function() {\n";
		$cur_month = (int) date("n");$cur_year = (int) date("Y");
		$month = ($cur_month == 1)?12:($cur_month - 1);
		$year = ($cur_month == 1)?($cur_year - 1):$cur_year;
		for($cpt = 0;$cpt < 4;$cpt++) {
			$events = db_post_agenda::events($month, $year);
			foreach($events as $event) {
				$post = db_post::post("agenda", $event->id);
				$event_timestamp = strtotime($event->date);
				$event_js = "{";
				$event_js .= "day: ".date("j", $event_timestamp).",";
				$event_js .= "month: ".date("n", $event_timestamp).",";
				$event_js .= "year: ".date("Y", $event_timestamp).",";
				$event_js .= "label: '".addslashes($event->label)."',";
				$event_js .= "descr: '".addslashes($event->caption)."',";
				$href = (is_null($post))?"#":"thread-".$post->thread_id."_".$post->id;
				$event_js .= "url: \"".$href."\"";
				$event_js .= "}";
				$extra_js .= "DILECTIO.calendar.add(".$event_js.");";
			}
			$extra_js .= "DILECTIO.calendar.render('".$this->langue."', 'calendar-".$cpt."', ".$month.", ".$year.");\n";
			if ($month == 12) {$month = 1;$year += 1;}
			else {$month += 1;}
		}
		$extra_js .= "});\n";
		return $extra_js;
	}
}