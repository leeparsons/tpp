jQuery(function($) {

    //get the width of the current container.

    //If the natural image width is greater than this container, then make the image width the same as this container.


    $.fn.slideHeightAdjust = function(init) {
        $(this).find('img').each(function(x) {
            if (init === true && x > 0) {
                $(this).fadeOut().removeClass('vhidden');
            }

        });
return;
        var container_width = $('#product_images .slides').width()*1;
        var container_height = $('#product_images .slides').height()*1;
        var w = 0;
        var h = 0;
        var scale = 0;
        var tp = 0;

        var imgs = $(this).find('img');


        imgs.each(function(x) {

            $(this).data('ind', x);
            if (x > 0) {
                $(this).fadeOut();
            }



            $(this).load(function() {

                var x = $(this).data('ind');
                var w = $(this).attr('width');
                var h = $(this).attr('height');

                if (w == undefined || h == undefined) {
                    w = $(this).get(0).clientWidth;
                    h = $(this).get(0).clientHeight;
                }

                var lft = false;

                //if height > wrapper height, reduce the height and set the width to auto

                if (h > container_height) {
                    $(this).css({height:container_height, width:'auto', top:0});
                    $(this).css('left', (container_width - parseInt($(this).width()))/2);

//                    scale = container_height / h;
//                    h = container_height;
//                    w = scale * w;
//                    tp = 0;
//                    lft = (container_width - w);
                } else {
                    tp = (container_height - h)/2;
                    $(this).css({'left': (container_width - parseInt($(this).width()))/2, top: tp});



                    lft = 0;
                }

               // $(this).css({width:w,height:h, top:tp});


                w = $(this).width()*1;

//                if (lft === false) {
//                    lft = container_width - w;
//                }
//
//                if (w < container_width) {
//                    $(this).css({left:lft/2});
//                }


            });

        });
//
//         img_heights = [];
//
//        var product_images_height = $('#product_images .slides').height()*1;
//        var product_images_width = $('#product_images .slides').width()*1;
//
//        $(this).find('img').each(function(x) {
//            $(this).data('ind', x);
//
//            var w = product_images_width - 40;
//
//            //figure out the scale ratio
//
//            var original_width = $(this).attr('width')*1;
//            var original_height = $(this).attr('height')*1;
//
//            original_width = $(this).get(0).clientWidth;
//            original_height = $(this).get(0).clientHeight;
//
//            if (isNaN(original_width) || isNaN(original_height)) {
//                var h = product_images_height;
//            } else {
//                var scale = original_width / w;
//                var h = original_height / scale;
//            }
//
//            if ( h > w ) {
//                //portrait so force the height to be the height while width is auto
//            }
//
//            $(this).css({width:'auto', height:h});
//
//            img_heights.push(h);
//        });

        //find the highest_height





//            return ;
//        highest_height = img_heights.sort(function(a,b){return b-a;})[0];
//
//        $('#product_images .slides').css({minHeight: highest_height + 40, height: highest_height + 40});
//
//        $('#product_images').css({minHeight: highest_height + $('#product_images .slide-navigation').height()*1})
//
//        $(this).find('img').each(function(x) {
//            var img = $(this);
//            var container_width = $('#product_images .slides').width()*1;
//            var container_height = $('#product_images .slides').height()*1;
//
//            var h = img.height()*1;
//            var w = img.width()*1;
//
//            if (isNaN(w) || isNaN(h)) {
//                w = img.attr('width')*1;
//                h = img.attr('height')*1;
//            }
//
//            //if (h < container_height && h > 0) {
////                    img.css('top', (container_height - h)/ 2);
////                } else if (h > container_height) {
////                    $('#product_images .slides').css('height', h + 40);
////                } else if (h < container_height) {
//            //}
//
//            if (w < container_width && w > 0) {
//                img.css('left', (container_width - w)/2)
//            }
//
//            if (h < container_height && h > 0) {
//                img.css('top', (container_height - h)/2);
//            } else {
//                img.css('top', 0);
//            }
//
//            if (init === true && img.data('ind') > 0) {
//                img.fadeOut().removeClass('vhidden');
//            }
//        });
    }

    $.fn.setUpSlides = function() {

        for (var x = 0; x < $(this).find('img').length; x++) {
            $(this).find('img').eq(x).data('ind', x);
        }

        $(this).on('click', 'img', function() {
            var active = $('.slide-navigation').find('.active');
            active.css('opacity', 0.5);
            $(this).css('opacity', 1).addClass('active');
            $('.slides').find('img.active').fadeOut().removeClass('active');
            $('.slides').find('img').eq($(this).data('ind')).fadeIn().addClass('active');
        });

    }

    $('.slides').ready(function() {$('.slides').slideHeightAdjust(true);});
    $('.slide-navigation').ready(function() {
        $('.slide-navigation').setUpSlides();
    });

    var email_form = false;
    var shs = $('.email-share').on('click', function() {
        if (email_form === false) {
            email_form = document.createElement('div');
            var frm = document.createElement('form');
            frm.setAttribute('action', '/shop/email_friend/');
            frm.setAttribute('id', 'email_send_form');
            var sub = document.createElement('input');
            sub.setAttribute('type', 'submit');
            sub.setAttribute('class', 'btn btn-primary');
            sub.value = 'Send';

            var err = document.createElement('div');
            err.setAttribute('id', 'email_errors');
            err.style.display = 'none';
            var gr = document.createElement('div');
            gr.setAttribute('class', 'form-group');
            frm.appendChild(err);
            var lbl = document.createElement('label');
            lbl.setAttribute('for', 'friend_email');
            lbl.innerHTML = 'Friend\s email:';
            var em = document.createElement('input');
            em.setAttribute('name', 'femail');
            em.setAttribute('placeholder', 'Friend\'s Email');
            em.setAttribute('id', 'friend_email');
            em.setAttribute('class', 'form-control');
            gr.appendChild(lbl);
            gr.appendChild(em);

            frm.appendChild(gr);

            var gr2 = document.createElement('div');
            gr2.setAttribute('class', 'form-group');
            var lbl2 = document.createElement('label');
            lbl2.setAttribute('for', 'from');
            lbl2.innerHTML = 'Your name:';
            var em2 = document.createElement('input');
            em2.setAttribute('name', 'from');
            em2.setAttribute('placeholder', 'Your Name');
            em2.setAttribute('id', 'from');
            em2.setAttribute('class', 'form-control');
            gr2.appendChild(lbl2);
            gr2.appendChild(em2);

            frm.appendChild(gr2);

            var gr3 = document.createElement('div');
            gr3.setAttribute('class', 'form-group');

            gr3.appendChild(sub);

            frm.appendChild(gr3);
            email_form.appendChild(frm);

            $('#email_send_form').live('submit', function(e) {

                e.preventDefault();
                $('#email_errors').hide().html('');
                var errors = [];
                var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                if ('' == $('#from').val().replace(/\s+/g, '')) {
                    errors.push('Please enter your name');
                }
                if (false === re.test($('#friend_email').val())) {
                    errors.push('Please make sure your friend\'s email address is valid');
                }

                if (errors.length > 0) {
                    e.preventDefault();
                    for ( var x = 0; x < errors.length; x++ ) {
                        $('#email_errors').append('<p class="wp-error">' + errors[x] + '</p>').show();
                    }
                } else {

                    $.post(
                        '/shop/email_share/',
                        {
                            'from':$('#from').val(),
                            'femail':$('#friend_email').val(),
                            'p':$('#product').val()
                        },
                        function() {
                            $('#email_send_form').find('div.form-group').slideUp();
                            $('#email_errors').html('').show().html('<p class="wp-message">Your share has been sent!</p>');
                        }
                    );
                }

            });
        } else {
            $('#email_send_form').find('div.form-group').slideDown();
        }
        overlay.setBody(email_form.innerHTML);
        overlay.setHeader('Send to a friend');
        overlay.populateInner();
    });


});











window.onresize = function() {
    jQuery('.slides').slideHeightAdjust(false);
}



