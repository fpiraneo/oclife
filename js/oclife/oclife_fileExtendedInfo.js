$(document).ready(function(){
    // no versions actions in public mode
    // beware of https://github.com/owncloud/core/issues/4545
    // as enabling this might hang Chrome
    if($('#isPublic').val()){
		return;
    }

    // Add tags button to 'files/index.php'
    if(typeof FileActions !== 'undefined') {
		// Add action to tag a group of files
		$(".selectedActions").html(function(index, oldhtml) {
			if(oldhtml.indexOf("download") > 0) {
				var tagIconPath = OC.imagePath('oclife','icon_tag');
				var newAction = "<a class=\"donwload\" id=\"tagGroup\">";
				newAction += "<img class=\"svg\" src=\"" + tagIconPath + "\" alt=\"Tag group of file\" style=\"width: 17px; height: 17px; margin: 0px 5px 0px 5px;\" />";
				newAction += t('oclife', 'Tag selected files') + "</a>";
				return newAction + oldhtml;
			} else {
				return oldhtml;
			}
		});
		
		var infoIconPath = OC.imagePath('oclife','icon_info');
		FileActions.register('file', t('oclife', 'Informations'), OC.PERMISSION_UPDATE, infoIconPath, function(fileName) {
			// Action to perform when clicked
			if(scanFiles.scanning) { return; } // Workaround to prevent additional http request block scanning feedback

			showFileInfo(fileName);
        });
    }

    // This is the div where informations will appears
    $('#content').append('<div id="oclife_infos" title="' + t('oclife', 'Informations') + '">\n\
        <div id="oclife_infoData">\n\
        <table>\n\
        <tr>\n\
        <td id="oclife_preview"></td>\n\
        <td id="oclife_infosData" style="vertical-align: top; padding: 10px;"></td>\n\
        </tr>\n\
        </table>\n\
        </div>\n\
        <fieldset class="oclife_tagsbox" id="oclife_tags_container"><legend>Tags</legend>\n\
        <input type="text" class="form-control" id="oclife_tags" placeholder="' + t('oclife', 'Enter tags here') + '" min-width: 150px; />\n\
        </fieldset>\n\
        </div>');

    // This is the div where tag group will happens
    $('#content').append('<div id="oclife_tagGroup" title="' + t('oclife', 'Tag selected files') + '">\n\
        <fieldset class="oclife_tagsbox"><legend>' + t('oclife', 'File(s) where the tags will be applied') + '</legend>\n\
        <table style="margin: 5px;">\n\
        <tr>\n\
        <td id="oclife_multiPreview"></td>\n\
        <td id="" style="vertical-align: top; padding: 10px;">\n\
        <select id="oclife_filesGroup">\n\
        </select>\n\
        <div id="oclife_multInfosData" />\n\
        </td>\n\
        </tr>\n\
        </table>\n\
        </fieldset>\n\
        <fieldset class="oclife_tagsbox" id="oclife_allfiles_tags_container"><legend>' + t('oclife', 'Tags common to all files') + '</legend>\n\
        <input type="text" class="form-control" id="oclife_allfiles_tags" placeholder="' + t('oclife', 'Enter tags here') + '" min-width: 150px; />\n\
        </fieldset>\n\
        <fieldset class="oclife_tagsbox" id="oclife_selfiles_tags_container"><legend>' + t('oclife', 'Tags for the selected file') + '</legend>\n\
        <input type="text" class="form-control" id="oclife_selfiles_tags" placeholder="' + t('oclife', 'Enter tags here') + '" min-width: 150px; />\n\
        </fieldset>\n\
        </div>');
    
    // How to react when user click on "Tag group of file" button
    $( "#tagGroup" ).on( "click", function() {
        var files = getSelectedFiles();
        
        if(files.length === 1) {
            showFileInfo(files[0].name);
        } else {
            showFileGroupInfo(files);
        }        
    });

    $("#oclife_filesGroup").on("change", function() {
        var selFilePath = $("#oclife_filesGroup").val();

        if(selFilePath.substr(selFilePath.length - 1) === '/') {
            $("#oclife_allfiles_tags").tokenfield('enable');
            $("#oclife_selfiles_tags").tokenfield('disable');
        } else {
            $("#oclife_allfiles_tags").tokenfield('disable');
            $("#oclife_selfiles_tags").tokenfield('enable');
        }
        
        populateFileInfo(selFilePath);
    });

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

    $("#oclife_tagGroup").dialog({
        autoOpen: false,
        width: 640,
        height: 600,
        modal: true,

        close: function() {
            $('#oclife_selfiles_tags').off('afterCreateToken');
            $('#oclife_selfiles_tags').off('removeToken');
            $('#oclife_allfiles_tags').off('afterCreateToken');
            $('#oclife_allfiles_tags').off('removeToken');
        }
    });
});

function handleTagAdd(eventData, selFileID) {
    var tagID = eventData.token.value;
    var tagLabel = eventData.token.label;
    var newTag = (tagID.toString() === tagLabel);

    if(newTag) {
        var createNew = window.confirm(t('oclife', 'The tag "') + eventData.token.label + t('oclife', '" doesn\'t exist; would you like to create a new one?'));                                                        

        if(!createNew) {
            $(eventData.relatedTarget).addClass('invalid');
        } else {
            $.ajax({
                url: OC.filePath('oclife', 'ajax', 'tagOps.php'),
                async: false,
                timeout: 2000,

                data: {
                    tagOp: 'new',
                    parentID: -1,
                    tagName: tagLabel,
                    tagLang: "xx"
                },

                type: "POST",

                success: function(result) {
                    var resArray = jQuery.parseJSON(result);
                    if(resArray.result === 'OK') {
                        tagID = parseInt(resArray.key);

                        newTag = false;                                                    
                    } else {
                        window.alert(t('oclife', 'Unable to create the tag! Ajax error.'));
                    }
                },

                error: function(xhr, status) {
                    window.alert(t('oclife', 'Unable to create the tag! Ajax error.'));
                    $(eventData.relatedTarget).addClass('invalid');
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
                fileID: JSON.stringify(selFileID),
                tagID: tagID
            },

            error: function (xhr, status) {
                window.alert(t('oclife', 'Unable to add the tag! Ajax error.'));
                $(eventData.relatedTarget).addClass('invalid');
            },

            type: "POST"});
    }
}

function handleTagRemove(eventData, selFileID) {
    $.ajax({
        url: OC.filePath('oclife', 'ajax', 'tagsUpdate.php'),
        async: false,
        timeout: 2000,

        data: {
            op: 'remove',
            fileID: JSON.stringify(selFileID),
            tagID: eventData.token.value.toString()
        },

        success: function(result) {
            if(result === "0") {
                window.alert(t('oclife', 'Unable to remove the tag! Data base error.'));
            }
        },

        error: function (xhr, status) {
            window.alert(t('oclife', 'Unable to remove the tag! Ajax error.'));
        },

        type: "POST"});
}

function showFileInfo(fileName) {
    var infoPreview = "";
    var infoContent = "";
    var fileID = -1;
    var directory = $('#dir').val();
    directory = (directory === "/") ? directory : directory + "/";

    $.ajax({
        url: OC.filePath('oclife', 'ajax', 'getFileInfo.php'),
        async: false,
        timeout: 2000,

        data: {
            filePath: directory + fileName
        },

        type: "POST",

        success: function( result ) {
            var jsonResult = JSON.parse(result);

            infoPreview = jsonResult.preview;
            infoContent = jsonResult.infos;
            fileID = jsonResult.fileid;
            
            // Prepare token fields
            $('#oclife_tags').tokenfield({
              autocomplete: {
                source:  function(request, response) {
                    $.ajax({
                            url: OC.filePath('oclife', 'ajax', 'getTagFlat.php'),
                            data: {
                                term: request.term
                            },

                            success: function(data) {
                                var returnString = data;
                                var jsonResult = jQuery.parseJSON(returnString);
                                response(jsonResult);
                            },

                            error: function (xhr, status) {
                                window.alert(t('oclife', 'Unable to get the tags! Ajax error.'));
                            }
                        })
                    },
                    minLength: 2,
                    delay: 200
              },
              showAutocompleteOnFocus: false
            }).data('bs.tokenfield').$input.on('autocompletefocus', function(e, ui){
                e.preventDefault();
                $(this).val(ui.item.label);
            });            
        },

        error: function( xhr, status ) {
            infoContent = t('oclife', 'Unable to retrieve informations on this file! Ajax error!');
        }
    });                                

    var dialogTitle =  t('oclife', 'Informations on') + ' "' + fileName + '"';
    $('#oclife_infos').dialog( "option", "title", dialogTitle );

    $.ajax({
        url: OC.filePath('oclife', 'ajax', 'getTagsForFile.php'),
        async: false,
        timeout: 2000,

        data: {
            id: fileID
        },

        success: function(result) {
            $('#oclife_tags').tokenfield('setTokens', JSON.parse(result));
        },

        error: function (xhr, status) {
            window.alert(t('oclife', 'Unable to get actual tags for this document! Ajax error!'));
        },

        type: "POST"});

    // Install event handlers
    $('#oclife_tags').on('afterCreateToken', function(e) {
        handleTagAdd(e, fileID);
    });

    $('#oclife_tags').on('removeToken', 
        function (e) {
            handleTagRemove(e, fileID);
        }
    );

    $('#oclife_preview').html(infoPreview);
    $('#oclife_infosData').html(infoContent);
    $('#oclife_infos').dialog("open");
}

function showFileGroupInfo(files) {
    var filesList = JSON.stringify(files);
    var directory = $('#dir').val();
    directory = (directory === "/") ? directory : directory + "/";

    // Populate the select
    $('#oclife_filesGroup').html('');
    $('#oclife_filesGroup').append('<option value="' + directory + '" selected>' + t('oclife', 'All selected files') + '</option>');
    for(var iterator = 0; iterator < files.length; iterator++) {
        $('#oclife_filesGroup').append('<option value="' + directory + files[iterator].name + '">' + files[iterator].name + '</option>');
    }

    // Get multiple files attribute
    populateFileInfo(directory);

    // Opens the popup
    $("#oclife_allfiles_tags").tokenfield('enable');
    $("#oclife_selfiles_tags").tokenfield('disable');    
    $('#oclife_tagGroup').dialog("open");
}

function populateFileInfo(filePath) {
	// Get file infos
	var fileInfos = getFileInfo(filePath);
	
	$('#oclife_multiPreview').html(fileInfos.preview);
	$('#oclife_multInfosData').html(fileInfos.infos);
	
	$('#oclife_allfiles_tags, #oclife_selfiles_tags').tokenfield({
	  autocomplete: {
		source:  function(request, response) {
			$.ajax({
					url: OC.filePath('oclife', 'ajax', 'getTagFlat.php'),

					data: {
						term: request.term
					},

					success: function(data) {
						var returnString = data;
						var jsonResult = jQuery.parseJSON(returnString);
						response(jsonResult);
					},

					error: function (xhr, status) {
						window.alert(p('oclife', 'Unable to get the tags! Ajax error.'));
					}
				})
			},
			minLength: 2,
			delay: 200                                                
	  },
	  showAutocompleteOnFocus: false
	});
			
	$('#oclife_allfiles_tags').data('bs.tokenfield').$input.on('autocompletefocus', function(e, ui){
		e.preventDefault();
		$(this).val(ui.item.label);
	});

	$('#oclife_selfiles_tags').data('bs.tokenfield').$input.on('autocompletefocus', function(e, ui){
		e.preventDefault();
		$(this).val(ui.item.label);
	});

	// Query to populate the tags
	var fileID = (fileInfos.fileID === -1) ? getSelectedFiles('id') : parseInt(fileInfos.fileID);

	// Remove old event handler
	$('#oclife_selfiles_tags').off('afterCreateToken');
	$('#oclife_selfiles_tags').off('removeToken');
	$('#oclife_allfiles_tags').off('afterCreateToken');
	$('#oclife_allfiles_tags').off('removeToken');

	// Query for actual tags
	$.ajax({
		url: OC.filePath('oclife', 'ajax', 'getTagsForFile.php'),
		async: false,
		timeout: 2000,

		data: {
			id: JSON.stringify(fileID)
		},

		success: function(result) {
			$('#oclife_allfiles_tags').tokenfield('setTokens', []);
			$('#oclife_selfiles_tags').tokenfield('setTokens', []);
			
			if(fileID instanceof Array) {
				$('#oclife_allfiles_tags').tokenfield('setTokens', JSON.parse(result));
			} else {
				$('#oclife_selfiles_tags').tokenfield('setTokens', JSON.parse(result));
			}
		},

		error: function (xhr, status) {
			window.alert(p('oclife', 'Unable to get actual tags for this document! Ajax error!'));
		},

		type: "POST"});
			
	// Install event handlers
	$('#oclife_selfiles_tags').on('afterCreateToken', function(e) {
		handleTagAdd(e, fileID);
	});

	$('#oclife_selfiles_tags').on('removeToken', 
		function (e) {
			handleTagRemove(e, fileID);
		}
	);
	
	$('#oclife_allfiles_tags').on('afterCreateToken', function(e) {
		handleTagAdd(e, fileID);
	});
	
	$('#oclife_allfiles_tags').on('removeToken', 
		function (e) {
			handleTagRemove(e, fileID);
		}
	);
}

function getFileInfo(filePath) {
    var result = new Object();
    
    $.ajax({
        url: OC.filePath('oclife', 'ajax', 'getFileInfo.php'),
        async: false,
        timeout: 2000,

        data: {
            filePath: filePath
        },

        type: "POST",

        success: function(ajaxResult) {
            var jsonResult = JSON.parse(ajaxResult);

            result.result = "OK";
            result.preview = jsonResult.preview;
            result.infos = jsonResult.infos;
            result.fileID = parseInt(jsonResult.fileid);
            },

        error: function (xhr, status) {
            result.result = "KO";
            result.preview = "";
            result.infos = "";
            result.fileID = -1;
        }
    });
    
    return result;
}

function getSelectedFiles(property) {
	var elements=$('td.filename input:checkbox:checked').parent().parent();
	var files=[];
	elements.each(function(i,element) {
		var file={
			id:$(element).attr('data-id'),
			name:$(element).attr('data-file'),
			mime:$(element).data('mime'),
			type:$(element).data('type'),
			size:$(element).data('size'),
			etag:$(element).data('etag')
		};

		if(file.mime.indexOf('directory') === -1) {
			if (property) {
				files.push(file[property]);
			} else {
				files.push(file);
			}
		}
	});
	return files;
}
