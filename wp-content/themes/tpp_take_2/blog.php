<?php
/*
 * Template Name: blog
 */

get_header('blog'); ?>
<section class="blog-main">

<?php

query_posts(array(
    'posts_per_page'    =>  5
));

if (have_posts()):

    $i = 0; ?>
        <div class="post-slides">
        <?php while (have_posts()): the_post(); ?>
            <?php $i++; ?>
            <article class="post-slide" <?php echo $i > 1?'style="display:none"':'' ?>>
                <?php the_post_thumbnail('blog_post_slide'); ?>
                <div class="post-info">
                    <header><h3><a href="<?php the_permalink() ?>"><?php echo tpp_limit_content(get_the_title(), 40, ''); ?></a></h3></header>
                    <div class="hentry excerpt">
                        <p><?php echo tpp_limit_content(get_the_excerpt(), 120) ?></p>
                        <a class="readmore" href="<?php the_permalink() ?>">Read More</a>
                    </div>
                </div>
            </article>
        <?php flush(); endwhile; ?>
        </div>
<!--        <div class="wrap navigation">-->
<!--            --><?php //posts_nav_link(' ', '<span class="align-right btn btn-primary">Recent Posts</span>', '<span class="align-left btn btn-primary">Previous Posts</span>'); ?>
<!--        </div>-->

<?php endif; ?>
</section>
<script>
    var slides = document.getElementsByClassName('post-slide');

    for (var x = 0; x < slides.length; x++) {

        if (x > 0) {
            slides[x].setAttribute('data-active', false);
            slides[x].style.opacity = 0;
            slides[x].style.display = 'block';
        } else {
            slides[x].setAttribute('data-active', true);
        }
    }

    setInterval(function() {

        var slides = document.getElementsByClassName('post-slide');

        for (var x = 0; x < slides.length; x++) {

            if (slides[x].getAttribute('data-active') == 'true') {
                slides[x].style.opacity = '0';

                slides[x].setAttribute('data-active', false);
                if (x < slides.length  - 1) {
                    slides[x+1].style.opacity = 1;
                    slides[x+1].setAttribute('data-active', true);
                } else {
                    slides[0].style.opacity = 1;
                    slides[0].setAttribute('data-active', true);
                }
                break;
            } else {

            }
        }

    }, 3000);

</script>
<?php

get_template_part('sidebars/general');

get_footer();


 