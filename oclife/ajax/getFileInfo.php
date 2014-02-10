<?php
// Check if app enabled and user logged in
OCP\JSON::checkAppEnabled('oclife');
OCP\User::checkLoggedIn();

// Revert parameters from ajax
$fileID = intval(filter_input(INPUT_POST, 'fileID', FILTER_SANITIZE_NUMBER_INT));

// Begin to collect files informations
$filePath = \OC\Files\Filesystem::getPath($fileID);

/*
 *  $fileInfos contains:
 * Array ( [fileid] => 30 
 * [storage] => home::qsecofr 
 * [path] => files/Immagini/HungryIla.png 
 * [parent] => 18 
 * [name] => HungryIla.png 
 * [mimetype] => image/png 
 * [mimepart] => image 
 * [size] => 3981786 
 * [mtime] => 1388521137 
 * [storage_mtime] => 1388521137 
 * [encrypted] => 1 
 * [unencrypted_size] => 3981786 
 * [etag] => 52c326b169ba4
 * [permissions] => 27 ) 
 */
$fileInfos = \OC\Files\Filesystem::getFileInfo($filePath);

$thumbPath = OCP\Util::linkToAbsolute('oclife', 'getThumbnail.php', array('fileid' => $fileInfos['fileid']));
$preview = '<img style="border: 1px solid black; display: block;" src="' . $thumbPath . '" />';

$infos = array();
$infos[] = '<strong>File name: </strong>' . $fileInfos['name'];
$infos[] = '<strong>MIME: </strong>' . $fileInfos['mimetype'];
$infos[] = '<strong>Size: </strong>' . \OCA\OCLife\utilities::formatBytes($fileInfos['size'], 2, TRUE);
$infos[] = '<strong>When added: </strong>' . \OCP\Util::formatDate($fileInfos['storage_mtime']);
$infos[] = '<strong>Encrypted? </strong>' . (($fileInfos['encrypted'] === TRUE) ? 'Yes' : 'No');

if($fileInfos['encrypted']) {
    $infos[] = '<strong>Unencrypted size: </strong>' . \OCA\OCLife\utilities::formatBytes($fileInfos['unencrypted_size'], 2, TRUE);
}

// Output the result!
$htmlInfos = '';
foreach($infos as $row) {
    $htmlInfos .= $row . '<br />';
}

print <<<END1
<table>
<tr>

<td>
$preview
</td>

<td style="vertical-align: top;">
<div style="margin: 10px;">
$htmlInfos
</div>
</td>

</tr>
</table>
END1;
