<?php

class view_component_post extends view_component {
	public function post($user_id, &$post, $html, $wrapper = true) {
		/* Icone type */
		$type_id = $post->type_id;
		$type = db_type::get($type_id);
		$icone_type = o::icomoon($type->icon, array("class" => "dilectio-post-type"));

		/* Date de création */
		$datetime_post = strtotime($post->creation);
		$horodatage = $this->format_horodatage($datetime_post);
		$date_creation = o::span_span($horodatage, _class, "dilectio-post-date");

		/* Image profil */
		$image_profil = o::img(_src, __DILECTIO_PROFILES."profile-".$post->profile_id."/avatar.png", _class, "dilectio-post-avatar");

		/* Emotions autres */
		$icones_emotions_autres = "";
		$emotions = db_post_emotion::post_emotionned_by_not($post->id, $user_id);
		foreach($emotions as $profil => $emotion) {
			$nom_icone = ($emotion->icon)."2"; // Cf Icomoon
			$icone_autre = o::icomoon($nom_icone, array("class" => "dilectio-post-emotion-autre", "title" => $profil));
			$icones_emotions_autres .= $icone_autre;
		}

		/* Actions */
		$actions = "";
		if ($user_id == $post->profile_id) {
			/* Le type doit être éditable */
			if ($type->editable > 0) {
				$icone_edit = o::mdlicon("edit");
				/* Le post doit être ni émotionné ni favorisé */
				if (count($emotions) > 0) {
					$label_warning_emotion = lang_i18n::trad($this->langue, "post_edit_warning_emotion");
					$actions .= o::button_button($icone_edit, _id, "edit-".$post->id, _class, "mdl-button mdl-js-button mdl-button--icon dilectio-post-edit", _disabled, "disabled", _title, $label_warning_emotion, _style, "cursor:not-allowed;");
				}
				else {
					$favorited = db_favorite::post_favorite_by_not($post->id, $user_id);
					if ($favorited) {
						$label_warning_favorite = lang_i18n::trad($this->langue, "post_edit_warning_favorite");
						$actions .= o::button_button($icone_edit, _id, "edit-".$post->id, _class, "mdl-button mdl-js-button mdl-button--icon dilectio-post-edit", _disabled, "disabled", _title, $label_warning_favorite, _style, "cursor:not-allowed;");
					}
					else {
						$label_post_edit = lang_i18n::trad($this->langue, "edition");
						$actions .= o::button_button($icone_edit, _id, "edit-".$post->id, _class, "mdl-button mdl-js-button mdl-button--icon dilectio-post-edit", _title, $label_post_edit);
					}
				}
			}
			$label_post_delete = lang_i18n::trad($this->langue, "delete");
			$icone_trash = o::mdlicon("delete");
			$actions .= o::button_button($icone_trash, _id, "delete-".$post->id, _class, "mdl-button mdl-js-button mdl-button--icon dilectio-post-delete", _title, $label_post_delete);
		}

		/* Menu des émotions */
		$menu_emotions = $this->menu_emotions($user_id, $post->id);

		/* Bouton des favoris */
		$toggle_favori = $this->button_favorite($user_id, $post->id);

		/* Classes selon post "mine" ou "not mine" */
		$wrapper_mine_not_mine = ($post->profile_id == $user_id)?"dilectio-post-wrapper-mine":"dilectio-post-wrapper-not-mine";
		$bubble_mine_not_mine = ($post->profile_id == $user_id)?"dilectio-post-bubble-mine":"dilectio-post-bubble-not-mine";
		
		/* Si c'est un cadeau non ouvert on occulte le contenu */
		if (($post->is_gift) && ($post->profile_id != $user_id)) {
			$is_opened = db_gift_open::is_opened_by($post->id, $user_id);
			if (!($is_opened)) {
				$icone_gift = o::icomoon("gift");
				$button_gift = o::button_button($icone_gift, _id, "gift-".$post->id, _class, "mdl-button mdl-js-button mdl-button--fab dilectio-post-gift-curtain-handle");
				$html = o::div_div($button_gift, _id, "curtain-".$post->id, _class, "dilectio-post-gift-curtain").$html;
			}
		}
		
		/* Création du post */
		$o = "";
		$read = db_read::post_read_by($post->id, $user_id);
		if ($wrapper) {
			$o .= o::a_a(null, _id, "anchor-post-".$post->id, _name, $post->id, _n)
				.o::div(_class, "dilectio-post-wrapper ".$wrapper_mine_not_mine);
		}
		$o .= o::div(_id, "article-".$post->id, _class, "mdl-shadow--4dp dilectio-post ".$bubble_mine_not_mine, "data-type", $type_id, "data-profil", $post->profile_id, _n)
			.o::div(_class, "mdl-card__title mdl-card--border dilectio-post-titre")
			.$icone_type
			.$date_creation
			.o::div_div(null, _class, "mdl-layout-spacer")
			.$image_profil
			.o::_div(_n)
			.o::div(_class, "mdl-card__supporting-text dilectio-post-contenu", _n)
			.$html
			.o::_div(_n)
			.o::div(_class, "mdl-card__actions mdl-card--border dilectio-post-actions", _n)
			.$actions
			.o::div_div(null, _class, "mdl-layout-spacer")
			.$icones_emotions_autres
			.o::span_span(null, _style, "position:absolute;top:0;right:80px;width:1px;height:100%;border-left:1px solid rgba(0, 0, 0, 0.1);")
			.o::div_div($menu_emotions, _id, "post-emotion-".$post->id, _style, "position:relative;")
			.$toggle_favori
			.o::_div(_n)
			.o::_div(_n);
		if ($wrapper) {
			$o .= o::_div(_n);
		}
		return $o;
	}
	
	public function new_post() {
		$o = o::a_a(null, _id, "anchor-post-new", _name, "new", _n)
			.o::div_div(null, _class, "dilectio-new-post-wrapper")
			.o::div_div(null, _class, "dilectio-new-post-footer");
		return $o;
	}

	public function menu_emotions($user_id, $post_id) {
		$emotionned = db_post_emotion::post_emotionned_by($post_id, $user_id);
		if (!(is_null($emotionned))) {
			$icone_bouton_emotion = o::icomoon($emotionned->icon);
			$classe_bouton_emotion = "actif";
			$emotion_select_id = $emotionned->id;
		}
		else {
			$icone_bouton_emotion = o::icomoon("neutral");
			$classe_bouton_emotion = "inactif";
			$emotion_select_id = 0;
		}
		$id_bouton_menu = "bouton-menu-emotion-".$post_id;
		$id_menu = "menu-emotion-".$post_id;
		$emotions = db_emotion::all();

		$nom_moi = lang_i18n::trad($this->langue, "me");
		$o = o::button_button($icone_bouton_emotion, _id, $id_bouton_menu, _class, "mdl-button mdl-js-button mdl-button--raised mdl-button--icon dilectio-post-emotions-bouton dilectio-post-emotions-bouton-".$classe_bouton_emotion, _title, $nom_moi)
			.o::ul(_id, $id_menu, _class, "mdl-menu mdl-menu--top-right mdl-js-menu mdl-js-ripple-effect dilectio-post-emotions-menu", "data-mdl-for", $id_bouton_menu);
		foreach($emotions as $emotion) {
			$icone_emotion = o::icomoon($emotion->icon);
			$emotion_id = $emotion->id;
			$classe_liste_emotion = ($emotion_id == $emotion_select_id)?"select":"actif";
			$o .= o::li_li($icone_emotion, _id, "emotion-".$emotion->id, _class, "mdl-menu__item dilectio-post-emotions-menu-icone dilectio-post-emotions-menu-icone-".$classe_liste_emotion);
		}
		$o .= o::li_li(o::icomoon("neutral"), _id, "emotion-0", _class, "mdl-menu__item dilectio-post-emotions-menu-icone dilectio-post-emotions-menu-icone-inactif");
		$o .= o::_ul();
		return $o;
	}

	public function button_favorite($user_id, $post_id) {
		$favori = db_favorite::post_favorite_by($post_id, $user_id);
		$label_title = lang_i18n::trad($this->langue, "favorite_some");
		$o = o::label(_class, "mdl-icon-toggle mdl-js-icon-toggle mdl-js-ripple-effect dilectio-post-favori", _for, "toggle-favorite-".$post_id , _title, $label_title);
		if ($favori) {
			$o .= o::input_checkbox(_id, "toggle-favorite-".$post_id, _name, "toggle-favorite", _class, "mdl-icon-toggle__input dilectio-post-favori-icone", _checked, "checked");
			$o .= o::mdlicon("favorite", array(_class => "dilectio-post-favori-vrai"));
		}
		else {
			$o .= o::input_checkbox(_id, "toggle-favorite-".$post_id, _name, "toggle-favorite", _class, "mdl-icon-toggle__input dilectio-post-favori-icone");
			$o .= o::mdlicon("favorite", array(_class => "dilectio-post-favori-faux"));
		}
		$o .= o::_label();

		return $o;
	}
}