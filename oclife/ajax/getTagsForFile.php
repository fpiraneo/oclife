<?php
OCP\JSON::checkLoggedIn();
OCP\JSON::checkAppEnabled('oclife');

$fileID = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
$etag = filter_input(INPUT_POST, 'etag', FILTER_SANITIZE_STRING);

$result = array();

$jsonTagData = json_encode((array) $result);
echo $jsonTagData;