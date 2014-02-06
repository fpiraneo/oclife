<div class='oclife_toolbar'>
    <div>
        <span class="oclife_header">Actual tags</span>
    </div>

    <div style="text-align: right; padding-right: 10px;">
        <button id="expandAll">Expand all</button>
        <button id="collapseAll">Collapse all</button>        
    </div>
</div>


<div class="oclife_content">
    <div id="tagstree">
    </div>

    <div id="renameTag" title="Rename tag">
        <form>
            <fieldset>
                <p class="validateTips">Rename the tag and confirm.</p>
                <input type="text" name="tagName" style="width: 300px;" id="tagName" class="text ui-widget-content ui-corner-all" />
                <input type="hidden" name="tagID" id="tagID" value="" />
            </fieldset>
        </form>
    </div>
    
    <div id="createTag" title="Create a new tag">
        <form>
            <fieldset>
                <p class="validateTips">Insert the new tag and confirm.</p>
                <input type="text" name="newTagName" style="width: 300px;" id="newTagName" class="text ui-widget-content ui-corner-all" />
                <input type="hidden" name="parentID" id="parentID" value="-1" />
            </fieldset>
        </form>
    </div>
    
    <div id="deleteConfirm" title="Delete tag">
        <p>
            Really delete the tag:<br />
            <div style="width: 100%; text-align: center; padding: 5px 0px 15px 0px; font-weight: bold;" id="tagToDelete">TagToDelete</div>
            <strong>NOTE:</strong> Also child tags will be removed!
        </p>
        <input type="hidden" name="deleteID" id="deleteID" value="-1" />
    </div>
    
    <div id="onlyAdmin" title="Permission denied!">
        <p>
            <strong>SORRY!</strong> Only an administrator can edit tags! Ask your administrator to do what you think!
        </p>
    </div>    
    
    <input type="hidden" name="canEdit" id="canEdit" value="<?php p($_['canEdit']); ?>" />
</div>


<div class="oclife_statusbar" id="oclife_status">
    Ready!
</div>