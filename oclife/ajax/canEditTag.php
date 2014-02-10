<?php
OCP\JSON::checkAppEnabled('oclife');
OCP\User::checkLoggedIn();

$onlyAdminCanEdit = intval(OCP\Config::getAppValue('oclife', 'onlyAdminCanEdit'));

$isAdmin = OC_User::isAdminUser(OC_User::getUser());

$canEdit = $isAdmin || !$onlyAdminCanEdit;

echo $canEdit;