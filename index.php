<?php

/* Forçage UTF-8 */
ini_set("default_charset", "utf-8");
header("X-Robots-Tag: noindex, nofollow", true);

/* Chargement des paramètres de configuration */
require_once "php/config.php";

/* Appel du routeur */
require_once "third/altorouter/third_altorouter.php";
$router = new AltoRouter();
$router->setBasePath(__DILECTIO_DOSSIER_INSTALLATION);

/* Routes GET **************************************************/

/* Login */
$router->map("GET", "/", function() {
	router::load("control_page_login");
});

/* DEBUG */
$router->map("GET", "/debug", function() {require "debug.php";});
$router->map("POST", "/debug", function() {require "debug.php";});

/* Logout */
$router->map("GET", "/close", function() {require "php/control/close.php";});

/* Images */
$router->map("GET", "/photo/original/[i:profile_id]/[i:photo_id]", function($profile_id, $photo_id) {
	router::load_image("control_image_photo_original", $profile_id, $photo_id);
});
$router->map("GET", "/photo/thumb/[i:profile_id]/[i:photo_id]", function($profile_id, $photo_id) {
	router::load_image("control_image_photo_thumb", $profile_id, $photo_id);
});
$router->map("GET", "/link/original/[i:profile_id]/[i:link_id]", function($profile_id, $link_id) {
	router::load_image("control_image_link_original", $profile_id, $link_id);
});
$router->map("GET", "/link/thumb/[i:profile_id]/[i:link_id]", function($profile_id, $link_id) {
	router::load_image("control_image_link_thumb", $profile_id, $link_id);
});

/* Thread */
$router->map("GET", "/thread-[i:id]_[i:fragment]", function($id, $fragment) {
	router::load_page("control_page_thread", $id, $fragment);
});
$router->map("GET", "/thread-[i:id]", function($id) {
	router::load_page("control_page_thread", $id);
});

/* Autres */
$router->map("GET", "/[a:page]", function($nom_page) {
	$classe_page = "control_page_".$nom_page;
	router::load_page($classe_page);
});

/* Routes POST **************************************************/

/* Ajax */
$router->map("POST", "/ajax/[a:page]/[a:script]", function($page, $script) {
	router::load_ajax($page, $script);
});

/* Fin routes **************************************************/

/* Matching de la route */
$match = $router->match();

/* Traitement du matching */
if (($match) && (is_callable($match["target"]))) {
	call_user_func_array($match["target"], $match["params"]); 
}
else {
	echo "<pre>";print_r($_SERVER);echo "</pre>\n";
	die("404 Not Found");
}