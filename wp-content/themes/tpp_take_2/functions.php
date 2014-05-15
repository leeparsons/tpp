<?php

//require_once get_template_directory() . '/paypal_adaptive/default.php';
if (is_main_site()) {
    include_once get_template_directory() . '/classes/contact_us.php';
    include_once get_template_directory() . '/classes/bitly.php';
    if (isset($_POST['action']) && $_POST['action'] == 'contact_submission') {
        TppContactUs::getInstance()->actionPost();
    }
}

include get_template_directory() . '/classes/blog_product_relations.php';

if (false === TppStoreControllerUser::getInstance()->loadUserFromSession() && !is_user_logged_in()) {
    include get_template_directory() . '/classes/comments/antispam.php';
    add_action('init', TppAntiSpam::init(), 10);
}



add_theme_support( 'menus' );

add_theme_support( 'post-thumbnails' );
add_image_size('slideshow_thumb', 50, 50, true);
add_image_size('home_widget', 250, 250, true);
//add_image_size('four_square_small', 100, 100, true);
add_image_size('slideshow', 960, 300);
add_image_size('slide_navi', 175, 175, true);
add_image_size('featured_blog_post', 400, 300, true);
add_image_size('size-full', 825, 620, true);
add_image_size('store_related', 110, 110, true);

add_image_size('blog_post_thumb', 250, 150, true);
add_image_size('blog_post_slide', 695, 340, true);
add_image_size('blog_sidebar', 380, 165, true);
add_image_size('blog_small_square', 200, 130, true);
add_image_size('blog_large_square', 475, 356, true);
add_image_size('blog_square_half', 340, 200, true);
add_image_size('blog_square_x_small', 70, 70, true);
add_image_size('blog_small_my_first_time', 215, 150, true);
add_image_size('blog_square_two', 330, 190, true);

register_nav_menu( 'footer_contact', 'Contact Us Footer Menu' );

register_nav_menu( 'footer_pages', 'Pages Footer Menu' );
register_nav_menu( 'blog', 'Blog Header Menu' );


if ( is_admin() ) {
    TppBlogProductRelations::getInstance()->registerAdminHooks();
}

function tpp_is_tablet()
{
    return false !== stripos($_SERVER['HTTP_USER_AGENT'], 'ipad');
}

function tpp_is_ipad()
{
    return false !== stripos($_SERVER['HTTP_USER_AGENT'], 'ipad');
}

function tpp_comment($comment)
{
    switch ( $comment->comment_type ) :
        case 'pingback' :
        case 'trackback' :

            break;

        default:

            global $post;

    ?><li class="align-left wrap">

                <article id="comment-<?php comment_ID(); ?>" class="comment">
                    <header class="comment-meta comment-author vcard">
                        <?php
                        echo get_avatar( $comment, 44 );
                        printf( '<cite class="fn">%1$s %2$s</cite>',
                            get_comment_author_link(),
                            // If current post author is also comment author, make it known visually.
                            ( $comment->user_id === $post->post_author ) ? '<span> ' . __( 'Post author') . '</span>' : ''
                        );
                        printf( '<a href="%1$s"><time datetime="%2$s">%3$s</time></a>',
                            esc_url( get_comment_link( $comment->comment_ID ) ),
                            get_comment_time( 'c' ),
                            /* translators: 1: date, 2: time */
                            sprintf( __( '%1$s at %2$s' ), get_comment_date(), get_comment_time() )
                        );
                        ?>
                    </header><!-- .comment-meta -->

                    <?php if ( '0' == $comment->comment_approved ) : ?>
                        <p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.' ); ?></p>
                    <?php endif; ?>

                    <section class="comment-content comment">
                        <?php comment_text(); ?>
                        <?php edit_comment_link( __( 'Edit' ), '<p class="edit-link">', '</p>' ); ?>
                    </section><!-- .comment-content -->

                    <div class="reply">
                        <?php comment_reply_link( array( 'reply_text' => __( 'Reply' ), 'after' => ' <span>&darr;</span>' )  ); ?>
                    </div><!-- .reply -->
                </article>

    </li><?php


    break;


    endswitch;
}

function tpp_is_blog()
{
    return substr($_SERVER['REQUEST_URI'], 0, 6) == '/blog';
}

/*
 * determines if we are on a blog sub page, not the main blog list
 */
function tpp_is_on_blog_page()
{

    if (stripos($_SERVER['REQUEST_URI'], '/blog') === (int)0) {
        $url = substr($_SERVER['REQUEST_URI'], 0, 6);
        return $url !== '/blog';
    } elseif (stripos($_SERVER['REQUEST_URI'], '/author') === (int)0) {
        $url = substr($_SERVER['REQUEST_URI'], 0, 7);
        return $url == '/author';
    } else {
        return is_single() || is_category();
    }


}

function tpp_on_shop()
{
    $url = $_SERVER['REQUEST_URI'];

    if (substr($url, 0, 6) == '/shop/') {
        return true;
    } else {
        return false;
    }
}


include WP_CONTENT_DIR . '/themes/tpp_take_2/classes/tpp_cacher.php';

function tppSavePost($post_id)
{

    global $post;

    $cats = wp_get_post_categories($post_id);

    $c = new TppCacher();

    foreach ($cats as $cat_id) {
        $c->setCachePath('blog/category/' . $cat_id);
        $c->deleteRecursive();
    }

    $c->setCachePath('blog/posts/' . $post_id);
    $c->deleteRecursive();

    $c->setCachePath('homepage/blog/');
    $c->deleteCache();

    return true;
}

function tppAfterComment($comment_id)
{
    $comment = get_comment($comment_id);
    if ($comment->comment_approved == 1) {
        tppAfterCommentApproved($comment);
    }

}

function tppAfterCommentApproved($comment)
{
    //clean post cache!
    $c = new TppCacher();

    $c->setCachePath('blog/posts/' . $comment->comment_post_ID);
    $c->deleteRecursive();


}

add_action('comment_unapproved_to_approved', 'tppAfterCommentApproved', 10, 3);

add_action('comment_post', 'tppAfterComment', 10, 3);

add_action( 'save_post', 'tppSavePost' );

function tppReadMore($more = '') {
    global $post;
    return '<a class="moretag" href="'. get_permalink($post->ID) . '"> Read more ...</a>';
}

add_filter('excerpt_more', 'tppReadMore');

function tpp_limit_content($content = '', $len = 120, $more = '...')
{
    if (trim($content) == '') {
        return $content;
    }

    $content = strip_tags($content);

    $content_len = strlen($content);

    if ($content_len > $len) {
        $more_len = strlen($more);
        if ($content_len + $more_len > $len) {
            $content = substr(strip_tags($content), 0, $len - $more_len);

            return substr($content, 0, strrpos($content, ' '))

            . $more;
        } else {
            return substr(strip_tags($content), 0, $len) . $more;
        }
    } else {
        return substr(strip_tags($content), 0, $len);
    }

}

add_filter( 'wp_default_scripts', 'remove_jquery_migrate' );

function remove_jquery_migrate( &$scripts)
{
    if(!is_admin())
    {
        $scripts->remove( 'jquery');
        $scripts->add( 'jquery', false, array( 'jquery-core' ) );
    }
}

add_filter( 'comments_array', 'array_reverse' );
