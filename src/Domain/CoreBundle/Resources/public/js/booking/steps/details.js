console.log('script loaded');
require(['jquery', 'jquery.jqte', 'jquery.iframe-transport', 'jquery.fileupload'], function($) {
    console.log('require-init');

        // for old browsers
        if (!window.FileReader) {
            $progress_el.addClass('alternative-view');
        }

        var fileUploadOptions = {
            dataType: 'json',
            singleFileUploads: true,
            url: Routing.generate('upload_file'),
            add: function(e, data) {

                var acceptFileTypes = /(\.|\/)(pdf|msword|vnd\.openxmlformats-officedocument\.wordprocessingml\.document)$/i;
                var acceptFileExt = /\.(pdf|doc|docx)$/i;
                var maxFileSize = 5 * 1024 * 1024;

                //no file to process
                if (undefined === data.originalFiles[0]) {
                    return;
                }

                var file = data.originalFiles[0];

                if (undefined !== file['type']) {
                    if (!acceptFileTypes.test(file['type'])) {
                        alert('Not an accepted file type');
                        return;
                    }
                } else if (undefined !== file['name']) {
                    if (!acceptFileExt.test(file['name'])) {
                        alert('Not an accepted file type');
                        return;
                    }
                } else {
                    alert('Unsupported file format.');
                    return;
                }

                if (undefined !== file['size'] && file['size'] > maxFileSize) {
                    alert('Filesize is too big. Max size: 5mb');
                    return;
                }

                // submit file
                if (data.autoUpload || data.autoUpload !== false) {
                    data.process().done(function() {
                        var jqXHR = data.submit();
                        onStartUploading(file, jqXHR);
                    });
                }

            },
            done: function(e, data) {
                var file = data.result.files[0];
                onUploadDone(file);
            },
            fail: function(e, data) {
                onAbbort();
            },
            progressall: function(e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $brogressBar_el.width(progress + '%');
            }
        }


        function onStartUploading(file, jqXHR) {
            $brogressBar_el.width(0);
            $progress_el.show();
            $fileControlContainer.empty();
            $fileControlContainer.append('<div class="silver">' + file.name + '</div>');
            var $button = $('<a style="margin-left: 10px" >Cancel</a>')
            $button.click(function(e) {
                e.preventDefault();
                jqXHR.abort();
            });

            $fileControlContainer.append($button);
        }

        function onAbbort() {
            $progress_el.hide();
            $fileControlContainer.empty();
            $file_el.val('');
        }

        function onUploadDone(file) {
            $progress_el.hide();
            $fileControlContainer.empty();
            $fileControlContainer.append('<a>' + file.name + '</a>');

            $file_el.val(file.id);

            var $button = $('<a class="remove_file" >Remove</a>')

            $fileControlContainer.append($button);
        }


        var $fileuploadeer_el = $('#fileuploader');
        var $file_el = $('#booking_details_resumeFile');
        var $progress_el = $('#progress');
        var $brogressBar_el = $progress_el.find('.bar');
        var $fileControlContainer = $('#file_controls');


        $fileControlContainer.on('click', '.remove_file', function(e) {
            e.preventDefault();
            $file_el.val('');
            $fileControlContainer.empty();
        });


        console.log('jqte');

        $('.textarea_content').jqte();

        console.log('fileupload');


        $fileuploadeer_el.prop('disabled', false);
        $fileuploadeer_el.fileupload(fileUploadOptions);

});

