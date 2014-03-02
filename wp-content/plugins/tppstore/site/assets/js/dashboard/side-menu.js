jQuery(function($) {

    $('.dashboard-menu').find('a:not(.primary)').slideUp();
    $('.dashboard-menu').find('a.primary').each(function() {
        if ($(this).siblings('a').length > 0) {
            $(this).addClass('toggler');

            $(this).on('click', function(e) {
                e.preventDefault();
                if (!$(this).siblings('a').eq(0).is(':visible')) {
                    $(this).addClass('active');
                    $(this).siblings('a').slideDown();
                } else {
                    $(this).removeClass('active');
                    $(this).siblings('a').slideUp();
                }
            });
        }
    });
});