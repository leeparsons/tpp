var fbLogin = {

    path: '',
    on_login_page: false,

    discover: true,
    //this detrmeines, when not on the login page, whether or not to discover the facebook account in the sysstem or to attempt to log them in

    me: 0,
    me_data: [],

    ajaxRequest: false,
    callback: false,

    getMe: false,

    on_application_form: false,

    //sets the extra parameters for the login to our site submission

    init: function() {


        fbLogin.getInitLoginStatus();

        jQuery('.fb-login-button-a, .btn-facebook').live('click', function(e) {
                e.preventDefault();
                fbLogin.discover = false;
                fbLogin.getFBLoginStatus(true, false);

        });

        FB.Event.subscribe('auth.login', fbLogin.authResponse);

        var paths = window.location.pathname.split('/');


        //determine if the last path is the register page?
        var path = paths[paths.length-1];

        if (path == '') {
            if (paths.length > 2) {
                path = paths[paths.length-2];
            }
        }

        this.path = path;
        //var cntu = true;

        switch (path) {
            case 'store_login':
            case 'store_register':
                //do nothing
                fbLogin.on_login_page = true;
                //cntu = false;
                //prevent user from logging in! Let them choose how
                break;

            case 'sell-with-us':
                //cntu = false;
                fbLogin.on_application_form = true;
                break;

            default:
//                if (document.getElementById('logged_in')) {
//                    cntu = false;
//                } else if (document.cookie != null && document.cookie.match(/ stay_logged_out=/) !== null) {
//                    cntu = false;
//                }
                break;
        }


        //if (cntu === true) {
            //see if the user is logged in, if they are, then see if they have a matching accounton our system. If so then log them in!
        //    fbLogin.getFBLoginStatus(true, false, true);
       //}

    },

    getFBLoginStatus: function(trigger_login, callback) {

        if ( callback != undefined) {
            fbLogin.callback = callback;
        }
        if (fbLogin.me == 0) {
            //assume that the user has not logged in and that we have got their status as per the first call in the init function
            if (trigger_login === true) {
                fbLogin.getMe = true;
            } else {
                fbLogin.getMe = false;
            }

            //force the facebook api to login
            //force the user to login!
            FB.login(fbLogin.authResponse, {scope:'email,user_friends'});


            return;

            //preventing popup window

            FB.getLoginStatus(function(response) {




                if (response.status === 'connected') {

                    fbLogin.me = response.authResponse.userID;

                    // the user is logged in and has authenticated your
                    // app, and response.authResponse supplies
                    // the user's ID, a valid access token, a signed
                    // request, and the time the access token
                    // and signed request each expire
                    var uid = response.authResponse.userID;
                    var accessToken = response.authResponse.accessToken;

                    if (trigger_login) {
                        fbLogin.sendFBLoginRequest();
                    } else if (fbLogin.callback != undefined) {
                        var cb = fbLogin.callback;
                        fbLogin.callback = false;
                        eval(cb);
                    }


                        //jQuery('.fb-login-button').removeClass('fb-login-button');
    //                var a = jQuery('<a></a>');
    //                a.addClass('fb-login-button');
    //                jQuery('#facebook_register_form').append(a);

                } else if (response.status === 'not_authorized') {



                    // the user is logged in to Facebook,
                    // but has not authenticated your app
                    //force the facebook api to login
                    //force the user to login!
                    if (trigger_login && (true === fbLogin.on_login_page || (fbLogin.discover === false && login_from_init === false))) {
                        FB.login(fbLogin.authResponse, {scope:'email,user_friends'});
                    } else if (trigger_login && true === fbLogin.on_application_form) {
                        FB.login(fbLogin.authResponse, {scope:'email,user_friends'});
                    }



                } else {
                    //force the facebook api to login
                    //force the user to login!
                    if (trigger_login && (true === fbLogin.on_login_page || (fbLogin.discover === false && login_from_init === false))) {
                        FB.login(fbLogin.authResponse, {scope:'email,user_friends'});
                    } else if (trigger_login && true === fbLogin.on_application_form) {
                        FB.login(fbLogin.authResponse, {scope:'email,user_friends'});
                    }

                    // the user isn't logged in to Facebook.
                }



            }, {scope: 'email,user_friends'});

        } else if (trigger_login) {
            fbLogin.sendFBLoginRequest();
        }
    },

    /*
    This should only be called on page init and only populates the me object
     */
    getInitLoginStatus: function() {
        FB.getLoginStatus(function(response) {
            if (response.status === 'connected') {
                fbLogin.me = response.authResponse.userID;
            }

        }, {scope: 'email,user_friends'});


    },

    triggerAuthLogin: function() {
        FB.login(
            fbLogin.authResponse,
            {scope:'email,user_friends'}
        );
    },

    sendFBLoginRequest: function(callback) {


        if (callback != undefined && (fbLogin.callback == undefined || fbLogin.callback === false)) {
            fbLogin.callback = callback;
        }

        FB.api('/me', function(response) {

            fbLogin.me_data = response;

                fbLogin.me = response.id;

            if (false !== fbLogin.ajaxRequest) {
                fbLogin.ajaxRequest.abort();
            }

            if (true === fbLogin.on_login_page) {

                var div = document.createElement('div');
                jQuery(div).css({padding:'5%', width:'100%'});
                overlay.setHeader('Hello!');
                overlay.setBody('<p>Good to see you, ' + response.first_name + '!<br><br>Please wait while we log you into our store...<br><br></p>');
                overlay.setCloseLink(false);
                overlay.populateInner();
                fbLogin.ajaxRequest = jQuery.ajax(
                    {
                        url:        '/shop/store_register/fb/ajax/',
                        type:       'POST',
                        dataType:   'json',
                        cache:      false,
                        data:       {
                            fname:          response.first_name,
                            lname:          response.last_name,
                            _email:         response.email,
                            fid:            response.id,
                            gender:         response.gender,
                            profile_link:   response.link,
                            application_form:   fbLogin.on_application_form
                        },
                        success:   function(data) {

                            if (true === fbLogin.on_login_page) {

                                    if (data.message) {
                                        overlay.setHeader(data.message.header);
                                        overlay.setBody(data.message.body);
                                        overlay.populateInner();
                                        if (fbLogin.callback !== false) {
                                            var cb = fbLogin.callback;
                                            fbLogin.callback = false;
                                            eval(cb);
                                        }
                                    } else if (data.error) {
                                        overlay.setHeader('Oops!');
                                        overlay.setBody(data.status);
                                        overlay.setCloseLink(false);
                                        overlay.populateInner();
                                        if (fbLogin.callback !== false) {
                                            var cb = fbLogin.callback;
                                            fbLogin.callback = false;
                                            eval(cb);
                                        }
                                    } else {
                                        if (fbLogin.callback !== false) {
                                            var cb = fbLogin.callback;
                                            fbLogin.callback = false;
                                            eval(cb);
                                        } else {
                                            window.location.href = data.redirect;
                                        }
                                    }
                            } else {

                                if (!data.error) {


                                    if (data.popup && data.popup.name) {
                                        var popup_text = "Welcome back " + data.popup.name;
//                                    if (data.popup.last_visit) {
//                                        popup_text += ", we haven't seen you in " + data.popup.last_visit;
//                                    }

                                        popup.setText(popup_text);
                                        popup.show();

                                    }


                                    fbLogin.replaceMenu(data);

                                }

                                if (fbLogin.callback !== false) {
                                    var cb = fbLogin.callback;
                                    fbLogin.callback = false;
                                    eval(cb);
                                }
                            }

                        },

                        error:      function(data) {
                            if (data.readyState != 0) {
                                console.log('error occurred', data);
                            }
                        }
                    }
                );
            } else {


                fbLogin.ajaxRequest = jQuery.ajax(
                    {
                        url:        '/shop/store_register/fb/ajax/',
                        type:       'POST',
                        dataType:   'json',
                        cache:      false,
                        data:       {
                            discover:       fbLogin.discover === true?1:0,
                            fname:          response.first_name,
                            lname:          response.last_name,
                            _email:         response.email,
                            fid:            response.id,
                            gender:         response.gender,
                            profile_link:   response.link,
                            application_form:   fbLogin.on_application_form
                        },
                        success:   function(data) {

                            if (true === fbLogin.on_login_page) {
                                if (data.message) {
                                    overlay.setHeader(data.message.header);
                                    overlay.setBody(data.message.body);
                                    overlay.setCloseLink(data.redirect);
                                    overlay.populateInner();
                                    if (fbLogin.callback !== false) {
                                        var cb = fbLogin.callback;
                                        fbLogin.callback = false;
                                        eval(cb);
                                    }
                                } else if (data.error) {
                                    overlay.setHeader('Oops!');
                                    overlay.setBody(data.status);
                                    overlay.setCloseLink(false);
                                    overlay.populateInner();
                                    if (fbLogin.callback !== false) {
                                        var cb = fbLogin.callback;
                                        fbLogin.callback = false;
                                        eval(cb);
                                    }
                                } else {
                                    if (fbLogin.callback !== false) {
                                        var cb = fbLogin.callback;
                                        fbLogin.callback = false;
                                        eval(cb);
                                    } else {
                                        window.location.href = data.redirect;
                                    }
                                }


                            } else {

                                if (!data.error) {
                                    if (data && data.popup && data.popup.name) {
                                        var popup_text = "Welcome back " + data.popup.name;
//                                    if (data.popup.last_visit) {
//                                        popup_text += ", we haven't seen you in " + data.popup.last_visit;
//                                    }
                                        popup.setText(popup_text);
                                        popup.show();


                                    }

                                   fbLogin.replaceMenu(data);


                                }

                                if (fbLogin.callback !== false) {
                                    var cb = fbLogin.callback;
                                    fbLogin.callback = false;
                                    eval(cb);
                                }


                            }

                        },

                        error:      function(data) {
                            if (data.readyState != 0) {
                                console.log('error', data);
                            }
                        }
                    }
                );
            }
        },
            {scope:'email,user_friends'}
        );

    },

    authResponse: function(response)
    {


        // do something with response
        if (
            typeof response.authResponse !== undefined && response.authResponse
                &&
                typeof response.authResponse.accessToken !== undefined
                &&
                typeof response.authResponse.userID !== undefined
                &&
                typeof response.status !== undefined
            ) {

            fbLogin.me = response.authResponse.userID;

            if (fbLogin.getMe === true && response.status == 'connected') {

                fbLogin.sendFBLoginRequest();

            } else if (fbLogin.callback !== false) {
                eval(fbLogin.callback);
            }
        } else if (fbLogin.callback !== false) {
            eval(fbLogin.callback);
        }

    },
    replaceMenu: function(data) {

        if (fbLogin.on_application_form === true && (fbLogin.callback == undefined || fbLogin.callback === false)) {
            fbApply.fbStatus();
        }
        jQuery('.menu-btns').find('.login-btn').eq(0).remove();
        jQuery('.menu-btns').find('.btn-facebook').remove();
        jQuery('.menu-btns').find('.or').remove();
        if (data.link_text && data.link_text == 'My Dashboard') {
            jQuery('.menu-btns').find('.sell').prop('href', '/shop/dashboard/product/new').text('List a product');
        }
        jQuery('.menu-btns').find('a:first').after('<a href="' + data.redirect + '">' + (data.link_text?data.link_text:'My Account') + '</a>');
        jQuery('.menu-btns').find('a:last').before('<a href="/shop/store_logout" class="logout-btn btn btn-danger">Logout</a>');

    }
}



var overlay = {
    overlay: false,
    header: '',
    body: '',
    close_link: false,
    init: function() {
        ;
        overlay.overlay = jQuery('<div></div>');

        overlay.overlay.prop('id', 'overlay');
        overlay.overlay.css({opacity:0, zIndex:-1});

        overlay.overlay.on('click', overlay.close);
        overlay.header = document.createElement('h3');
        overlay.body = document.createElement('p');
        jQuery('body').append(overlay.overlay);
    },

    setCloseLink: function(link) {
        overlay.close_link = link;
    },
    setHeader: function(data) {
        overlay.header.innerHTML = data;
    },
    setBody: function(data) {
        overlay.body.innerHTML = data;
    },
    populateInner: function() {



        if (jQuery('.overlay-inner').length == 1) {
            var inner_wrap = jQuery('.overlay-inner');
            inner_wrap.html('');
        } else {
            var inner_wrap = jQuery('<div></div>');
            inner_wrap.prop('class', 'overlay-inner');
        }
        var a = document.createElement('a');
        jQuery(a).on('click', overlay.close);
        jQuery(a).addClass('close');
        inner_wrap.append(a);
        //wrap.on('click', lrp.close).append(inner_wrap);
        inner_wrap.css('z-index', 5003);


        var div = jQuery('<div></div>');

        div
            .css({padding:'5%'}).addClass('wrap')
            .append(overlay.header, overlay.body);
        inner_wrap.append(div);

        var top = jQuery(window).scrollTop()*1.2;

        if (top == 0) {
            top = '100px';
        }

        inner_wrap.css('top', top);

        overlay.overlay.animate({opacity:0.55}, 500).css('z-index', 5000);

        jQuery('body').append(inner_wrap).css({overflow:'hidden'});
    },
    close: function(e) {
        if (e !== undefined) {
            e.preventDefault();
        }

        if (false !== overlay.close_link) {
            window.location.href = overlay.close_link;
        }
        overlay.overlay.animate({opacity:0}, 500).css({zIndex:-1});
        jQuery('.overlay-inner').fadeOut('fast', function() {
            jQuery('.overlay-inner').remove();
        })
        jQuery('body').css({overflow:'visible'});

    }
}


var popup = {

    text: '',
    to: 0,
    popup: '',

    init: function() {

        popup.popup = jQuery('<div></div>');
        popup.popup.prop('class', 'popup');

        popup.popup.hide();
        jQuery('body').append(popup.popup);

        popup.popup.fadeOut();

        var a = jQuery('<a></a>');
        a.prop('class', 'close');
        a.on('click', popup.close);

        var p = document.createElement('p');

        popup.popup.append(a, p);
    },

    setText: function(data) {
        popup.text = data;
    },

    show: function() {
        popup.popup.find('p').html(popup.text);
        popup.popup.show();
        popup.popup.fadeIn();
        popup.to = setTimeout(popup.close, 2000);
    },

    close: function(e) {
        if (e !== undefined) {
            e.preventDefault();
        }
        clearTimeout(popup.to);
        popup.popup.fadeOut('normal', popup.hide);
    },

    hide: function() {
        clearTimeout(popup.to);
        popup.popup.hide();
    }

}

jQuery(document).ready(function($) {

    // Create the event
    $(document).on("facebook:ready", fbLogin.init);

    // Create the ready app to handle initialization
    function facebookReady() {
        // call facebook init
        FB.init({appId:'270470249767149',status:true,xfbml:true});

        // Initialization called, trigger the
        // facebook ready event
        $(document).trigger("facebook:ready");
    }

    // Query if FB object is available, if not
    // assign the window async function
    // otherwise, initialize per Facebook documentation
    if (window.FB) {
        facebookReady();
    } else {
        window.fbAsyncInit = facebookReady;
    }


    popup.init();
    overlay.init();
});





