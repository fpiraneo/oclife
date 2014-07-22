<?php
// Handle translations
$l = new \OC_L10N('oclife');
?>

<div id="notification" style="display:none;"></div>

<div class='oclife_toolbar'>
    <div>
        <span class="oclife_header"><?php p($l->t('Actual tags')) ?></span>
    </div>

    <div style="text-align: right; padding-right: 10px;">
        <button id="expandAll"><?php p($l->t('Expand all')) ?></button>
        <button id="collapseAll"><?php p($l->t('Collapse all')) ?></button>        
    </div>
</div>

<div id="renameTag" title="<?php p($l->t('Rename tag')) ?>">
    <form>
        <fieldset>
            <p class="validateTips"><?php p($l->t('Rename the tag and confirm.')) ?></p>
            <input type="text" name="tagName" style="width: 300px;" id="tagName" class="text ui-widget-content ui-corner-all" />
            <input type="hidden" name="tagID" id="tagID" value="" />
        </fieldset>
    </form>
</div>

<div id="createTag" title="<?php p($l->t('Create a new tag')) ?>">
    <form>
        <fieldset>
            <p class="validateTips"><?php p($l->t('Insert the new tag and confirm.')) ?></p>
            <input type="text" name="newTagName" style="width: 300px;" id="newTagName" class="text ui-widget-content ui-corner-all" />
            <input type="hidden" name="parentID" id="parentID" value="-1" />
        </fieldset>
    </form>
</div>

<div id="deleteConfirm" title="<?php p($l->t('Delete tag')) ?>"> 
    <div>
        <?php p($l->t('Really delete the tag:')) ?><br />
        <div style="width: 100%; text-align: center; padding: 5px 0px 15px 0px; font-weight: bold;" id="tagToDelete">TagToDelete</div>
        <strong><?php p($l->t('NOTE: ')) ?></strong><?php p($l->t('Also child tags will be removed!')) ?>
    </div>
    <input type="hidden" name="deleteID" id="deleteID" value="-1" />
</div>

<div id="filePath" title="<?php p($l->t('Where is this file?')) ?>"> 
    <div>
        <?php p($l->t('You can find this file here:')) ?><br />
        <div style="width: 100%; text-align: center; padding: 5px 0px 15px 0px; font-weight: bold;" id="pathInfo">filePath</div>
    </div>
</div>

<div data-layout='{"type": "border", "hgap": 5, "vgap": 3}' class="oclife_content" id="oclife_content">
    <div class="west" id="tagscontainer">
        <div class="oclife_tagtree" id="tagstree">
        </div>
    </div>
    
    <div class="center" id="fileTable">
        <p class="oclife_title"><?php p($l->t('Associated files')) ?></p>
        <div id="oclife_fileList"></div>
        <div id="oclife_emptylist"><?php p($l->t('Select one or more tags to view the associated files.')) ?></div>
    </div>
</div>
