$(function() {
    $( "#oclife_settings" )
        .click(function() {
            var dataPath = OC.filePath('oclife', 'ajax', 'savePrefs.php');
            var v_useImageMagick = $('#useImageMagick').is(":checked");
            
            $.ajax({
                url: dataPath,
                async: false,
                timeout: 2000,

                data: {
                    useImageMagick: v_useImageMagick
                },

                type: "POST",

                success: function( result ) {
                    if(result !== 'OK') {
                        window.alert(t('oclife', 'Settings not saved! Data base error!'))
                    }
                },

                error: function( xhr, status ) {
                    window.alert(t('Settings not saved! Communication error!'))
                }                            
            });
        });
    });