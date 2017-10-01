<?php

class view_component_nav extends view_component {
	public function nav_types($tab_types, $active = true) {
		$o = "";
		foreach($tab_types as $label => $info_type) {
			if ($active) {
				$icone = o::icomoon($info_type["icon"], array("class" => "dilectio-navigation-type-post-icone"));
				$o .= o::a(_id, "nav-type-".$info_type["id"], _class, "mdl-navigation__link dilectio-navigation-type-post", _href, "#")
					.$icone
					.o::span_span($label, _class, "dilectio-navigation-type-post-label")
					.o::_a();
			}
			else {
				$icone = o::icomoon($info_type["icon"], array("class" => "dilectio-navigation-type-post-icone-inactive"));
				$o .= o::p(_class, "dilectio-navigation-type-post-inactif")
					.$icone
					.o::span_span($label, _class, "dilectio-navigation-type-post-label-inactif")
					.o::_p();
			}
		}
		return $o;
	}

	public function nav_config($tab_config, $selected = null) {
		$o = "";
		foreach($tab_config as $label => $info_config) {
			$icone = o::icomoon($info_config["icon"], array("class" => "dilectio-navigation-type-post-icone"));
			if (strcmp($info_config["id"], $selected)) {
				$href = isset($info_config["href"])?$info_config["href"]:"";
				$href = (strlen($href) > 0)?$href:"#";
				$o .= o::a(_id, "nav-config-".$info_config["id"], _class, "mdl-navigation__link dilectio-navigation-type-post", _href, $href)
					.$icone
					.o::span_span($label, _class, "dilectio-navigation-type-post-label")
					.o::_a();
			}
			else {
				$o .= o::span(_class, "dilectio-navigation-link-selected")
					.$icone
					.o::span_span($label, _class, "dilectio-navigation-type-post-label")
					.o::_span();
			}
		}
		return $o;
	}
}