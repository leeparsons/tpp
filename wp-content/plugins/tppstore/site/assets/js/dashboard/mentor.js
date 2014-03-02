jQuery(function($) {

$('#mentor_form').on('submit', function(e) {

    var errors = [];

    if ($('#mentor_name').val().replace(/\s+/g, '') == '') {
        errors.push('Please enter a mentor name');
    }

    if ($('#mentor_country').val() == '') {
        errors.push('Please select the mentor\'s country');
    }


    if ($('.preview').find('img').length === 0) {
        errors.push('Please upload an image.');
    }

    var s1, s2, s3;

    s1 = document.getElementById('specialism_one').value.replace(/\s+/g, '');
    s2 = document.getElementById('specialism_two').value.replace(/\s+/g, '');
    s3 = document.getElementById('specialism_three').value.replace(/\s+/g, '');

    if (s1 == '' && s2 == '' && s3 == '') {
        errors.push('Please enter a specialism');
    }

    if (errors.length > 0) {

        e.preventDefault();

        if ($('#message').length == 0) {
            var message = $('<div id="message"></div>');
            $('.page-article-part').find('form').eq(0).prepend(message);
        } else {
            var message = $('#message');
        }

        for (var x in errors) {
            message.append('<p class="wp-error">' + errors[x] + '</p>');
        }




        overlay.setHeader('Oops, sorry there were errors');

        message = [];

        for (var x in errors) {
            message.push(errors[x]);
        }

        overlay.setBody('Please see the error messages and fill in the required fields:<br><br>' + message.join('<br>'));
        overlay.populateInner();

        return false;
    }

});

});