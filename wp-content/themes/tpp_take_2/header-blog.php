<?php get_template_part('blog/head'); ?>
<?php if (!wp_is_mobile() || tpp_is_tablet()): ?>
    <?php if (has_nav_menu('blog')): ?>
        <nav class="main-menu main-menu-blog">
            <ul class="menu gradient">
            <?php wp_nav_menu(array(
                'menu'  =>  'blog'
            )) ?>
                </ul>
        </nav>
    <?php else: ?>
        <?php get_template_part('menus/main_menu') ?>
    <?php endif; ?>
<?php endif; ?>
    <div class="wrap">
        <section class="innerwrap"><?php



if (tpp_is_on_blog_page()): ?>
<div class="wrap">
    <a href="/blog" class="btn btn-primary">Go back to blog</a>
</div>


<?php endif;

flush();