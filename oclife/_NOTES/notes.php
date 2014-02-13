<?php
/**
 * TODO list
 * - Nella finestra informazioni con movimento cursore su tag compare il numero e non la descrizione...
 * - Verificare calcolo della larghezza dell'editfield nell'inserimento delle tags
 * - Nelle tags mostrare anche i files; quelli non taggati devono comparire in 'root' (?)
 */

OCP\JSON::checkLoggedIn();
OCP\JSON::checkAppEnabled('oclife');

$view = \OC\Files\Filesystem::getView();

// User's home!
$user = OCP\User::getUser();
$myDir = \OC_User::getHome($user);

// On javascript: t('oclife', 'Informations') to localize textes

/**
* Send json error msg
*
* Return a json error message with optional extra data for
* error message or app specific data.
*
* Example use:
*
*     $id = [some value]
*     OCP\JSON::error(array('data':array('message':'An error happened', 'id': $id)));
*
* Will return the json formatted string:
*
*     {"status":"error","data":{"message":"An error happened", "id":[some value]}}
*
* @param array $data The data to use
* @return string json formatted error string.
*/
