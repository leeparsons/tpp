<?php

get_header();
wp_enqueue_script('file_uploads_engine', '/assets/js/jquery.filedrop.js', array('jquery'), '1', true) ?>
<?php wp_enqueue_script('file_uploads', '/assets/js/file_upload-ck.js', array('jquery'), '1', true) ?>
<?php wp_enqueue_style('uploads', TPP_STORE_PLUGIN_URL . '/site/assets/css/dashboard/upload.css'); ?>
<form method="post" enctype="multipart/form-data" id="mentor_form">

    <input type="hidden" id="upload_destination" value="/shop/dashboard/mentor/upload/">

    <input type="hidden" name="m" value="<?php echo $mentor->mentor_id ?>">
    <input type="hidden" name="sid" value="<?php echo $store->store_id ?>">

    <header class="wrap">
        <h1><?php if (intval($mentor->mentor_id) > 0): ?>Edit Mentor: <?php echo $mentor->mentor_name ?><?php else: ?>Create a new Mentor<?php endif; ?></h1>
        <input type="submit" value="Save" class="btn btn-primary">
        <a href="/shop/dashboard/mentors" class="btn btn-default">Cancel</a>
        <br><br>
    </header>

    <?php TppStoreMessages::getInstance()->render() ?>

    <aside class="wrap">


        <fieldset>
            <legend>About the mentor</legend>
            <div class="form-group">
                <label for="mentor_name">Mentor Name</label>
                <input type="text" class="form-control" id="mentor_name" name="mentor_name" value="<?php echo $mentor->mentor_name ?>" placeholder="Mentor Name">
            </div>

            <div class="form-group">

                <label for="mentor_company">Mentor Company</label>
                <input type="text" class="form-control" id="mentor_company" name="mentor_company" value="<?php echo $mentor->mentor_company ?>" placeholder="Mentor Company">

            </div>

            <div class="form-group">

                <label for="mentor_bio">Mentor Bio/Description</label>
                <textarea name="mentor_bio" id="mentor_bio" class="form-control" rows="5"><?php echo $mentor->getBio(); ?></textarea>

            </div>

        </fieldset>

        <fieldset>
            <legend>Specialities</legend>
            <div class="form-group">
            <pre>Please list the top three areas of specialism.

These will appear in mentor listings so try to make them stand out. Examples:

"Website SEO"
"Album Design"
"Wedding Photography"</pre>
            </div>
            <div class="form-group">

                <div class="control-section">
                    <label for="specialism_one">First Specialism</label>
                    <input type="text" name="specialism_one" value="<?php echo $mentor->getSpecialism()->specialism_one ?>" id="specialism_one" placeholder="Main specialism" class="form-control">
                </div>
            </div>

            <div class="form-group">


                <div class="control-section">
                    <label for="specialism_two">Second Specialism</label>
                    <input type="text" name="specialism_two" value="<?php echo $mentor->getSpecialism()->specialism_two ?>" id="specialism_two" placeholder="Secondary specialism" class="form-control">
                </div>

            </div>
            <div class="form-group">

                <div class="control-section">
                    <label for="specialism_three">Third Specialism</label>
                    <input type="text" name="specialism_three" value="<?php echo $mentor->getSpecialism()->specialism_three ?>" id="specialism_three" placeholder="Third specialism" class="form-control">
                </div>

            </div>
        </fieldset>


        <fieldset>
            <legend>Location</legend>
            <div class="form-group">
                <label for="mentor_city">Mentor's nearest city</label>
                <input type="text" class="form-control" name="mentor_city" id="mentor_city" value="<?php echo $mentor->mentor_city ?>" placeholder="Mentor City">
            </div>

            <div class="form-group">

                <label for="mentor_country">Country where mentor is based</label>

                <?php

                $select_name = 'mentor_country';

                $selected_value = $mentor->mentor_country;

                include TPP_STORE_PLUGIN_DIR . 'templates/countries.php';

                ?>

            </div>
        </fieldset>


        <fieldset>
            <legend>Mentor Image</legend>
            <div class="form-group">
                <p class="wp-message">Upload an image for your mentor. The ideal image size is 250 pixels by 250 pixels but we will resize your image if it's larger.</p>
                <div class="form-group">
                    <div id="dropbox" class="store-dropbox">
                        <div class="drop-wrap">
                            <div class="photo-box">
                                <div class="handle" style="background:none"></div>
                                <div class="delete-icon"></div>
                                <div class="preview">
                                    <?php if (intval($mentor->mentor_id) > 0 && $mentor->src): ?>
                                        <img src="<?php echo $mentor->getSrc() ?>">
                                        <input type="hidden" name="original_pic[]" value="<?php echo $mentor->src ?>">
                                    <?php else: ?>
                                        <div class="upload-icon"></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="message"></div>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>



        <fieldset>
            <legend>&nbsp;</legend>
            <div class="form-group bt">
                <input type="submit" value="Save" class="btn btn-primary">
                <a href="/shop/dashboard/mentors" class="btn btn-default">Cancel</a>
            </div>
        </fieldset>



    </aside>
</form>
<?php

get_footer();
?>