var video_click_detection = {
    hovering:   false,
    playing:    false,
    init:       function() {
        document.getElementById('interview_<?php echo get_the_ID() ?>').onmouseover = function() {
            video_click_detection.hovering = true;
        };

        document.getElementById('interview_<?php echo get_the_ID() ?>').onmouseout = function() {
            video_click_detection.hovering = false;
        };

        video_click_detection.register_click_watcher();
    },
    _click:  function() {
        console.log('click');
        if (video_click_detection.hovering === true) {
            console.log('hovering');
            if (video_click_detection.playing === false) {
                console.log('playing now');
                video_click_detection.playing = true;
                document.getElementById('interview_title-<?php echo get_the_ID() ?>').style.display = 'none';
            } else {
                console.log('finished playing now');
                video_click_detection.playing = false;
                document.getElementById('interview_title-<?php echo get_the_ID() ?>').style.display = 'block';
            }
        }
        console.log('end cycle');
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