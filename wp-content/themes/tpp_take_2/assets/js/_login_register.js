//
//This is used for overlays when clickong on the menu buttons to have seamless logins.
//
 jQuery(function($) {
    var lrp = {

        overlay: false,
        ajax_call: false,

        register_form: '',

        init: function() {
            return;
            $('.menu-btns').on('click', '.sell, .signup-btn, .login-btn', lrp.click);
            var overlay = document.createElement('div');
            lrp.overlay = $(overlay);
            lrp.overlay.prop('id', 'overlay');
        },

        click: function(e) {
            e.preventDefault();

            if (typeof lrp.ajax_call == 'object') {
                lrp.ajax_call.abort();
            }

            if ($('#overlay').length == 0) {
                $('body').append(lrp.overlay);
                lrp.overlay.on('click', lrp.close);
            } else {
                lrp.overlay.css('z-index', 5001).fadeIn();
            }

//            if ($('.overlay-wrap').length == 0) {
//                var wrap = $('<div></div>');
//                wrap.prop('class', 'overlay-wrap');
//                $('body').append(wrap);
//            } else {
//                var wrap = $('.overlay-wrap');
//                wrap.css('z-index', 5001);
//            }

            if (lrp.register_form !== '') {
                lrp.populateInner(lrp.register_form);
            } else {
                lrp.ajax_call = $.ajax({
                    url: '/shop/store_register/component',
                    success: function(data) {
                        lrp.register_form = data;
                        lrp.populateInner(data);
                    }
                });
            }




        },

        populateInner: function(data) {
            var inner_wrap = $('<div></div>');
            inner_wrap.prop('class', 'overlay-inner');
            //wrap.on('click', lrp.close).append(inner_wrap);
            inner_wrap.css('z-index', 5003);
            inner_wrap.html(data);
            $('body').append(inner_wrap);
        },

        close: function(e) {

            if (!$(e.delegateTarget).hasClass('overlay-wrap')) {
                e.preventDefault();
                e.stopImmediatePropagation();
                e.stopPropagation();
                lrp.overlay.fadeOut().css('z-index', 1);
                $('.overlay-inner').fadeOut('fast', function() {$('.overlay-inner').remove();});
            }
        }

    }
    lrp.init();
});

