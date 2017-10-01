<?php

/*
 * Dilectio : Script AJAX pour thread
 */

/* Contrôle de la session (à remonter dans le router ?) */
tool_session::ouvrir();
$session_ok = tool_session::verifier(false);
if (!($session_ok)) {
	echo json_encode(array("valide" => false));
	die();
}

/* Récupération du profil */
$user_id = tool_session::lire_param("profil_id");

/* Récupération de la langue */
$langue = tool_session::lire_param("lang");
lang_i18n::init($langue);

/* Contrôle des paramètres post_id et question_id */
$post_id = (int) tool_post::Post("post-id");
$question_id = (int) tool_post::Post("question-id");

/* Constitution du HTML */
$valide = false;
$answer_id = 0;
$html = "";
if (($question_id > 0) && ($post_id > 0)) {
	db::open(__DILECTIO_PREFIXE_DB);
	$now = db::now();
	$post = db_post::get($post_id);
	if (!(is_null($post))) {
		$post->modifier_profile_id = $user_id;
		$post->modification = $now;
		db::store($post);
	}
	$question = db_post_question::get($question_id);
	if (!(is_null($question))) {
		$answer = db_post_answer::instance();
		$answer->question_id = $question_id;
		$answer->profile_id = $user_id;
		$answer->date = $now;
		$answer->answer_1 = isset($_POST["answer-1"]);
		$answer->answer_2 = isset($_POST["answer-2"]);
		$answer->answer_3 = isset($_POST["answer-3"]);
		$answer->answer_4 = isset($_POST["answer-4"]);
		$answer->answer_5 = isset($_POST["answer-5"]);
		$answer->answer_6 = isset($_POST["answer-6"]);
		$answer->answer_7 = isset($_POST["answer-7"]);
		$answer->answer_8 = isset($_POST["answer-8"]);
		$answer->answer_9 = isset($_POST["answer-9"]);
		$answer->answer_10 = isset($_POST["answer-10"]);
		$caption = tool_post::Post("caption");
		if (strlen($caption) > 0) {$answer->caption = $caption;}
		$answer_id = db::store($answer);
		$html = type_question::display_answer($langue, $question, $answer);
		tool_notification::insert_post("answered", $post_id);
		$valide = true;
	}
	db::close();
}

echo json_encode(array("valide" => $valide, "answer_id" => $answer_id, "html" => $html));

