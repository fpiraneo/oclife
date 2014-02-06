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
<fieldset class="oclife_tagsbox" id="oclife_tags_container"><legend>Tags</legend>\n\
<input type="text" class="form-control" id="oclife_tags" placeholder="Enter tags here" />\n\
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
                                                                                        
                                            $('#oclife_tags').tokenfield({
                                              autocomplete: {
                                                source: getTagFlat,
                                                minLength: 2,
                                                delay: 200                                                
                                              },
                                              showAutocompleteOnFocus: false
                                            });
                                            
                                            $('#oclife_tags').on('afterCreateToken', 
                                                function (e) {
                                                    var updateTags = OC.filePath('oclife', 'ajax', 'tagsUpdate.php');
                                                    
                                                    $.ajax({
                                                        url: updateTags,
                                                        async: false,
                                                        timeout: 2000,

                                                        data: {
                                                            op: 'add',
                                                            tag: e.token.value.toString()
                                                        },

                                                        type: "POST"});
                                                }
                                            );
                                    
                                            $('#oclife_tags').on('removeToken', 
                                                function (e) {
                                                    var updateTags = OC.filePath('oclife', 'ajax', 'tagsUpdate.php');
                                                    
                                                    $.ajax({
                                                        url: updateTags,
                                                        async: false,
                                                        timeout: 2000,

                                                        data: {
                                                            op: 'remove',
                                                            tag: e.token.value.toString()
                                                        },

                                                        type: "POST"});
                                                }
                                            );
                                                
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

                                $.ajax({
                                    url: OC.filePath('oclife', 'ajax', 'getTagsForFile.php'),
                                    async: false,
                                    timeout: 2000,

                                    data: {
                                    },

                                    success: function(result) {
                                        $('#oclife_tags').tokenfield('setTokens', result);
                                    },
                                    
                                    error: function (xhr, status) {
                                        window.alert('Unable to get actual tags for this document!')
                                    },
                                    
                                    type: "POST"});
                            
                                $('#oclife_infoData').append(infoContent);
                                
                                $('#oclife_infos').dialog("open");
			}
		);
	}

        $( "#oclife_infos" ).dialog({
            autoOpen: false,
            width: 640,
            height: 480,
            modal: true,
            
            close: function() {
                $('.token').remove();
            }
        });
});
