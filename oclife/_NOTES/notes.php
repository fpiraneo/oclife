<?php
/**
 * TODO list
 * - Nella finestra informazioni con movimento cursore su tag compare il numero e non la descrizione...
 * - Verificare calcolo della larghezza dell'editfield nell'inserimento delle tags
 * - Nelle tags mostrare anche i files; quelli non taggati devono comparire in 'root' (?)
 * 
 * - TRIGGERS
 * -- Su cancellazione del file togliere tutte le thumbnails ed i tag associati;
 * -- Su cancellazione dell'utente togliere tutte le thumbnails ed i tag associati.
 */

OCP\JSON::checkLoggedIn();
OCP\JSON::checkAppEnabled('oclife');

$view = \OC\Files\Filesystem::getView();

// User's home!
$user = OCP\User::getUser();
$myDir = \OC_User::getHome($user);

// On javascript: t('oclife', 'Informations') to localize textes
