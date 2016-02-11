require(['jquery', 'history', 'twitter.bootstrap.select', 'jquery.icheck', 'jquery.deparam'], function ($) {

    var icheck_params = {
        checkboxClass: 'icheckbox_minimal-grey',
        radioClass: 'iradio_minimal-grey',
        increaseArea: '20%' // optional
    };

    $('input:checkbox').iCheck(icheck_params);

    var $sidebar = $('#sidebar');
    var $pagination = $('#pagination');
    var $contents = $('#contents');
    var $filter_list = $('#filter-list');
    var $sidebar_form = $('#sidebar-form');
    var $pagination = $('#pagination');
    var $top_form = $('#top-form');
    var $search_field = $('#top-form').find('input');
    var $top_category_field = $('#top-form').find('select');

    $top_category_field.selectpicker({
        'width': 260
    });

    var request_jqXHR = null;

    //click on search button on the top form
    $top_form.on('click', '[type="submit"]', function(e) {
        e.preventDefault();
        var top_category = $top_category_field.val();
        var search = $search_field.val();
        if ( ! (search || top_category)) {
            $search_field.focus();
            return false;
        }

        var top_category_name = top_category ?
            $top_category_field.children(':selected').text()
            : null;

        filter_off('search', 'search');
        filter_off('top_category', 'top_category');

        fill_primary_tags(search, top_category_name);

        make_request({
            search: search,
            top_category: top_category
        });
    });

    // function called on load to display primary tags
    function fill_primary_tags(search, top_category_name) {
        // display tag for search field
        // when custom category name is passed - display tag too
        search = search || $search_field.val();
        if (search) {
            filter_on('search', 'search', search);
        }
        // display tag for top category field
        var top_category = $top_category_field.val();

        // when custom category name is passed - display tag too
        if (top_category || top_category_name) {
            // if custom passed - display it, otherwise get current value from select
            top_category_name = top_category_name || $top_category_field.children(':selected').text();
            filter_on('top_category', 'top_category', top_category_name);
        }
    }

    fill_primary_tags();

    // change page by ajax
    $pagination.on('click', 'li > a', function (e) {
        var page = $(this).data('page');
        if (page !== undefined && page) {
            e.preventDefault();
            make_request({page: page});
        }
    });

    //show more filters
    $sidebar.on('click', 'li.show-more a', function (e) {
        e.preventDefault();

        var $this = $(this);

        var $ul = $this.closest('ul');

        $ul.find('li.over-limit:not(.tagged)').toggleClass('shown');

        text_more($this);
    });

    function text_more($link){
        var flag = $link.data('show_flag');
        if (undefined === flag) {
            flag = false;
            $link.data('link_text', $link.text());
        }
        flag = !flag;

        $link.data('show_flag', flag);

        if(flag){
            $link.text('hide');
        }else{
            $link.text($link.data('link_text'));
        }
    }

    //for native checkbox
    $sidebar.on('change', '.sidebar-list  input:checkbox', checkboc_status_change);

    $sidebar.on('ifChanged', '.sidebar-list  input:checkbox', checkboc_status_change);

    //remove tag on click
    $filter_list.on('click', '.filters .tag', function (e) {
        var $tag_el = $(this);
        remove_tag_by_el($tag_el);
        make_request();
    });

    //remove all tags
    $filter_list.on('click', '.clear-tags', function (e) {
        e.preventDefault();
        clear_tags();
        make_request();
    });

    //change filter status and make request
    function checkboc_status_change() {
        var $this = $(this);
        var id = $this.val();
        var type = $this.data('type');
        var title = $this.data('title');

        if (this.checked) {
            filter_on(type, id, title);
        } else {
            filter_off(type, id);
        }
        make_request();
    }

    // show filter bar(tag bar) if it contains any item
    function show_filter_bar_if_not_empty() {
        if ($filter_list.find('.filters .tag').length && $filter_list.is(':hidden')) {
            $filter_list.show();
        }
    }

    // hide filter bar(tag bar) if it's empty
    function hide_filter_bar_if_empty() {
        if (!$filter_list.find('.filters .tag').length && $filter_list.is(':visible')) {
            $filter_list.hide();
        }
    }

    // add tag to filter bar
    function add_tag(type, id, title) {
        var $existing_tag = find_tag(type, id);
        if ($existing_tag.length) {
            return;
        }

        var $tag = $('<span class="tag" data-filter-type="' + type + '" data-filter-id="' + id + '">' + title + '<button class="remove-tag" type="button"><i class="icon-grey-x"></i></button></span>');

        if ($.inArray(type, ['search', 'top_category']) !== -1) {
            $tag.addClass('primary');
            $filter_list.find('.filters').prepend($tag);
        } else {
            $filter_list.find('.filters').append($tag);
        }

        show_filter_bar_if_not_empty();
    }

    // remove tag by jquery doom element
    function remove_tag_by_el($tag_el) {
        filter_off($tag_el.data('filter-type'), $tag_el.data('filter-id'));
    }

    //find filter tag by type and id
    function find_tag(type, id) {
        return $filter_list.find('.filters [data-filter-type="' + type + '"][data-filter-id="' + id + '"].tag');
    }

    //remove tag by type and id
    function remove_tag(type, id) {
        var $tag_el = find_tag(type, id);
        $tag_el.remove();
        hide_filter_bar_if_empty();
    }

    function serialize_tags() {
        var tags = [];
        $filter_list.find('.filters .tag').each(function () {
            tags.push({
                id: $(this).data('filter-id'),
                type: $(this).data('filter-type'),
                title: $(this).text()
            });
        });
        return tags;
    }

    // remove all tags
    function clear_tags(silent) {
        $filter_list.find('.filters .tag').each(function () {
            var $tag_el = $(this);
            if (silent !== undefined) {
                remove_tag($tag_el.data('filter-type'), $tag_el.data('filter-id'));
            } else {
                remove_tag_by_el($tag_el);
            }
        });
        $top_category_field.selectpicker('deselectAll');
        $search_field.val('');
    }

    // update tags after successful request
    function update_tags(historyState) {

        // remove old tags
        clear_tags(true);

        var tags = historyState.data.tags || [];

        // // add tag for each value
        for(var key in tags) {
            var tag = tags[key];
            if (tag.type === 'search') {
                $search_field.val(tag.title);
            } else if (tag.type === 'top_category') {
                $top_category_field.children('option:contains(' + tag.title + ')').attr('selected', true);
                $top_category_field.change();
            }
            add_tag(tag.type, tag.id, tag.title);
        }
    }

    // find checkbox by type and id
    function find_checkbox(type, id) {
        return $sidebar.find('[data-type="' + type + '"][value="' + id + '"]input:checkbox');
    }

    //make filter checked
    function uncheck_filter(type, id) {
        if (type === 'search') {
            // search title filter
            $search_field.val('');
        } else if (type === 'top_category') {
            // category dropdown filter
            $top_category_field.selectpicker('deselectAll');
        } else {
            // sidebar filters
            var $filter = find_checkbox(type, id);
            if ($filter.prop('checked')) {
                $filter.prop('checked', false);
                updateICheck($filter);
            }
        }
    }

    //make filter unchecked
    function check_filter(type, id, title) {
        if (type === 'search') {
            // search title filter
            $search_field.val(title);
        } else if (type === 'top_category') {
            // category dropdown filter
            // find by title and make it selected
            $top_category_field.children('option:contains(' + title + ')').attr('selected', true);
            $top_category_field.change();
        } else {
            // sidebar filters
            var $filter = find_checkbox(type, id);
            if (!$filter.prop('checked')) {
                $filter.prop('checked', true);
                updateICheck($filter);
            }
        }
    }

    // turn off filter
    function filter_off(type, id) {
        remove_tag(type, id);
        uncheck_filter(type, id);
    }

    // turn on filter
    function filter_on(type, id, title) {
        check_filter(type, id, title);
        add_tag(type, id, title);
    }

    //for iCheck - update view
    function updateICheck($filter) {
        if (undefined !== $filter.data('iCheck')) {
            $filter.iCheck('update');
        }
    }

    // receive data form
    function agregate_data() {
        var sidebar_data = $.deparam($sidebar_form.serialize());
        var top_form_data = $.deparam($top_form.serialize());
        return $.extend({}, top_form_data, sidebar_data);
    }

    // make request to get new content
    function make_request(additional_data, historyState) {

        // if historyState is truly, do not add current request to history
        // it is set to true only on history buttons press

        if (historyState !== undefined) {

            // get data from history
            var data_with_tags = $.extend(true, {}, historyState.data);
            // exclude tags to form a route
            delete data_with_tags.tags;

            var string_data = $.param(data_with_tags);
            var request_url = historyState.url;

        } else {

            var data = agregate_data();

            if (undefined === additional_data || !$.isPlainObject(additional_data)) {
                additional_data = {};
            }
            data = $.extend(data, additional_data);

            var string_data = $.param(data);
            var request_url = Routing.generate('search_expert');

            // add serialized tags to data, they will be saved to history
            // save tags because if we are trying to receive tags from DOM,
            // there won't be all tags for id + type because of history ajax load
            var data_with_tags = $.extend(true, data, { tags: serialize_tags() });

            History.pushState(data_with_tags, '', request_url + "?" + string_data);
        }
        

        if (request_jqXHR) {
            request_jqXHR.abort();
        }

        $pagination.empty();
        $contents.html('<div class="ajax-center"><i class="big-ajax-gray"></i></div>');

        request_jqXHR = $.get(request_url, string_data, null, 'json').always(function () {

        }).fail(function () {
                var $error = $('<div class="alert alert-error text-center">Connection Error. Please click <a class="retry">retry</a> or refresh the page.</div>');
                $error.find('a.retry').click(function () {
                    make_request(additional_data);
                });
                $contents.html($error);
            }).done(function (data) {
                if (undefined === data) {
                    return;
                }

                if (undefined !== data.content) {
                    $contents.html(data.content);
                }
                if (undefined !== data.pagination) {
                    $pagination.html(data.pagination);
                }
                if (undefined !== data.sidebar) {
                    $sidebar_form.html(data.sidebar);
                    $sidebar_form.find('input:checkbox').iCheck(icheck_params);
                }
                if(undefined !== data.count){
                    $('#count_experts').text(data.count);
                }

                // if browser state changed with history
                if (historyState) {
                    // get values from history and update tags
                    update_tags(historyState);
                }
            });
    }

    History.Adapter.bind(window, 'statechange', function() { 
        var state = History.getState();
        make_request(state.data, {
            url: state.cleanUrl,
            data: state.data
        });
    });
});