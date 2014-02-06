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

            source: {
                url: dataPath
            },
            
            dnd: {
                    preventVoidMoves: true, // Prevent dropping nodes 'before self', etc.
                    preventRecursiveMoves: true, // Prevent dropping nodes on own descendants
                    autoExpandMS: 400,
                    dragStart: function(node, data) {
                      return canEdit.value === '1';
                    },
                    dragEnter: function(node, data) {
                       return true;
                    },
                    dragDrop: function(node, data) {
                        data.otherNode.moveTo(node, data.hitMode);

                        var dataPath = OC.filePath('oclife', 'ajax', 'changeHierachy.php');

                        $.ajax({
                            url: dataPath,
                            
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
                    menu: {
                        'edit' : { 'name': 'Rename', 'icon': 'edit' },
                        'new': { 'name': 'New', 'icon': 'add' },
                        'delete': { 'name': 'Delete', 'icon': 'delete'}
                    },
                    
                    actions: function(node, action, options) {
                        if(canEdit.value === '1') {
                            switch(action) {
                                case 'edit': {
                                    var node = $("#tagstree").fancytree("getActiveNode");

                                    tagName.value = node.title;
                                    tagID.value = node.key;

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

                                    $("#tagToDelete").text(node.title);
                                    deleteID.value = node.key;

                                    $( "#deleteConfirm" ).dialog( "open" );
                                    break;
                                }
                            }
                        } else {
                            $("#onlyAdmin").dialog("open");
                        }
                    }
                  }
        });

        allFields = $( [] ).add( tagName );

        function checkLength( o, min, max ) {
            if ( o.value.length > max || o.value.length < min ) {
                    //o.addClass( "ui-state-error" );
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
            $( "#oclife_status" )
                .text( t )
                .addClass( "ui-state-highlight" );
                setTimeout(function() {
                    $("#oclife_status").text("Ready!");
                }, 1500 );
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
                        
                        var dataPath = OC.filePath('oclife', 'ajax', 'renameTag.php');

                        $.ajax({
                            url: dataPath,
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
                "Conferma": function() {
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
                                var resArray = result.split("-");
                                if(resArray[0] === 'OK') {
                                    var node = $("#tagstree").fancytree("getActiveNode");

                                    node.addChildren({title: newValue});
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
                        
                        $( this ).dialog( "close" );                        
                    }
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
                    
                    if(tagID == "-1") {
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
    
        $( "#onlyAdmin" ).dialog({
            modal: true,
            autoOpen: false,
            width: 320,
            buttons: {
                Ok: function() {
                    $( this ).dialog( "close" );
                }
            }
        });
});