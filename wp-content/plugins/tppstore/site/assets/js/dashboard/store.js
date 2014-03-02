jQuery(function($) {
    jQuery('form#store_form').submit(function(e) {

        if ($(this).data('e').hasClass('publish')) {
            document.getElementById('enabled_yes').checked = true;
        } else if ($(this).data('e').hasClass('unpublish')) {
            document.getElementById('enabled_no').checked = true;
        }


        var errors = [];


        if (document.getElementById('paypal_email').value.replace(/\s+/g, '') == '') {
            errors.push('Please enter your paypal address to receive payments');
        }

        if (errors.length > 0) {
            overlay.setHeader('Oops!');
            overlay.setBody(errors.join('<br>'));
            overlay.populateInner();
            return false;
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

