<?php
OCP\JSON::checkLoggedIn();
OCP\JSON::checkAppEnabled('oclife');

$ctags = new OCA\OCLife\hTags();
$tagData = $ctags->getTagTree('xx');
$jsonTagData = json_encode($tagData);
echo $jsonTagData;