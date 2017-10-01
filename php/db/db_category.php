<?php

class db_category extends db_table {
	public static function children($parent) {
		$liste = db::find(db::table("category"), " parent_category_id = ? ORDER BY label", array($parent));
		return $liste;
	}
	
	/*
	 * Ecriture directe en SQL pour optimiser la vitesse de traitement
	 */
	public static function change($tab_data) {
		$sql = "UPDATE '".db::table("category")."' ";
		$sql .= "SET parent_category_id = CASE id ";
		foreach($tab_data as $data) {
			$sql .= "WHEN ".$data["id"]." THEN ".$data["parent_category_id"]. " ";
		}
		$sql .= "ELSE 0 END";
		db::exec($sql);
	}

	
	/*
	 * Ecriture directe en SQL pour optimiser la vitesse de traitement
	 */
	public static function delete($category_id) {
		$category = self::get($category_id);
		if (!(is_null($category))) {
			/* Toutes les filles de cette catégorie récupèrent son parent */
			$parent_category_id = $category->parent_category_id;
			$sql = "UPDATE '".db::table("category")."' ";
			$sql .= "SET parent_category_id = ".$parent_category_id." ";
			$sql .= "WHERE parent_category_id = ".$category_id;
			db::exec($sql);
			
			/* Suppression */
			db::trash($category);
		}
	}
	
	/* 
	 * Génération des entrées hiérarchiques pour le niceselect
	 * NB : Méthode récursive
	 * TODO : Limiter la profondeur de la récursivité pour éviter des boucles infinies
	 */
	public static function tab_categories(&$tab_categories, $parent = 0, $level = 0) {
		$categories = self::children($parent);
		foreach($categories as $category) {
			$tab_categories[$category->id] = array("label" => $category->label, "level" => $level);
			$nb_children = self::tab_categories($tab_categories, $category->id, $level + 1);
			$tab_categories[$category->id]["children"] = $nb_children;
		}
		return (count($categories));
	}
	
	/* 
	 * Génération des entrées hiérarchiques pour le nestable
	 * NB : Méthode récursive
	 * TODO : Limiter la profondeur de la récursivité pour éviter des boucles infinies
	 */
	public static function tab_nested_categories(&$tab_categories, $parent = 0, $level = 0) {
		$categories = self::children($parent);
		foreach($categories as $category) {
			$tab_categories[$category->id] = array("label" => $category->label, "level" => $level, "children" => array());
			self::tab_nested_categories($tab_categories[$category->id]["children"], $category->id, $level + 1);
		}
	}
}