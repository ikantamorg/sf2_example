require(['jquery', 'full_calendar', 'post_to_url'], function ($) {

    var events_source = {
        events: available_events,
        className:'confirmed',
        color: 'transparent',
        textColor: 'black'
    };

    var options = {
        dayClick: function(date, allDay, jsEvent, view) {
            var date_str = $.fullCalendar.formatDate( date, 'yyyy-MM-dd' );
            sent_date(date_str);
        },
        eventClick: function(calEvent, jsEvent, view) {
            var date_str = $.fullCalendar.formatDate( calEvent.start, 'yyyy-MM-dd' );
            sent_date(date_str);
        },
        firstDay: 0,
        weekMode: 'variable',
        dayRender: function(date, cell){
            if (cell.hasClass('fc-today')) {
                cell.prepend('<i class="today">Today</i>');
            }
        },
        header: {
            left: 'prev,next, today:none, title',
            right: 'none'
        },
        //editable: true,
        eventRender: function(event, element) {
            element.find('.fc-event-inner').prepend('<i></i>');
        },
        eventSources: [events_source]
    };

    $('#calendar').fullCalendar(options);

    function sent_date(date){

        var data = {
            'booking': {
                'step': booking_step,
                'data': date,
                'prefered_step': booking_step + 1
            }
        };
        post_to_url('', data, 'POST');
    }

});

