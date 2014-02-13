jQuery(function($) {

    var homeslides = {

        _slides:false,
        _navi:false,
        _to: 0,
        _delay: 5000,
        _duration:4500,

        init: function() {
            homeslides._slides = $('#header_slideshow');
            homeslides._navi = $('#slideshow_navigation');

            homeslides._slides.on({mouseenter:homeslides.pause, mouseleave: homeslides.play}, 'li');

            if (homeslides._slides.find('li').length > 1) {
                homeslides._slides.find('li').each(function(k) {if (k > 0) {$(this).fadeOut();$(this).find('.img').show();} else {$(this).addClass('active');}});
                homeslides._to = setTimeout(homeslides._relay, homeslides._delay);
                if (homeslides.navi) {
                    homeslides._navi.find('li').eq(0).addClass('active');
                    homeslides._navi.on('click', 'li', homeslides.rotateTo);
                }
            }
        },
        _relay: function() {
            homeslides._clearTo();
            homeslides._intv = setInterval(homeslides._rotateRight, homeslides._duration);
        },
        _rotateRight: function(ind, callback) {
            homeslides._clearTo();
            var $cur = homeslides._slides.find('li.active');

            if (ind == undefined) {
                if ($cur.next('li').length == 0) {
                    var $nxt = homeslides._slides.find('li').eq(0);
                } else {
                    var $nxt = homeslides._slides.find('li.active').next('li').eq(0);
                }
            } else {
                var $nxt = homeslides._slides.find('li').eq(ind);
            }



            var cur_ind = $cur.data('ind');
            var nxt_ind = $nxt.data('ind');

            if (homeslides._navi) {
                homeslides._navi.find('li').eq(cur_ind).removeClass('active');
                homeslides._navi.find('li').eq(nxt_ind).addClass('active');
            }

            $cur.fadeOut().removeClass('active');
            $nxt.fadeIn().addClass('active');

            if (callback != undefined) {
                callback();
            }

        },
        _clearTo: function() {
            clearTimeout(homeslides._to);
        },
        _clearIntv: function() {
            clearInterval(homeslides._intv);
        },
        rotateTo: function(e) {
            homeslides._clearIntv();
            homeslides._clearTo();
            homeslides._rotateRight($(e.currentTarget).data('ind'), function() {homeslides._relay();});
        },
        pause: function() {
            homeslides._clearTo();
            homeslides._clearIntv();
        },
        play: function() {
            homeslides._relay();
        }
    }


    homeslides.init();

});