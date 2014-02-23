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

OCP\User::checkAdminUser();

OCP\Util::addscript('oclife', 'oclife/oclife_admin');

$onlyAdminCanEdit = intval(OCP\Config::getAppValue('oclife', 'onlyAdminCanEdit'));
$useImageMagick = intval(OCP\Config::getAppValue('oclife', 'useImageMagick'));

$tmpl = new \OCP\Template('oclife', 'settings');
$tmpl->assign('onlyAdminCanEdit', ($onlyAdminCanEdit === 1) ? 'CHECKED' : '');
$tmpl->assign('useImageMagick', ($useImageMagick === 1) ? 'CHECKED' : '');

$imagick = extension_loaded('imagick');
$imagickEnabled = $imagick ? "ImageMagick is loaded and ready to be used" : "ImageMagick is not loaded: Please refers to php manual.";
$tmpl->assign('imagickEnabled', $imagickEnabled);

$tmpl->assign('enImageMagick', $imagick ? '' : 'disabled="DISABLED"');
$tmpl->assign('imagickMessageColor', $imagick ? 'green' : 'red');

return $tmpl->fetchPage();