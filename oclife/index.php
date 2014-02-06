<?php

// Highlight current menu item
OCP\App::setActiveNavigationEntry('oclife');

// Include what's needed by fancytree
\OCP\Util::addStyle('oclife', 'ui.fancytree');
\OCP\Util::addStyle('oclife', 'jquery.contextMenu');

\OCP\Util::addScript('oclife', 'jquery.fancytree');
\OCP\Util::addScript('oclife', 'jquery.fancytree.dnd');
\OCP\Util::addScript('oclife', 'jquery.contextMenu-1.6.5');
\OCP\Util::addScript('oclife', 'jquery.fancytree.contextMenu');
\OCP\Util::addScript('oclife', 'jquery.fancytree.edit');

// THEN execute what needed by us...
\OCP\Util::addStyle('oclife', 'oclife');
\OCP\Util::addScript('oclife', 'tagstree');

// Look up other security checks in the docs!
\OCP\User::checkLoggedIn();
\OCP\App::checkAppEnabled('oclife');

$tpl = new OCP\Template("oclife", "main", "user");

$onlyAdminCanEdit = intval(OCP\Config::getAppValue('oclife', 'onlyAdminCanEdit'));
$isAdmin = OC_User::isAdminUser(OC_User::getUser());

if(!$onlyAdminCanEdit || $isAdmin) {
    $tpl->assign('canEdit', '1');
} else {
    $tpl->assign('canEdit', '0');
}

$tpl->printPage();
