var fbApply = {
    fbStatus: function() {


        if (fbLogin.me > 0) {

            overlay.setHeader('Detecting Previous Applications');
            overlay.setBody('Please wait while we check your application status...');

            jQuery('.page-article').find('header').eq(0).slideUp(500, function() {
                jQuery(this).html('<h2>Welcome, ' + fbLogin.me_data.first_name + '!</h2>').slideDown();
            });

            jQuery.ajax({
                url:        '/shop/application-status/',
                type:       'post',
                data:       {fid:fbLogin.me},
                dataType:   'json',
                success: function(response) {

console.log(response);
                    if (response.status && response.status == 'success') {

                        if (response.data) {
                            if (response.data.state == 1) {
                                //successful - they are currently selling
                                overlay.setHeader('Oops!');
                                overlay.setBody('It looks as though you have already have a store with us. Please contact us if you think this is not the case.');
                                fbApply.hideAll('It looks as though you have already have a store with us. Please contact us if you think this is not the case.');
                            } else if (response.data.state == -2) {
                                //nothing so continue
                                overlay.close();
                                fbApply.replace(response.data);
                            } else if (response.data.state == -1) {
                                //declined!
                                fbApply.hideAll('It looks as though a previous application was declined. Please contact us if you think this is not the case.<br><br>You can also re apply from your account.');
                                overlay.setHeader('Oops!');
                                overlay.setBody('It looks as though a previous application was declined. Please contact us if you think this is not the case.<br><br>You can also re apply from your account.');

                            } else {
                                //in processing
                                fbApply.hideAll('We have found a store application for you on file, but it is currently pending approval. If you do not hear from us within the next 24 hours, please feel free to get in touch about your application.');
                                overlay.setHeader('Oops!');
                                overlay.setBody('We have found a store application for you on file, but it is currently pending approval. If you do not hear from us within the next 24 hours, please feel free to get in touch about your application.');
                            }
                        } else {
                            //erm, something bad!
                            fbApply.hideAll('Something unusual happened, please contact us! - looks like the response did not contain any data');
                            overlay.setHeader("Oops! That wasn't supposed to happen");
                            overlay.setBody('Something unusual happened, please contact us!');
                        }
                    } else {
                        //error occurred!
                        fbApply.hideAll('Something unusual happened, please contact us!' + response.status + 'no status?');
                        overlay.setHeader("Oops! That wasn't supposed to happen");
                        overlay.setBody('Something unusual happened, please contact us!');
                    }
                },
                error: function(response) {
                    if (response.readyState != 0) {
                        fbApply.hideAll('Opps! There was an error:<br><br>' + response.responseText);
                        overlay.setHeader('Opps! There was an error');
                        overlay.setBody(response.responseText);
                    } else {
                        overlay.close();
                    }
                }
            });

        } else {
            overlay.setHeader('Oops, something went wrong!');
            overlay.setBody('We could not detect your Facebook connection. Please try again!!');
        }
    },

    hideAll: function(str) {
        //hide the application form!
        if (str != undefined) {
            jQuery('fieldset').each(function(k) {
                if (k > 0) {
                    jQuery(this).remove();
                } else {
                    jQuery(this).html('<p>' + str + '</p>');
                }
            });
        } else {
            jQuery('fieldset').remove();
        }
    },
    replace: function(data) {


        jQuery('#facebook_register_form').parent().remove();
        jQuery('#user_email').prev('label').hide().parent().parent().removeClass('half-right');
        jQuery('#user_email').val(data.user.email).hide().after('<span>You have chosen to sign in with Facebook. If in the future you want to log in via email, we have sent your password to your email address registered with Facebook. You can manage your preferences via "Your Account" at the top of the website.</span>');
        jQuery('fieldset').eq(2).find('.form-group').eq(2).remove();
        jQuery('fieldset').eq(2).find('.form-group').eq(1).remove();
        jQuery('#user_email')
        jQuery('#u_title').val(data.user.title);
        jQuery('#f_name').val(data.user.first_name);
        jQuery('#l_name').val(data.user.last_name);

    },
    init: function() {

        if (fbLogin.me == 0) {
            if (document.getElementById('fb_connect')) {
                jQuery('#fb_connect').die('click');
                jQuery('#fb_connect').click(function(e) {
                    e.preventDefault();
                    overlay.setHeader('Connecting to Facebook');
                    overlay.setBody('Please wait while we connect you ...')
                    overlay.populateInner();

                    fbLogin.discover = false;
                    fbLogin.getFBLoginStatus(true, 'fbApply.fbStatus()');

                    return false;
                });
            }

        }

    }


}




jQuery(function($) {


        // Create the event
        $(document).on("facebook:ready", function() {
            fbApply.init();
        });

    $('div.slider').each(function(k) {
        if (k > 0) {
            $(this).slideUp();
        }

        if (k < $('div.slider').length - 1) {
            var a = document.createElement('a');
            jQuery(a).text('Next');
            a.setAttribute('class', 'btn btn-primary align-left');
            a.style.marginBottom = '20px';
            $(a).on('click', function() {
                $(this).parent().parent().next('fieldset').find('legend').data('callback', 'focusLegend()').click();
            });
            $(this).append(a);

        }
    });

    $('legend').on('click', function(e) {

        var self = $(this);

        $('div.slider').slideUp('fast');
            $('legend.active').removeClass('active');
            self.addClass('active').next('div.slider').slideDown('fast', function() {

                $('html, body').animate({
                    scrollTop: self.offset().top
                   // scrollLeft: offset.left
                }, 100);
            });



    });


    $('form#application_form').submit(function(e) {

        var errors = [];

        if (false === $('#terms').is(':checked')) {
            errors.push('Please accept our terms and conditions');
        }

        if ($('#user_email').length == 1) {
            var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

            if (!re.test($('#user_email').val())) {
                errors.push('Enter a valid email');
            }

        }

        if ($('#u_password').length == 1 && $('#u_password_confirm').length == 1) {
            if ($('#u_password').val() == '' || $('#u_password_confirm').val() == '') {
                errors.push('Please enter your password');
            }
        }

        if ($('#f_name').length == 1 && $('#f_name').val() == '') {
            errors.push('Please enter your name');
        }

        if (errors.length > 0) {

            if ($('#message').length == 0) {
                var d = document.createElement('div');
                d.setAttribute('class', 'error');
                d.setAttribute('id', 'message');
                d = $(d);
                $('#application_form').prepend(d);
            } else {
                var d = $('#message');
                d.find('p').remove();
            }

            for (var x = 0; x < errors.length; x++) {
                d.append('<p class="wp-error">' + errors[x] + '</p>');
            }

            $('html, body').animate({scrollTop:d.offset().top}, 500);

            e.preventDefault();
        }


    });

    $('#l_name, #store_website').on({keydown:function(e) {
        if (e.keyCode == 9 && false === e.shiftKey) {
            if ($(this).parent().parent().parent().next('fieldset').length == 1) {
                $(this).parent().parent().parent().next('fieldset').find('legend').click();
            }
        }
    }});


    $('#u_password_confirm').on({keydown:function(e) {
        if (e.keyCode == 9 && false === e.shiftKey) {
            if ($(this).parent().parent().parent().parent().parent().next('fieldset').length == 1) {
                $(this).parent().parent().parent().parent().parent().next('fieldset').find('legend').click();
            }
        }
    }});

    $('#user_email').on({keydown:function(e) {
        if (e.keyCode == 9 && true === e.shiftKey) {
            if ($(this).parent().parent().parent().parent().parent().prev('fieldset').length == 1) {
                $(this).parent().parent().parent().parent().parent().prev('fieldset').find('legend').click();
            }
        }
    }});

    $('#how, #u_title').on({keydown:function(e) {
        if (e.keyCode == 9 && true === e.shiftKey) {
            if ($(this).parent().parent().parent().prev('fieldset').length == 1) {
                $(this).parent().parent().parent().prev('fieldset').find('legend').click();
            }
        }
    }});

});