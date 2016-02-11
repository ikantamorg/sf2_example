require(['jquery', 'post_to_url'], function ($) {

    var back_button = $('#step_back');

    back_button.click(function(e){
        e.preventDefault();

        var data = {
            'booking': {
                'prefered_step': booking_step-1
            }
        };
        post_to_url('', data, 'POST');

    });

});

