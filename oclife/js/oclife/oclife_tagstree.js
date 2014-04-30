var canEditTag = 0;

$(document).ready(
    function() {
        // Check if we can create / edit tags
        $.ajax({
            url: OC.filePath('oclife', 'ajax', 'canEditTag.php'),
            async: false,
            timeout: 500,

            success: function(result) {
                canEditTag = parseInt(result);
            },

            error: function (xhr, status) {
                updateStatusBar(t('oclife', 'Unable to get user\'s priviledge.'));
            },

            type: "GET"});            
    });

$(document).ready(
    function() {
        $("#fileTable").delegate(
            ".oclife_tile",
            "click",
            function(eventData) {
                var fileID = $(this).attr("data-fileid");
                var filePath = $(this).attr("data-filepath");

                $("#pathInfo").text(filePath);
                $("#filePath").dialog("open");
            });            
    });

$(function(){    
    var dataPath = OC.filePath('oclife', 'ajax', 'getTags.php');

    $( "#expandAll" )
        .button()
        .click(function() {
            $("#tagstree").fancytree("getRootNode").visit(function(node){
                node.setExpanded(true);                
            });
        });

    $( "#collapseAll" )
        .button()
        .click(function() {
            $("#tagstree").fancytree("getRootNode").visit(function(node){
                node.setExpanded(false);
            });
        });

    $("#tagstree").fancytree({
            extensions: ["dnd", "contextMenu"],

            renderNode: function(event, data) {
                // Optionally tweak data.node.span
                var nodeClass = data.node.data.class;
                
                if(nodeClass === 'global') {
                    var globalIconCSS = "URL(" + OC.filePath('oclife', 'img', 'fancytree/icon_globe.png') + ")";
                    var span = $(data.node.span);
                    var findResult = span.find("> span.fancytree-icon");
                    findResult.css("backgroundImage", globalIconCSS);
                    findResult.css("backgroundPosition", "0 0");
                }
             },

            source: {
                url: dataPath
            },
            
            checkbox: true,

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
                      return canEditTag === 1;
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
                  },
                  
            contextMenu: {
                    menu: function () {
                        if(canEditTag === 1) {
                            return {
                                'edit' : { 'name': t('oclife', 'Rename'), 'icon': 'edit' },
                                'new': { 'name': t('oclife', 'New'), 'icon': 'add' },
                                'delete': { 'name': t('oclife', 'Delete'), 'icon': 'delete'}
                            };
                        } else {
                            return {'nothing' : {'name': t('oclife', 'Nothing possible'), 'icon':'delete', disabled: true}};
                        }
                    },
                    
                    actions: function(node, action, options) {
                        switch(action) {
                            case 'edit': {
                                var node = $("#tagstree").fancytree("getActiveNode");
                                
                                if(node.key === '-1') {
                                    updateStatusBar(t('oclife', 'Editing of Root node not allowed!'));
                                    break;
                                }

                                $("#tagName").val(node.title);
                                $("#tagID").val(node.key);

                                $( "#renameTag" ).dialog( "open" );
                                $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:eq(0)').focus();
                                break;
                            }

                            case 'new': {
                                var node = $("#tagstree").fancytree("getActiveNode");

                                newTagName.value = "";
                                parentID.value = node.key;

                                $( "#createTag" ).dialog( "open" );
                                $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:eq(0)').focus();
                                break;
                            }

                            case 'delete': {
                                var node = $("#tagstree").fancytree("getActiveNode");

                                if(node.key === '-1') {
                                    updateStatusBar(t('oclife', 'Deleting of Root node not allowed!'));
                                    break;
                                }
                                $("#tagToDelete").text(node.title);
                                deleteID.value = node.key;

                                $( "#deleteConfirm" ).dialog( "open" );
                                $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:eq(0)').focus();
                                break;
                            }
                        }
                    }
                  }
        });

        allFields = $( [] ).add( tagName );

        function checkLength( o, min, max ) {
            if ( o.value.length > max || o.value.length < min ) {
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
            window.setTimeout(
                    function(){
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
                                'class': resultData.class
                            };
                            var newNode = parentNode.addChildren(nodeData);
                            newNode.setActive(true);

                            updateStatusBar(t('oclife', 'Rename done!'));
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
                                'class': resArray.class
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
});
