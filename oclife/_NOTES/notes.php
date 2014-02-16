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

/*
checkbox: true,

activate: function(event, data) {
deactivate: function(event, data) {
click: function(event, data) {

select: function(event, data) {
        logEvent(event, data, "current state=" + data.node.isSelected());
        var s = data.tree.getSelectedNodes().join(", ");
        $("#echoSelected").text(s);
      }

http://layout.jquery-dev.net/
http://www.instantshift.com/2013/08/19/jquery-layout-and-ui-plugins/
http://tutorialzine.com/2013/04/50-amazing-jquery-plugins/
 */