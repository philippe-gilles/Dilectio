<?php

// Declaration de l'interface dilectio_interface
interface dilectio_interface {
	const TYPE_MESSAGE_SILENT = "silent";
	const TYPE_MESSAGE_TOAST = "toast";
	const TYPE_MESSAGE_ALERT = "alert";

	public static function head($excerpt_only = false);
	public static function body($excerpt_only = false);
    public static function excerpt($profil_id, $post_id, $type_post_id);
    public static function post($profil_id, $post_id, $type_post_id);
    public static function form($langue, $thread_id, $type_id);
	public static function submit($profil_id, &$type_message, &$message);
}