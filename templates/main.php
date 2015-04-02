<div id="notification" style="display:none;"></div>

<div class='oclife_toolbar'>
    <span class="oclife_header">
        <?php p($l->t('Actual tags')) ?>
    </span>

    <button id="btnNew"><?php p($l->t('New')) ?></button>
    <button id="btnRename"><?php p($l->t('Rename')) ?></button>
    <button id="btnDelete"><?php p($l->t('Delete')) ?></button>
    <button id="btnExpandAll"><?php p($l->t('Expand all')) ?></button>
    <button id="btnCollapseAll"><?php p($l->t('Collapse all')) ?></button>
    
    <?php p($l->t('Owner:')) ?>
    <select name="menuOwnName" id="menuOwnName" disabled="true">
        <option value='' disabled='disabled' selected='selected'><?php p($l->t('Not set')) ?></option>
        
        <?php
            $usersList = \OCA\OCLife\utilities::getUsers(NULL, TRUE);
            foreach($usersList as $uid => $userName) {
                printf("<option value='%s'>%s</option>'", $uid, is_null($userName) ? $uid : $userName);
            }
        ?>        
    </select>

    <select name="menuOwnPriv" id="menuOwnPriv" disabled="true">
        <option value="OwnRO"><?php p($l->t('Read only')) ?></option>
        <option value="OwnRW"><?php p($l->t('Can modify')) ?></option>
    </select>

    <?php p($l->t('Group:')) ?>
    <select name="menuGrpPriv" id="menuGrpPriv" disabled="true">
        <option value="GrpNO"><?php p($l->t('None')) ?></option>
        <option value="GrpRO"><?php p($l->t('Read only')) ?></option>
        <option value="GrpRW"><?php p($l->t('Can modify')) ?></option>
    </select>

    <?php p($l->t('All:')) ?>
    <select name="menuAllPriv" id="menuAllPriv" disabled="true">
        <option value="AllNO"><?php p($l->t('None')) ?></option>
        <option value="AllRO"><?php p($l->t('Read only')) ?></option>
        <option value="AllRW"><?php p($l->t('Can modify')) ?></option>
    </select>
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

<div id="imagePreview" title="<?php p($l->t('Image preview')) ?>">
    <div>
        <img id="previewArea" src="" />
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
