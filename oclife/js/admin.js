$(function() {
    $( "#onlyAdminCanEdit" )
        .click(function() {
            var dataPath = OC.filePath('oclife', 'ajax', 'savePrefs.php');
            var v_onlyAdminCanEdit = onlyAdminCanEdit.value;
            
            $.ajax({
                url: dataPath,
                async: false,
                timeout: 2000,

                data: {
                    onlyAdminCanEdit: v_onlyAdminCanEdit
                },

                type: "POST",

                success: function( result ) {
                    if(result !== 'OK') {
                        window.alert("Settings not saved! Data base error!")
                    }
                },

                error: function( xhr, status ) {
                    window.alert("Settings not saved! Communication error!")
                }                            
            });
        });
    });