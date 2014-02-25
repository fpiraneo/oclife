<?php
/*
 * Copyright 2014 by Francesco PIRANEO G. (fpiraneo@gmail.com)
 * 
 * This file is part of oclife.
 * 
 * oclife is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * oclife is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with oclife.  If not, see <http://www.gnu.org/licenses/>.
 */

// Highlight current menu item
OCP\App::setActiveNavigationEntry('oclife');

// Include what's needed by fancytree
\OCP\Util::addStyle('oclife', 'ui.fancytree');
\OCP\Util::addStyle('oclife', 'jquery.contextMenu');

\OCP\Util::addScript('oclife', 'fancytree/jquery.fancytree');
\OCP\Util::addScript('oclife', 'fancytree/jquery.fancytree.dnd');
\OCP\Util::addScript('oclife', 'fancytree/jquery.contextMenu-1.6.5');
\OCP\Util::addScript('oclife', 'fancytree/jquery.fancytree.contextMenu');

// THEN execute what needed by us...
\OCP\Util::addStyle('oclife', 'oclife');
\OCP\Util::addScript('oclife', 'oclife/oclife_tagstree');

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
