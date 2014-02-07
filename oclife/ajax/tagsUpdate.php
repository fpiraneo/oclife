<?php
OCP\JSON::checkLoggedIn();
OCP\JSON::checkAppEnabled('oclife');

$op = filter_input(INPUT_POST, 'op', FILTER_SANITIZE_STRING);
$tagID = filter_input(INPUT_POST, 'tag', FILTER_SANITIZE_NUMBER_INT);


die();