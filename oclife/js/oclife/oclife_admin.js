$(function() {
    $( "#oclife_settings" )
        .click(function() {
            var dataPath = OC.filePath('oclife', 'ajax', 'savePrefs.php');
            var v_onlyAdminCanEdit = $('#onlyAdminCanEdit').is(":checked");
            var v_useImageMagick = $('#useImageMagick').is(":checked");
            
            $.ajax({
                url: dataPath,
                async: false,
                timeout: 2000,

                data: {
                    onlyAdminCanEdit: v_onlyAdminCanEdit,
                    useImageMagick: v_useImageMagick
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