<?php

OCP\User::checkAdminUser();
OCP\JSON::callCheck();

$onlyAdminCanEdit = filter_input(INPUT_POST, 'onlyAdminCanEdit', FILTER_SANITIZE_STRING);

$result = OCP\Config::setAppValue('oclife', 'onlyAdminCanEdit', ($onlyAdminCanEdit === 'on') ? 1 : 0);

echo $result ? 'OK' : 'KO';