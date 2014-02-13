jQuery(function($) {
    $('fieldset').each(function(k) {
        $('.sidemenu').find('li').eq(k).data('ind', k+1);
        $(this).data('ind', k);
        if (k > 0 && !$(this).hasClass('notoggle')) {
            $(this).slideUp();
        }
        if (k < $('.sidemenu').find('li').length - 1) {
            var a = document.createElement('a');
            $(a).addClass('btn btn-primary');
            $(a).data('ind', k);
            $(a).text('next');

            $(a).on('click', function() {
                $('.sidemenu').find('li').eq($(this).data('ind')*1 + 1).click();
            });
            var d = document.createElement('div');
            $(d).addClass('wrap')
                .append(a);

            $(this).append(d);
        }
    });

    $('a.step').on('click', function(e) {
        e.preventDefault();
        var self = $(this);
        $('.aside-25').find('li').eq(self.data('step')).click();
    });

    $('.sidemenu').on('click', 'li', function() {
        $('fieldset').not('.notoggle').slideUp();
        $('.aside-25').find('.active').removeClass('active');
        $(this).addClass('active');
        $('fieldset').eq($(this).data('ind')-1).slideDown();
        $('html, body').animate({
            scrollTop: $('.sidemenu').offset().top
        }, 100);

    });
});

