<!--
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
-->


<form id="oclife_settings">
        <fieldset class="personalblock">
                <h2>Tags</h2>
                <div>
                    <input type="checkbox" id="onlyAdminCanEdit" name="onlyAdminCanEdit" <?php p($_['onlyAdminCanEdit']) ?> />
                    <label for="onlyAdminCanEdit">Only admins can edit tags</label>
                </div>

                <div>
                    <input type="checkbox" id="useImageMagick" name="useImageMagick" <?php p($_['useImageMagick']) ?> <?php p($_['enImageMagick']) ?>/>
                    <label for="useImageMagick">Use ImageMagick instead of GD (More image formats handled)</label>
                </div>
                
                <div style="color:<?php p($_['imagickMessageColor']) ?>;"><?php p($_['imagickEnabled']) ?></div>
        </fieldset>
</form>
