$(document).ready(function(){

	if ($('#isPublic').val()){
		// no versions actions in public mode
		// beware of https://github.com/owncloud/core/issues/4545
		// as enabling this might hang Chrome
		return;
	}

        // This is the div where informations will appears
        $('#content').append('<div id="oclife_infos" title="Informations">\n\
    <div id="oclife_infoData"></div>\n\
<fieldset style="border: 1px solid darkgray; padding-left: 10px;"><legend>Tags</legend>\n\
<input type="text" class="tm-input" name="tags" placeholder="Add tags here" />\n\
</fieldset>\n\
</div>');

	if (typeof FileActions !== 'undefined') {
		// Add tags button to 'files/index.php'
		FileActions.register('file', 'Informations', OC.PERMISSION_UPDATE,
			function() {
				// Specify icon for informations button
				return OC.imagePath('oclife','icon_info');
			},
			function(fileName) {
				// Action to perform when clicked
				if(scanFiles.scanning) { return; } // Workaround to prevent additional http request block scanning feedback
                                var tr = $('tr').filterAttr('data-file', fileName);
                                var fileID = $(tr).data('id');
                                var etag = $(tr).data('etag');
                                
                                var dataPath = OC.filePath('oclife', 'ajax', 'getFileInfo.php');
                                var updateTags = OC.filePath('oclife', 'ajax', 'tagsUpdate.php');
                                var getTagFlat = OC.filePath('oclife', 'ajax', 'getTagFlat.php');

                                $.ajax({
                                    url: dataPath,
                                    async: false,
                                    timeout: 2000,

                                    data: {
                                        fileID: fileID,
                                        etag: etag
                                    },

                                    type: "POST",

                                    success: function( result ) {
                                        if(result !== 'KO') {
                                            infoContent = result;
                                                                                        
                                            jQuery(".tm-input").tagsManager({
                                                tagClass: '',
                                                backspace: [],
                                                AjaxPush: updateTags,
                                                AjaxPushAllTags: true,
                                                AjaxPushParameters: { 'authToken': 'foobar' }
                                            });
                                            
                                            jQuery(".tm-input").typeahead({
                                                name: 'tag',
                                                prefetch: getTagFlat
                                                }).on('typeahead:selected', function (e, d) {
                                                    tagApi.tagsManager("pushTag", d.value);
                                                });
                                                
                                        } else {
                                            infoContent = "Unable to retrieve informations on this file!";
                                        }
                                    },

                                    error: function( xhr, status ) {
                                        infoContent = "Unable to retrieve informations on this file! Ajax error!"
                                    }                            
                                });                                
                                
                                var dialogTitle = "Informations on \"" + fileName + "\"";
                                $('#oclife_infos').dialog( "option", "title", dialogTitle );
                                $('#oclife_infoData').empty();
                                $('#oclife_infoData').append(infoContent);
                                jQuery(".tm-input").tagsManager('empty');
                                $('#oclife_infos').dialog("open");
			}
		);
	}

        $( "#oclife_infos" ).dialog({
            autoOpen: false,
            width: 640,
            height: 480,
            modal: true,
            buttons: {
            }
        });
});
