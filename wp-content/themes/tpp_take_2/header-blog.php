<?php get_template_part('common/head'); ?>
<?php if (!wp_is_mobile() || tpp_is_tablet()): ?>
    <div class="wrap">
        <section id="header_category" class="header-slideshow"></section>
    </div>
    <?php if (has_nav_menu('blog')): ?>
        <nav class="main-menu main-menu-blog">
            <ul class="menu gradient">
            <?php wp_nav_menu(array(
                'menu'  =>  'blog'
            )) ?>
                </ul>
        </nav>
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