jQuery(function($) {
    jQuery('form#store_form').submit(function(e) {

        if ($(this).data('e').hasClass('publish')) {
            document.getElementById('enabled_yes').checked = true;
        } else if ($(this).data('e').hasClass('unpublish')) {
            document.getElementById('enabled_no').checked = true;
        }


        if (true === dropper.uploading) {
            var c = confirm('Your image has not finished uploading. If you choose to continue, you may lose the  image. Continue?');
            if (false === c) {
                return false;
            }
        }
    });

    $('#store_form').on('click', 'input[type="submit"]', function(e) {
        $('#store_form').data('e', $(this));
    });
});

