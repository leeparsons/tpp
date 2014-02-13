<?php

get_header();
?><?php wp_enqueue_script('file_uploads', '/assets/js/file_upload.js', 'jquery', '1', true) ?>
<?php wp_enqueue_script('file_uploads_engine', '/assets/js/jquery.filedrop.js', 'jquery', '1', true) ?>
<?php wp_enqueue_style('uploads', TPP_STORE_PLUGIN_URL . '/site/assets/css/dashboard/upload.css'); ?>
<form method="post" enctype="multipart/form-data" id="mentor_form">

    <input type="hidden" id="upload_destination" value="/shop/dashboard/mentor/upload/">

    <input type="hidden" name="m" value="<?php echo $mentor->mentor_id ?>">

    <div class="form-group">

        <label for="">Mentor Name</label>
        <input type="text" class="form-control" name="mentor_name" value="<?php echo $mentor->mentor_name ?>" placeholder="Mentor Name">
    </div>
</form>
<?php

get_footer();
?>