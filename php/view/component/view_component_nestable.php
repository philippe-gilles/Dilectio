<?php

class view_component_nestable extends view_component {

	private $icone_drag = null;
	private $icone_done = null;
	private $icone_undo = null;
	private $icone_delete = null;

	public function __construct($langue = __DILECTIO_LANGUE_DEFAUT) {
		$this->icone_drag = o::mdlicon("drag_handle");
		$this->icone_done = o::mdlicon("done");
		$this->icone_undo = o::mdlicon("undo");
		$this->icone_delete = o::mdlicon("delete");

		parent::__construct($langue);
	}
	
	public function expand_collapse_buttons() {
		$icone_collapse = o::icomoon("folder");
		$icone_expand = o::icomoon("folder-open");
		$icone_sort = o::icomoon("sort-alpha-asc");
		$html = o::div(_class, "dilectio-config-category-tree-management");
		$html .= o::button_button($icone_collapse, _class, "mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent dilectio-config-category-tree-collapse");
		$html .= o::button_button($icone_expand, _class, "mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent dilectio-config-category-tree-expand");
		$html .= o::button_button($icone_sort, _class, "mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent dilectio-config-category-tree-sort");
		$html .= o::_div();
		
		return $html;
	}
	
	public function list_categories($tab_nested_categories) {
		$liste = $this->nested_categories($tab_nested_categories);
		$html = o::div(_id, "nestable-categories", _class, "dilectio-nestable");
		$html .= o::ol(_id, "nestable-categories-root", _class, "dilectio-nestable-list");
		$html .= $liste;
		$html .= o::_ol();
		$html .= o::_div();

		return $html;
	}

	public function new_category() {	
		$label_new_category = lang_i18n::trad($this->langue, "category_new");
		$html = o::div(_class, "dilectio-config-category-new");
		$html .= o::button_button($label_new_category, _class, "mdl-button mdl-js-button mdl-button--raised mdl-button--accent dilectio-config-category-new-button");
		$html .= o::_div();
		
		return $html;
	}
	
	public function generate_nested_category($category_id, $label, $close_li = true) {
		$html = o::li(_class, "dilectio-nestable-item dilectio-nestable-extension-item", "data-id", $category_id);
		$html .= o::div_div($this->icone_drag, _class, "mdl-color-text--accent dilectio-nestable-handle dilectio-nestable-extension-handle");
		$html .= o::div(_class, "dilectio-nestable-extension-content");

		$html .= o::div(_class, "mdl-textfield mdl-js-textfield dilectio-config-category-field");
		$html .= o::input_text(_id, "field-".$category_id, _class, "mdl-textfield__input dilectio-config-category-name", _value, $label);
		$html .= o::_div();

		$html .= o::button_button($this->icone_undo, _id, "undo-".$category_id, _class, "mdl-button mdl-js-button mdl-button--icon dilectio-config-category-undo");
		$html .= o::button_button($this->icone_done, _id, "done-".$category_id, _class, "mdl-button mdl-js-button mdl-button--icon dilectio-config-category-done");
		$html .= o::button_button($this->icone_delete, _id, "delete-".$category_id, _class, "mdl-button mdl-js-button mdl-button--icon dilectio-config-category-delete");

		$html .= o::_div();
		if ($close_li) {$html .= o::_li();}
		
		return $html;
	}

	/* ATTENTION : Méthode récursive */
	private function nested_categories($tab_nested_categories) {
		$html = "";
		foreach($tab_nested_categories as $categorie_id => $info_categorie) {
			if ($categorie_id > 0) {
				$html .= $this->generate_nested_category($categorie_id, $info_categorie["label"], false);
				$children = $info_categorie["children"];
				$nb_children = count($children);
				if ($nb_children > 0) {
					$html .= o::ol(_class, "dilectio-nestable-list");
					$html .= $this->nested_categories($children);
					$html .= o::_ol();
				}
				$html .= o::_li();
			}
		}
		return $html;
	}
}