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
                    updateStatusBar('Unable to Get user\'s priviledge.');
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
                    $( "#filePath" ).dialog( "open" );
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
                
                if(nodeClass == 'global') {
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
                        updateStatusBar("Unable to get files list!");
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
                                    updateStatusBar("Tag moved successfully!");
                                } else {
                                    updateStatusBar("Tag not moved! DB error!");
                                }
                            },
                            error: function( xhr, status ) {
                                    updateStatusBar("Tag not moved! Ajax error!");
                            }
                        });                      
                      
                    }
                  },
                  
            contextMenu: {
                    menu: function () {
                        if(canEditTag === 1) {
                            return {'edit' : { 'name': 'Rename', 'icon': 'edit' },
                            'new': { 'name': 'New', 'icon': 'add' },
                            'delete': { 'name': 'Delete', 'icon': 'delete'}};
                        } else {
                            return {'nothing' : {'name': 'Nothing possible', 'icon':'delete', disabled: true}};
                        }
                    },
                    
                    actions: function(node, action, options) {
                        switch(action) {
                            case 'edit': {
                                var node = $("#tagstree").fancytree("getActiveNode");
                                
                                if(node.key == -1) {
                                    updateStatusBar("Editing of Root node not allowed!");
                                    break;
                                }

                                $("#tagName").val(node.title);
                                $("#tagID").val(node.key);

                                $( "#renameTag" ).dialog( "open" );
                                break;
                            }

                            case 'new': {
                                var node = $("#tagstree").fancytree("getActiveNode");

                                newTagName.value = "";
                                parentID.value = node.key;

                                $( "#createTag" ).dialog( "open" );
                                break;
                            }

                            case 'delete': {
                                var node = $("#tagstree").fancytree("getActiveNode");

                                if(node.key == -1) {
                                    updateStatusBar("Deleting of Root node not allowed!");
                                    break;
                                }
                                $("#tagToDelete").text(node.title);
                                deleteID.value = node.key;

                                $( "#deleteConfirm" ).dialog( "open" );
                                break;
                            }
                        }
                    }
                  }
        });

        allFields = $( [] ).add( tagName );

        function checkLength( o, min, max ) {
            if ( o.value.length > max || o.value.length < min ) {
                    updateTips( "Lenght must be between " + min + " - " + max + "." );
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

        $( "#renameTag" ).dialog({
            autoOpen: false,
            height: 200,
            width: 350,
            modal: true,
            resizable: false,
            buttons: {
                "Conferma": function() {
                    var bValid = true;
                    allFields.removeClass( "ui-state-error" );
                    bValid = bValid && checkLength( tagName, 1, 20 );

                    if ( bValid ) {
                        var newValue = tagName.value;
                        var tagToMod = tagID.value;
                        
                        $.ajax({
                            url: OC.filePath('oclife', 'ajax', 'renameTag.php'),
                            async: false,
                            timeout: 2000,
                            
                            data: {
                                tagID: tagToMod,
                                tagName: newValue
                            },
                            
                            type: "POST",
                            
                            success: function( result ) {
                                if(result === 'OK') {
                                    var node = $("#tagstree").fancytree("getActiveNode");
                                    node.setTitle(newValue);
                                    
                                    updateStatusBar("Rename done!");
                                } else {
                                    updateStatusBar("Unable to rename! DB error!");
                                }
                            },

                            error: function( xhr, status ) {
                                updateStatusBar("Unable to rename! Ajax error!");
                            }                            
                        });                        
                        
                        $( this ).dialog( "close" );                        
                    }
                },
            
                Cancel: {
                    text: "Annulla",
                    click: function() {
                        $( this ).dialog( "close" );
                    }
                }
            },

            close: function() {
                allFields.val( "" ).removeClass( "ui-state-error" );
            }
        });
        
        $( "#createTag" ).dialog({
            autoOpen: false,
            height: 200,
            width: 350,
            modal: true,
            resizable: false,
            buttons: {
                Confirm: {
                    text: "Confirm",
                    click: insertTag()
                },
            
                Cancel: {
                    text: "Cancel",
                    click: function() {
                        $( this ).dialog( "close" );
                    }
                }
            },

            close: function() {
                allFields.val( "" ).removeClass( "ui-state-error" );
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

                var dataPath = OC.filePath('oclife', 'ajax', 'createTag.php');

                $.ajax({
                    url: dataPath,
                    async: false,
                    timeout: 2000,

                    data: {
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
                            node.addChildren(nodeData);
                            node.setExpanded(true);

                            updateStatusBar("Tag created successfully!");
                        } else {
                            updateStatusBar("Unable to create! DB error!");
                        }
                    },

                    error: function( xhr, status ) {
                        updateStatusBar("Unable to create! Ajax error!");
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
                "Annulla": function() {
                    $( this ).dialog( "close" );
                    updateStatusBar("Operation canceled: No deletion occurred!");
                },
                
                "Cancella": function() {
                    $( this ).dialog( "close" );
                    
                    var dataPath = OC.filePath('oclife', 'ajax', 'deleteTag.php');
                    var tagID = deleteID.value;
                    
                    if(tagID === "-1") {
                        updateStatusBar("Invalid tag number! Nothing done!");
                        return;
                    }
                    
                    $.ajax({
                        url: dataPath,
                        async: false,
                        timeout: 2000,

                        data: {
                            tagID: tagID
                        },

                        type: "POST",

                        success: function( result ) {
                            if(result === 'OK') {
                                $("#tagstree").fancytree("getActiveNode").remove();
                                updateStatusBar("Tags removed successfully!");
                            } else {
                                updateStatusBar("Tags not removed! DB error!");
                            }
                        },
                        error: function( xhr, status ) {
                                updateStatusBar("Tags not removed! Ajax error!");
                        }
                    });                    
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
