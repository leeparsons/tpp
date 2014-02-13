jQuery(function($) {

    //get the width of the current container.

    //If the natural image width is greater than this container, then make the image width the same as this container.


    $.fn.slideHeightAdjust = function(init) {


        var container_width = $('#product_images .slides').width()*1;
        var container_height = $('#product_images .slides').height()*1;
        var w = 0;
        var h = 0;
        var scale = 0;
        var tp = 0;
        $(this).find('img').each(function(x) {
            var img = $(this);
            img.data('ind', x);
            w = $(this).get(0).clientWidth;

            h = $(this).get(0).clientHeight;
            if (h > container_height) {
                scale = container_height / h;
                h = container_height;
                w = scale * w;
                tp = 0;
            } else {
                tp = (container_height - h)/2;
            }

            $(this).css({width:w,height:h, top:tp});

            w = $(this).get(0).clientWidth;

            if (w < container_width) {
                $(this).css({left:(container_width - w)/2});
            }

            if (init === true && x > 0) {
                img.fadeOut().removeClass('vhidden');
            }

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
});











window.onresize = function() {
    jQuery('.slides').slideHeightAdjust(false);
}



