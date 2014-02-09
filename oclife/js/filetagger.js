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
                                            
                                            // Prepare token field
                                            var getTagFlat = OC.filePath('oclife', 'ajax', 'getTagFlat.php');
                                            
                                            $('#oclife_tags').tokenfield({
                                              autocomplete: {
                                                source:  function(request, response) {
                                                    $.ajax({
                                                            url: getTagFlat,

                                                            data: {
                                                                term: request.term
                                                            },

                                                            success: function(data) {
                                                                var returnString = data;
                                                                var jsonResult = jQuery.parseJSON(returnString);
                                                                response(jsonResult);
                                                            },
                                                            
                                                            error: function (xhr, status) {
                                                                window.alert("Unable to get tags! Ajax error!");
                                                            }
                                                        })
                                                    },
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
                                                            fileID: fileID,
                                                            tag: e.token.value.toString()
                                                        },

                                                        error: function (xhr, status) {
                                                            window.alert('Unable to add the tag! Ajax error.');
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
                                                            fileID: fileID,
                                                            tag: e.token.value.toString()
                                                        },

                                                        success: function(result) {
                                                            if(result === "0") {
                                                                window.alert('Unable to remove the tag! Data base error.');
                                                            }
                                                        },

                                                        error: function (xhr, status) {
                                                            window.alert('Unable to remove the tag! Ajax error.');
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
                                        id: fileID,
                                        etag: etag
                                    },

                                    success: function(result) {
                                        $('#oclife_tags').tokenfield('setTokens', JSON.parse(result));
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
                $('#oclife_tags').off('afterCreateToken');
                $('#oclife_tags').off('removeToken');
            }
        });
});
