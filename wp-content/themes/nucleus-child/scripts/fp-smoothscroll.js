jQuery( document ).ready(function( $ ) {
    // pmeFeatures animated Scroll
    $('.menu-item-473').on('click', function (e) {
        e.preventDefault();
        var target = $('#pmeFeatures');
        scrollToTarget(target);
    });
     $('.features_scroll').on('click', function (e) {
        e.preventDefault();
        var target = $('#pmeFeatures');
        scrollToTarget(target);
    });

    // pmeEngineer animated Scroll
    $('.menu-item-474').on('click', function (e) {
        e.preventDefault();
        var target = $('#pmeEngineer');
        scrollToTarget(target);
    });
    $('.engineer_scroll').on('click', function (e) {
        e.preventDefault();
        var target = $('#pmeEngineer');
        scrollToTarget(target);
    });


    // pmeBlog animated Scroll
    $('.menu-item-555').on('click', function (e) {
        e.preventDefault();
        var target = $('#pmeBlog');
        scrollToTarget(target);
    });
    
     // subscribe animated Scroll
    $('.subscribe_scroll').on('click', function (e) {
        e.preventDefault();
        var target = $('#pmeAction');
        scrollToTarget(target);
    });

    function scrollToTarget(target){
        var offset = target.offset().top;

        if($(window).width() >= 1199){
            offset -= $('.navbar.navbar-fullwidth').height();
        }

        $('html, body').animate({
            scrollTop: offset
        }, 1000);
    }
});

