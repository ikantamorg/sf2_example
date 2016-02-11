require(['jquery', 'jquery.responsiveSlides', 'twitter.bootstrap.select'], function ($) {

    $("#slider").responsiveSlides({
        auto: true,
        pager: false,
        speed: 800,
        maxwidth: 300,
        timeout: 7000
    });

    $('.selectpicker').selectpicker({
        'width': 260
    });

});
