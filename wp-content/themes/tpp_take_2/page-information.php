<?php
/*
 * Template Name: Information Landing Page
 */



get_header();

$id = 0;
?>

<article class="page-article aside-60">
<?php while (have_posts()): the_post(); ?>
    <header>
        <h1><?php the_title(); ?></h1>
    </header>
    <div class="entry-content">
        <?php the_content(); ?>
    </div>
    <?php $id = get_the_ID(); ?>
<?php endwhile; ?>
<?php

    //get all pages under this page!

    if ($id > 0):

        $query = new WP_Query(array(
            'post_type' =>  'page',
            'child_of'  =>  $id
        ));

        if ($query->have_posts()):

            ?><nav class="page-menu"><ul><?php

            while ($query->have_posts()):
                $query->the_post(); ?>

                <li>
                    <h3><a class="wrap" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    <p><?php the_excerpt(); ?></p>
                </li>


            <?php endwhile;

            ?></ul></nav><?php

        endif;

    endif;

?>

</article>


<?php

get_template_part('sidebars/page');

get_footer();
