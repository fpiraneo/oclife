var previewShown = false;

$(document).ready(function() {
    allFields = $([]).add(tagName);
    
    $("#tagstree").fancytree({
        extensions: ["dnd"],

        renderNode: function(event, data) {
            var iconCSS = getItemIcon(data.node.data.permission);

            var span = $(data.node.span);
            var findResult = span.find("> span.fancytree-icon");
            findResult.css("backgroundImage", iconCSS);
            findResult.css("backgroundPosition", "0 0");
        },

        source: {
            url: OC.filePath('oclife', 'ajax', 'getTags.php')
        },

        checkbox: true,
        
        activate: function(event, data) {
            var node = $("#tagstree").fancytree("getActiveNode");
            if(node.key === "-1") {
                $("#btnNew").button( "option", "disabled", false );
                return;
            }
            
            $("#btnRename").button( "option", "disabled", false );
            $("#btnDelete").button( "option", "disabled", false );
            adjustPriviledge(data.node.key);
        },
        
        deactivate: function(event, data) {
            deActivateButtons();
        },

        select: function(event, data) {
            var selectedNodes = data.tree.getSelectedNodes();
            var selNodesData = new Array();

            for(i = 0; i < selectedNodes.length; i++) {
                var nodeData = new Object();
                nodeData.key = selectedNodes[i].key;
                nodeData.title = selectedNodes[i].title;

                selNodesData.push(nodeData);
            }

            var tags = JSON.stringify(selNodesData);

            $.ajax({
                url: OC.filePath('oclife', 'ajax', 'searchFilesFromTags.php'),

                data: {
                    tags: tags
                },

                type: "POST",

                success: function( result ) {
                    $("#oclife_fileList").html(result);

                    if(result === '') {
                        $("#oclife_emptylist").css("display", "block");
                    } else {
                        $("#oclife_emptylist").css("display", "none");
                    }
                },

                error: function( xhr, status ) {
                    updateStatusBar(t('oclife', 'Unable to get files list!'));
                }
            });
        },

        dnd: {
            preventVoidMoves: true, // Prevent dropping nodes 'before self', etc.
            preventRecursiveMoves: true, // Prevent dropping nodes on own descendants
            autoExpandMS: 400,
            dragStart: function(node, data) {
              return true;
            },
            dragEnter: function(node, data) {
               return true;
            },
            dragDrop: function(node, data) {
                data.otherNode.moveTo(node, data.hitMode);

                $.ajax({
                    url: OC.filePath('oclife', 'ajax', 'changeHierachy.php'),

                    data: {
                        movedTag: data.otherNode.key,
                        droppedTo: data.node.key
                    },

                    type: "POST",

                    success: function( result ) {
                        if(result === 'OK') {
                            updateStatusBar(t('oclife', 'Tag moved successfully!'));
                        } else {
                            updateStatusBar(t('oclife', 'Tag not moved! Data base error!'));
                        }
                    },
                    
                    error: function( xhr, status ) {
                        updateStatusBar(t('oclife', 'Tag not moved! Ajax error!'));
                    }
                });
            }
        }
    });

    $( "#btnExpandAll" )
        .button({
            icons: {
                primary: "ui-icon-plus"
            },
            text: false
        })
        .click(function() {
            $("#tagstree").fancytree("getRootNode").visit(function(node){
                node.setExpanded(true);
            });
        });

    $( "#btnCollapseAll" )
        .button({
            icons: {
                primary: "ui-icon-minus"
            },
            text: false
        })
        .click(function() {
            $("#tagstree").fancytree("getRootNode").visit(function(node){
                node.setExpanded(false);
            });
        });

    $( "#btnNew" )
        .button({
            icons: {
                primary: "ui-icon-document"
            },
            text: false,
            disabled: true 
        })
        .click(function() {
            var node = $("#tagstree").fancytree("getActiveNode");
            var nodeKey = -1;
            if(node !== null) {
                nodeKey = node.key;
            }

            newTagName.value = "";
            parentID.value = nodeKey;

            $( "#createTag" ).dialog( "open" );
            $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:eq(0)').focus();
        });

    $( "#btnRename" )
        .button({
            icons: {
                primary: "ui-icon-pencil"
            },
            text: false,
            disabled: true 
        })
        .click(function() {
            var node = $("#tagstree").fancytree("getActiveNode");
            if(node === null) {
                return;
            }

            if(node.key === '-1') {
                updateStatusBar(t('oclife', 'Editing of Root node not allowed!'));
                return;
            }

            $("#tagName").val(node.title);
            $("#tagID").val(node.key);

            $( "#renameTag" ).dialog( "open" );
            $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:eq(0)').focus();                
        });

    $( "#btnDelete" )
        .button({
            icons: {
                primary: "ui-icon-trash"
            },
            text: false,
            disabled: true 
        })
        .click(function() {
            var node = $("#tagstree").fancytree("getActiveNode");
            if(node === null) {
                return;
            }

            if(node.key === '-1') {
                updateStatusBar(t('oclife', 'Deleting of Root node not allowed!'));
                return;
            }
            $("#tagToDelete").text(node.title);
            deleteID.value = node.key;

            $( "#deleteConfirm" ).dialog( "open" );
            $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:eq(0)').focus();
        });

    $("#menuOwnPriv").on("change", function(){
        var value = $("#menuOwnPriv").val();
        var node = $("#tagstree").fancytree("getActiveNode");
        
        if(value === "OwnRO") {
            changePriviledge(node.key, 'r-xxxx');
        } else if(value === "OwnRW") {
            changePriviledge(node.key, 'rwxxxx');
        }
    });
    
    $("#menuGrpPriv").on("change", function(){
        var value = $("#menuGrpPriv").val();
        var node = $("#tagstree").fancytree("getActiveNode");
        
        if(value === "GrpNO") {
            changePriviledge(node.key, 'xx--xx');
        } else if(value === "GrpRO") {
            changePriviledge(node.key, 'xxr-xx');
        } else if(value === "GrpRW") {
            changePriviledge(node.key, 'xxrwxx');
        }
    });

    $("#menuAllPriv").on("change", function(){
        var value = $("#menuAllPriv").val();
        var node = $("#tagstree").fancytree("getActiveNode");
        
        if(value === "AllNO") {
            changePriviledge(node.key, 'xxxx--');
        } else if(value === "AllRO") {
            changePriviledge(node.key, 'xxxxr-');
        } else if(value === "AllRW") {
            changePriviledge(node.key, 'xxxxrw');
        }
    });

    $("#menuOwnName").on("change", function(){
        var newOwner = $("#menuOwnName").val();
        var nodeKey = $("#tagstree").fancytree("getActiveNode").key;
        
        $.ajax({
            url: OC.filePath('oclife', 'ajax', 'changePriviledge.php'),

            data: {
                tagID: nodeKey,
                tagOwner: newOwner
            },

            type: "POST",

            success: function(result) {
                var resultData = jQuery.parseJSON(result);
                
                if(resultData.result === 'OK') {
                    updateStatusBar(t('oclife', 'Owner changed successfully!'));
                } else if(result === 'NOTALLOWED') {
                    updateStatusBar(t('oclife', 'Owner not changed! Permission denied!'));
                } else {
                    updateStatusBar(t('oclife', 'Owner not changed! Data base error!'));
                }
            },
            error: function( xhr, status ) {
                updateStatusBar(t('oclife', 'Owner not changed! Ajax error!'));
            }
        });
    });

    $("#fileTable").delegate(
        ".oclife_tile",
        "click",
        function(eventData) {
            var fileID = $(this).attr("data-fileid");
            var filePath = $(this).attr("data-fullPath");

            showPreview(filePath);
        });

        $(window).resize(function(){
            if(previewShown) {
                $("#imagePreview").dialog("option","position","center");
            }
        }).resize();

    $("#fileTable").delegate(
        "#imagePreview",
        "click",
        function(eventData) {
            $("#imagePreview").dialog("close");
            $("#previewArea").attr("src", "");
            previewShown = false;
        });

    function deActivateButtons() {
        $("#btnRename").button( "option", "disabled", true );
        $("#btnDelete").button( "option", "disabled", true );

        $("#menuOwnName").prop("disabled", true);
        $("#menuOwnPriv").prop("disabled", true);
        $("#menuGrpPriv").prop("disabled", true);
        $("#menuAllPriv").prop("disabled", true);
    }
    
    function adjustPriviledge(tagID) {
        $.ajax({
            url: OC.filePath('oclife', 'ajax', 'tagOps.php'),
            async: false,
            timeout: 2000,

            data: {
                tagOp: 'info',
                tagID: tagID,
                tagName: '',
                tagLang: 'xx'
            },

            type: "POST",

            success: function(result) {
                var resultData = jQuery.parseJSON(result);

                if(resultData.result === 'OK') {
                    $("#menuOwnName").prop("disabled", false);
                    $("#menuOwnName").val(resultData.owner);
                    
                    $("#menuOwnPriv").prop("disabled", false);
                    $("#menuGrpPriv").prop("disabled", false);
                    $("#menuAllPriv").prop("disabled", false);
                    
                    var permission = resultData.permission;
                    
                    if(permission.substring(0,1) === "r" && permission.substring(1,2) === "w") {
                        $("#menuOwnPriv").val("OwnRW");
                    } else {
                        $("#menuOwnPriv").val("OwnRO");
                    }
                    
                    if(permission.substring(2,3) === "-" && permission.substring(3,4) === "-") {
                        $("#menuGrpPriv").val("GrpNO");
                    }
                    else if(permission.substring(2,3) === "r" && permission.substring(3,4) === "w") {
                        $("#menuGrpPriv").val("GrpRW");
                    } else {
                        $("#menuGrpPriv").val("GrpRO");
                    }

                    if(permission.substring(4,5) === "-" && permission.substring(5,6) === "-") {
                        $("#menuAllPriv").val("AllNO");
                    }
                    else if(permission.substring(4,5) === "r" && permission.substring(5,6) === "w") {
                        $("#menuAllPriv").val("AllRW");
                    } else {
                        $("#menuAllPriv").val("AllRO");
                    }
                } else {
                    updateStatusBar(t('oclife', 'Unable to get info! Data base error!'));
                }
            },

            error: function( xhr, status ) {
                updateStatusBar(t('oclife', 'Unable to get info! Ajax error!'));
            }                            
        });
    }

    function showPreview(filePath) {
        var maskHeight = $(window).height();  
        var maskWidth = $(window).width();
        var prevWidth = 825;
        var prevHeight = 660;
        var dialogTop =  (maskHeight  - prevHeight) / 2;  
        var dialogLeft = (maskWidth - prevWidth) / 2; 
        var thumbPath = OC.filePath("oclife", "", "getPreview.php") + "?filePath=" + encodeURIComponent(filePath);

        $("#imagePreview").dialog("open");
        $("#previewArea").attr("src", thumbPath);
        $("#imagePreview").dialog({top: dialogTop, left: dialogLeft, width: prevWidth, height: prevHeight, position: "fixed"});
        $("#imagePreview").dialog("option","position","center");

        previewShown = true;
    }

    function getItemIcon(nodeClass) {
        if(nodeClass === undefined) {
            return '';
        }
        
        var iconCSS = '';
        var ownPriv = nodeClass.substring(0,2);
        var grpPriv = nodeClass.substring(2,4);
        var allPriv = nodeClass.substring(4,6);

        if(ownPriv === 'r-' && grpPriv === '--' && allPriv === '--') {
            iconCSS = "URL(" + OC.filePath('oclife', 'img', 'fancytree/icon_person_red.png') + ")";
        } else if(ownPriv === 'rw' && grpPriv === '--' && allPriv === '--') {
            iconCSS = "URL(" + OC.filePath('oclife', 'img', 'fancytree/icon_person_green.png') + ")";
        } else if(grpPriv === 'r-' && allPriv === '--') {
            iconCSS = "URL(" + OC.filePath('oclife', 'img', 'fancytree/icon_group_red.png') + ")";
        } else if(grpPriv === 'rw' && allPriv === '--') {
            iconCSS = "URL(" + OC.filePath('oclife', 'img', 'fancytree/icon_group_green.png') + ")";
        } else if(allPriv === 'r-') {
            iconCSS = "URL(" + OC.filePath('oclife', 'img', 'fancytree/icon_globe_red.png') + ")";
        } else if(allPriv === 'rw') {
            iconCSS = "URL(" + OC.filePath('oclife', 'img', 'fancytree/icon_globe_green.png') + ")";
        } else {
            iconCSS = "URL(" + OC.filePath('oclife', 'img', 'fancytree/icon_invalid.png') + ")";
        }

        return iconCSS;
    }

    function checkLength( o, min, max ) {
        if(o.value.length > max || o.value.length < min ) {
            updateTips('Lenght must be between ' + min + " - " + max + "." );
            return false;
        } else {
            return true;
        }
    }

    function updateTips( t ) {
        $( ".validateTips" )
            .text( t )
            .addClass( "ui-state-highlight" );
            setTimeout(function() {
                $( ".validateTips" ).removeClass( "ui-state-highlight", 1500 );
            }, 500 );
    }

    function updateStatusBar( t ) {
        $('#notification').html(t);
        $('#notification').slideDown();
        window.setTimeout(function(){
            $('#notification').slideUp();
        }, 5000);            
    }

    $("#renameTag").dialog({
        autoOpen: false,
        height: 200,
        width: 350,
        modal: true,
        resizable: false,
        buttons: {
            Confirm: {
                text: t('oclife', 'Confirm'),
                click: function() {
                    renameTag();
                }
            },

            Cancel: {
                text: t('oclife', 'Cancel'),
                click: function() {
                    $( this ).dialog( "close" );
                }
            }
        },

        close: function() {
            allFields.val( "" ).removeClass( "ui-state-error" );
        }
    });

    $("#renameTag").on('keypress', function(e) {
        var code = (e.keyCode ? e.keyCode : e.which);
        if(code === 13) {
            e.preventDefault();
            renameTag();
        }
    });

    function renameTag() {
        var bValid = true;
        allFields.removeClass( "ui-state-error" );
        bValid = bValid && checkLength( tagName, 1, 20 );

        if ( bValid ) {
            var newValue = tagName.value;
            var tagToMod = tagID.value;

            $.ajax({
                url: OC.filePath('oclife', 'ajax', 'tagOps.php'),
                async: false,
                timeout: 2000,

                data: {
                    tagOp: 'rename',
                    tagID: tagToMod,
                    tagName: newValue,
                    tagLang: 'xx'
                },

                type: "POST",

                success: function(result) {
                    var resultData = jQuery.parseJSON(result);

                    if(resultData.result === 'OK') {
                        var parentNode = $("#tagstree").fancytree("getActiveNode").getParent();
                        $("#tagstree").fancytree("getActiveNode").remove();

                        var nodeData = {
                            'title': resultData.title,
                            'key': parseInt(resultData.key),
                            'permission': resultData.permission
                        };
                        var newNode = parentNode.addChildren(nodeData);
                        newNode.setActive(true);

                        updateStatusBar(t('oclife', 'Rename done!'));
                    } else if(resultData.result === 'NOTALLOWED' ) {
			updateStatusBar(t('oclife', 'Unable to rename! Permission denied!'));
		    } else {
                        updateStatusBar(t('oclife', 'Unable to rename! Data base error!'));
                    }
                },

                error: function( xhr, status ) {
                    updateStatusBar(t('oclife', 'Unable to rename! Ajax error!'));
                }
            });                        

            $("#renameTag").dialog( "close" );
        }
    }

    $( "#createTag" ).dialog({
        autoOpen: false,
        height: 200,
        width: 350,
        modal: true,
        resizable: false,
        buttons: {
            Confirm: {
                text: t('oclife', 'Confirm'),
                click: function() {
                    insertTag();
                }
            },

            Cancel: {
                text: t('oclife', 'Cancel'),
                click: function() {
                    $( this ).dialog( "close" );
                }
            }
        },

        close: function() {
            allFields.val("").removeClass( "ui-state-error" );
        }
    });

    $("#createTag").on('keypress', function(e) {
        var code = (e.keyCode ? e.keyCode : e.which);
        if(code === 13) {
            e.preventDefault();
            insertTag();
        }
    });

    function insertTag() {
        var bValid = true;
        allFields.removeClass( "ui-state-error" );
        bValid = bValid && checkLength( newTagName, 1, 20 );

        if ( bValid ) {
            var newValue = newTagName.value;
            var parent = parentID.value;

            $.ajax({
                url: OC.filePath('oclife', 'ajax', 'tagOps.php'),
                async: false,
                timeout: 2000,

                data: {
                    tagOp: 'new',
                    parentID: parent,
                    tagName: newValue,
                    tagLang: "xx"
                },

                type: "POST",

                success: function( result ) {                                
                    var resArray = jQuery.parseJSON(result);
                    if(resArray.result === 'OK') {
                        var node = $("#tagstree").fancytree("getActiveNode");

                        var nodeData = {
                            'title': resArray.title,
                            'key': parseInt(resArray.key),
                            'permission': resArray.permission
                        };
                        var newNode = node.addChildren(nodeData);
                        node.setExpanded(true);
                        newNode.setActive(true);

                        updateStatusBar(t('oclife', 'Tag created successfully!'));
                    } else {
                        updateStatusBar(t('oclife', 'Unable to create tag! Data base error!'));
                    }
                },

                error: function( xhr, status ) {
                    updateStatusBar(t('oclife', 'Unable to create tag! Ajax error!'));
                }
            });                        

            $('#createTag').dialog( "close" );                        
        }
    }

    $( "#deleteConfirm" ).dialog({
        resizable: false,
        autoOpen: false,
        width: 320,
        height: 200,
        modal: true,
        buttons: {
            Cancel: {
                text: t('oclife', 'Cancel'),
                click: function() {
                    $( this ).dialog( "close" );
                    updateStatusBar(t('oclife', 'Operation canceled: No deletion occurred!'));
                }
            },

            Delete: {
                text: t('oclife', 'Delete'),
                click: function() {
                    $( this ).dialog( "close" );

                    var tagID = deleteID.value;

                    if(tagID === "-1") {
                        updateStatusBar(t('oclife', 'Invalid tag number! Nothing done!'));
                        return;
                    }

                    $.ajax({
                        url: OC.filePath('oclife', 'ajax', 'tagOps.php'),
                        async: false,
                        timeout: 2000,

                        data: {
                            tagOp: 'delete',
                            parentID: '-1',
                            tagName: '',
                            tagLang: "xx",
                            tagID: tagID
                        },

                        type: "POST",

                        success: function(result) {
                            var resArray = jQuery.parseJSON(result);

                            if(resArray.result === 'OK') {
                                $("#tagstree").fancytree("getActiveNode").remove();
                                updateStatusBar(t('oclife', 'Tag removed successfully!'));
			    } else if (resArray.result === 'NOTALLOWED') {
				updateStatusBar(t('oclife', 'Tag not removed! Permission denied'));
                            } else {
                                updateStatusBar(t('oclife', 'Tag not removed! Data base error!'));
                            }
                        },
                        error: function( xhr, status ) {
                            updateStatusBar(t('oclife', 'Tags not removed! Ajax error!'));
                        }
                    });
                }
            }
        }
    });

    function changePriviledge(nodeKey, priviledge) {
        $.ajax({
            url: OC.filePath('oclife', 'ajax', 'changePriviledge.php'),

            data: {
                tagID: nodeKey,
                setPriviledge: priviledge
            },

            type: "POST",

            success: function(result) {
                var resultData = jQuery.parseJSON(result);
                
                if(resultData.result === 'OK') {
                    var node = $("#tagstree").fancytree("getActiveNode");
                    var iconCSS = getItemIcon(resultData.newpriviledges);

                    var span = $(node.span);
                    var findResult = span.find("> span.fancytree-icon");
                    findResult.css("backgroundImage", iconCSS);
                    findResult.css("backgroundPosition", "0 0");


                    updateStatusBar(t('oclife', 'Priviledge changed successfully!'));
                } else if(result === 'NOTALLOWED') {
                    updateStatusBar(t('oclife', 'Priviledge not changed! Permission denied!'));
                } else {
                    updateStatusBar(t('oclife', 'Priviledge not changed! Data base error!'));
                }
            },
            error: function( xhr, status ) {
                updateStatusBar(t('oclife', 'Priviledge not changed! Ajax error!'));
            }
        });
    }

    $("#filePath").dialog({
        resizable: false,
        autoOpen: false,
        width: 320,
        height: 200,
        modal: true,
        buttons: {
            "Close": function() {
                $( this ).dialog( "close" );
            }
        }
    });

    $("#imagePreview").dialog({
        resizable: true,
        autoOpen: false,
        width: 800,
        height: 650,
        modal: true,
        buttons: {
        },

        close: function() {
            $("#previewArea").attr("src", "");
        }            
    });
});
