/*
 * jQuery File Upload Plugin JS Example 8.0
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

/*jslint nomen: true, unparam: true, regexp: true */
/*global $, window, document */

jQuery(function ($) {

    var $img = $('#logoImage');
    var $progressBar = $('#progress');
    var $cropDataForm = $('#cropData');
    var $imageLink = $('#imageLink');
    var $imageName = $('#imageName');

    var jcropAPI;

    $progressBar.hide();
    $cropDataForm.hide();

    $cropDataForm.on('submit', function() {
        $.ajax({
            url: $cropDataForm.attr('action'),
            type: 'POST',
            data: $cropDataForm.serialize(),
            success: function() {
            }
        });
        return false;
    });

    $('#fileupload').fileupload({
        url: 'http://localhost/pinpoint/web/app_dev.php/imager/handle_upload/',
        maxFileSize: 5000000,
        acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
        type: 'POST',
        dataType: 'JSON',
        progressall: function (e, data) {
            $progressBar.show();
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $progressBar.find('.bar').css('width',progress + '%');
        },
        error: function(e,d,w) {
            console.log(e);
            console.log(d);
            console.log(w);
        }
    }).bind("fileuploaddone", function (e, data) {
            $progressBar.find('.bar').css('width', '0px');
            $progressBar.hide();
            $.each(data.result.files, function (index, file) {
                console.log(file);
                if($img.attr('src') != '') {
                    stopJcrop();
                    $img.removeAttr('style');
                }
                $img.attr('src', file.url);
                $imageLink.val(file.url);
                $imageName.val(file.name);
                $img.show();
                $cropDataForm.show();
                initJcrop();
            });
        });

    function initJcrop() {
        $($img).Jcrop({
            onChange: setCoords,
            onSelect: setCoords
        },function(){
            jcropAPI = this;
        });
    }

    function stopJcrop() {
        jcropAPI.destroy();
        return (false);
    }

    function setCoords(c)
    {
        jQuery('#xCoord').val(c.x);
        jQuery('#yCoord').val(c.y);
        jQuery('#width').val(c.w);
        jQuery('#height').val(c.h);
    };


});
