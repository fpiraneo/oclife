<?php
OCP\User::checkAdminUser();

OCP\Util::addscript('oclife', 'admin');

$onlyAdminCanEdit = intval(OCP\Config::getAppValue('oclife', 'onlyAdminCanEdit'));

$tmpl = new OCP\Template('oclife', 'settings');
$tmpl->assign('onlyAdminCanEdit', ($onlyAdminCanEdit === 1) ? 'CHECKED' : '');

return $tmpl->fetchPage();