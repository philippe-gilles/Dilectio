<?php

/**
 * Classe de gestion des sessions
 */
 
class tool_session {
	const SESSION_PREFIXE = "dilectio_";
	const SESSION_TIMEOUT = 14400;
	const SESSION_SORTIE = "./";

	public static function demarrer() {
		$session_id = self::ouvrir();
		$checksum_id = self::checksum_sessid($session_id);
		self::ecrire_param("log", $checksum_id);
		self::ecrire_param("time", time());
		return (strlen($session_id) > 0);
	}

	public static function ouvrir() {
		session_start();
		return session_id();
	}

	public static function fermer($url_fermeture = null) {
		$id = session_id();
		if (strlen($id) > 0) {
			$_SESSION = array();
			session_destroy();
		}
		if (strlen($url_fermeture) > 0) {
			if (file_exists($url_fermeture)) {
				header("Location: ".$url_fermeture);
				die();
			}
		}
	}

	public static function ouvrir_et_verifier() {
		self::ouvrir();
		/* Gros hack car sur certains mobiles il y a un souci de délai     */
		/* On laisse quand même tomber au bout de 100 * 50000 = 5 secondes */
		$cpt = 0;
		$waiting_for = (self::SESSION_PREFIXE)."log";
		while ((!(isset($_SESSION[$waiting_for]))) && ($cpt < 100)) {
			usleep(50000);
			$cpt += 1;
		}
		$session_ok = self::verifier();
		if (!($session_ok)) {
			self::fermer(self::SESSION_SORTIE);
		}
	}

	public static function verifier($regenerate = true) {
		$ret = false;
		$sess_id = self::lire_param("log");
		$checksum_id = self::checksum_sessid(session_id());
		if (!(strcmp($checksum_id, $sess_id))) {
			// Vérification du timeout
			$sess_time = self::lire_param("time");
			$sess_lifetime = time() - $sess_time;
			if ($sess_lifetime <= self::SESSION_TIMEOUT) {
				if ($regenerate) {
					// On réarme le timeout
					self::ecrire_param("time", time());
					// Par sécurité on regénère l'identifiant de session (si header encore vide)
					$ret = @headers_sent();
					if (!($ret)) {
						$ret = @session_regenerate_id();
						if ($ret) {
							self::ecrire_param("log", self::checksum_sessid(session_id()));
						}
					}
				}
				else {
					$ret = true;
				}
			}
		}
		return $ret;
	}

	public static function lire_param($nom) {
		$nom_avec_prefixe = (self::SESSION_PREFIXE).$nom;
		if (strlen($nom) == 0) {$ret = null;}
		else {
			if (isset($_SESSION[$nom_avec_prefixe])) {
				$ret = $_SESSION[$nom_avec_prefixe];
				if (strlen($ret) == 0) {$ret = null;}
				else {$ret = str_replace("\0", '', $ret);}
			}
			else {$ret = null;}
		}
		return $ret;
	}

	public static function ecrire_param($nom, $valeur) {
		$nom_avec_prefixe = (self::SESSION_PREFIXE).$nom;
		if (strlen($nom) == 0) {$ret = null;}
		else {
			$_SESSION[$nom_avec_prefixe] = $valeur;
			$ret = $valeur;
		}
		return $ret;
	}

	public static function supprimer_param($nom) {
		$nom_avec_prefixe = (self::SESSION_PREFIXE).$nom;
		if (strlen($nom) > 0) {unset($_SESSION[$nom_avec_prefixe]);}
	}

	public static function checksum_sessid($id) {
		$ret = (int) 0;
		if (!(is_null($id))) {
			for ($cpt = 0; $cpt < strlen($id); $cpt++) {
				$ret += (ord(substr($id, $cpt, 1))-32);
			}
		}
		return $ret;
	}
}