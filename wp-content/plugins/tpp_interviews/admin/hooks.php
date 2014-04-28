<?php

add_action('save_post', array(
    'TppInterviewsAdminControllerDefault',
    'savePost'
));




add_action( 'admin_notices', array(
    'TppInterviewsAdminControllerDefault',
    'renderNotices'
) );