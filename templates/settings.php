<?php
// Handle translations
$l = new \OC_L10N('oclife');
?>

<form id="oclife_settings">
        <fieldset class="personalblock">
                <h2>Tags</h2>
                <div>
                    <input type="checkbox" id="useImageMagick" name="useImageMagick" <?php p($_['useImageMagick']) ?> <?php p($_['enImageMagick']) ?>/>
                    <label for="useImageMagick"><?php p($l->t('Use ImageMagick instead of GD (More image formats handled)')) ?></label>
                </div>
                
                <div style="color:<?php p($_['imagickMessageColor']) ?>;"><?php p($_['imagickEnabled']) ?></div>
        </fieldset>
</form>
