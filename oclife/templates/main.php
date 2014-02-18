<div id="notification" style="display:none;"></div>
<div class='oclife_toolbar'>
    <div>
        <span class="oclife_header">Actual tags</span>
    </div>

    <div style="text-align: right; padding-right: 10px;">
        <button id="expandAll">Expand all</button>
        <button id="collapseAll">Collapse all</button>        
    </div>
</div>


<div class="oclife_content" id="oclife_content">
    <div class="oclife_tagtree" id="tagstree">
    </div>
    
    <div class="oclife_filetable" id="fileTable">
        <p class="oclife_title">Associated files</p>
        <div id="oclife_fileList"></div>
        <div id="oclife_emptylist">Select one or more checkbox to view the files with the associated tags.</div>
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
        <div>
            Really delete the tag:<br />
            <div style="width: 100%; text-align: center; padding: 5px 0px 15px 0px; font-weight: bold;" id="tagToDelete">TagToDelete</div>
            <strong>NOTE:</strong> Also child tags will be removed!
        </div>
        <input type="hidden" name="deleteID" id="deleteID" value="-1" />
    </div>

    <div id="filePath" title="Where is this file?"> 
        <div>
            You can find this file here:<br />
            <div style="width: 100%; text-align: center; padding: 5px 0px 15px 0px; font-weight: bold;" id="pathInfo">filePath</div>
        </div>
    </div>
</div>