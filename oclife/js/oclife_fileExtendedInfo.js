$(document).ready(function(){

    // no versions actions in public mode
    // beware of https://github.com/owncloud/core/issues/4545
    // as enabling this might hang Chrome
    if($('#isPublic').val()){
            return;
    }

    // Add tags button to 'files/index.php'
    if(typeof FileActions !== 'undefined') {
            var infoIconPath = OC.imagePath('oclife','icon_info');
            FileActions.register('file', 'Informations', OC.PERMISSION_UPDATE, infoIconPath, function(fileName) {
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
                                var tagID = e.token.value;
                                var tagLabel = e.token.label;
                                var newTag = (tagID.toString() === tagLabel);

                                if(newTag) {                                                        
                                    var createNew = false;

                                    if(canEditTag === 1) {
                                        createNew = window.confirm('The tag "' + e.token.label + '" doesn\'t exist; would you like to create a new tag?');                                                        
                                    }

                                    if(!createNew) {
                                        $(e.relatedTarget).addClass('invalid');
                                    } else {

                                        $.ajax({
                                            url: OC.filePath('oclife', 'ajax', 'createTag.php'),
                                            async: false,
                                            timeout: 2000,

                                            data: {
                                                parentID: -1,
                                                tagName: tagLabel,
                                                tagLang: "xx"
                                            },

                                            type: "POST",

                                            success: function(result) {
                                                var resArray = result.split("-");
                                                if(resArray[0] === 'OK') {
                                                    tagID = parseInt(resArray[1]);

                                                    newTag = false;                                                    
                                                } else {
                                                    window.alert('Unable to create the tag! Ajax error.');
                                                }
                                            },

                                            error: function(xhr, status) {
                                                window.alert('Unable to create the tag! Ajax error.');
                                                $(e.relatedTarget).addClass('invalid');
                                            }
                                        });                        

                                    }
                                }

                                if(!newTag) {
                                    $.ajax({
                                        url: OC.filePath('oclife', 'ajax', 'tagsUpdate.php'),
                                        async: false,
                                        timeout: 1000,

                                        data: {
                                            op: 'add',
                                            fileID: fileID,
                                            tagID: tagID,
                                            tagName: tagLabel
                                        },

                                        error: function (xhr, status) {
                                            window.alert('Unable to add the tag! Ajax error.');
                                            $(e.relatedTarget).addClass('invalid');
                                        },

                                        type: "POST"});
                                }                                                    
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
        });
    }

    // Check if we can create / edit tags
    var canEditTag = 0;

    $.ajax({
        url: OC.filePath('oclife', 'ajax', 'canEditTag.php'),
        async: false,
        timeout: 500,

        success: function(result) {
            canEditTag = parseInt(result);
        },

        error: function (xhr, status) {
            window.alert('Unable to Get user\'s priviledge.');
        },

        type: "GET"});

    // This is the div where informations will appears
    $('#content').append('<div id="oclife_infos" title="Informations">\n\
        <div id="oclife_infoData"></div>\n\
        <fieldset class="oclife_tagsbox" id="oclife_tags_container"><legend>Tags</legend>\n\
        <input type="text" class="form-control" id="oclife_tags" placeholder="Enter tags here" min-width: 150px; />\n\
        </fieldset>\n\
        </div>');


    $("#oclife_infos").dialog({
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
