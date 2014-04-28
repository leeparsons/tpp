<?php
/**
 * User: leeparsons
 * Date: 26/04/2014
 * Time: 20:31
 */


class TppInterviewsAdminControllerDefault {


    /*
     * Adds the admin meta boxes
     */
    public static function addMetaBoxes()
    {

        add_meta_box(
            'tpp_interviews_datetime',
            'Interview Date and Time',
            array('TppInterviewsAdminControllerDefault', 'renderDateTimeMetaBox'),
            'tpp_interview',
            'normal',
            'default'
        );

        add_meta_box(
            'tpp_interviews_video',
            'Interview Video Embed Code',
            array(
                'TppInterviewsAdminControllerDefault',
                'renderVideoMetaBox'
            ),
            'tpp_interview',
            'normal',
            'default'
        );
    }


    public static function renderDateTimeMetaBox($post, $metabox_item)
    {

        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');


        include_once TPP_INTERVIEWS_PLUGIN_DIR . 'models/interview.php';

        $interview = TppInterviewModel::getInstance()->setData(array(
            'post_id'   =>  $post->ID
        ))->load();

        ?>
        <label for="interview_date">Date:
            <input type="text" id="interview_date" name="interview_date" value="<?php echo $interview->getDate('d-m-Y'); ?>"/>
        </label>
        <br>
        <br>
        <label for="interview_start_time">Start Time (24 hour):
            <input type="time" value="<?php echo $interview->start_time ?>" name="interview_start_time" id="interview_start_time">
        </label>
        <br>
        <br>
        <label for="interview_end_time">End Time (24 hour):
            <input type="time" value="<?php echo $interview->end_time ?>" name="interview_end_time" id="interview_end_time">
        </label>
        <script>
            jQuery(function() {
                jQuery('#interview_date').datepicker({
                    dateFormat : 'dd-mm-yy'
                });
            });
        </script>
    <?php

    }

    public static function renderVideoMetaBox($post)
    {


        include_once TPP_INTERVIEWS_PLUGIN_DIR . 'models/interview.php';

        $interview = TppInterviewModel::getInstance($post->ID)->load();


        ?>
        <label>
            Enter The Video Embed Code (use the embed code available as an iframe):
            <textarea style="width:98%;height:200px;" name="interview_video" id="interview_video"><?php

                echo esc_textarea($interview->video);

                ?></textarea>
        </label>
        <span id="preview_vd" class="button button-controls">Preview</span>
        <br><br>
        <div id="video_preview"><?php echo $interview->video ?></div>

        <script>

            var tpp_vd_preview = {
                vd:         "<?php echo esc_textarea($interview->video) ?>",
                em:         {},
                preview_em: {},
                btn:        {},
                init:       function() {

                    this.em = document.getElementById('interview_video');
                    this.preview_em = document.getElementById('video_preview');
                    this.btn = document.getElementById('preview_vd');
                    this.btn.onclick = tpp_vd_preview.preview;
                },
                preview:    function() {
                    if (tpp_vd_preview.em.value != tpp_vd_preview.vd) {
                        tpp_vd_preview.preview_em.innerHTML = tpp_vd_preview.em.value;
                    }
                }
            };

            tpp_vd_preview.init();


        </script>

    <?php
    }


    public static function savePost($post_id = 0)
    {

        // If this is just a revision, don't send the email.
        if ( wp_is_post_revision( $post_id ) ) {
            return;
        }

        $post = get_post($post_id);

        if ($post->post_type == 'tpp_interview') {

            include_once TPP_INTERVIEWS_PLUGIN_DIR . 'models/interview.php';

            if (false !== ($interview = TppInterviewModel::getInstance($post_id)->readFromPost())) {
                $interview->save();
            }


        }

    }

    public static function renderNotices()
    {

        include_once TPP_INTERVIEWS_PLUGIN_DIR . 'models/interview.php';

        $notices = TppInterviewModel::getInstance()->getNotices();

        if (count($notices) > 0) {
            foreach ($notices as $notice): ?>
                <div class="error"><?php

                    echo __($notice);

                    ?></div>
            <?php endforeach;
        }

        TppInterviewModel::getInstance()->deleteNotices();

    }


}