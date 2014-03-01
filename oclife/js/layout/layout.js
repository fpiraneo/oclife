jQuery(function($) {
    var container = $('#oclife_content');

    function relayout() {
        container.layout({resize: false});
    }
    relayout();

    $(window).resize(relayout);

    $('#tagscontainer').resizable({
        handles: 'e',
        stop: relayout
    });
});