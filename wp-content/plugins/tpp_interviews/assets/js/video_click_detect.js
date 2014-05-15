var video_click_detection = {
    hovering:   false,
    playing:    false,
    init:       function() {

        video_click_detection._id = document.getElementById('video_id').value;

        document.getElementById('interview_' + video_click_detection._id).onmouseover = function() {
            video_click_detection.hovering = true;
        };

        document.getElementById('interview_' + video_click_detection._id).onmouseout = function() {
            video_click_detection.hovering = false;
        };

        video_click_detection.register_click_watcher();

        setInterval(video_click_detection._watcher, 100);

    },
    _watcher: function() {
            var cw = document.getElementById('interview_media-' + video_click_detection._id).clientWidth;
            var ch = document.getElementById('interview_media-' + video_click_detection._id).clientHeight;

            var ratio = cw / ch;

            var new_w = 0;
            var padding = 0;

            if (cw < 475 && ch < 356) {
                padding = (356 - ch) / 2;
            }

            document.getElementById('interview_media_wrap-' + video_click_detection._id).style.padding = padding + 'px 0px';


    },
    _click:  function() {
        if (video_click_detection.hovering === true) {
            if (video_click_detection.playing === false) {
                video_click_detection.playing = true;
                document.getElementById('interview_title-' + video_click_detection._id).style.display = 'none';
            } else {
                video_click_detection.playing = false;
                document.getElementById('interview_title-' + video_click_detection._id).style.display = 'block';
            }
        }
    },
    register_click_watcher:   function() {
        if(typeof window.addEventListener != 'undefined') {
            window.addEventListener('blur', video_click_detection._click, false);
            window.addEventListener('focus', video_click_detection._click, false);
        } else if (typeof document.addEventListener != 'undefined') {
            document.addEventListener('blur', video_click_detection._click, false);
            document.addEventListener('focus', video_click_detection._click, false);
        } else if (typeof window.attachEvent != 'undefined') {
            window.attachEvent('blur', video_click_detection._click);
            window.attachEvent('focus', video_click_detection._click);
        } else {
            if (typeof window.blur == 'function') {
                window.blur = video_click_detection._click();
            } else {
                window.onblur = video_click_detection._click();
            }
            if (typeof window.focus == 'function') {
                window.focus = video_click_detection._click();
            } else {
                window.onfocus = video_click_detection._click();
            }
        }
    }
}

video_click_detection.init();


