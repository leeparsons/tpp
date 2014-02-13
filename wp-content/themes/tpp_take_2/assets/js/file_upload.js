var dropper = function() {};
jQuery(function($){

    dropper = function() {
        dropper.paramname = 'pic';
        dropper.maxfiles = 1;
        dropper.maxfilesize = 2;
        dropper.url = '';///shop/dashboard/product/upload
        dropper.errors = ["BrowserNotSupported", "TooManyFiles", "FileTooLarge"];

        dropper.uploading = false;

        this.init = function(em) {

            if ($('#upload_destination').length == 1) {
                dropper.url = $('#upload_destination').val();
            } else {
                alert('could not determine your upload destination. File uploads will not work. Please contact us!')
                return;
            }

            var self = this;
            em.on({drop: self.drop, dragenter: self.dragEnter, dragover: self.dragOver,dragleave: self.dragLeave}, {self:self});
            $(document).on({'drop': self.docDrop, 'dragenter': self.docEnter, 'dragover': self.docOver,'dragleave': self.docLeave}, {self:self});
            em.on('click', '.delete-icon', function(e) {
                var del = confirm('Delete this image?');
                if (del === true) {
                    $(e.delegateTarget).find('.preview').html('<div class="upload-icon"></div>');
                }
            });

            em.on('click', '.preview', function(e) {


                var id = 'file_temp_' + $(this).data('ind');
                var f = document.getElementById(id);

                if (f === null) {
                    f = document.createElement('input');
                    f.setAttribute('type', 'file');
                    f.setAttribute('id', id);
                    document.body.appendChild(f);
                } else {
                    $(f).unbind('change');

                }
                $(f).live('change', {evt:e}, dropper.selectFiles);

                f.click();


            });

            if (jQuery('#dropbox').sortable) {
                jQuery('#dropbox').sortable({
                    placeholder:    "ui-state-highlight",
                    stop: function() {dropper.applyOrdering();}

                });
            }

        }


        dropper.selectFiles = function(e) {

            var files = null;

            if (e.target.files) {
                files = e.target.files;
            } else if (e.currentTarget.files) {
                files = e.currentTarget.files;
            }


            if (!files) {
                alert('We have detected this functionality is not supported in your browser. Please use Firefox or Google Chrome to access this feature.');
                return false;
            }

            if (files.length == 0 || files[0].name == '') {
                dropper.error(dropper.errors[0], e);
            } else {
                dropper.upload(e.data.evt, 1, files);
            }
        }

        this.dragEnter = function(e) {
            e.preventDefault();
        }

        this.dragOver = function(e) {
            e.preventDefault();
            e.data.self.docOver(e);
        }

        this.dragLeave = function(e) {
            e.stopPropagation();
        }

        this.docDrop = function(e) {
            e.preventDefault();
            e.data.self.drop(e);
            return false;
        }

        this.docEnter = function(e) {
            e.preventDefault();
            return false;
        }

        this.docOver = function(e) {
            e.preventDefault();
            return false;
        }

        this.docLeave = function(e) {
            //do nothing
        }

        this.drop = function(e) {
            e.preventDefault();

            files = e.dataTransfer.files;
            if (files === null || files === undefined) {
                dropper.error(dropper.errors[0], e);
                return false;
            }

            files_count = files.length;
            dropper.upload(e, files_count, files);
            return false;

        }

        dropper.sendIE = function(file, em) {
            dropper.sendXHRDeprecated(file, em);
        }

        dropper.upload = function(e, files_count, files) {
            if (!files) {
                dropper.error(dropper.errors[0], e);
                return false;
            }

            dropper.files_done = 0;
            dropper.files_rejected = 0;

            if (files_count > dropper.maxfiles) {
                dropper.error(dropper.errors[1], e);
                return false;
            }




            dropper.files_count = files_count;
            dropper.files = files;



            for (var i=0; i<dropper.files_count; i++) {
                try {

                    if (dropper.beforeEach(dropper.files[i]) != false) {

                        if (i === dropper.files_count) return;



                        var reader = new FileReader(),
                            max_file_size = 1048576 * dropper.maxfilesize;

                        reader.index = i;
                        if (files[i].size > dropper.max_file_size) {
                            dropper.error(dropper.errors[2], dropper.files[i], i);
                            filesRejected++;
                            return;
                        }


                        if ($(e.currentTarget).hasClass('preview')) {
                            $(e.currentTarget).html('');
                        } else {
                            $(e.currentTarget).find('.preview').remove('');
                        }

                        var ua = navigator.userAgent.toLowerCase();
                        if (ua.indexOf('msie') > -1) {
                            //this supports ie10 - anything else doesn't support file API
                            dropper.sendIE(files[i], e);
                        } else {

                            reader.onloadend =
                                (  function(file) {
                                    return function(evt) {
                                        //dropper.send(evt, file, e);
                                        dropper.send(evt, e);
                                    };
                                })(e);

                            reader.readAsBinaryString(dropper.files[i]);
                        }


                    } else {
                        dropper.files_rejected++;
                    }
                } catch(err) {
                    dropper.error(dropper.errors[0], e);
                    return false;
                }
            }
        }

        dropper.beforeEach = function(file) {

            if(!file.type.match(/^image\//)){
                overlay.setHeader('Oops');
                overlay.setBody('Only images (jpg, png, gif) are allowed');
                overlay.populateInner();
                // Returning false will cause the
                // file to be rejected
                return false;
            }

            if (file.size > 500000) {
                overlay.setHeader('Oops');
                overlay.setBody('Your file is a little too big to upload. We recommend uploading a maximum size of 500KB');
                overlay.populateInner();
                return false;
            }

        }


        dropper.sendXHR = function(file, reader_e, em) {
            var xhr = new XMLHttpRequest(),
                upload = xhr.upload,
                index = reader_e.target.index,
                start_time = new Date().getTime(),
                boundary = '------multipartformboundary' + (new Date).getTime(),
                builder;

            newName = file.name;
            if (typeof newName === "string") {
                builder = dropper.getBuilder(newName, reader_e.target.result, boundary);
            } else {
                builder = dropper.getBuilder(file.name, reader_e.target.result, boundary);
            }

            upload.index = index;
            upload.file = file;
            upload.downloadStartTime = start_time;
            upload.currentStart = start_time;
            upload.currentProgress = 0;
            upload.startData = 0;

            upload.addEventListener("progress", dropper.progress, false);
            if (dropper.url.substr(-1) != '/') {
                dropper.url += '/';
            }

            xhr.open("POST", dropper.url, true);

            xhr.setRequestHeader('content-type', 'multipart/form-data; boundary=' + boundary);

            xhr.sendAsBinary(builder);



            // xhr.sendAsBinary(builder);
            dropper.uploadStarted(index, file, em);


            xhr.onload = function() {

                var data = JSON.parse(xhr.responseText);

                if ((data && !data.error || data.error === false ) && xhr.responseText) {

                    var result = dropper.uploadFinished(em, file, jQuery.parseJSON(xhr.responseText));
                    //only allowing 1 file per box!
                    //dropper.files_done++;
                    //if (result === false) dropper.stop_loop = true;
                } else if (data && data.error && data.error === true) {
                    dropper.uploadFinished(em, file, data);

                 //   alert('There was an error uploading your image. Please try uploading it again.');
                }
            };

        }

        dropper.sendXHRDeprecated = function(file, em) {
            //safari

            var xhr2 = {};

            if (window.XMLHttpRequest) {
                xhr2 = new XMLHttpRequest();
            }
            else {
                try {
                    xhr2 = new ActiveXObject("MSXML2.XMLHTTP.3.0");
                }
                catch(ex) {
                    alert('Could not complete your request. Please Upgrade your browser - or use Chrome or Firefox, we know these work!');
                    return false;
                }
            }


            xhr2.upload.addEventListener("progress", dropper.progress, false);


            xhr2.open("POST", dropper.url, true);
            xhr2.setRequestHeader("X-FILENAME", file.name);

//                    var data2 = new FormData();
//                    data2.append(file);

            xhr2.send(file);

            dropper.uploadStarted(0, file, em);

            xhr2.onload = function(e) {





                var data = JSON.parse(xhr2.responseText);


                if ((data && !data.error || data.error === false ) && xhr2.responseText) {

                    var result = dropper.uploadFinished(em, file, jQuery.parseJSON(xhr2.responseText));
                    //only allowing 1 file per box!
                    //dropper.files_done++;
                    //if (result === false) dropper.stop_loop = true;
                } else if (data && data.error && data.error === true) {
                    dropper.uploadFinished(em, file, data);
                    //alert('There was an error uploading your image. Please try uploading it again.');
                }
            };

        }



        dropper.send = function(reader_e, em) {

            if (reader_e.target.index == undefined) {
                reader_e.target.index = dropper.getIndexBySize(reader_e.total);
            }
            var file = dropper.files[reader_e.target.index];


            var ua = navigator.userAgent.toLowerCase();
            if (ua.indexOf('safari') != -1) {
                if(ua.indexOf('chrome') > -1) {
                    dropper.sendXHR(file, reader_e, em);
                } else {
                    dropper.sendXHRDeprecated(file, em);
                }
            } else {
                dropper.sendXHR(file, reader_e, em);
            }

        }

        dropper.getIndexBySize = function(size) {
            for (var i=0; i < dropper.files_count; i++) {
                if (dropper.files[i].size == size) {
                    return i;
                }
            }

            return undefined;
        }


        dropper.getBuilder = function(filename, filedata, boundary) {
            var dashdash = '--',
                crlf = '\r\n',
                builder = '';

//            $.each(opts.data, function(i, val) {
//                if (typeof val === 'function') val = val();
//                builder += dashdash;
//                builder += boundary;
//                builder += crlf;
//                builder += 'Content-Disposition: form-data; name="'+i+'"';
//                builder += crlf;
//                builder += crlf;
//                builder += val;
//                builder += crlf;
//            });

            builder += dashdash;
            builder += boundary;
            builder += crlf;
            builder += 'Content-Disposition: form-data; name="'+dropper.paramname+'"';
            builder += '; filename="' + filename + '"';
            builder += crlf;

            builder += 'Content-Type: application/octet-stream';
            builder += crlf;
            builder += crlf;

            builder += filedata;
            builder += crlf;

            builder += dashdash;
            builder += boundary;
            builder += dashdash;
            builder += crlf;
            return builder;
        }

        dropper.progress = function() {
            dropper.uploading = true;
//            var pc = parseInt(100 - (e.loaded / e.total * 100));
  //          $(em.currentTarget).find('.progress').css('width', pc + "%");
        }

        dropper.uploadStarted = function(index, file, em) {
            dropper.createProgressBar(em);
            dropper.uploading = true;
            $(em.currentTarget).find('.progress').show();
        }

        dropper.createProgressBar = function(em) {
            var template =
                '<div class="progressHolder" style="height:100px">'+
                '<div class="progress" style="display:none"></div>'+
                '</div>';
            var tmpl = $(template);

            if ($(em.currentTarget).hasClass('preview')) {
                $(em.currentTarget).parent(0).next('.message').slideUp();
                $(em.currentTarget).html('').append(tmpl);
//                preview.appendTo($(dropper.e.currentTarget));
            } else {
                $(em.currentTarget).next('.message').slideUp();
                if ($(em.currentTarget).find('.preview').length === 0) {
                    $(em.currentTarget).append('<div class="preview"></div>');
                    tmpl.appendTo($(em.currentTarget).find('.preview'));
                } else {
                    tmpl.appendTo($(em.currentTarget));
                }
            }
        }

        dropper.createImage = function(file, em) {
            var template =
                '<span class="imageHolder">'+
                '<img />'+
                '<span class="uploaded"></span>'+
                '</span>';

            var tmpl = $(template),
                image = $('img', tmpl);

            var reader = new FileReader();

            image.width = 100;
            image.height = 100;

            reader.onload = function(e){

                // e.target.result holds the DataURL which
                // can be used as a source of the image:

                image.attr('src',e.target.result);
            };

            // Reading the file as a DataURL. When finished,
            // this will trigger the onload function above:
            reader.readAsDataURL(file);

            $.data(file, tmpl);

            if ($(em.currentTarget).hasClass('preview')) {
                $(em.currentTarget).parent(0).next('.message').slideUp();
                $(em.currentTarget).html('').append(tmpl);
//                preview.appendTo($(dropper.e.currentTarget));
            } else {
                $(em.currentTarget).next('.message').slideUp();
                if ($(em.currentTarget).find('.preview').length === 0) {
                    $(em.currentTarget).append('<div class="preview"></div>');
                    tmpl.appendTo($(em.currentTarget).find('.preview'));
                } else {
                    tmpl.appendTo($(em.currentTarget));
                }
            }

            // Associating a preview container
            // with the file, using jQuery's $.data():
        }

        dropper.uploadFinished = function(em, file, response) {

            if (!response.error || response.error === false) {
                dropper.createImage(file, em);
            }


            dropper.uploading = false;


            $(em.currentTarget).find('.progress').fadeOut('normal', function() {$(em.currentTarget).find('.progressHolder').remove();});
            if (response != undefined && response.status) {

                var preview = {};
                if ($(em.currentTarget).hasClass('preview')) {
                    preview = $(em.currentTarget);
                } else {
                    preview = $(em.currentTarget).find('.preview');
                }

                if (response.error === true) {
                    //$(em.currentTarget).find('.preview').remove();
                    $(em.currentTarget).append('<div class="upload-icon"></div>');

                    preview.append('<div></div>');

                    showMessage(response.status, em);
                } else if (response.data.saved_name) {


                    if (preview.find('input[name="uploaded_pic[]"]').length == 1) {
                        preview.find('input[name="uploaded_pic[]"]').val(response.data.saved_name);
                    } else {
                        preview.append('<input type="hidden" name="uploaded_pic[]" value="' + response.data.saved_name + '">')
                    }

                    if (preview.find('input.image-ordering').length == 1) {
                        preview.find('input.image-ordering').prop('name', 'image_ordering[' + response.data.saved_name + ']');
                    } else {
                        preview.append('<input type="hidden" class="image-ordering" name="image_ordering[' + response.data.saved_name + ']" value="0">')
                    }

                    dropper.applyOrdering();

                    var id = 'file_temp_' + preview.data('ind');

                    var f = document.getElementById(id);
                    if (f !== undefined) {
                        $(f).remove();
                    }

                }
            }
        },

        dropper.applyOrdering = function() {
            $('.photo-box').each(function(j) {
                if ($(this).find('input.image-ordering').length === 0) {
                    $(this).find('.preview').append('<input type="hidden" class="image-ordering" name="image_ordering[]" value="' + (j+1) + '">');
                } else {
                    $(this).find('input.image-ordering').val(j+1);
                    $(this).find('input.child-image-ordering').val(j+1);
                }
            });
        }

        dropper.error = function(err, e) {
            switch(err) {
                case 'BrowserNotSupported':
                    showMessage('We have detected this functionality is not supported in your browser. Please use Firefox or Google Chrome to access this feature, or upgrade your browser to the latest version.', e);
                    break;
                case 'TooManyFiles':
                    alert('Too many files! Please select 5 at most! (configurable)');
                    break;
                case 'FileTooLarge':
                    alert(file.name+' is too large! Please upload files up to 2mb (configurable).');
                    break;
                default:
                    alert(err);
                    break;
            }
        }



//            this.em.filedrop({
//                // Called before each upload is started
//                beforeEach: function(file){
//                    if(!file.type.match(/^image\//)){
//                        alert('Only images are allowed!');
//                        // Returning false will cause the
//                        // file to be rejected
//                        return false;
//                    }
//                },
//                uploadStarted:function(ind, file, len){
//
//                    console.log($(this));
//
//                    $('.photo-box').find('.progress').width(0);
//                    console.log($(this.data.l).html());
//                    createImage(ind, file);
//                }
//            });


    };

//    $('.photo-box').each(function() {
//        var e = new dropper();
//        e.init($(this));
//    });

	
	
	function showMessage (msg, e) {
        alert('Oops, there was an error: ' + msg + '. Please try again');
//        $(e.currentTarget).find('.message').html(msg).slideDown();
    }


    $('.photo-box').each(function(k) {
        var e = new dropper();
        $(this).find('.preview').data('ind', k);
        e.init($(this));
    });

});

