<?php
/**
 * TODO list
 * - Nella finestra informazioni con movimento cursore su tag compare il numero e non la descrizione...
 * - Verificare calcolo della larghezza dell'editfield nell'inserimento delle tags
 * - Se non viene creata la thumbnail nelle extended infos mettere una icona sostitutiva!
 * - Mettere l'icona del globo per le tags globali (ora tutte...)
 * 
 * DEVI INSTALLARE IMAGEMAGICK!!!
 */

OCP\JSON::checkLoggedIn();
OCP\JSON::checkAppEnabled('oclife');

$view = \OC\Files\Filesystem::getView();

// User's home!
$user = OCP\User::getUser();
$myDir = \OC_User::getHome($user);

// On javascript: t('oclife', 'Informations') to localize textes

/*
http://layout.jquery-dev.net/
http://www.instantshift.com/2013/08/19/jquery-layout-and-ui-plugins/
http://tutorialzine.com/2013/04/50-amazing-jquery-plugins/
 */