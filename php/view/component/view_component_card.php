<?php

class view_component_card extends view_component {
	public function post_card($user_id, &$post, $html) {
		/* Icone type */
		$type_id = $post->type_id;
		$type = db_type::get($type_id);
		$icone_type = o::icomoon($type->icon, array("class" => "dilectio-extrait-type"));

		/* Date de création */
		$datetime_post = strtotime($post->creation);
		$horodatage = $this->format_horodatage($datetime_post);
		$date_creation = o::span_span($horodatage, _class, "dilectio-extrait-date");
		
		/* Infos sur le fil de discussion */
		$fil_id = $post->thread_id;
		$fil = db_thread::get($fil_id);
		$label_fil = o::h3_h3($fil->label, _class, "dilectio-extrait-fil");
		$post_is_mine = ($post->profile_id == $user_id) && (($post->modifier_profile_id == 0) || ($post->modifier_profile_id == $user_id));

		/* Icone catégorie */
		$icone_categorie = o::icomoon("folder-open", array("class" => "dilectio-extrait-type"));

		/* Nom de la catégorie */
		$categorie_id = $fil->category_id;
		if ($categorie_id > 0) {
			$categorie = db_category::get($categorie_id);
			$nom_categorie = $categorie->label;
		}
		else {
			$nom_categorie = lang_i18n::trad($this->langue, "category_none");
		}
		$label_categorie = o::span_span($nom_categorie, _class, "dilectio-extrait-categorie");

		/* Image profil */
		$image_profil = o::img(_src, __DILECTIO_PROFILES."profile-".$post->profile_id."/avatar.png", _class, "dilectio-extrait-avatar");
		
		/* Image du profil "modifier" si nécessaire */
		$image_profil_modifier = "";
		if (($post->modifier_profile_id > 0) && ($post->modifier_profile_id != $post->profile_id)) {
			$image_profil_modifier = o::img(_src, __DILECTIO_PROFILES."profile-".$post->modifier_profile_id."/avatar.png", _class, "dilectio-extrait-avatar");
			$image_profil_modifier .= o::mdlicon("keyboard_arrow_right");
		}
		
		/* Bouton ouverture */
		$read = $post_is_mine?true:db_read::post_read_by($post->id, $user_id);
		$button_class = $read?"":" mdl-button--accent";
		$button_label = lang_i18n::trad($this->langue, "open");
		$button_href = "thread-".$fil_id."_".$post->id;
		$button_action = o::a_a($button_label, _class, "mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect".$button_class, _href, $button_href);

		/* Icones des émotions autres */
		$icones_emotions_autres = "";
		$emotions = db_post_emotion::post_emotionned_by_not($post->id, $user_id);
		foreach($emotions as $profil => $emotion) {
			$nom_icone = ($emotion->icon)."2"; // Cf Icomoon
			$icone_autre = o::icomoon($nom_icone, array("class" => "dilectio-extrait-emotion-autre", "title" => $profil));
			$icones_emotions_autres .= $icone_autre;
		}

		/* Icone des émotions perso */
		$icone_emotion_perso = "";
		$emotion_perso = db_post_emotion::post_emotionned_by($post->id, $user_id);
		if (!(is_null($emotion_perso))) {
			$nom_moi = lang_i18n::trad($this->langue, "me");
			$icone_emotion_perso .= o::icomoon($emotion_perso->icon, array("class" => "dilectio-extrait-emotion", "title" => $nom_moi));
		}

		/* Icone des favoris */
		$favori = db_favorite::post_favorite_by($post->id, $user_id);
		$icone_favori = ($favori)?o::mdlicon("favorite", array("class" => "dilectio-extrait-favori-vrai")):"";

		/* Post caché par défaut si c'est celui de l'auteur */
		$class_sup = $post_is_mine?" dilectio-extrait-hide":"";
		
		/* Si c'est un cadeau non ouvert on occulte le contenu */
		if (($post->is_gift) && ($post->profile_id != $user_id)) {
			$is_opened = db_gift_open::is_opened_by($post->id, $user_id);
			if (!($is_opened)) {
				$icone_gift = o::icomoon("gift", array("class" => "dilectio-extrait-gift"));
				$html = o::div_div($icone_gift, _class, "dilectio-extrait-gift-container");
			}
		}

		/* Création de l'extrait */
		$datetime_slider = $datetime_post - ($datetime_post % 86400);
		$o = o::div(_id, "article-".$post->id, _class, "mdl-cell mdl-card mdl-shadow--4dp dilectio-extrait".$class_sup, "data-time", $datetime_slider, "data-category", $categorie_id, "data-type", $type_id, "data-mine", (int) $post_is_mine, "data-read", (int) $read, "data-favorite", (int) $favori, _n)
			.o::div(_class, "mdl-card__title mdl-card--border dilectio-extrait-titre")
			.$icone_type
			.$date_creation
			.$icone_categorie
			.$label_categorie
			.o::div_div(null, _class, "mdl-layout-spacer")
			.$image_profil_modifier
			.$image_profil
			.o::_div(_n)
			.o::div(_class, "mdl-card__supporting-text dilectio-extrait-contenu", _n)
			.$label_fil
			.o::div_div($html, _class, "dilectio-extrait-apercu")
			.o::_div(_n)
			.o::div(_class, "mdl-card__actions mdl-card--border dilectio-extrait-actions", _n)
			.$button_action
			.o::div_div(null, _class, "mdl-layout-spacer")
			.$icones_emotions_autres
			.$icone_emotion_perso
			.$icone_favori
			.o::_div(_n)
			.o::_div(_n);
		return $o;
	}

	public function post_pending($thread_id) {
		$thread = db_thread::get($thread_id);

		/* Date de création */
		$datetime_post = strtotime($thread->creation);
		$horodatage = $this->format_horodatage($datetime_post);
		$date_creation = o::span_span($horodatage, _class, "dilectio-extrait-date");
		
		/* Infos sur le fil de discussion */
		$label_fil = o::h3_h3($thread->label, _class, "dilectio-extrait-fil");

		/* Icone catégorie */
		$icone_categorie = o::icomoon("folder-open", array("class" => "dilectio-extrait-type"));

		/* Nom de la catégorie */
		$categorie_id = $thread->category_id;
		if ($categorie_id > 0) {
			$categorie = db_category::get($categorie_id);
			$nom_categorie = $categorie->label;
		}
		else {
			$nom_categorie = lang_i18n::trad($this->langue, "category_none");
		}
		$label_categorie = o::span_span($nom_categorie, _class, "dilectio-extrait-categorie");

		/* Image profil */
		$image_profil = o::img(_src, __DILECTIO_IMAGES."system.png", _class, "dilectio-extrait-avatar");
		
		/* Bouton ouverture */
		$button_label = lang_i18n::trad($this->langue, "open");
		$button_href = "thread-".$thread_id;
		$button_action = o::a_a($button_label, _class, "mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect  mdl-button--accent", _href, $button_href);

		/* Création de l'extrait */
		$pending_label = lang_i18n::trad($this->langue, "thread_warning_empty");
		$o = o::div(_id, "system-".$thread->id, _class, "mdl-cell mdl-card mdl-shadow--4dp dilectio-extrait dilectio-extrait-system", "data-category", $categorie_id, "data-type", 0, "data-mine", 0, "data-read", 0, "data-favorite", 0, _n)
			.o::div(_class, "mdl-card__title mdl-card--border dilectio-extrait-titre")
			.$date_creation
			.$icone_categorie
			.$label_categorie
			.o::div_div("", _class, "mdl-layout-spacer")
			.$image_profil
			.o::_div(_n)
			.o::div(_class, "mdl-card__supporting-text dilectio-extrait-contenu", _n)
			.$label_fil
			.o::div_div($pending_label, _class, "dilectio-extrait-apercu")
			.o::_div(_n)
			.o::div(_class, "mdl-card__actions mdl-card--border dilectio-extrait-actions", _n)
			.$button_action
			.o::_div(_n)
			.o::_div(_n);
		return $o;
	}
}