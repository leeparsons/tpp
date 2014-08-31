<div class="form-group product-type-group <?php if ((intval($product->product_type) != 1 && intval($product->product_type) != 4)) {echo 'hidden';} ?>" id="download_group">

    <h3>File details for download</h3>

    <?php if (isset($download_text)): ?>
        <pre><?php echo $download_text ?></pre>
    <?php endif; ?>

    <div class="form-group">
        <strong class="wrap">Is your file:</strong>
        <label class="wrap"><input type="radio" <?php

            if (false === ($download_exists = $product->downloadExists())) {
                echo ' checked="checked" ';
            }

            ?> value="1" name="download_location">Hosted elsewhere (enter the url location for the download)</label>
        <label class="wrap"><input type="radio" <?php

            if (true === $download_exists) {
                echo ' checked="checked" ';
            }

            ?> value="2" name="download_location">Hosted with us (upload your file)</label>
    </div>

    <div class="wrap" id="elsewhere" <?php if ($download_exists === true): ?>style="display:none;"<?php endif; ?>>
        <pre>Enter the url for download (files hosted elsewhere)</pre>
        <input type="text" placeholder="Enter the url where your download can be accessed if hosted elsewhere" class="form-control" name="download_elsewhere" id="download_elsewhere" value="<?php echo $product->product_type == 1 && stripos($product->product_type_text, 'http://') !== false?$product->product_type_text:'' ?>">
    </div>

    <div class="wrap" id="withus" <?php if ($download_exists === false): ?>style="display:none;"<?php endif; ?>>
        <pre>Select file for download</pre>
        <!--input name="download" id="file_download" type="file" class="form-control"-->
        <div class="wrap">
            <div class="upload-file-wrap">
                <div class="handle">Choose a file</div>
                <a href="#" id="upload_file" class="upload-icon <?php

                if ($download_exists === true) {

                    $f = new TppStoreLibraryFile();
                    $f->setFile($product->product_type_text);
                    $extension = $f->getExtension();
                    switch ($extension) {
                        case 'pdf':
                        case 'zip':
                        case 'doc':
                        case 'docx':
                        case 'psd':
                        case 'jpg':
                            echo $extension;
                            break;
                        case 'jpeg':
                            echo 'jpg';
                            break;
                        default:
                            echo 'generic';
                            break;
                    }

                    unset($extension);

                    unset($f);

                }

                ?>"></a>
            </div>
            <div id="progress_bar"><div id="bar"></div></div>
        </div>


    </div>
    <?php if (intval($product->product_type) == 1 || intval($product->product_type) == 4) { ?>
        <?php if($product->product_type_text != ''): ?>
            <div class="wrap">
                <strong>Current Download Link:</strong>
                <a href="<?php echo $product->getDownloadUrl() ?>" target="_blank"><?php echo $product->product_type_text ?></a>
                <input type="hidden" name="original_download" value="<?php echo $product->product_type_text?>">
                <br><br><br>
            </div>
        <?php endif; ?>
        <input type="hidden" name="original_download" id="original_download" value="<?php echo $product->product_type_text; ?>">

    <?php } ?>

</div>

<script>

    var input = document.createElement('input');
    input.setAttribute('type', 'file');
    input.setAttribute('name', 'upload_file');
    input.filename = this.value;

    document.getElementById('upload_file').onclick = function(evt) {
        evt.preventDefault();
        input.click();
    }


    input.onchange = function() {
        var file = this.files[0];
        if (file) {

            var fileSize = 0;
            if (file.size > 15 * 1024 * 1024) {
                alert('Please select a file below 15MB.');
                this.value = '';
                return false;
            }
        }


        tppSend(file);
    }

    function tppUploadComplete(data)
    {
    }

    function tppUploadFailed(data)
    {

    }

    function tppUploadCanceled(data)
    {

    }

    var tpp_upload_t_o = 0;

    function tppUploadProgress(e)
    {
        clearTimeout(tpp_upload_t_o);
        document.getElementById('progress_bar').style.display = 'block';

        if (e.lengthComputable) {
            var percentComplete = Math.round(e.loaded * 100 / e.total);
            document.getElementById('bar').style.width = percentComplete + '%';
            if (percentComplete == 100) {
                tpp_upload_t_o = setTimeout(function() {
                    document.getElementById('bar').style.width = '0%';
                    document.getElementById('progress_bar').style.display = 'none';
                }, 1000);
            }
        }
    }

    function tppSend(file)
    {

        var extension = file.name.split('.').pop();



        var fd = new FormData();
        fd.append("upload_file", file);

        fd.processData = false;
        fd.contentType = false;

        var xhr = new XMLHttpRequest();
        xhr.upload.addEventListener("progress", tppUploadProgress, false);
        //    xhr.addEventListener("load", tppUploadComplete, false);
        //      xhr.addEventListener("error", tppUploadFailed, false);
//        xhr.addEventListener("abort", tppUploadCanceled, false);
        xhr.open("POST", "/shop/dashboard/product/upload_file/", false);



        xhr.send(fd);
        xhr.onload = function() {



            var data = JSON.parse(xhr.responseText);

            if (data.error === false) {

                switch (extension) {
                    case 'pdf':
                    case 'zip':
                    case 'doc':
                    case 'docx':
                    case 'psd':
                    case 'jpg':
                        break;
                    case 'jpeg':
                        extension = 'jpg';
                        break;
                    default:
                        extension = 'generic';
                        break;
                }
                document.getElementById('upload_file').setAttribute('class', 'upload-icon ' + extension);

                overlay.setHeader('Success!');
                overlay.setBody('File uploaded');
                overlay.populateInner()
                if (document.getElementById('original_download')) {
                   document.getElementById('original_download').value = data.data;
                }
            } else {
                document.getElementById('upload_file').setAttribute('class', 'upload-icon');
                overlay.setHeader('Oops');
                overlay.setBody('We had a problem uploading your file. Please contact us: ' + data.status + ', ' + data.data);
                overlay.populateInner();
            }

        }

        return;

        var xhr = new XMLHttpRequest(),
            upload = xhr.upload,
            start_time = new Date().getTime(),
            boundary = '------multipartformboundary' + (new Date).getTime(),
            builder;


        var newName = file.name;
        if (typeof newName === "string") {
            builder = getBuilder(newName, reader_e.result, boundary);
        } else {
            builder = getBuilder(file.name, reader_e.result, boundary);
        }

        upload.file = file;
        upload.downloadStartTime = start_time;
        upload.currentStart = start_time;
        upload.currentProgress = 0;
        upload.startData = 0;

//            upload.addEventListener("progress", dropper.progress, false);
//            if (dropper.url.substr(-1) != '/') {
//                dropper.url += '/';
//            }

        xhr.open("POST", "/shop/dashboard/product/upload_file/", true);

        xhr.setRequestHeader('content-type', 'multipart/form-data; boundary=' + boundary);

        xhr.sendAsBinary(builder);


    }

    function getBuilder(filename, filedata, boundary)
    {
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
        builder += 'Content-Disposition: form-data; name="upload"';
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

    function loaded()
    {
        //handle the upload start
    }



</script>