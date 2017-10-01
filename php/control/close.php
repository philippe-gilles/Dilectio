<?php

/*
 * Dilectio : Fermeture de session
 */
 
require_once "php/init.php";

/* Contrôle de la session */
tool_session::ouvrir_et_verifier();

/* Fermeture */
tool_session::fermer(tool_session::SESSION_SORTIE);