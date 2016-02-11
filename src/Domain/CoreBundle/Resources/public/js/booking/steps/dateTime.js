require(['jquery', 'jquery.icheck'], function ($) {

    var icheck_params = {
        checkboxClass: 'icheckbox_minimal-grey',
        radioClass: 'iradio_minimal-grey',
        increaseArea: '20%' // optional
    };

    $('input:radio').iCheck(icheck_params);
});

