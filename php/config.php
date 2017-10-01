<?php

/*
 * Dilectus : Paramètres de configuration
 */

/*
 * Dossier d'installation de Dilectus dans le cas où ce dossier
 * ne serait pas directement installé sous la racine
 * IMPORTANT : synthaxe = "/xxx/yyy/zzz" (slash au début, pas à la fin)
 */
define("__DILECTIO_DOSSIER_INSTALLATION", "/dilectio");

/* 
 * Préfixe des tables dans la base de données 
 * Un caractère _ (underscore) sépare le préfixe et le nom de la table
 * Ex : préfixe = dilectus / table = profil => nom = dilectus_profil
 */
define("__DILECTIO_PREFIXE_DB", "dilectio");

/*
 * Identifiants du service de messagerie "no-reply" (SMTP)
 */
define("__DILECTIO_NO_REPLY_IDENTIFIER", "no-reply@dilect.io");
define("__DILECTIO_NO_REPLY_PASSWORD", "L<3veG33k70");

/*
 * Langue par défaut
 * Langue utilisée si une traduction n'est pas trouvée dans la langue recherchée
 */
define("__DILECTIO_LANGUE_DEFAUT", "fr");

/*
 * Thème par défaut
 */
define("__DILECTIO_THEME_DEFAUT", "default");

/*
 * Nombre max de posts chargés dans une période sur la page d'accueil
 * Non valable pour la période "aujourd'hui", valable pour la période
 * "récemment" ainsi que les suivantes
 */
 define("__DILECTIO_PERIOD_MAX_THUMBS", "30");

/*
 * Taille maximum autorisée à l'upload en octets
 * ATTENTION : Les paramètres PHP post_max_size et upload_max_filesize sont prioritaires
 */
define("__DILECTIO_UPLOAD_MAX_FILESIZE", "8388608"); // 8Mo

/*
 * Taille maximum pour une image dans le fil de la conversation
 */
define("__DILECTIO_IMAGE_ORIGINAL_MAX_WIDTH", "800");
define("__DILECTIO_IMAGE_ORIGINAL_MAX_HEIGHT", "600");
define("__DILECTIO_IMAGE_ORIGINAL_COMPRESSION", "90");

/* 
 * Taille fixe pour une image dans un extrait 
 */
define("__DILECTIO_IMAGE_THUMB_WIDTH", "400");
define("__DILECTIO_IMAGE_THUMB_HEIGHT", "120");
define("__DILECTIO_IMAGE_THUMB_COMPRESSION", "75");
