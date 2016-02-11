require(['jquery', 'jquery.collapsible'], function ($) {

    //fire collapsible
    $('.collapsible').collapsible();
    $('.remove-parent').on('click', function() {
        $(this).parent().remove();
    });
    $('.remove-parents').on('click', function() {
        $(this).parent().parent().remove();
        return false;
    });

    $('.tabs .blocks section:first').show();
    $('.tabs .navigation li:first').addClass("activetab");
    $('.tabs .navigation li .tb-btn').click( function() {
        $(this).parents('li').addClass('activetab');
        $(this).parents('li').siblings().removeClass('activetab')
        var thisId=$(this).attr('id');
        $('#sc_'+thisId).show(500);
        $('#sc_'+thisId).siblings().hide(500);
    });
});
