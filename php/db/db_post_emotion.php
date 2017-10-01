<?php

class db_post_emotion extends db_table {
	public static function post_emotionned_by($post_id, $profile_id) {
		$ret = null;
		$post_emotion = db::findOne(db::table("post_emotion"), " post_id = :post_id AND profile_id = :profile_id ", array(":post_id" => $post_id, ":profile_id" => $profile_id));
		if (!(is_null($post_emotion))) {
			$emotion_id = (int) $post_emotion->emotion_id;
			if ($emotion_id > 0) {$ret = db_emotion::get($emotion_id);}
		}
		return $ret;
	}
	
	public static function post_emotionned_by_not($post_id, $profile_id) {
		$ret = array();
		$post_emotions = db::find(db::table("post_emotion"), " post_id = :post_id AND profile_id <> :profile_id ", array(":post_id" => $post_id, ":profile_id" => $profile_id));
		foreach($post_emotions as $post_emotion) {
			$profile_id = (int) $post_emotion->profile_id;
			$emotion_id = (int) $post_emotion->emotion_id;
			if (($emotion_id > 0) && ($profile_id > 0)) {
				$emotion = db_emotion::get($emotion_id);
				$profile = db_profile::get($profile_id);
				if ((!(is_null($emotion))) && (!(is_null($profile)))) {
					$nom_profil = $profile->alias;
					if (strlen($nom_profil) > 0) {$ret[$nom_profil] = $emotion;}
				}
			}
		}
		return $ret;
	}
	
	public static function emotionned($post_id, $emotion_id) {
		$ret_id = false;
		$user_id = tool_session::lire_param("profil_id");
		$emotionned = db::findOne(db::table("post_emotion"), " post_id = :post_id AND profile_id = :profile_id ", array(":post_id" => $post_id, ":profile_id" => $user_id));
		if (!(is_null($emotionned))) {
			$emotionned->emotion_id = $emotion_id;
		}
		else {
			$emotionned = self::instance();
			$emotionned->post_id = $post_id;
			$emotionned->profile_id = $user_id;
			$emotionned->emotion_id = $emotion_id;
		}
		$ret_id = db::store($emotionned);
		return $ret_id;
	}

	public static function unemotionned($post_id) {
		$ret = false;
		$user_id = tool_session::lire_param("profil_id");
		$emotionned = db::findOne(db::table("post_emotion"), " post_id = :post_id AND profile_id = :profile_id ", array(":post_id" => $post_id, ":profile_id" => $user_id));
		if (!(is_null($emotionned))) {
			db::trash($emotionned);
			$ret = true;
		}
		return $ret;
	}

}