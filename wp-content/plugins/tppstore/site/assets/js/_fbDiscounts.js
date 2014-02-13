
var fbDiscounts = {

    init: function() {
        if (document.getElementById('fb_share')) {
            document.getElementById('fb_share').onclick = fbDiscounts._click;
        }

        if (document.getElementById('f_click')) {
            document.getElementById('f_click').onclick = fbDiscounts._fClick;
        }

    },
    _authResponse: function(response) {
        console.log('authorisation response on fbDiscounts');
    },
    _getDiscountCode: function() {
        overlay.setBody('Please wait while we retrieve your discount code');
        overlay.setHeader('Fetching Discount Code');
        overlay.populateInner();

        jQuery.ajax(
            {
                url:    '/shop/discounts/make/',
                data:   {
                    product:    document.getElementById('product').value,
                    fb_user_id: fbLogin.me
                },
                method: 'POST',
                cache:  false,
                success: fbDiscounts._codeRetrieved,
                error: fbDiscounts._codeRetrievalFailed
            }
        );

    },
    _codeRetrievalFailed: function(data) {
        overlay.setBody('There was a problem retrieving your discount code. Please contact us.');
        overlay.setHeader('Sorry!');
        overlay.populateInner();
    },
    _codeRetrieved: function(data) {
        if (data.error === true) {
            if (data.status && data.status != '') {
                overlay.setBody(data.status);
            } else {
                overlay.setBody('There was an error getting your discount code!<br><br>Please contact us.');
            }
            overlay.setHeader('Oops!');
        } else {
            overlay.setBody('Your discount has been added to your cart for this product!<br><br>All you need to do is add this product to your cart and the discount will be applied automatically!');
            overlay.setHeader('Congratulations!');
        }
        overlay.populateInner();
    },

    showShare: function() {
        console.log(main_image);
        FB.ui(
            {
                method: 'feed',
                name: title,
                link: permalink,
                picture: main_image,
                caption: '',
                description: description,
                message: ''
            },
            function(response) {
                if (response && response.post_id) {

                    FB.api('https://graph.facebook.com/' + response.post_id, 'get', function(response) {

                        if (!response || response.error) {
                            overlay.setHeader('Opps!');
                            overlay.setBody('We could not detect a public share, sorry. Please make sure you make a public share!');
                            overlay.populateInner();
                        } else {
                            overlay.setHeader('Updating ...');
                            overlay.setBody('Thank you for sharing, please bear with us while we retrieve your one time discount code...');
                            overlay.populateInner();
                            jQuery.ajax(
                                {
                                    url:        '/shop/discounts/create/',
                                    type:     'post',
                                    data:       {
                                        post_id:    response.post_id,
                                        product:    document.getElementById('product').value
                                    },
                                    success:    fbDiscounts._codeRetrieved,
                                    error:      fbDiscounts._codeRetrievalFailed
                                }
                            );
                        }
                    });
                } else {
                    alert('You would have got a discount code if you shared!.');
                }
            }
        );
    },

    validateLogin: function(response) {
        //if logged in share!
        if (response.status == 'connected') {
            overlay.setBody('Share this product on your facebook wall to receive your discount');
            overlay.setCloseLink(false);
            overlay.setHeader('Social Share Discount');
            overlay.populateInner();
            fbDiscounts.showShare();
        } else if (response.status == 'not_authorized') {
            overlay.setHeader('Oops');
            overlay.setBody('I detected you have not authorized The Photography Parlour Store on Facebook.<br><br>You can allow The Photography Parlour access in your Facebook privacy settings.<br><br>This popup will automatically close in 7 seconds');
            overlay.setCloseLink(false);
            overlay.populateInner();
            setTimeout(overlay.close,7000);

        } else {

            overlay.setHeader('Oops');
            overlay.setBody('I detected you cancelled the login... <br><br>This popup wil automaticaly close in 4 seconds');
            overlay.setCloseLink(false);
            overlay.populateInner();
            setTimeout(overlay.close,4000);
        }



    },

    _click: function() {

        overlay.setBody('Please wait while we detect your social status...');
        overlay.setCloseLink(false);
        overlay.setHeader('Social Share Discount');
        overlay.populateInner();

        if (fbLogin.me === 0) {
            setTimeout(function() {
                FB.login(fbDiscounts.validateLogin, {scope:'email,user_friends'});
            }, 500);
        } else {
            //fake the login status
            fbDiscounts.validateLogin({status:'connected'});
        }


        return false;

//                FB.getLoginStatus(function(response) {
//
//
//
//                    if (response.status === 'connected') {
//                        // the user is logged in and has authenticated your
//                        // app, and response.authResponse supplies
//                        // the user's ID, a valid access token, a signed
//                        // request, and the time the access token
//                        // and signed request each expire
//                        var uid = response.authResponse.userID;
//                        var accessToken = response.authResponse.accessToken;
//                        overlay.setBody('Please share this product to receive your discount code!');
//                        overlay.setHeader('Hello ' + ' ' + response.first_name);
//                        overlay.populateInner();
//
//                        fbDiscounts.fb_user_id = response.uid;
//
//                        fbDiscounts.showShare();
//                    } else {
//
//                        // the user is logged in to Facebook,
//                        // but has not authenticated your app
//                        //force the facebook api to login
//                        //force the user to login!
//
//                        overlay.setBody('Please wait while we redirect you to to login via Facebook.<br><br>Please make sure you have allowed popup windows from our website to receive your social discount!');
//                        overlay.setCloseLink(false);
//                        overlay.setHeader('Social Share Discount ... Redirecting');
//                        overlay.populateInner();
//
//                        document.getElementById('f_click').click();
//
//                        // the user isn't logged in to Facebook.
//                    }
//
//                    return false;
//                }, {scope: 'email'});
    },

    _fClick: function() {
        FB.login(fbDiscounts.authResponse,{scope:'email,user_friends'});
        return false;
    }
}



var fbFriends = {

    init: function() {
        if (fbLogin.me > 0) {
            fbFriends.status();
            //fbLogin.getFBLoginStatus(false, 'fbFriends.status()');
        }
    },

    status: function() {

        if (fbLogin.me && fbLogin.me !== shop_fb_id) {

            // query your friend's likes based on their ID
            FB.api(
                '/me/mutualfriends/' + shop_fb_id + '?limit=10000&offset=0',
                fbFriends.friends
            );
        }
    },

    friends: function(response) {
        if (response && !response.error && response.data.length > 0) {
            //get the first five only!
            var i, d = document.getElementById('facebook_friends');

            i = document.createElement('h5');

            jQuery(i).text('Mutual Friends').addClass('indent align-left wrap');

            d.appendChild(i);

            for (var x = 0; x < 5; x++) {
                i = document.createElement('img');
                i.setAttribute('src', "http://graph.facebook.com/" + response.data[x].id + "/picture?type=square");
                d.appendChild(i);
            }

            i = document.createElement('span');

            jQuery(i).text(' and ' + response.data.length + ' more');


            d.appendChild(i);

        }
    }

}



/*

 FB.ui(
 {
 method: 'feed',
 name: '<?php echo $product->product_title ?>',
 link: '<?php echo $product->getPermalink() ?>',
 picture: '<?php echo get_bloginfo('url') .  $main_image ?>',
 caption: '',
 description: '<?php echo esc_attr(str_replace('<br>', ' ', nl2br($product->excerpt, false))) ?>'
 },
 function(response) {
 if (response && response.post_id) {

 FB.api('https://graph.facebook.com/' + response.post_id, 'get', function(response) {
 console.log(response);
 if (!response || response.error) {

 popup.setText('We could not detect a public share, sorry. Please make sure you show a public share!');
 popup.show();

 } else {
 popup.setText('Your discount code for this product is: monix nosh!');
 popup.show();
 }
 });

 } else {
 alert('You would have got a discount code if you shared!.');
 }
 }
 );
 */



jQuery(document).ready(function($) {

    // Create the event
    $(document).on("facebook:ready", function() {
        fbDiscounts.init();
        fbFriends.init();
    });

});