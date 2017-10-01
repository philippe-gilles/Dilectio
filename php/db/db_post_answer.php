<?php

class db_post_answer extends db_table {
	public static function to_question_by($question_id, $profile_id) {
		$answer = db::findOne(db::table("post_answer"), " question_id = :question_id AND profile_id = :profile_id ", array(":question_id" => $question_id, ":profile_id" => $profile_id));
		return $answer;
	}
	
	public static function to_question_by_not($question_id, $profile_id) {
		$answers = db::find(db::table("post_answer"), " question_id = :question_id AND profile_id <> :profile_id ORDER BY date ", array(":question_id" => $question_id, ":profile_id" => $profile_id));
		return $answers;
	}
}